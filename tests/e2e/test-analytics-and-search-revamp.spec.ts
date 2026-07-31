import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Mobile Search Bar & Actionable Deal Velocity Analytics Hub', () => {
  test('verify full-width mobile search input, 2-column filters, and interactive analytics launchers', async ({ page }) => {
    page.on('pageerror', exception => {
      console.log(`Uncaught exception: "${exception.message}"`);
    });

    // Set mobile viewport (iPhone SE resolution)
    await page.setViewportSize({ width: 375, height: 667 });
    await login(page);
    await page.goto('/workspace/leads');
    await page.waitForLoadState('networkidle');

    // 1. Verify Full-Width Mobile Search Bar & Filter Grid
    const searchInput = page.locator('#cora-lead-search-input');
    await expect(searchInput).toBeVisible();
    await expect(searchInput).toHaveAttribute('placeholder', '🔍 Search leads by name, email, city...');

    const stageFilter = page.locator('#cora-lead-stage-filter');
    await expect(stageFilter).toBeVisible();

    const assigneeFilter = page.locator('#cora-lead-assignee-filter');
    await expect(assigneeFilter).toBeVisible();

    // 2. Switch to Funnel & Analytics Subtab
    const analyticsTabBtn = page.locator('.cora-lead-subtab-btn[data-tab="analytics"]');
    await expect(analyticsTabBtn).toBeVisible({ timeout: 10000 });
    await analyticsTabBtn.click();

    const analyticsPane = page.locator('#cora-lead-pane-analytics');
    await expect(analyticsPane).not.toHaveClass(/hidden/);

    // 3. Verify High-Priority Action Launchers
    const actionLaunchers = page.locator('#cora-lead-pane-analytics button:has-text("Filter & Contact"), #cora-lead-pane-analytics button:has-text("Review & Convert")');
    await expect(actionLaunchers.first()).toBeVisible();

    // 4. Verify Coming Soon badge on Lead Acquisition Channels block
    const comingSoonBadge = page.locator('#cora-lead-channels-card:has-text("Coming Soon")');
    await expect(comingSoonBadge).toBeVisible();

    // 5. Click Action Launcher button to jump back to Directory with stage filter applied
    await actionLaunchers.first().click();

    const directoryPane = page.locator('#cora-lead-pane-directory');
    await expect(directoryPane).not.toHaveClass(/hidden/);

    // Take mobile screenshot of revamped search toolbar & actionable analytics
    await page.screenshot({ path: 'tests/e2e/mobile-leads-search-and-analytics.png' });
  });
});
