import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('verify sidebar layout top position is fixed', async ({ page }) => {
  await login(page);
  await page.goto('/workspace/forms');
  await page.waitForLoadState('networkidle');

  // Wait for sidebar to be visible
  await page.waitForSelector('.cora-sidebar');

  const rects = await page.evaluate(() => {
    const sidebar = document.querySelector('.cora-sidebar');
    const topbar = document.getElementById('cora-global-topbar');
    if (!sidebar || !topbar) return null;
    return {
      topbarBottom: topbar.getBoundingClientRect().bottom,
      sidebarTop: sidebar.getBoundingClientRect().top
    };
  });

  console.log('LAYOUT POSITION CHECK:', JSON.stringify(rects, null, 2));

  if (rects) {
    // Top of sidebar should align perfectly with bottom of topbar (with at most 1px error/overlap due to subpixel rendering)
    const diff = Math.abs(rects.sidebarTop - rects.topbarBottom);
    console.log(`Difference between sidebar top and topbar bottom: ${diff}px`);
    expect(diff).toBeLessThanOrEqual(1);
  }
});
