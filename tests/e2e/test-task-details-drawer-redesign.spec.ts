import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('verify Dedicated Task Details & Work Management Drawer with Interactive Subtasks and Property Editing', async ({ page }) => {
    page.on('pageerror', exception => {
        console.log(`Uncaught page error: "${exception.message}"`);
    });

    // 1. Login as studio owner
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

    // 2. Navigate to Task Workspace
    await page.goto('/studio/tasks');
    await page.waitForLoadState('networkidle');

    // Ensure Kanban board is visible
    const kanbanBoard = page.locator('#cora-task-kanban-board');
    await expect(kanbanBoard).toBeVisible({ timeout: 10000 });

    // 3. Open first task card
    const firstTask = page.locator('.cora-task-card').first();
    await expect(firstTask).toBeVisible();
    await firstTask.click();

    // 4. Verify Task Drawer opened
    const drawer = page.locator('#cora-task-drawer');
    await expect(drawer).toBeVisible({ timeout: 5000 });

    // 5. Verify ALL Dedicated Task Management elements exist
    await expect(page.locator('#drawer-task-id')).toBeVisible();
    await expect(page.locator('#drawer-task-status-select')).toBeVisible();
    await expect(page.locator('#drawer-task-priority-select')).toBeVisible();
    await expect(page.locator('#drawer-task-title-input')).toBeVisible();
    await expect(page.locator('#drawer-task-assignee-select')).toBeVisible();
    await expect(page.locator('#drawer-task-client-input')).toBeVisible();
    await expect(page.locator('#drawer-task-due-date-input')).toBeVisible();
    await expect(page.locator('#drawer-task-category-select')).toBeVisible();
    await expect(page.locator('#drawer-subtasks-list')).toBeVisible();
    await expect(page.locator('#drawer-task-notes-input')).toBeVisible();
    await expect(page.locator('#drawer-asset-links-list')).toBeVisible();
    await expect(page.locator('#drawer-comments-feed')).toBeVisible();

    // 6. Verify ZERO CRM Lead buttons exist
    const waLeadBtn = drawer.locator('a:has-text("WhatsApp Client"), #drawer-task-wa-btn');
    expect(await waLeadBtn.count()).toBe(0);
    const callLeadBtn = drawer.locator('a:has-text("Call Client"), #drawer-task-call-btn');
    expect(await callLeadBtn.count()).toBe(0);

    // 7. Test Subtask Checklist Interaction
    const subtaskCheckboxes = page.locator('#drawer-subtasks-list input[type="checkbox"]');
    const initialCheckCount = await subtaskCheckboxes.count();
    expect(initialCheckCount).toBeGreaterThan(0);

    // Toggle first subtask checkbox
    const firstCheckbox = subtaskCheckboxes.first();
    const initialChecked = await firstCheckbox.isChecked();
    await firstCheckbox.click();
    await page.waitForTimeout(300);
    expect(await firstCheckbox.isChecked()).toBe(!initialChecked);

    // 8. Test Adding a New Subtask
    const newSubtaskInput = page.locator('#drawer-new-subtask-input');
    await newSubtaskInput.fill('Run final 4K export test run');
    await page.locator('form:has(#drawer-new-subtask-input) button[type="submit"]').click();
    await page.waitForTimeout(300);

    // Verify new subtask item is rendered in the list
    await expect(page.locator('#drawer-subtasks-list:has-text("Run final 4K export test run")')).toBeVisible();

    // 9. Test Posting a Team Work Note / Comment
    const commentInput = page.locator('#drawer-new-comment-input');
    await commentInput.fill('Color correction LUT applied and approved by senior retoucher.');
    await page.locator('form:has(#drawer-new-comment-input) button[type="submit"]').click();
    await page.waitForTimeout(300);

    // Verify comment appears in work log
    await expect(page.locator('#drawer-comments-feed:has-text("Color correction LUT applied")')).toBeVisible();

    // 10. Capture Live Screenshot of Redesigned Task Drawer
    await page.screenshot({
        path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/task-drawer-redesigned-live.png',
        fullPage: false
    });
    console.log('Task Details & Work Management Drawer E2E test passed successfully!');
});
