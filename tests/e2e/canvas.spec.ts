import { test, expect, request } from '@playwright/test';
import { login } from './helpers';

test.describe('Canvas Front-End Management System E2E Tests', () => {
  let consoleErrors: string[] = [];

  test.beforeEach(async ({ page }) => {
    consoleErrors = [];
    page.on('console', msg => {
      console.log(`PAGE LOG [${msg.type()}]: ${msg.text()}`);
      if (msg.type() === 'error') {
        const text = msg.text();
        if (!text.includes('404') && !text.includes('unsplash') && !text.includes('favicon')) {
          consoleErrors.push(text);
        }
      }
    });
    page.on('pageerror', err => {
      console.log(`PAGE EXCEPTION: ${err.message}`);
      // Filter out native Elementor exceptions that are unrelated to Cora's codebase
      if (!err.message.includes('components') && !err.message.includes('elementor') && !err.message.includes('Mui') && !err.message.includes('404')) {
        consoleErrors.push(err.message);
      }
    });
    page.on('response', response => {
      if (response.status() === 404) {
        console.log(`404 RESPONSE: ${response.url()}`);
      }
    });
    page.on('requestfailed', request => {
      console.log(`REQUEST FAILED: ${request.url()} - ${request.failure()?.errorText}`);
    });
  });

  test('Should navigate the Level 1/2/3 Canvas Hub layout, perform operations, and test permissions', async ({ page }) => {
    // 1. Login and go to Canvas Hub
    await login(page);
    await page.goto('/workspace/canvas');

    // Wait for Level 1 Canvas Hub view to load
    await page.waitForSelector('#canvas-level-1', { state: 'visible' });


    // Assert Level 1 headers
    await expect(page.locator('#canvas-level-1 h1')).toContainText('Canvas');
    await expect(page.locator('#canvas-level-1')).toContainText('Active Theme');

    // 2. Open New Theme setup drawer (Level 1)
    await page.click('button:has-text("+ New Theme")');
    await page.waitForSelector('#drawer-new-theme:not(.opacity-0)', { state: 'visible' });
    
    // Fill theme name and close drawer
    const newThemeName = `E2E Catalog Theme ${Math.floor(Math.random() * 1000)}`;
    await page.fill('#new-theme-name-input', newThemeName);
    await page.click('#drawer-new-theme-card button:has-text("Cancel")');
    await page.waitForSelector('#drawer-new-theme.opacity-0');

    // 3. Edit Active Theme to enter Level 2 Theme Dashboard
    await page.click('button:has-text("Edit Theme")');
    await page.waitForSelector('#canvas-level-2', { state: 'visible' });
    await page.waitForSelector('#canvas-level-1', { state: 'hidden' });

    // Verify Level 2 headers
    await expect(page.locator('#dashboard-theme-name')).toContainText('Cora Default Theme');

    // 4. Open New Page setup drawer (Level 2)
    await page.click('#tab-content-pages button:has-text("+ New Page")');
    await page.waitForSelector('#drawer-new-page:not(.opacity-0)', { state: 'visible' });

    const newPageTitle = `E2E Penthouse ${Math.floor(Math.random() * 1000)}`;
    const newPageSlug = `e2e-penthouse-${Math.floor(Math.random() * 1000)}`;
    await page.fill('#new-page-title-input', newPageTitle);
    await page.fill('#new-page-slug-input', newPageSlug);
    await page.click('#drawer-new-page-card button:has-text("Cancel")');
    await page.waitForSelector('#drawer-new-page.opacity-0');

    // 5. Open SEO & Metadata Settings Drawer (Level 2)
    // Find the first SEO checkmark/warning button in the page table (represented by td.cursor-pointer)
    await page.click('#pages-table-body tr:first-child td.cursor-pointer');
    await page.waitForSelector('#drawer-page-seo:not(.opacity-0)', { state: 'visible' });

    // Fill SEO metadata
    await page.fill('#seo-title-input', 'Gurgaon DLF Luxury Villas for Sale');
    await page.fill('#seo-desc-input', 'Best rates on super premium villas located at DLF Phase 5 Gurgaon.');
    await page.click('#drawer-page-seo-card button:has-text("Save SEO")');
    
    // Expect custom toast validation
    const toast = page.locator('#cora-toast-container');
    await expect(toast).toContainText('SEO parameters synchronized successfully');
    await page.waitForSelector('#drawer-page-seo.opacity-0');

    // 6. Enter Level 3 Elementor Iframe Page Editor Wrapper
    await page.click('#pages-table-body tr:first-child button:has-text("Edit")');
    await page.waitForSelector('#canvas-level-3', { state: 'visible' });

    // Assert custom Level 3 top-bar contents
    await expect(page.locator('#editor-theme-title')).toContainText('Cora Default Theme');
    
    // Close editor and return to Level 2
    const editorFrame = page.frameLocator('#elementor-editor-iframe');
    
    // Wait for the iframe loader to disappear to ensure iframe has loaded
    await page.waitForSelector('#iframe-loader', { state: 'hidden' });

    await editorFrame.locator('button:has-text("Theme Dashboard")').click();
    await page.waitForSelector('#canvas-level-2', { state: 'visible' });
    await page.waitForSelector('#canvas-level-3', { state: 'hidden' });

    // 7. Verify back navigation to Level 1
    await page.click('button[onclick="backToCanvasHub()"]');
    await page.waitForSelector('#canvas-level-1', { state: 'visible' });
    await page.waitForSelector('#canvas-level-2', { state: 'hidden' });

    // Ensure no unhandled exceptions occurred
    expect(consoleErrors.length).toBe(0);
  });

  // ── After all tests, clean up E2E-generated themes so they don't accumulate ──
  test.afterAll(async ({ browser }) => {
    const context = await browser.newContext();
    const page = await context.newPage();
    await login(page);
    await page.goto('/workspace/canvas');
    await page.waitForSelector('#canvas-level-1', { state: 'visible', timeout: 15000 });

    // Find and delete any theme whose name starts with 'E2E'
    let cleaned = 0;
    for (let attempt = 0; attempt < 20; attempt++) {
      const e2eThemeBtn = await page.$('#draft-themes-library-card [data-draft-theme-id]');
      if (!e2eThemeBtn) break;

      // Get theme ID
      const themeId = await e2eThemeBtn.getAttribute('data-draft-theme-id');
      const themeName = await e2eThemeBtn.$eval('h4', (el: Element) => el.textContent?.trim() || '').catch(() => '');
      if (!themeName.startsWith('E2E') && !themeName.startsWith('e2e')) break;

      // Use the delete AJAX endpoint directly
      const nonce = await page.evaluate(() => (window as any).coraREData?.ajaxNonce || '');
      const ajaxUrl = await page.evaluate(() => (window as any).coraREData?.ajaxUrl || '/wp-admin/admin-ajax.php');
      if (themeId && nonce) {
        await page.evaluate(async ({ ajaxUrl, nonce, themeId }) => {
          const fd = new FormData();
          fd.append('action', 'cora_ajax_delete_theme');
          fd.append('theme_id', themeId);
          fd.append('nonce', nonce);
          await fetch(ajaxUrl, { method: 'POST', body: fd });
        }, { ajaxUrl, nonce, themeId });
        cleaned++;
      }
      await page.waitForTimeout(300);
    }
    if (cleaned > 0) console.log(`E2E cleanup: removed ${cleaned} test theme(s).`);
    await context.close();
  });

  test('Should import valid Elementor kits and reject invalid uploads with toasts', async ({ page }) => {
    const fs = require('fs');
    const { execSync } = require('child_process');

    // Setup dummy good kit
    if (!fs.existsSync('/tmp/good-kit')) fs.mkdirSync('/tmp/good-kit');
    fs.writeFileSync('/tmp/good-kit/manifest.json', JSON.stringify({
      manifest: {
        templates: [
          { title: 'E2E Imported Page', file: 'imported-page.json', type: 'page' }
        ]
      }
    }));
    fs.writeFileSync('/tmp/good-kit/imported-page.json', JSON.stringify({
      title: 'E2E Imported Page',
      type: 'page',
      content: [
        {
          id: 'sec1',
          elType: 'section',
          elements: [
            {
              id: 'col1',
              elType: 'column',
              elements: [
                {
                  id: 'widget1',
                  elType: 'widget',
                  widgetType: 'heading',
                  settings: { title: 'Welcome to Imported Workspace' }
                }
              ]
            }
          ]
        }
      ],
      page_settings: {}
    }));
    execSync('zip -j /tmp/good-kit.zip /tmp/good-kit/manifest.json /tmp/good-kit/imported-page.json');

    // Setup dummy bad kit
    if (!fs.existsSync('/tmp/bad-kit')) fs.mkdirSync('/tmp/bad-kit');
    fs.writeFileSync('/tmp/bad-kit/dummy.txt', 'This is some text files, not Elementor data');
    execSync('zip -j /tmp/bad-kit.zip /tmp/bad-kit/dummy.txt');

    // Login and go to Canvas Hub
    await login(page);
    await page.goto('/workspace/canvas');
    await page.waitForSelector('#canvas-level-1', { state: 'visible' });

    // 1. Test Negative Case: Upload invalid kit
    await page.click('button:has-text("Import Kit")');
    await page.waitForSelector('#drawer-import-kit:not(.opacity-0)', { state: 'visible' });

    await page.fill('#import-kit-name-input', 'Bad Kit Workspace');
    await page.setInputFiles('#import-kit-file-input', '/tmp/bad-kit.zip');
    await page.click('#drawer-import-kit-card button:has-text("Import Kit")');

    // Confirm error toast
    const toast = page.locator('#cora-toast-container');
    await expect(toast).toContainText('Invalid Kit');

    // 2. Test Positive Case: Upload valid kit
    await page.fill('#import-kit-name-input', 'E2E Imported Theme Kit');
    await page.setInputFiles('#import-kit-file-input', '/tmp/good-kit.zip');
    await page.click('#drawer-import-kit-card button:has-text("Import Kit")');

    // Confirm success reload
    await page.waitForSelector('#drawer-import-kit.opacity-0');
    await page.waitForSelector('#canvas-level-1', { state: 'visible' });
    await expect(page.locator('body')).toContainText('E2E Imported Theme Kit');

    // Cleanup temp files
    execSync('rm -rf /tmp/good-kit /tmp/bad-kit /tmp/good-kit.zip /tmp/bad-kit.zip');
  });
});
