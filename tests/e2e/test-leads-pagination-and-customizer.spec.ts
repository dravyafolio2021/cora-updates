import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Leads Directory Pagination & Simplified Mobile Column Customizer', () => {
  test('verify server-side pagination controls and simplified stage customizer drawer', async ({ page }) => {
    page.on('pageerror', exception => {
      console.log(`Uncaught exception: "${exception.message}"`);
    });

    // Set mobile viewport (iPhone SE resolution)
    await page.setViewportSize({ width: 375, height: 667 });
    await login(page);
    await page.goto('/workspace/leads');
    await page.waitForLoadState('networkidle');

    // 1. Verify Pagination Bar elements in Leads Directory
    const paginationBar = page.locator('#cora-directory-pagination');
    await expect(paginationBar).toBeVisible();

    const paginationInfo = page.locator('#cora-pagination-info');
    await expect(paginationInfo).toContainText('Showing');

    const perPageSelect = page.locator('#cora-pagination-per-page');
    await expect(perPageSelect).toBeVisible();

    const prevBtn = page.locator('#cora-pagination-prev');
    await expect(prevBtn).toBeVisible();

    const nextBtn = page.locator('#cora-pagination-next');
    await expect(nextBtn).toBeVisible();

    // 2. Open Column Customizer Drawer
    const columnsBtn = page.locator('.flex.sm\\:hidden button:has-text("Columns")');
    await expect(columnsBtn).toBeVisible();
    await columnsBtn.click();

    const customizerDrawer = page.locator('#cora-lead-stages-drawer');
    await expect(customizerDrawer).toBeVisible({ timeout: 5000 });
    await expect(customizerDrawer).toContainText('Customize Pipeline Columns');

    // 3. Verify simplified stage config rows (single color circle button per row)
    const stageRows = page.locator('.cora-stage-config-row');
    await expect(stageRows.first()).toBeVisible();

    const colorCircleBtn = page.locator('.cora-stage-config-row button[title="Tap to change color theme"]');
    await expect(colorCircleBtn.first()).toBeVisible();

    // Test color circle cycling
    await colorCircleBtn.first().click();

    // Take screenshot of mobile Column Customizer drawer
    await page.screenshot({ path: 'tests/e2e/mobile-leads-pagination-customizer.png' });
  });
});
