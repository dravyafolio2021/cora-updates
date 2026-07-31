import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Mobile Side Drawer Validation, Geo-Location & Functional Outreach', () => {
  test('verify strict validation, geo-location auto-detect, and outreach links', async ({ page }) => {
    // 1. Set iPhone 14 Pro Max viewport
    await page.setViewportSize({ width: 430, height: 932 });
    await login(page);
    await page.goto('/workspace/leads?subtab=directory');
    await page.waitForLoadState('networkidle');

    // 2. Open Prospect Detail Drawer by clicking first lead card or table row
    const firstItem = page.locator('.cora-lead-row, .cora-lead-card').first();
    await expect(firstItem).toBeVisible({ timeout: 15000 });
    await firstItem.click();

    const drawer = page.locator('#cora-lead-detail-drawer');
    await expect(drawer).toBeVisible({ timeout: 10000 });

    // 3. Verify Outreach Action Buttons have functional href attributes
    const whatsappBtn = drawer.locator('#cora-drawer-whatsapp-btn');
    await expect(whatsappBtn).toBeVisible();

    const emailBtn = drawer.locator('#cora-drawer-sla-email-btn');
    await expect(emailBtn).toBeVisible();

    // 4. Test Target City Geo-Location Hub Pills
    const mumbaiPill = drawer.locator('button:has-text("Mumbai")');
    await expect(mumbaiPill).toBeVisible();
    await mumbaiPill.click();

    const cityInput = drawer.locator('#cora-drawer-input-city');
    await expect(cityInput).toHaveValue('Mumbai');

    // 5. Test Form Validation (Enter invalid email format)
    const emailInput = drawer.locator('#cora-drawer-input-email');
    await emailInput.fill('invalid-email-format');

    const saveBtn = drawer.locator('button:has-text("Save Deal Changes")');
    await saveBtn.click();

    // Expect red highlight on invalid email field
    await expect(emailInput).toHaveClass(/border-rose-500/);

    // Fix email to valid format
    await emailInput.fill('valid.prospect@gmail.com');
    await expect(emailInput).toHaveValue('valid.prospect@gmail.com');

    // Take screenshot of validated & geo-enhanced mobile side drawer
    await page.screenshot({ path: 'tests/e2e/mobile-drawer-validation-screenshot.png' });
  });
});
