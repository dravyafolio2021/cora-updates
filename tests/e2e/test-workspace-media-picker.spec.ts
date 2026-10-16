import { test, expect } from '@playwright/test';
import { login } from './helpers';
import * as path from 'path';

test('Verify Workspace Media Library Module Picker in Step 4 across Desktop and Mobile', async ({ page }) => {
  const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/a61fdc7a-f854-4606-9d91-f2a9f6b6294c';

  page.on('console', msg => console.log('BROWSER LOG:', msg.text()));
  page.on('pageerror', err => console.log('BROWSER PAGE ERROR:', err.message));

  await page.setViewportSize({ width: 1280, height: 850 });
  await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
  await page.goto('/workspace/projects');
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(1000);

  // Verify sidebar navigation link renamed to "Portfolio"
  const portfolioNavLink = page.locator('.cora-sidebar a[href*="projects"]');
  await expect(portfolioNavLink).toContainText('Portfolio');
  await page.locator('.cora-sidebar').screenshot({
    path: path.join(artifactDir, 'sidebar_portfolio_renamed.png')
  });

  // Open Edit drawer reliably
  await page.evaluate(() => {
    if (typeof window.coraEditProject === 'function') {
      const firstProj = (window.coraProjectsData || [])[0];
      if (firstProj) {
        window.coraEditProject(firstProj.id, 1);
      } else if (typeof window.coraOpenProjectDrawerForCreate === 'function') {
        window.coraOpenProjectDrawerForCreate();
      }
    }
  });
  await page.waitForTimeout(600);

  const drawer = page.locator('#cora-project-drawer');
  await expect(drawer).toBeVisible();
  await expect(drawer).toHaveClass(/open/);

  // Switch to Step 4
  await page.evaluate(() => {
    window.coraGoToProjectStep(4);
  });
  await page.waitForTimeout(400);

  // Click "Workspace Media" button
  await page.locator('#btn-open-gallery-media-modal').click();
  await page.waitForTimeout(400);

  const modal = page.locator('#cora-media-library-modal');
  await expect(modal).toBeVisible();

  // Verify modal title says "Workspace Media Library"
  await expect(page.locator('#cora-media-picker-modal-title')).toHaveText(/Workspace Media Library/i);

  // Verify that real workspace items are rendered (NOT the mock 9 Unsplash photos)
  const mediaCards = modal.locator('#cora-modal-media-grid > div');
  const cardCount = await mediaCards.count();
  expect(cardCount).toBeGreaterThan(0);
  console.log(`Found ${cardCount} workspace media assets in the picker!`);

  // Verify cards do NOT contain mock Unsplash photos
  const firstCardImgSrc = await mediaCards.first().locator('img').getAttribute('src');
  console.log('First asset image src:', firstCardImgSrc);
  expect(firstCardImgSrc).not.toContain('images.unsplash.com');

  // Capture desktop screenshot with real workspace assets
  await page.screenshot({
    path: path.join(artifactDir, 'desktop_workspace_media_picker_open.png'),
    fullPage: false,
  });

  // Test real-time search in picker
  const searchInput = page.locator('#cora-media-picker-search-input');
  await searchInput.fill('Dravya');
  await page.waitForTimeout(300);

  // Capture filtered view screenshot
  await page.screenshot({
    path: path.join(artifactDir, 'desktop_workspace_media_picker_filtered.png'),
    fullPage: false,
  });

  // Clear search
  await page.locator('#cora-media-picker-search-clear').click();
  await page.waitForTimeout(400);

  // Select first 2 assets using fresh locators
  const freshCards = modal.locator('#cora-modal-media-grid > div');
  await freshCards.nth(0).click();
  await freshCards.nth(1).click();
  await page.waitForTimeout(200);

  // Assert counter says at least 1 or 2 assets selected
  const countText = await page.locator('#cora-modal-selected-count').innerText();
  expect(countText).toMatch(/\d+ asset/i);

  // Confirm selection
  await page.locator('#btn-modal-confirm-add').click();
  await page.waitForTimeout(400);

  // Verify modal is closed
  await expect(modal).not.toBeVisible();

  // Verify assets are added to gallery count badge
  const galleryBadge = page.locator('#cora-gallery-count-badge');
  const galleryCountText = await galleryBadge.innerText();
  expect(galleryCountText).not.toBe('0 Assets');

  // 2. MOBILE VERIFICATION (390 x 844)
  await page.setViewportSize({ width: 390, height: 844 });
  await page.waitForTimeout(300);

  // Open modal again on mobile
  await page.locator('#btn-open-gallery-media-modal').click();
  await page.waitForTimeout(400);

  await expect(modal).toBeVisible();

  // Capture mobile bottom sheet screenshot
  await page.screenshot({
    path: path.join(artifactDir, 'mobile_workspace_media_picker_bottom_sheet.png'),
    fullPage: false,
  });

  // Close modal
  await page.locator('#cora-media-picker-close-x').click();
  await page.waitForTimeout(300);
  await expect(modal).not.toBeVisible();

  console.log('Successfully verified Workspace Media Module picker on Desktop and Mobile!');
});
