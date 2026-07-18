<?php
/**
 * Cora Unified Onboarding Landing Page - demo.heycora.in
 * 
 * Interactive SaaS Subscription Cost Calculator + 10-Second Sandbox Signup Form.
 * Minimalist monochromatic aesthetic.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cora AI — Real Estate Tech Audit & Workspace Generator</title>
    
    <!-- Enqueue Inter Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
    
    <!-- Load jQuery (WordPress standard) -->
    <?php wp_print_scripts( array( 'jquery' ) ); ?>

    <style>
        :root {
            --bg-color: #FBFaf7; /* Warm cream */
            --primary-color: #18181b;
            --text-color: #27272a;
            --border-color: #e4e4e7;
            --font-sans: 'Inter', sans-serif;
            --font-serif: 'Playfair Display', serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: var(--font-sans);
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-font-smoothing: antialiased;
        }

        .cora-landing-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 24px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-sizing: border-box;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 30px;
            border-bottom: 1px solid rgba(24, 24, 27, 0.05);
        }

        .logo-block {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 16px;
            color: var(--primary-color);
            letter-spacing: -0.03em;
        }

        .logo-circle {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .badge-trial {
            background-color: #f4f4f5;
            color: #71717a;
            border: 1px solid var(--border-color);
            font-size: 9px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .hero-section {
            display: grid;
            grid-template-cols: 1fr;
            gap: 48px;
            padding: 40px 0;
            align-items: start;
        }

        @media (min-width: 768px) {
            .hero-section {
                grid-template-cols: 1.1fr 0.9fr;
            }
        }

        .hero-info {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .hero-tagline {
            font-family: var(--font-serif);
            font-style: italic;
            font-size: 18px;
            color: #71717a;
            margin: 0;
        }

        .hero-title {
            font-size: 36px;
            font-weight: 800;
            color: var(--primary-color);
            line-height: 1.15;
            margin: 0;
            letter-spacing: -0.04em;
        }

        .hero-desc {
            font-size: 13.5px;
            color: #52525b;
            line-height: 1.6;
            margin: 0;
        }

        /* Interactive Calculator Styles */
        .calculator-card {
            background-color: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(24, 24, 27, 0.02);
            margin-top: 10px;
        }

        .calculator-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #71717a;
            margin-bottom: 16px;
            border-bottom: 1px solid #f4f4f5;
            padding-bottom: 10px;
        }

        .calc-tool-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed #f4f4f5;
        }

        .calc-tool-row:last-child {
            border-bottom: none;
        }

        .calc-tool-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .calc-checkbox {
            width: 16px;
            height: 16px;
            accent-color: var(--primary-color);
            cursor: pointer;
        }

        .calc-tool-label {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-color);
            cursor: pointer;
        }

        .calc-tool-desc {
            font-size: 10.5px;
            color: #a1a1aa;
            display: block;
            margin-top: 2px;
        }

        .calc-tool-cost {
            font-family: monospace;
            font-size: 13px;
            font-weight: 700;
            color: #ef4444;
        }

        .calc-results-bar {
            margin-top: 20px;
            background-color: #fef2f2;
            border: 1px solid #fca5a5;
            border-radius: 8px;
            padding: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .calc-results-left {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #991b1b;
        }

        .calc-results-value {
            font-size: 20px;
            font-weight: 800;
            color: #991b1b;
        }

        .calc-savings-box {
            margin-top: 12px;
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 8px;
            padding: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .calc-savings-left {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #065f46;
        }

        .calc-savings-value {
            font-size: 20px;
            font-weight: 800;
            color: #065f46;
        }

        /* Right Column Form */
        .signup-card {
            background-color: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 10px 30px rgba(24, 24, 27, 0.03);
            display: flex;
            flex-direction: column;
            gap: 18px;
            position: sticky;
            top: 40px;
        }

        .signup-header {
            text-align: center;
        }

        .signup-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
            letter-spacing: -0.02em;
        }

        .signup-subtitle {
            font-size: 11px;
            color: #a1a1aa;
            margin: 4px 0 0 0;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-label {
            font-size: 10.5px;
            font-weight: 600;
            color: #71717a;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 13px;
            box-sizing: border-box;
            background-color: #ffffff;
            color: var(--primary-color);
            font-family: var(--font-sans);
            transition: border-color 0.15s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .form-input::placeholder {
            color: #d4d4d8;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: var(--primary-color);
            color: #ffffff;
            border: 0;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.15s ease, transform 0.1s ease;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background-color: #27272a;
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        .btn-submit:disabled {
            background-color: #a1a1aa;
            cursor: not-allowed;
            transform: none;
        }

        .form-footer {
            font-size: 10px;
            color: #a1a1aa;
            text-align: center;
            margin: 0;
        }

        footer {
            text-align: center;
            padding-top: 40px;
            border-top: 1px solid rgba(24, 24, 27, 0.05);
            font-size: 11px;
            color: #a1a1aa;
        }

        /* Monochromatic Toast Notification styles */
        .cora-toast {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background-color: var(--primary-color);
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            z-index: 1000000;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            pointer-events: none;
            white-space: nowrap;
        }

        .cora-toast.show {
            transform: translateX(-50%) translateY(0);
        }
    </style>
</head>
<body>

<div class="cora-landing-container">
    <header>
        <div class="logo-block">
            <div class="logo-circle">C</div>
            <span>Cora AI</span>
        </div>
        <span class="badge-trial">Free Sandbox</span>
    </header>

    <main class="hero-section">
        <div class="hero-info">
            <h2 class="hero-tagline">Stop the SaaS subscription bleed.</h2>
            <h1 class="hero-title">Unified Workspace for High-Speed Real Estate Agencies</h1>
            <p class="hero-desc">Indian brokerages lose ₹6 Lakhs annually to fragmented tools. Cora consolidates your listings, CRM, field GPS agent check-ins, social scheduling, and instant WhatsApp brochure routing into a single dashboard.</p>
            
            <!-- Interactive Stack Auditor Calculator -->
            <div class="calculator-card">
                <div class="calculator-title">Tool Subscription Cost Auditor</div>
                
                <div class="calc-tool-row">
                    <div class="calc-tool-left">
                        <input type="checkbox" id="calc-crm" class="calc-checkbox" value="25000" checked onchange="calculateSavings()" />
                        <div>
                            <label for="calc-crm" class="calc-tool-label">Lead CRM & Pipelines</label>
                            <span class="calc-tool-desc">e.g. Sell.do / Salesforce (for 10 agents)</span>
                        </div>
                    </div>
                    <div class="calc-tool-cost">₹25,000/mo</div>
                </div>

                <div class="calc-tool-row">
                    <div class="calc-tool-left">
                        <input type="checkbox" id="calc-hr" class="calc-checkbox" value="7000" checked onchange="calculateSavings()" />
                        <div>
                            <label for="calc-hr" class="calc-tool-label">Field Agent GPS Attendance</label>
                            <span class="calc-tool-desc">e.g. Keka HR / Spine HR</span>
                        </div>
                    </div>
                    <div class="calc-tool-cost">₹7,000/mo</div>
                </div>

                <div class="calc-tool-row">
                    <div class="calc-tool-left">
                        <input type="checkbox" id="calc-wa" class="calc-checkbox" value="2000" checked onchange="calculateSavings()" />
                        <div>
                            <label for="calc-wa" class="calc-tool-label">WhatsApp Business API</label>
                            <span class="calc-tool-desc">e.g. Wati / AiSensy / Interakt</span>
                        </div>
                    </div>
                    <div class="calc-tool-cost">₹2,000/mo</div>
                </div>

                <div class="calc-tool-row">
                    <div class="calc-tool-left">
                        <input type="checkbox" id="calc-drive" class="calc-checkbox" value="13000" checked onchange="calculateSavings()" />
                        <div>
                            <label for="calc-drive" class="calc-tool-label">Google Drive Storage (10 Users)</label>
                            <span class="calc-tool-desc">For heavy 4K site videos & KYC scans</span>
                        </div>
                    </div>
                    <div class="calc-tool-cost">₹13,000/mo</div>
                </div>

                <div class="calc-tool-row">
                    <div class="calc-tool-left">
                        <input type="checkbox" id="calc-social" class="calc-checkbox" value="2500" checked onchange="calculateSavings()" />
                        <div>
                            <label for="calc-social" class="calc-tool-label">Social Media Scheduler</label>
                            <span class="calc-tool-desc">e.g. Hootsuite / Buffer</span>
                        </div>
                    </div>
                    <div class="calc-tool-cost">₹2,500/mo</div>
                </div>

                <!-- Monthly Spend Output -->
                <div class="calc-results-bar">
                    <div class="calc-results-left">Current Monthly Spend:</div>
                    <div class="calc-results-value" id="calc-outflow">₹49,500</div>
                </div>

                <!-- Savings with Cora Output -->
                <div class="calc-savings-box">
                    <div class="calc-savings-left">Cora Annual Savings:</div>
                    <div class="calc-savings-value" id="calc-savings">₹5,70,000</div>
                </div>
            </div>
        </div>

        <!-- Right Side Onboarding Form -->
        <div class="signup-card">
            <div class="signup-header">
                <h3 class="signup-title">Launch Free Sandbox Site</h3>
                <p class="signup-subtitle">Zero hosting setup. Pre-seeded demo database.</p>
            </div>

            <form id="cora-signup-form" onsubmit="event.preventDefault(); handleCoraSignup();">
                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label">Full Name</label>
                    <input type="text" id="signup-name" class="form-input" placeholder="e.g. Dravya Bansal" required />
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label">Agency Name</label>
                    <input type="text" id="signup-agency" class="form-input" placeholder="e.g. Apex Realty" required />
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label">WhatsApp Number</label>
                    <input type="tel" id="signup-whatsapp" class="form-input" placeholder="e.g. +919876543210" required />
                </div>

                <div class="form-group" style="margin-bottom: 18px;">
                    <label class="form-label">City</label>
                    <input type="text" id="signup-city" class="form-input" placeholder="e.g. Gurgaon" required />
                </div>

                <button type="submit" id="submit-btn" class="btn-submit">
                    Spin Up My Workspace
                </button>
            </form>

            <p class="form-footer">By launching, you get 30 days free access.</p>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Cora Platform. Built for Indian Real Estate Agencies.</p>
    </footer>
</div>

<!-- Toast Overlay -->
<div id="cora-toast" class="cora-toast">Workspace generated! Redirecting...</div>

<script>
    function formatINR(number) {
        return '₹' + number.toLocaleString('en-IN');
    }

    function calculateSavings() {
        var totalMonthly = 0;
        var checkboxes = ['calc-crm', 'calc-hr', 'calc-wa', 'calc-drive', 'calc-social'];
        
        checkboxes.forEach(function(id) {
            var cb = document.getElementById(id);
            if (cb && cb.checked) {
                totalMonthly += parseInt(cb.value);
            }
        });

        var annualToolSpend = totalMonthly * 12;
        var coraProAnnual = 2000 * 12;
        var totalSavings = Math.max(0, annualToolSpend - coraProAnnual);

        document.getElementById('calc-outflow').innerHTML = formatINR(totalMonthly);
        document.getElementById('calc-savings').innerHTML = formatINR(totalSavings);
    }

    // Run initial calculations
    calculateSavings();

    function showToast(message) {
        var toast = document.getElementById('cora-toast');
        toast.innerHTML = message;
        toast.classList.add('show');
        setTimeout(function() {
            toast.classList.remove('show');
        }, 3000);
    }

    function handleCoraSignup() {
        var name = document.getElementById('signup-name').value;
        var agency = document.getElementById('signup-agency').value;
        var whatsapp = document.getElementById('signup-whatsapp').value;
        var city = document.getElementById('signup-city').value;
        var btn = document.getElementById('submit-btn');

        btn.disabled = true;
        btn.innerHTML = 'Provisioning Sandbox (5s)...';

        showToast("Spinning up subsite databases and assets...");

        jQuery.post('<?php echo admin_url("admin-ajax.php"); ?>', {
            action: 'cora_trial_signup',
            name: name,
            agency_name: agency,
            whatsapp: whatsapp,
            city: city,
            _nonce: '<?php echo wp_create_nonce("cora_trial_signup"); ?>'
        }, function(response) {
            if (response.success) {
                showToast("Workspace ready! Opening dashboard...");
                setTimeout(function() {
                    window.location.href = response.data.workspace_url;
                }, 1000);
            } else {
                btn.disabled = false;
                btn.innerHTML = 'Spin Up My Workspace';
                
                if (response.data && response.data.redirect_url) {
                    showToast("Redirecting to existing active workspace...");
                    setTimeout(function() {
                        window.location.href = response.data.redirect_url;
                    }, 1500);
                } else {
                    showToast(response.data.message || "Failed to generate workspace.");
                }
            }
        }).fail(function() {
            btn.disabled = false;
            btn.innerHTML = 'Spin Up My Workspace';
            showToast("Server connection error. Please try again.");
        });
    }
</script>

</body>
</html>
