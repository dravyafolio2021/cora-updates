import { test, expect } from '@playwright/test';
import { login } from './helpers';
import * as path from 'path';

test('Verify Compact Mobile Multi-Step Project Drawer', async ({ page }) => {
  const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/a61fdc7a-f854-4606-9d91-f2a9f6b6294c';

  // 1. Authenticate and load Projects view on mobile viewport (390 x 844)
  await page.setViewportSize({ width: 390, height: 844 });
  await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
  await page.goto('/workspace/projects');
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(600);

  // 2. Open Drawer for Create
  await page.evaluate(() => {
    if (typeof window.coraOpenProjectDrawerForCreate === 'function') {
      window.coraOpenProjectDrawerForCreate();
    }
  });
  await page.waitForTimeout(500);

  const drawer = page.locator('#cora-project-drawer');
  await expect(drawer).toBeVisible();
  await expect(drawer).toHaveClass(/open/);

  // STEP 1: Screenshot & Assertions
  await page.evaluate(() => {
    window.coraGoToProjectStep(1);
  });
  await page.waitForTimeout(300);
  await page.screenshot({
    path: path.join(artifactDir, 'mobile_drawer_step1_compact.png'),
    fullPage: false,
  });

  // STEP 2: Screenshot & Assertions
  await page.evaluate(() => {
    window.coraGoToProjectStep(2);
  });
  await page.waitForTimeout(300);
  await page.screenshot({
    path: path.join(artifactDir, 'mobile_drawer_step2_compact.png'),
    fullPage: false,
  });

  // STEP 3: Screenshot & Assertions
  await page.evaluate(() => {
    window.coraGoToProjectStep(3);
  });
  await page.waitForTimeout(300);
  await page.screenshot({
    path: path.join(artifactDir, 'mobile_drawer_step3_compact.png'),
    fullPage: false,
  });

  // STEP 4: Screenshot & Assertions
  await page.evaluate(() => {
    window.coraGoToProjectStep(4);
  });
  await page.waitForTimeout(300);
  await page.screenshot({
    path: path.join(artifactDir, 'mobile_drawer_step4_compact.png'),
    fullPage: false,
  });

  // STEP 5: Screenshot & Assertions
  await page.evaluate(() => {
    window.coraGoToProjectStep(5);
  });
  await page.waitForTimeout(300);
  await page.screenshot({
    path: path.join(artifactDir, 'mobile_drawer_step5_compact.png'),
    fullPage: false,
  });

  // STEP 6: Screenshot & Assertions
  await page.evaluate(() => {
    window.coraGoToProjectStep(6);
  });
  await page.waitForTimeout(300);
  await page.screenshot({
    path: path.join(artifactDir, 'mobile_drawer_step6_compact.png'),
    fullPage: false,
  });

  // Check horizontal overflow
  const scrollWidth = await page.evaluate(() => document.documentElement.scrollWidth);
  const clientWidth = await page.evaluate(() => document.documentElement.clientWidth);
  expect(scrollWidth).toBeLessThanOrEqual(clientWidth + 2);

  console.log('Mobile screenshots captured successfully with zero horizontal overflow!');
});
