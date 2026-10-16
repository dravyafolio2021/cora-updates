import { test, expect } from '@playwright/test';
import { login } from './helpers';
import * as path from 'path';

test('Verify all Super Admin nav items remain visible on load, DOM ready, and reload', async ({ page }) => {
  const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/a61fdc7a-f854-4606-9d91-f2a9f6b6294c';

  await page.setViewportSize({ width: 1280, height: 850 });
  await login(page, 'admin@cora.local', 'cora_secure_pass_123');

  // Go to super-announcements (Platform Control Broadcast Console)
  await page.goto('/workspace/super-announcements');
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(1000);

  const sidebar = page.locator('.cora-sidebar');
  await expect(sidebar).toBeVisible();

  const expectedSuperTargets = [
    'super-admin',
    'super-users',
    'super-finances',
    'super-appeals',
    'super-governance',
    'super-announcements',
    'super-health',
    'super-ai-tokens',
    'super-feature-flags',
    'super-emergency',
    'super-audit',
  ];

  // Verify all 11 items are visible and not hidden by JS
  for (const target of expectedSuperTargets) {
    const item = page.locator(`.cora-nav-item[data-target="${target}"]`);
    await expect(item, `Target ${target} should be present in DOM`).toBeAttached();
    await expect(item, `Target ${target} should be visible`).toBeVisible();
  }

  // Capture screenshot of complete Super Admin sidebar with all 11 items visible
  await sidebar.screenshot({
    path: path.join(artifactDir, 'super_admin_sidebar_all_items_visible.png'),
  });

  // Now reload page and ensure none disappear on refresh
  await page.reload();
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(1000);

  for (const target of expectedSuperTargets) {
    const item = page.locator(`.cora-nav-item[data-target="${target}"]`);
    await expect(item, `Target ${target} should still be visible after reload`).toBeVisible();
  }

  console.log('All Super Admin navigation items verified visible and persistent across reloads!');
});
