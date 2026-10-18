import { test, expect } from '@playwright/test';
import { login } from './helpers';
import * as path from 'path';

test('Verify TopBar is transparent normally and turns white with shadow when stuck', async ({ page }) => {
  const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/58e87118-a119-4a20-9906-b30972fba7f6';

  await page.setViewportSize({ width: 1280, height: 850 });
  await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

  // --- 1. Content Suite ---
  await page.goto('/workspace/blogs?ct=ct-library');
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(800);

  const contentTabs = page.locator('#cora-content-tabs');
  await expect(contentTabs).toBeVisible();

  // Inspect BEFORE scroll
  const beforeStyles = await contentTabs.evaluate((el) => {
    const s = window.getComputedStyle(el);
    return {
      backgroundColor: s.backgroundColor,
      boxShadow: s.boxShadow,
      isStuck: el.classList.contains('cora-tabs-stuck')
    };
  });
  console.log('Content Tabs Before Scroll:', beforeStyles);
  expect(beforeStyles.isStuck).toBe(false);
  expect(beforeStyles.backgroundColor).toBe('rgba(0, 0, 0, 0)');

  await page.screenshot({
    path: path.join(artifactDir, 'topbar_transparent_before_scroll.png')
  });

  // Scroll down 250px to stick the TopBar
  await page.evaluate(() => {
    const mainEl = document.querySelector('main.cora-main');
    if (mainEl) {
      mainEl.scrollTop = 250;
      mainEl.dispatchEvent(new Event('scroll'));
    }
    window.scrollTo(0, 250);
    window.dispatchEvent(new Event('scroll'));
  });
  await page.waitForTimeout(500);

  // Inspect AFTER scroll
  const afterStyles = await contentTabs.evaluate((el) => {
    const s = window.getComputedStyle(el);
    return {
      backgroundColor: s.backgroundColor,
      boxShadow: s.boxShadow,
      isStuck: el.classList.contains('cora-tabs-stuck')
    };
  });
  console.log('Content Tabs After Scroll:', afterStyles);
  expect(afterStyles.isStuck).toBe(true);
  expect(afterStyles.backgroundColor).toBe('rgba(255, 255, 255, 0.96)');
  expect(afterStyles.boxShadow).not.toBe('none');

  await page.screenshot({
    path: path.join(artifactDir, 'topbar_white_shadow_after_scroll.png')
  });

  // --- 2. Clients View (Centralized workspace-header engine) ---
  await page.goto('/workspace/clients');
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(800);

  const clientsTabs = page.locator('.cora-sticky-sub-tabs');
  await expect(clientsTabs).toBeVisible();

  const clientsBefore = await clientsTabs.evaluate((el) => {
    const s = window.getComputedStyle(el);
    return {
      backgroundColor: s.backgroundColor,
      isStuck: el.classList.contains('cora-tabs-stuck')
    };
  });
  console.log('Clients Tabs Before Scroll:', clientsBefore);
  expect(clientsBefore.isStuck).toBe(false);
  expect(clientsBefore.backgroundColor).toBe('rgba(0, 0, 0, 0)');

  // Ensure realistic page height for scroll testing on clients view
  await page.locator('#cora-page-clients').evaluate(el => {
    (el as HTMLElement).style.minHeight = '1400px';
  });

  // Scroll clients view
  await page.evaluate(() => {
    const mainEl = document.querySelector('main.cora-main');
    if (mainEl) {
      mainEl.scrollTop = 200;
      mainEl.dispatchEvent(new Event('scroll'));
    }
  });
  await page.waitForTimeout(500);

  const clientsAfter = await clientsTabs.evaluate((el) => {
    const s = window.getComputedStyle(el);
    return {
      backgroundColor: s.backgroundColor,
      boxShadow: s.boxShadow,
      isStuck: el.classList.contains('cora-tabs-stuck')
    };
  });
  console.log('Clients Tabs After Scroll:', clientsAfter);
  expect(clientsAfter.isStuck).toBe(true);
  expect(clientsAfter.backgroundColor).toBe('rgba(255, 255, 255, 0.96)');
  expect(clientsAfter.boxShadow).not.toBe('none');

  await page.screenshot({
    path: path.join(artifactDir, 'clients_topbar_after_scroll.png')
  });
});
