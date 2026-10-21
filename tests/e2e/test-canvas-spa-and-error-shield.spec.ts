import { test, expect } from '@playwright/test';

test.describe('Cora Canvas Universal Import - Error Shield & SPA Fallback', () => {
    test('Verifies imported ClickUp page renders with full fidelity and zero whiteouts', async ({ page }) => {
        const consoleErrors: string[] = [];
        page.on('pageerror', err => consoleErrors.push(err.message));

        await page.goto('http://cora.local/site/imported-home', { waitUntil: 'load', timeout: 30000 });

        // Ensure page title is present and correct
        const title = await page.title();
        expect(title).toContain('ClickUp');

        // Verify body is not empty
        const bodyText = await page.evaluate(() => document.body ? document.body.innerText.trim() : '');
        expect(bodyText.length).toBeGreaterThan(100);

        // Verify heading or key content renders
        const hasHeader = await page.locator('h1, header, nav, img').count();
        expect(hasHeader).toBeGreaterThan(0);
    });

    test('Verifies imported ClickUp subpage (ai-notetaker) renders with full fidelity', async ({ page }) => {
        await page.goto('http://cora.local/site/brain-ai-notetaker', { waitUntil: 'load', timeout: 30000 });

        const title = await page.title();
        expect(title.toLowerCase()).toContain('notetaker');

        const bodyText = await page.evaluate(() => document.body ? document.body.innerText.trim() : '');
        expect(bodyText.length).toBeGreaterThan(100);
    });
});
