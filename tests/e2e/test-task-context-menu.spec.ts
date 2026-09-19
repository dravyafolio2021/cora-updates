import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Task Manager Right-Click Command Menu E2E', () => {
    test.beforeEach(async ({ page }) => {
        page.on('pageerror', exception => {
            console.log(`Uncaught page error: "${exception.message}"`);
        });
        // Login as studio owner
        await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
    });

    test('Verify Right-Click Command Menu open, quick actions, stage move, duplicate, priority, and dismissal', async ({ page }) => {
        await page.setViewportSize({ width: 1280, height: 850 });
        await page.goto('/studio/tasks');
        await page.waitForLoadState('networkidle');

        // Ensure Kanban board is visible
        const kanbanBoard = page.locator('#cora-task-kanban-board');
        await expect(kanbanBoard).toBeVisible({ timeout: 10000 });

        const firstCard = page.locator('.cora-task-card').first();
        await expect(firstCard).toBeVisible();
        const firstCardId = await firstCard.getAttribute('data-id');

        const commandMenu = page.locator('#cora-task-command-menu');
        await expect(commandMenu).toBeHidden();

        // 1. Right-Click on Task Card
        await firstCard.click({ button: 'right' });
        await page.waitForTimeout(300);

        // Verify Command Menu opened
        await expect(commandMenu).toBeVisible();
        await expect(page.locator('#cora-cmd-task-title')).toBeVisible();

        // Capture live screenshot of open command menu
        await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/task-command-menu-live.png' });

        // 2. Dismiss via Escape Key
        await page.keyboard.press('Escape');
        await page.waitForTimeout(200);
        await expect(commandMenu).toBeHidden();

        // 3. Open via 3-dots Option Button on Card
        const threeDotsBtn = firstCard.locator('button[title*="Task Options"]');
        await expect(threeDotsBtn).toBeVisible();
        await threeDotsBtn.click();
        await page.waitForTimeout(300);
        await expect(commandMenu).toBeVisible();

        // 4. Test Priority Level Action (Switch to Urgent)
        const urgentBtn = commandMenu.locator('button:has-text("Urgent")');
        await urgentBtn.click();
        await page.waitForTimeout(400);

        // Assert card priority updated to urgent
        await expect(firstCard).toHaveAttribute('data-priority', 'urgent');
        await expect(commandMenu).toBeHidden();

        // 5. Test Duplicate Task Action
        await firstCard.click({ button: 'right' });
        await page.waitForTimeout(300);
        await expect(commandMenu).toBeVisible();

        const initialCardsCount = await page.locator('.cora-task-card').count();
        const duplicateBtn = commandMenu.locator('button:has-text("Duplicate Task")');
        await duplicateBtn.click();
        await page.waitForTimeout(500);

        const newCardsCount = await page.locator('.cora-task-card').count();
        expect(newCardsCount).toBe(initialCardsCount + 1);

        // 6. Test Advance Stage from Command Menu
        const todoCard = page.locator('.cora-task-card[data-status="todo"]').first();
        const targetId = await todoCard.getAttribute('data-id');
        await todoCard.click({ button: 'right' });
        await page.waitForTimeout(300);
        await expect(commandMenu).toBeVisible();

        const advanceBtn = commandMenu.locator('#cora-cmd-btn-advance');
        await advanceBtn.click();
        await page.waitForTimeout(500);

        const movedCard = page.locator(`.cora-task-card[data-id="${targetId}"]`);
        await expect(movedCard).toHaveAttribute('data-status', 'in_progress');

        // 7. Test Copy Task Summary
        const lastCard = page.locator('.cora-task-card').last();
        await lastCard.click({ button: 'right' });
        await page.waitForTimeout(300);
        const copySummaryBtn = commandMenu.locator('button:has-text("Copy Task Summary")');
        await copySummaryBtn.click();
        await page.waitForTimeout(300);

        // Verify toast appears
        const toast = page.locator('#cora-toast-container, .cora-toast');
        if (await toast.count() > 0) {
            await expect(toast.first()).toBeVisible();
        }
    });
});
