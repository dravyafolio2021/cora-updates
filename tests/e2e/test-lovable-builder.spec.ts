import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Lovable Page Builder & Routing Integration E2E Tests', () => {

  test('Should successfully provision a new Lovable draft theme using the wizard', async ({ page }) => {
    // 1. Login and go to Canvas Hub
    await login(page);
    await page.goto('/workspace/canvas');
    await page.waitForSelector('#canvas-level-1', { state: 'visible' });

    // 2. Open Add Theme Wizard
    await page.click('button:has-text("Add Theme")');
    await page.waitForSelector('#atw', { state: 'visible' });

    // Step 1: Builder Selection
    // Select Lovable card to enable next button
    await page.click('#wizard-card-lovable');
    await page.click('#wiz-next-btn');

    // Wait for Step 2b (Lovable Setup) to be visible
    await page.waitForSelector('#wizard-step-2b', { state: 'visible' });

    // Fill Step 2b details
    await page.fill('#wiz-lovable-url', 'https://lovable.dev/projects/e2e-test-project');
    await page.fill('#wiz-github-repo-lov', 'https://github.com/cora-platform/e2e-lovable-theme');
    await page.fill('#wiz-github-token-lov', 'ghp_e2e_mock_token_123456');
    await page.fill('#wiz-github-branch-lov', 'main');

    // Click Next to go to Step 3 (Summary & Name)
    await page.click('#wiz-next-btn');
    await page.waitForSelector('#wizard-step-3', { state: 'visible' });

    // Fill Theme Name in Step 3
    const testThemeName = `E2E Lovable Theme ${Math.floor(Math.random() * 10000)}`;
    await page.fill('#wiz-theme-name', testThemeName);

    // Verify summary values
    await expect(page.locator('#wiz-summary-builder')).toContainText('Lovable Studio');
    await expect(page.locator('#wiz-summary-source')).toContainText('https://lovable.dev/projects/e2e-test-project');

    // Click Save Theme
    await page.click('#wiz-next-btn');

    // The wizard calls closeAddThemeWizard and reloads the page
    await page.waitForSelector('#atw', { state: 'hidden' });
    await page.waitForLoadState('networkidle');

    // Confirm that the newly created theme is listed in the Canvas page
    await page.waitForSelector('#canvas-level-1', { state: 'visible' });
    await expect(page.locator('body')).toContainText(testThemeName);
  });

  // Clean up any generated test themes
  test.afterAll(async ({ browser }) => {
    const context = await browser.newContext();
    const page = await context.newPage();
    await login(page);
    await page.goto('/workspace/canvas');
    await page.waitForSelector('#canvas-level-1', { state: 'visible' });

    let cleaned = 0;
    for (let attempt = 0; attempt < 10; attempt++) {
      const e2eThemeBtn = await page.$('#draft-themes-library-card [data-draft-theme-id]');
      if (!e2eThemeBtn) break;

      const themeId = await e2eThemeBtn.getAttribute('data-draft-theme-id');
      const themeName = await e2eThemeBtn.$eval('h4', (el: Element) => el.textContent?.trim() || '').catch(() => '');
      if (!themeName.startsWith('E2E Lovable Theme')) break;

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
      await page.reload();
    }
    if (cleaned > 0) console.log(`Lovable clean up: removed ${cleaned} test theme(s).`);
    await context.close();
  });

});
