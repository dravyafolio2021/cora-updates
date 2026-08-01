import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('verify drawer sheets open and close cleanly, and activity log pane opens dynamically', async ({ page }) => {
  page.on('console', msg => console.log('BROWSER LOG:', msg.text()));
  page.on('pageerror', err => console.error('BROWSER ERROR:', err.stack || err.message));

  await login(page);

  await page.goto('/workspace/leads');
  await page.waitForLoadState('networkidle');

  // 1. Test Activity Log tab switching
  const activityBtn = page.locator('#cora-top-header-activity-btn');
  await expect(activityBtn).toBeVisible();
  await activityBtn.click();
  await page.waitForTimeout(300);

  // Assert activity pane is visible
  const activityPane = page.locator('#cora-lead-pane-activity');
  await expect(activityPane).toBeVisible();
  await expect(activityPane).toContainText('7-Day Auto-Purge Schedule');

  // Take screenshot of Activity Log pane
  await page.screenshot({ path: 'tests/e2e/activity-log-pane.png', fullPage: false });

  // 2. Test opening Customize Columns drawer sheet
  const customizeBtn = page.locator('button:has-text("Customize Columns")');
  await expect(customizeBtn).toBeVisible();
  await customizeBtn.click();
  await page.waitForTimeout(400);

  const stagesDrawer = page.locator('#cora-lead-stages-drawer');
  await expect(stagesDrawer).toBeVisible();
  await expect(stagesDrawer).not.toHaveClass(/translate-x-full/);

  // Take screenshot of open drawer sheet
  await page.screenshot({ path: 'tests/e2e/drawer-open.png', fullPage: false });

  // 3. Test closing drawer sheet via Esc key or close button
  await page.keyboard.press('Escape');
  await page.waitForTimeout(400);

  // Assert drawer sheet is closed and has translate-x-full
  await expect(stagesDrawer).toHaveClass(/translate-x-full/);

  // Take screenshot of closed drawer sheet
  await page.screenshot({ path: 'tests/e2e/drawer-closed.png', fullPage: false });
});
