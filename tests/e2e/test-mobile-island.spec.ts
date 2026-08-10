import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Mobile Floating Island Interactive States', () => {
  test('verify states and toggle clicks', async ({ page }) => {
    // Listen for page console errors
    page.on('console', msg => {
      console.log(`PAGE CONSOLE [${msg.type()}]: ${msg.text()}`);
    });
    page.on('pageerror', exception => {
      console.log(`PAGE UNHANDLED EXCEPTION: "${exception.message}"`);
    });

    // Set mobile resolution
    await page.setViewportSize({ width: 375, height: 667 });
    await login(page);
    
    // Go to blogs page
    await page.goto('/workspace/blogs?industry=real_estate');
    await page.waitForLoadState('networkidle');

    // 1. Locate the floating island
    const island = page.locator('#cora-mobile-floating-island');
    await expect(island).toBeVisible({ timeout: 10000 });

    // Print initial classes and display states
    const initialAIStyle = await page.locator('#cora-island-view-ai').getAttribute('style');
    const initialAIClass = await page.locator('#cora-island-view-ai').getAttribute('class');
    const initialNavStyle = await page.locator('#cora-island-view-nav').getAttribute('style');
    const initialNavClass = await page.locator('#cora-island-view-nav').getAttribute('class');
    console.log(`Initial AI View style: "${initialAIStyle}", class: "${initialAIClass}"`);
    console.log(`Initial Nav View style: "${initialNavStyle}", class: "${initialNavClass}"`);

    // Let's take screenshot of initial state (should be AI state)
    await page.screenshot({ path: 'tests/e2e/mobile-island-initial.png' });

    // 2. Click Left Menu toggle button to switch to 'nav' state
    const menuBtn = page.locator('#cora-island-state-menu-btn');
    console.log('Clicking menu button (switching to nav state)...');
    await menuBtn.click();
    await page.waitForTimeout(500);

    const postMenuAIStyle = await page.locator('#cora-island-view-ai').getAttribute('style');
    const postMenuAIClass = await page.locator('#cora-island-view-ai').getAttribute('class');
    const postMenuNavStyle = await page.locator('#cora-island-view-nav').getAttribute('style');
    const postMenuNavClass = await page.locator('#cora-island-view-nav').getAttribute('class');
    console.log(`Post-Menu AI View style: "${postMenuAIStyle}", class: "${postMenuAIClass}"`);
    console.log(`Post-Menu Nav View style: "${postMenuNavStyle}", class: "${postMenuNavClass}"`);
    
    await page.screenshot({ path: 'tests/e2e/mobile-island-nav-state.png' });

    // 3. Click Right AI toggle button to switch back to 'ai' state
    const aiBtn = page.locator('#cora-island-state-ai-btn');
    console.log('Clicking AI button (switching back to ai state)...');
    await aiBtn.click();
    await page.waitForTimeout(500);

    const finalAIStyle = await page.locator('#cora-island-view-ai').getAttribute('style');
    const finalAIClass = await page.locator('#cora-island-view-ai').getAttribute('class');
    const finalNavStyle = await page.locator('#cora-island-view-nav').getAttribute('style');
    const finalNavClass = await page.locator('#cora-island-view-nav').getAttribute('class');
    console.log(`Final AI View style: "${finalAIStyle}", class: "${finalAIClass}"`);
    console.log(`Final Nav View style: "${finalNavStyle}", class: "${finalNavClass}"`);

    await page.screenshot({ path: 'tests/e2e/mobile-island-final-ai-state.png' });
  });
});
