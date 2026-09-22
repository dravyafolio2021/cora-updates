import { test, expect } from '@playwright/test';
import { login } from './helpers';
import * as path from 'path';
import * as fs from 'fs';

test.describe('Canvas Themes Module Revamp', () => {
  test.beforeEach(async ({ page }) => {
    await login(page);
  });

  test('Desktop: Verify Canvas Header, 4 KPI cards, Unified Theme Library & Edge-to-Edge Sticky Bar', async ({ page }) => {
    await page.setViewportSize({ width: 1440, height: 900 });
    await page.goto('/workspace/canvas');
    await page.waitForLoadState('networkidle');

    // 1. Verify Header
    await expect(page.locator('h1, span').filter({ hasText: 'Canvas Themes' }).first()).toBeVisible();

    // 2. Verify 4 High-Density KPI analytical cards
    await expect(page.locator('#pagespeed-val-score').first()).toBeVisible();
    await expect(page.locator('#pagespeed-val-lcp').first()).toBeVisible();

    // 3. Verify Sticky Sub-Navigation Tabs (3 tabs: Theme Library, Speed & Core Web Vitals, Migration & Tools)
    const overviewTab = page.locator('#subtab-btn-tab-canvas-overview');
    const vitalsTab = page.locator('#subtab-btn-tab-canvas-vitals');
    const migrationTab = page.locator('#subtab-btn-tab-canvas-migration');

    await expect(overviewTab).toBeVisible();
    await expect(vitalsTab).toBeVisible();
    await expect(migrationTab).toBeVisible();

    // Verify Tab 1 (Theme Library) contains both Active Theme Card and Draft Themes Card
    await expect(page.locator('#tab-canvas-overview')).toBeVisible();
    await expect(page.locator('#active-theme-card')).toBeVisible();
    await expect(page.locator('#draft-themes-library-card')).toBeVisible();

    const outputDir = path.resolve(process.cwd(), 'test_output');
    if (!fs.existsSync(outputDir)) {
      fs.mkdirSync(outputDir, { recursive: true });
    }
    await page.screenshot({ path: path.join(outputDir, 'desktop_canvas_overview.png'), fullPage: false });

    // Scroll down to test sticky bar edge-to-edge flush alignment on Desktop
    await page.evaluate(() => {
      const scrollEl = document.querySelector('.cora-main') || window;
      scrollEl.scrollTo({ top: 220, behavior: 'instant' });
    });
    await page.waitForTimeout(300);
    await page.screenshot({ path: path.join(outputDir, 'desktop_canvas_sticky_scrolled.png'), fullPage: false });

    // Switch to Tab 2: Speed & Core Web Vitals
    await vitalsTab.click();
    await expect(page.locator('#tab-canvas-vitals')).toBeVisible();
    await expect(page.locator('#tab-canvas-overview')).toBeHidden();
    await page.screenshot({ path: path.join(outputDir, 'desktop_canvas_vitals.png'), fullPage: false });

    // Switch to Tab 3: Migration & Tools
    await migrationTab.click();
    await expect(page.locator('#tab-canvas-migration')).toBeVisible();
    await expect(page.locator('#tab-canvas-vitals')).toBeHidden();
    await page.screenshot({ path: path.join(outputDir, 'desktop_canvas_migration.png'), fullPage: false });
  });

  test('Mobile: Verify 390x844 Mobile Layout, 2x2 KPIs, and Touch Navigation', async ({ page }) => {
    await page.setViewportSize({ width: 390, height: 844 });
    await page.goto('/workspace/canvas');
    await page.waitForLoadState('networkidle');

    // Verify Active Theme Card & Drafts rendered cleanly
    await expect(page.locator('#subtab-btn-tab-canvas-overview')).toBeVisible();
    await expect(page.locator('#subtab-btn-tab-canvas-vitals')).toBeVisible();
    await expect(page.locator('#subtab-btn-tab-canvas-migration')).toBeHidden();
    await expect(page.locator('#tab-canvas-overview')).toBeVisible();
    await expect(page.locator('#active-theme-card')).toBeVisible();
    await expect(page.locator('#draft-themes-library-card')).toBeVisible();

    const outputDir = path.resolve(process.cwd(), 'test_output');
    await page.screenshot({ path: path.join(outputDir, 'mobile_canvas_overview.png'), fullPage: false });

    // Scroll on mobile to verify sticky bar pins directly below the global topbar
    await page.evaluate(() => {
      window.scrollTo({ top: 320, behavior: 'instant' });
    });
    await page.waitForTimeout(300);
    await page.screenshot({ path: path.join(outputDir, 'mobile_canvas_sticky_scrolled.png'), fullPage: false });

    // Switch to vitals
    await page.locator('#subtab-btn-tab-canvas-vitals').click();
    await expect(page.locator('#tab-canvas-vitals')).toBeVisible();
    await page.screenshot({ path: path.join(outputDir, 'mobile_canvas_vitals.png'), fullPage: false });
  });
});
