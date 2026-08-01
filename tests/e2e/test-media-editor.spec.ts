import { test, expect } from '@playwright/test';
import { login } from './helpers';
import * as path from 'path';

test('verify media library grid and image editor modal clipping', async ({ page }) => {
  await login(page);

  // 1. Go to workspace/media
  await page.goto('/workspace/media');
  await page.waitForLoadState('networkidle');

  // Verify we are on the media page
  await expect(page.locator('#cm-root')).toBeVisible();

  // Print all file names/cells in the grid
  const cells = await page.locator('.cm-cell').all();
  console.log(`Found ${cells.length} cells in the media list.`);

  if (cells.length === 0) {
    // If no cells, let's upload a dummy file first
    console.log('No media found, uploading a dummy file...');
    // We can use page.setInputFiles to upload
    const fileInput = page.locator('#cm-file-input');
    // Create a dummy text/image file or use an existing one.
    // Let's use the local file path
    const filePath = path.resolve(__dirname, 'helpers.ts'); // just as a test, or a real image
    await fileInput.setInputFiles(filePath);
    await page.waitForTimeout(2000); // Wait for upload
  }

  // Click on the first image item in the grid
  // In view-media.php, cells are grid elements. Let's find one that is an image.
  // Images have a preview or icon. Let's click the first cell.
  const firstCell = page.locator('.cm-cell').first();
  await expect(firstCell).toBeVisible();
  await firstCell.click();

  // Wait for the detail panel to open
  const detailPanel = page.locator('#cm-detail');
  await expect(detailPanel).toHaveClass(/open/);

  // Check if the Edit button is visible
  const editBtn = page.locator('#cm-d-edit-btn');
  const isEditVisible = await editBtn.isVisible();
  console.log(`Edit button visible: ${isEditVisible}`);

  if (!isEditVisible) {
    // If the selected item wasn't an image, let's try other cells
    const allCells = await page.locator('.cm-cell').all();
    for (let i = 1; i < allCells.length; i++) {
      await allCells[i].click({ force: true });
      if (await editBtn.isVisible()) {
        console.log(`Found image at cell index ${i}`);
        break;
      }
    }
  }

  // If Edit button is still not visible, we can try to upload an actual image file
  if (!await editBtn.isVisible()) {
    console.log('No image cell found, uploading a test image...');
    // Use standard local favicon as test image
    const testImagePath = '/Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-workspace/assets/images/cora-favicon.png';
    await page.setInputFiles('#cm-file-input', testImagePath);
    await page.waitForTimeout(3000); // Wait for upload and render
    
    // Find the new image cell and click it
    await page.locator('.cm-cell').first().click({ force: true });
    await expect(editBtn).toBeVisible();
  }

  // Click Edit to open the editor modal
  await editBtn.click();
  
  // Wait for editor modal to open
  const editorModal = page.locator('#cm-editor-modal');
  await expect(editorModal).toHaveClass(/open/);

  // Wait for image to load in canvas
  const editorImg = page.locator('#cm-editor-img');
  await expect(editorImg).toBeVisible();

  // Test under normal size (1280x720)
  await page.setViewportSize({ width: 1280, height: 720 });
  await page.waitForTimeout(1000);
  let layout = await getLayout(page);
  console.log('Layout at 1280x720:', JSON.stringify(layout, null, 2));
  await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/587dd660-2d30-493a-9a58-9c57a142bb9d/media_editor_1280_720.png' });

  // Test under narrower width (750x720) - check if wrapping happens and if it clips
  await page.setViewportSize({ width: 750, height: 720 });
  await page.waitForTimeout(1000);
  layout = await getLayout(page);
  console.log('Layout at 750x720 (narrow):', JSON.stringify(layout, null, 2));
  await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/587dd660-2d30-493a-9a58-9c57a142bb9d/media_editor_750_720.png' });

  // Test under short viewport (1280x500) - check if it clips vertically
  await page.setViewportSize({ width: 1280, height: 500 });
  await page.waitForTimeout(1000);
  layout = await getLayout(page);
  console.log('Layout at 1280x500 (short):', JSON.stringify(layout, null, 2));
  await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/587dd660-2d30-493a-9a58-9c57a142bb9d/media_editor_1280_500.png' });

  // Test under mobile/narrow + short (500x600)
  await page.setViewportSize({ width: 500, height: 600 });
  await page.waitForTimeout(1000);
  layout = await getLayout(page);
  console.log('Layout at 500x600:', JSON.stringify(layout, null, 2));
  await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/587dd660-2d30-493a-9a58-9c57a142bb9d/media_editor_500_600.png' });

  // Let's check the Done button
  const doneBtn = page.locator('button:has-text("Done")');
  await expect(doneBtn).toBeVisible();
});

async function getLayout(page) {
  return await page.evaluate(() => {
    const modal = document.getElementById('cm-editor-modal');
    const card = document.getElementById('cm-editor-card');
    const header = document.getElementById('cm-editor-card-header');
    const canvas = document.getElementById('cm-editor-canvas');
    const bar = document.getElementById('cm-editor-bar');

    if (!modal || !card || !header || !canvas || !bar) {
      return { error: 'One or more editor elements not found' };
    }

    const modalRect = modal.getBoundingClientRect();
    const cardRect = card.getBoundingClientRect();
    const headerRect = header.getBoundingClientRect();
    const canvasRect = canvas.getBoundingClientRect();
    const barRect = bar.getBoundingClientRect();

    // Check if the bar extends beyond the card bottom or screen bottom
    const isBarClippedByCard = barRect.bottom > cardRect.bottom + 1; // 1px tolerance
    const isBarClippedByScreen = barRect.bottom > window.innerHeight + 1;

    // Check if bar children wrap and if any children are clipped or overflowed horizontally/vertically
    const childrenRects = Array.from(bar.children).map(c => {
      const r = c.getBoundingClientRect();
      return {
        tag: c.tagName,
        text: c.textContent?.trim(),
        rect: { top: r.top, bottom: r.bottom, left: r.left, right: r.right, height: r.height, width: r.width }
      };
    });

    return {
      windowHeight: window.innerHeight,
      windowWidth: window.innerWidth,
      modal: { top: modalRect.top, bottom: modalRect.bottom, height: modalRect.height },
      card: { top: cardRect.top, bottom: cardRect.bottom, height: cardRect.height },
      header: { height: headerRect.height },
      canvas: { height: canvasRect.height },
      bar: { top: barRect.top, bottom: barRect.bottom, height: barRect.height, scrollHeight: bar.scrollHeight },
      isBarClippedByCard,
      isBarClippedByScreen,
      children: childrenRects
    };
  });
}
