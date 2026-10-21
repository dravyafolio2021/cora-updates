import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Cora Canvas Next.js Static Export Workspace Deployment (Zero Vercel)', () => {
  test('deploy Next.js static build theme directly to workspace, verify multi-page sync and live routes', async ({ page }) => {
    test.setTimeout(180000);

    // 1. Authenticate with Studio Owner credentials
    await page.setViewportSize({ width: 1280, height: 850 });
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

    // 2. Navigate to Canvas Overview
    await page.goto('/workspace/canvas');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1000);

    // 3. Open "+ Add Theme" Wizard
    await page.evaluate(() => {
      if (typeof window.openAddThemeWizard === 'function') {
        window.openAddThemeWizard();
      }
    });

    const wizardContainer = page.locator('#atw');
    await expect(wizardContainer).toBeVisible({ timeout: 10000 });

    // 4. Select "Next.js Static Export (GitHub & Workspace Hosted)" card in Step 1
    const nextjsCard = page.locator('#wizard-card-nextjs');
    await expect(nextjsCard).toBeVisible();
    await expect(nextjsCard).toContainText('Next.js');
    await expect(nextjsCard).toContainText('Workspace Hosted');

    await nextjsCard.click();
    await page.waitForTimeout(300);

    // Proceed to Step 2
    const nextBtn = page.locator('#wiz-next-btn');
    await expect(nextBtn).toBeVisible();
    await nextBtn.click();

    // 5. In Step 2, verify 3-Tab Segmented Selector
    const step2Nextjs = page.locator('#wizard-step-2-nextjs');
    await expect(step2Nextjs).toBeVisible();

    // Switch to Starter Templates Tab and select "SaaS Studio"
    const starterTabBtn = page.locator('#wiz-nextjs-subtab-templates');
    await expect(starterTabBtn).toBeVisible();
    await starterTabBtn.click();
    await page.waitForTimeout(300);

    const saasTemplateCard = page.locator('#wiz-nextjs-tmpl-saas_studio');
    await expect(saasTemplateCard).toBeVisible();
    await saasTemplateCard.click();
    await page.waitForTimeout(300);

    // 6. Click "Deploy Next.js Theme ⚡"
    const deployNextjsBtn = page.locator('#btn-deploy-nextjs-theme');
    await expect(deployNextjsBtn).toBeVisible();
    await deployNextjsBtn.click();

    // 7. Wait for deployment completion & page redirect / toast
    await page.waitForTimeout(3500);

    // 8. Navigate to Canvas Pages Manager
    await page.goto('/workspace/canvas?tab=pages');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(2000);

    // Verify Pages Table renders discovered Next.js pages
    const tableBody = page.locator('#global-pages-table-body');
    await expect(tableBody).toBeVisible();

    // Verify Homepage is present
    const homeRow = tableBody.locator('tr:has-text("Home"), tr:has-text("home")').first();
    await expect(homeRow).toBeVisible();

    // Verify Technology Tags include Next.js / React
    await expect(tableBody).toContainText('Next.js');

    // 9. Execute Live Route Verification on Cora Workspace (Zero Vercel)
    // A) Verify Homepage: /site/
    const homeResponse = await page.goto('/site/', { waitUntil: 'domcontentloaded' });
    expect(homeResponse?.status()).toBe(200);

    // Verify live page has title and rendered content
    const pageTitle = await page.title();
    expect(pageTitle.length).toBeGreaterThan(0);

    // Verify Next.js / Tailwind interactive elements
    const bodyContent = await page.locator('body').innerText();
    expect(bodyContent.length).toBeGreaterThan(50);

    // Take screenshot of live deployed Next.js site
    await page.screenshot({ path: 'test-results/nextjs-live-workspace-deployed.png' });
    console.log('[E2E TEST] Captured live Next.js screenshot: test-results/nextjs-live-workspace-deployed.png');

    // B) Verify Subpage Route: /site/features or /site/pricing
    const subpageResponse = await page.goto('/site/features', { waitUntil: 'domcontentloaded' });
    expect(subpageResponse?.status()).toBe(200);

    const subpageBody = await page.locator('body').innerText();
    expect(subpageBody.length).toBeGreaterThan(50);
    console.log('[E2E TEST] Verified subpage /site/features returned 200 OK with content.');
  });

  test('verify GitHub repository route scanning and global PAT persistence without unauthorized capability error', async ({ page }) => {
    test.setTimeout(120000);

    // 1. Authenticate with Studio Owner credentials
    await page.setViewportSize({ width: 1280, height: 850 });
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

    // 2. Navigate to Canvas Overview
    await page.goto('/workspace/canvas');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1000);

    // 3. Open Add Theme Wizard
    await page.evaluate(() => {
      if (typeof window.openAddThemeWizard === 'function') {
        window.openAddThemeWizard();
      }
    });

    const wizardContainer = page.locator('#atw');
    await expect(wizardContainer).toBeVisible({ timeout: 10000 });

    // 4. Select Next.js Card in Step 1
    const nextjsCard = page.locator('#wizard-card-nextjs');
    await expect(nextjsCard).toBeVisible();
    await nextjsCard.click();
    await page.waitForTimeout(300);

    // Proceed to Step 2
    const nextBtn = page.locator('#wiz-next-btn');
    await expect(nextBtn).toBeVisible();
    await nextBtn.click();

    // 5. In Step 2, ensure GitHub Sync Tab is active
    const step2Nextjs = page.locator('#wizard-step-2-nextjs');
    await expect(step2Nextjs).toBeVisible();

    const githubSubtab = page.locator('#wiz-nextjs-subtab-github');
    await expect(githubSubtab).toBeVisible();
    await githubSubtab.click();
    await page.waitForTimeout(300);

    // 6. Enter repository from user request: https://github.com/dravyafolio2021/clientportal
    const repoInput = page.locator('#wiz-nextjs-repo');
    await expect(repoInput).toBeVisible();
    await repoInput.fill('https://github.com/dravyafolio2021/clientportal');

    // 7. Verify Global PAT status badge and save token globally
    const patInput = page.locator('#wiz-nextjs-token');
    await expect(patInput).toBeVisible();

    // Test saving PAT globally
    await patInput.fill('ghp_testGlobalTokenSample123456');
    const savePatBtn = page.locator('#wiz-nextjs-save-pat-btn');
    await expect(savePatBtn).toBeVisible();
    await savePatBtn.click();
    await page.waitForTimeout(1000);

    // Verify global PAT pill updates
    const patPill = page.locator('#wiz-nextjs-pat-status-pill');
    await expect(patPill).toBeVisible();
    await expect(patPill).toContainText('Connected Globally');

    // 8. Click "Scan Routes" button
    const scanBtn = page.locator('#wiz-nextjs-check-btn');
    await expect(scanBtn).toBeVisible();
    await scanBtn.click();

    // 9. Verify that scan completes and scanned container appears with routes discovered
    const scannedContainer = page.locator('#wiz-nextjs-scanned-container');
    await expect(scannedContainer).toBeVisible({ timeout: 25000 });

    const scannedCount = page.locator('#wiz-nextjs-scanned-count');
    await expect(scannedCount).toBeVisible();
    const countText = await scannedCount.innerText();
    expect(countText).toMatch(/\d+\s+Routes/i);
    console.log(`[E2E TEST] Successfully scanned Next.js repo routes: ${countText}`);

    // Verify routes list contains Home
    const scannedList = page.locator('#wiz-nextjs-scanned-list');
    await expect(scannedList).toBeVisible();
    await expect(scannedList).toContainText('/');

    // 10. Verify NO "Unauthorized capability" toast is shown
    const unauthorizedToast = page.locator('.cora-toast:has-text("Unauthorized capability"), #cora-toast-container:has-text("Unauthorized capability")');
    await expect(unauthorizedToast).toHaveCount(0);

    // Take screenshot of scanned routes container
    await page.screenshot({ path: 'test-results/nextjs-scanned-routes-success.png' });
    console.log('[E2E TEST] Captured screenshot: test-results/nextjs-scanned-routes-success.png');
  });
});
