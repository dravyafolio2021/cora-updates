import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('verify in-column real-time search and lead temperature color psychology', async ({ page }) => {
  await login(page);
  await page.goto('/workspace/leads');
  await page.waitForLoadState('networkidle');

  await page.evaluate(() => {
    if (typeof (window as any).coraNavigateTo === 'function') {
      (window as any).coraNavigateTo('leads');
    }
  });
  await page.waitForTimeout(500);

  // 1. Take overall screenshot of Kanban showing Temperature Color Psychology
  await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/1a191bd8-ca8e-4f43-b8ea-289e35a89b5e/kanban-temperature-color-psychology.png' });

  // 2. Test In-Column Search: Click search icon in first column header
  const searchBtn = page.locator('.cora-kanban-column').first().locator('.cora-col-search-btn');
  await expect(searchBtn).toBeVisible();
  await searchBtn.dispatchEvent('click');
  await page.waitForTimeout(300);

  const searchBox = page.locator('.cora-kanban-column').first().locator('.cora-col-search-box');
  await expect(searchBox).toBeVisible();
  await expect(searchBox).not.toHaveClass(/hidden/);

  // Take screenshot of open column search box
  await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/1a191bd8-ca8e-4f43-b8ea-289e35a89b5e/in-column-search-opened.png' });

  // 3. Type search query into column search input
  const searchInput = searchBox.locator('.cora-col-search-input');
  await searchInput.fill('Shruti');
  await page.waitForTimeout(300);

  // Take screenshot of filtered column results
  await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/1a191bd8-ca8e-4f43-b8ea-289e35a89b5e/in-column-search-filtered.png' });

  // Clear search using clear button
  const clearBtn = searchBox.locator('button[title="Clear Search"]');
  await clearBtn.click();
  await page.waitForTimeout(300);
  await expect(searchBox).toHaveClass(/hidden/);
});
