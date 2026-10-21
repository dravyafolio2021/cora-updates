import { test, expect } from '@playwright/test';
import { login } from './helpers';
import * as path from 'path';

test('Verify Forms sticky sub-tabs background turns white when scrolling (Light & Dark)', async ({ page }) => {
  const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/77ca00f3-771e-4ab4-9010-29866a4ae0bf';

  await page.setViewportSize({ width: 1280, height: 850 });
  await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

  // Navigate to Forms module
  await page.goto('/workspace/forms');
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(1000);

  const tabs = page.locator('#cora-forms-tabs');
  await expect(tabs).toBeVisible();

  // Initially at top: should NOT have cora-tabs-stuck
  const initialClass = await tabs.getAttribute('class');
  expect(initialClass).not.toContain('cora-tabs-stuck');

  // Scroll main.cora-main down
  await page.evaluate(() => {
    const mainEl = document.querySelector('main.cora-main');
    if (mainEl) {
      mainEl.scrollTop = 350;
      mainEl.dispatchEvent(new Event('scroll'));
    }
  });
  await page.waitForTimeout(400);

  // Now stuck: should have cora-tabs-stuck class
  const stuckClass = await tabs.getAttribute('class');
  expect(stuckClass).toContain('cora-tabs-stuck');

  // Check computed background color (Light mode)
  const bgColor = await tabs.evaluate((el) => {
    return window.getComputedStyle(el).backgroundColor;
  });
  console.log('Light mode stuck tabs computed backgroundColor:', bgColor);
  expect(bgColor).toMatch(/rgba?\(255,\s*255,\s*255/);

  // Take screenshot of desktop sticky state
  const screenshotPath = path.join(artifactDir, 'forms-sticky-header-desktop.png');
  await page.screenshot({ path: screenshotPath, fullPage: false });

  // Test Dark Mode
  await page.evaluate(() => {
    document.documentElement.classList.add('dark');
  });
  await page.waitForTimeout(200);

  const darkBgColor = await tabs.evaluate((el) => {
    return window.getComputedStyle(el).backgroundColor;
  });
  console.log('Dark mode stuck tabs computed backgroundColor:', darkBgColor);
  expect(darkBgColor).toMatch(/rgba?\(12,\s*12,\s*14/);

  const darkScreenshotPath = path.join(artifactDir, 'forms-sticky-header-dark.png');
  await page.screenshot({ path: darkScreenshotPath, fullPage: false });

  // Test mobile viewport
  await page.evaluate(() => {
    document.documentElement.classList.remove('dark');
  });
  await page.setViewportSize({ width: 390, height: 844 });
  await page.reload();
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(1000);

  const mobileTabs = page.locator('#cora-forms-tabs');
  await expect(mobileTabs).toBeVisible();

  await page.evaluate(() => {
    window.scrollTo(0, 300);
    window.dispatchEvent(new Event('scroll'));
  });
  await page.waitForTimeout(400);

  const mobileStuckClass = await mobileTabs.getAttribute('class');
  expect(mobileStuckClass).toContain('cora-tabs-stuck');

  const mobileBgColor = await mobileTabs.evaluate((el) => {
    return window.getComputedStyle(el).backgroundColor;
  });
  console.log('Mobile stuck tabs computed backgroundColor:', mobileBgColor);
  expect(mobileBgColor).toMatch(/rgba?\(255,\s*255,\s*255/);

  const mobileScreenshotPath = path.join(artifactDir, 'forms-sticky-header-mobile.png');
  await page.screenshot({ path: mobileScreenshotPath, fullPage: false });
});
