import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Task Manager Filter Bar UX Optimization & Mobile Bottom Sheet E2E', () => {
    test.beforeEach(async ({ page }) => {
        page.on('pageerror', exception => {
            console.log(`Uncaught page error: "${exception.message}"`);
        });
        // Login as studio owner
        await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
    });

    test('Desktop: Verify compact single-row filter bar, smart timeframe dropdown, priority, and reset actions', async ({ page }) => {
        await page.setViewportSize({ width: 1280, height: 800 });
        await page.goto('/studio/tasks');
        await page.waitForLoadState('networkidle');

        // Ensure Kanban board is visible
        const kanbanBoard = page.locator('#cora-task-kanban-board');
        await expect(kanbanBoard).toBeVisible({ timeout: 10000 });

        // 1. Verify Desktop Search Input and Compact Selects
        const searchInput = page.locator('#task-search-input');
        await expect(searchInput).toBeVisible();

        const timeframeSelect = page.locator('#task-filter-timeframe');
        await expect(timeframeSelect).toBeVisible();

        const prioritySelect = page.locator('#task-filter-priority');
        await expect(prioritySelect).toBeVisible();

        const clientSelect = page.locator('#task-filter-client');
        await expect(clientSelect).toBeVisible();

        // 2. Test Live Search Filtering
        await searchInput.fill('Turntable');
        await page.waitForTimeout(400);

        // Clear button should become visible
        const clearBtn = page.locator('#task-search-clear-btn');
        await expect(clearBtn).toBeVisible();

        // Reset button should appear
        const resetBtn = page.locator('#cora-task-reset-btn');
        await expect(resetBtn).toBeVisible();

        // Clear search
        await clearBtn.click();
        await page.waitForTimeout(300);
        await expect(searchInput).toHaveValue('');

        // 3. Test Priority Select
        await prioritySelect.selectOption('urgent');
        await page.waitForTimeout(400);
        await expect(resetBtn).toBeVisible();

        // 4. Test Reset Action
        await resetBtn.click();
        await page.waitForTimeout(400);
        await expect(prioritySelect).toHaveValue('all');
        await expect(resetBtn).toBeHidden();

        // Capture Desktop Screenshot
        await page.screenshot({
            path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/task-filter-desktop-compact-live.png',
            fullPage: false
        });
        console.log('Desktop filter verification passed successfully!');
    });

    test('Mobile: Verify ultra-compact bar, bottom-up slide filter sheet, touch chips, and filter application', async ({ page }) => {
        await page.setViewportSize({ width: 390, height: 844 });
        console.log('Navigating to /studio/tasks on mobile viewport...');
        await page.goto('/studio/tasks');
        await page.waitForLoadState('networkidle');

        // Ensure Kanban board is visible
        const kanbanBoard = page.locator('#cora-task-kanban-board');
        await expect(kanbanBoard).toBeVisible({ timeout: 10000 });
        console.log('Kanban board visible on mobile.');

        // 1. Verify Desktop Selects are Hidden on Mobile and Mobile Filter Button is Visible
        const mobileFilterBtn = page.locator('#btn-mobile-task-filters');
        await expect(mobileFilterBtn).toBeVisible();
        console.log('Mobile filter button visible.');

        const desktopTimeframe = page.locator('#task-filter-timeframe');
        await expect(desktopTimeframe).toBeHidden();
        console.log('Desktop timeframe selector hidden on mobile.');

        // 2. Open Mobile Bottom Filter Sheet
        await mobileFilterBtn.click();
        await page.waitForTimeout(400);
        console.log('Clicked mobile filter button.');

        const mobileSheet = page.locator('#cora-task-mobile-filter-sheet');
        await expect(mobileSheet).toHaveClass(/open/);
        console.log('Mobile sheet has open class.');

        // 3. Select Urgent Priority in Mobile Sheet
        console.log('Locating urgent chip...');
        const urgentChip = page.locator('.mobile-pri-btn[data-pri="urgent"]');
        console.log('Checking urgent chip attached...');
        await expect(urgentChip).toBeAttached();
        await page.waitForTimeout(300);
        console.log('Clicking urgent chip...');
        await urgentChip.click({ force: true });
        console.log('Urgent chip clicked. Checking active class...');
        await expect(urgentChip).toHaveClass(/active/);
        console.log('Urgent chip has active class.');

        // 4. Capture Mobile Filter Sheet Open Screenshot
        console.log('Capturing mobile filter sheet open screenshot...');
        await page.screenshot({
            path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/task-filter-mobile-bottom-sheet-live.png',
            fullPage: false
        });
        console.log('Captured mobile filter sheet screenshot.');

        // 5. Apply Filters
        const applyBtn = page.locator('#cora-task-mobile-filter-sheet button:has-text("Apply Filters")');
        await applyBtn.click({ force: true });
        await page.waitForTimeout(400);
        console.log('Clicked Apply Filters.');

        // Mobile Sheet should close
        await expect(mobileSheet).not.toHaveClass(/open/);
        console.log('Mobile sheet closed.');

        // Filter Badge should show count 1
        const mobileBadge = page.locator('#mobile-filter-badge');
        await expect(mobileBadge).toBeVisible();
        await expect(mobileBadge).toHaveText('1');
        console.log('Mobile badge shows active count 1.');

        // 6. Capture Filtered Mobile View
        await page.screenshot({
            path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/task-filter-mobile-applied-live.png',
            fullPage: false
        });

        console.log('Mobile bottom sheet filter verification passed successfully!');
    });
});
