import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('verify Feature Hub module saving and streamlined client modules', async ({ page }) => {
    page.on('pageerror', exception => {
        console.log(`Uncaught exception: "${exception.message}"`);
    });

    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

    await page.goto('/studio/feature-hub');
    await page.waitForLoadState('networkidle');

    // Verify consolidated modules exist
    await expect(page.locator('text=Client Management & CRM Directory').first()).toBeVisible({ timeout: 10000 });
    await expect(page.locator('text=Client Portal, Contracts & Approvals').first()).toBeVisible();
    await expect(page.locator('text=Agency Partner & Referral Network').first()).toBeVisible();

    // Toggle a module to reveal Save Changes bottom bar
    const clientToggle = page.locator('input[type="checkbox"][value="clients"]').first();
    if (await clientToggle.isVisible()) {
        await clientToggle.click({ force: true });
        await page.waitForTimeout(300);

        // Click Save Changes button
        const saveBtn = page.locator('.cora-fh-save-btn').first();
        await expect(saveBtn).toBeVisible();
        await saveBtn.click();

        // Verify successful save toast
        await expect(page.locator('text=Modules updated successfully')).toBeVisible({ timeout: 10000 });
    }

    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/feature-hub-save-success.png' });
});
