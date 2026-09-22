const { chromium } = require('playwright');
const path = require('path');
const { execSync } = require('child_process');

async function inspectRoute() {
    const phpBin = '/Applications/Local.app/Contents/Resources/extraResources/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php';
    const cookieCmd = `${phpBin} -r "require_once 'app/public/wp-load.php'; \\$user = get_user_by('login', 'studio_owner'); \\$exp = time() + 86400; \\$cookie = wp_generate_auth_cookie(\\$user->ID, \\$exp, 'logged_in'); echo json_encode([['name' => LOGGED_IN_COOKIE, 'value' => \\$cookie, 'domain' => 'cora.local', 'path' => '/']]);"`;
    const cookies = JSON.parse(execSync(cookieCmd, { cwd: path.join(__dirname, '..') }).toString().trim());

    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext();
    await context.addCookies(cookies);
    const page = await context.newPage();

    const requests = [];

    page.on('request', req => {
        requests.push({
            url: req.url(),
            method: req.method(),
            resourceType: req.resourceType(),
            startTime: Date.now()
        });
    });

    page.on('requestfinished', req => {
        const item = requests.find(r => r.url === req.url());
        if (item) item.duration = Date.now() - item.startTime;
    });

    page.on('requestfailed', req => {
        console.log('FAILED REQUEST:', req.url(), req.failure().errorText);
    });

    console.log('Navigating to Studio Dashboard...');
    const t0 = Date.now();
    await page.goto('http://cora.local/workspace/dashboard?industry=photography_studio', { waitUntil: 'load' });
    const t1 = Date.now();
    console.log(`Page Load took ${t1 - t0}ms`);

    // Check external network requests
    console.log('\n--- Network Requests Breakdown ---');
    requests.forEach(r => {
        const isExternal = !r.url.includes('cora.local');
        console.log(`[${r.resourceType}] ${r.duration || 0}ms ${isExternal ? '(EXTERNAL) ' : ''}${r.url.substring(0, 100)}`);
    });

    // Check DOM element count and inline script executions
    const stats = await page.evaluate(() => {
        return {
            domElements: document.getElementsByTagName('*').length,
            scripts: Array.from(document.querySelectorAll('script')).map(s => s.src || 'inline: ' + s.innerText.substring(0, 50)),
            styles: Array.from(document.querySelectorAll('link[rel="stylesheet"], style')).map(s => s.href || 'inline-style')
        };
    });

    console.log(`\nTotal DOM Elements: ${stats.domElements}`);
    console.log(`Total Scripts: ${stats.scripts.length}`);
    console.log(`Total Styles: ${stats.styles.length}`);

    await browser.close();
}

inspectRoute().catch(console.error);
