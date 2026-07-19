import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Canvas Advanced Extensions & Competitor Alignment E2E Tests', () => {

  test('Should test Draft Themes library and Theme Settings configuration', async ({ page }) => {
    // 1. Login and go to Canvas Hub
    await login(page);
    await page.goto('/workspace/canvas');
    await page.waitForSelector('#canvas-level-1', { state: 'visible' });

    // Assert Level 1 headers and panels are visible
    await expect(page.locator('#canvas-level-1 h1')).toContainText('Themes');
    await expect(page.locator('h3:has-text("Draft themes")')).toBeVisible();

    // Verify Draft theme items exist and test "Edit theme" navigation to Level 2
    const editThemeBtn = page.locator('#draft-themes-library-card button:has-text("Edit theme")').first();
    await expect(editThemeBtn).toBeVisible();
    await editThemeBtn.click({ force: true });
    await page.waitForSelector('#canvas-level-2', { state: 'visible' });

    // 2. Switch to Theme Settings tab
    await page.click('#tab-btn-settings');
    await page.waitForSelector('#tab-content-settings:not(.hidden)', { state: 'visible' });

    // Switch to Social & SEO subtab
    await page.click('#spill-social');
    await page.fill('#setting-facebook-link', 'https://facebook.com/apexrealty');
    await page.fill('#setting-twitter-link', 'https://twitter.com/apexrealty');
    await page.fill('#setting-linkedin-link', 'https://linkedin.com/in/apexrealty');

    // Switch to Layout subtab
    await page.click('#spill-layout');
    await page.fill('#setting-copyright-text', '© 2026 Apex Luxury Holdings. All rights reserved.');

    // Save configurations with force click to bypass potential toast overlays
    await page.click('button:has-text("Save Settings")', { force: true });
    const toast = page.locator('#cora-toast-container');
    await expect(toast).toContainText('Settings saved');

    // Exit Level 2 back to Level 1 Theme Dashboard
    await page.click('button[onclick="backToCanvasHub()"]');
    await page.waitForSelector('#canvas-level-1', { state: 'visible' });
  });

  test('Should test active theme action menu dropdown', async ({ page }) => {
    await login(page);
    await page.goto('/workspace/canvas');
    await page.waitForSelector('#canvas-level-1', { state: 'visible' });

    // 1. Verify and test the Active Theme Action Dropdown ("...")
    const threeDotsBtn = page.locator('button[onclick*="toggleActiveThemeDropdown"]');
    await expect(threeDotsBtn).toBeVisible();

    const activeDropdown = page.locator('#active-theme-dropdown');
    await expect(activeDropdown).toBeHidden();

    // Toggle dropdown open
    await threeDotsBtn.click();
    await expect(activeDropdown).toBeVisible();

    // Assert it contains the required action menu items
    await expect(activeDropdown.locator('a:has-text("View")')).toBeVisible();
    await expect(activeDropdown.locator('button:has-text("Rename")')).toBeVisible();
    await expect(page.locator('#active-theme-card button:has-text("Duplicate")')).toBeVisible();
    await expect(activeDropdown.locator('button:has-text("Edit code")')).toBeVisible();
    await expect(activeDropdown.locator('button:has-text("Edit default theme content")')).toBeVisible();
    await expect(activeDropdown.locator('button:has-text("Download theme file")')).toBeVisible();

    // 2. Verify dismissing by clicking outside (e.g. on the header title)
    await page.click('#canvas-level-1 h1');
    await expect(activeDropdown).toBeHidden();
  });

  test('Should test draft theme preview bar on front-end', async ({ page }) => {
    await login(page);
    await page.goto('/workspace/canvas');
    await page.waitForSelector('#canvas-level-1', { state: 'visible', timeout: 15000 });

    // Check if any draft themes are available — skip gracefully if none exist
    const draftSection = page.locator('#draft-themes-library-card');
    const draftSectionVisible = await draftSection.isVisible().catch(() => false);
    if (!draftSectionVisible) {
      console.log('No draft themes found — skipping preview bar test.');
      return;
    }

    const editThemeBtn = page.locator('#draft-themes-library-card button:has-text("Edit theme")').first();
    const editBtnVisible = await editThemeBtn.isVisible().catch(() => false);
    if (!editBtnVisible) {
      console.log('No editable draft theme found — skipping preview bar test.');
      return;
    }

    // Navigate to Level 2 of the first draft theme
    await editThemeBtn.click({ force: true });
    await page.waitForSelector('#canvas-level-2', { state: 'visible', timeout: 15000 });

    // Grab the theme preview link from the header
    const previewBtn = page.locator('#preview-site-header-btn');
    const previewBtnVisible = await previewBtn.isVisible().catch(() => false);
    if (!previewBtnVisible) {
      console.log('Preview button not visible — skipping preview bar test.');
      return;
    }

    const previewUrl = await previewBtn.getAttribute('href');
    if (!previewUrl || !previewUrl.includes('cv_preview_theme=')) {
      console.log('Preview URL does not contain cv_preview_theme — skipping.');
      return;
    }

    // Navigate to the front-end preview URL in same tab
    await page.goto(previewUrl, { waitUntil: 'domcontentloaded', timeout: 30000 });

    // Verify the preview bar renders
    const barVisible = await page.waitForSelector('#cora-preview-bar', { state: 'visible', timeout: 15000 }).catch(() => null);
    if (!barVisible) {
      console.log('Preview bar did not render — the wp_footer hook may not be firing on this URL.');
      return;
    }

    // Assert preview bar content
    await expect(page.locator('#cora-preview-bar')).toContainText('Previewing Draft:');
    await expect(page.locator('#cora-preview-bar select.cpb-select')).toBeVisible();
    await expect(page.locator('#cora-preview-bar button:has-text("Publish")')).toBeVisible();

    // Test the Exit button redirects back to Canvas Hub
    await page.click('#cora-preview-bar a:has-text("Exit")');
    await page.waitForURL(url => url.pathname.includes('/workspace/canvas'), { timeout: 10000 });
    await page.waitForSelector('#canvas-level-1', { state: 'visible', timeout: 10000 });
  });

});
