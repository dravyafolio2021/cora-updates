<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cora — Set New Password</title>
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

        #reset-card {
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
        }

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
            transition: background 0.2s;
            margin-top: 8px;
        }

        .submit-btn:hover {
            background: var(--btn-hover);
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
    <div id="reset-card">
        <h2>Set New Password</h2>
        <p class="subtitle">Please enter your new password below.</p>
        
        <form id="reset-form" onsubmit="handleResetSubmit(event)">
            <div class="form-group">
                <label for="reset-pass">New Password</label>
                <input type="password" id="reset-pass" required placeholder="••••••••">
                <button type="button" class="pw-toggle" onclick="togglePasswordVisibility('reset-pass', 'eye-open-1', 'eye-closed-1')">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" id="eye-open-1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" id="eye-closed-1" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                </button>
            </div>

            <div class="form-group">
                <label for="reset-confirm">Confirm Password</label>
                <input type="password" id="reset-confirm" required placeholder="••••••••">
                <button type="button" class="pw-toggle" onclick="togglePasswordVisibility('reset-confirm', 'eye-open-2', 'eye-closed-2')">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" id="eye-open-2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" id="eye-closed-2" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                </button>
            </div>
            
            <button type="submit" class="submit-btn" id="submit-btn">Reset Password</button>
        </form>
    </div>

    <div id="cora-toast-container"></div>

    <script src="<?php echo CORA_WORKSPACE_URL . 'assets/js/jquery.min.js'; ?>"></script>
    <script>
        // Light mode standard

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

        function handleResetSubmit(e) {
            e.preventDefault();
            var pass = $('#reset-pass').val();
            var confirm = $('#reset-confirm').val();

            if (pass !== confirm) {
                showToast('Passwords do not match.');
                return;
            }

            // Min 8 characters, at least 1 uppercase, 1 lowercase, 1 number
            var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
            if (!regex.test(pass)) {
                showToast('Password must be at least 8 characters and contain one uppercase, one lowercase, and one number.');
                return;
            }

            // Extract reset token from URL query string
            var urlParams = new URLSearchParams(window.location.search);
            var token = urlParams.get('token') || '';

            $('#submit-btn').prop('disabled', true).text('Updating password...');

            $.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
                action: 'cora_ajax_reset_password',
                password: pass,
                token: token,
                nonce: '<?php echo wp_create_nonce( "cora_login_nonce" ); ?>'
            }, function(res) {
                if (res.success) {
                    showToast('Password updated. Please log in.');
                    setTimeout(function() {
                        window.location.href = '<?php echo home_url( "/workspace/login" ); ?>';
                    }, 1500);
                } else {
                    showToast(res.data.message);
                    $('#submit-btn').prop('disabled', false).text('Reset Password');
                }
            }).fail(function() {
                showToast('Network error. Please try again.');
                $('#submit-btn').prop('disabled', false).text('Reset Password');
            });
        }
    </script>
</body>
</html>
