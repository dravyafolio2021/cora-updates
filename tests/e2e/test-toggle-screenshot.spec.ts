import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('capture automation tab toggle switches screenshot', async ({ page }) => {
  await login(page);
  await page.goto('/workspace/leads');
  await page.waitForLoadState('networkidle');

  await page.evaluate(() => {
    if (typeof (window as any).coraNavigateTo === 'function') {
      (window as any).coraNavigateTo('leads');
    }
  });
  await page.waitForTimeout(500);

  // Click on the first lead card to open the drawer
  const reviewBtn = page.locator('.cora-lead-card button:has-text("Review")').first();
  await reviewBtn.click();

  // Wait for drawer
  const drawer = page.locator('#cora-lead-detail-drawer');
  await expect(drawer).toBeVisible();

  // Click Automation tab
  const autoTab = page.locator('.cora-tab-btn[data-tab="automation"]');
  await autoTab.click();

  // Wait for automation tab content
  await page.waitForTimeout(500);

  // Take screenshot of drawer
  await drawer.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/1a191bd8-ca8e-4f43-b8ea-289e35a89b5e/prospect-drawer-automation-toggles.png' });
});
