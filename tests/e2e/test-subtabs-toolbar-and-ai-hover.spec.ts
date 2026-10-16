import { test, expect } from '@playwright/test';
import { login } from './helpers';
import * as path from 'path';
import * as fs from 'fs';

test.describe('Standardized Sub-Tabs Toolbar & AI Stack Hover Verification', () => {
  const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/a61fdc7a-f854-4606-9d91-f2a9f6b6294c';

  test('Desktop: Verify search bar alignment, grid/list switcher & AI tooltip unclipped', async ({ page }) => {
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
    await page.setViewportSize({ width: 1280, height: 800 });
    await page.goto('/workspace/projects');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(500);

    // 1. Verify AI Stack Hover Tooltips
    const aiStack = page.locator('.cora-platform-stack').first();
    await expect(aiStack).toBeVisible();

    const aiButtons = aiStack.locator('button');
    const buttonCount = await aiButtons.count();
    expect(buttonCount).toBeGreaterThanOrEqual(3);

    // Hover over ChatGPT button
    const chatgptBtn = aiButtons.first();
    await chatgptBtn.hover();
    await page.waitForTimeout(300);

    // Take screenshot of ChatGPT hover tooltip
    await page.screenshot({
      path: path.join(artifactDir, 'desktop_ai_hover_fixed.png'),
      fullPage: false,
    });

    // Check tooltip visibility and computed z-index / bounding box
    const tooltip = chatgptBtn.locator('span').first();
    await expect(tooltip).toBeVisible();
    const tooltipBox = await tooltip.boundingBox();
    expect(tooltipBox).not.toBeNull();
    console.log('Tooltip bounding box:', tooltipBox);

    // Check that header has higher z-index (45) than sub-tabs (40)
    const headerZIndex = await page.evaluate(() => {
      const header = document.querySelector('.cora-workspace-header');
      const tabs = document.querySelector('.cora-sub-tabs-container');
      return {
        headerZ: header ? window.getComputedStyle(header).zIndex : null,
        headerPos: header ? window.getComputedStyle(header).position : null,
        tabsZ: tabs ? window.getComputedStyle(tabs).zIndex : null,
      };
    });
    console.log('Stacking context check:', headerZIndex);
    expect(Number(headerZIndex.headerZ)).toBe(45);
    expect(Number(headerZIndex.tabsZ)).toBe(40);

    // 2. Verify Toolbar: Search Input + Table/Grid Switcher Alignment
    const mobileTrigger = page.locator('#cora-proj-search-trigger-btn');
    // On desktop (1280px), the mobile trigger MUST be hidden
    await expect(mobileTrigger).toBeHidden();

    const searchInput = page.locator('#cora-proj-search-input');
    await expect(searchInput).toBeVisible();

    const switcherTable = page.locator('#view-mode-table-btn');
    const switcherGrid = page.locator('#view-mode-grid-btn');
    await expect(switcherTable).toBeVisible();
    await expect(switcherGrid).toBeVisible();

    // Verify heights and alignment
    const metrics = await page.evaluate(() => {
      const input = document.getElementById('cora-proj-search-input');
      const switcher = document.querySelector('.cora-subtabs-switcher');
      const tableBtn = document.getElementById('view-mode-table-btn');
      const gridBtn = document.getElementById('view-mode-grid-btn');
      const toolbar = document.querySelector('.cora-subtabs-toolbar');

      const inputRect = input ? input.getBoundingClientRect() : null;
      const switcherRect = switcher ? switcher.getBoundingClientRect() : null;
      const tableRect = tableBtn ? tableBtn.getBoundingClientRect() : null;
      const toolbarRect = toolbar ? toolbar.getBoundingClientRect() : null;

      return {
        inputHeight: inputRect ? inputRect.height : null,
        switcherHeight: switcherRect ? switcherRect.height : null,
        tableBtnHeight: tableRect ? tableRect.height : null,
        verticalDiff: (inputRect && switcherRect) ? Math.abs(inputRect.top - switcherRect.top) : null,
      };
    });
    console.log('Toolbar metrics:', metrics);
    expect(metrics.inputHeight).toBe(28);
    expect(metrics.switcherHeight).toBe(28);
    expect(metrics.tableBtnHeight).toBe(22);
    // Vertical alignment diff between search input top and switcher top must be <= 1px
    expect(metrics.verticalDiff).toBeLessThanOrEqual(1);

    // 3. Test Switcher Interaction
    await switcherGrid.click();
    await page.waitForTimeout(200);
    const gridView = page.locator('#cora-projects-grid-view');
    const tableView = page.locator('#cora-projects-table-view');
    await expect(gridView).toBeVisible();
    await expect(tableView).toBeHidden();
    await expect(switcherGrid).toHaveClass(/active/);

    await switcherTable.click();
    await page.waitForTimeout(200);
    await expect(tableView).toBeVisible();
    await expect(gridView).toBeHidden();
    await expect(switcherTable).toHaveClass(/active/);

    // Take screenshot of perfectly aligned desktop toolbar
    await page.screenshot({
      path: path.join(artifactDir, 'desktop_toolbar_standardized.png'),
      fullPage: false,
    });
  });

  test('Mobile: Verify 28px search trigger, expansion, escape, and toggle', async ({ page }) => {
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
    await page.setViewportSize({ width: 390, height: 844 });
    await page.goto('/workspace/projects');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(500);

    const mobileTrigger = page.locator('#cora-proj-search-trigger-btn');
    await expect(mobileTrigger).toBeVisible();

    const triggerBox = await mobileTrigger.boundingBox();
    expect(triggerBox).not.toBeNull();
    console.log('Mobile trigger bounding box:', triggerBox);
    expect(triggerBox?.height).toBe(28);
    expect(triggerBox?.width).toBe(28);

    const searchWrapper = page.locator('#cora-proj-search-wrapper');
    await expect(searchWrapper).toBeHidden();

    // Click trigger to expand
    await mobileTrigger.click();
    await page.waitForTimeout(300);

    await expect(searchWrapper).toBeVisible();
    await expect(searchWrapper).toHaveClass(/mobile-expanded/);

    // Take screenshot of mobile expanded search
    await page.screenshot({
      path: path.join(artifactDir, 'mobile_search_expanded.png'),
      fullPage: false,
    });

    // Close search via close button
    const closeBtn = page.locator('#cora-proj-search-close-btn');
    await expect(closeBtn).toBeVisible();
    await closeBtn.click();
    await page.waitForTimeout(300);

    await expect(searchWrapper).toBeHidden();
    await expect(mobileTrigger).toBeVisible();

    // Take screenshot of mobile collapsed toolbar
    await page.screenshot({
      path: path.join(artifactDir, 'mobile_toolbar_collapsed.png'),
      fullPage: false,
    });
  });
});
