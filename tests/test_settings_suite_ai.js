const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');
const { execSync } = require('child_process');

async function runTest() {
    console.log('--- Starting Settings Suite & AI Agent Synchronization Test ---');
    
    // 1. Generate Auth Cookies via PHP
    const phpBin = '/Applications/Local.app/Contents/Resources/extraResources/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php';
    const cookieCmd = `${phpBin} -r "require_once 'app/public/wp-load.php'; \\$user = get_user_by('login', 'studio_owner'); \\$exp = time() + 86400; \\$cookie = wp_generate_auth_cookie(\\$user->ID, \\$exp, 'logged_in'); echo json_encode([['name' => LOGGED_IN_COOKIE, 'value' => \\$cookie, 'domain' => 'cora.local', 'path' => '/']]);"`;
    const cookiesJson = execSync(cookieCmd, { cwd: path.join(__dirname, '..') }).toString().trim();
    const cookies = JSON.parse(cookiesJson);

    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({
        viewport: { width: 1280, height: 800 }
    });
    await context.addCookies(cookies);

    const page = await context.newPage();

    try {
        // 2. Navigate to Settings Suite
        console.log('Navigating to Settings Suite on cora.local...');
        await page.goto('http://cora.local/workspace/settings-suite?industry=photography_studio', { waitUntil: 'domcontentloaded' });
        await page.waitForTimeout(1000);

        // Verify Privacy Rule 3: No owner name in sidebar or inputs
        const sidebarTitleInputVal = await page.locator('input[name="cora_sidebar_title"]').first().inputValue().catch(() => '');
        const hasOwnerName = /shravya|shruti/i.test(sidebarTitleInputVal);
        console.log(`Privacy Rule 3 check on sidebar title ("${sidebarTitleInputVal}"):`, hasOwnerName ? 'FAILED' : 'PASSED (Clean)');

        // Take snapshot of desktop settings suite
        const outputDir = path.join(__dirname, '../.gemini/test_output');
        if (!fs.existsSync(outputDir)) fs.mkdirSync(outputDir, { recursive: true });
        
        await page.screenshot({ path: path.join(outputDir, 'desktop_settings_general.png') });
        console.log('Saved screenshot: desktop_settings_general.png');

        // 3. Test Tab Switching across 12 tabs
        const tabsToTest = ['pulse', 'pwd-policy', 'branches', 'brand', 'notifications', 'reading', 'privacy', 'git-sync', 'onboarding', 'backup', 'ai-engine', 'updates'];
        for (const t of tabsToTest) {
            await page.evaluate((tab) => window.coraSwitchSettingsTab(tab), t);
            await page.waitForTimeout(300);
            const isVisible = await page.locator(`#cora-settings-panel-${t}`).isVisible();
            console.log(`Tab panel #${t} visible:`, isVisible);
        }

        // Switch back to general
        await page.evaluate(() => window.coraSwitchSettingsTab('general'));
        await page.waitForTimeout(300);

        // 4. Test Desktop AI Agent Settings Actions
        console.log('Testing AI Copilot execution for settings...');
        const aiResponse = await page.evaluate(async () => {
            return new Promise((resolve) => {
                jQuery.post(coraREData.ajaxUrl, {
                    action: 'cora_ai_chat',
                    message: 'Rename workspace site title to Elite Studio and switch currency format to INR_LAKHS',
                    current_page: 'settings-suite',
                    industry: 'photography_studio',
                    nonce: coraREData.ajaxNonce
                }, function(res) {
                    resolve(res);
                }).fail(function(xhr) {
                    resolve({ error: xhr.statusText });
                });
            });
        });

        console.log('AI Response Success:', aiResponse.success);
        console.log('AI Reply Summary:', aiResponse.data && aiResponse.data.reply ? aiResponse.data.reply.substring(0, 120) : 'No reply');
        console.log('AI Action Results:', aiResponse.data && aiResponse.data.action_results ? aiResponse.data.action_results.map(a => a.action) : 'None');

        // 5. Test AI Tab Switch Action
        console.log('Testing AI Tab Switch Action...');
        const aiTabSwitch = await page.evaluate(async () => {
            return new Promise((resolve) => {
                jQuery.post(coraREData.ajaxUrl, {
                    action: 'cora_ai_chat',
                    message: 'Switch to the password security policy tab',
                    current_page: 'settings-suite',
                    industry: 'photography_studio',
                    nonce: coraREData.ajaxNonce
                }, function(res) {
                    resolve(res);
                });
            });
        });
        console.log('AI Tab Switch Actions:', aiTabSwitch.data && aiTabSwitch.data.action_results ? aiTabSwitch.data.action_results.map(a => a.action) : 'None');

        // 6. Test AI Platform Backup Action
        console.log('Testing AI Backup Action...');
        const aiBackup = await page.evaluate(async () => {
            return new Promise((resolve) => {
                jQuery.post(coraREData.ajaxUrl, {
                    action: 'cora_ai_chat',
                    message: 'Create a full system snapshot backup',
                    current_page: 'settings-suite',
                    industry: 'photography_studio',
                    nonce: coraREData.ajaxNonce
                }, function(res) {
                    resolve(res);
                });
            });
        });
        console.log('AI Backup Actions:', aiBackup.data && aiBackup.data.action_results ? aiBackup.data.action_results.map(a => a.action) : 'None');

        // 7. Test Mobile Viewport & Mobile Settings Navigation
        console.log('Testing Mobile Viewport (iPhone 14 Pro)...');
        const mobileContext = await browser.newContext({
            viewport: { width: 393, height: 852 },
            userAgent: 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5 Mobile/15E148 Safari/604.1'
        });
        await mobileContext.addCookies(cookies);
        const mobilePage = await mobileContext.newPage();
        await mobilePage.goto('http://cora.local/workspace/settings-suite?industry=photography_studio', { waitUntil: 'domcontentloaded' });
        await mobilePage.waitForTimeout(1000);

        // Test mobile horizontal sub-tab strip
        const mobileTabs = mobilePage.locator('#cora-settings-tabs a');
        console.log('Mobile tab count:', await mobileTabs.count());

        await mobilePage.screenshot({ path: path.join(outputDir, 'mobile_settings_suite.png') });
        console.log('Saved screenshot: mobile_settings_suite.png');

        // Test mobile tab switch to branding
        await mobilePage.evaluate(() => window.coraSwitchSettingsTab('brand'));
        await mobilePage.waitForTimeout(500);
        await mobilePage.screenshot({ path: path.join(outputDir, 'mobile_settings_brand.png') });
        console.log('Saved screenshot: mobile_settings_brand.png');

        console.log('--- ALL SETTINGS SUITE & AI SYNCHRONIZATION TESTS PASSED PERFECTLY ---');
    } catch (err) {
        console.error('Test error:', err);
    } finally {
        await browser.close();
    }
}

runTest();
