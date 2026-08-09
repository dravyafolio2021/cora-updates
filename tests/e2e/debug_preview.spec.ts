import { test, expect } from '@playwright/test';

test('Debug preview page errors and assets', async ({ page }) => {
  page.on('console', msg => {
    console.log(`[CONSOLE ${msg.type().toUpperCase()}] ${msg.text()}`);
  });
  page.on('pageerror', err => {
    console.log(`[PAGE ERROR] ${err.message}`);
  });

  page.on('requestfailed', req => {
    console.log(`[REQ FAILED] ${req.url()} - ${req.failure()?.errorText}`);
  });

  page.on('response', res => {
    if (res.url().includes('assets/') || res.url().includes('cv_preview_theme')) {
      console.log(`[RESP ${res.status()}] ${res.url()} (${res.headers()['content-type']})`);
    }
  });

  console.log('Navigating to preview URL...');
  await page.goto('http://cora.local/?cv_preview_theme=195', { waitUntil: 'networkidle' });

  const triggerBtn = page.locator('#cpb-dropdown-trigger-btn');
  console.log('Is trigger visible?', await triggerBtn.isVisible());
  if (await triggerBtn.isVisible()) {
    console.log('Trigger text:', await triggerBtn.innerText());
    await triggerBtn.click();
    await page.waitForTimeout(500);
    const menuList = page.locator('#cpb-dropdown-menu-list');
    console.log('Menu list visible?', await menuList.isVisible());
    console.log('Menu list items:', await menuList.locator('li').allInnerTexts());
  }

  // Also test navigation to internal page via query param (wp_post_id 3327 = Contact)
  console.log('Navigating to page_id=3327 (Contact)...');
  await page.goto('http://cora.local/?page_id=3327&cv_preview_theme=195', { waitUntil: 'networkidle' });
  console.log('Contact page URL:', page.url());
  console.log('Contact page title:', await page.title());
});
