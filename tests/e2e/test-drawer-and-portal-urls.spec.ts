import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('verify drawer is closed by default, opens on click, and supports custom name & token portal URLs', async ({ page }) => {
    page.on('pageerror', exception => {
        console.log(`Uncaught exception: "${exception.message}"`);
    });

    // 1. Test Easy-to-Remember Name Public Portal URL
    await page.goto('/studio/client-portal?token=rohan-verma');
    await page.waitForLoadState('networkidle');

    await expect(page.locator('#portal-tab-overview')).toBeVisible({ timeout: 10000 });
    await expect(page.locator('text=Rohan Verma').first()).toBeVisible();

    // 2. Login & navigate to Clients Directory
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
    await page.goto('/studio/clients');
    await page.waitForLoadState('networkidle');

    // 3. Verify drawer is closed / off-screen on initial page load
    const drawer = page.locator('#cora-client-drawer');
    await expect(drawer).not.toHaveClass(/open/);

    // Capture screenshot showing drawer closed on initial load
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/clients-directory-drawer-closed.png' });

    // 4. Click client table row to open drawer
    const clientRow = page.locator('.client-table-row').first();
    await clientRow.click();
    await page.waitForTimeout(400);

    await expect(drawer).toHaveClass(/open/);
    await expect(page.locator('#drawer-client-name')).toBeVisible();

    // 5. Check Portal Security tab and verify both URL inputs
    await page.locator('#drawer-tab-btn-portal').click();
    await page.waitForTimeout(300);

    const easyUrlInput = page.locator('#drawer-portal-easy-input');
    const tokenUrlInput = page.locator('#drawer-portal-input');

    await expect(easyUrlInput).toBeVisible();
    await expect(tokenUrlInput).toBeVisible();

    const easyVal = await easyUrlInput.inputValue();
    const tokenVal = await tokenUrlInput.inputValue();

    console.log('Friendly Name URL:', easyVal);
    console.log('Secure Token URL:', tokenVal);

    expect(easyVal).toContain('/client-portal?token=');
    expect(tokenVal).toContain('/client-portal?token=cora_clt_');

    // Capture screenshot of Portal Security tab with dual URL sharing options
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/client-drawer-dual-portal-urls.png' });

    // 6. Close drawer
    await page.locator('#btn-close-client-drawer').click();
    await page.waitForTimeout(400);
    await expect(drawer).not.toHaveClass(/open/);
});
