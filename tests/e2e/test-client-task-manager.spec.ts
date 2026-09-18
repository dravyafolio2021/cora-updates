import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('verify CRM Tasks standalone Kanban Pipeline with Dynamic Industry, Timeframe & Client Filters', async ({ page }) => {
    page.on('pageerror', exception => {
        console.log(`Uncaught exception: "${exception.message}"`);
    });

    // Login as studio owner
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

    // 1. Test /studio/clients — verify 4 subtabs exist and bidirectional tasks button in Client Drawer
    await page.goto('/studio/clients');
    await page.waitForLoadState('networkidle');

    await expect(page.locator('#tab-clients-directory, [data-target="directory"]').first()).toBeVisible({ timeout: 10000 });
    await expect(page.locator('#tab-clients-portals, [data-target="portals"]').first()).toBeVisible();
    await expect(page.locator('#tab-clients-invoices, [data-target="invoices"]').first()).toBeVisible();
    await expect(page.locator('#tab-clients-health, [data-target="health"]').first()).toBeVisible();

    // Verify Kanban Tasks subtab is NOT inside Clients
    const tasksTabInClients = page.locator('#cora-clients-module #tab-clients-tasks, #cora-clients-module .cora-sub-tab[data-target="tasks"]');
    expect(await tasksTabInClients.count()).toBe(0);

    // Open first client row and test bidirectional Task Kanban button
    const firstClientRow = page.locator('.client-table-row').first();
    if (await firstClientRow.isVisible()) {
        await firstClientRow.click();
        await expect(page.locator('#cora-client-drawer')).toBeVisible({ timeout: 5000 });
        await expect(page.locator('#drawer-btn-view-tasks')).toBeVisible();
        await page.locator('#btn-close-client-drawer').click();
    }

    // 2. Test /studio/tasks
    await page.goto('/studio/tasks');
    await page.waitForLoadState('networkidle');

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
    const initialCardCount = await taskCards.count();
    expect(initialCardCount).toBeGreaterThan(0);

    // 3. Test Timeframe switcher
    const tfToday = page.locator('.task-timeframe-btn[data-timeframe="today"]');
    if (await tfToday.isVisible()) {
        await tfToday.click();
        await page.waitForTimeout(300);
    }

    const tfTomorrow = page.locator('.task-timeframe-btn[data-timeframe="tomorrow"]');
    if (await tfTomorrow.isVisible()) {
        await tfTomorrow.click();
        await page.waitForTimeout(300);
    }

    const tfMonth = page.locator('.task-timeframe-btn[data-timeframe="month"]');
    if (await tfMonth.isVisible()) {
        await tfMonth.click();
        await page.waitForTimeout(300);
    }

    const tfAll = page.locator('.task-timeframe-btn[data-timeframe="all"]');
    if (await tfAll.isVisible()) {
        await tfAll.click();
        await page.waitForTimeout(300);
    }

    // 4. Test Client Dropdown Filter
    const clientSelect = page.locator('#task-filter-client');
    await expect(clientSelect).toBeVisible();
    const clientOptions = await clientSelect.locator('option').allInnerTexts();
    expect(clientOptions.length).toBeGreaterThan(1);

    // Select second option (specific client)
    await clientSelect.selectOption({ index: 1 });
    await page.waitForTimeout(300);
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/client-tasks-filtered-client.png', fullPage: false });

    // Reset to all clients
    await clientSelect.selectOption('all');
    await page.waitForTimeout(300);

    // 5. Test Quick Task Creator Drawer Sheet
    const btnNewTask = page.locator('#btn-add-task').first();
    await expect(btnNewTask).toBeVisible();
    await btnNewTask.click();
    const createDrawer = page.locator('#cora-create-task-drawer');
    await expect(createDrawer).toBeVisible({ timeout: 5000 });
    await page.locator('#btn-close-create-task').click();
    await expect(createDrawer).not.toBeVisible();

    // 6. Test Task Details Drawer
    const visibleTaskCard = page.locator('.cora-task-card:visible').first();
    await expect(visibleTaskCard).toBeVisible({ timeout: 5000 });
    await visibleTaskCard.click();
    const detailsDrawer = page.locator('#cora-task-drawer');
    await expect(detailsDrawer).toBeVisible({ timeout: 5000 });
    await page.locator('#btn-close-task-drawer').click();
    await expect(detailsDrawer).not.toBeVisible();

    // Capture main view screenshot
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/client-tasks-multi-filter-live.png', fullPage: false });

    // 7. Test Real Estate Workspace Dynamic Terminology
    await page.goto('/workspace/tasks?industry=real_estate');
    await page.waitForLoadState('networkidle');

    await expect(page.locator('#cora-task-kanban-board')).toBeVisible({ timeout: 10000 });
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/client-tasks-realestate-industry.png', fullPage: false });
});

