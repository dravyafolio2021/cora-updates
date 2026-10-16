import { test, expect } from '@playwright/test';
import { login } from './helpers';
import * as path from 'path';

test('Verify Accordions are Closed by Default in Project Drawer across Desktop and Mobile', async ({ page }) => {
  const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/a61fdc7a-f854-4606-9d91-f2a9f6b6294c';

  // 1. DESKTOP VERIFICATION (1280 x 850)
  await page.setViewportSize({ width: 1280, height: 850 });
  await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
  await page.goto('/workspace/projects');
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(600);

  // Open Edit drawer for first project and go to Step 2
  await page.evaluate(() => {
    var p = window.coraProjectsData && window.coraProjectsData[0];
    if (p && typeof window.coraEditProject === 'function') {
      window.coraEditProject(p.id, 2);
    } else if (typeof window.coraOpenProjectDrawerForCreate === 'function') {
      window.coraOpenProjectDrawerForCreate();
      window.coraGoToProjectStep(2);
    }
  });
  await page.waitForTimeout(500);

  const drawer = page.locator('#cora-project-drawer');
  await expect(drawer).toBeVisible();
  await expect(drawer).toHaveClass(/open/);

  // Ensure Step 2 is active
  await page.evaluate(() => {
    window.coraGoToProjectStep(2);
  });
  await page.waitForTimeout(400);

  // Assert all 4 story accordions do NOT have the .open class
  const act1 = page.locator('#story-accordion-act-1');
  const act2 = page.locator('#story-accordion-act-2');
  const act3 = page.locator('#story-accordion-act-3');
  const act4 = page.locator('#story-accordion-act-4');

  await expect(act1).not.toHaveClass(/open/);
  await expect(act2).not.toHaveClass(/open/);
  await expect(act3).not.toHaveClass(/open/);
  await expect(act4).not.toHaveClass(/open/);

  // Capture desktop screenshot with closed accordions
  await page.screenshot({
    path: path.join(artifactDir, 'desktop_drawer_step2_closed_accordions.png'),
    fullPage: false,
  });

  // Toggle Act 1 open and verify only Act 1 is open
  await act1.locator('.cora-accordion-trigger').click();
  await page.waitForTimeout(250);
  await expect(act1).toHaveClass(/open/);
  await expect(act2).not.toHaveClass(/open/);
  await expect(act3).not.toHaveClass(/open/);
  await expect(act4).not.toHaveClass(/open/);

  await page.screenshot({
    path: path.join(artifactDir, 'desktop_drawer_step2_act1_open.png'),
    fullPage: false,
  });

  // Toggle Act 1 closed again
  await act1.locator('.cora-accordion-trigger').click();
  await page.waitForTimeout(250);
  await expect(act1).not.toHaveClass(/open/);

  // Test Expand all button
  await page.evaluate(() => {
    window.coraSetAllStoryAccordions(true);
  });
  await page.waitForTimeout(200);
  await expect(act1).toHaveClass(/open/);
  await expect(act2).toHaveClass(/open/);
  await expect(act3).toHaveClass(/open/);
  await expect(act4).toHaveClass(/open/);

  // Test Collapse all button
  await page.evaluate(() => {
    window.coraSetAllStoryAccordions(false);
  });
  await page.waitForTimeout(200);
  await expect(act1).not.toHaveClass(/open/);
  await expect(act2).not.toHaveClass(/open/);
  await expect(act3).not.toHaveClass(/open/);
  await expect(act4).not.toHaveClass(/open/);

  // 2. MOBILE VERIFICATION (390 x 844)
  await page.setViewportSize({ width: 390, height: 844 });
  await page.waitForTimeout(400);

  // Verify in mobile view that accordions remain closed by default
  await expect(act1).not.toHaveClass(/open/);
  await expect(act2).not.toHaveClass(/open/);
  await expect(act3).not.toHaveClass(/open/);
  await expect(act4).not.toHaveClass(/open/);

  // Capture mobile screenshot
  await page.screenshot({
    path: path.join(artifactDir, 'mobile_drawer_step2_closed_accordions.png'),
    fullPage: false,
  });

  // Also verify Create mode on mobile
  await page.evaluate(() => {
    if (typeof window.coraOpenProjectDrawerForCreate === 'function') {
      window.coraOpenProjectDrawerForCreate();
      window.coraGoToProjectStep(2);
    }
  });
  await page.waitForTimeout(300);

  await expect(act1).not.toHaveClass(/open/);
  await expect(act2).not.toHaveClass(/open/);
  await expect(act3).not.toHaveClass(/open/);
  await expect(act4).not.toHaveClass(/open/);

  console.log('Successfully verified all accordions closed by default on desktop and mobile!');
});
