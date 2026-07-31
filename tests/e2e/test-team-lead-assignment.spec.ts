import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Lead Management Subtabs & Team Lead Assignment', () => {
  test('verify subtab active state legibility and team assignment features', async ({ page }) => {
    page.on('pageerror', exception => {
      console.log(`Uncaught exception: "${exception.message}"`);
    });

    await page.setViewportSize({ width: 1440, height: 900 });
    await login(page);
    await page.goto('/workspace/leads');
    await page.waitForLoadState('networkidle');

    // 1. Verify subtab buttons rendering and active high-contrast styling
    const kanbanBtn = page.locator('.cora-lead-subtab-btn[data-tab="kanban"]');
    await expect(kanbanBtn).toBeVisible({ timeout: 10000 });
    await expect(kanbanBtn).toHaveClass(/active/);
    await expect(kanbanBtn).toHaveClass(/bg-white/);

    // 2. Verify subtab switching to directory tab
    const dirBtn = page.locator('.cora-lead-subtab-btn[data-tab="directory"]');
    await expect(dirBtn).toBeVisible();
    await dirBtn.click();
    await page.waitForTimeout(300);
    await expect(dirBtn).toHaveClass(/active/);

    // 3. Verify Team Member Filter dropdown in top toolbar
    const teamFilter = page.locator('#cora-lead-assignee-filter');
    await expect(teamFilter).toBeVisible();

    // 4. Verify "Assigned To" header in directory table
    const assignedHeader = page.locator('th:has-text("Assigned To")');
    await expect(assignedHeader).toBeVisible();

    // Take screenshot of updated leads workspace with team features
    await page.screenshot({ path: 'tests/e2e/team-lead-assignment-verified.png' });
  });
});
