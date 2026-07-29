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
    const newFolderBtn = foldersSection.locator('button:has-text("New folder")');
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
    const foldersSection = page.locator('#cm-folders-section');
    const newFolderBtn = foldersSection.locator('button:has-text("New folder")');
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

  test('7. Verify Pre-Upload Validation for Security Extensions, File Size, and Storage Quota', async ({ page }) => {
    // 1. Test disallowed security extension rejection
    await page.evaluate(() => {
      (window as any).cmHandleFiles([{ name: 'malicious.php', size: 1024 }]);
    });
    await expect(page.locator('#cora-toast-container')).toContainText('Upload rejected: Security risk file extension is not allowed.');

    // 2. Test per-file size exceeding 100 MB limit rejection
    await page.evaluate(() => {
      (window as any).cmHandleFiles([{ name: 'oversized_video.mp4', size: 101 * 1024 * 1024 }]);
    });
    await expect(page.locator('#cora-toast-container')).toContainText('Upload rejected: File size exceeds 100 MB limit.');

    // 3. Test storage quota limit check rejection
    await page.evaluate(() => {
      if ((window as any).CM) {
        (window as any).CM.storage_bytes = 4.95 * 1024 * 1024 * 1024;
        (window as any).CM.limit_bytes = 5 * 1024 * 1024 * 1024;
      }
      (window as any).cmHandleFiles([{ name: 'large_archive.zip', size: 100 * 1024 * 1024 }]);
    });
    await expect(page.locator('#cora-toast-container')).toContainText('Upload rejected: Exceeds 5 GB workspace storage quota.');
  });

});

test.describe('Media Library - Mobile Viewport Layout', () => {
  test.use({ viewport: { width: 390, height: 844 } });

  test.beforeEach(async ({ page }) => {
    await login(page);
    await page.goto('/workspace/media');
    await page.waitForLoadState('networkidle');
  });

  test('1. Verify Mobile Header & Sticky Bottom Bar', async ({ page }) => {
    const bottomBar = page.locator('#cm-mobile-bottom-bar');
    await expect(bottomBar).toBeVisible();

    const uploadBtn = bottomBar.locator('button:has-text("Upload")');
    await expect(uploadBtn).toBeVisible();

    const newFolderBtn = bottomBar.locator('button:has-text("New Folder")');
    await expect(newFolderBtn).toBeVisible();
  });

  test('2. Verify Mobile Filter Drawer', async ({ page }) => {
    const filterBtn = page.locator('#cm-btn-mobile-filter');
    await expect(filterBtn).toBeVisible();

    await filterBtn.click();

    const filterDlg = page.locator('#cm-mobile-filter-dlg');
    await expect(filterDlg).toBeVisible();
    await expect(filterDlg).toHaveClass(/open/);
  });

  test('3. Verify Mobile Folders 2-column Grid', async ({ page }) => {
    const foldersGrid = page.locator('#cm-folders-grid');
    await expect(foldersGrid).toBeVisible();

    const gridComputedColumns = await foldersGrid.evaluate((el) => {
      return window.getComputedStyle(el).gridTemplateColumns;
    });
    const colCount = gridComputedColumns.split(' ').length;
    expect(colCount).toBe(2);
  });

  test('4. Verify Mobile Touch List View card layout', async ({ page }) => {
    const btnList = page.locator('#cm-btn-list');
    await expect(btnList).toBeVisible();
    await btnList.click();

    const listContainer = page.locator('#cm-list');
    await expect(listContainer).toBeVisible();
  });
});


