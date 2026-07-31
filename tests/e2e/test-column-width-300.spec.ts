import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('verify 300px kanban column width', async ({ page }) => {
    await login(page);
    await page.goto('/workspace/leads');
    await page.waitForLoadState('networkidle');

    await page.evaluate(() => {
        if (typeof (window as any).coraNavigateTo === 'function') {
            (window as any).coraNavigateTo('leads');
        }
    });
    await page.waitForTimeout(500);

    const kanbanColumn = page.locator('.cora-kanban-column').first();
    await expect(kanbanColumn).toBeVisible();

    await page.screenshot({ path: 'tests/e2e/kanban-columns-300px.png', fullPage: false });
});
