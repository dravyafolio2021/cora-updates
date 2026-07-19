import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('verify sidebar branding and collapsibility', async ({ page }) => {
  await login(page);
  await page.goto('/workspace/dashboard');
  
  // Wait for workspace layout to render
  await page.waitForSelector('.cora-sidebar');
  
  // Check workspace name is present
  const workspaceTitle = page.locator('.cora-studio-info');
  await expect(workspaceTitle).toContainText(/Cora Real Estate|E2E Testing Office|E2E Agency/);
  
  // Take screenshot of expanded sidebar
  await page.screenshot({ path: 'tests/e2e/sidebar-expanded.png' });
  
  // Find toggle button and collapse
  const toggleBtn = page.locator('#cora-sidebar-toggle');
  await expect(toggleBtn).toBeVisible();
  
  await toggleBtn.click();
  
  // Wait short transition
  await page.waitForTimeout(500);
  
  // Check sidebar collapsed class
  const sidebar = page.locator('.cora-sidebar');
  await expect(sidebar).toHaveClass(/collapsed-sidebar/);
  
  // Take screenshot of collapsed sidebar
  await page.screenshot({ path: 'tests/e2e/sidebar-collapsed.png' });
});

test('verify sidebar simple search filtering', async ({ page }) => {
  await login(page);
  await page.goto('/workspace/dashboard');
  
  await page.waitForSelector('.cora-sidebar');
  
  const searchInput = page.locator('#cora-sidebar-search-input');
  await expect(searchInput).toBeVisible();
  
  // Verify both elements are initially visible
  const dashboardItem = page.locator('li[data-target="dashboard"]');
  const vaultItem = page.locator('li[data-target="vault"]');
  await expect(dashboardItem).toBeVisible();
  await expect(vaultItem).toBeVisible();
  
  // Type query that matches "Vault" but not "Dashboard"
  await searchInput.fill('Vault');
  
  // Verify Dashboard is hidden and Secure Vault is visible
  await expect(dashboardItem).toBeHidden();
  await expect(vaultItem).toBeVisible();
  
  // Clear search input
  await searchInput.fill('');
  
  // Verify both are visible again
  await expect(dashboardItem).toBeVisible();
  await expect(vaultItem).toBeVisible();
});
