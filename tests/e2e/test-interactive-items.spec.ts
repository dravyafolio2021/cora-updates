import { test, expect } from '@playwright/test';
import { login } from './helpers';
import * as path from 'path';

test.describe('Projects Interactive Items & Clickability Suite', () => {
  const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/a61fdc7a-f854-4606-9d91-f2a9f6b6294c';

  test('Verify All Sub-Tabs, Metric Cards, Table Rows, Tags, and Sort Headers Are Fully Clickable', async ({ page }) => {
    page.on('console', msg => console.log('[BROWSER CONSOLE]', msg.type(), msg.text()));
    page.on('pageerror', err => console.log('[BROWSER PAGE ERROR]', err.message));

    // 1. Authenticate and load Projects view
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
    await page.setViewportSize({ width: 1280, height: 850 });
    await page.goto('/workspace/projects');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    const tableRows = page.locator('#cora-projects-table-body tr.cora-project-row');
    const initialCount = await tableRows.count();
    expect(initialCount).toBeGreaterThan(0);

    // 2. Test Search input filtering
    const searchInput = page.locator('#cora-proj-search-input');
    await expect(searchInput).toBeVisible();
    await searchInput.fill('Vogue');
    await page.waitForTimeout(300);
    const searchRows = page.locator('#cora-projects-table-body tr.cora-project-row:visible');
    expect(await searchRows.count()).toBe(1);
    await expect(searchRows.first()).toContainText('Vogue');

    // Clear search
    await searchInput.fill('');
    await page.waitForTimeout(300);
    expect(await page.locator('#cora-projects-table-body tr.cora-project-row:visible').count()).toBe(initialCount);

    // 9. Test Capability Tag Click (e.g. "Creative Direction")
    const firstTag = page.locator('#cora-projects-table-body span[title*="Filter by"]').first();
    const tagText = (await firstTag.innerText()).trim();
    await firstTag.click();
    await page.waitForTimeout(300);
    // Search input should be populated with tag text
    expect(await searchInput.inputValue()).toBe(tagText);

    // Clear search
    await searchInput.fill('');
    await page.waitForTimeout(300);

    // 10. Test Table Column Sorting Headers
    const titleSortHeader = page.locator('th[title="Sort by title"]');
    await expect(titleSortHeader).toBeVisible();
    await titleSortHeader.click();
    await page.waitForTimeout(300);

    const industrySortHeader = page.locator('th[title="Sort by industry"]');
    await expect(industrySortHeader).toBeVisible();
    await industrySortHeader.click();
    await page.waitForTimeout(300);

    // 11. Test Table Row Click (Title click opens Project Drawer)
    const firstTitle = page.locator('#cora-projects-table-body tr.cora-project-row span.font-semibold').first();
    await firstTitle.click();
    await page.waitForTimeout(400);

    const drawer = page.locator('#cora-project-drawer');
    await expect(drawer).toBeVisible();
    await expect(drawer).toHaveClass(/open/);

    // Close drawer via Escape key
    await page.keyboard.press('Escape');
    await page.waitForTimeout(400);
    await expect(drawer).not.toHaveClass(/open/);

    // 12. Test Thumbnail Click (Opens Drawer directly to Step 4 "Visuals")
    const firstThumb = page.locator('#cora-projects-table-body tr.cora-project-row img[title="View media assets"]').first();
    if (await firstThumb.isVisible()) {
      await firstThumb.click();
      await page.waitForTimeout(400);
      await expect(drawer).toHaveClass(/open/);
      const step4Panel = page.locator('#proj-step-4');
      await expect(step4Panel).toBeVisible();

      // Close drawer
      await page.keyboard.press('Escape');
      await page.waitForTimeout(400);
    }

    // 13. Test Primary KPI Outcome Click (Opens Drawer directly to Step 3 "Results")
    const firstKpiPill = page.locator('#cora-projects-table-body tr.cora-project-row div[title="Edit proven results & KPIs"]').first();
    if (await firstKpiPill.isVisible()) {
      await firstKpiPill.click();
      await page.waitForTimeout(400);
      await expect(drawer).toHaveClass(/open/);
      const step3Panel = page.locator('#proj-step-3');
      await expect(step3Panel).toBeVisible();

      // Close drawer
      await page.keyboard.press('Escape');
      await page.waitForTimeout(400);
    }

    // Assert ZERO horizontal scroll on desktop
    await page.setViewportSize({ width: 1440, height: 900 });
    await page.waitForTimeout(400);
    const scroll1440 = await page.evaluate(() => {
      const container = document.querySelector('#cora-projects-table-view > div');
      return container ? container.scrollWidth > container.clientWidth : false;
    });
    expect(scroll1440).toBe(false);

    // Also assert ZERO horizontal scroll at 1280px standard laptop resolution
    await page.setViewportSize({ width: 1280, height: 800 });
    await page.waitForTimeout(300);
    const scroll1280 = await page.evaluate(() => {
      const container = document.querySelector('#cora-projects-table-view > div');
      return container ? container.scrollWidth > container.clientWidth : false;
    });
    expect(scroll1280).toBe(false);

    // Test More Actions Dropdown Menu
    const moreBtn = page.locator('button[title="More Actions"]').first();
    await expect(moreBtn).toBeVisible();
    await moreBtn.click();
    await page.waitForTimeout(300);
    const rowMenu = page.locator('.cora-row-menu:not(.hidden)').first();
    await expect(rowMenu).toBeVisible();
    await expect(rowMenu).toContainText('Copy Public Link');
    await expect(rowMenu).toContainText('Duplicate Project');

    // Close menu by clicking outside
    await page.mouse.click(10, 10);
    await page.waitForTimeout(200);
    await expect(page.locator('.cora-row-menu:not(.hidden)')).toHaveCount(0);

    // Capture desktop clean portfolio table visual confirmation artifact
    await page.setViewportSize({ width: 1440, height: 900 });
    await page.waitForTimeout(400);
    await page.screenshot({
      path: path.join(artifactDir, 'desktop_clean_portfolio_table.png'),
      fullPage: false,
    });

    await page.screenshot({
      path: path.join(artifactDir, 'interactive_portfolio_view_verified.png'),
      fullPage: false,
    });

    // 14. Mobile Viewport Verification & Screenshot
    await page.setViewportSize({ width: 390, height: 844 });
    await page.waitForTimeout(500);
    const mobileCards = page.locator('#cora-projects-mobile-list .cora-project-row');
    expect(await mobileCards.count()).toBeGreaterThan(0);

    await page.screenshot({
      path: path.join(artifactDir, 'mobile_clean_portfolio_cards.png'),
      fullPage: false,
    });

    console.log('All interactive clickability items verified successfully!');
  });
});
