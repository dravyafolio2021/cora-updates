import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('verify saving user drawer under studio_owner role does not fail with unauthorized', async ({ page }) => {
  // Log in as Photography Studio Workspace Owner
  await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
  await page.goto('/workspace/team-roles?industry=photography_studio');
  await page.waitForLoadState('networkidle');

  // Locate the member row (studio owner or a team member) and edit
  const memberRow = page.locator('#tab-active-members table tbody tr').first();
  await expect(memberRow).toBeVisible();
  await memberRow.locator('button').filter({ hasText: /edit/i }).first().click();

  // Verify Edit User drawer is visible
  const editDrawer = page.locator('#cora-edit-user-drawer');
  await expect(editDrawer).toBeVisible();

  // Fill in display name (make a small change or just save)
  const nameInput = editDrawer.locator('#edit-display-name');
  await expect(nameInput).toBeVisible();
  const originalName = await nameInput.inputValue();
  
  // Click save button
  const saveBtn = editDrawer.locator('#save-edit-btn');
  await expect(saveBtn).toBeVisible();
  await saveBtn.click();

  // We expect it NOT to show an unauthorized status toast or pill
  // Let's check the toast messages or check if the drawer closed
  const toast = page.locator('#cora-toast-container');
  await expect(toast).toContainText(/profile updated successfully/i);
  await expect(editDrawer).toBeHidden();
});
