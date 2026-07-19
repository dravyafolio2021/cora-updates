<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cora — Login</title>
    <style>
        :root {
            --bg-color: #fcfcfc;
            --card-bg: #ffffff;
            --text-primary: #18181b;
            --text-secondary: #52525b;
            --border-color: #e4e4e7;
            --input-bg: #ffffff;
            --btn-bg: #18181b;
            --btn-hover: #27272a;
            --btn-text: #ffffff;
            --accent: #10b981;
        }
        
        .cora-dark-theme {
            --bg-color: #09090b;
            --card-bg: #111113;
            --text-primary: #f4f4f5;
            --text-secondary: #a1a1aa;
            --border-color: #27272a;
            --input-bg: #18181b;
            --btn-bg: #f4f4f5;
            --btn-hover: #e4e4e7;
            --btn-text: #09090b;
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
            transition: background-color 0.3s, color 0.3s;
        }

        #login-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 36px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            box-sizing: border-box;
        }

        h2 {
            margin: 0 0 8px;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        p.subtitle {
            margin: 0 0 28px;
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
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

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px 14px;
            font-size: 14px;
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.2s;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: var(--text-primary);
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

        .pw-toggle:hover {
            color: var(--text-primary);
        }

        .flex-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
            font-size: 12px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            color: var(--text-secondary);
        }

        .remember-me input {
            accent-color: var(--text-primary);
            margin: 0;
        }

        .forgot-link {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 600;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .submit-btn {
            width: 100%;
            padding: 11px;
            background: var(--btn-bg);
            color: var(--btn-text);
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
        }

        .submit-btn:hover {
            background: var(--btn-hover);
        }

        .submit-btn:active {
            transform: scale(0.98);
        }

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
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.2s ease-out;
            max-width: 320px;
        }

        @keyframes slideIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>
    <div id="login-card">
        <h2>Cora Portal</h2>
        <p class="subtitle">Enter your credentials to access the workspace.</p>
        
        <form id="login-form" onsubmit="handleLoginSubmit(event)">
            <div class="form-group">
                <label for="login-email">Email Address</label>
                <input type="email" id="login-email" required placeholder="name@agency.com">
            </div>
            
            <div class="form-group">
                <label for="login-password">Password</label>
                <input type="password" id="login-password" required placeholder="••••••••">
                <button type="button" class="pw-toggle" onclick="togglePasswordVisibility()">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" id="eye-open"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" id="eye-closed" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                </button>
            </div>
            
            <div class="flex-row">
                <label class="remember-me">
                    <input type="checkbox" id="login-remember">
                    Remember me
                </label>
                <a href="<?php echo esc_url( home_url( '/workspace/forgot-password' ) ); ?>" class="forgot-link">Forgot password?</a>
            </div>
            
        <button type="submit" class="submit-btn" id="login-btn">Sign In</button>
        </form>
        <div style="margin-top:20px; text-align:center; font-size:12px; color:var(--text-secondary);">
            Don't have an account?
            <a href="<?php echo esc_url( home_url( '/workspace/register' ) ); ?>" style="color:var(--text-primary);font-weight:700;text-decoration:none;">Create workspace →</a>
        </div>
    </div>

    <div id="cora-toast-container"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.body.classList.add('cora-dark-theme');
        }

        $(document).ready(function() {
            var urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('deactivated')) {
                showToast('Your account has been deactivated. Contact your agency admin.');
            }
            if (urlParams.get('suspended')) {
                showToast('Your agency account has been suspended. Contact Cora support.');
            }
            if (urlParams.get('password_updated')) {
                showToast('Password updated. Please log in.');
            }
            if (urlParams.get('expired')) {
                showToast('Your account access has expired. Please contact your administrator.');
            }
        });

        function showToast(msg) {
            var container = $('#cora-toast-container');
            var toast = $('<div class="cora-toast"></div>').html(msg);
            container.append(toast);
            setTimeout(function() {
                toast.fadeOut(300, function() { $(this).remove(); });
            }, 4000);
        }

        function togglePasswordVisibility() {
            var input = $('#login-password');
            var openIcon = $('#eye-open');
            var closedIcon = $('#eye-closed');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                openIcon.hide();
                closedIcon.show();
            } else {
                input.attr('type', 'password');
                openIcon.show();
                closedIcon.hide();
            }
        }

        var lockoutRemaining = <?php echo intval( get_transient( 'cora_lockout_' . $_SERVER['REMOTE_ADDR'] ) ? ( get_option( 'cora_lockout_time_' . $_SERVER['REMOTE_ADDR'] ) - time() ) : 0 ); ?>;
        var lockoutInterval = null;

        function startLockoutTimer() {
            if (lockoutRemaining <= 0) return;
            $('#login-btn').prop('disabled', true);
            updateTimerDisplay();
            lockoutInterval = setInterval(function() {
                lockoutRemaining--;
                if (lockoutRemaining <= 0) {
                    clearInterval(lockoutInterval);
                    $('#login-btn').prop('disabled', false).text('Sign In');
                } else {
                    updateTimerDisplay();
                }
            }, 1000);
        }

        function updateTimerDisplay() {
            var minutes = Math.floor(lockoutRemaining / 60);
            var seconds = lockoutRemaining % 60;
            var display = (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
            $('#login-btn').text('Too many attempts. Try again in ' + display);
        }

        if (lockoutRemaining > 0) {
            startLockoutTimer();
        }

        function handleLoginSubmit(e) {
            e.preventDefault();
            if (lockoutRemaining > 0) return;

            var email = $('#login-email').val().trim();
            var password = $('#login-password').val();
            var remember = $('#login-remember').is(':checked') ? 1 : 0;

            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showToast('Please enter a valid email address.');
                return;
            }

            $('#login-btn').prop('disabled', true).text('Signing in...');

            $.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
                action: 'cora_ajax_login',
                email: email,
                password: password,
                remember: remember,
                nonce: '<?php echo wp_create_nonce( "cora_login_nonce" ); ?>'
            }, function(res) {
                if (res.success) {
                    showToast('Login successful. Redirecting...');
                    setTimeout(function() {
                        var redirectUrl = new URLSearchParams(window.location.search).get('redirect_to');
                        window.location.href = redirectUrl ? decodeURIComponent(redirectUrl) : res.data.redirect_url;
                    }, 800);
                } else {
                    showToast(res.data.message);
                    if (res.data.lockout) {
                        lockoutRemaining = res.data.lockout;
                        startLockoutTimer();
                    } else {
                        $('#login-btn').prop('disabled', false).text('Sign In');
                    }
                }
            }).fail(function() {
                showToast('Network error. Please try again.');
                $('#login-btn').prop('disabled', false).text('Sign In');
            });
        }

        function coraResendVerification(email) {
            showToast('Sending verification link...');
            $.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
                action: 'cora_ajax_resend_verification',
                email: email,
                nonce: '<?php echo wp_create_nonce( "cora_login_nonce" ); ?>'
            }, function(res) {
                showToast(res.data.message);
            });
        }
    </script>
</body>
</html>
