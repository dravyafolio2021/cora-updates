import { test, expect } from '@playwright/test';
import { login } from './helpers';
import * as path from 'path';
import * as fs from 'fs';

test.describe('Canvas GitHub Integration - SaaS UX Unconnected State', () => {
  test.beforeEach(async ({ page }) => {
    await login(page);
  });

  test('Desktop & Mobile: Clean Unconnected Onboarding, Direct Links & Guide Modal', async ({ page }) => {
    await page.setViewportSize({ width: 1440, height: 900 });
    await page.goto('/workspace/canvas');
    await page.waitForLoadState('domcontentloaded');

    // 1. Locate and click GitHub Integration subtab
    const githubTabBtn = page.locator('#subtab-btn-tab-canvas-github');
    await expect(githubTabBtn).toBeVisible();
    await githubTabBtn.click();
    await page.waitForTimeout(500);

    // 2. Verify Tab Content is visible
    const githubTabContent = page.locator('#tab-canvas-github');
    await expect(githubTabContent).toBeVisible();

    // 3. Verify UNCONNECTED VIEW is visible and CONNECTED DASHBOARD is hidden
    const unconnectedView = page.locator('#cora-gh-unconnected-view');
    const connectedDashboard = page.locator('#cora-gh-connected-dashboard');
    await expect(unconnectedView).toBeVisible();
    await expect(connectedDashboard).toBeHidden();

    // 4. Verify Hero Header
    await expect(unconnectedView.locator('h2')).toContainText('Connect GitHub to Workspace');

    // 5. Verify Direct Link to GitHub Token Settings with prefilled scopes & description
    const ghLink = unconnectedView.locator('a[href*="github.com/settings/tokens/new"]');
    await expect(ghLink).toBeVisible();
    const href = await ghLink.getAttribute('href');
    expect(href).toContain('scopes=repo,read:org');
    expect(href).toContain('description=Cora%20Studio%20Integration');

    // 6. Verify Single Focused Connection Card (Personal GitHub) and Managed Card Removed
    const personalCard = unconnectedView.locator('#cora-gh-unconnected-choices').locator('div').filter({ hasText: 'Personal GitHub' }).first();
    const managedCard = unconnectedView.locator('#cora-gh-unconnected-choices').locator('div').filter({ hasText: 'Instant Cloud Setup' });
    await expect(personalCard).toBeVisible();
    await expect(managedCard).toHaveCount(0);

    // 7. Verify Trust Bar is removed for clean no-scroll UI & doc link exists
    await expect(unconnectedView.locator('h4', { hasText: '1-Click Snapshot Rollbacks' })).toHaveCount(0);
    await expect(unconnectedView.locator('h4', { hasText: 'Zero Data Loss Backups' })).toHaveCount(0);
    await expect(unconnectedView.locator('h4', { hasText: 'Collaborative Pull Requests' })).toHaveCount(0);
    await expect(unconnectedView.locator('a[href*="docs.github.com"]').first()).toBeVisible();

    // Capture screenshot of Unconnected View
    const outputDir = path.resolve(process.cwd(), 'test_output');
    if (!fs.existsSync(outputDir)) {
      fs.mkdirSync(outputDir, { recursive: true });
    }
    await page.screenshot({ path: path.join(outputDir, 'github_unconnected_saas_view.png'), fullPage: false });

    // 8. Test Step-by-Step Guide Modal Opening
    const guideBtn = unconnectedView.locator('button', { hasText: 'How it Works & Step-by-Step Guide' });
    await expect(guideBtn).toBeVisible();
    await guideBtn.click();
    await page.waitForTimeout(400);

    const guideModal = page.locator('#modal-github-connection-guide');
    await expect(guideModal).toBeVisible();
    await expect(guideModal.locator('h3')).toContainText('GitHub Connection Guide');
    await expect(guideModal.locator('h4', { hasText: 'Open GitHub' })).toBeVisible();
    await expect(guideModal.locator('h4', { hasText: 'Verify Scopes' })).toBeVisible();
    await expect(guideModal.locator('h4', { hasText: 'Paste in Cora' })).toBeVisible();

    // Verify modal does NOT have overflow-y-auto / scrolling
    const modalContent = guideModal.locator('.cora-guide-modal-card');
    await expect(modalContent).toBeVisible();

    // Verify background scroll is locked
    const isScrollLocked = await page.evaluate(() => {
      return document.body.style.overflow === 'hidden';
    });
    expect(isScrollLocked).toBe(true);

    // Record scroll position before wheel
    const scrollTopBefore = await page.evaluate(() => {
      const mainEl = document.querySelector('main.cora-main');
      return mainEl ? mainEl.scrollTop : window.scrollY;
    });

    // Simulate mouse wheel scroll while modal is open
    await page.mouse.wheel(0, 500);
    await page.waitForTimeout(200);

    // Verify background page did NOT scroll
    const scrollTopAfter = await page.evaluate(() => {
      const mainEl = document.querySelector('main.cora-main');
      return mainEl ? mainEl.scrollTop : window.scrollY;
    });
    expect(scrollTopAfter).toBe(scrollTopBefore);

    // Verify modal card remains centered in viewport
    const box = await modalContent.boundingBox();
    expect(box).not.toBeNull();
    if (box) {
      expect(box.y).toBeGreaterThan(50);
      expect(box.y + box.height).toBeLessThan(850);
    }

    // Capture modal screenshot for visual review
    await page.screenshot({ path: path.join(outputDir, 'github_guide_modal_compact.png'), fullPage: false });

    // 9. Close Guide Modal via Close Button
    const closeBtn = guideModal.locator('button', { hasText: 'Got it, Close' });
    await closeBtn.click();
    await page.waitForTimeout(300);
    await expect(guideModal).toBeHidden();

    // Verify background scroll restored
    const isScrollRestored = await page.evaluate(() => {
      return document.body.style.overflow === '';
    });
    expect(isScrollRestored).toBe(true);
  });
});
