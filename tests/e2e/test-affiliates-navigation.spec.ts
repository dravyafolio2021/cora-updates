import { test, expect } from '@playwright/test';
import { login } from './helpers';
import * as path from 'path';
import * as fs from 'fs';

test.describe('Affiliates & Referrals Sidebar & View Verification', () => {
  test.beforeEach(async ({ page }) => {
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
  });

  test('Desktop: Verify Affiliates in Sidebar & Navigate to Affiliates View', async ({ page }) => {
    await page.setViewportSize({ width: 1440, height: 900 });
    await page.goto('/workspace/dashboard?industry=photography_studio');
    await page.waitForLoadState('networkidle');

    // Verify sidebar item exists under Workspace group
    const affiliateSidebarBtn = page.locator('button, a').filter({ hasText: 'Affiliates & Referrals' }).first();
    await expect(affiliateSidebarBtn).toBeVisible({ timeout: 10000 });

    const outputDir = path.resolve(process.cwd(), 'test_output');
    if (!fs.existsSync(outputDir)) {
      fs.mkdirSync(outputDir, { recursive: true });
    }
    await page.screenshot({ path: path.join(outputDir, 'desktop_sidebar_affiliates.png'), fullPage: false });

    // Click to navigate to Affiliates & Referrals module
    await affiliateSidebarBtn.click();
    await page.waitForLoadState('networkidle');

    await expect(page.locator('h1, h2, h3, span').filter({ hasText: /Affiliate|Referral|Partner/i }).first()).toBeVisible();
    await page.screenshot({ path: path.join(outputDir, 'desktop_affiliates_view.png'), fullPage: false });
  });

  test('Mobile: Verify Affiliates View on Mobile', async ({ page }) => {
    await page.setViewportSize({ width: 390, height: 844 });
    await page.goto('/workspace/affiliates?industry=photography_studio');
    await page.waitForLoadState('networkidle');

    await expect(page.locator('body')).toBeVisible();
    const outputDir = path.resolve(process.cwd(), 'test_output');
    await page.screenshot({ path: path.join(outputDir, 'mobile_affiliates_view.png'), fullPage: false });
  });
});
