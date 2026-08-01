import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('verify prospect drawer form validation and save functionality', async ({ page }) => {
    page.on('pageerror', exception => {
        console.log(`Uncaught exception: "${exception.message}"`);
    });
    await login(page);
    await page.goto('/workspace/leads');
    await page.waitForLoadState('networkidle');

    // Wait for Kanban board to load
    const card = page.locator('.cora-lead-card').first();
    await expect(card).toBeVisible({ timeout: 10000 });
    await card.click();

    // Verify side drawer opens
    const drawer = page.locator('#cora-lead-detail-drawer');
    await expect(drawer).toBeVisible({ timeout: 5000 });

    // Verify email field is NOT corrupted with "Shoot:" text
    const emailInput = page.locator('#cora-drawer-input-email');
    await expect(emailInput).toBeVisible({ timeout: 5000 });
    const emailVal = await emailInput.inputValue();
    expect(emailVal).not.toContain('Shoot:');

    // Test Invalid Email Validation
    await emailInput.fill('invalid-email-string');
    const saveBtn = page.locator('button:has-text("Save Deal Changes")');
    await saveBtn.click();

    // Red error highlighting applied
    await expect(emailInput).toHaveClass(/border-rose-500/);

    // Test Successful Save with Valid Data
    await page.locator('#cora-drawer-input-names').fill('Valid Enterprise Client');
    await emailInput.fill('contact@enterprise.com');
    await page.locator('#cora-drawer-input-phone').fill('+91 98765 43210');

    // Expand Deal Budget accordion
    await page.locator('button:has-text("Deal Budget & Intent Priority")').click();
    await page.waitForTimeout(300);

    await page.locator('#cora-drawer-input-price').fill('450000');

    await saveBtn.click();

    // Save Toast appears
    const toastContainer = page.locator('#cora-toast-container');
    await expect(toastContainer).toBeVisible({ timeout: 5000 });
    await expect(toastContainer).toContainText('Lead record updated successfully');
});
