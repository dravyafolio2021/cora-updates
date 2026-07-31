import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('verify top header Activity Log button and Customize Columns drawer', async ({ page }) => {
    await login(page);

    await page.goto('/workspace/leads');
    await page.waitForLoadState('networkidle');

    await page.evaluate(() => {
        if (typeof (window as any).coraNavigateTo === 'function') {
            (window as any).coraNavigateTo('leads');
        }
    });
    await page.waitForTimeout(500);

    // 1. Take screenshot of optimized top header bar & secondary toolbar
    await page.screenshot({ path: 'tests/e2e/leads-top-header-reorganized.png' });

    // 2. Test Activity Log button in top header
    const activityBtn = page.locator('#cora-top-header-activity-btn');
    await expect(activityBtn).toBeVisible();
    await activityBtn.click();
    await page.waitForTimeout(500);

    const activityPane = page.locator('#cora-lead-pane-activity');
    await expect(activityPane).toBeVisible();
    await page.screenshot({ path: 'tests/e2e/activity-log-pane-top-header-trigger.png' });

    // Switch back to kanban pipeline using subtab button
    const kanbanBtn = page.locator('.cora-lead-subtab-btn[data-tab="kanban"]');
    await kanbanBtn.click();
    await page.waitForTimeout(300);

    // 3. Test Customize Columns button in top header
    const customizeBtn = page.locator('#cora-top-header-customize-cols');
    await expect(customizeBtn).toBeVisible();
    await customizeBtn.click();
    await page.waitForTimeout(600);

    const customizeDrawer = page.locator('#cora-lead-stages-drawer');
    await expect(customizeDrawer).toBeVisible();
    await expect(customizeDrawer).not.toHaveClass(/translate-x-full/);
    await page.screenshot({ path: 'tests/e2e/customize-columns-drawer-opened.png' });

    // 4. Close drawer via backdrop or close button
    const closeBtn = page.locator('#cora-lead-stages-drawer button').first();
    if (await closeBtn.isVisible()) {
        await closeBtn.click();
    } else {
        await page.locator('#cora-drawer-backdrop').click();
    }
    await page.waitForTimeout(500);
    await expect(customizeDrawer).toHaveClass(/translate-x-full/);
});
