const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');
const { execSync } = require('child_process');

async function runAudit() {
    console.log('====================================================');
    console.log('   CORA PLATFORM PERFORMANCE AUDIT BENCHMARK');
    console.log('====================================================');

    const phpBin = '/Applications/Local.app/Contents/Resources/extraResources/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php';
    const cookieCmd = `${phpBin} -r "require_once 'app/public/wp-load.php'; \\$user = get_user_by('login', 'studio_owner'); \\$exp = time() + 86400; \\$cookie = wp_generate_auth_cookie(\\$user->ID, \\$exp, 'logged_in'); echo json_encode([['name' => LOGGED_IN_COOKIE, 'value' => \\$cookie, 'domain' => 'cora.local', 'path' => '/']]);"`;
    const cookiesJson = execSync(cookieCmd, { cwd: path.join(__dirname, '..') }).toString().trim();
    const cookies = JSON.parse(cookiesJson);

    const routes = [
        { name: 'Public Landing', url: 'http://cora.local/', auth: false },
        { name: 'Login Screen', url: 'http://cora.local/workspace/login', auth: false },
        { name: 'Studio Dashboard', url: 'http://cora.local/workspace/dashboard?industry=photography_studio', auth: true },
        { name: 'Real Estate Dashboard', url: 'http://cora.local/workspace/dashboard?industry=real_estate', auth: true },
        { name: 'Leads CRM', url: 'http://cora.local/workspace/leads', auth: true },
        { name: 'Invoicing & GST', url: 'http://cora.local/workspace/invoicing', auth: true },
        { name: 'Galleries & Proofing', url: 'http://cora.local/workspace/galleries', auth: true },
        { name: 'Settings Suite (12 Modules)', url: 'http://cora.local/workspace/settings-suite', auth: true },
        { name: 'MCP Gateway & AI Models', url: 'http://cora.local/workspace/mcp-gateway', auth: true },
        { name: 'SEO & GEO Content Hub', url: 'http://cora.local/workspace/seo-geo', auth: true }
    ];

    const browser = await chromium.launch({ headless: true });

    const results = [];

    for (const route of routes) {
        const context = await browser.newContext({
            viewport: { width: 1280, height: 800 }
        });
        if (route.auth) {
            await context.addCookies(cookies);
        }

        const page = await context.newPage();
        
        let requestCount = 0;
        let totalBytes = 0;
        const resourcesByType = {};

        page.on('response', async (response) => {
            requestCount++;
            try {
                const headers = response.headers();
                const contentLength = headers['content-length'] ? parseInt(headers['content-length'], 10) : 0;
                totalBytes += contentLength;
                
                const type = response.request().resourceType();
                resourcesByType[type] = (resourcesByType[type] || 0) + 1;
            } catch (e) {}
        });

        const startTime = Date.now();
        await page.goto(route.url, { waitUntil: 'load', timeout: 30000 });
        const totalLoadTime = Date.now() - startTime;

        const perfMetrics = await page.evaluate(() => {
            const nav = performance.getEntriesByType('navigation')[0];
            const paint = performance.getEntriesByType('paint');
            const fcp = paint.find(p => p.name === 'first-contentful-paint');
            
            return {
                ttfb: nav ? Math.round(nav.responseStart - nav.requestStart) : 0,
                domInteractive: nav ? Math.round(nav.domInteractive - nav.startTime) : 0,
                domContentLoaded: nav ? Math.round(nav.domContentLoadedEventEnd - nav.startTime) : 0,
                loadEvent: nav ? Math.round(nav.loadEventEnd - nav.startTime) : 0,
                fcp: fcp ? Math.round(fcp.startTime) : 0
            };
        });

        // Measure LCP using PerformanceObserver if available
        const lcp = await page.evaluate(async () => {
            return new Promise((resolve) => {
                let lcpValue = 0;
                const observer = new PerformanceObserver((entryList) => {
                    const entries = entryList.getEntries();
                    const lastEntry = entries[entries.length - 1];
                    if (lastEntry) lcpValue = Math.round(lastEntry.startTime);
                });
                observer.observe({ type: 'largest-contentful-paint', buffered: true });
                setTimeout(() => {
                    observer.disconnect();
                    resolve(lcpValue);
                }, 300);
            });
        });

        results.push({
            route: route.name,
            url: route.url,
            totalLoadTime,
            ttfb: perfMetrics.ttfb,
            fcp: perfMetrics.fcp,
            lcp: lcp || perfMetrics.fcp,
            domContentLoaded: perfMetrics.domContentLoaded,
            loadEvent: perfMetrics.loadEvent,
            requests: requestCount,
            sizeKb: Math.round(totalBytes / 1024),
            resources: resourcesByType
        });

        await context.close();
    }

    await browser.close();

    console.table(results.map(r => ({
        Route: r.route,
        'TTFB (ms)': r.ttfb,
        'FCP (ms)': r.fcp,
        'LCP (ms)': r.lcp,
        'DCL (ms)': r.domContentLoaded,
        'Load (ms)': r.totalLoadTime,
        'Reqs': r.requests,
        'Size (KB)': r.sizeKb
    })));

    // Check pass/fail against sub 2-second budget (2000ms)
    const failing = results.filter(r => r.totalLoadTime > 2000 || r.lcp > 2000);
    console.log('\n--- Performance Summary ---');
    console.log(`Total Routes Tested: ${results.length}`);
    console.log(`Routes < 2.0s: ${results.length - failing.length} / ${results.length}`);
    if (failing.length > 0) {
        console.log(`Routes exceeding 2.0s:`, failing.map(f => `${f.route} (${f.totalLoadTime}ms)`));
    } else {
        console.log('✅ ALL routes load under 2.0s in local benchmark!');
    }
}

runAudit().catch(console.error);
