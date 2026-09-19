import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('verify Sleek Tabbed Navigation inside Task Details & Work Management Drawer', async ({ page }) => {
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

    // 5. Verify Fixed Header & Property Matrix elements
    await expect(page.locator('#drawer-task-id')).toBeVisible();
    await expect(page.locator('#drawer-task-status-select')).toBeVisible();
    await expect(page.locator('#drawer-task-priority-select')).toBeVisible();
    await expect(page.locator('#drawer-task-title-input')).toBeVisible();
    await expect(page.locator('#drawer-task-assignee-select')).toBeVisible();
    await expect(page.locator('#drawer-task-client-input')).toBeVisible();
    await expect(page.locator('#drawer-task-due-date-input')).toBeVisible();
    await expect(page.locator('#drawer-task-category-select')).toBeVisible();

    // 6. Verify 4 Tab Navigation Buttons are visible
    await expect(page.locator('#task-tab-btn-checklist')).toBeVisible();
    await expect(page.locator('#task-tab-btn-scope')).toBeVisible();
    await expect(page.locator('#task-tab-btn-assets')).toBeVisible();
    await expect(page.locator('#task-tab-btn-activity')).toBeVisible();

    // 7. Verify ZERO CRM Lead buttons exist
    const waLeadBtn = drawer.locator('a:has-text("WhatsApp Client"), #drawer-task-wa-btn');
    expect(await waLeadBtn.count()).toBe(0);
    const callLeadBtn = drawer.locator('a:has-text("Call Client"), #drawer-task-call-btn');
    expect(await callLeadBtn.count()).toBe(0);

    // ── TAB 1: CHECKLIST ──
    await page.locator('#task-tab-btn-checklist').click();
    await expect(page.locator('#task-tab-panel-checklist')).toBeVisible();
    await expect(page.locator('#drawer-subtasks-list')).toBeVisible();

    // Toggle first subtask checkbox
    const subtaskCheckboxes = page.locator('#drawer-subtasks-list input[type="checkbox"]');
    const initialCheckCount = await subtaskCheckboxes.count();
    expect(initialCheckCount).toBeGreaterThan(0);

    const firstCheckbox = subtaskCheckboxes.first();
    const initialChecked = await firstCheckbox.isChecked();
    await firstCheckbox.click();
    await page.waitForTimeout(300);
    expect(await firstCheckbox.isChecked()).toBe(!initialChecked);

    // Add a new subtask
    const newSubtaskInput = page.locator('#drawer-new-subtask-input');
    await newSubtaskInput.fill('Run final 4K export test run');
    await page.locator('form:has(#drawer-new-subtask-input) button[type="submit"]').click();
    await page.waitForTimeout(300);
    await expect(page.locator('#drawer-subtasks-list:has-text("Run final 4K export test run")')).toBeVisible();

    // ── TAB 2: SCOPE & SPECS ──
    await page.locator('#task-tab-btn-scope').click();
    await expect(page.locator('#task-tab-panel-scope')).toBeVisible();
    await expect(page.locator('#task-tab-panel-checklist')).toBeHidden();
    const notesInput = page.locator('#drawer-task-notes-input');
    await expect(notesInput).toBeVisible();
    await notesInput.fill('Deliverables required in ProRes 422 HQ and high-res JPEG with custom color grading LUT applied.');
    await notesInput.blur();
    await page.waitForTimeout(300);

    // ── TAB 3: ASSETS & LINKS ──
    await page.locator('#task-tab-btn-assets').click();
    await expect(page.locator('#task-tab-panel-assets')).toBeVisible();
    await expect(page.locator('#task-tab-panel-scope')).toBeHidden();
    await expect(page.locator('#drawer-asset-links-list')).toBeVisible();

    // Add asset link via inline form
    await page.locator('#drawer-new-link-title').fill('Final ProRes Drive');
    await page.locator('#drawer-new-link-url').fill('https://drive.google.com/test-assets');
    await page.locator('button:has-text("Add Asset Link")').click();
    await page.waitForTimeout(300);
    await expect(page.locator('#drawer-asset-links-list:has-text("Final ProRes Drive")')).toBeVisible();

    // ── TAB 4: ACTIVITY & NOTES ──
    await page.locator('#task-tab-btn-activity').click();
    await expect(page.locator('#task-tab-panel-activity')).toBeVisible();
    await expect(page.locator('#task-tab-panel-assets')).toBeHidden();
    await expect(page.locator('#drawer-comments-feed')).toBeVisible();

    // Add team comment
    const commentInput = page.locator('#drawer-new-comment-input');
    await commentInput.fill('Color correction LUT applied and approved by senior retoucher.');
    await page.locator('form:has(#drawer-new-comment-input) button[type="submit"]').click();
    await page.waitForTimeout(300);
    await expect(page.locator('#drawer-comments-feed:has-text("Color correction LUT applied")')).toBeVisible();

    // Switch back to Checklist tab for clean screenshot
    await page.locator('#task-tab-btn-checklist').click();
    await page.waitForTimeout(300);

    // Capture Live Screenshot of Redesigned Tabbed Task Drawer
    await page.screenshot({
        path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/task-drawer-redesigned-live.png',
        fullPage: false
    });
    console.log('Sleek Tabbed Task Details Drawer E2E test passed successfully!');
});
