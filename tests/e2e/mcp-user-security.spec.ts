import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Cora User MCP Security E2E Tests', () => {

  test('Open user drawer, inspect MCP credentials UI, and regenerate token', async ({ page }) => {
    // 1. Log in and navigate to the members/roles page
    await login(page);
    await page.goto('/workspace/team-roles?industry=real_estate');
    await page.waitForLoadState('networkidle');

    // 2. Locate a member row (e.g. one containing "owner" or "admin")
    const memberRow = page.locator('#tab-active-members table tbody tr').first();
    await expect(memberRow).toBeVisible();

    // 3. Click the edit/manage button on the row to open the Member Edit Drawer
    const manageBtn = memberRow.locator('button').filter({ hasText: /edit/i }).first();
    await expect(manageBtn).toBeVisible();
    await manageBtn.click();

    // 4. Verify the Member Edit Drawer is displayed
    const editDrawer = page.locator('#cora-edit-user-drawer');
    await expect(editDrawer).toBeVisible();

    // 5. Select the "AI & Security" tab in the drawer
    const aiSecurityTabBtn = editDrawer.locator('button[data-drawer-tab="tab-edit-ai-security"]');
    await expect(aiSecurityTabBtn).toBeVisible();
    await aiSecurityTabBtn.click();

    // Verify that the tab contents are visible
    const tabContent = editDrawer.locator('#tab-edit-ai-security');
    await expect(tabContent).toBeVisible();

    // 6. Verify that the MCP Access Configuration credentials block is present
    const mcpTokenInput = tabContent.locator('#edit-user-mcp-token');
    await expect(mcpTokenInput).toBeVisible();

    // Get current token value
    const tokenBefore = await mcpTokenInput.inputValue();
    console.log("Token before regeneration:", tokenBefore);

    // 7. Click the "Generate" button to regenerate bearer token
    const generateBtn = tabContent.locator('button:has-text("Generate")');
    await expect(generateBtn).toBeVisible();
    await generateBtn.click();

    // Confirm that the toast message appears indicating success
    const toast = page.locator('#cora-toast-container');
    await expect(toast).toContainText(/MCP employee token generated successfully/i);

    // Confirm the token input value has changed
    const tokenAfter = await mcpTokenInput.inputValue();
    console.log("Token after regeneration:", tokenAfter);
    expect(tokenAfter).not.toBe(tokenBefore);
    expect(tokenAfter.length).toBeGreaterThan(10);

    // 8. Save the user drawer
    const saveBtn = editDrawer.locator('#save-edit-btn');
    await expect(saveBtn).toBeVisible();
    await saveBtn.click();

    // Confirm success toast for member save
    await expect(toast).toContainText(/Member profile updated|saved/i);
    await expect(editDrawer).toBeHidden();
  });

});
