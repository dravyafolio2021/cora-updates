import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Desktop & Mobile Responsive Lead Directory Card Grid & View Mode Switcher', () => {
  test('verify desktop card grid layout and view switcher toggle', async ({ page }) => {
    // 1. Set Desktop Viewport
    await page.setViewportSize({ width: 1280, height: 900 });
    await login(page);
    await page.goto('/workspace/leads?subtab=directory');
    await page.waitForLoadState('networkidle');

    // 2. Verify Card Grid Container is visible on desktop by default
    const gridContainer = page.locator('#cora-directory-grid-container');
    await expect(gridContainer).toBeVisible({ timeout: 15000 });

    // 3. Verify Lead Cards are visible inside the grid
    const leadCards = page.locator('#cora-directory-grid-container .cora-lead-card');
    await expect(leadCards.first()).toBeVisible();

    // 4. Test View Mode Switcher: Switch to Table List View
    const tableBtn = page.locator('#cora-dir-view-btn-table');
    await expect(tableBtn).toBeVisible();
    await tableBtn.click();

    const tableContainer = page.locator('#cora-directory-table-container');
    await expect(tableContainer).toBeVisible();
    await expect(gridContainer).toBeHidden();

    // 5. Switch back to Card Grid View
    const gridBtn = page.locator('#cora-dir-view-btn-grid');
    await gridBtn.click();
    await expect(gridContainer).toBeVisible();
    await expect(tableContainer).toBeHidden();

    // 6. Click a card to open Prospect Detail Drawer
    await leadCards.first().click();

    const drawer = page.locator('#cora-lead-detail-drawer');
    await expect(drawer).toBeVisible({ timeout: 10000 });

    // Take screenshot of Desktop Card Grid & View Mode Switcher
    await page.screenshot({ path: 'tests/e2e/desktop-card-grid-screenshot.png' });
  });
});
