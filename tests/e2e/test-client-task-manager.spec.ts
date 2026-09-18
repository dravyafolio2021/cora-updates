import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('verify Client Kanban Tasks sub-tab inside Clients module and exact Leads Kanban UI', async ({ page }) => {
    page.on('pageerror', exception => {
        console.log(`Uncaught exception: "${exception.message}"`);
    });

    // Login as studio owner
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

    // 1. Test /studio/clients
    await page.goto('/studio/clients');
    await page.waitForLoadState('networkidle');

    // Verify sub-tabs exist
    const tasksTab = page.locator('#tab-clients-tasks, [data-target="tasks"]').first();
    await expect(tasksTab).toBeVisible({ timeout: 10000 });

    // Click on Kanban Tasks sub-tab
    await tasksTab.click();
    await page.waitForTimeout(300);

    // Verify Kanban Board container is visible
    const kanbanBoard = page.locator('#cora-task-kanban-board');
    await expect(kanbanBoard).toBeVisible({ timeout: 10000 });

    // Verify all 4 stage columns are rendered
    await expect(page.locator('.cora-task-kanban-column[data-status="todo"]')).toBeVisible();
    await expect(page.locator('.cora-task-kanban-column[data-status="in_progress"]')).toBeVisible();
    await expect(page.locator('.cora-task-kanban-column[data-status="review"]')).toBeVisible();
    await expect(page.locator('.cora-task-kanban-column[data-status="done"]')).toBeVisible();

    // Verify task cards exist inside board
    const taskCards = page.locator('.cora-task-card');
    const cardCount = await taskCards.count();
    expect(cardCount).toBeGreaterThan(0);

    // Capture visual screenshot of the live Client Kanban Tasks pipeline
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/client-tasks-kanban-live.png', fullPage: false });

    // 2. Test sidebar navigation to Tasks in CRM group
    const crmTasksNav = page.locator('nav a[href*="/tasks"], nav a[href*="tab=tasks"]').first();
    await expect(crmTasksNav).toBeVisible();
    await crmTasksNav.click();
    await page.waitForLoadState('networkidle');
    await expect(page.locator('#cora-task-kanban-board')).toBeVisible({ timeout: 10000 });
});
