<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>Cora — Login</title>
    <style>
        :root {
            color-scheme: only light !important;
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

        /* Google Button */
        .google-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 10px 14px;
            background: var(--card-bg);
            border: 1.5px solid #2563eb;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
            cursor: pointer;
            transition: background 0.15s, border-color 0.15s, box-shadow 0.15s;
            text-decoration: none;
            margin-bottom: 20px;
            position: relative;
            box-sizing: border-box;
        }
        .google-btn:hover {
            background: var(--bg-color);
            border-color: #1d4ed8;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .google-pill {
            position: absolute;
            top: -8px;
            right: 12px;
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 1px 6px;
            border-radius: 4px;
            letter-spacing: 0.05em;
            border: 1px solid #bfdbfe;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            color: var(--text-secondary);
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
    </style>
</head>
<body>
    <div id="login-card">
        <!-- Suspended Banner Callout -->
        <div id="cora-suspended-banner" style="display:none; margin-bottom: 20px; padding: 14px; background: #fafafa; border: 1.5px solid #18181b; border-radius: 10px; text-align: left;">
            <div style="display:flex; align-items:center; gap: 8px; font-weight: 700; font-size: 13px; color: #09090b; margin-bottom: 4px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Account Suspended
            </div>
            <div style="font-size: 12px; color: #71717a; line-height: 1.4; margin-bottom: 10px;">
                Your workspace account has been suspended by the platform administrator. You can submit an appeal to request reactivation.
            </div>
            <button type="button" onclick="openAppealDrawer()" style="width:100%; padding: 9px 12px; background: #09090b; color: #ffffff; border: none; border-radius: 6px; font-size: 11px; font-weight: 700; cursor: pointer; transition: all 0.15s ease;">
                Request Reactivation Appeal →
            </button>
        </div>

        <h2>Cora Portal</h2>
        <p class="subtitle">Enter your credentials to access the workspace.</p>
        
        <?php
        $google_enabled  = ( get_option( 'cora_onboarding_google_enabled', 1 ) && ! empty( get_option( 'cora_google_client_id', '' ) ) ) || cora_is_local_environment();
        $google_auth_url = home_url( '/workspace/auth/google' );
        if ( ! empty( $_GET['plan'] ) ) {
            $google_auth_url = add_query_arg( array(
                'plan'    => sanitize_text_field( $_GET['plan'] ),
                'billing' => sanitize_text_field( $_GET['billing'] ?? 'annual' ),
            ), $google_auth_url );
        }
        if ( $google_enabled ) :
        ?>
        <a href="<?php echo esc_url( $google_auth_url ); ?>" class="google-btn" id="google-btn">
            <span class="google-pill">LAST USED</span>
            <svg width="18" height="18" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12s5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24s8.955,20,20,20s20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z" fill="#FFC107"/>
                <path d="M6.306,14.691l6.571,4.819C14.655,15.108,19.003,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z" fill="#FF3D00"/>
                <path d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z" fill="#4CAF50"/>
                <path d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z" fill="#1976D2"/>
            </svg>
            Continue with Google
        </a>
        <div class="divider">or continue with email</div>
        <?php endif; ?>

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
        <!--
        <div style="margin-top:16px; text-align:center; font-size:12px;">
            <a href="#" onclick="toggleAuthMode('magic', event)" style="color:var(--text-secondary);text-decoration:none;font-weight:600;">Sign in with Magic Link →</a>
        </div>
        -->
        </form>

        <form id="magic-link-form" onsubmit="handleMagicLinkSubmit(event)" style="display:none;">
            <div class="form-group">
                <label for="magic-email">Email Address</label>
                <input type="email" id="magic-email" required placeholder="name@agency.com">
            </div>
            
            <button type="submit" class="submit-btn" id="magic-btn">Send Magic Link</button>
            
            <div style="margin-top:16px; text-align:center; font-size:12px;">
                <a href="#" onclick="toggleAuthMode('password', event)" style="color:var(--text-secondary);text-decoration:none;font-weight:600;">← Sign in with Password</a>
            </div>
        </form>
        <div style="margin-top:20px; text-align:center; font-size:12px; color:var(--text-secondary);">
            Don't have an account?
            <a href="<?php echo esc_url( home_url( '/workspace/onboarding' ) ); ?>" style="color:var(--text-primary);font-weight:700;text-decoration:none;">Create workspace →</a>
        </div>
    </div>

    <div id="cora-toast-container"></div>

    <!-- Right-sliding Side Drawer Sheet for Suspension Appeal -->
    <div id="cora-appeal-drawer" style="position: fixed; inset: 0; z-index: 9999; display: none;">
        <!-- Backdrop overlay -->
        <div onclick="closeAppealDrawer()" style="position: absolute; inset: 0; background: rgba(0,0,0,0.4); backdrop-filter: blur(2px);"></div>
        <!-- Drawer Panel -->
        <div style="position: absolute; top: 0; right: 0; bottom: 0; width: 100%; max-width: 420px; background: #ffffff; box-shadow: -4px 0 25px rgba(0,0,0,0.15); display: flex; flex-direction: column; z-index: 10000;">
            <!-- Header -->
            <div style="padding: 20px 24px; border-bottom: 1px solid #e4e4e7; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #09090b;">Reactivation Appeal</h3>
                    <p style="margin: 2px 0 0 0; font-size: 12px; color: #71717a;">Submit a request for workspace reactivation.</p>
                </div>
                <button onclick="closeAppealDrawer()" type="button" style="background: transparent; border: none; font-size: 20px; color: #71717a; cursor: pointer; padding: 4px; border-radius: 4px;">&times;</button>
            </div>
            <!-- Body -->
            <div style="padding: 24px; flex: 1; overflow-y: auto;">
                <form id="appeal-form" onsubmit="submitAppeal(event)">
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #09090b; margin-bottom: 6px;">Account Email *</label>
                        <input type="email" id="appeal-email" required placeholder="name@agency.com" style="width: 100%; padding: 10px 12px; border: 1px solid #e4e4e7; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
                    </div>
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #09090b; margin-bottom: 6px;">Workspace Name / Agency</label>
                        <input type="text" id="appeal-workspace" placeholder="e.g. Apex Realty" style="width: 100%; padding: 10px 12px; border: 1px solid #e4e4e7; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
                    </div>
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #09090b; margin-bottom: 6px;">Contact Phone / WhatsApp (Optional)</label>
                        <input type="text" id="appeal-phone" placeholder="+91 98765 43210" style="width: 100%; padding: 10px 12px; border: 1px solid #e4e4e7; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #09090b; margin-bottom: 6px;">Reason for Appeal *</label>
                        <textarea id="appeal-reason" required rows="4" placeholder="Explain why your workspace account should be reactivated..." style="width: 100%; padding: 10px 12px; border: 1px solid #e4e4e7; border-radius: 8px; font-size: 13px; font-family: inherit; resize: vertical; box-sizing: border-box;"></textarea>
                    </div>
                    <button type="submit" id="appeal-submit-btn" style="width: 100%; padding: 12px; background: #09090b; color: #ffffff; border: none; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; transition: background 0.15s ease;">
                        Submit Reactivation Appeal
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="<?php echo CORA_WORKSPACE_URL . 'assets/js/jquery.min.js'; ?>"></script>
    <script>
        // Light mode standard

        $(document).ready(function() {
            var urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('deactivated')) {
                showToast('Your account has been deactivated. Contact your agency admin.');
                if (window.history.replaceState) {
                    var cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                    window.history.replaceState({ path: cleanUrl }, '', cleanUrl);
                }
            }
            if (urlParams.get('suspended')) {
                $('#cora-suspended-banner').show();
                showToast('Your agency account has been suspended. Contact Cora support.');
            }
            if (urlParams.get('password_updated')) {
                showToast('Password updated. Please log in.');
            }
            if (urlParams.get('expired')) {
                showToast('Your account access has expired. Please contact your administrator.');
            }
            if (urlParams.has('error')) {
                const errKey = urlParams.get('error');
                const errMsgs = {
                    'google_disabled'    : 'Google sign-in is not available.',
                    'oauth_state'        : 'Security check failed. Try again.',
                    'oauth_token'        : 'Could not connect to Google. Please try again.',
                    'registration_closed': 'Self-registration is disabled for this workspace.'
                };
                const msg = errMsgs[errKey] || 'An error occurred during authentication.';
                showToast(msg);
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

            $.ajax({
                url: '<?php echo esc_url( cora_get_origin_relative_url( admin_url( 'admin-ajax.php' ) ) ); ?>',
                type: 'POST',
                dataType: 'json',
                timeout: 15000,
                data: {
                    action: 'cora_ajax_login',
                    email: email,
                    password: password,
                    remember: remember,
                    nonce: '<?php echo wp_create_nonce( "cora_login_nonce" ); ?>'
                },
                success: function(res) {
                    try {
                        if (res && res.success) {
                            showToast('Login successful. Redirecting...');
                            setTimeout(function() {
                                var redirectUrl = new URLSearchParams(window.location.search).get('redirect_to');
                                window.location.href = redirectUrl ? decodeURIComponent(redirectUrl) : res.data.redirect_url;
                            }, 800);
                        } else {
                            var msg = (res && res.data && res.data.message) ? res.data.message : 'Authentication failed. Please try again.';
                            showToast(msg);
                            if (msg.toLowerCase().indexOf('suspended') !== -1) {
                                $('#cora-suspended-banner').slideDown(200);
                            }
                            if (res && res.data && res.data.lockout) {
                                lockoutRemaining = res.data.lockout;
                                startLockoutTimer();
                            } else {
                                $('#login-btn').prop('disabled', false).text('Sign In');
                            }
                        }
                    } catch (err) {
                        console.error('Cora login callback error:', err);
                        showToast('An unexpected error occurred. Please try again.');
                        $('#login-btn').prop('disabled', false).text('Sign In');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Cora login AJAX error:', status, error, xhr.responseText);
                    if (status === 'timeout') {
                        showToast('Request timed out. Please check your connection and try again.');
                    } else {
                        showToast('Network error. Please try again.');
                    }
                    $('#login-btn').prop('disabled', false).text('Sign In');
                }
            });
        }

        function openAppealDrawer() {
            var emailVal = $('#login-email').val() || '';
            if (emailVal) {
                $('#appeal-email').val(emailVal);
            }
            $('#cora-appeal-drawer').fadeIn(150);
        }

        function closeAppealDrawer() {
            $('#cora-appeal-drawer').fadeOut(150);
        }

        function submitAppeal(e) {
            e.preventDefault();
            var email = $('#appeal-email').val().trim();
            var workspace_name = $('#appeal-workspace').val().trim();
            var phone = $('#appeal-phone').val().trim();
            var reason = $('#appeal-reason').val().trim();

            if (!email || !reason) {
                showToast('Please fill in all required fields.');
                return;
            }

            $('#appeal-submit-btn').prop('disabled', true).text('Submitting Appeal...');

            $.post('<?php echo esc_url( cora_get_origin_relative_url( admin_url( 'admin-ajax.php' ) ) ); ?>', {
                action: 'cora_submit_suspension_appeal',
                email: email,
                workspace_name: workspace_name,
                phone: phone,
                reason: reason
            }, function(res) {
                if (res.success) {
                    showToast(res.data.message);
                    closeAppealDrawer();
                    $('#appeal-form')[0].reset();
                } else {
                    showToast(res.data.message || 'Error submitting appeal.');
                }
                $('#appeal-submit-btn').prop('disabled', false).text('Submit Reactivation Appeal');
            }).fail(function() {
                showToast('Network error while submitting appeal. Please try again.');
                $('#appeal-submit-btn').prop('disabled', false).text('Submit Reactivation Appeal');
            });
        }

        function coraResendVerification(email) {
            showToast('Sending verification link...');
            $.post('<?php echo esc_url( cora_get_origin_relative_url( admin_url( 'admin-ajax.php' ) ) ); ?>', {
                action: 'cora_ajax_resend_verification',
                email: email,
                nonce: '<?php echo wp_create_nonce( "cora_login_nonce" ); ?>'
            }, function(res) {
                showToast(res.data.message);
            });
        }

        function toggleAuthMode(mode, e) {
            if (e) e.preventDefault();
            if (mode === 'magic') {
                $('#login-form').hide();
                $('#magic-link-form').show();
            } else {
                $('#login-form').show();
                $('#magic-link-form').hide();
            }
        }

        function handleMagicLinkSubmit(e) {
            e.preventDefault();
            var email = $('#magic-email').val().trim();
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showToast('Please enter a valid email address.');
                return;
            }

            $('#magic-btn').prop('disabled', true).text('Sending link...');

            $.post('<?php echo esc_url( cora_get_origin_relative_url( admin_url( 'admin-ajax.php' ) ) ); ?>', {
                action: 'cora_request_magic_link',
                email: email,
                nonce: '<?php echo wp_create_nonce( "cora_login_nonce" ); ?>'
            }, function(res) {
                if (res.success) {
                    showToast(res.data.message);
                    if (res.data.dev_magic_url) {
                        var oldNotice = $('#dev-magic-notice');
                        if (oldNotice.length) oldNotice.remove();

                        var devDiv = $('<div id="dev-magic-notice" style="margin-top:16px; padding: 12px; background: #fef08a; color: #854d0e; border: 1px solid #fef08a; border-radius: 8px; font-size: 11.5px; text-align: left; line-height: 1.4;"></div>')
                            .html('<strong>[Dev Mode] Magic Link generated:</strong><br><a href="' + res.data.dev_magic_url + '" style="color:#854d0e; font-weight:700; text-decoration:underline;">Click here to simulate magic link login →</a>');
                        $('#magic-link-form').append(devDiv);
                    }
                } else {
                    showToast(res.data.message);
                    $('#magic-btn').prop('disabled', false).text('Send Magic Link');
                }
            }).fail(function() {
                showToast('Network error. Please try again.');
                $('#magic-btn').prop('disabled', false).text('Send Magic Link');
            });
        }
    </script>
</body>
</html>
