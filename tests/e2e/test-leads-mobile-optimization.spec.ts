import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Lead Management Mobile Screen Optimization', () => {
  test('verify mobile 2x2 KPI grid and prioritized CTA toolbar', async ({ page }) => {
    page.on('pageerror', exception => {
      console.log(`Uncaught exception: "${exception.message}"`);
    });

    // Set mobile resolution (iPhone SE viewport)
    await page.setViewportSize({ width: 375, height: 667 });
    await login(page);
    await page.goto('/workspace/leads');
    await page.waitForLoadState('networkidle');

    // 1. Verify mobile CTAs layout (Priority 1: Add Lead, Priority 2: Columns, Utility overflow menu)
    const mobileAddLeadBtn = page.locator('.flex.sm\\:hidden button:has-text("Add Lead")');
    await expect(mobileAddLeadBtn).toBeVisible({ timeout: 10000 });

    const mobileColumnsBtn = page.locator('.flex.sm\\:hidden button:has-text("Columns")');
    await expect(mobileColumnsBtn).toBeVisible();

    const mobileMoreBtn = page.locator('#cora-mobile-more-actions-btn');
    await expect(mobileMoreBtn).toBeVisible();

    // Test overflow menu click
    await mobileMoreBtn.click();
    const popover = page.locator('#cora-mobile-more-actions-popover');
    await expect(popover).toBeVisible();
    await expect(popover).toContainText('Activity Log');
    await expect(popover).toContainText('Export CSV');

    // Close popover
    await page.click('body', { position: { x: 10, y: 10 } });

    // 2. Verify 2x2 grid container for KPI cards on mobile
    const kpiGrid = page.locator('.grid.grid-cols-2.lg\\:grid-cols-4').first();
    await expect(kpiGrid).toBeVisible();

    // Take mobile screenshot
    await page.screenshot({ path: 'tests/e2e/mobile-leads-revamped.png' });
  });
});
