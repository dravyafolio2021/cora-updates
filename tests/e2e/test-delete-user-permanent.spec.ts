import { test, expect } from '@playwright/test';
import { login } from './helpers';
import { execFileSync } from 'child_process';

const phpBin = '/Applications/Local.app/Contents/Resources/extraResources/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php';

function provisionTestUser(email: string, username: string, name: string) {
  const phpCode = `
    require 'app/public/wp-load.php';
    $existing = get_user_by('email', '${email}');
    if ($existing) {
      wp_delete_user($existing->ID);
    }
    $user_id = wp_create_user('${username}', 'cora_secure_pass_123', '${email}');
    if (!is_wp_error($user_id)) {
      wp_update_user([
        'ID' => $user_id,
        'display_name' => '${name}',
        'first_name' => explode(' ', '${name}')[0],
        'last_name' => explode(' ', '${name}')[1] ?? '',
        'role' => 'cora_photographer'
      ]);
      update_user_meta($user_id, 'cora_user_status', 'active');
      update_user_meta($user_id, 'cora_agency_id', '2');
      update_user_meta($user_id, 'cora_industry', 'photography_studio');
      echo "USER_CREATED:" . $user_id;
    } else {
      echo "ERROR:" . $user_id->get_error_message();
    }
  `;
  try {
    const out = execFileSync(phpBin, ['-r', phpCode], { stdio: 'pipe' }).toString();
    return out;
  } catch (e) {
    console.error('Provision failed:', e);
    return null;
  }
}

function checkUserExists(email: string): boolean {
  const phpCode = `
    require 'app/public/wp-load.php';
    $u = get_user_by('email', '${email}');
    echo $u ? 'EXISTS' : 'DELETED';
  `;
  try {
    const out = execFileSync(phpBin, ['-r', phpCode], { stdio: 'pipe' }).toString();
    return out.includes('EXISTS');
  } catch (e) {
    return false;
  }
}

test.describe('Permanent Team User Deletion (Workspace Owner Exclusive)', () => {
  const testEmail = 'dev.testmember.delete@cora.local';
  const testUsername = 'test_member_delete';
  const testName = 'Dev Member Deletable';

  test.beforeEach(() => {
    provisionTestUser(testEmail, testUsername, testName);
  });

  test('Workspace Owner can open delete modal, cancel, and permanently delete a team member on Desktop', async ({ page }) => {
    // 1. Log in as Photography Studio Workspace Owner
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

    // 2. Navigate to Team & Roles
    await page.goto('/workspace/team-roles?industry=photography_studio');
    await page.waitForLoadState('networkidle');

    // 3. Verify member exists in the active members list
    const memberRow = page.locator('#active-members-table tbody tr', { hasText: testName }).first();
    await expect(memberRow).toBeVisible({ timeout: 10000 });

    // 4. Verify delete button is present on the member row
    const deleteBtn = memberRow.locator('.cora-delete-user-row-btn, button[title="Permanently Delete Member"]');
    await expect(deleteBtn).toBeVisible();

    // 5. Click delete button on the row to trigger confirmation modal
    await deleteBtn.click();

    // 6. Verify confirmation modal is visible
    const modal = page.locator('#cora-delete-user-modal');
    await expect(modal).toBeVisible();
    await expect(modal.locator('#delete-user-name')).toContainText(testName);
    await expect(modal.locator('#delete-user-email')).toContainText(testEmail);
    await expect(modal).toContainText('Delete Team Member Permanently?');
    await expect(modal).toContainText('This action is irreversible and immediate.');

    // 7. Test Cancel button
    const cancelBtn = modal.locator('button', { hasText: 'Cancel' });
    await cancelBtn.click();
    await expect(modal).toBeHidden();

    // 8. Now open the Edit User Drawer and test the Danger Zone delete button
    const editBtn = memberRow.locator('button[onclick*="openEditUserDrawer"]').first();
    await editBtn.click();

    const editDrawer = page.locator('#cora-edit-user-drawer');
    await expect(editDrawer).toBeVisible({ timeout: 10000 });

    // Switch to Actions tab
    const actionsTabBtn = page.locator('button.drawer-edit-tab[data-drawer-tab="tab-edit-actions"]');
    await actionsTabBtn.click();

    // Verify Danger Zone Delete Button
    const dangerZoneDeleteBtn = page.locator('button[onclick*="coraDeleteCurrentEditingUser"]');
    await expect(dangerZoneDeleteBtn).toBeVisible();
    await dangerZoneDeleteBtn.click();

    // Verify modal is open again
    await expect(modal).toBeVisible();
    await expect(modal.locator('#delete-user-name')).toContainText(testName);

    // 9. Confirm permanent deletion
    const confirmDeleteBtn = page.locator('#cora-confirm-delete-user-btn');
    await confirmDeleteBtn.dispatchEvent('click');

    // 10. Verify confirmation toast
    const toast = page.locator('#cora-toast-container, .cora-toast');
    await expect(toast).toContainText(/permanently deleted/i, { timeout: 10000 });

    // 11. Verify modal is closed
    await expect(modal).toBeHidden();

    // 12. Verify user is removed from DOM table
    await expect(page.locator('#active-members-table tbody tr', { hasText: testName })).toHaveCount(0);

    // 13. Verify user is deleted from WordPress database
    const stillExists = checkUserExists(testEmail);
    expect(stillExists).toBe(false);
  });

  test('Workspace Owner can delete a team member from Mobile Card layout', async ({ page }) => {
    // Set mobile viewport
    await page.setViewportSize({ width: 390, height: 844 });

    // 1. Log in
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

    // 2. Navigate to Team & Roles
    await page.goto('/workspace/team-roles?industry=photography_studio');
    await page.waitForLoadState('networkidle');

    // 3. Find mobile card for the test member
    const mobileCard = page.locator('.active-member-row', { hasText: testName }).first();
    await expect(mobileCard).toBeVisible({ timeout: 10000 });

    // 4. Click delete button on the card
    const deleteBtn = mobileCard.locator('button[title="Permanently Delete User"]');
    await expect(deleteBtn).toBeVisible();
    await deleteBtn.click();

    // 5. Verify modal opens
    const modal = page.locator('#cora-delete-user-modal');
    await expect(modal).toBeVisible();
    await expect(modal.locator('#delete-user-name')).toContainText(testName);

    // 6. Confirm deletion
    const confirmDeleteBtn = page.locator('#cora-confirm-delete-user-btn');
    await confirmDeleteBtn.dispatchEvent('click');

    // 7. Verify confirmation toast
    const toast = page.locator('#cora-toast-container, .cora-toast');
    await expect(toast).toContainText(/permanently deleted/i, { timeout: 10000 });

    // 8. Verify mobile card is removed
    await expect(page.locator('.active-member-row', { hasText: testName })).toHaveCount(0);

    // 9. Verify user is deleted from WordPress database
    const stillExists = checkUserExists(testEmail);
    expect(stillExists).toBe(false);
  });
});
