<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cora — Set Up Your Account</title>
    <style>
        :root {
            --bg-color: #fcfcfc;
            --card-bg: #ffffff;
            --text-primary: #18181b;
            --text-secondary: #52525b;
            --text-tertiary: #a1a1aa;
            --border-color: #e4e4e7;
            --input-bg: #ffffff;
            --input-hover-bg: #fafafa;
            --btn-bg: #18181b;
            --btn-hover: #27272a;
            --btn-text: #ffffff;
            --accent: #10b981;
            --accent-bg: #ecfdf5;
        }
        
        .cora-dark-theme {
            --bg-color: #09090b;
            --card-bg: #111113;
            --text-primary: #f4f4f5;
            --text-secondary: #a1a1aa;
            --text-tertiary: #71717a;
            --border-color: #27272a;
            --input-bg: #18181b;
            --btn-bg: #f4f4f5;
            --btn-hover: #e4e4e7;
            --btn-text: #09090b;
            --accent: #10b981;
            --accent-bg: rgba(16,185,129,0.08);
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
            padding: 24px 16px;
            box-sizing: border-box;
            transition: background-color 0.3s, color 0.3s;
        }

        #setup-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 4px 32px rgba(9, 9, 11, 0.03);
            box-sizing: border-box;
        }

        h2 {
            margin: 0 0 8px;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.04em;
            line-height: 1.2;
            color: var(--text-primary);
        }

        p.subtitle {
            margin: 0 0 28px;
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
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

        /* Input prefix icon wrappers */
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
        input[type="password"] {
            width: 100%;
            padding: 11px 14px;
            font-size: 14px;
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-primary);
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-family: inherit;
        }

        input:disabled {
            background: #f4f4f5;
            cursor: not-allowed;
            color: #71717a;
        }

        .cora-dark-theme input:disabled {
            background: #1c1c1e;
            color: #71717a;
            border-color: #27272a;
        }

        input:focus:not(:disabled) {
            border-color: var(--text-primary);
            box-shadow: 0 0 0 3px rgba(24,24,27,0.06);
        }

        .cora-dark-theme input:focus:not(:disabled) {
            box-shadow: 0 0 0 3px rgba(244,244,245,0.04);
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

        .pw-toggle:hover {
            color: var(--text-primary);
        }

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

        @keyframes slideIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>
    <?php
    // Fetch invitation details
    $token = sanitize_text_field( $_GET['token'] ?? '' );
    $invitations = get_option( 'cora_invitations', array() );
    $invite = isset( $invitations[ $token ] ) ? $invitations[ $token ] : null;
    $email = $invite ? $invite['email'] : '';
    $name = '';
    if ( $invite ) {
        // Pre-fill name if sent from invite (or parse it from email if name not stored)
        $name = ucwords( str_replace( '.', ' ', explode( '@', $email )[0] ) );
    }
    ?>
    <div id="setup-card">
        <h2>Create Account</h2>
        <p class="subtitle">Complete your profile to join your agency team.</p>
        
        <form id="setup-form" onsubmit="handleSetupSubmit(event)">
            <div class="form-group">
                <label for="setup-email">Email Address</label>
                <div class="input-group-with-icon">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="input-icon-prefix"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    <input type="email" id="setup-email" value="<?php echo esc_attr( $email ); ?>" disabled>
                </div>
            </div>

            <div class="form-group">
                <label for="setup-name">Your Full Name</label>
                <div class="input-group-with-icon">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="input-icon-prefix"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <input type="text" id="setup-name" value="<?php echo esc_attr( $name ); ?>" required placeholder="e.g. John Doe">
                </div>
            </div>

            <div class="form-group">
                <label for="setup-pass">Password</label>
                <div class="input-group-with-icon">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="input-icon-prefix"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <input type="password" id="setup-pass" required placeholder="••••••••">
                    <button type="button" class="pw-toggle" onclick="togglePasswordVisibility('setup-pass', 'eye-open-1', 'eye-closed-1')">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" id="eye-open-1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" id="eye-closed-1" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label for="setup-confirm">Confirm Password</label>
                <div class="input-group-with-icon">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="input-icon-prefix"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <input type="password" id="setup-confirm" required placeholder="••••••••">
                    <button type="button" class="pw-toggle" onclick="togglePasswordVisibility('setup-confirm', 'eye-open-2', 'eye-closed-2')">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" id="eye-open-2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" id="eye-closed-2" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                    </button>
                </div>
            </div>
            
            <button type="submit" class="submit-btn" id="submit-btn">
                Create Account
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>
            
            <div class="secure-footer-text">
                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Your data is secure. We'll never share it with anyone.
            </div>
        </form>
    </div>

    <div id="cora-toast-container"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.body.classList.add('cora-dark-theme');
        }

        function showToast(msg) {
            var container = $('#cora-toast-container');
            var toast = $('<div class="cora-toast"></div>').html(msg);
            container.append(toast);
            setTimeout(function() {
                toast.fadeOut(300, function() { $(this).remove(); });
            }, 4000);
        }

        function togglePasswordVisibility(inputId, openId, closedId) {
            var input = $('#' + inputId);
            var openIcon = $('#' + openId);
            var closedIcon = $('#' + closedId);
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

        function handleSetupSubmit(e) {
            e.preventDefault();
            var name = $('#setup-name').val().trim();
            var pass = $('#setup-pass').val();
            var confirm = $('#setup-confirm').val();

            if (pass !== confirm) {
                showToast('Passwords do not match.');
                return;
            }

            var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
            if (!regex.test(pass)) {
                showToast('Password must be at least 8 characters and contain one uppercase, one lowercase, and one number.');
                return;
            }

            var token = '<?php echo esc_js( $token ); ?>';

            $('#submit-btn').prop('disabled', true).text('Creating account...');

            $.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
                action: 'cora_ajax_accept_invitation',
                name: name,
                password: pass,
                token: token,
                nonce: '<?php echo wp_create_nonce( "cora_login_nonce" ); ?>'
            }, function(res) {
                if (res.success) {
                    showToast('Account setup complete! Logging you in...');
                    setTimeout(function() {
                        window.location.href = res.data.redirect_url;
                    }, 1200);
                } else {
                    showToast(res.data.message);
                    $('#submit-btn').prop('disabled', false).text('Create Account');
                }
            }).fail(function() {
                showToast('Network error. Please try again.');
                $('#submit-btn').prop('disabled', false).text('Create Account');
            });
        }
    </script>
</body>
</html>
