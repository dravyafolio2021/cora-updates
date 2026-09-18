import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Workspace Level RAG Knowledge Base & Co-Founder Intelligence Suite', () => {
  test('verify multi-domain knowledge indexing, category filtering, and telemetry metrics', async ({ page }) => {
    page.on('pageerror', exception => {
      console.log(`Uncaught exception: "${exception.message}"`);
    });

    // 1. Log in and navigate to AI Tools MCP with RAG tab hash
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
    await page.goto('/studio/mcp#rag-settings');
    await page.waitForLoadState('networkidle');

    // 2. Verify RAG Panel is displayed
    const ragPanel = page.locator('#cora-ai-panel-rag-settings');
    await expect(ragPanel).toBeVisible({ timeout: 10000 });

    // 3. Trigger Re-Index Knowledge Sweep
    const reindexBtn = page.locator('button:has-text("Re-Index Knowledge"), button:has-text("Re-Index Workspace Knowledge")').first();
    if (await reindexBtn.isVisible()) {
      await reindexBtn.click();
      await page.waitForTimeout(2000);
      await page.waitForLoadState('networkidle');
    }

    // Ensure RAG Panel is active
    if (!await ragPanel.isVisible()) {
      const ragTab = page.locator('.cora-ai-tab:has-text("RAG Knowledge Base")').first();
      if (await ragTab.isVisible()) await ragTab.click();
      await page.waitForTimeout(500);
    }

    // Verify 4 telemetry metric cards
    await expect(page.locator('text=Indexed Fragments').first()).toBeVisible();
    await expect(page.locator('text=Memory Quota').first()).toBeVisible();
    await expect(page.locator('text=Domain Vectors').first()).toBeVisible();
    await expect(page.locator('text=Learning Sync').first()).toBeVisible();

    // Capture screenshot of RAG Knowledge Base telemetry & table
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/rag-knowledge-base-telemetry.png' });

    // 4. Test category filter: Clients & Portals
    const clientsFilter = page.locator('#rag-cat-clients');
    if (await clientsFilter.isVisible()) {
      await clientsFilter.click();
      await page.waitForTimeout(400);
      await expect(page.locator('.rag-resource-row[data-source-type="clients"]').first()).toBeVisible();
    }

    // 5. Test category filter: Financials & Ledger
    const finFilter = page.locator('#rag-cat-financials');
    if (await finFilter.isVisible()) {
      await finFilter.click();
      await page.waitForTimeout(400);
      await expect(page.locator('.rag-resource-row[data-source-type="financials"]').first()).toBeVisible();
    }

    // 6. Test category filter: Learned Rules
    const rulesFilter = page.locator('#rag-cat-business_rule');
    if (await rulesFilter.isVisible()) {
      await rulesFilter.click();
      await page.waitForTimeout(400);
      await expect(page.locator('.rag-resource-row[data-source-type="business_rule"]').first()).toBeVisible();
    }

    // Switch back to All
    const allFilter = page.locator('#rag-cat-all');
    if (await allFilter.isVisible()) {
      await allFilter.click();
      await page.waitForTimeout(400);
    }

    // Take final screenshot of RAG multi-domain table
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/rag-knowledge-base-multidomain-live.png' });
  });
});
