import { test, expect } from '@playwright/test';
import { login } from './helpers';
import * as path from 'path';

test.describe('Canvas Sub-Tabs Reorganization & Brand Identity System', () => {

  test('Verify all 6 tabs in exact order, smooth switching, and Brand tokens live sync', async ({ page }) => {
    // 1. Authenticate with Studio Owner credentials
    await page.setViewportSize({ width: 1366, height: 900 });
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

    // 2. Navigate to Canvas Overview
    await page.goto('/workspace/canvas');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1000);

    // 3. Verify subtab bar exists and assert exact 6-tab order
    const subtabsBar = page.locator('#cora-canvas-subtabs');
    await expect(subtabsBar).toBeVisible();

    const subtabButtons = subtabsBar.locator('> button.cora-canvas-sub-tab');
    await expect(subtabButtons).toHaveCount(6);

    // Verify Tab 1: Themes
    const tab1 = subtabButtons.nth(0);
    await expect(tab1).toHaveId('subtab-btn-tab-canvas-overview');
    await expect(tab1).toContainText('Themes');

    // Verify Tab 2: Pages
    const tab2 = subtabButtons.nth(1);
    await expect(tab2).toHaveId('subtab-btn-tab-canvas-pages');
    await expect(tab2).toContainText('Pages');

    // Verify Tab 3: Performance
    const tab3 = subtabButtons.nth(2);
    await expect(tab3).toHaveId('subtab-btn-tab-canvas-vitals');
    await expect(tab3).toContainText('Performance');

    // Verify Tab 4: Brand
    const tab4 = subtabButtons.nth(3);
    await expect(tab4).toHaveId('subtab-btn-tab-canvas-brand');
    await expect(tab4).toContainText('Brand');

    // Verify Tab 5: Migration
    const tab5 = subtabButtons.nth(4);
    await expect(tab5).toHaveId('subtab-btn-tab-canvas-migration');
    await expect(tab5).toContainText('Migration');

    // Verify Tab 6: GitHub (Compact label, contiguous placement, no bulky badge)
    const tab6 = subtabButtons.nth(5);
    await expect(tab6).toHaveId('subtab-btn-tab-canvas-github');
    await expect(tab6).toContainText('GitHub');
    await expect(tab6).not.toHaveClass(/sm:ml-auto/);

    // 4. Test Switching Through All 6 Tabs
    // Tab 1: Themes
    await tab1.click();
    await page.waitForTimeout(300);
    await expect(page.locator('#tab-canvas-overview')).toBeVisible();
    await expect(page.locator('#tab-canvas-overview')).not.toHaveClass(/hidden/);
    expect(page.url()).toContain('tab=themes');

    // Tab 2: Pages
    await tab2.click();
    await page.waitForTimeout(300);
    await expect(page.locator('#tab-canvas-pages')).toBeVisible();
    await expect(page.locator('#tab-canvas-pages')).not.toHaveClass(/hidden/);
    expect(page.url()).toContain('tab=pages');

    // Tab 3: Performance
    await tab3.click();
    await page.waitForTimeout(300);
    await expect(page.locator('#tab-canvas-vitals')).toBeVisible();
    await expect(page.locator('#tab-canvas-vitals')).not.toHaveClass(/hidden/);
    expect(page.url()).toContain('tab=performance');

    // Tab 4: Brand
    await tab4.click();
    await page.waitForTimeout(300);
    await expect(page.locator('#tab-canvas-brand')).toBeVisible();
    await expect(page.locator('#tab-canvas-brand')).not.toHaveClass(/hidden/);
    expect(page.url()).toContain('tab=brand');

    // Tab 5: Migration & Tools
    await tab5.click();
    await page.waitForTimeout(300);
    await expect(page.locator('#tab-canvas-migration')).toBeVisible();
    await expect(page.locator('#tab-canvas-migration')).not.toHaveClass(/hidden/);
    expect(page.url()).toContain('tab=migration');

    // Tab 6: GitHub Integration
    await tab6.click();
    await page.waitForTimeout(300);
    await expect(page.locator('#tab-canvas-github')).toBeVisible();
    await expect(page.locator('#tab-canvas-github')).not.toHaveClass(/hidden/);
    expect(page.url()).toContain('tab=github');

    // 5. Test Brand Tab: Clean Consolidated Collateral & Creative Assets Hub
    await tab4.click();
    await page.waitForTimeout(300);
    await expect(page.locator('#tab-canvas-brand')).toBeVisible();

    // Verify clean Brand Hub header is visible
    await expect(page.locator('#tab-canvas-brand h2:has-text("Brand Ecosystem & Collateral Hub")')).toBeVisible();

    // Verify Section 1: Connected Brand Ecosystem
    await expect(page.locator('#tab-canvas-brand h3:has-text("Connected Brand Ecosystem")')).toBeVisible();
    await expect(page.locator('#tab-canvas-brand h4:has-text("Figma Design System")')).toBeVisible();
    await expect(page.locator('#tab-canvas-brand h4:has-text("Canva Collateral")')).toBeVisible();
    await expect(page.locator('#tab-canvas-brand h4:has-text("Pitch & Keynotes")')).toBeVisible();
    await expect(page.locator('#tab-canvas-brand h4:has-text("Typography Catalog")')).toBeVisible();

    // Verify Section 2: External Brand Assets & Collateral Library
    await expect(page.locator('#tab-canvas-brand h3:has-text("External Brand Assets & Workspace Library")')).toBeVisible();
    await expect(page.locator('#cora-brand-assets-filter-bar')).toBeVisible();
    await expect(page.locator('#cora-brand-assets-grid')).toBeVisible();

    // Verify backward compatibility hidden input fields exist for theme settings sync
    await expect(page.locator('#brand-tab-site-title')).toBeAttached();
    await expect(page.locator('#brand-tab-color-primary')).toBeAttached();

    // 6. Navigate to GitHub tab and verify multi-tenant enterprise card has been removed
    await tab6.click();
    await page.waitForTimeout(400);
    await expect(page.locator('#tab-canvas-github')).toBeVisible();

    // Verify 100,000+ Multi-Tenant Security & Isolation Architecture is removed
    const multiTenantSection = page.locator('#tab-canvas-github').getByText('100,000+ Multi-Tenant Security');
    await expect(multiTenantSection).toHaveCount(0);

    const cryptographicText = page.locator('#tab-canvas-github').getByText('Cryptographic Tenant Scoping');
    await expect(cryptographicText).toHaveCount(0);

    // 7. Capture screenshots
    const artifactPath = path.resolve(
      '/Users/shrutian/.gemini/antigravity/brain/e7db172d-afc3-4021-b0cc-a3073c916f94/cora_canvas_6_tabs_brand_verified.png'
    );
    await page.screenshot({ path: artifactPath, fullPage: true });

    const subtabsPath = path.resolve(
      '/Users/shrutian/.gemini/antigravity/brain/e7db172d-afc3-4021-b0cc-a3073c916f94/cora_canvas_subtabs_compact_verified.png'
    );
    await subtabsBar.screenshot({ path: subtabsPath });

    const githubTabPath = path.resolve(
      '/Users/shrutian/.gemini/antigravity/brain/e7db172d-afc3-4021-b0cc-a3073c916f94/cora_canvas_github_tab_clean.png'
    );
    await page.locator('#tab-canvas-github').screenshot({ path: githubTabPath });
    console.log('Saved github clean screenshot to:', githubTabPath);
  });

});
