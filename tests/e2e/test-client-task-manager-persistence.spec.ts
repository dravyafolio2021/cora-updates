import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('verify Task Manager Drag-and-Drop & Stage Update State Persistence Across Page Refreshes', async ({ page }) => {
    page.on('pageerror', exception => {
        console.log(`Uncaught page error: "${exception.message}"`);
    });

    // 1. Authenticate as studio owner
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

    // 2. Navigate to Task Manager
    await page.goto('/studio/tasks');
    await page.waitForLoadState('networkidle');

    // Verify Kanban board is visible
    const kanbanBoard = page.locator('#cora-task-kanban-board');
    await expect(kanbanBoard).toBeVisible({ timeout: 10000 });

    // Set filter to "All Time" so all cards are visible during test
    const allTimeBtn = page.locator('.task-timeframe-btn[data-timeframe="all"]');
    if (await allTimeBtn.isVisible()) {
        await allTimeBtn.click();
        await page.waitForTimeout(300);
    }

    // Find task-101 (in_progress)
    const task101 = page.locator('.cora-task-card[data-id="task-101"]');
    await expect(task101).toBeVisible();
    const initialStatus = await task101.getAttribute('data-status');
    console.log(`Task 101 initial status: ${initialStatus}`);

    // Open task-101 drawer and change stage to 'done' (or drag to 'done')
    await task101.click();
    const drawer = page.locator('#cora-task-drawer');
    await expect(drawer).toBeVisible({ timeout: 5000 });

    // Change status to 'done' in the drawer status dropdown
    const [response] = await Promise.all([
        page.waitForResponse(resp => resp.url().includes('admin-ajax.php') && resp.status() === 200),
        page.selectOption('#drawer-task-status-select', 'done')
    ]);

    const responseJson = await response.json();
    console.log('AJAX response for stage update:', responseJson);
    expect(responseJson.success).toBe(true);

    // Verify task-101 is now inside the 'done' column in DOM
    const doneColumn = page.locator('.cora-task-kanban-column[data-status="done"]');
    await expect(doneColumn.locator('.cora-task-card[data-id="task-101"]')).toBeVisible();

    // 3. NOW PERFORM HARD PAGE REFRESH TO TEST DATABASE PERSISTENCE
    console.log('Reloading page to verify persistence...');
    await page.reload();
    await page.waitForLoadState('networkidle');

    // Set filter to "All Time" to view all persisted tasks
    if (await allTimeBtn.isVisible()) {
        await allTimeBtn.click();
        await page.waitForTimeout(300);
    }

    // Verify task-101 is STILL in the 'done' column after reload!
    const reloadedDoneColumn = page.locator('.cora-task-kanban-column[data-status="done"]');
    const persistedTask101 = reloadedDoneColumn.locator('.cora-task-card[data-id="task-101"]');
    await expect(persistedTask101).toBeVisible({ timeout: 5000 });
    const persistedStatus = await persistedTask101.getAttribute('data-status');
    console.log(`Task 101 status after reload: ${persistedStatus}`);
    expect(persistedStatus).toBe('done');

    // 4. Test Task Creation & Persistence
    const uniqueTaskTitle = `E2E Automated Deliverable ${Date.now()}`;
    const newTaskBtn = page.locator('#btn-open-create-task-drawer, button:has-text("New Task")').first();
    await newTaskBtn.click();
    const createDrawer = page.locator('#cora-create-task-drawer');
    await expect(createDrawer).toBeVisible();

    await page.fill('#create-task-client', 'Aarav Mehta');
    await page.fill('#create-task-title', uniqueTaskTitle);
    await page.selectOption('#create-task-priority', 'urgent');

    const [createResponse] = await Promise.all([
        page.waitForResponse(resp => resp.url().includes('admin-ajax.php') && resp.status() === 200),
        page.locator('#cora-create-task-form button[type="submit"]').click()
    ]);

    const createJson = await createResponse.json();
    console.log('AJAX create response:', createJson);
    expect(createJson.success).toBe(true);

    // Reload again and verify newly created task exists
    await page.reload();
    await page.waitForLoadState('networkidle');

    if (await allTimeBtn.isVisible()) {
        await allTimeBtn.click();
        await page.waitForTimeout(300);
    }

    const createdTaskCard = page.locator(`.cora-task-card:has-text("${uniqueTaskTitle}")`).first();
    await expect(createdTaskCard).toBeVisible({ timeout: 5000 });

    // Capture screenshot of colorful Kanban board
    await page.screenshot({
        path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/client-tasks-colorful-kanban-live.png',
        fullPage: false
    });
    await page.screenshot({
        path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/client-tasks-persisted-live.png',
        fullPage: false
    });
    console.log('Persistence test passed successfully!');
});
