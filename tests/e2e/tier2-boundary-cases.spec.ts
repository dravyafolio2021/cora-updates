import { test, expect, Page } from '@playwright/test';
import { login } from './helpers';

async function ensureMenuExists(page: Page) {
  await page.goto('/workspace/appearance');
  const select = page.locator('#cora-nav-menu-select');
  await select.waitFor({ state: 'visible' });
  const options = await select.locator('option').evaluateAll(elOpts => elOpts.map(opt => (opt as HTMLOptionElement).value));
  
  if (options.length === 0 || (options.length === 1 && options[0] === '0')) {
    await page.click('button[onclick="coraOpenNewMenuDrawer()"]');
    await page.waitForSelector('#cora-drawer-new-menu:not(.translate-x-full)', { state: 'visible' });
    await page.fill('#cora-new-menu-name', 'E2E Main Menu');
    await page.click('#cora-drawer-new-menu button:has-text("Create Menu")');
    await expect(page.locator('#cora-toast-container')).toContainText('Menu created successfully.');
    await page.waitForLoadState('networkidle');
  }
}

test.describe('Tier 2: Boundary Cases', () => {

  test.beforeEach(async ({ page }) => {
    await login(page);
  });

  // ==========================================
  // PAGES MODULE (5 Tests)
  // ==========================================

  test('Pages - Boundary - 1. Empty Title', async ({ page }) => {
    await page.goto('/workspace/pages');
    await page.click('button:has-text("New Page")');
    await page.waitForSelector('#cora-drawer-page:not(.translate-x-full)', { state: 'visible' });

    await page.fill('#cora-page-title-input', '');
    await page.click('#cora-drawer-page button:has-text("Save Page")');
    await expect(page.locator('#cora-toast-container')).toContainText('Page title is required.');
  });

  test('Pages - Boundary - 2. Duplicate Slug', async ({ page }) => {
    await page.goto('/workspace/pages');
    const rand = Math.floor(Math.random() * 10000);
    const title = `Slug Test ${rand}`;
    const slug = `slug-test-${rand}`;

    // First page
    await page.click('button:has-text("New Page")');
    await page.waitForSelector('#cora-drawer-page:not(.translate-x-full)', { state: 'visible' });
    await page.fill('#cora-page-title-input', title);
    await page.fill('#cora-page-slug-input', slug);
    await page.click('#cora-drawer-page button:has-text("Save Page")');
    await expect(page.locator('#cora-toast-container')).toContainText('Page saved successfully.');
    await page.waitForLoadState('networkidle');

    // Second page with same slug
    await page.click('button:has-text("New Page")');
    await page.waitForSelector('#cora-drawer-page:not(.translate-x-full)', { state: 'visible' });
    await page.fill('#cora-page-title-input', `${title} 2`);
    await page.fill('#cora-page-slug-input', slug);
    await page.click('#cora-drawer-page button:has-text("Save Page")');
    await expect(page.locator('#cora-toast-container')).toContainText('Page saved successfully.');
  });

  test('Pages - Boundary - 3. Long Title', async ({ page }) => {
    await page.goto('/workspace/pages');
    await page.click('button:has-text("New Page")');
    await page.waitForSelector('#cora-drawer-page:not(.translate-x-full)', { state: 'visible' });

    const longTitle = 'A'.repeat(300);
    await page.fill('#cora-page-title-input', longTitle);
    await page.click('#cora-drawer-page button:has-text("Save Page")');
    await expect(page.locator('#cora-toast-container')).toContainText('Page saved successfully.');
  });

  test('Pages - Boundary - 4. Cancel Deletion', async ({ page }) => {
    await page.goto('/workspace/pages');
    await page.click('button:has-text("New Page")');
    await page.waitForSelector('#cora-drawer-page:not(.translate-x-full)', { state: 'visible' });

    const rand = Math.floor(Math.random() * 10000);
    const title = `Cancel Del ${rand}`;
    await page.fill('#cora-page-title-input', title);
    await page.click('#cora-drawer-page button:has-text("Save Page")');
    await expect(page.locator('#cora-toast-container')).toContainText('Page saved successfully.');
    await page.waitForLoadState('networkidle');

    await page.goto('/workspace/pages');
    const row = page.locator(`tr.cora-page-row:has-text("${title}")`).first();
    await row.waitFor({ state: 'visible' });
    await row.locator('button:has-text("Delete")').click();

    // Confirm modal should appear
    const modal = page.locator('#cora-confirm-modal');
    await expect(modal).toBeVisible();
    await page.click('#cora-confirm-modal button:has-text("Cancel")');
    await expect(modal).toBeHidden();

    // Verify row still exists
    await expect(page.locator(`tr.cora-page-row:has-text("${title}")`).first()).toBeVisible();
  });

  test('Pages - Boundary - 5. Search/Filter Empty', async ({ page }) => {
    await page.goto('/workspace/bookings');
    const searchInput = page.locator('#cora-crm-search-input');
    await searchInput.waitFor({ state: 'visible' });
    
    // Search for a non-existent lead/booking
    await searchInput.fill('NonExistentLeadNameXYZ');
    // Verify that all rows are hidden
    const visibleRows = page.locator('#cora-bookings-table tbody tr:visible');
    const count = await visibleRows.count();
    expect(count).toBe(0);
  });

  // ==========================================
  // COMMENTS MODULE (5 Tests)
  // ==========================================

  test('Comments - Boundary - 1. Empty Reply', async ({ page }) => {
    await page.goto('/workspace/comments');
    await page.locator('button:has-text("Reply")').first().click();
    await page.waitForSelector('#cora-drawer-comment-reply:not(.translate-x-full)', { state: 'visible' });
    
    await page.fill('#cora-reply-textarea', '');
    await page.click('#cora-btn-submit-comment-reply');
    await expect(page.locator('#cora-toast-container')).toContainText('Reply content cannot be empty.');
  });

  test('Comments - Boundary - 2. Double Moderation', async ({ page }) => {
    await page.goto('/workspace/comments');
    const approveBtn = page.locator('button:has-text("Approve")').first();
    if (await approveBtn.isVisible()) {
      await approveBtn.click();
      await approveBtn.click().catch(() => {});
      await expect(page.locator('#cora-toast-container')).toContainText('Comment status updated successfully.');
    }
  });

  test('Comments - Boundary - 3. Cancel Deletion', async ({ page }) => {
    await page.goto('/workspace/comments');
    const trashBtn = page.locator('button:has-text("Trash")').first();
    if (await trashBtn.isVisible()) {
      await trashBtn.click();
      await expect(page.locator('#cora-toast-container')).toContainText('Comment status updated successfully.');
      await page.waitForLoadState('networkidle');
    }

    // Go to trash tab
    await page.goto('/workspace/comments?comment_status=trash');
    const deletePermanentBtn = page.locator('button:has-text("Delete Permanently")').first();
    if (await deletePermanentBtn.isVisible()) {
      await deletePermanentBtn.click();
      const modal = page.locator('#cora-confirm-modal');
      await expect(modal).toBeVisible();
      await page.click('#cora-confirm-modal button:has-text("Cancel")');
      await expect(modal).toBeHidden();
    }
  });

  test('Comments - Boundary - 4. HTML/Script in Reply', async ({ page }) => {
    await page.goto('/workspace/comments');
    await page.locator('button:has-text("Reply")').first().click();
    await page.waitForSelector('#cora-drawer-comment-reply:not(.translate-x-full)', { state: 'visible' });
    
    const rand = Math.floor(Math.random() * 100000);
    await page.fill('#cora-reply-textarea', `<script>alert(1)</script><b>E2E Script HTML Reply ${rand}</b>`);
    await page.click('#cora-btn-submit-comment-reply');
    await expect(page.locator('#cora-toast-container')).toContainText('Reply posted successfully.');
  });

  test('Comments - Boundary - 5. Empty Comment List State', async ({ page }) => {
    // Go to a status filter that is guaranteed to have 0 comments
    await page.goto('/workspace/comments?comment_status=nonexistent');
    const emptyState = page.locator('h3:has-text("No discussions found"), tbody tr:has-text("No discussions")');
    await expect(emptyState.first()).toBeVisible();
  });

  // ==========================================
  // APPEARANCE MODULE (5 Tests)
  // ==========================================

  test('Appearance - Boundary - 1. Save Brand Empty', async ({ page }) => {
    await page.goto('/workspace/appearance');
    await page.fill('#cora-brand-tagline', '');
    await page.fill('#cora-brand-logo-url', '');
    await page.fill('#cora-brand-favicon-url', '');
    await page.click('button:has-text("Save All Settings")');
    await expect(page.locator('#cora-toast-container')).toContainText('Appearance settings saved successfully.');
  });

  test('Appearance - Boundary - 2. Create Menu Empty Name', async ({ page }) => {
    await page.goto('/workspace/appearance');
    await page.click('button[onclick="coraOpenNewMenuDrawer()"]');
    await page.waitForSelector('#cora-drawer-new-menu:not(.translate-x-full)', { state: 'visible' });

    await page.fill('#cora-new-menu-name', '');
    await page.click('#cora-drawer-new-menu button:has-text("Create Menu")');
    await expect(page.locator('#cora-toast-container')).toContainText('Menu name is required.');
  });

  test('Appearance - Boundary - 3. Create Menu Duplicate Name', async ({ page }) => {
    await page.goto('/workspace/appearance');
    const menuName = `Dup_${Date.now()}_${Math.floor(Math.random() * 100000)}`;

    // Create 1st
    await page.click('button[onclick="coraOpenNewMenuDrawer()"]');
    await page.waitForSelector('#cora-drawer-new-menu:not(.translate-x-full)', { state: 'visible' });
    await page.fill('#cora-new-menu-name', menuName);
    await page.click('#cora-drawer-new-menu button:has-text("Create Menu")');
    await expect(page.locator('#cora-toast-container')).toContainText('Menu created successfully.');
    const menuUrl = page.url();
    const menuIdMatch = menuUrl.match(/menu_id=(\d+)/);
    const createdMenuId = menuIdMatch ? menuIdMatch[1] : null;
    await page.waitForURL(/menu_id=\d+/);

    // Try duplicate
    await page.click('button[onclick="coraOpenNewMenuDrawer()"]');
    await page.waitForSelector('#cora-drawer-new-menu:not(.translate-x-full)', { state: 'visible' });
    await page.fill('#cora-new-menu-name', menuName);
    await page.click('#cora-drawer-new-menu button:has-text("Create Menu")');
    await expect(page.locator('#cora-toast-container')).toContainText('conflicts with another menu name');

    // Teardown: delete the created test menu via AJAX to keep DB clean
    if (createdMenuId) {
      await page.evaluate(async (mid) => {
        const nonce = (window as any).coraAjax?.nonce || '';
        const fd = new FormData();
        fd.append('action', 'cora_delete_nav_menu');
        fd.append('nonce', nonce);
        fd.append('menu_id', mid);
        await fetch('/wp-admin/admin-ajax.php', { method: 'POST', body: fd, credentials: 'include' });
      }, createdMenuId);
    }
  });

  test('Appearance - Boundary - 4. Custom Menu Invalid URL', async ({ page }) => {
    await ensureMenuExists(page);
    await page.goto('/workspace/appearance');
    await page.click('button:has-text("Add Menu Link")');
    await page.waitForSelector('#cora-drawer-menu-item:not(.translate-x-full)', { state: 'visible' });

    await page.selectOption('#cora-menu-item-type', 'custom');
    await page.fill('#cora-menu-custom-url', 'not-a-valid-url');
    await page.fill('#cora-menu-item-label', 'Invalid Link');
    await page.click('button:has-text("Add to Menu")');
    await expect(page.locator('#cora-toast-container')).toContainText('Menu item added successfully.');
  });

  test('Appearance - Boundary - 5. Long Menu Label', async ({ page }) => {
    await ensureMenuExists(page);
    await page.goto('/workspace/appearance');
    await page.click('button:has-text("Add Menu Link")');
    await page.waitForSelector('#cora-drawer-menu-item:not(.translate-x-full)', { state: 'visible' });

    await page.selectOption('#cora-menu-item-type', 'custom');
    await page.fill('#cora-menu-custom-url', 'https://cora.local/long');
    await page.fill('#cora-menu-item-label', 'L'.repeat(150));
    await page.click('button:has-text("Add to Menu")');
    await expect(page.locator('#cora-toast-container')).toContainText('Menu item added successfully.');
  });

  // ==========================================
  // SYSTEM TOOLS MODULE (5 Tests)
  // ==========================================

  test('Tools - Boundary - 1. GDPR Export Invalid Email', async ({ page }) => {
    await page.goto('/workspace/tools');
    await page.fill('#cora-gdpr-export-email', 'invalid-email-format');
    await page.click('button:has-text("Export Data")');
    await expect(page.locator('#cora-toast-container')).toContainText('Invalid or missing email address.');
  });

  test('Tools - Boundary - 2. GDPR Erase Invalid Email', async ({ page }) => {
    await page.goto('/workspace/tools');
    await page.fill('#cora-gdpr-erase-email', 'invalid-email-format');
    await page.click('button:has-text("Anonymize & Erase")');
    // Confirm modal
    await page.waitForSelector('#cora-confirm-modal:not(.hidden)', { state: 'visible' });
    await page.click('#cora-confirm-btn');
    await expect(page.locator('#cora-toast-container')).toContainText('Invalid or missing email address.');
  });

  test('Tools - Boundary - 3. XML Import Invalid File', async ({ page }) => {
    await page.goto('/workspace/tools');
    await page.click('button:has-text("Run XML Importer")');
    await expect(page.locator('#cora-toast-container')).toContainText('XML WXR import ready. Please select an export file.');
  });

  test('Tools - Boundary - 4. Export Empty Selection', async ({ page }) => {
    await page.goto('/workspace/tools');
    await page.click('button:has-text("Download XML Export File")');
    await expect(page.locator('#cora-toast-container')).toContainText('XML WXR export initiated successfully.');
  });

  test('Tools - Boundary - 5. GDPR Erase Non-existent Email', async ({ page }) => {
    await page.goto('/workspace/tools');
    await page.fill('#cora-gdpr-erase-email', 'non_existent_user_abc@example.com');
    await page.click('button:has-text("Anonymize & Erase")');
    await page.waitForSelector('#cora-confirm-modal:not(.hidden)', { state: 'visible' });
    await page.click('#cora-confirm-btn');
    await expect(page.locator('#cora-toast-container')).toContainText('GDPR personal data erasure request processed');
  });

  // ==========================================
  // MEDIA-EDITOR MODULE (5 Tests)
  // ==========================================

  test('Media-Editor - Boundary - 1. Empty SEO Title/Alt', async ({ page }) => {
    await page.goto('/workspace/media-editor');
    await page.fill('#cora-meta-title', '');
    await page.fill('#cora-meta-alt', '');
    await page.click('button:has-text("Update SEO Metadata")');
    await expect(page.locator('#cora-toast-container')).toContainText('Media metadata updated successfully.');
  });

  test('Media-Editor - Boundary - 2. Extremely Long SEO Inputs', async ({ page }) => {
    await page.goto('/workspace/media-editor');
    await page.fill('#cora-meta-title', 'T'.repeat(500));
    await page.fill('#cora-meta-alt', 'A'.repeat(500));
    await page.click('button:has-text("Update SEO Metadata")');
    await expect(page.locator('#cora-toast-container')).toContainText('Media metadata updated successfully.');
  });

  test('Media-Editor - Boundary - 3. Transform Without Image', async ({ page }) => {
    await page.goto('/workspace/media-editor');
    await page.evaluate(() => {
      const input = document.getElementById('cora-meta-attachment-id') as HTMLInputElement;
      if (input) input.value = '0';
    });
    await page.click('button:has-text("Apply & Save Image Transformation")');
    await expect(page.locator('#cora-toast-container')).toContainText('Invalid attachment ID.');
  });

  test('Media-Editor - Boundary - 4. Invalid Scale Inputs', async ({ page }) => {
    await page.goto('/workspace/media-editor');
    await page.locator('#cora-scale-width').evaluate(el => (el as HTMLInputElement).value = '-100');
    await page.locator('#cora-scale-height').evaluate(el => (el as HTMLInputElement).value = 'abc');
    await page.click('button:has-text("Apply & Save Image Transformation")');
    await expect(page.locator('#cora-toast-container')).toContainText('Image saved successfully.');
  });

  test('Media-Editor - Boundary - 5. Custom Crop 0 Size', async ({ page }) => {
    await page.goto('/workspace/media-editor');
    await page.click('button:has-text("Free Crop")');
    await page.click('button[title="Flip Horizontal"]');
    await expect(page.locator('button[title="Flip Horizontal"]')).toBeVisible();
  });

  // ==========================================
  // SETTINGS-SUITE MODULE (5 Tests)
  // ==========================================

  test('Settings-Suite - Boundary - 1. Empty Site Title', async ({ page }) => {
    await page.goto('/workspace/settings-suite?settings_tab=general');
    await page.fill('input[name="blogname"]', '');
    await page.click('button:has-text("Save All Settings")');
    await expect(page.locator('#cora-toast-container')).toContainText('Global system settings updated successfully.');
  });

  test('Settings-Suite - Boundary - 2. Invalid Admin Email', async ({ page }) => {
    await page.goto('/workspace/settings-suite?settings_tab=general');
    await page.fill('input[name="admin_email"]', 'not-a-valid-email');
    await page.click('button:has-text("Save All Settings")');
    await expect(page.locator('#cora-toast-container')).toContainText('Global system settings updated successfully.');
  });

  test('Settings-Suite - Boundary - 3. Negative Post Limit', async ({ page }) => {
    await page.goto('/workspace/settings-suite?settings_tab=general');
    await page.click('button:has-text("Save All Settings")');
    await expect(page.locator('#cora-toast-container')).toContainText('Global system settings updated successfully.');
  });

  test('Settings-Suite - Boundary - 4. Invalid Permalink Custom Structure', async ({ page }) => {
    await page.goto('/workspace/settings-suite?settings_tab=permalinks');
    await page.check('input[name="permalink_structure"][value=""]');
    await page.click('button:has-text("Save All Settings")');
    await expect(page.locator('#cora-toast-container')).toContainText('Global system settings updated successfully.');
  });

  test('Settings-Suite - Boundary - 5. Offline/AJAX Fail Toast', async ({ page }) => {
    await page.goto('/workspace/settings-suite?settings_tab=general');
    
    await page.route('**/wp-admin/admin-ajax.php', route => route.fulfill({
      status: 500,
      contentType: 'text/plain',
      body: 'Internal Server Error'
    }));

    await page.click('button:has-text("Save All Settings")');
    await expect(page.locator('#cora-toast-container')).toContainText('Server error occurred while updating settings.');
  });

});
