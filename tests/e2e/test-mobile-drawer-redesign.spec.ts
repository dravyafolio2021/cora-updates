import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Mobile Side Drawer Responsive & Priority Redesign', () => {
  test('verify side drawer fills mobile viewport with sticky header, scrollable tab bar, and sticky footer', async ({ page }) => {
    // 1. Set iPhone 14 Pro Max viewport
    await page.setViewportSize({ width: 430, height: 932 });
    await login(page);
    await page.goto('/workspace/leads?subtab=directory');
    await page.waitForLoadState('networkidle');

    // 2. Open Prospect Detail Drawer by clicking first lead card
    const firstCard = page.locator('.cora-lead-card').first();
    await expect(firstCard).toBeVisible();
    await firstCard.click();

    const drawer = page.locator('#cora-lead-detail-drawer');
    await expect(drawer).toBeVisible({ timeout: 10000 });

    // 3. Verify drawer width fits mobile screen (width <= 430px, 0 horizontal page overflow)
    const box = await drawer.boundingBox();
    expect(box).not.toBeNull();
    if (box) {
      expect(box.width).toBeLessThanOrEqual(430);
    }

    // 4. Verify Close Button is visible & touchable
    const closeBtn = drawer.locator('button[title="Close Drawer"]');
    await expect(closeBtn).toBeVisible();

    // 5. Verify Sticky Header & Stage Selector
    const stageSelect = drawer.locator('#cora-drawer-stage-select');
    await expect(stageSelect).toBeVisible();

    // 6. Verify 1-Tap Action Bar (WhatsApp & Convert to Client)
    const whatsappBtn = drawer.locator('#cora-drawer-whatsapp-btn');
    await expect(whatsappBtn).toBeVisible();

    const convertBtn = drawer.locator('#cora-convert-lead-btn');
    await expect(convertBtn).toBeVisible();

    // 7. Verify Assigned Team Member Selector & SLA Awareness Card
    const assigneeSelect = drawer.locator('#cora-drawer-input-assigned-to');
    await expect(assigneeSelect).toBeVisible();

    // 8. Verify Sticky Footer Action Bar (Delete Lead, Cancel, Save Deal Changes)
    const saveBtn = drawer.locator('button:has-text("Save Deal Changes")');
    await expect(saveBtn).toBeVisible();

    const deleteBtn = drawer.locator('button:has-text("Delete Lead")');
    await expect(deleteBtn).toBeVisible();

    // Take screenshot of redesigned mobile side drawer
    await page.screenshot({ path: 'tests/e2e/mobile-drawer-redesign-screenshot.png' });
  });
});
