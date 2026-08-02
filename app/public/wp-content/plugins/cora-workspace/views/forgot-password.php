<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cora — Forgot Password</title>
    <style>
        /* Shared clean neutral theme styling */
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

        #forgot-card {
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
            margin-bottom: 24px;
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

        input[type="email"] {
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

        input[type="email"]:focus {
            border-color: var(--text-primary);
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
            margin-bottom: 16px;
        }

        .submit-btn:hover {
            background: var(--btn-hover);
        }

        .back-link {
            display: block;
            text-align: center;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
        }

        .back-link:hover {
            text-decoration: underline;
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
    <div id="forgot-card">
        <h2>Reset Password</h2>
        <p class="subtitle">Enter your email address and we'll send you a link to reset your password if the account exists.</p>
        
        <form id="forgot-form" onsubmit="handleForgotSubmit(event)">
            <div class="form-group">
                <label for="forgot-email">Email Address</label>
                <input type="email" id="forgot-email" required placeholder="name@agency.com">
            </div>
            
            <button type="submit" class="submit-btn" id="submit-btn">Send Reset Link</button>
            <a href="<?php echo esc_url( home_url( '/workspace/login' ) ); ?>" class="back-link">Back to sign in</a>
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

        function handleForgotSubmit(e) {
            e.preventDefault();
            var email = $('#forgot-email').val().trim();

            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showToast('Please enter a valid email address.');
                return;
            }

            $('#submit-btn').prop('disabled', true).text('Sending reset link...');

            $.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
                action: 'cora_ajax_forgot_password',
                email: email,
                nonce: '<?php echo wp_create_nonce( "cora_login_nonce" ); ?>'
            }, function(res) {
                // Security rule: Always show success message regardless of email existence
                showToast('If the email exists, a password reset link has been sent.');
                setTimeout(function() {
                    window.location.href = '<?php echo home_url( "/workspace/login" ); ?>';
                }, 1500);
            }).fail(function() {
                showToast('Network error. Please try again.');
                $('#submit-btn').prop('disabled', false).text('Send Reset Link');
            });
        }
    </script>
</body>
</html>
