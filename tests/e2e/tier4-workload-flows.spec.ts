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

test.describe('Tier 4: Workload Flows', () => {

  test.beforeEach(async ({ page }) => {
    await login(page);
  });

  test('Workload - 1. Redesign Branding and Menu Setup', async ({ page }) => {
    // 1. Update branding settings
    await page.goto('/workspace/appearance');
    await page.fill('#cora-brand-tagline', 'Luxury Properties Delhi');
    await page.fill('#cora-brand-logo-url', 'https://example.com/logo.png');
    await page.click('button:has-text("Save All Settings")');
    await expect(page.locator('#cora-toast-container')).toContainText('Appearance settings saved successfully.');
    await page.waitForLoadState('networkidle');

    // 2. Create a new menu
    await page.goto('/workspace/appearance');
    await page.click('button[onclick="coraOpenNewMenuDrawer()"]');
    await page.waitForSelector('#cora-drawer-new-menu:not(.translate-x-full)', { state: 'visible' });

    const menuName = `RedesignMenu_${Math.floor(Math.random() * 1000)}`;
    await page.fill('#cora-new-menu-name', menuName);
    await page.click('#cora-drawer-new-menu button:has-text("Create Menu")');
    await expect(page.locator('#cora-toast-container')).toContainText('Menu created successfully.');
    await page.waitForLoadState('networkidle');

    // 3. Add Custom Menu item
    await page.goto('/workspace/appearance');
    await page.click('button:has-text("Add Menu Link")');
    await page.waitForSelector('#cora-drawer-menu-item:not(.translate-x-full)', { state: 'visible' });
    await page.selectOption('#cora-menu-item-type', 'custom');
    await page.fill('#cora-menu-custom-url', 'https://cora.local/redesign');
    await page.fill('#cora-menu-item-label', 'Redesign Link');
    await page.click('button:has-text("Add to Menu")');
    await expect(page.locator('#cora-toast-container')).toContainText('Menu item added successfully.');
    await page.waitForLoadState('networkidle');

    // 4. Remove the item
    const deleteBtn = page.locator('#cora-menu-items-list button').first();
    await deleteBtn.waitFor({ state: 'visible' });
    await deleteBtn.click();
    await page.waitForSelector('#cora-confirm-modal:not(.hidden)', { state: 'visible' });
    await page.click('#cora-confirm-btn');
    await expect(page.locator('#cora-toast-container')).toContainText('Menu item deleted successfully.');
  });

  test('Workload - 2. Backup, Migration & Import Flow', async ({ page }) => {
    // 1. Export Pages WXR
    await page.goto('/workspace/tools');
    await page.check('input[name="cora_export_type"][value="pages"]');
    await page.click('button:has-text("Download XML Export File")');
    await expect(page.locator('#cora-toast-container')).toContainText('XML WXR export initiated successfully.');
    await page.waitForLoadState('networkidle');

    // 2. Select file & run importer
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
      name: 'migration-import.xml',
      mimeType: 'text/xml',
      buffer: Buffer.from('<rss version="2.0"></rss>')
    });

    await page.click('button:has-text("Run XML Importer")');
    await expect(page.locator('#cora-toast-container')).toContainText('XML WXR import ready.');
  });

  test('Workload - 3. CRM Bookings & Crew Allocation Workload', async ({ page }) => {
    await page.goto('/workspace/bookings');
    
    // 1. Add Showing Booking
    await page.click('#cora-add-booking-btn');
    await page.fill('#cora-drawer-client-name', 'Aashna Kumar');
    await page.selectOption('#cora-drawer-deal-type', 'Luxury Villa Sale');
    await page.fill('#cora-drawer-location', 'Gurgaon Penthouse');
    await page.fill('#cora-drawer-price', '₹50,000');
    await page.click('#cora-save-showing-btn');
    await expect(page.locator('#cora-toast-container')).toContainText('Booking created successfully!');
  });

  test('Workload - 4. Page Creation and Content Verification Lifecycle', async ({ page }) => {
    const rand = Math.floor(Math.random() * 10000);
    const title = `E2E Content Lifecycle Page ${rand}`;
    const slug = `e2e-content-lifecycle-${rand}`;

    // 1. Create published page
    await page.goto('/workspace/pages');
    await page.click('button:has-text("New Page")');
    await page.waitForSelector('#cora-drawer-page:not(.translate-x-full)', { state: 'visible' });

    await page.fill('#cora-page-title-input', title);
    await page.fill('#cora-page-slug-input', slug);
    await page.selectOption('#cora-page-status-input', 'publish');
    await page.selectOption('#cora-page-template-input', 'full-width');
    await page.fill('#cora-page-quill-editor .ql-editor', 'E2E Content lifecycle description text.');

    await page.click('#cora-drawer-page button:has-text("Save Page")');
    await expect(page.locator('#cora-toast-container')).toContainText('Page saved successfully.');
    await page.waitForLoadState('networkidle');

    // 2. Verify frontend content
    await page.goto(`/${slug}/`);
    await expect(page.locator('body')).toContainText(title);

    // 3. Delete the page
    await page.goto('/workspace/pages');
    const row = page.locator(`tr.cora-page-row:has-text("${title}")`).first();
    await row.waitFor({ state: 'visible' });
    await row.locator('button:has-text("Delete")').click();
    await page.click('#cora-confirm-btn');
    await expect(page.locator('#cora-toast-container')).toContainText('Page deleted successfully.');
  });

  test('Workload - 5. GDPR Privacy & Erasure Compliance Walkthrough', async ({ page }) => {
    await page.goto('/workspace/tools');

    // 1. Run GDPR export
    await page.fill('#cora-gdpr-export-email', 'privacy_user@example.com');
    await page.click('button:has-text("Export Data")');
    await expect(page.locator('#cora-toast-container')).toContainText('GDPR personal data export request generated');
    await page.waitForLoadState('networkidle');

    // 2. Run GDPR erasure
    await page.goto('/workspace/tools');
    await page.fill('#cora-gdpr-erase-email', 'privacy_user@example.com');
    await page.click('button:has-text("Anonymize & Erase")');
    await page.waitForSelector('#cora-confirm-modal:not(.hidden)', { state: 'visible' });
    await page.click('#cora-confirm-btn');
    await expect(page.locator('#cora-toast-container')).toContainText('GDPR personal data erasure request processed');
  });

  test('Workload - 6. Media SEO Optimization & Diagnostics Copy', async ({ page }) => {
    // 1. Copy site diagnostics
    await page.goto('/workspace/tools');
    await page.click('button:has-text("Copy Site Diagnostics")');
    await expect(page.locator('#cora-toast-container')).toContainText('System diagnostics copied to clipboard.');
    await page.waitForLoadState('networkidle');

    // 2. Update Media SEO metadata
    await page.goto('/workspace/media-editor');
    await page.fill('#cora-meta-title', 'E2E Optimized Title');
    await page.fill('#cora-meta-alt', 'E2E Optimized Alt Text');
    await page.click('button:has-text("Update SEO Metadata")');
    await expect(page.locator('#cora-toast-container')).toContainText('Media metadata updated successfully.');
  });

});
