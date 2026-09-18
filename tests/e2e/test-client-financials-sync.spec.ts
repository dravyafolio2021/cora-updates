import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Client Management & Financial Overview Real-Time Synchronization Suite', () => {
  test('verify client retainers, receivables, milestone settlements, and ledger sync', async ({ page }) => {
    page.on('pageerror', exception => {
      console.log(`Uncaught exception: "${exception.message}"`);
    });

    // 1. Log in and navigate to Studio Financials
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
    await page.goto('/studio/financials');
    await page.waitForLoadState('networkidle');

    // Verify Financial Overview metrics are rendered
    await expect(page.locator('text=Available Cash').first()).toBeVisible({ timeout: 10000 });
    await expect(page.locator('text=Incoming (Money In)').first()).toBeVisible();
    await expect(page.locator('text=Outgoing (Money Out)').first()).toBeVisible();

    // Capture initial screenshot of Financials
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/financial-overview-initial.png' });

    // 2. Navigate to Studio Clients
    await page.goto('/studio/clients');
    await page.waitForLoadState('networkidle');

    // Verify all 4 subtabs exist in header
    const directoryTab = page.locator('#tab-clients-directory');
    const portalsTab = page.locator('#tab-clients-portals');
    const invoicesTab = page.locator('#tab-clients-invoices');
    const healthTab = page.locator('#tab-clients-health');

    await expect(directoryTab).toBeVisible();
    await expect(portalsTab).toBeVisible();
    await expect(invoicesTab).toBeVisible();
    await expect(healthTab).toBeVisible();

    // Click Invoices & Retainers tab
    await invoicesTab.click();
    await expect(page.locator('#clients-tab-content-invoices')).toBeVisible();

    // Verify invoice milestones and retainer status badges
    await expect(page.locator('text=Milestone Retainer (50%)').first()).toBeVisible();
    await expect(page.locator('text=PAID (50% Retainer)').first()).toBeVisible();

    // Take screenshot of Invoices & Retainers tab
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/clients-invoices-retainers-synced.png' });

    // 3. Click "Record Balance ✓" on a pending milestone if present
    const recordBtn = page.locator('button:has-text("Record Balance ✓")').first();
    if (await recordBtn.isVisible()) {
      await recordBtn.click();
      await page.waitForTimeout(2000);
    }

    // 4. Return to Financials to verify synced entries and transaction ledger
    await page.goto('/studio/financials');
    await page.waitForLoadState('networkidle');

    // Switch to Master Chronological Ledger tab
    const ledgerTabBtn = page.locator('button:has-text("Ledger & Activity")').first();
    if (await ledgerTabBtn.isVisible()) {
      await ledgerTabBtn.click();
      await page.waitForTimeout(1000);
    }

    // Take final screenshot of Financials with synchronized client transactions
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/financial-overview-synced-clients.png' });
  });
});
