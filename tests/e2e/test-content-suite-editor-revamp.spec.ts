import { test, expect } from '@playwright/test';
import { login } from './helpers';
import * as path from 'path';

test('Verify Content Suite Editor Revamp: Workspace Media Selector, Ambient Canvas & AI Copilot', async ({ page }) => {
  const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/58e87118-a119-4a20-9906-b30972fba7f6';

  page.on('console', msg => console.log('BROWSER LOG:', msg.text()));
  page.on('pageerror', err => console.log('BROWSER PAGE ERROR:', err.message));

  await page.setViewportSize({ width: 1440, height: 900 });
  await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
  
  // Navigate to Content Suite Library
  await page.goto('/workspace/blogs?ct=ct-library');
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(1000);

  // Open editor by clicking first article edit or creating new
  await page.evaluate(() => {
    if (typeof window.coraEditArticle === 'function') {
      const editBtn = document.querySelector('[onclick*="coraEditArticle"]');
      if (editBtn) {
        editBtn.click();
      } else {
        window.coraOpenContentDrawer();
      }
    } else {
      window.coraOpenContentDrawer();
    }
  });
  await page.waitForTimeout(1000);

  const editor = page.locator('#cora-full-page-editor');
  await expect(editor).toBeVisible();

  // 1. Verify Notion-style ambient header (No bulky 160px dashed dropzone)
  const ambientActions = page.locator('#cora-ambient-header-actions');
  await expect(ambientActions).toBeVisible();
  const ambientCoverBtn = page.locator('#cora-ambient-cover-btn');
  await expect(ambientCoverBtn).toBeVisible();

  // Capture ambient header state
  await page.screenshot({
    path: path.join(artifactDir, 'editor_ambient_canvas_header.png')
  });

  // 2. Verify Left Sidebar Media Tab is unlocked and active
  const mediaTabBtn = page.locator('#cora-left-tab-media');
  await expect(mediaTabBtn).toBeVisible();
  const isDisabled = await mediaTabBtn.getAttribute('disabled');
  expect(isDisabled).toBeNull(); // Should NOT be disabled

  // Click Media tab in left sidebar
  await mediaTabBtn.click();
  await page.waitForTimeout(500);
  const mediaPanel = page.locator('#cora-left-panel-media');
  await expect(mediaPanel).toBeVisible();
  await expect(page.locator('#cora-left-panel-outline')).toBeHidden();

  // Capture left sidebar media tab state
  await page.screenshot({
    path: path.join(artifactDir, 'editor_left_sidebar_media_tab.png')
  });

  // Switch back to Outline tab
  await page.locator('#cora-left-tab-outline').click();
  await page.waitForTimeout(300);

  // 3. Verify Workspace Media Selector Modal (Direct Connection to Workspace Media Vault)
  // Trigger cover selection
  await page.evaluate(() => {
    window.coraOpenMediaLibrary('cover');
  });
  await page.waitForTimeout(600);

  const mediaModal = page.locator('#cora-workspace-media-selector-modal');
  await expect(mediaModal).toBeVisible();

  // Verify modal title says "Workspace Media Vault"
  await expect(page.locator('#cora-media-selector-title')).toContainText(/Workspace Media Vault/i);

  // Verify that real workspace items are rendered in grid
  const mediaGrid = page.locator('#cora-media-selector-grid');
  await expect(mediaGrid).toBeVisible();
  const mediaItems = page.locator('#cora-media-selector-grid > div');
  const itemCount = await mediaItems.count();
  expect(itemCount).toBeGreaterThan(0);

  // Capture Workspace Media Selector modal
  await page.screenshot({
    path: path.join(artifactDir, 'editor_workspace_media_selector_modal.png')
  });

  // Select first media item and confirm
  await mediaItems.first().evaluate((el: HTMLElement) => el.click());
  await page.waitForTimeout(300);
  const confirmBtn = page.locator('#cora-media-selector-confirm-btn');
  await confirmBtn.evaluate((el: HTMLElement) => el.click());
  await page.waitForTimeout(600);

  // Verify modal closed and cover image is displayed
  await expect(mediaModal).toBeHidden();
  const coverImg = page.locator('#cora-cover-image-img');
  await expect(coverImg).toBeVisible();
  const coverUrl = await coverImg.getAttribute('src');
  expect(coverUrl).toBeTruthy();

  // 4. Verify AI Copilot Palette (⌘K)
  const aiPaletteBtn = page.locator('#cora-ai-palette-btn');
  await expect(aiPaletteBtn).toBeVisible();
  await aiPaletteBtn.click();
  await page.waitForTimeout(400);

  const aiPalette = page.locator('#cora-ai-writing-palette');
  await expect(aiPalette).toBeVisible();
  await expect(page.locator('#cora-ai-prompt-input')).toBeVisible();

  // Capture AI Copilot Palette
  await page.screenshot({
    path: path.join(artifactDir, 'editor_ai_copilot_palette.png')
  });

  // Dismiss palette
  await page.keyboard.press('Escape');
  await page.waitForTimeout(300);
  await expect(aiPalette).toBeHidden();

  // 5. Verify Real-time SEO & AI Visibility (GEO) Auditing
  // Focus Quill editor and type rich text
  await page.evaluate(() => {
    if ((window as any).coraQuillListingCoordinator) {
      (window as any).coraQuillListingCoordinator.clipboard.dangerouslyPasteHTML(
        '<h2>Prime Commercial Studio Production Overview</h2><p>Our studio in Gurgaon DLF CyberCity provides state-of-the-art Sony FX3 cameras and Profoto lighting packages with dedicated high-speed optical connectivity for commercial shoots.</p>'
      );
      if (typeof (window as any).coraUpdateWordCount === 'function') {
        (window as any).coraUpdateWordCount();
      }
    }
  });
  await page.waitForTimeout(500);

  // Check that word count and scores updated
  const wordStat = page.locator('#left-stat-words');
  const wordsText = await wordStat.innerText();
  expect(parseInt(wordsText || '0', 10)).toBeGreaterThan(10);

  // Open AI Visibility tab in Inspector
  await page.locator('#btn-sidebar-geo').evaluate((el: HTMLElement) => el.click());
  await page.waitForTimeout(400);

  const geoScoreDisplay = page.locator('#cora-geo-score-display');
  await expect(geoScoreDisplay).toBeVisible();

  // Capture SEO & AI Visibility Inspector
  await page.screenshot({
    path: path.join(artifactDir, 'editor_seo_geo_inspector.png')
  });

  console.log('Content Suite Editor Revamp verified successfully!');
});
