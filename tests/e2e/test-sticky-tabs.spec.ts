import { test, expect } from '@playwright/test';
import { login } from './helpers';
import * as path from 'path';

test('Test Content Suite sticky topbar desktop before and after scroll', async ({ page }) => {
  const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/58e87118-a119-4a20-9906-b30972fba7f6';

  await page.setViewportSize({ width: 1280, height: 850 });
  await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
  await page.goto('/workspace/blogs?ct=ct-library');
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(1000);

  // Switch to Content Library tab if not already active
  const libraryTab = page.locator('#btn-tab-articles-list');
  if (await libraryTab.isVisible()) {
    await libraryTab.click();
    await page.waitForTimeout(500);
  }

  const tabsBar = page.locator('#cora-content-tabs');
  await expect(tabsBar).toBeVisible();

  // Bounding rect before scroll
  const boxBefore = await tabsBar.boundingBox();
  console.log('Desktop tabs box before scroll:', boxBefore);

  await page.screenshot({
    path: path.join(artifactDir, 'before_scroll.png')
  });

  // Scroll main.cora-main
  await page.evaluate(() => {
    const mainEl = document.querySelector('main.cora-main');
    if (mainEl) {
      mainEl.scrollTop = 450;
      mainEl.dispatchEvent(new Event('scroll'));
    } else {
      window.scrollTo(0, 450);
    }
  });
  await page.waitForTimeout(600);

  const boxAfter = await tabsBar.boundingBox();
  console.log('Desktop tabs box after scroll 450px:', boxAfter);

  const topbar = page.locator('#cora-global-topbar');
  const topbarBox = await topbar.boundingBox();
  console.log('Desktop global topbar box:', topbarBox);

  // Expect tabs bar to be exactly flush with topbar bottom
  expect(boxAfter!.y).toBeCloseTo(topbarBox!.y + topbarBox!.height, 1);

  await page.screenshot({
    path: path.join(artifactDir, 'after_scroll.png')
  });
});

test('Test Content Suite sticky topbar mobile before and after scroll', async ({ page }) => {
  const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/58e87118-a119-4a20-9906-b30972fba7f6';

  await page.setViewportSize({ width: 390, height: 844 });
  await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
  await page.goto('/workspace/blogs?ct=ct-library');
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(1000);

  const tabsBar = page.locator('#cora-content-tabs');
  await expect(tabsBar).toBeVisible();

  // On mobile, window scrolls
  await page.evaluate(() => {
    window.scrollTo(0, 300);
  });
  await page.waitForTimeout(600);

  const boxAfter = await tabsBar.boundingBox();
  console.log('Mobile tabs box after scroll 300px:', boxAfter);

  const topbar = page.locator('#cora-global-topbar');
  const topbarBox = await topbar.boundingBox();
  console.log('Mobile global topbar box:', topbarBox);

  await page.screenshot({
    path: path.join(artifactDir, 'mobile_after_scroll.png')
  });
});
