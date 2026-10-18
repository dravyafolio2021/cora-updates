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

  const subagentArtifactDir = '/Users/shrutian/.gemini/antigravity/brain/a7e05cdb-6c68-4e2c-99f0-28e37435f155';

  // 1. Verify Gemini Tab: Cloud MCP app link visible (#cora-gemini-app-link)
  const geminiSubTab = page.locator('#cora-eco-btn-gemini');
  await expect(geminiSubTab).toBeVisible();
  await geminiSubTab.click();
  await page.waitForTimeout(400);

  const geminiAppLink = page.locator('#cora-gemini-app-link');
  await expect(geminiAppLink).toBeVisible();
  const geminiUrl = await geminiAppLink.inputValue();
  console.log('Gemini Cloud App Link:', geminiUrl);
  expect(geminiUrl).toContain('/mcp');
  await page.screenshot({ path: path.join(artifactDir, 'mcp_connect_gemini.png') });
  try { await page.screenshot({ path: path.join(subagentArtifactDir, 'mcp_connect_gemini.png') }); } catch (e) {}

  // 2. Verify Claude Tab: #cora-eco-btn-claude clicks and shows #cora-claude-cloud-view with app link input (#cora-claude-app-link) and name input (#cora-claude-name)
  const claudeSubTab = page.locator('#cora-eco-btn-claude');
  await expect(claudeSubTab).toBeVisible();
  await claudeSubTab.click();
  await page.waitForTimeout(400);

  const claudeCloudTab = page.locator('#cora-claude-tab-cloud');
  if (await claudeCloudTab.isVisible()) {
    await claudeCloudTab.click();
    await page.waitForTimeout(200);
  }

  const claudeCloudView = page.locator('#cora-claude-cloud-view');
  await expect(claudeCloudView).toBeVisible();

  const claudeAppLink = page.locator('#cora-claude-app-link');
  await expect(claudeAppLink).toBeVisible();
  const claudeUrl = await claudeAppLink.inputValue();
  console.log('Claude Cloud App Link:', claudeUrl);
  expect(claudeUrl).toContain('/mcp');

  const claudeNameInput = page.locator('#cora-claude-name');
  await expect(claudeNameInput).toBeVisible();
  const claudeNameVal = await claudeNameInput.inputValue();
  console.log('Claude Connector Name:', claudeNameVal);
  expect(claudeNameVal).toBeTruthy();

  await page.screenshot({ path: path.join(artifactDir, 'mcp_connect_claude.png') });
  try { await page.screenshot({ path: path.join(subagentArtifactDir, 'mcp_connect_claude.png') }); } catch (e) {}

  // 3. Verify ChatGPT Tab: #cora-eco-btn-chatgpt clicks and shows #cora-chatgpt-mcp-view with server URL input (#cora-chatgpt-app-link) and name input (#cora-chatgpt-name)
  const chatgptSubTab = page.locator('#cora-eco-btn-chatgpt');
  await expect(chatgptSubTab).toBeVisible();
  await chatgptSubTab.click();
  await page.waitForTimeout(400);

  const chatgptMcpTab = page.locator('#cora-chatgpt-tab-mcp');
  if (await chatgptMcpTab.isVisible()) {
    await chatgptMcpTab.click();
    await page.waitForTimeout(200);
  }

  const chatgptMcpView = page.locator('#cora-chatgpt-mcp-view');
  await expect(chatgptMcpView).toBeVisible();

  const chatgptAppLink = page.locator('#cora-chatgpt-app-link');
  await expect(chatgptAppLink).toBeVisible();
  const chatgptUrl = await chatgptAppLink.inputValue();
  console.log('ChatGPT Server URL:', chatgptUrl);
  expect(chatgptUrl).toContain('/mcp');

  const chatgptNameInput = page.locator('#cora-chatgpt-name');
  await expect(chatgptNameInput).toBeVisible();
  const chatgptNameVal = await chatgptNameInput.inputValue();
  console.log('ChatGPT Connector Name:', chatgptNameVal);
  expect(chatgptNameVal).toBeTruthy();

  await page.screenshot({ path: path.join(artifactDir, 'mcp_connect_chatgpt.png') });
  try { await page.screenshot({ path: path.join(subagentArtifactDir, 'mcp_connect_chatgpt.png') }); } catch (e) {}

  // 3b. Verify ChatGPT Agent Skills & MCP Plugins tab (and zero trace of legacy Custom GPT action)
  await expect(page.locator('#cora-chatgpt-tab-action')).toHaveCount(0);
  await expect(page.locator('#cora-chatgpt-action-view')).toHaveCount(0);

  const chatgptSkillsTab = page.locator('#cora-chatgpt-tab-skills');
  await expect(chatgptSkillsTab).toBeVisible();
  await chatgptSkillsTab.click();
  await page.waitForTimeout(300);

  const chatgptSkillsView = page.locator('#cora-chatgpt-skills-view');
  await expect(chatgptSkillsView).toBeVisible();
  await expect(chatgptSkillsView.locator('text=SEO & GEO Content Architect').first()).toBeVisible();
  await page.screenshot({ path: path.join(artifactDir, 'mcp_chatgpt_skills_plugins.png') });

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

test('Verify Redesigned Minimal OAuth 2.1 Consent Screen', async ({ page }) => {
  const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/58e87118-a119-4a20-9906-b30972fba7f6';
  await page.setViewportSize({ width: 1200, height: 900 });
  await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

  // Navigate to OAuth Authorize endpoint for Google Gemini
  await page.goto('/oauth/authorize?response_type=code&client_id=cora_gemini_client&redirect_uri=https://gemini.google.com/apps/callback&scope=*&state=xyz123');
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(1000);

  // Assert clean high-trust elements
  await expect(page.locator('text=Connect Google Gemini to Cora').first()).toBeVisible();
  await expect(page.locator('text=Autonomous Campaigns & Content Engine').first()).toBeVisible();
  await expect(page.locator('text=Website, Themes & Canvas').first()).toBeVisible();
  await expect(page.locator('text=CRM Leads & Client Pipelines').first()).toBeVisible();
  await expect(page.locator('text=Studio Operations & MCP Tools').first()).toBeVisible();
  await expect(page.locator('button:has-text("Authorize Google Gemini")').first()).toBeVisible();

  // Capture screenshot
  await page.screenshot({ path: path.join(artifactDir, 'mcp_oauth_consent_screen_new.png') });
});
