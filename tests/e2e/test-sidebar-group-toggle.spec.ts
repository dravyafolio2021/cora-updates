import { test, expect } from '@playwright/test';
import { login } from './helpers';
import * as path from 'path';

test('Verify collapsible sidebar navigation groups with space optimization', async ({ page }) => {
  const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/a61fdc7a-f854-4606-9d91-f2a9f6b6294c';

  await page.setViewportSize({ width: 1280, height: 850 });
  await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
  
  // Clear any existing localStorage state to test fresh default experience
  await page.goto('/workspace/projects');
  await page.evaluate(() => {
    localStorage.removeItem('cora_sidebar_group_states');
    localStorage.removeItem('cora_sidebar_collapsed_groups');
  });
  await page.reload();
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(600);

  const sidebar = page.locator('.cora-sidebar');
  await expect(sidebar).toBeVisible();

  // 1. Verify Primary Group has NO title/header and is NOT collapsible
  const primaryGroup = page.locator('.cora-nav-group.cora-primary-group');
  await expect(primaryGroup).toBeVisible();
  const primaryGroupLabel = primaryGroup.locator('.cora-nav-group-label');
  expect(await primaryGroupLabel.count()).toBe(0); // Label is removed!
  await expect(primaryGroup).toHaveAttribute('data-collapsible', '0');
  await expect(primaryGroup.locator('li[data-target="dashboard"]')).toBeVisible();

  // 2. Verify all non-primary groups are COLLAPSED by default
  const nonPrimaryGroups = page.locator('.cora-nav-group[data-collapsible="1"]');
  const count = await nonPrimaryGroups.count();
  expect(count).toBeGreaterThan(0);

  for (let i = 0; i < count; i++) {
    const group = nonPrimaryGroups.nth(i);
    await expect(group).toHaveClass(/is-collapsed/);
  }

  // 3. Verify CRM group label is visible with chevron
  const crmGroup = page.locator('.cora-nav-group[data-group="crm"]');
  const crmLabel = crmGroup.locator('.cora-nav-group-label');
  await expect(crmLabel).toBeVisible();
  await expect(crmGroup).toHaveClass(/is-collapsed/);

  // Capture Screenshot of Default State (No Workspace title, all other groups collapsed)
  await sidebar.screenshot({
    path: path.join(artifactDir, 'sidebar_groups_default_collapsed.png'),
  });

  // 4. Click CRM group label to expand it
  await crmLabel.click();
  await page.waitForTimeout(300);
  await expect(crmGroup).not.toHaveClass(/is-collapsed/);
  await expect(crmGroup.locator('li[data-target="projects"]')).toBeVisible();

  // Capture Screenshot with CRM Expanded
  await sidebar.screenshot({
    path: path.join(artifactDir, 'sidebar_groups_crm_expanded.png'),
  });

  // 5. Verify state persistence on page reload: CRM should STAY expanded
  await page.reload();
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(600);

  const crmAfterReload = page.locator('.cora-nav-group[data-group="crm"]');
  await expect(crmAfterReload).not.toHaveClass(/is-collapsed/);
  await expect(crmAfterReload.locator('li[data-target="projects"]')).toBeVisible();

  // 6. User clicks CRM header again to collapse it
  await crmAfterReload.locator('.cora-nav-group-label').click();
  await page.waitForTimeout(300);
  await expect(crmAfterReload).toHaveClass(/is-collapsed/);

  // 7. Reload again: CRM should STAY collapsed
  await page.reload();
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(600);

  const crmAfterSecondReload = page.locator('.cora-nav-group[data-group="crm"]');
  await expect(crmAfterSecondReload).toHaveClass(/is-collapsed/);

  console.log('Sidebar group toggles, default collapsed state, and reload persistence verified 100%!');
});
