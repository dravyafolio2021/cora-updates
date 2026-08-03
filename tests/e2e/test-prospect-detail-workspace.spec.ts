import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('verify clean non-technical prospect detail workspace drawer', async ({ page }) => {
    page.on('pageerror', exception => {
        console.log(`Uncaught exception: "${exception.message}"`);
    });
    await login(page);
    await page.goto('/workspace/leads');
    await page.waitForLoadState('networkidle');

    // Wait for Kanban board to load
    const leadCard = page.locator('.cora-lead-card').first();
    await expect(leadCard).toBeVisible({ timeout: 10000 });
    await leadCard.click();
    await page.waitForTimeout(400);

    const drawer = page.locator('#cora-lead-detail-drawer');
    await expect(drawer).toBeVisible();

    // Take screenshot of Tab 1: Overview
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/1a191bd8-ca8e-4f43-b8ea-289e35a89b5e/prospect-drawer-overview-clean.png' });

    // 2. Click 'Automation' tab
    await page.click('#cora-lead-detail-tab-btn-automation');
    await page.waitForTimeout(300);
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/1a191bd8-ca8e-4f43-b8ea-289e35a89b5e/prospect-drawer-automation-clean.png' });

    // 3. Click 'Checklist' tab
    await page.click('#cora-lead-detail-tab-btn-checklist');
    await page.waitForTimeout(300);
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/1a191bd8-ca8e-4f43-b8ea-289e35a89b5e/prospect-drawer-checklist-clean.png' });

    // 4. Click 'Activity Log' tab
    await page.click('#cora-lead-detail-tab-btn-audit');
    await page.waitForTimeout(300);
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/1a191bd8-ca8e-4f43-b8ea-289e35a89b5e/prospect-drawer-audit-clean.png' });
});
