import { test, expect } from '@playwright/test';
import { login } from './helpers';
import * as path from 'path';

test.describe('Workspace Tour Experience Revamp', () => {

  test('Verify elevated tour UI, radiant spotlight, progress bar, category badges, and interactive step dots', async ({ page }) => {
    const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/e7db172d-afc3-4021-b0cc-a3073c916f94';

    await page.setViewportSize({ width: 1366, height: 850 });
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

    await page.goto('/workspace/dashboard?industry=photography_studio');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1000);

    // 1. Trigger the Product Tour
    await page.evaluate(() => {
      window.coraStartProductTour();
    });
    await page.waitForTimeout(500);

    // 2. Verify Backdrop and Spotlight
    const backdrop = page.locator('.cora-tour-backdrop');
    await expect(backdrop).toBeVisible();
    await expect(backdrop).toHaveClass(/active/);

    const spotlight = page.locator('#cora-tour-spotlight');
    await expect(spotlight).toBeVisible();
    await expect(spotlight).toHaveClass(/active/);

    // Verify Popover card elements
    const popover = page.locator('#cora-tour-popover');
    await expect(popover).toBeVisible();
    await expect(popover).toHaveClass(/active/);

    const progressBar = page.locator('#cora-tour-progress-bar');
    await expect(progressBar).toBeVisible();

    const stepNum = page.locator('#cora-tour-step-num');
    await expect(stepNum).toHaveText('1');

    const category = page.locator('#cora-tour-category');
    await expect(category).toHaveText('TELEMETRY & KPIS');

    const iconTile = page.locator('#cora-tour-icon-tile');
    await expect(iconTile).toBeVisible();
    const iconSvg = iconTile.locator('svg');
    await expect(iconSvg).toBeVisible();

    const title = page.locator('#cora-tour-title');
    await expect(title).toHaveText('1. Workspace Telemetry & KPIs');

    const dots = page.locator('.cora-tour-step-dot');
    expect(await dots.count()).toBe(12);

    // Step 1 Screenshot
    await page.screenshot({
      path: path.join(artifactDir, 'tour_step_1_telemetry_prominent.png'),
      fullPage: false
    });
    console.log('Step 1 captured successfully.');

    // 3. Advance to Step 2 (Search)
    const nextBtn = page.locator('#cora-tour-next');
    await nextBtn.click();
    await page.waitForTimeout(400);

    await expect(stepNum).toHaveText('2');
    await expect(category).toHaveText('COMMAND & AI');
    await expect(title).toHaveText('2. Universal Command Search & Voice AI');

    // Step 2 Screenshot
    await page.screenshot({
      path: path.join(artifactDir, 'tour_step_2_search_prominent.png'),
      fullPage: false
    });
    console.log('Step 2 captured successfully.');

    // 4. Advance to Step 3 (Quick Actions)
    await nextBtn.click();
    await page.waitForTimeout(400);

    await expect(stepNum).toHaveText('3');
    await expect(category).toHaveText('QUICK ACTIONS');

    // Step 3 Screenshot
    await page.screenshot({
      path: path.join(artifactDir, 'tour_step_3_actions_prominent.png'),
      fullPage: false
    });
    console.log('Step 3 captured successfully.');

    // 5. Test Interactive Step Dot Jump (Click Step 11: AI Co-Founder)
    const dot11 = dots.nth(10);
    await dot11.click();
    await page.waitForTimeout(400);

    await expect(stepNum).toHaveText('11');
    await expect(category).toHaveText('AI & MCP GATEWAY');

    // Step 11 Screenshot
    await page.screenshot({
      path: path.join(artifactDir, 'tour_step_11_ai_gateway_prominent.png'),
      fullPage: false
    });
    console.log('Step 11 captured successfully.');

    // 6. Test Escape key to end tour
    await page.keyboard.press('Escape');
    await page.waitForTimeout(300);

    await expect(popover).not.toHaveClass(/active/);
    await expect(backdrop).not.toHaveClass(/active/);
    console.log('Tour ended cleanly on Escape key.');
  });
});
