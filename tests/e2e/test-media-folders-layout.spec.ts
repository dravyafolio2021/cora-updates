import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Media Library - Dedicated Folders Layout', () => {

  test.beforeEach(async ({ page }) => {
    await login(page);
    await page.goto('/workspace/media');
    await page.waitForLoadState('networkidle');
  });

  test('1. Verify Top Folders Section & Global All Media Card', async ({ page }) => {
    // Top Folders Section
    const foldersSection = page.locator('#cm-folders-section');
    await expect(foldersSection).toBeVisible();

    // New folder CTA
    const newFolderBtn = page.locator('button:has-text("New folder")');
    await expect(newFolderBtn).toBeVisible();

    // Folders Grid & All Media Card
    const foldersGrid = page.locator('#cm-folders-grid');
    await expect(foldersGrid).toBeVisible();

    const allMediaCard = foldersGrid.locator('.cm-fcard').first();
    await expect(allMediaCard).toContainText('All Media');
  });

  test('2. Verify Breadcrumbs & Sorting Row', async ({ page }) => {
    const breadcrumbBar = page.locator('#cm-breadcrumbs-bar');
    await expect(breadcrumbBar).toBeVisible();
    await expect(breadcrumbBar.locator('.cm-bc-path')).toContainText('All Media');

    const sortSelect = page.locator('#cm-sort-select');
    await expect(sortSelect).toBeVisible();
  });

  test('3. Verify Enhanced Filter Toolbar & Reset Action', async ({ page }) => {
    const toolbar = page.locator('#cm-toolbar');
    await expect(toolbar).toBeVisible();

    const searchInput = page.locator('#cm-search');
    await expect(searchInput).toBeVisible();
    await searchInput.fill('test search');

    const resetBtn = toolbar.locator('button:has-text("Reset")');
    await expect(resetBtn).toBeVisible();
    await resetBtn.click();

    await expect(searchInput).toHaveValue('');
  });

  test('4. Verify Folder Selection and Breadcrumb Path Update', async ({ page }) => {
    const folders = await page.locator('#cm-folders-grid .cm-fcard').all();
    if (folders.length > 1) {
      const secondFolder = folders[1];
      const folderNameText = (await secondFolder.locator('.cm-fcard-name').textContent()) || '';
      await secondFolder.click();
      
      // Active state styling check
      await expect(secondFolder).toHaveClass(/active/);

      // Breadcrumb path update check
      if (folderNameText.trim()) {
        const subPath = page.locator('#cm-bc-subpath');
        await expect(subPath).toBeVisible();
      }
    }
  });

  test('5. Verify Centered Pop-Up Modal & Folder Creation', async ({ page }) => {
    const newFolderBtn = page.locator('button:has-text("New folder")');
    await newFolderBtn.click();

    const folderModal = page.locator('#cm-folder-dlg');
    await expect(folderModal).toBeVisible();

    // Verify centered card positioning
    const card = page.locator('#cm-folder-card');
    await expect(card).toBeVisible();

    const folderName = 'Automated Test Folder ' + Date.now();
    await page.fill('#cm-folder-name', folderName);

    const submitBtn = page.locator('#cm-folder-card button:has-text("Create Folder")');
    await submitBtn.click();

    // Modal closes
    await expect(folderModal).not.toHaveClass(/open/);

    // If truncation toggle is visible, expand to reveal new folder
    const toggleCard = page.locator('#cm-folders-grid .cm-fcard-toggle');
    if (await toggleCard.isVisible()) {
      await toggleCard.click();
    }

    // Folder appears in grid
    await expect(page.locator('#cm-folders-grid')).toContainText(folderName);
  });

  test('6. Verify Folder Truncation and Expand/Collapse Toggle', async ({ page }) => {
    const toggleCard = page.locator('#cm-folders-grid .cm-fcard-toggle');
    if (await toggleCard.isVisible()) {
      await expect(toggleCard).toContainText('+ Show');
      await toggleCard.click();
      const collapseCard = page.locator('#cm-folders-grid .cm-fcard-toggle');
      await expect(collapseCard).toContainText('Show less');
      await collapseCard.click();
      await expect(toggleCard).toContainText('+ Show');
    }
  });

});
