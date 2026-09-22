const { chromium } = require('playwright');
const path = require('path');
const { execSync } = require('child_process');

async function testAllRoutes() {
    const phpBin = '/Applications/Local.app/Contents/Resources/extraResources/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php';
    const cookieCmd = `${phpBin} -r "require_once 'app/public/wp-load.php'; \\$user = get_user_by('login', 'studio_owner'); \\$exp = time() + 86400; \\$cookie = wp_generate_auth_cookie(\\$user->ID, \\$exp, 'logged_in'); echo json_encode([['name' => LOGGED_IN_COOKIE, 'value' => \\$cookie, 'domain' => 'cora.local', 'path' => '/']]);"`;
    const cookies = JSON.parse(execSync(cookieCmd, { cwd: path.join(__dirname, '..') }).toString().trim());

    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext();
    await context.addCookies(cookies);
    const page = await context.newPage();

    const routes = [
        { name: 'Studio Dashboard', url: 'http://cora.local/workspace/dashboard?industry=photography_studio' },
        { name: 'Real Estate Dashboard', url: 'http://cora.local/workspace/dashboard?industry=real_estate' },
        { name: 'Content Workflow', url: 'http://cora.local/workspace/dashboard?page=content-workflow' },
        { name: 'Documents Vault', url: 'http://cora.local/workspace/dashboard?page=documents-vault' },
        { name: 'Media Library', url: 'http://cora.local/workspace/dashboard?page=media-library' },
        { name: 'Leads Pipeline', url: 'http://cora.local/workspace/dashboard?page=leads-pipeline' },
    ];

    const results = [];

    for (const route of routes) {
        const consoleErrors = [];
        page.on('console', msg => {
            if (msg.type() === 'error') {
                consoleErrors.push(msg.text());
            }
        });

        const failedRequests = [];
        page.on('requestfailed', req => {
            failedRequests.push(`${req.method()} ${req.url()}: ${req.failure() ? req.failure().errorText : 'failed'}`);
        });

        const t0 = Date.now();
        const resp = await page.goto(route.url, { waitUntil: 'domcontentloaded' });
        const t1 = Date.now();

        const status = resp.status();
        results.push({
            name: route.name,
            status,
            loadTimeMs: t1 - t0,
            errorsCount: consoleErrors.length,
            errors: consoleErrors,
            failedRequests
        });
    }

    console.log(JSON.stringify(results, null, 2));
    await browser.close();
}

testAllRoutes().catch(console.error);
