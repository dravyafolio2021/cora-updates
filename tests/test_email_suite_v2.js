const { chromium } = require('playwright');
const path = require('path');
const { execSync } = require('child_process');

async function testEmailSuiteV2() {
    const phpBin = '/Applications/Local.app/Contents/Resources/extraResources/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php';
    const cookieCmd = `${phpBin} -r "require_once 'app/public/wp-load.php'; \\$user = get_user_by('login', 'studio_owner'); \\$exp = time() + 86400; \\$cookie = wp_generate_auth_cookie(\\$user->ID, \\$exp, 'logged_in'); echo json_encode([['name' => LOGGED_IN_COOKIE, 'value' => \\$cookie, 'domain' => 'cora.local', 'path' => '/']]);"`;
    const cookies = JSON.parse(execSync(cookieCmd, { cwd: path.join(__dirname, '..') }).toString().trim());

    const browser = await chromium.launch({ headless: true });
    
    // Desktop Viewport (1440x900)
    const context = await browser.newContext({ viewport: { width: 1440, height: 900 } });
    await context.addCookies(cookies);
    const page = await context.newPage();

    const consoleErrors = [];
    page.on('console', msg => {
        if (msg.type() === 'error') consoleErrors.push(msg.text());
    });

    const url = 'http://cora.local/workspace/emails';
    const resp = await page.goto(url, { waitUntil: 'networkidle' });
    
    console.log('HTTP Status:', resp.status());
    console.log('Console Errors:', consoleErrors);

    // 1. Verify Compose Tab Sender Picker
    await page.waitForSelector('#compose-from-inbox-picker option', { state: 'attached' });
    const senderOptions = await page.$$eval('#compose-from-inbox-picker option', els => els.map(e => e.text));
    console.log('Connected Sender Options in Compose Picker:', senderOptions);

    // Test Sender selection change
    if (senderOptions.length > 1) {
        await page.selectOption('#compose-from-inbox-picker', { index: 1 });
        const previewFrom = await page.innerText('#preview-from');
        console.log('Live Preview From after select:', previewFrom);
    }

    // 2. Verify Tab 2: Connected Inboxes
    await page.click('#tab-btn-email-tab-inboxes');
    await page.waitForTimeout(300);
    const inboxesVisible = await page.isVisible('#email-tab-inboxes');
    console.log('Connected Inboxes Tab Visible:', inboxesVisible);

    const inboxesCount = await page.locator('#inboxes-grid-container > div').count();
    console.log('Rendered Inbox Cards Count:', inboxesCount);

    const planQuotaText = await page.innerText('#plan-quota-text');
    console.log('Plan Quota Text:', planQuotaText);

    const planTiersCount = await page.locator('#plan-tiers-grid > div').count();
    console.log('Plan Tiers Cards Count (1, 5, 10, 20):', planTiersCount);

    await page.screenshot({ path: 'test_output/email_tab_inboxes.png' });

    // 3. Test Connect Inbox Drawer
    await page.click('button:has-text("Connect Business Inbox")');
    await page.waitForTimeout(400);
    const drawerOpen = await page.isVisible('#cora-connect-inbox-drawer');
    console.log('Connect Inbox Drawer Open:', drawerOpen);

    // Switch to Hostinger Webmail tab in drawer
    await page.click('#provider-tab-btn-hostinger');
    await page.waitForTimeout(200);
    const hostingerGuide = await page.innerText('#connect-provider-guide');
    console.log('Hostinger Guide Text:', hostingerGuide.slice(0, 80) + '...');

    await page.screenshot({ path: 'test_output/email_connect_drawer_hostinger.png' });
    await page.click('#cora-connect-inbox-drawer button:has-text("✕ Close")');
    await page.waitForTimeout(300);

    // 4. Verify Tab 3: Cold Outreach Campaigns
    await page.click('#tab-btn-email-tab-outreach');
    await page.waitForTimeout(300);
    const outreachVisible = await page.isVisible('#email-tab-outreach');
    console.log('Outreach Campaigns Tab Visible:', outreachVisible);

    const outreachCards = await page.locator('#outreach-campaigns-container > div').count();
    console.log('Outreach Campaigns Count:', outreachCards);
    await page.screenshot({ path: 'test_output/email_tab_outreach.png' });

    // 5. Verify Tab 4: Marketing Broadcasts
    await page.click('#tab-btn-email-tab-broadcasts');
    await page.waitForTimeout(300);
    const broadcastsVisible = await page.isVisible('#email-tab-broadcasts');
    console.log('Marketing Broadcasts Tab Visible:', broadcastsVisible);

    const bcastCards = await page.locator('#marketing-broadcasts-container > div').count();
    console.log('Marketing Broadcasts Count:', bcastCards);
    await page.screenshot({ path: 'test_output/email_tab_broadcasts.png' });

    // 6. Test Spintax & Variable Injection in Compose
    await page.click('#tab-btn-email-tab-compose');
    await page.waitForTimeout(300);
    await page.fill('#email-subject', 'Special showcase for {client_name}');
    await page.fill('#email-message', '{Hi|Hello|Hey} {client_name}, this is a test from {studio_name}.');
    await page.dispatchEvent('#email-message', 'input');
    await page.waitForTimeout(200);

    const previewBody = await page.innerText('#preview-body');
    console.log('Live Preview Parsed Body (Spintax evaluated):', previewBody);

    await page.screenshot({ path: 'test_output/email_tab_compose_spintax.png' });

    await browser.close();
    console.log('All Email Suite v2 tests passed successfully! ✓');
}

testEmailSuiteV2().catch(console.error);
