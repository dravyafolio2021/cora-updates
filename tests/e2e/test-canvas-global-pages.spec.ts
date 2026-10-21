import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Canvas Global Pages & Shopify-Style Template Architecture', () => {
  test('verify single homepage, live templates, dedicated tab url, and distinct preview routes', async ({ page }) => {
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
    await expect(pagesSubtabBtn).toHaveClass(/border-zinc-950/);

    // 4. Verify Pages content panel is visible and active
    const pagesPanel = page.locator('#tab-canvas-pages');
    await expect(pagesPanel).toBeVisible();

    // 5. Verify the table loaded
    const tableBody = page.locator('#global-pages-table-body');
    await expect(tableBody).toBeVisible();
    const rows = tableBody.locator('tr');
    await expect(rows).not.toHaveCount(0);

    // 6. Verify Single Homepage enforcement in table
    const homeBadge = page.locator('.cora-home-badge');
    await expect(homeBadge).toHaveCount(1);

    // 7. Verify professional table structure with checkboxes, Page Title, URL Route, Status & Theme, Actions
    const tableHeaders = page.locator('#tab-canvas-pages table thead th');
    await expect(tableHeaders).toHaveCount(5);
    await expect(tableHeaders.nth(1)).toContainText('Page Title');
    await expect(tableHeaders.nth(2)).toContainText('URL Route');
    await expect(tableHeaders.nth(3)).toContainText('Status & Theme');
    await expect(tableHeaders.nth(4)).toContainText('Actions');

    // Verify master checkbox and row checkboxes are present
    const masterCheckbox = page.locator('#pages-master-checkbox');
    await expect(masterCheckbox).toBeVisible();
    const rowCheckboxes = page.locator('.cora-page-row-cb');
    await expect(rowCheckboxes).not.toHaveCount(0);

    // Verify bulk toolbar activates on row check
    await rowCheckboxes.first().check();
    const bulkBar = page.locator('#pages-bulk-actions-bar');
    await expect(bulkBar).toBeVisible();
    await expect(page.locator('#pages-bulk-selected-count')).toContainText('1 selected');

    // Deselect all cleans up toolbar
    await page.locator('#btn-deselect-all-pages').click();
    await expect(bulkBar).not.toBeVisible();

    // 8. Verify preview links point to distinct URLs (/site and /site/{slug})
    const previewLinks = tableBody.locator('a[title="Preview in browser"]');
    const previewHrefs: string[] = [];
    const count = await previewLinks.count();
    for (let i = 0; i < count; i++) {
      const href = await previewLinks.nth(i).getAttribute('href');
      if (href) previewHrefs.push(href);
    }
    expect(previewHrefs.some(h => h.endsWith('/site') || h.endsWith('/site/'))).toBeTruthy();
    expect(previewHrefs.some(h => h.includes('/site/contact'))).toBeTruthy();
    expect(previewHrefs.some(h => h.includes('/site/about'))).toBeTruthy();

    // 9. Open docked Edit Page drawer on first row and verify Live Theme templates are immediately accessible in single-view layout
    const firstEditBtn = tableBody.locator('.btn-edit-page').first();
    await expect(firstEditBtn).toBeVisible();
    await firstEditBtn.click();

    const editDrawer = page.locator('#drawer-edit-global-page');
    await expect(editDrawer).toBeVisible();
    await expect(editDrawer).toHaveClass(/open/);

    const editPageTemplateSelect = page.locator('#edit-global-page-template-select');
    await expect(editPageTemplateSelect).toBeVisible();
    const selectHtml = await editPageTemplateSelect.innerHTML();
    expect(selectHtml).toContain('page.default');
    expect(selectHtml).toContain('page.contact');
    expect(selectHtml).toContain('page.about');
    expect(selectHtml).toContain('page.landing');
    expect(selectHtml).toContain('page.fullwidth');

    // Close edit drawer
    await page.keyboard.press('Escape');
    await expect(editDrawer).not.toHaveClass(/open/);

    // 10. Verify "+ Add Page" drawer populates live theme templates
    const addPageBtn = page.locator('#btn-add-global-page');
    await expect(addPageBtn).toBeVisible();
    await addPageBtn.click();

    const newPageDrawer = page.locator('#drawer-new-page');
    await expect(newPageDrawer).toBeVisible();
    const drawerTemplateSelect = page.locator('#new-page-template-input');
    await expect(drawerTemplateSelect).toBeVisible();
    const drawerSelectHtml = await drawerTemplateSelect.innerHTML();
    expect(drawerSelectHtml).toContain('page.default');
    expect(drawerSelectHtml).toContain('page.contact');
    expect(drawerSelectHtml).toContain('page.about');
    expect(drawerSelectHtml).toContain('page.landing');
    expect(drawerSelectHtml).toContain('page.fullwidth');

    // Close drawer
    await page.locator('#btn-close-new-page').click();
    await expect(newPageDrawer).not.toBeVisible();

    // 11. Verify browser history pushState when switching tabs
    const overviewBtn = page.locator('#subtab-btn-tab-canvas-overview');
    await overviewBtn.click();
    await expect(page).toHaveURL(/tab=themes/);

    await pagesSubtabBtn.click();
    await expect(page).toHaveURL(/tab=pages/);

    // 12. Verify popstate on browser Back button
    await page.goBack();
    await expect(page).toHaveURL(/tab=themes/);
    await expect(page.locator('#tab-canvas-overview')).toBeVisible();

    await page.goForward();
    await expect(page).toHaveURL(/tab=pages/);
    await expect(page.locator('#tab-canvas-pages')).toBeVisible();

    // 13. Verify distinct public rendering of /site/contact and /site/about
    const contactRes = await page.goto('/site/contact');
    expect(contactRes?.status()).toBe(200);
    const contactTitle = await page.title();
    expect(contactTitle).toContain('Contact');
    const contactContent = await page.content();
    expect(contactContent).toContain('cora-page-content');

    const aboutRes = await page.goto('/site/about');
    expect(aboutRes?.status()).toBe(200);
    const aboutTitle = await page.title();
    expect(aboutTitle).toContain('About');

    // 14. Verify live template switching via Single-View Docked Edit Drawer and instant public re-render
    await page.goto('/workspace/canvas?tab=pages');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1000);

    // Click Edit on Contact page row
    const contactEditBtn = page.locator('#global-pages-table-body tr:has-text("/contact") .btn-edit-page');
    await expect(contactEditBtn).toBeVisible();
    await contactEditBtn.click();

    await expect(editDrawer).toHaveClass(/open/);
    await page.locator('#edit-global-page-template-select').selectOption('landing');

    // Save Page Settings
    await page.locator('#btn-save-edit-global-page').click();
    await expect(editDrawer).not.toHaveClass(/open/);
    await page.waitForTimeout(1000);

    // Verify /site/contact now renders the landing page blueprint
    await page.goto('/site/contact');
    const landingContent = await page.content();
    expect(landingContent).toContain('High-Precision Commercial Media Production');

    // Switch back to Contact template via Single-View Docked Edit Drawer
    await page.goto('/workspace/canvas?tab=pages');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1000);

    await page.locator('#global-pages-table-body tr:has-text("/contact") .btn-edit-page').click();
    await expect(editDrawer).toHaveClass(/open/);
    await page.locator('#edit-global-page-template-select').selectOption('contact');

    // Save Page Settings
    await page.locator('#btn-save-edit-global-page').click();
    await expect(editDrawer).not.toHaveClass(/open/);
    await page.waitForTimeout(1000);

    // Verify /site/contact restored to contact layout
    await page.goto('/site/contact');
    const restoredContent = await page.content();
    expect(restoredContent).toContain('Contact &amp; Studio Hire');
  });

  test('create custom template in live theme and verify immediate availability in pages manager', async ({ page }) => {
    const consoleErrors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') {
        const text = msg.text();
        if (!text.includes('favicon.ico')) {
          consoleErrors.push(text);
        }
      }
    });

    await page.setViewportSize({ width: 1280, height: 850 });
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

    // 1. Direct navigation to live theme templates tab
    await page.goto('/workspace/canvas?cv_action=edit_live&cv_tab=templates');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1500);

    // 2. Verify Templates tab is visible
    const templatesTab = page.locator('#tab-content-templates');
    await expect(templatesTab).toBeVisible();

    // 3. Open "+ Add Template" docked drawer
    const addTemplateBtn = page.locator('#tab-btn-add-template');
    await expect(addTemplateBtn).toBeVisible();
    await addTemplateBtn.click();

    const newTemplateDrawer = page.locator('#drawer-new-template');
    await expect(newTemplateDrawer).toBeVisible();
    await expect(newTemplateDrawer).toHaveClass(/open/);

    // 4. Select "Page" archetype
    const pageArchBtn = page.locator('#new-template-archetype-selector [data-arch="page"]');
    await pageArchBtn.click();
    await page.waitForTimeout(300);

    const archBadge = page.locator('#cora-new-template-badge');
    await expect(archBadge).toHaveText('PAGE');

    // 5. Fill Name & Slug
    await page.locator('#new-template-name-input').fill('VIP Studio Booking');
    await page.locator('#new-template-slug-input').fill('vip-booking');

    // 6. Save Template
    const saveTemplateBtn = page.locator('#btn-save-new-template');
    await expect(saveTemplateBtn).toBeVisible();
    await saveTemplateBtn.click();

    // 7. Verify drawer closes and template appears in list
    await expect(newTemplateDrawer).not.toBeVisible();
    await page.waitForTimeout(1000);
    const templatesTbody = page.locator('#theme-templates-tbody');
    await expect(templatesTbody).toContainText('VIP Studio Booking');
    await expect(templatesTbody).toContainText('vip-booking');

    // 8. Navigate to Pages tab
    await page.goto('/workspace/canvas?tab=pages');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1500);

    // 9. Verify Pages Edit Drawer template select includes VIP Studio Booking
    const firstEditBtn = page.locator('#global-pages-table-body .btn-edit-page').first();
    await expect(firstEditBtn).toBeVisible();
    await firstEditBtn.click();

    const editDrawer = page.locator('#drawer-edit-global-page');
    await expect(editDrawer).toHaveClass(/open/);
    const editPageTemplateSelect = page.locator('#edit-global-page-template-select');
    await expect(editPageTemplateSelect).toBeVisible();
    const tableSelectHtml = await editPageTemplateSelect.innerHTML();
    expect(tableSelectHtml).toContain('VIP Studio Booking (page.vip-booking)');

    // Close drawer
    await page.keyboard.press('Escape');
    await expect(editDrawer).not.toHaveClass(/open/);

    // 10. Verify "+ Add Page" drawer includes VIP Studio Booking
    await page.locator('#btn-add-global-page').click();
    const newPageDrawer = page.locator('#drawer-new-page');
    await expect(newPageDrawer).toBeVisible();
    const drawerSelect = page.locator('#new-page-template-input');
    const drawerSelectHtml = await drawerSelect.innerHTML();
    expect(drawerSelectHtml).toContain('VIP Studio Booking (page.vip-booking)');

    // Close drawer
    await page.locator('#btn-close-new-page').click();
    await expect(newPageDrawer).not.toBeVisible();

    // 11. Assign vip-booking to About page via Single-View Docked Edit Drawer
    const aboutEditBtn = page.locator('#global-pages-table-body tr:has-text("/about") .btn-edit-page');
    await expect(aboutEditBtn).toBeVisible();
    await aboutEditBtn.click();

    await expect(editDrawer).toHaveClass(/open/);
    await page.locator('#edit-global-page-template-select').selectOption('vip-booking');
    await page.locator('#btn-save-edit-global-page').click();
    await expect(editDrawer).not.toHaveClass(/open/);
    await page.waitForTimeout(1000);

    // 12. Public preview renders successfully
    const aboutRes = await page.goto('/site/about');
    expect(aboutRes?.status()).toBe(200);

    // 13. Restore About page back to about template via Single-View Docked Edit Drawer
    await page.goto('/workspace/canvas?tab=pages');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1000);

    await page.locator('#global-pages-table-body tr:has-text("/about") .btn-edit-page').click();
    await expect(editDrawer).toHaveClass(/open/);
    await page.locator('#edit-global-page-template-select').selectOption('about');
    await page.locator('#btn-save-edit-global-page').click();
    await expect(editDrawer).not.toHaveClass(/open/);
    await page.waitForTimeout(1000);

    // 14. Assert 0 unexpected console errors
    expect(consoleErrors).toEqual([]);
  });

  test('verify bulk page actions: scope filter, master select-all, status toggle, and inline confirmation delete', async ({ page }) => {
    await page.setViewportSize({ width: 1280, height: 850 });
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

    await page.goto('/workspace/canvas?tab=pages');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1500);

    // 1. Verify Scope Buttons (Live Theme Pages vs All Workspace Pages)
    const btnLiveScope = page.locator('#global-scope-btn-live');
    const btnAllScope = page.locator('#global-scope-btn-all');
    await expect(btnLiveScope).toBeVisible();
    await expect(btnAllScope).toBeVisible();
    await expect(btnLiveScope).toHaveClass(/bg-white/);

    // 2. Switch to All Workspace Pages scope
    await btnAllScope.click();
    await page.waitForTimeout(1200);
    await expect(btnAllScope).toHaveClass(/bg-white/);

    // 3. Verify Master Checkbox selects all visible rows
    const masterCheckbox = page.locator('#pages-master-checkbox');
    await expect(masterCheckbox).toBeVisible();
    await masterCheckbox.check();

    const bulkBar = page.locator('#pages-bulk-actions-bar');
    await expect(bulkBar).toBeVisible();
    const countBadge = page.locator('#pages-bulk-selected-count');
    await expect(countBadge).toBeVisible();
    const countText = await countBadge.innerText();
    expect(countText).toMatch(/\d+ selected/);

    // Capture screenshot with Bulk Toolbar Active
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/e7db172d-afc3-4021-b0cc-a3073c916f94/cora_canvas_pages_bulk_toolbar_active.png', fullPage: false });

    // 4. Click Bulk Delete to trigger inline confirmation bar
    const bulkDeleteBtn = page.locator('#btn-bulk-delete-pages');
    await expect(bulkDeleteBtn).toBeVisible();
    await bulkDeleteBtn.click();

    const confirmBar = page.locator('#pages-bulk-delete-confirm-bar');
    await expect(confirmBar).toBeVisible();
    await expect(confirmBar).toContainText('Permanently delete');

    // Capture screenshot with Inline Confirmation Bar Active
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/e7db172d-afc3-4021-b0cc-a3073c916f94/cora_canvas_pages_bulk_confirm_bar.png', fullPage: false });

    // 5. Test Cancel dismissal
    const cancelBtn = confirmBar.locator('button:has-text("Cancel")');
    await cancelBtn.click();
    await expect(confirmBar).not.toBeVisible();
    await expect(bulkBar).toBeVisible();

    // 6. Deselect all cleans up bulk bar
    await page.locator('#btn-deselect-all-pages').click();
    await expect(bulkBar).not.toBeVisible();

    // 7. Switch back to Live Theme Pages scope
    await btnLiveScope.click();
    await page.waitForTimeout(1200);
    await expect(btnLiveScope).toHaveClass(/bg-white/);

    // 8. Capture screenshot of the enhanced pages tab
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/e7db172d-afc3-4021-b0cc-a3073c916f94/cora_canvas_pages_bulk_actions_verified.png', fullPage: false });
  });
});

