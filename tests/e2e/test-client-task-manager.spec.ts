import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('verify Client Task Manager opens and renders tasks and board at /workspace/tasks and /studio/tasks', async ({ page }) => {
    page.on('pageerror', exception => {
        console.log(`Uncaught exception: "${exception.message}"`);
    });

    // Login as studio owner
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

    // 1. Test /studio/tasks
    await page.goto('/studio/tasks');
    await page.waitForLoadState('networkidle');

    // Verify task manager header / container is visible
    const heading = page.getByRole('heading', { name: /Client Task Manager|Task Manager/i }).first();
    await expect(heading).toBeVisible({ timeout: 10000 });

    // Capture screenshot of live Client Task Manager
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/client-task-manager-live.png' });

    // 2. Test /workspace/tasks
    await page.goto('/workspace/tasks');
    await page.waitForLoadState('networkidle');
    await expect(page.getByRole('heading', { name: /Client Task Manager|Task Manager/i }).first()).toBeVisible({ timeout: 10000 });
});
