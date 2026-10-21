import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Canvas AI Website & URL Importer: Dual-Mode Architecture (Rule 15 SOP)', () => {

  test('Mode 1: Single Page Fast URL Import from Pages toolbar', async ({ page }) => {
    // 1. Authenticate with Studio Owner credentials
    await page.setViewportSize({ width: 1280, height: 850 });
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

    // 2. Direct navigation to dedicated pages sub-tab URL
    await page.goto('/workspace/canvas?tab=pages');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1000);

    // 3. Verify Pages sub-tab button is active
    const pagesSubtabBtn = page.locator('#subtab-btn-tab-canvas-pages');
    await expect(pagesSubtabBtn).toBeVisible();

    // 4. Verify Prominent "Import via URL" button on Pages toolbar
    const importUrlBtn = page.locator('#btn-import-page-url');
    await expect(importUrlBtn).toBeVisible();
    await expect(importUrlBtn).toContainText('Import via URL');

    // 5. Click "Import via URL" to open Rule 15 docked drawer in Single Page mode
    await importUrlBtn.click();
    await page.waitForTimeout(400);

    const importDrawer = page.locator('#drawer-url-website-import');
    await expect(importDrawer).toBeVisible();
    await expect(importDrawer).toHaveClass(/open/);
    await expect(importDrawer).not.toHaveClass(/collapsed/);

    // 6. Verify Rule 15 Docked Architecture (No dark overlay backdrop, docked below header)
    const overlayBackdrop = page.locator('.fixed.inset-0.bg-zinc-900\\/40');
    if (await overlayBackdrop.count() > 0) {
      const isVisible = await overlayBackdrop.first().isVisible();
      expect(isVisible).toBeFalsy();
    }

    // 7. Verify Drawer Header & Single Page Scope is Active by default
    const drawerTitle = page.locator('#url-website-import-title');
    await expect(drawerTitle).toContainText('Import Single Page via URL');

    const singleScopeBtn = page.locator('#url-scope-btn-single');
    const fullScopeBtn = page.locator('#url-scope-btn-full');
    await expect(singleScopeBtn).toBeVisible();
    await expect(fullScopeBtn).toBeVisible();
    await expect(singleScopeBtn).toHaveClass(/bg-white/);

    // 8. Verify Destination Defaults to "Add to Website Pages"
    const destRadioPages = page.locator('#url-dest-radio-pages');
    await expect(destRadioPages).toBeChecked();

    // 9. Inspect a single target URL
    const urlInput = page.locator('#url-website-input');
    await expect(urlInput).toBeVisible();
    await urlInput.fill('http://cora.local/workspace/login');

    const scanBtn = page.locator('#btn-scan-website-url');
    await scanBtn.click();

    // 10. Verify Single Page Inspection Card appears (not multi-page checklist)
    const singleCard = page.locator('#url-step-single-page-card');
    await expect(singleCard).toBeVisible({ timeout: 15000 });

    const pageTitleInput = page.locator('#url-single-page-title');
    const pageSlugInput = page.locator('#url-single-page-slug');
    await expect(pageTitleInput).toBeVisible();
    await expect(pageSlugInput).toBeVisible();

    // Customize Title
    await pageTitleInput.fill('Single Fast Import Landing');
    await pageSlugInput.fill('single-fast-landing');

    // Verify Action button label
    const primaryImportBtn = page.locator('#btn-start-url-import');
    await expect(primaryImportBtn).toContainText('Import Single Page into Canvas');

    // Take screenshot of Single Page Inspection card
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/e7db172d-afc3-4021-b0cc-a3073c916f94/cora_canvas_single_page_inspection.png', fullPage: false });

    // 11. Execute Single Page Import
    await primaryImportBtn.click();

    // 12. Verify Success card
    const successCard = page.locator('#url-step-success-card');
    await expect(successCard).toBeVisible({ timeout: 20000 });
    await expect(successCard).toContainText('Page Imported Successfully!');

    // 13. Click "View in Website Pages" to close drawer and view table
    const viewPagesBtn = page.locator('#url-step-success-card button:has-text("View in Website Pages")');
    await viewPagesBtn.click();
    await page.waitForTimeout(500);

    await expect(importDrawer).toHaveClass(/collapsed/);
    const tableBody = page.locator('#global-pages-table-body');
    await expect(tableBody).toBeVisible();
    await expect(tableBody).toContainText('Single Fast Import Landing');

    // Take screenshot of updated table
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/e7db172d-afc3-4021-b0cc-a3073c916f94/cora_canvas_single_page_imported.png', fullPage: false });
  });

  test('Mode 2: Full Website URL Migration from Migration sub-tab with selective confirmation checklist', async ({ page }) => {
    // 1. Authenticate with Studio Owner credentials
    await page.setViewportSize({ width: 1280, height: 850 });
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

    // 2. Direct navigation to Migration sub-tab
    await page.goto('/workspace/canvas?tab=migration');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1000);

    // 3. Verify Migration sub-tab is active and visible
    const migrationTab = page.locator('#tab-canvas-migration');
    await expect(migrationTab).toBeVisible();

    // 4. Verify Both URL Importer Cards exist on Migration tab
    const fullSiteCardBtn = page.locator('#btn-migration-migrate-full-site');
    const singlePageCardBtn = page.locator('#btn-migration-import-single-page');
    await expect(fullSiteCardBtn).toBeVisible();
    await expect(singlePageCardBtn).toBeVisible();

    // Take screenshot of Migration sub-tab cards
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/e7db172d-afc3-4021-b0cc-a3073c916f94/cora_canvas_migration_tab_cards.png', fullPage: false });

    // 5. Click "Migrate Entire Site" to open Drawer in Full Site mode
    await fullSiteCardBtn.click();
    await page.waitForTimeout(400);

    const importDrawer = page.locator('#drawer-url-website-import');
    await expect(importDrawer).toBeVisible();
    await expect(importDrawer).toHaveClass(/open/);

    // 6. Verify Scope is "Full Website (All Pages)" by default
    const fullScopeBtn = page.locator('#url-scope-btn-full');
    await expect(fullScopeBtn).toHaveClass(/bg-white/);

    const drawerTitle = page.locator('#url-website-import-title');
    await expect(drawerTitle).toContainText('Migrate Entire Website via URL');

    // 7. Verify Destination defaults to Draft Theme for full site migration
    const destRadioTheme = page.locator('#url-dest-radio-theme');
    await expect(destRadioTheme).toBeChecked();

    // 8. Crawl target website
    const urlInput = page.locator('#url-website-input');
    await urlInput.fill('http://cora.local/workspace/login');

    const scanBtn = page.locator('#btn-scan-website-url');
    await scanBtn.click();

    // 9. Verify Multi-Page Scanned Checklist appears
    const scannedCard = page.locator('#url-step-scanned-card');
    await expect(scannedCard).toBeVisible({ timeout: 15000 });

    const pageChecklist = page.locator('#url-scanned-pages-checklist');
    await expect(pageChecklist).toBeVisible();
    const discoveredPages = pageChecklist.locator('label');
    await expect(discoveredPages).not.toHaveCount(0);

    // 10. Test Select All / Deselect All interactive toggle
    const toggleSelectAllBtn = page.locator('#url-btn-toggle-select-all');
    await expect(toggleSelectAllBtn).toBeVisible();
    await expect(toggleSelectAllBtn).toContainText('Deselect All');

    // Deselect all
    await toggleSelectAllBtn.click();
    await page.waitForTimeout(200);
    await expect(toggleSelectAllBtn).toContainText('Select All');

    // Primary button should be disabled when 0 pages selected
    const primaryImportBtn = page.locator('#btn-start-url-import');
    await expect(primaryImportBtn).toBeDisabled();

    // Select all back
    await toggleSelectAllBtn.click();
    await page.waitForTimeout(200);
    await expect(toggleSelectAllBtn).toContainText('Deselect All');
    await expect(primaryImportBtn).not.toBeDisabled();
    await expect(primaryImportBtn).toContainText(/Import \d+ Selected Page/);

    // Take screenshot of multi-page selective checklist
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/e7db172d-afc3-4021-b0cc-a3073c916f94/cora_canvas_full_site_page_selection.png', fullPage: false });

    // 11. Execute Batch Migration
    await primaryImportBtn.click();

    // 12. Verify Success card
    const successCard = page.locator('#url-step-success-card');
    await expect(successCard).toBeVisible({ timeout: 20000 });
    await expect(successCard).toContainText('Import Complete!');

    // Take screenshot of Full Site Migration success
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/e7db172d-afc3-4021-b0cc-a3073c916f94/cora_canvas_full_site_migration_success.png', fullPage: false });
  });

  test('Mode 3: Interactive Scope Switcher inside Drawer', async ({ page }) => {
    // 1. Authenticate with Studio Owner credentials
    await page.setViewportSize({ width: 1280, height: 850 });
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

    await page.goto('/workspace/canvas?tab=pages');
    await page.waitForLoadState('domcontentloaded');

    // Open drawer
    await page.locator('#btn-import-page-url').click();
    await page.waitForTimeout(300);

    const importDrawer = page.locator('#drawer-url-website-import');
    await expect(importDrawer).toBeVisible();

    const singleScopeBtn = page.locator('#url-scope-btn-single');
    const fullScopeBtn = page.locator('#url-scope-btn-full');
    const drawerTitle = page.locator('#url-website-import-title');

    // Initially Single Page
    await expect(singleScopeBtn).toHaveClass(/bg-white/);
    await expect(drawerTitle).toContainText('Import Single Page via URL');

    // Click Full Website
    await fullScopeBtn.click();
    await page.waitForTimeout(200);
    await expect(fullScopeBtn).toHaveClass(/bg-white/);
    await expect(drawerTitle).toContainText('Migrate Entire Website via URL');

    // Click Single Page back
    await singleScopeBtn.click();
    await page.waitForTimeout(200);
    await expect(singleScopeBtn).toHaveClass(/bg-white/);
    await expect(drawerTitle).toContainText('Import Single Page via URL');

    // Close with Escape
    await page.keyboard.press('Escape');
    await page.waitForTimeout(300);
    await expect(importDrawer).toHaveClass(/collapsed/);
  });

});
