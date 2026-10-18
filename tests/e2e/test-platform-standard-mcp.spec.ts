import { test, expect } from '@playwright/test';
import { login } from './helpers';
import * as path from 'path';

test('Verify standard MCP button and TopBar across multiple platform views', async ({ page }) => {
  const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/58e87118-a119-4a20-9906-b30972fba7f6';

  await page.setViewportSize({ width: 1280, height: 850 });
  await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

  // 1. View: Content Suite
  await page.goto('/workspace/blogs?ct=ct-library');
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(800);

  const contentMcpBtn = page.locator('#cora-content-tabs .cora-ai-alive-btn');
  await expect(contentMcpBtn).toBeVisible();
  const contentTabs = page.locator('#cora-content-tabs');
  const tabsBox = await contentTabs.boundingBox();
  expect(tabsBox!.height).toBeGreaterThanOrEqual(40);

  await page.screenshot({
    path: path.join(artifactDir, 'standard_topbar_content.png')
  });

  // 2. View: Clients (rendered via cora_render_workspace_header)
  await page.goto('/workspace/clients');
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(800);

  const clientsMcpBtn = page.locator('.cora-sticky-sub-tabs .cora-ai-alive-btn');
  await expect(clientsMcpBtn).toBeVisible();
  await page.screenshot({
    path: path.join(artifactDir, 'standard_topbar_clients.png')
  });

  // Click MCP button on Clients view to verify it opens the docked gateway drawer
  await clientsMcpBtn.click();
  await page.waitForTimeout(400);

  const drawer = page.locator('#cora-workspace-developer-gateway-drawer');
  await expect(drawer).toBeVisible();
  expect(await drawer.evaluate(el => !el.classList.contains('collapsed'))).toBe(true);

  // Close via Escape
  await page.keyboard.press('Escape');
  await page.waitForTimeout(300);
  expect(await drawer.evaluate(el => el.classList.contains('collapsed'))).toBe(true);

  // 3. View: Emails
  await page.goto('/workspace/emails');
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(800);

  const emailsMcpBtn = page.locator('#cora-emails-subtabs .cora-ai-alive-btn');
  await expect(emailsMcpBtn).toBeVisible();
  await page.screenshot({
    path: path.join(artifactDir, 'standard_topbar_emails.png')
  });

  // 4. View: Dashboard (No subtabs, so global header MCP button should be visible)
  await page.goto('/workspace/dashboard');
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(800);

  const headerMcpBtn = page.locator('#cora-header-mcp-btn');
  await expect(headerMcpBtn).toBeVisible();
  await page.screenshot({
    path: path.join(artifactDir, 'standard_topbar_dashboard.png')
  });
});
