import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('verify one-click auto-update trigger overlay and step transitions', async ({ page }) => {
  // Log in as administrator
  await login(page);

  // Intercept the auto-update AJAX endpoint to mock a successful update
  await page.route('**/admin-ajax.php', async (route) => {
    const postData = route.request().postData();
    if (postData && postData.includes('action=cora_trigger_workspace_update')) {
      await route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({ success: true, data: { message: 'Workspace updated successfully!' } }),
      });
    } else {
      await route.continue();
    }
  });

  // Navigate to the auto-update trigger URL
  await page.goto('/wp-admin/admin.php?page=cora-workspace&cora_auto_update=1&target_version=2.9.2');

  // Verify overlay panel is visible
  const overlayPanel = page.locator('#cora-auto-update-overlay-panel');
  await expect(overlayPanel).toBeVisible({ timeout: 15000 });

  // Verify title and version details
  const statusTitle = page.locator('#cora-upgrade-status-title');
  await expect(statusTitle).toContainText('Upgrade');

  // Verify steps and transition states
  // Step 1: Validating administrator authorization should pass
  const step1 = page.locator('#cora-step-1');
  await expect(step1).toContainText('Validating administrator authorization');
  await expect(step1.locator('.step-status')).toContainText('Passed', { timeout: 10000 });

  // Step 2: Downloading update should complete (mocked success)
  const step2 = page.locator('#cora-step-2');
  await expect(step2.locator('.step-status')).toContainText('Complete', { timeout: 10000 });

  // Step 3: Extracting update packages should complete
  const step3 = page.locator('#cora-step-3');
  await expect(step3.locator('.step-status')).toContainText('Complete', { timeout: 10000 });

  // Step 4: Upgrading core modules should complete
  const step4 = page.locator('#cora-step-4');
  await expect(step4.locator('.step-status')).toContainText('Complete', { timeout: 10000 });

  // Finally, status title should change to completed
  await expect(statusTitle).toContainText('Workspace Updated!', { timeout: 15000 });
});

test('verify unauthorized users are blocked from auto-updating', async ({ page }) => {
  // We navigate directly to the workspace auto-update trigger URL without logging in first.
  // Since user is not logged in, they should be redirected or block should be displayed if authorization fails.
  // Let's go to the update URL. WordPress should redirect us to the login page.
  await page.goto('/wp-admin/admin.php?page=cora-workspace&cora_auto_update=1&target_version=2.9.2');

  // Verify we land on the custom login page
  await expect(page.url()).toContain('/workspace/login');
});
