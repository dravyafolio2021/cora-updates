import { test, expect, Page } from '@playwright/test';
import { login } from './helpers';

async function ensureMenuExists(page: Page) {
  await page.goto('/workspace/appearance');
  const select = page.locator('#cora-nav-menu-select');
  await select.waitFor({ state: 'visible' });
  const options = await select.locator('option').evaluateAll(elOpts => elOpts.map(opt => (opt as HTMLOptionElement).value));
  
  // If no menu exists (options is empty or only contains 0)
  if (options.length === 0 || (options.length === 1 && options[0] === '0')) {
    await page.click('button[onclick="coraOpenNewMenuDrawer()"]');
    await page.waitForSelector('#cora-drawer-new-menu:not(.translate-x-full)', { state: 'visible' });
    await page.fill('#cora-new-menu-name', 'E2E Main Menu');
    await page.click('#cora-drawer-new-menu button:has-text("Create Menu")');
    await expect(page.locator('#cora-toast-container')).toContainText('Menu created successfully.');
    await page.waitForLoadState('networkidle');
  }
}

test.describe('Tier 1: Feature Coverage', () => {

  test.beforeEach(async ({ page }) => {
    await login(page);
  });

  // ==========================================
  // PAGES MODULE (5 Tests)
  // ==========================================

  test('Pages - 1. List View', async ({ page }) => {
    await page.goto('/workspace/pages');
    const header = page.locator('section#cora-page-pages');
    await expect(header).toBeVisible();
    const table = page.locator('#cora-pages-table-body');
    await expect(table).toBeVisible();
  });

  test('Pages - 2. Create Page', async ({ page }) => {
    await page.goto('/workspace/pages');
    await page.click('button:has-text("New Page")');
    await page.waitForSelector('#cora-drawer-page:not(.translate-x-full)', { state: 'visible' });

    const rand = Math.floor(Math.random() * 10000);
    const title = `E2E Page ${rand}`;
    const slug = `e2e-page-${rand}`;

    await page.fill('#cora-page-title-input', title);
    await page.fill('#cora-page-slug-input', slug);
    await page.selectOption('#cora-page-status-input', 'publish');
    await page.selectOption('#cora-page-template-input', 'full-width');
    await page.fill('#cora-page-quill-editor .ql-editor', 'This is E2E page content.');
    await page.fill('#cora-page-seo-desc-input', 'E2E description');

    await page.click('#cora-drawer-page button:has-text("Save Page")');
    
    // Check toast notification
    await expect(page.locator('#cora-toast-container')).toContainText('Page saved successfully.');
  });

  test('Pages - 3. Edit Page', async ({ page }) => {
    await page.goto('/workspace/pages');
    await page.click('button:has-text("New Page")');
    await page.waitForSelector('#cora-drawer-page:not(.translate-x-full)', { state: 'visible' });

    const rand = Math.floor(Math.random() * 10000);
    const title = `Edit Test ${rand}`;
    await page.fill('#cora-page-title-input', title);
    await page.click('#cora-drawer-page button:has-text("Save Page")');
    await expect(page.locator('#cora-toast-container')).toContainText('Page saved successfully.');

    // Now edit the page
    await page.goto('/workspace/pages');
    const row = page.locator(`tr.cora-page-row:has-text("${title}")`).first();
    await row.waitFor({ state: 'visible' });
    await row.click();
    await page.waitForSelector('#cora-drawer-page:not(.translate-x-full)', { state: 'visible' });

    await page.fill('#cora-page-title-input', `${title} Updated`);
    await page.click('#cora-drawer-page button:has-text("Save Page")');
    await expect(page.locator('#cora-toast-container')).toContainText('Page saved successfully.');
  });

  test('Pages - 4. Change Status', async ({ page }) => {
    await page.goto('/workspace/pages');
    await page.click('button:has-text("New Page")');
    await page.waitForSelector('#cora-drawer-page:not(.translate-x-full)', { state: 'visible' });

    const rand = Math.floor(Math.random() * 10000);
    const title = `Status Test ${rand}`;
    await page.fill('#cora-page-title-input', title);
    await page.selectOption('#cora-page-status-input', 'publish');
    await page.click('#cora-drawer-page button:has-text("Save Page")');
    await expect(page.locator('#cora-toast-container')).toContainText('Page saved successfully.');

    // Edit and change status to Draft
    await page.goto('/workspace/pages');
    const row = page.locator(`tr.cora-page-row:has-text("${title}")`).first();
    await row.waitFor({ state: 'visible' });
    await row.click();
    await page.waitForSelector('#cora-drawer-page:not(.translate-x-full)', { state: 'visible' });

    await page.selectOption('#cora-page-status-input', 'draft');
    await page.click('#cora-drawer-page button:has-text("Save Page")');
    await expect(page.locator('#cora-toast-container')).toContainText('Page saved successfully.');
  });

  test('Pages - 5. Delete Page', async ({ page }) => {
    await page.goto('/workspace/pages');
    await page.click('button:has-text("New Page")');
    await page.waitForSelector('#cora-drawer-page:not(.translate-x-full)', { state: 'visible' });

    const rand = Math.floor(Math.random() * 10000);
    const title = `Delete Test ${rand}`;
    await page.fill('#cora-page-title-input', title);
    await page.click('#cora-drawer-page button:has-text("Save Page")');
    await expect(page.locator('#cora-toast-container')).toContainText('Page saved successfully.');

    // Delete it using confirm modal
    await page.goto('/workspace/pages');
    const row = page.locator(`tr.cora-page-row:has-text("${title}")`).first();
    await row.waitFor({ state: 'visible' });
    await row.locator('button:has-text("Delete")').click();

    // Confirm modal should appear
    const modal = page.locator('#cora-confirm-modal');
    await expect(modal).not.toHaveClass(/hidden/);
    await page.click('#cora-confirm-btn');

    await expect(page.locator('#cora-toast-container')).toContainText('Page deleted successfully.');
  });

  // ==========================================
  // COMMENTS MODULE (5 Tests)
  // ==========================================

  test('Comments - 1. List View', async ({ page }) => {
    await page.goto('/workspace/comments');
    const title = page.locator('h1.cora-page-title');
    await expect(title).toContainText('Client Discussions');
  });

  test('Comments - 2. Filter by Status', async ({ page }) => {
    await page.goto('/workspace/comments');
    await page.click('a[href*="comment_status=hold"]');
    await expect(page).toHaveURL(/.*comment_status=hold.*/);
    await page.click('a[href*="comment_status=approve"]');
    await expect(page).toHaveURL(/.*comment_status=approve.*/);
  });

  test('Comments - 3. Approve Pending', async ({ page }) => {
    await page.goto('/?p=1');
    await page.fill('#comment', 'E2E Comment to approve');
    await page.click('#submit');
    await page.waitForLoadState('networkidle');

    await page.goto('/workspace/comments');
    
    // Toggle Approved comment to Unapproved first to make it Pending
    const unapproveBtn = page.locator('button:has-text("Unapprove")').first();
    if (await unapproveBtn.isVisible()) {
      await unapproveBtn.click();
      await expect(page.locator('#cora-toast-container')).toContainText('Comment status updated successfully.');
    }
    
    // Now look for Approve button and click it
    const approveBtn = page.locator('button:has-text("Approve")').first();
    await approveBtn.waitFor({ state: 'visible' });
    await approveBtn.click();
    await expect(page.locator('#cora-toast-container')).toContainText('Comment status updated successfully.');
  });

  test('Comments - 4. Reply to Comment via Reply Drawer', async ({ page }) => {
    page.on('console', msg => console.log('PAGE LOG:', msg.text()));
    page.on('pageerror', err => console.log('PAGE ERROR:', err.message));

    await page.goto('/?p=1');
    await page.fill('#comment', 'E2E Comment to reply to');
    await page.click('#submit');
    await page.waitForLoadState('networkidle');

    await page.goto('/workspace/comments');
    await page.locator('button:has-text("Reply")').first().click();
    await page.waitForSelector('#cora-drawer-comment-reply:not(.translate-x-full)', { state: 'visible' });
    
    const rand = Math.floor(Math.random() * 100000);
    await page.fill('#cora-reply-textarea', `E2E Reply Text ${rand}`);
    await page.click('#cora-btn-submit-comment-reply');


    await expect(page.locator('#cora-toast-container')).toContainText('Reply posted successfully.');
  });


  test('Comments - 5. Spam/Trash', async ({ page }) => {
    await page.goto('/?p=1');
    await page.fill('#comment', 'E2E Spam Comment');
    await page.click('#submit');
    await page.waitForLoadState('networkidle');

    await page.goto('/workspace/comments');
    await page.locator('button:has-text("Spam")').first().click();
    await expect(page.locator('#cora-toast-container')).toContainText('Comment status updated successfully.');
  });

  // ==========================================
  // APPEARANCE MODULE (5 Tests)
  // ==========================================

  test('Appearance - 1. Save Branding Options', async ({ page }) => {
    await page.goto('/workspace/appearance');
    await page.fill('#cora-brand-tagline', 'Luxury Real Estate Platform');
    await page.fill('#cora-brand-logo-url', 'https://example.com/logo.png');
    await page.fill('#cora-brand-favicon-url', 'https://example.com/favicon.ico');
    await page.click('button:has-text("Save All Settings")');
    await expect(page.locator('#cora-toast-container')).toContainText('Appearance settings saved successfully.');
  });

  test('Appearance - 2. Select Menu', async ({ page }) => {
    await page.goto('/workspace/appearance');
    const select = page.locator('#cora-nav-menu-select');
    await expect(select).toBeVisible();
  });

  test('Appearance - 3. Create Menu', async ({ page }) => {
    await page.goto('/workspace/appearance');
    await page.click('button[onclick="coraOpenNewMenuDrawer()"]');
    await page.waitForSelector('#cora-drawer-new-menu:not(.translate-x-full)', { state: 'visible' });

    const menuName = `Menu_${Math.floor(Math.random() * 10000)}`;
    await page.fill('#cora-new-menu-name', menuName);
    await page.click('#cora-drawer-new-menu button:has-text("Create Menu")');
    await expect(page.locator('#cora-toast-container')).toContainText('Menu created successfully.');
  });

  test('Appearance - 4. Add Menu Item', async ({ page }) => {
    await ensureMenuExists(page);
    await page.goto('/workspace/appearance');
    await page.click('button:has-text("Add Menu Link")');
    await page.waitForSelector('#cora-drawer-menu-item:not(.translate-x-full)', { state: 'visible' });

    await page.selectOption('#cora-menu-item-type', 'custom');
    await page.fill('#cora-menu-custom-url', 'https://cora.local/custom');
    await page.fill('#cora-menu-item-label', 'Custom Link');
    await page.click('button:has-text("Add to Menu")');
    await expect(page.locator('#cora-toast-container')).toContainText('Menu item added successfully.');
  });

  test('Appearance - 5. Remove Menu Item', async ({ page }) => {
    await ensureMenuExists(page);
    await page.goto('/workspace/appearance');
    
    // Check if we need to add an item first
    let deleteBtn = page.locator('#cora-menu-items-list button').first();
    if (!await deleteBtn.isVisible()) {
      await page.click('button:has-text("Add Menu Link")');
      await page.waitForSelector('#cora-drawer-menu-item:not(.translate-x-full)', { state: 'visible' });
      await page.selectOption('#cora-menu-item-type', 'custom');
      await page.fill('#cora-menu-custom-url', 'https://cora.local/custom');
      await page.fill('#cora-menu-item-label', 'Link to Remove');
      await page.click('button:has-text("Add to Menu")');
      await expect(page.locator('#cora-toast-container')).toContainText('Menu item added successfully.');
      await page.waitForLoadState('networkidle');
    }
    
    deleteBtn = page.locator('#cora-menu-items-list button').first();
    await deleteBtn.waitFor({ state: 'visible' });
    await deleteBtn.click();
    await page.waitForSelector('#cora-confirm-modal:not(.hidden)', { state: 'visible' });
    await page.click('#cora-confirm-btn');
    await expect(page.locator('#cora-toast-container')).toContainText('Menu item deleted successfully.');
  });

  // ==========================================
  // SYSTEM TOOLS MODULE (5 Tests)
  // ==========================================

  test('Tools - 1. View Diagnostics & Copy', async ({ page }) => {
    await page.goto('/workspace/tools');
    await page.click('button:has-text("Copy Site Diagnostics")');
    await expect(page.locator('#cora-toast-container')).toContainText('System diagnostics copied to clipboard.');
  });

  test('Tools - 2. XML Export Run', async ({ page }) => {
    await page.goto('/workspace/tools');
    await page.check('input[name="cora_export_type"][value="pages"]');
    await page.click('button:has-text("Download XML Export File")');
    await expect(page.locator('#cora-toast-container')).toContainText('XML WXR export initiated successfully.');
  });

  test('Tools - 3. XML Import Run', async ({ page }) => {
    await page.goto('/workspace/tools');
    
    await page.evaluate(() => {
      window.coraShowSelectedImportFile = function(el) {
        const display = document.getElementById('cora-selected-file-display');
        if (display) {
          display.textContent = 'Selected: ' + (el.files[0]?.name || '');
          display.classList.remove('hidden');
        }
      };
    });

    const fileChooserPromise = page.waitForEvent('filechooser');
    await page.click('button:has-text("Browse Local File")');
    const fileChooser = await fileChooserPromise;
    await fileChooser.setFiles({
      name: 'wxr-test.xml',
      mimeType: 'text/xml',
      buffer: Buffer.from('<rss version="2.0"></rss>')
    });

    await page.click('button:has-text("Run XML Importer")');
    await expect(page.locator('#cora-toast-container')).toContainText('XML WXR import ready.');
  });

  test('Tools - 4. GDPR Export Run', async ({ page }) => {
    await page.goto('/workspace/tools');
    await page.fill('#cora-gdpr-export-email', 'gdpr_client@example.com');
    await page.click('button:has-text("Export Data")');
    await expect(page.locator('#cora-toast-container')).toContainText('GDPR personal data export request generated');
  });

  test('Tools - 5. GDPR Erase Run', async ({ page }) => {
    await page.goto('/workspace/tools');
    await page.fill('#cora-gdpr-erase-email', 'gdpr_client@example.com');
    await page.click('button:has-text("Anonymize & Erase")');
    
    // Confirm modal
    await page.waitForSelector('#cora-confirm-modal:not(.hidden)', { state: 'visible' });
    await page.click('#cora-confirm-btn');
    await expect(page.locator('#cora-toast-container')).toContainText('GDPR personal data erasure request processed');
  });

  // ==========================================
  // MEDIA-EDITOR MODULE (5 Tests)
  // ==========================================

  test('Media-Editor - 1. View Media List', async ({ page }) => {
    await page.goto('/workspace/media-editor');
    const select = page.locator('#cora-editor-media-select');
    await expect(select).toBeVisible();
  });

  test('Media-Editor - 2. Apply Crop Ratio', async ({ page }) => {
    await page.goto('/workspace/media-editor');
    await page.click('button:has-text("1:1 Square")');
    await expect(page.locator('button:has-text("4:3 Standard")')).toBeVisible();
  });

  test('Media-Editor - 3. Rotate/Flip', async ({ page }) => {
    await page.goto('/workspace/media-editor');
    await page.click('button[title="Rotate 90° Clockwise"]');
    await page.click('button[title="Flip Horizontal"]');
    await expect(page.locator('button[title="Rotate 90° Counter-Clockwise"]')).toBeVisible();
  });

  test('Media-Editor - 4. Save Transformation', async ({ page }) => {
    await page.goto('/workspace/media-editor');
    await page.click('button:has-text("Apply & Save Image Transformation")');
    await expect(page.locator('#cora-toast-container')).toContainText('Image saved successfully.');
  });

  test('Media-Editor - 5. Save SEO Metadata', async ({ page }) => {
    await page.goto('/workspace/media-editor');
    await page.fill('#cora-meta-title', 'E2E Title');
    await page.fill('#cora-meta-alt', 'E2E Alt Text');
    await page.fill('#cora-meta-caption', 'E2E Caption Text');
    await page.fill('#cora-meta-description', 'E2E Description Text');
    await page.click('button:has-text("Update SEO Metadata")');
    await expect(page.locator('#cora-toast-container')).toContainText('Media metadata updated successfully.');
  });

  // ==========================================
  // SETTINGS-SUITE MODULE (5 Tests)
  // ==========================================

  test('Settings-Suite - 1. Tab Navigation', async ({ page }) => {
    await page.goto('/workspace/settings-suite');
    await page.click('a[href*="settings_tab=reading"]');
    await expect(page).toHaveURL(/.*settings_tab=reading.*/);
    await page.click('a[href*="settings_tab=writing"]');
    await expect(page).toHaveURL(/.*settings_tab=writing.*/);
  });

  test('Settings-Suite - 2. Save General Settings', async ({ page }) => {
    await page.goto('/workspace/settings-suite?settings_tab=general');
    await page.fill('input[name="blogname"]', 'Cora E2E Test Site');
    await page.click('button:has-text("Save All Settings")');
    await expect(page.locator('#cora-toast-container')).toContainText('Global system settings updated successfully.');
  });

  test('Settings-Suite - 3. Save Reading/Writing Settings', async ({ page }) => {
    await page.goto('/workspace/settings-suite?settings_tab=reading');
    await page.check('input[name="show_on_front"][value="posts"]');
    await page.click('button:has-text("Save All Settings")');
    await expect(page.locator('#cora-toast-container')).toContainText('Global system settings updated successfully.');
  });

  test('Settings-Suite - 4. Save Discussion Settings', async ({ page }) => {
    await page.goto('/workspace/settings-suite?settings_tab=discussion');
    await page.check('input[name="comment_moderation"]');
    await page.click('button:has-text("Save All Settings")');
    await expect(page.locator('#cora-toast-container')).toContainText('Global system settings updated successfully.');
  });

  test('Settings-Suite - 5. Save Permalinks', async ({ page }) => {
    await page.goto('/workspace/settings-suite?settings_tab=permalinks');
    await page.check('input[name="permalink_structure"][value="/%postname%/"]');
    await page.click('button:has-text("Save All Settings")');
    await expect(page.locator('#cora-toast-container')).toContainText('Global system settings updated successfully.');
  });

});
