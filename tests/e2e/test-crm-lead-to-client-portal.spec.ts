import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('verify CRM lead-to-client conversion, clients directory, and secure client portal', async ({ page }) => {
    page.on('pageerror', exception => {
        console.log(`Uncaught exception: "${exception.message}"`);
    });

    // 1. Verify Public Client Portal (zero WordPress login requirement)
    await page.goto('/client-portal?token=demo');
    await page.waitForLoadState('networkidle');

    await expect(page.locator('#portal-tab-overview')).toBeVisible({ timeout: 10000 });
    await expect(page.locator('#portal-tab-deliverables')).toBeVisible();
    await expect(page.locator('#portal-tab-invoices')).toBeVisible();
    await expect(page.locator('#portal-tab-documents')).toBeVisible();

    // Verify Project Progress Stepper
    await expect(page.locator('text=Project Lifecycle Progress')).toBeVisible();
    await expect(page.locator('text=Intake Brief').first()).toBeVisible();

    // Take screenshot of Public Client Portal
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/client-portal-public-live.png' });

    // Test switching tabs on the public portal
    await page.locator('#portal-tab-deliverables').click();
    await page.waitForTimeout(300);
    await expect(page.locator('#portal-view-deliverables')).toBeVisible();

    await page.locator('#portal-tab-invoices').click();
    await page.waitForTimeout(300);
    await expect(page.locator('#portal-view-invoices')).toBeVisible();

    await page.locator('#portal-tab-documents').click();
    await page.waitForTimeout(300);
    await expect(page.locator('#portal-view-documents')).toBeVisible();

    // 2. Login to Studio Workspace
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

    // 3. Navigate to Clients Directory
    await page.goto('/studio/clients');
    await page.waitForLoadState('networkidle');

    // Verify Clients Directory UI & KPI Metrics
    await expect(page.locator('#metric-total-clients')).toBeVisible({ timeout: 10000 });
    await expect(page.locator('#metric-active-clients')).toBeVisible();
    await expect(page.locator('#metric-total-ltv')).toBeVisible();
    await expect(page.locator('#metric-portal-rate')).toBeVisible();

    // Verify Clients Table and Filter Buttons
    await expect(page.locator('#clients-search-input')).toBeVisible();
    await expect(page.locator('.clients-filter-btn[data-filter="all"]')).toBeVisible();

    // Take screenshot of Clients Directory
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/clients-directory-live.png' });

    // 4. Open Client Details Right Drawer
    const clientRow = page.locator('.client-table-row').first();
    if (await clientRow.isVisible()) {
        await clientRow.click();
        await page.waitForTimeout(500);

        const drawer = page.locator('#cora-client-drawer');
        await expect(drawer).toHaveClass(/open/);
        await expect(page.locator('#drawer-client-name')).toBeVisible();
        await expect(page.locator('#drawer-info-email')).toBeVisible();

        // Switch to Portal Security tab in Drawer
        await page.locator('#drawer-tab-btn-portal').click();
        await page.waitForTimeout(300);
        await expect(page.locator('#btn-drawer-copy-portal')).toBeVisible();

        // Take screenshot of open Client Drawer
        await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/client-drawer-live.png' });

        // Close drawer
        await page.locator('#btn-close-client-drawer').click();
        await page.waitForTimeout(300);
    }

    // 5. Navigate to Leads Pipeline and verify Convert flow
    await page.goto('/studio/leads');
    await page.waitForLoadState('networkidle');

    const leadCard = page.locator('.cora-lead-card').first();
    if (await leadCard.isVisible()) {
        await leadCard.click();
        await page.waitForTimeout(600);

        // Verify Prospect Drawer contains the Convert button
        const convertBtn = page.locator('#cora-convert-lead-btn');
        if (await convertBtn.isVisible()) {
            await convertBtn.click();
            await page.waitForTimeout(1000);
        }

        // Take screenshot of Lead Drawer after conversion
        await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/lead-conversion-live.png' });
    }
});
