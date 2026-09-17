import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.use({ 
  viewport: { width: 375, height: 812 },
  hasTouch: true,
  isMobile: true
});

test('switching to permissions matrix via mobile dropdown and testing scroll', async ({ page }) => {
  await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
  await page.goto('/workspace/team-roles?industry=photography_studio');
  await page.waitForLoadState('networkidle');

  // Click More button
  const moreBtn = page.locator('#mobile-tabs-more-btn');
  await expect(moreBtn).toBeVisible();
  await moreBtn.click();

  // Click Permissions Matrix option inside dropdown
  const permOption = page.locator('#mobile-tabs-more-dropdown button[data-target="tab-permissions-matrix"]');
  await expect(permOption).toBeVisible();
  await permOption.click();

  // Verify tab is visible
  const permTab = page.locator('#tab-permissions-matrix');
  await expect(permTab).toBeVisible();

  // Check scroll lock or height constraints
  const scrollCheck = await page.evaluate(() => {
    return {
      windowScrollY: window.scrollY,
      docHeight: document.documentElement.scrollHeight,
      winHeight: window.innerHeight,
      tabHeight: document.getElementById('tab-permissions-matrix').offsetHeight,
      bodyOverflow: window.getComputedStyle(document.body).overflow,
      htmlOverflow: window.getComputedStyle(document.documentElement).overflow
    };
  });
  console.log('SCROLL CHECK AFTER TAB SWITCH:', JSON.stringify(scrollCheck, null, 2));

  // Try scrolling down to the bottom of the page
  await page.evaluate(() => {
    window.scrollTo(0, document.body.scrollHeight);
  });

  const scrolledY = await page.evaluate(() => window.scrollY);
  console.log('SCROLLED Y:', scrolledY);
  expect(scrolledY).toBeGreaterThan(100);
});
