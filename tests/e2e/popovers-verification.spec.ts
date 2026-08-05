import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('verify workspace and profile popovers toggle and close correctly', async ({ page }) => {
  await login(page);
  await page.goto('/workspace/dashboard');
  
  // Wait for workspace layout to render
  await page.waitForSelector('.cora-sidebar');

  const workspaceCard = page.locator('.cora-workspace-card');
  const userFooter = page.locator('.cora-user-footer');
  
  const workspacePopover = page.locator('#cora-workspace-popover');
  const profilePopover = page.locator('#cora-profile-popover');

  // Initially both should be hidden
  await expect(workspacePopover).toBeHidden();
  await expect(profilePopover).toBeHidden();

  // Click switcher -> workspace popover should show, profile popover should be hidden
  await workspaceCard.click({ force: true });
  await expect(workspacePopover).toBeVisible();
  await expect(profilePopover).toBeHidden();

  // Click user footer -> profile popover should show, workspace popover should be hidden
  await userFooter.click({ force: true });
  await expect(profilePopover).toBeVisible();
  await expect(workspacePopover).toBeHidden();

  // Click switcher again -> workspace popover shows, profile popover hides
  await workspaceCard.click({ force: true });
  await expect(workspacePopover).toBeVisible();
  await expect(profilePopover).toBeHidden();

  // Click outside (e.g. on the dashboard main container) -> both should hide
  const mainContainer = page.locator('.cora-main');
  await mainContainer.click({ position: { x: 100, y: 100 } });
  
  await expect(workspacePopover).toBeHidden();
  await expect(profilePopover).toBeHidden();
});
