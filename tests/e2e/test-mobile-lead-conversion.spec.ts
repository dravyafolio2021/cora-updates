import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Mobile-First Lead Directory & Conversion Cards', () => {
  test('verify mobile defaults to Directory subtab and renders decision-focused conversion cards', async ({ page }) => {
    page.on('pageerror', exception => {
      console.log(`Uncaught exception: "${exception.message}"`);
    });

    // Set mobile viewport (iPhone SE resolution)
    await page.setViewportSize({ width: 375, height: 667 });
    await login(page);
    await page.goto('/workspace/leads');
    await page.waitForLoadState('networkidle');

    // 1. Verify Directory subtab is active by default on mobile viewport
    const directoryTabBtn = page.locator('.cora-lead-subtab-btn[data-tab="directory"]');
    await expect(directoryTabBtn).toHaveClass(/active/);

    const directoryPane = page.locator('#cora-lead-pane-directory');
    await expect(directoryPane).not.toHaveClass(/hidden/);

    // 2. Verify Next Step milestone banner is visible on prospect cards in directory pane
    const nextActionBanners = page.locator('#cora-lead-pane-directory .cora-lead-card:has-text("Next Action")');
    await expect(nextActionBanners.first()).toBeVisible();

    // 3. Verify stage-based action CTAs (e.g. Contact Client, Schedule Visit, Convert Deal)
    const actionBtn = page.locator('#cora-lead-pane-directory .cora-lead-card button span').filter({
      hasText: /Contact Client|Schedule Visit|Negotiate|Convert Deal|Converted/
    });
    await expect(actionBtn.first()).toBeVisible();

    // 4. Verify WhatsApp one-tap shortcut button
    const whatsappBtn = page.locator('#cora-lead-pane-directory a[title="Chat on WhatsApp"]');
    await expect(whatsappBtn.first()).toBeVisible();

    // Take mobile screenshot of the revamped conversion cards
    await page.screenshot({ path: 'tests/e2e/mobile-leads-conversion-cards.png' });
  });
});
