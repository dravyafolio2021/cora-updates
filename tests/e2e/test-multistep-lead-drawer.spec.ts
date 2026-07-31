import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('verify 3-step lead capture drawer and bi-directional stage preselection', async ({ page }) => {
    await login(page);
    await page.goto('/workspace/leads');
    await page.waitForLoadState('networkidle');

    await page.evaluate(() => {
        if (typeof (window as any).coraNavigateTo === 'function') {
            (window as any).coraNavigateTo('leads');
        }
    });
    await page.waitForTimeout(500);

    // 1. Click quick add '+' button on the 'Negotiation' column header
    const negAddBtn = page.locator('.cora-kanban-column[data-status="Negotiation"] button[title="Quick Add Lead"]').first();
    await expect(negAddBtn).toBeVisible();
    await negAddBtn.click();
    await page.waitForTimeout(400);

    // Assert drawer is open and target badge shows 'Stage: Negotiation'
    const drawer = page.locator('#cora-create-lead-drawer');
    await expect(drawer).toBeVisible();
    await expect(page.locator('#cora-create-lead-target-badge')).toContainText('Negotiation');

    // Take screenshot of Step 1
    await page.screenshot({ path: 'tests/e2e/create-lead-step-1.png' });

    // Fill Step 1 fields
    await page.fill('#cora-new-lead-names', 'Aditya Roy Kapoor');
    await page.fill('#cora-new-lead-email', 'aditya@kapoor.com');

    // Click Next: Stage & Budget
    await page.click('button:has-text("Next: Stage & Budget")');
    await page.waitForTimeout(300);

    // Take screenshot of Step 2 (Stage Cards grid with Negotiation highlighted)
    await page.screenshot({ path: 'tests/e2e/create-lead-step-2.png' });

    // Click Next: Scope & Notes
    await page.click('button:has-text("Next: Scope & Notes")');
    await page.waitForTimeout(300);

    // Take screenshot of Step 3
    await page.screenshot({ path: 'tests/e2e/create-lead-step-3.png' });
});
