import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Mobile Side Drawer Responsive & Collapsible Accordion UI', () => {
  test('verify side drawer with collapsible toggle cards, quick actions, and sticky footer', async ({ page }) => {
    // 1. Set iPhone 14 Pro Max viewport
    await page.setViewportSize({ width: 430, height: 932 });
    await login(page);
    await page.goto('/workspace/leads?subtab=directory');
    await page.waitForLoadState('networkidle');

    // 2. Open Prospect Detail Drawer by clicking first lead card or table row
    const firstItem = page.locator('.cora-lead-row, .cora-lead-card').first();
    await expect(firstItem).toBeVisible({ timeout: 15000 });
    await firstItem.click();

    const drawer = page.locator('#cora-lead-detail-drawer');
    await expect(drawer).toBeVisible({ timeout: 10000 });

    // 3. Verify drawer width fits mobile screen (width <= 430px)
    const box = await drawer.boundingBox();
    expect(box).not.toBeNull();
    if (box) {
      expect(box.width).toBeLessThanOrEqual(430);
    }

    // 4. Verify Close Button is visible
    const closeBtn = drawer.locator('button[title="Close Drawer"]');
    await expect(closeBtn).toBeVisible();

    // 5. Verify 1-Tap Quick Action Row (WhatsApp, Email, Convert)
    const whatsappBtn = drawer.locator('#cora-drawer-whatsapp-btn');
    await expect(whatsappBtn).toBeVisible();

    const emailBtn = drawer.locator('#cora-drawer-sla-email-btn');
    await expect(emailBtn).toBeVisible();

    const convertBtn = drawer.locator('#cora-convert-lead-btn');
    await expect(convertBtn).toBeVisible();

    // 6. Verify Collapsible Accordion toggling
    const accordionHeader = drawer.locator('button:has-text("Deal Status & Assignee")');
    await expect(accordionHeader).toBeVisible();

    // Body should be hidden by default
    const stageSelect = drawer.locator('#cora-drawer-stage-select');
    await expect(stageSelect).toBeHidden();

    // Click accordion header to expand
    await accordionHeader.click();
    await expect(stageSelect).toBeVisible();

    // 7. Verify Sticky Bottom Action Bar (Delete Lead, Cancel, Save Deal Changes)
    const saveBtn = drawer.locator('button:has-text("Save Deal Changes")');
    await expect(saveBtn).toBeVisible();

    const deleteBtn = drawer.locator('button:has-text("Delete Lead")');
    await expect(deleteBtn).toBeVisible();

    // Screenshot of Collapsible Accordion Drawer
    await page.screenshot({ path: 'tests/e2e/mobile-drawer-accordion-screenshot.png' });
  });
});
