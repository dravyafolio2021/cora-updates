import { test, expect } from '@playwright/test';
import { login } from './helpers';
import * as path from 'path';

test('Verify All Entries Inspector Drawer docks directly below header with fully visible close button', async ({ page }) => {
  const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/77ca00f3-771e-4ab4-9010-29866a4ae0bf';

  await page.setViewportSize({ width: 1280, height: 850 });
  await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

  // Navigate directly to All Entries tab in Forms
  await page.goto('/workspace/forms#all-entries');
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(1500);

  // Check topbar
  const topbar = page.locator('#cora-global-topbar');
  await expect(topbar).toBeVisible();
  const topbarBox = await topbar.boundingBox();
  expect(topbarBox).not.toBeNull();
  const topbarBottom = topbarBox!.y + topbarBox!.height;
  console.log('Topbar bottom Y coordinate:', topbarBottom);

  // Click on the first entry row in the table
  const firstRow = page.locator('.workspace-entry-row, tr[onclick*="openEntryInspector"]').first();
  await expect(firstRow).toBeVisible();
  await firstRow.click();
  await page.waitForTimeout(600);

  // Check inspector drawer
  const inspector = page.locator('#cora-entry-inspector');
  await expect(inspector).toBeVisible();

  const inspectorBox = await inspector.boundingBox();
  expect(inspectorBox).not.toBeNull();
  console.log('Inspector top Y coordinate:', inspectorBox!.y);
  console.log('Inspector height:', inspectorBox!.height);

  // Crucial assertion: Inspector top MUST be >= topbarBottom (not behind header)
  expect(inspectorBox!.y).toBeGreaterThanOrEqual(Math.floor(topbarBottom) - 1);

  // Check Inspector Header is visible and accessible
  const entryIdHeader = inspector.locator('#inspector-entry-id');
  await expect(entryIdHeader).toBeVisible();
  const entryIdBox = await entryIdHeader.boundingBox();
  expect(entryIdBox).not.toBeNull();
  console.log('Inspector Entry ID Header Y coordinate:', entryIdBox!.y);
  expect(entryIdBox!.y).toBeGreaterThanOrEqual(topbarBottom);

  // Check Close Button is visible, below header, and clickable
  const closeBtn = inspector.locator('button[title="Close"]').first();
  await expect(closeBtn).toBeVisible();
  const closeBtnBox = await closeBtn.boundingBox();
  expect(closeBtnBox).not.toBeNull();
  console.log('Inspector Close Button Y coordinate:', closeBtnBox!.y);
  expect(closeBtnBox!.y).toBeGreaterThanOrEqual(topbarBottom);

  // Take screenshot of desktop docked inspector
  const screenshotPath = path.join(artifactDir, 'forms-entry-inspector-docked.png');
  await page.screenshot({ path: screenshotPath, fullPage: false });
  console.log('Screenshot saved to:', screenshotPath);

  // Click close button and ensure it dismisses
  await closeBtn.click({ force: true });
  await page.waitForTimeout(400);
  const isDismissed = await inspector.evaluate((el) => {
    return el.classList.contains('hidden') || el.classList.contains('translate-x-full') || el.style.display === 'none';
  });
  expect(isDismissed).toBe(true);
  console.log('Drawer successfully closed via close button.');
});
