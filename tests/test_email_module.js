const { chromium } = require('playwright');
const path = require('path');
const { execSync } = require('child_process');

async function testEmailModule() {
    const phpBin = '/Applications/Local.app/Contents/Resources/extraResources/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php';
    const cookieCmd = `${phpBin} -r "require_once 'app/public/wp-load.php'; \\$user = get_user_by('login', 'studio_owner'); \\$exp = time() + 86400; \\$cookie = wp_generate_auth_cookie(\\$user->ID, \\$exp, 'logged_in'); echo json_encode([['name' => LOGGED_IN_COOKIE, 'value' => \\$cookie, 'domain' => 'cora.local', 'path' => '/']]);"`;
    const cookies = JSON.parse(execSync(cookieCmd, { cwd: path.join(__dirname, '..') }).toString().trim());

    const browser = await chromium.launch({ headless: true });
    
    // Test 1: Mobile Viewport (iPhone 14, 390x844)
    const mobileContext = await browser.newContext({ viewport: { width: 390, height: 844 } });
    await mobileContext.addCookies(cookies);
    const mobilePage = await mobileContext.newPage();

    const consoleErrors = [];
    mobilePage.on('console', msg => {
        if (msg.type() === 'error') consoleErrors.push(msg.text());
    });

    const url = 'http://cora.local/workspace/emails';
    const resp = await mobilePage.goto(url, { waitUntil: 'networkidle' });
    
    console.log('Mobile HTTP Status:', resp.status());
    console.log('Mobile Console Errors Count:', consoleErrors.length);

    const kpiCount = await mobilePage.locator('.grid.grid-cols-2 > div').count();
    console.log('Mobile KPI Cards Count (2x2 grid):', kpiCount);

    const subTabs = await mobilePage.locator('.cora-email-sub-tab').count();
    console.log('Sub Tabs Count:', subTabs);

    const activeTab = await mobilePage.locator('.cora-email-sub-tab.border-zinc-950').innerText();
    console.log('Active Sub Tab:', activeTab);

    const options = await mobilePage.$$eval('#compose-recipient-picker option', els => els.map(e => e.text));
    console.log('Recipient Picker Rendered Options:', options.slice(0, 8));

    await mobilePage.screenshot({ path: 'test_output/mobile_email_module_optimized.png' });
    console.log('Saved mobile screenshot to test_output/mobile_email_module_optimized.png');

    // Test 2: Desktop Viewport (1440x900)
    const desktopContext = await browser.newContext({ viewport: { width: 1440, height: 900 } });
    await desktopContext.addCookies(cookies);
    const desktopPage = await desktopContext.newPage();

    await desktopPage.goto(url, { waitUntil: 'networkidle' });
    await desktopPage.screenshot({ path: 'test_output/desktop_email_module_optimized.png' });
    console.log('Saved desktop screenshot to test_output/desktop_email_module_optimized.png');

    // Test Tab 2: Email Templates
    await desktopPage.click('#tab-btn-email-tab-templates');
    console.log('Templates Tab active:', await desktopPage.isVisible('#email-tab-templates'));

    // Test Tab 3: Automated Sequences
    await desktopPage.click('#tab-btn-email-tab-sequences');
    console.log('Sequences Tab active:', await desktopPage.isVisible('#email-tab-sequences'));

    // Test Tab 4: Outbox Logs
    await desktopPage.click('#tab-btn-email-tab-outbox');
    console.log('Outbox Tab active:', await desktopPage.isVisible('#email-tab-outbox'));

    // Test Tab 5: SMTP Settings
    await desktopPage.click('#tab-btn-email-tab-settings');
    console.log('SMTP Settings Tab active:', await desktopPage.isVisible('#email-tab-settings'));

    await browser.close();
}

testEmailModule().catch(console.error);
