import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Cora PWA & Mobile Performance Suite', () => {
    test('verify cora-manifest.json dynamic output and light mode splash configuration', async ({ request }) => {
        const response = await request.get('http://cora.local/cora-manifest.json');
        expect(response.status()).toBe(200);
        
        const manifest = await response.json();
        expect(manifest.name).toBe('CORA Workspace');
        expect(manifest.theme_color).toBe('#ffffff');
        expect(manifest.background_color).toBe('#ffffff');
        expect(manifest.display).toBe('standalone');
        expect(manifest.launch_handler?.client_mode).toBe('navigate-existing');
        expect(manifest.handle_links).toBe('preferred');
        
        // Ensure icon URLs contain dynamic version query stamps
        expect(manifest.icons.length).toBeGreaterThanOrEqual(4);
        for (const icon of manifest.icons) {
            expect(icon.src).toContain('?v=');
            expect(icon.src).toMatch(/^http:\/\/cora\.local/);
        }
    });

    test('verify cora-service-worker.js dynamic versioning and cache control headers', async ({ request }) => {
        const response = await request.get('http://cora.local/cora-service-worker.js');
        expect(response.status()).toBe(200);
        expect(response.headers()['service-worker-allowed']).toBe('/');
        expect(response.headers()['cache-control']).toContain('no-cache');
        
        const text = await response.text();
        expect(text).toContain("const CORA_VERSION = '4.0.1';");
        expect(text).toContain("const CACHE_NAME = 'cora-workspace-v' + CORA_VERSION;");
        expect(text).not.toContain('%%VERSION%%');
        expect(text).not.toContain('%%PLUGIN_URL%%');
    });

    test('verify version-check REST endpoint returns accurate platform release details', async ({ request }) => {
        const response = await request.get('http://cora.local/wp-json/cora-pwa/v1/version-check');
        expect(response.status()).toBe(200);
        
        const data = await response.json();
        expect(data.success).toBe(true);
        expect(data.version).toBe('4.0.1');
        expect(data.release_notes.length).toBeGreaterThan(0);
        expect(data.icon_url).toContain('icon_192.png?v=4.0.1');
    });

    test('verify mobile viewport responsiveness and zero horizontal scroll at 375px', async ({ page }) => {
        await page.setViewportSize({ width: 375, height: 812 });
        
        await login(page);
        await page.goto('http://cora.local/workspace/dashboard');
        await page.waitForSelector('#cora-workspace');
        
        // Verify theme color meta tag is light mode white
        const themeColorMeta = await page.getAttribute('meta[name="theme-color"]', 'content');
        expect(themeColorMeta).toBe('#ffffff');
        
        // Verify scroll width does not exceed viewport width
        const scrollWidth = await page.evaluate(() => document.documentElement.scrollWidth);
        expect(scrollWidth).toBeLessThanOrEqual(375);
    });

    test('verify mobile URL is never mutated to sub_page=leads and refresh stays strictly on active view', async ({ page }) => {
        await page.setViewportSize({ width: 375, height: 812 });
        await login(page);
        
        // 1. Dashboard View
        await page.goto('http://cora.local/workspace/dashboard');
        await page.waitForSelector('#cora-workspace');
        await page.waitForTimeout(500); // Allow DOM ready and subtab auto-selectors to run
        
        // URL must stay clean without rogue sub_page=leads query param
        expect(page.url()).toBe('http://cora.local/workspace/dashboard');
        expect(page.url()).not.toContain('sub_page=leads');
        expect(page.url()).not.toContain('subtab=directory');
        
        // Refresh and verify retention
        await page.reload();
        await page.waitForSelector('#cora-workspace');
        expect(page.url()).toBe('http://cora.local/workspace/dashboard');
        expect(page.url()).not.toContain('/workspace/leads');
        expect(page.url()).not.toContain('/workspace/bookings');
        
        // 2. Financials View
        await page.goto('http://cora.local/workspace/financials');
        await page.waitForSelector('#cora-workspace');
        await page.waitForTimeout(500);
        expect(page.url()).toBe('http://cora.local/workspace/financials');
        expect(page.url()).not.toContain('sub_page=leads');
        
        await page.reload();
        await page.waitForSelector('#cora-workspace');
        expect(page.url()).toBe('http://cora.local/workspace/financials');
        expect(page.url()).not.toContain('/workspace/leads');
        
        // 3. Vault View
        await page.goto('http://cora.local/workspace/vault');
        await page.waitForSelector('#cora-workspace');
        await page.waitForTimeout(500);
        expect(page.url()).toBe('http://cora.local/workspace/vault');
        expect(page.url()).not.toContain('sub_page=leads');
        
        await page.reload();
        await page.waitForSelector('#cora-workspace');
        expect(page.url()).toBe('http://cora.local/workspace/vault');
        expect(page.url()).not.toContain('/workspace/leads');
        
        // 4. Blogs View
        await page.goto('http://cora.local/workspace/blogs');
        await page.waitForSelector('#cora-workspace');
        await page.waitForTimeout(500);
        expect(page.url()).toContain('/workspace/blogs');
        expect(page.url()).not.toContain('sub_page=leads');
        
        await page.reload();
        await page.waitForSelector('#cora-workspace');
        expect(page.url()).toContain('/workspace/blogs');
        expect(page.url()).not.toContain('/workspace/leads');
    });

    test('verify PWA dynamic update banner and floating pill', async ({ page }) => {
        await page.setViewportSize({ width: 375, height: 812 });
        await login(page);
        await page.goto('http://cora.local/workspace/dashboard');
        await page.waitForSelector('#cora-workspace');
        
        // Trigger update banner
        await page.evaluate(() => {
            window.coraShowPwaUpdateBanner('4.0.0', '4.0.1');
        });
        
        const banner = page.locator('#cora-pwa-update-banner');
        await expect(banner).toBeVisible();
        await expect(banner).toContainText('v4.0.1');
        await expect(banner).toContainText('Update Now & Sync Icon');
        
        const pill = page.locator('#cora-pwa-update-pill');
        await expect(pill).toBeVisible();
        await expect(pill).toContainText('v4.0.1');
        
        // Dismiss banner
        await page.evaluate(() => {
            window.coraDismissPwaUpdateBanner();
        });
        await expect(banner).toHaveClass(/translate-y-24/);
    });

    test('verify universal in-app update drawer sheet functionality and device detection', async ({ page }) => {
        await login(page);
        await page.goto('http://cora.local/workspace/dashboard');
        await page.waitForSelector('#cora-workspace');
        
        // Open drawer
        await page.evaluate(() => {
            window.coraOpenPwaUpdateDrawer('4.0.1');
        });
        
        const drawer = page.locator('#cora-pwa-update-drawer');
        await expect(drawer).toBeVisible();
        await expect(drawer).toContainText('v4.0.1');
        await expect(drawer).toContainText('Release Highlights');
        await expect(drawer).toContainText('Update Workspace & Sync Now');
        
        const guidanceCard = page.locator('#cora-device-guidance-card');
        await expect(guidanceCard).toBeVisible();
        
        // Close drawer
        await page.evaluate(() => {
            window.coraClosePwaUpdateDrawer();
        });
        await expect(drawer).toHaveClass(/translate-x-full/);
    });
});
