import { test, expect } from '@playwright/test';
import { login } from './helpers';
import * as path from 'path';

test('Verify Multi-Model AI Connectors and Autonomous Campaigns in MCP Drawer', async ({ page }) => {
  const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/58e87118-a119-4a20-9906-b30972fba7f6';

  await page.setViewportSize({ width: 1280, height: 850 });
  await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

  // Navigate to Content Suite
  await page.goto('/workspace/blogs?ct=ct-library');
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(1000);

  // 1. Click standard living MCP button to open drawer
  const mcpBtn = page.locator('#cora-btn-ai-mcp-gateway');
  await expect(mcpBtn).toBeVisible();
  await mcpBtn.click();
  await page.waitForTimeout(600);

  const drawer = page.locator('#cora-workspace-developer-gateway-drawer');
  await expect(drawer).toBeVisible();
  await expect(drawer).not.toHaveClass(/collapsed/);

  // 2. Verify Workspace ID context is resolved (NOT #0)
  const wsBadge = drawer.locator('text=/Workspace ID #([1-9][0-9]*)/').first();
  await expect(wsBadge).toBeVisible();
  const wsBadgeText = await wsBadge.innerText();
  console.log('Resolved Workspace Context:', wsBadgeText);
  expect(wsBadgeText).not.toContain('#0');

  // 3. Tab 2: Connect AI Agent (Multi-Model Connectors)
  const connectTabBtn = page.locator('#cora-ws-gw-btn-connect');
  await expect(connectTabBtn).toBeVisible();
  await connectTabBtn.click();
  await page.waitForTimeout(400);

  const connectPane = page.locator('#cora-ws-pane-connect');
  await expect(connectPane).toBeVisible();

  // Test Claude view
  const claudeSubTab = page.locator('#cora-eco-btn-claude');
  await expect(claudeSubTab).toBeVisible();
  await claudeSubTab.click();
  await page.waitForTimeout(300);
  await expect(page.locator('#cora-ws-claude-cfg')).toBeVisible();
  await page.screenshot({ path: path.join(artifactDir, 'mcp_connect_claude.png') });

  // Test Gemini view
  const geminiSubTab = page.locator('#cora-eco-btn-gemini');
  await expect(geminiSubTab).toBeVisible();
  await geminiSubTab.click();
  await page.waitForTimeout(300);
  await expect(page.locator('#cora-ws-gemini-code')).toBeVisible();
  await page.screenshot({ path: path.join(artifactDir, 'mcp_connect_gemini.png') });

  // Test ChatGPT view
  const chatgptSubTab = page.locator('#cora-eco-btn-chatgpt');
  await expect(chatgptSubTab).toBeVisible();
  await chatgptSubTab.click();
  await page.waitForTimeout(300);
  await expect(page.locator('#cora-ws-openapi-url')).toBeVisible();
  await page.screenshot({ path: path.join(artifactDir, 'mcp_connect_chatgpt.png') });

  // 4. Tab 3: Autonomous Campaigns Command Center
  const campaignsTabBtn = page.locator('#cora-ws-gw-btn-campaigns');
  await expect(campaignsTabBtn).toBeVisible();
  await campaignsTabBtn.click();
  await page.waitForTimeout(600);

  const campaignsPane = page.locator('#cora-ws-pane-campaigns');
  await expect(campaignsPane).toBeVisible();

  // Verify pulse strip
  await expect(campaignsPane.locator('text=Active Campaigns').first()).toBeVisible();
  await expect(campaignsPane.locator('text=Average SEO Score').first()).toBeVisible();
  await expect(campaignsPane.locator('text=Average GEO Score').first()).toBeVisible();

  // Verify Active Campaign Matrix card & entries
  await expect(campaignsPane.locator('text=Active Campaign Matrix').first()).toBeVisible();
  await expect(campaignsPane.locator('text=CREATE AUTONOMOUS CAMPAIGN').first()).toBeVisible();

  // Test 1-Click "Run Now" trigger
  const runNowBtn = campaignsPane.locator('button:has-text("Run Now")').first();
  if (await runNowBtn.isVisible()) {
    console.log('Triggering autonomous campaign run...');
    await runNowBtn.click();
    await page.waitForTimeout(3000); // Wait for pipeline execution
  }

  // Verify Autonomous Activity Log feed exists
  await expect(campaignsPane.locator('text=Live Autonomous Publication Stream').first()).toBeVisible();

  await page.screenshot({ path: path.join(artifactDir, 'mcp_autonomous_campaigns.png') });

  // 5. Close drawer via Escape key
  await page.keyboard.press('Escape');
  await page.waitForTimeout(400);
  await expect(drawer).toHaveClass(/collapsed/);
});
