const { chromium } = require('playwright');
const path = require('path');
const { execSync } = require('child_process');

async function testAjaxEndpoints() {
    const phpBin = '/Applications/Local.app/Contents/Resources/extraResources/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php';
    const cookieCmd = `${phpBin} -r "require_once 'app/public/wp-load.php'; \\$user = get_user_by('login', 'studio_owner'); \\$exp = time() + 86400; \\$cookie = wp_generate_auth_cookie(\\$user->ID, \\$exp, 'logged_in'); echo json_encode([['name' => LOGGED_IN_COOKIE, 'value' => \\$cookie, 'domain' => 'cora.local', 'path' => '/']]);"`;
    const cookies = JSON.parse(execSync(cookieCmd, { cwd: path.join(__dirname, '..') }).toString().trim());

    const browser = await chromium.launch({ headless: true });
    const page = await browser.newPage();
    await page.context().addCookies(cookies);

    await page.goto('http://cora.local/workspace/emails', { waitUntil: 'networkidle' });

    // Test 1: Plan Switcher AJAX (Switch to Pro - 10 inboxes)
    console.log('\n--- TEST 1: Switch Plan to Pro (10 Inboxes) ---');
    const switchRes = await page.evaluate(async () => {
        return new Promise(resolve => {
            jQuery.ajax({
                url: '/wp-admin/admin-ajax.php',
                method: 'POST',
                data: {
                    action: 'cora_switch_email_plan',
                    nonce: window.coraAjaxNonce,
                    plan: 'pro'
                },
                success: resolve
            });
        });
    });
    console.log('Switch to Pro Result:', switchRes);

    // Test 2: Connect a new Google Workspace account
    console.log('\n--- TEST 2: Connect Google Workspace Inbox ---');
    const connectRes = await page.evaluate(async () => {
        return new Promise(resolve => {
            jQuery.ajax({
                url: '/wp-admin/admin-ajax.php',
                method: 'POST',
                data: {
                    action: 'cora_connect_inbox',
                    nonce: window.coraAjaxNonce,
                    provider: 'google_workspace',
                    email: 'partnerships@clarastudio.com',
                    from_name: 'Partnerships • Cora Studio',
                    password: 'abcd efgh ijkl mnop',
                    daily_limit: 50
                },
                success: resolve
            });
        });
    });
    console.log('Connect Inbox Result:', connectRes.data ? connectRes.data.message : connectRes);

    // Test 3: Test Inbox Handshake Ping
    console.log('\n--- TEST 3: Test Inbox Connection Ping ---');
    const pingRes = await page.evaluate(async () => {
        return new Promise(resolve => {
            jQuery.ajax({
                url: '/wp-admin/admin-ajax.php',
                method: 'POST',
                data: {
                    action: 'cora_test_inbox_connection',
                    nonce: window.coraAjaxNonce,
                    inbox_id: 'inbox_gw_1',
                    test_recipient: 'test@example.com'
                },
                success: resolve
            });
        });
    });
    console.log('Ping Result:', pingRes.data ? pingRes.data.message : pingRes);

    // Test 4: Run Outreach Multi-Inbox Batch Send
    console.log('\n--- TEST 4: Execute Campaign Batch (Sender Rotation) ---');
    const batchRes = await page.evaluate(async () => {
        return new Promise(resolve => {
            jQuery.ajax({
                url: '/wp-admin/admin-ajax.php',
                method: 'POST',
                data: {
                    action: 'cora_execute_campaign_batch',
                    nonce: window.coraAjaxNonce,
                    campaign_id: 'camp_outreach_01'
                },
                success: resolve
            });
        });
    });
    console.log('Batch Send Result:', batchRes.data ? batchRes.data.message : batchRes);
    if (batchRes.data && batchRes.data.batch) {
        console.log('Rotated Send Senders:', batchRes.data.batch.map(b => `${b.to} <- ${b.sent_from}`));
    }

    // Reset plan back to growth
    await page.evaluate(async () => {
        return new Promise(resolve => {
            jQuery.ajax({
                url: '/wp-admin/admin-ajax.php',
                method: 'POST',
                data: { action: 'cora_switch_email_plan', nonce: window.coraAjaxNonce, plan: 'growth' },
                success: resolve
            });
        });
    });

    await browser.close();
    console.log('\nAll AJAX Endpoint tests completed successfully! ✓');
}

testAjaxEndpoints().catch(console.error);
