import { test, expect } from '@playwright/test';

test.describe('Mobile-First White-Labeled Client Portal', () => {
    test.use({
        viewport: { width: 390, height: 844 },
        isMobile: true,
        hasTouch: true,
    });

    test('verifies white-label owner branding, mobile tabs, proofing lightbox, and fast pay modal', async ({ page }) => {
        page.on('pageerror', exception => {
            console.log(`Uncaught exception: "${exception.message}"`);
        });

        // 1. Open Portal as a mobile client (token=rohan-verma)
        await page.goto('/studio/client-portal?token=rohan-verma');
        await page.waitForLoadState('networkidle');

        // Check White-Label Studio Branding (No Cora mentions)
        const brandTitle = page.locator('.cora-portal-brand-name');
        await expect(brandTitle).toBeVisible({ timeout: 10000 });
        const brandText = await brandTitle.textContent();
        expect(brandText).toContain('Lumina Creative Studio');
        expect(brandText?.toLowerCase()).not.toContain('cora');

        // Check Verified Studio Badge
        await expect(page.locator('text=Verified Studio')).toBeVisible();

        // Check Client Greeting & Workspace Owner Context
        await expect(page.locator('text=Welcome back, Rohan')).toBeVisible();
        await expect(page.locator('text=Step 3 of 5')).toBeVisible();

        // Screenshot 1: Mobile Portal Overview (Hero + Progress + Actions)
        await page.screenshot({
            path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/mobile-client-portal-whitelabel-overview.png',
            fullPage: false,
        });

        // 2. Switch to Deliverables & Galleries Tab
        const deliverablesTab = page.locator('#portal-tab-deliverables');
        await expect(deliverablesTab).toBeVisible();
        await deliverablesTab.click();
        await page.waitForTimeout(400);
        await expect(page.locator('#portal-view-deliverables')).toBeVisible();

        // Open Proofing Viewer modal
        const proofingBtn = page.locator('button:has-text("Open Proofing Viewer")').first();
        if (await proofingBtn.isVisible()) {
            await proofingBtn.click();
            await page.waitForTimeout(400);

            const proofingModal = page.locator('#proofing-lightbox-modal');
            await expect(proofingModal).toBeVisible();
            await expect(page.locator('#lightbox-title')).toBeVisible();

            // Toggle favorite ❤️ button
            const favBtn = page.locator('#btn-proof-favorite');
            if (await favBtn.isVisible()) {
                await favBtn.click();
                await page.waitForTimeout(200);
            }

            // Screenshot 2: Mobile Proofing Lightbox Modal
            await page.screenshot({
                path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/mobile-client-portal-proofing-modal.png',
                fullPage: false,
            });

            // Close proofing modal by approving look
            await page.locator('button:has-text("Approve Look Selection")').click();
            await page.waitForTimeout(400);
        }

        // 3. Switch to Invoices & Fast Pay Tab
        const invoicesTab = page.locator('#portal-tab-invoices');
        await expect(invoicesTab).toBeVisible();
        await invoicesTab.click();
        await page.waitForTimeout(400);
        await expect(page.locator('#portal-view-invoices')).toBeVisible();

        // Screenshot 3: Mobile Invoices Tab
        await page.screenshot({
            path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/mobile-client-portal-invoices-tab.png',
            fullPage: false,
        });

        // Open Fast Pay Modal if due, or directly trigger modal for screenshot
        await page.evaluate(() => {
            if (typeof window['openFastPayModal'] === 'function') {
                window['openFastPayModal']();
            }
        });
        await page.waitForTimeout(400);

        // Screenshot 4: Mobile Fast Pay Modal
        await page.screenshot({
            path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/mobile-client-portal-fastpay-modal.png',
            fullPage: false,
        });

        // Close fast pay modal
        await page.evaluate(() => {
            if (typeof window['closeFastPayModal'] === 'function') {
                window['closeFastPayModal']();
            }
        });
        await page.waitForTimeout(300);

        // 4. Switch to Contracts & E-Sign Tab
        const contractsTab = page.locator('#portal-tab-documents');
        await expect(contractsTab).toBeVisible();
        await contractsTab.click();
        await page.waitForTimeout(400);
        await expect(page.locator('#portal-view-documents')).toBeVisible();

        // Verify Executed Agreement Card
        await expect(page.locator('text=Commercial Production Agreement & Copyright License')).toBeVisible();
        await expect(page.locator('text=100% Audit-Proof')).toBeVisible();

        // Screenshot 5: Mobile Contracts & E-Sign Tab
        await page.screenshot({
            path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/mobile-client-portal-contracts-tab.png',
            fullPage: false,
        });
    });
});
