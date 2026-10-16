import { test, expect } from '@playwright/test';
import { login } from './helpers';
import * as path from 'path';

test('Verify AI Quick button opens and toggles Cora AI panel cleanly on desktop', async ({ page }) => {
  const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/a61fdc7a-f854-4606-9d91-f2a9f6b6294c';

  await page.setViewportSize({ width: 1280, height: 850 });
  await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
  await page.goto('/workspace/projects');
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(800);

  const btn = page.locator('#cora-quick-ai-btn');
  await expect(btn).toBeVisible();

  const sidebar = page.locator('#cora-ai-sidebar');
  // Initially collapsed / hidden
  await expect(sidebar).toHaveClass(/collapsed/);

  // 1. Click AI Quick button to open
  await btn.click();
  await page.waitForTimeout(500);

  // Assert AI sidebar is now OPEN and visible
  await expect(sidebar).toBeVisible();
  await expect(sidebar).not.toHaveClass(/collapsed/);

  // Verify chat input is present inside the AI sidebar
  const chatInput = sidebar.locator('#cora-sidebar-chat-input');
  await expect(chatInput).toBeVisible();

  // Capture screenshot of opened AI panel on desktop
  await page.screenshot({
    path: path.join(artifactDir, 'cora_ai_panel_open_desktop.png')
  });

  // 2. Click AI Quick button again to toggle close
  await btn.click();
  await page.waitForTimeout(500);

  // Assert AI sidebar is now collapsed
  await expect(sidebar).toHaveClass(/collapsed/);

  // 3. Re-open and verify close via backdrop
  await btn.click();
  await page.waitForTimeout(500);
  await expect(sidebar).not.toHaveClass(/collapsed/);

  // Click on the backdrop (e.g. click at coordinates (200, 300) in the workspace body)
  await page.mouse.click(200, 300);
  await page.waitForTimeout(500);
  await expect(sidebar).toHaveClass(/collapsed/);

  // 4. Re-open and verify close via the '✕' close button in the panel header
  await btn.click();
  await page.waitForTimeout(500);
  await expect(sidebar).not.toHaveClass(/collapsed/);

  const closeBtn = sidebar.locator('button[title="Close Panel"]');
  await closeBtn.click();
  await page.waitForTimeout(500);
  await expect(sidebar).toHaveClass(/collapsed/);

  console.log('Desktop Cora AI Quick Button toggle and dismiss methods verified 100% successfully!');
});
