import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('verify customize columns drawer toggle switches state and animation', async ({ page }) => {
  await login(page);
  await page.goto('/workspace/leads');
  await page.waitForLoadState('networkidle');

  await page.evaluate(() => {
    if (typeof (window as any).coraNavigateTo === 'function') {
      (window as any).coraNavigateTo('leads');
    }
  });
  await page.waitForTimeout(500);

  // 1. Open Customize Columns drawer
  const customizeBtn = page.locator('#cora-top-header-customize-cols');
  await expect(customizeBtn).toBeVisible();
  await customizeBtn.click();

  const drawer = page.locator('#cora-lead-stages-drawer');
  await page.waitForTimeout(400);
  await expect(drawer).not.toHaveClass(/translate-x-full/);

  // 2. Locate first stage toggle checkbox and slider
  const firstCheckbox = page.locator('.cora-stage-enable-checkbox').first();
  const firstSlider = page.locator('.cora-toggle-slider').first();
  const firstToggleText = page.locator('.cora-toggle-text').first();

  await expect(firstCheckbox).toBeChecked();
  await expect(firstToggleText).toHaveText('Show');

  // Take screenshot of drawer with ON toggles
  await drawer.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/1a191bd8-ca8e-4f43-b8ea-289e35a89b5e/customize-columns-toggles-on.png' });

  // 3. Click the first toggle checkbox to toggle OFF
  await firstCheckbox.click({ force: true });
  await page.waitForTimeout(400);

  await expect(firstCheckbox).not.toBeChecked();
  await expect(firstToggleText).toHaveText('Hide');

  // Take screenshot of drawer with OFF toggle
  await drawer.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/1a191bd8-ca8e-4f43-b8ea-289e35a89b5e/customize-columns-toggles-off.png' });
});
