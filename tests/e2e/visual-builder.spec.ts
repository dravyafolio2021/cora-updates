import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Visual Page Builder E2E Tests', () => {
  let consoleErrors: string[] = [];

  test.beforeEach(async ({ page }) => {
    consoleErrors = [];
    page.on('console', msg => {
      if (msg.type() === 'error') {
        const text = msg.text();
        if (!text.includes('404') && !text.includes('unsplash') && !text.includes('favicon')) {
          consoleErrors.push(text);
        }
        console.log(`PAGE CONSOLE ERROR: "${text}"`);
      }
    });
    page.on('pageerror', err => {
      consoleErrors.push(err.message);
      console.log(`PAGE UNHANDLED EXCEPTION: "${err.message}"`);
    });
  });

  test('Should generate, configure, publish a visual builder page and verify responsively', async ({ page }) => {
    // 1. Login and go to visual builder workspace
    await login(page);
    await page.goto('/workspace/visual-builder');

    // Wait for GrapesJS editor container to initialize
    await page.waitForSelector('#gjs', { state: 'visible' });

    // 2. Submit AI generation layout prompt
    const promptInput = page.locator('#cora-builder-ai-prompt');
    await promptInput.fill('Luxury Beachfront Villa with Infinity Pool');
    await page.click('#cora-builder-generate-btn');

    // Wait for the fallback template (or generated template) to load inside the GrapesJS iframe
    const frame = page.frameLocator('iframe.gjs-frame');
    const heroTitle = frame.locator('h1');
    await heroTitle.first().waitFor({ state: 'visible', timeout: 15000 });

    // Verify it contains fallback mockup content
    await expect(frame.locator('body')).toContainText('Villa Serene');

    // 3. Open Settings Drawer and fill details
    await page.click('#cora-builder-settings-btn');
    await page.waitForSelector('#cora-builder-drawer:not(.translate-x-full)', { state: 'visible' });

    const rand = Math.floor(Math.random() * 100000);
    const pageTitle = `E2E Visual Villa ${rand}`;
    const pageSlug = `e2e-visual-villa-${rand}`;

    await page.fill('#cora-builder-title', pageTitle);
    await page.fill('#cora-builder-slug', pageSlug);
    await page.selectOption('#cora-builder-status', 'publish');

    // Close settings drawer
    await page.click('#cora-builder-drawer-close');
    await page.waitForSelector('#cora-builder-drawer-overlay', { state: 'hidden' });

    // 4. Click Publish and verify success toast
    await page.click('#cora-builder-publish-btn');
    
    // Wait for toast container and assert success
    const toast = page.locator('#cora-toast-container');
    await expect(toast).toContainText('Saved successfully!');

    // 5. Navigate to the published page's frontend URL
    await page.goto(`/${pageSlug}/`);

    // Wait for the content to render
    await page.waitForSelector('header', { state: 'visible' });

    // Verify page content rendered
    await expect(page.locator('body')).toContainText('Villa Serene');

    // 6. Set mobile viewport size to 375px width
    await page.setViewportSize({ width: 375, height: 667 });
    await page.waitForTimeout(1000); // Give layout time to adapt

    // Assert that it renders cleanly without horizontal scrolling
    const hasScrollbar = await page.evaluate(() => {
      return document.documentElement.scrollWidth > document.documentElement.clientWidth;
    });
    expect(hasScrollbar).toBe(false);

    // Verify no console errors occurred on frontend
    expect(consoleErrors.length).toBe(0);
  });
});
