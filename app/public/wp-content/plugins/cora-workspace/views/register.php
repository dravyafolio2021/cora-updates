<?php
if ( get_option( 'cora_onboarding_enabled', 1 ) ) {
    wp_redirect( home_url( '/workspace/onboarding' ) );
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>Cora — Create Your Workspace</title>
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
            --divider-color: #f0f0f0;
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
            --divider-color: #27272a;
        }
        *, *::before, *::after { box-sizing: border-box; }
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
            transition: background-color 0.3s, color 0.3s;
        }
        #reg-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 36px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.04);
        }
        .cora-wordmark {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--text-secondary);
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .cora-wordmark svg { flex-shrink: 0; }
        h2 {
            margin: 0 0 6px;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.03em;
        }
        p.subtitle {
            margin: 0 0 28px;
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.5;
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
        /* Form */
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .form-group { margin-bottom: 16px; position: relative; }
        label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
            color: var(--text-secondary);
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px 14px;
            font-size: 14px;
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-family: inherit;
        }
        input:focus {
            border-color: var(--text-primary);
            box-shadow: 0 0 0 3px rgba(24,24,27,0.07);
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
        .pw-toggle:hover { color: var(--text-primary); }
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
            transition: background 0.15s, transform 0.1s;
            margin-top: 4px;
            font-family: inherit;
        }
        .submit-btn:hover { background: var(--btn-hover); }
        .submit-btn:active { transform: scale(0.98); }
        .submit-btn:disabled { opacity: 0.6; cursor: not-allowed; }
        .footer-link {
            margin-top: 22px;
            text-align: center;
            font-size: 12px;
            color: var(--text-secondary);
        }
        .footer-link a {
            color: var(--text-primary);
            font-weight: 700;
            text-decoration: none;
        }
        .footer-link a:hover { text-decoration: underline; }
        /* Inbox State */
        #inbox-state { display: none; text-align: center; }
        .inbox-icon {
            width: 56px;
            height: 56px;
            background: var(--bg-color);
            border: 1.5px solid var(--border-color);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .inbox-title { font-size: 20px; font-weight: 800; letter-spacing: -0.03em; margin-bottom: 8px; }
        .inbox-sub { font-size: 13px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 24px; }
        .inbox-sub strong { color: var(--text-primary); }
        .resend-btn {
            background: none;
            border: none;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            cursor: pointer;
            text-decoration: underline;
            font-family: inherit;
            padding: 0;
        }
        .resend-btn:hover { color: var(--text-primary); }
        /* Toast */
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
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            animation: slideIn 0.2s ease-out;
            max-width: 320px;
        }
        @keyframes slideIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @media (max-width: 480px) {
            #reg-card { padding: 28px 20px; }
            .form-row { grid-template-columns: 1fr; gap: 0; }
        }
    </style>
</head>
<body>
<?php
$google_enabled  = get_option( 'cora_onboarding_google_enabled', 1 ) && ! empty( get_option( 'cora_google_client_id', '' ) );
$email_enabled   = get_option( 'cora_onboarding_email_enabled', 1 );
$reg_enabled     = get_option( 'cora_onboarding_enabled', 1 );
$google_auth_url = home_url( '/workspace/auth/google' );
?>

<div id="reg-card">
    <div class="cora-wordmark">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
            <rect width="14" height="14" rx="3.5" fill="currentColor" opacity="0.12"/>
            <rect x="3" y="3" width="3.5" height="3.5" rx="1" fill="currentColor"/>
            <rect x="7.5" y="3" width="3.5" height="3.5" rx="1" fill="currentColor"/>
            <rect x="3" y="7.5" width="3.5" height="3.5" rx="1" fill="currentColor"/>
            <rect x="7.5" y="7.5" width="3.5" height="3.5" rx="1" fill="currentColor"/>
        </svg>
        Cora Platform
    </div>

    <!-- Registration Form State -->
    <div id="form-state">
        <h2>Create your workspace</h2>
        <p class="subtitle">Start your free account. No credit card required.</p>

        <?php if ( ! $reg_enabled ) : ?>
        <div style="text-align:center; padding: 24px 0; color: var(--text-secondary); font-size:13px;">
            <p>Registration is currently closed. Please contact your administrator.</p>
            <a href="<?php echo esc_url( home_url( '/workspace/login' ) ); ?>" style="color:var(--text-primary);font-weight:700;font-size:12px;">← Back to sign in</a>
        </div>
        <?php else : ?>

        <?php if ( $google_enabled ) : ?>
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
        <?php endif; ?>

        <?php if ( $google_enabled && $email_enabled ) : ?>
        <div class="divider">or continue with email</div>
        <?php endif; ?>

        <?php if ( $email_enabled ) : ?>
        <form id="register-form" onsubmit="handleRegisterSubmit(event)" autocomplete="off">
            <div class="form-row">
                <div class="form-group">
                    <label for="reg-name">Full Name</label>
                    <input type="text" id="reg-name" required placeholder="Jane Smith" autocomplete="name">
                </div>
                <div class="form-group">
                    <label for="reg-agency">Agency Name</label>
                    <input type="text" id="reg-agency" required placeholder="My Agency">
                </div>
            </div>
            <div class="form-group">
                <label for="reg-email">Work Email</label>
                <input type="email" id="reg-email" required placeholder="jane@myagency.com" autocomplete="email">
            </div>
            <div class="form-group">
                <label for="reg-industry">Industry Profile</label>
                <select id="reg-industry" style="width: 100%; padding: 10px 14px; font-size: 14px; background: var(--input-bg); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); outline: none; transition: border-color 0.2s, box-shadow 0.2s; font-family: inherit;">
                    <option value="real_estate">Real Estate Agency</option>
                    <option value="photography">Photography Studio</option>
                </select>
            </div>
            <div class="form-group">
                <label for="reg-password">Password</label>
                <input type="password" id="reg-password" required placeholder="Min. 8 characters">
                <button type="button" class="pw-toggle" onclick="togglePw('reg-password','eye-open-1','eye-closed-1')">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" id="eye-open-1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" id="eye-closed-1" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                </button>
            </div>
            <div class="form-group">
                <label for="reg-confirm">Confirm Password</label>
                <input type="password" id="reg-confirm" required placeholder="Re-enter password">
                <button type="button" class="pw-toggle" onclick="togglePw('reg-confirm','eye-open-2','eye-closed-2')">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" id="eye-open-2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" id="eye-closed-2" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                </button>
            </div>
            <button type="submit" class="submit-btn" id="reg-btn">Create Workspace</button>
        </form>
        <?php endif; ?>

        <?php endif; // reg_enabled ?>

        <div class="footer-link">
            Already have an account? <a href="<?php echo esc_url( home_url( '/workspace/login' ) ); ?>">Sign in</a>
        </div>
    </div>

    <!-- Check Inbox State -->
    <div id="inbox-state">
        <div class="inbox-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
            </svg>
        </div>
        <div class="inbox-title">Check your inbox</div>
        <p class="inbox-sub">We sent a verification link to <strong id="inbox-email"></strong>. Click the link to activate your workspace and sign in automatically.</p>
        <p style="font-size:12px; color:var(--text-secondary); margin-bottom:6px;">Didn't receive it?</p>
        <button class="resend-btn" onclick="handleResend()">Resend verification email</button>
        <div class="footer-link" style="margin-top:28px;">
            <a href="<?php echo esc_url( home_url( '/workspace/login' ) ); ?>">← Back to sign in</a>
        </div>
    </div>
</div>

<div id="cora-toast-container"></div>
<script>
    // Light mode standard

    var registeredEmail = '';

    function showToast(msg) {
        var container = document.getElementById('cora-toast-container');
        var toast = document.createElement('div');
        toast.className = 'cora-toast';
        toast.innerHTML = msg;
        container.appendChild(toast);
        setTimeout(function() {
            toast.style.transition = 'opacity 0.3s';
            toast.style.opacity = '0';
            setTimeout(function() { toast.remove(); }, 300);
        }, 4500);
    }

    function togglePw(inputId, openId, closedId) {
        var input = document.getElementById(inputId);
        var open = document.getElementById(openId);
        var closed = document.getElementById(closedId);
        if (input.type === 'password') {
            input.type = 'text';
            open.style.display = 'none';
            closed.style.display = 'block';
        } else {
            input.type = 'password';
            open.style.display = 'block';
            closed.style.display = 'none';
        }
    }

    function showInboxState(email) {
        registeredEmail = email;
        document.getElementById('form-state').style.display = 'none';
        document.getElementById('inbox-state').style.display = 'block';
        document.getElementById('inbox-email').textContent = email;
    }

    function handleRegisterSubmit(e) {
        e.preventDefault();
        var name     = document.getElementById('reg-name').value.trim();
        var agency   = document.getElementById('reg-agency').value.trim();
        var email    = document.getElementById('reg-email').value.trim();
        var password = document.getElementById('reg-password').value;
        var confirm  = document.getElementById('reg-confirm').value;
        var industry = document.getElementById('reg-industry').value;

        if (!name)   { showToast('Please enter your full name.'); return; }
        if (!agency) { showToast('Please enter your agency name.'); return; }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showToast('Please enter a valid email address.'); return; }
        if (password.length < 8) { showToast('Password must be at least 8 characters.'); return; }
        if (password !== confirm) { showToast('Passwords do not match.'); return; }

        var btn = document.getElementById('reg-btn');
        btn.disabled = true;
        btn.textContent = 'Creating workspace…';

        var formData = new FormData();
        formData.append('action', 'cora_self_register');
        formData.append('name', name);
        formData.append('agency', agency);
        formData.append('email', email);
        formData.append('industry', industry);
        formData.append('password', password);
        formData.append('confirm', confirm);
        formData.append('nonce', '<?php echo wp_create_nonce( "cora_login_nonce" ); ?>');

        fetch('<?php echo esc_url( cora_get_origin_relative_url( admin_url( "admin-ajax.php" ) ) ); ?>', {
            method: 'POST',
            body: formData
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success) {
                showInboxState(email);
            } else {
                showToast(res.data.message || 'Something went wrong. Please try again.');
                btn.disabled = false;
                btn.textContent = 'Create Workspace';
            }
        })
        .catch(function() {
            showToast('Network error. Please try again.');
            btn.disabled = false;
            btn.textContent = 'Create Workspace';
        });
    }

    function handleResend() {
        if (!registeredEmail) return;
        showToast('Sending verification link…');
        var formData = new FormData();
        formData.append('action', 'cora_ajax_resend_verification');
        formData.append('email', registeredEmail);
        formData.append('nonce', '<?php echo wp_create_nonce( "cora_login_nonce" ); ?>');
        fetch('<?php echo esc_url( cora_get_origin_relative_url( admin_url( "admin-ajax.php" ) ) ); ?>', { method: 'POST', body: formData })
        .then(function(r) { return r.json(); })
        .then(function(res) { showToast(res.data.message || 'Link sent!'); });
    }

    // Show error from URL params (Google OAuth errors)
    (function() {
        var params = new URLSearchParams(window.location.search);
        var error = params.get('error');
        var messages = {
            'google_disabled'    : 'Google sign-in is not available.',
            'oauth_state'        : 'OAuth security check failed. Please try again.',
            'oauth_token'        : 'Could not connect to Google. Please try again.',
            'user_create'        : 'Failed to create account. Please try email registration.',
            'registration_closed': 'New registrations are currently closed.'
        };
        if (error && messages[error]) showToast(messages[error]);
    })();
</script>
</body>
</html>
