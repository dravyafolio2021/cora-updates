import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('Diagnose forms page with super admin preview', async ({ page }) => {
  const consoleErrors: string[] = [];
  page.on('pageerror', (exception) => {
    consoleErrors.push(`PAGE ERROR: ${exception.message}\n${exception.stack}`);
  });
  page.on('console', (msg) => {
    if (msg.type() === 'error') {
      consoleErrors.push(`CONSOLE ERROR: ${msg.text()}`);
    } else {
      console.log(`CONSOLE [${msg.type()}]: ${msg.text()}`);
    }
  });

  await login(page);
  console.log("Navigating to /workspace/forms...");
  await page.goto('/workspace/forms');
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(2000);

  // Switch role to cora_super_admin
  console.log("Opening bottom profile popover...");
  await page.click('.cora-user-footer');
  await expect(page.locator('#cora-profile-popover')).toBeVisible();

  console.log("Selecting cora_super_admin role preview...");
  const dropdown = page.locator('#cora-profile-popover .cora-role-preview-select');
  await dropdown.selectOption('cora_super_admin');
  await page.waitForTimeout(4000);

  console.log("URL after switching role:", page.url());
  await page.screenshot({ path: 'test-results/forms-super-admin.png', fullPage: true });

  if (consoleErrors.length > 0) {
    console.log("--- FOUND CONSOLE/PAGE ERRORS ---");
    consoleErrors.forEach(err => console.log(err));
  } else {
    console.log("No console/page errors found!");
  }
});
