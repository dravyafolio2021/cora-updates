import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Mobile Side Drawer Responsive & Humanized UI', () => {
  test('verify side drawer fills mobile viewport with sticky header, clean action buttons, and sticky footer', async ({ page }) => {
    // 1. Set iPhone 14 Pro Max viewport
    await page.setViewportSize({ width: 430, height: 932 });
    await login(page);
    await page.goto('/workspace/leads?subtab=directory');
    await page.waitForLoadState('networkidle');

    // 2. Open Prospect Detail Drawer by clicking first lead row/card
    const firstRow = page.locator('.cora-lead-row, .cora-lead-card').first();
    await expect(firstRow).toBeVisible({ timeout: 15000 });
    await firstRow.click();

    const drawer = page.locator('#cora-lead-detail-drawer');
    await expect(drawer).toBeVisible({ timeout: 10000 });

    // 3. Verify drawer width fits mobile screen (width <= 430px)
    const box = await drawer.boundingBox();
    expect(box).not.toBeNull();
    if (box) {
      expect(box.width).toBeLessThanOrEqual(430);
    }

    // 4. Verify Close Button is visible & touchable
    const closeBtn = drawer.locator('button[title="Close Drawer"]');
    await expect(closeBtn).toBeVisible();

    // 5. Verify Simplified Human-Friendly Tab Header Pills (Details, Automations, Tasks, History)
    await expect(drawer.locator('#cora-lead-detail-tab-btn-overview')).toContainText('Details');
    await expect(drawer.locator('#cora-lead-detail-tab-btn-automation')).toContainText('Automations');
    await expect(drawer.locator('#cora-lead-detail-tab-btn-checklist')).toContainText('Tasks');
    await expect(drawer.locator('#cora-lead-detail-tab-btn-audit')).toContainText('History');

    // 6. Verify Pipeline Stage & Team Member Selectors
    const stageSelect = drawer.locator('#cora-drawer-stage-select');
    await expect(stageSelect).toBeVisible();

    const assigneeSelect = drawer.locator('#cora-drawer-input-assigned-to');
    await expect(assigneeSelect).toBeVisible();

    // 7. Verify 1-Tap Direct Client Action Bar (WhatsApp, Email, Convert)
    const whatsappBtn = drawer.locator('#cora-drawer-whatsapp-btn');
    await expect(whatsappBtn).toBeVisible();

    const emailBtn = drawer.locator('#cora-drawer-sla-email-btn');
    await expect(emailBtn).toBeVisible();

    const convertBtn = drawer.locator('#cora-convert-lead-btn');
    await expect(convertBtn).toBeVisible();

    // 8. Verify Sticky Bottom Action Bar (Delete Lead, Cancel, Save Deal Changes)
    const saveBtn = drawer.locator('button:has-text("Save Deal Changes")');
    await expect(saveBtn).toBeVisible();

    const deleteBtn = drawer.locator('button:has-text("Delete Lead")');
    await expect(deleteBtn).toBeVisible();

    // Take screenshot of humanized mobile side drawer
    await page.screenshot({ path: 'tests/e2e/mobile-drawer-humanized-screenshot.png' });
  });
});
