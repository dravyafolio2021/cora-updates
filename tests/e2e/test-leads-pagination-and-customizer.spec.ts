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

    // 1. Ensure Directory subtab is active
    const dirTabBtn = page.locator('.cora-lead-subtab-btn[data-tab="directory"]');
    await expect(dirTabBtn).toBeVisible({ timeout: 10000 });
    await dirTabBtn.click();

    // Verify Pagination Bar elements in Leads Directory
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

    // 3. Verify stage config rows and color swatches
    const stageRows = page.locator('.cora-stage-config-row');
    await expect(stageRows.first()).toBeVisible();

    const colorSwatch = page.locator('.cora-stage-config-row .cora-stage-color-swatch');
    await expect(colorSwatch.first()).toBeVisible();

    // Test color swatch click
    await colorSwatch.first().click();

    // Take screenshot of mobile Column Customizer drawer
    await page.screenshot({ path: 'tests/e2e/mobile-leads-pagination-customizer.png' });
  });
});
