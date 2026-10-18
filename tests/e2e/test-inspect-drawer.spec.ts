import { test, expect } from '@playwright/test';
import { login } from './helpers';
import * as path from 'path';

test('Verify AI MCP Gateway button and bottom-slide docked drawer', async ({ page }) => {
  const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/58e87118-a119-4a20-9906-b30972fba7f6';

  await page.setViewportSize({ width: 1280, height: 850 });
  await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
  await page.goto('/workspace/blogs?ct=ct-library');
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(1000);

  // 1. Inspect the button
  const mcpBtn = page.locator('#cora-btn-ai-mcp-gateway');
  await expect(mcpBtn).toBeVisible();

  const btnBox = await mcpBtn.boundingBox();
  console.log('Button bounding box:', btnBox);

  // Capture button screenshot in tabs bar
  await page.screenshot({
    path: path.join(artifactDir, 'button_gradient.png')
  });

  // Verify button dimensions & styling
  expect(btnBox!.height).toBeLessThanOrEqual(32);
  expect(btnBox!.height).toBeGreaterThanOrEqual(26);

  // 2. Click button to open drawer
  await mcpBtn.click();
  await page.waitForTimeout(500);

  const drawer = page.locator('#cora-workspace-developer-gateway-drawer');
  await expect(drawer).toBeVisible();

  const drawerBox = await drawer.boundingBox();
  console.log('Drawer bounding box:', drawerBox);

  const drawerStyles = await drawer.evaluate((el) => {
    const s = window.getComputedStyle(el);
    return {
      display: s.display,
      position: s.position,
      top: s.top,
      bottom: s.bottom,
      left: s.left,
      width: s.width,
      height: s.height,
      transform: s.transform,
      zIndex: s.zIndex,
      borderRadius: s.borderRadius,
      boxShadow: s.boxShadow,
      borderTop: s.borderTop,
      borderLeft: s.borderLeft
    };
  });
  console.log('Drawer computed styles:', drawerStyles);

  // Check docked drawer rules
  expect(drawerBox!.y).toBeGreaterThanOrEqual(50); // Below global topbar (52px / 55px)
  expect(drawerStyles.boxShadow).toBe('none'); // No floating shadow
  expect(drawerStyles.borderRadius).toBe('0px'); // Clean architectural boundary

  await page.screenshot({
    path: path.join(artifactDir, 'drawer_docked_bottom.png')
  });

  // 3. Test Escape key dismissal
  await page.keyboard.press('Escape');
  await page.waitForTimeout(400);

  const isCollapsedAfterEsc = await drawer.evaluate((el) => el.classList.contains('collapsed'));
  console.log('Drawer collapsed after Escape:', isCollapsedAfterEsc);
  expect(isCollapsedAfterEsc).toBe(true);
});

test('Verify mobile bottom sheet drawer and button', async ({ page }) => {
  const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/58e87118-a119-4a20-9906-b30972fba7f6';

  await page.setViewportSize({ width: 390, height: 844 });
  await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
  await page.goto('/workspace/blogs?ct=ct-library');
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(1000);

  const mcpBtn = page.locator('#cora-btn-ai-mcp-gateway');
  await expect(mcpBtn).toBeVisible();

  // Click on mobile
  await mcpBtn.click();
  await page.waitForTimeout(500);

  const drawer = page.locator('#cora-workspace-developer-gateway-drawer');
  await expect(drawer).toBeVisible();

  const mobileBox = await drawer.boundingBox();
  console.log('Mobile drawer box:', mobileBox);

  await page.screenshot({
    path: path.join(artifactDir, 'drawer_mobile_bottom.png')
  });
});
