import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('Verify Embedded Media Block and Media Vault Integration', async ({ page }) => {
  const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/58e87118-a119-4a20-9906-b30972fba7f6';

  await page.setViewportSize({ width: 1280, height: 850 });
  await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

  // 1. Navigate to Content Suite
  await page.goto('/workspace/blogs?ct=ct-library');
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(1000);

  // 2. Open Full-Page Article Editor
  await page.evaluate(() => {
    if (typeof window.coraToggleContentDrawer === 'function') {
      window.coraToggleContentDrawer(true);
    }
  });
  await page.waitForTimeout(800);

  // 3. Switch to Left Sidebar Media Tab
  const mediaTabBtn = page.locator('#cora-left-tab-media');
  await expect(mediaTabBtn).toBeVisible();
  await mediaTabBtn.click();
  await page.waitForTimeout(500);

  // 4. Verify Initial Empty State of "EMBEDDED IN ARTICLE"
  const embeddedCount = page.locator('#cora-left-embedded-count');
  await expect(embeddedCount).toHaveText('0');

  const emptyCard = page.locator('#left-sidebar-media-grid .col-span-2');
  await expect(emptyCard).toBeVisible();
  await expect(emptyCard).toContainText('No Embedded Media');
  await expect(emptyCard).toContainText('Click or drop images here');

  // Capture screenshot of empty state
  await page.locator('#cora-left-panel-media').screenshot({
    path: `${artifactDir}/embedded_media_empty_state.png`
  });
  console.log('Captured embedded_media_empty_state.png');

  // 5. Click the Empty State Card (or "+ Add Image" button) to open Media Vault
  const addImageBtn = emptyCard.locator('button:has-text("Add Image")');
  await addImageBtn.click();
  await page.waitForTimeout(600);

  // Verify Media Selector modal is visible and layered above the editor
  const modal = page.locator('#cora-workspace-media-selector-modal');
  await expect(modal).toBeVisible();

  // Capture screenshot of opened modal above editor
  await page.screenshot({
    path: `${artifactDir}/media_vault_modal_above_editor.png`
  });
  console.log('Captured media_vault_modal_above_editor.png');

  // 6. Select a media card from the vault
  const firstCard = page.locator('.cora-media-card').first();
  await expect(firstCard).toBeVisible();
  await firstCard.scrollIntoViewIfNeeded();
  await firstCard.click({ force: true });
  await page.waitForTimeout(400);

  // Verify selection counter updated
  const counter = page.locator('#cora-media-selector-counter');
  await expect(counter).toHaveText('1 asset selected');

  // Confirm selection
  const confirmBtn = page.locator('#cora-media-selector-confirm-btn');
  await expect(confirmBtn).toBeEnabled();
  await confirmBtn.click();
  await page.waitForTimeout(1000);

  // Modal should now be closed
  await expect(modal).toBeHidden();

  // 7. Verify Embedded Block in Left Sidebar Updated
  await expect(embeddedCount).toHaveText('1');

  const embeddedCard = page.locator('#left-sidebar-media-grid .group').first();
  await expect(embeddedCard).toBeVisible();
  await expect(embeddedCard).toContainText('#1');

  // Capture screenshot of updated embedded media grid
  await page.locator('#cora-left-panel-media').screenshot({
    path: `${artifactDir}/embedded_media_single_card.png`
  });
  console.log('Captured embedded_media_single_card.png');

  // 8. Test Locate action
  await embeddedCard.hover();
  await page.waitForTimeout(300);
  const locateBtn = embeddedCard.locator('.locate-btn');
  await expect(locateBtn).toBeVisible();
  await locateBtn.click();
  await page.waitForTimeout(500);

  // 9. Test Delete action
  await embeddedCard.hover();
  await page.waitForTimeout(300);
  const deleteBtn = embeddedCard.locator('.delete-btn');
  await expect(deleteBtn).toBeVisible();
  await deleteBtn.click();
  await page.waitForTimeout(800);

  // Verify Embedded Block reverted back to 0 and empty state restored
  await expect(embeddedCount).toHaveText('0');
  await expect(page.locator('#left-sidebar-media-grid')).toContainText('No Embedded Media');

  // Capture screenshot of clean restored state
  await page.locator('#cora-left-panel-media').screenshot({
    path: `${artifactDir}/embedded_media_restored_after_delete.png`
  });
  console.log('Captured embedded_media_restored_after_delete.png');
});
