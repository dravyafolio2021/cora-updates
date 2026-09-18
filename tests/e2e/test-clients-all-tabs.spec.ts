import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('verify all 4 tabs in Clients module are functional, filter pills work, and new client modal operates cleanly', async ({ page }) => {
    page.on('pageerror', exception => {
        console.log(`Uncaught exception: "${exception.message}"`);
    });

    // 1. Login & navigate to Clients Directory
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
    await page.goto('/studio/clients');
    await page.waitForLoadState('networkidle');

    // 2. Verify Tab 1: Clients Directory is active by default
    const directoryPanel = page.locator('#clients-tab-content-directory');
    await expect(directoryPanel).toBeVisible({ timeout: 10000 });
    await expect(page.locator('#metric-total-clients')).toBeVisible();
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/tab-1-clients-directory.png' });

    // 3. Test Filter Pills in Directory Tab
    const vipFilterBtn = page.locator('.clients-filter-btn[data-filter="vip"]');
    await vipFilterBtn.click();
    await page.waitForTimeout(300);

    const activeFilterBtn = page.locator('.clients-filter-btn[data-filter="active"]');
    await activeFilterBtn.click();
    await page.waitForTimeout(300);

    const allFilterBtn = page.locator('.clients-filter-btn[data-filter="all"]');
    await allFilterBtn.click();
    await page.waitForTimeout(300);
    await expect(page.locator('.client-table-row').first()).toBeVisible();

    // 4. Switch to Tab 2: Client Portals & Proofing
    const portalsTab = page.locator('#tab-clients-portals, [data-target="portals"]').first();
    await portalsTab.click();
    await page.waitForTimeout(400);

    const portalsPanel = page.locator('#clients-tab-content-portals');
    await expect(portalsPanel).toBeVisible();
    await expect(directoryPanel).not.toBeVisible();
    await expect(page.locator('text=256-Bit Signed').first()).toBeVisible();
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/tab-2-client-portals.png' });

    // 5. Switch to Tab 3: Invoices & Retainers
    const invoicesTab = page.locator('#tab-clients-invoices, [data-target="invoices"]').first();
    await invoicesTab.click();
    await page.waitForTimeout(400);

    const invoicesPanel = page.locator('#clients-tab-content-invoices');
    await expect(invoicesPanel).toBeVisible();
    await expect(portalsPanel).not.toBeVisible();
    await expect(page.locator('text=Total Contract Invoiced').first()).toBeVisible();
    await expect(page.locator('text=GST Output Tax').first()).toBeVisible();
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/tab-3-invoices-retainers.png' });

    // 6. Switch to Tab 4: Activity & SLA Health
    const healthTab = page.locator('#tab-clients-health, [data-target="health"]').first();
    await healthTab.click();
    await page.waitForTimeout(400);

    const healthPanel = page.locator('#clients-tab-content-health');
    await expect(healthPanel).toBeVisible();
    await expect(invoicesPanel).not.toBeVisible();
    await expect(page.locator('text=Turnaround SLA').first()).toBeVisible();
    await expect(page.locator('text=SLA Compliance Radar').first()).toBeVisible();
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/tab-4-activity-sla-health.png' });

    // 7. Switch back to Directory and test "+ Client" modal
    const directoryTab = page.locator('#tab-clients-directory, [data-target="directory"]').first();
    await directoryTab.click();
    await page.waitForTimeout(300);

    const addClientBtn = page.locator('#btn-add-client').first();
    await addClientBtn.click();
    await page.waitForTimeout(300);

    const newClientModal = page.locator('#cora-new-client-modal');
    await expect(newClientModal).toBeVisible();
    await expect(page.locator('#modal-client-name')).toBeVisible();
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/new-client-modal-open.png' });

    // Close modal
    await page.locator('#cora-new-client-modal button:has-text("Cancel")').click();
    await page.waitForTimeout(300);
    await expect(newClientModal).toHaveClass(/opacity-0/);
});
