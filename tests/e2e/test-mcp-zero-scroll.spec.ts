import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('Capture MCP Gateway zero-scroll layout across viewports', async ({ page }) => {
  const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/e7db172d-afc3-4021-b0cc-a3073c916f94';

  // 1. Desktop 1366x850
  await page.setViewportSize({ width: 1366, height: 850 });
  await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

  await page.goto('/workspace/blogs?ct=ct-library');
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(600);

  const mcpBtn = page.locator('#cora-btn-ai-mcp-gateway');
  await mcpBtn.click();
  await page.waitForTimeout(600);

  const drawer = page.locator('#cora-workspace-developer-gateway-drawer');
  await expect(drawer).toBeVisible();

  // Verify tab bar has zero scroll (scrollWidth equals clientWidth or fits with zero horizontal overflow)
  const isScrolled = await page.evaluate(() => {
    const bar = document.getElementById('cora-ws-gw-tab-bar');
    if (!bar) return false;
    return bar.scrollWidth > bar.clientWidth + 5;
  });
  console.log('Desktop 1366px bar isScrolled:', isScrolled);
  expect(isScrolled).toBe(false);

  await page.screenshot({ path: `${artifactDir}/mcp_gateway_zero_scroll_desktop.png` });

  // 2. Laptop 1200x800
  await page.setViewportSize({ width: 1200, height: 800 });
  await page.waitForTimeout(400);

  const isScrolled1200 = await page.evaluate(() => {
    const bar = document.getElementById('cora-ws-gw-tab-bar');
    if (!bar) return false;
    return bar.scrollWidth > bar.clientWidth + 5;
  });
  console.log('Laptop 1200px bar isScrolled:', isScrolled1200);
  expect(isScrolled1200).toBe(false);

  await page.screenshot({ path: `${artifactDir}/mcp_gateway_zero_scroll_laptop.png` });

  // 3. Switch to campaigns and skills tabs to confirm active state styling
  const campaignsBtn = page.locator('#cora-ws-gw-btn-campaigns');
  await campaignsBtn.click();
  await page.waitForTimeout(400);
  await page.screenshot({ path: `${artifactDir}/mcp_gateway_campaigns_zero_scroll.png` });

  const skillsBtn = page.locator('#cora-ws-gw-btn-tools');
  await skillsBtn.click();
  await page.waitForTimeout(400);
  await page.screenshot({ path: `${artifactDir}/mcp_gateway_skills_zero_scroll.png` });
});
