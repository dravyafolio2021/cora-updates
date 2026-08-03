import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Leads Directory UI Revamp & Responsiveness', () => {
  
  test('verify desktop table UI structure, currency formatting, and selection', async ({ page }) => {
    page.on('pageerror', exception => {
      console.log(`Uncaught exception: "${exception.message}"`);
    });
    page.on('console', msg => console.log('BROWSER LOG:', msg.text()));
    // Set desktop resolution
    await page.setViewportSize({ width: 1280, height: 800 });
    await login(page);
    await page.goto('/workspace/leads');
    await page.waitForLoadState('networkidle');

    const scripts = await page.evaluate(() => {
      const els = Array.from(document.querySelectorAll('script'));
      return els.map(el => el.innerHTML).filter(html => html.includes('coraSwitchDirectoryViewMode'));
    });
    console.log("MATCHING SCRIPTS FOUND IN DOM:", scripts.length, scripts);

    const htmlContent = await page.content();
    require('fs').writeFileSync('/Users/shrutian/.gemini/antigravity/brain/587f1814-a612-4759-bd45-72efd0915829/scratch/page_source.html', htmlContent, 'utf8');

    // Wait for subtab buttons to render in DOM
    const dirTabBtn = page.locator('.cora-lead-subtab-btn[data-tab="directory"]');
    await expect(dirTabBtn).toBeVisible({ timeout: 10000 });

    // Switch to Leads Directory tab
    await dirTabBtn.click();
    await page.waitForTimeout(500);

    // Toggle view mode to Table List
    const tableBtn = page.locator('#cora-dir-view-btn-table');
    await expect(tableBtn).toBeVisible();
    await tableBtn.click();
    await page.waitForTimeout(300);

    // Verify table container is visible on desktop
    const desktopTableWrapper = page.locator('#cora-directory-table-container');
    await expect(desktopTableWrapper).toBeVisible();

    // Verify column headers are rendered correctly
    await expect(page.locator('th:has-text("Lead / Client Name")')).toBeVisible();
    await expect(page.locator('th:has-text("Budget / Value")')).toBeVisible();

    // Check pricing currency formatting (should contain ₹ and be formatted)
    const firstPriceText = await page.locator('#cora-leads-table-body tr td').nth(4).innerText();
    expect(firstPriceText).toContain('₹');

    // Verify select all checkbox behavior
    const selectAllCheckbox = page.locator('#cora-leads-select-all');
    await expect(selectAllCheckbox).toBeVisible();
    await selectAllCheckbox.click();

    // Ensure all individual row checkboxes are checked
    const rowCheckboxes = page.locator('.cora-lead-row-checkbox');
    const count = await rowCheckboxes.count();
    for (let i = 0; i < count; i++) {
      await expect(rowCheckboxes.nth(i)).toBeChecked();
    }

    // Take desktop screenshot of revamped table
    const directoryPane = page.locator('#cora-lead-pane-directory');
    await directoryPane.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/1a191bd8-ca8e-4f43-b8ea-289e35a89b5e/leads-directory-desktop.png' });
  });

  test('verify mobile view cards fallback instead of horizontal scrolling table', async ({ page }) => {
    page.on('pageerror', exception => {
      console.log(`Uncaught exception: "${exception.message}"`);
    });
    // Set mobile resolution
    await page.setViewportSize({ width: 375, height: 667 });
    await login(page);
    await page.goto('/workspace/leads');
    await page.waitForLoadState('networkidle');

    // Wait for subtab buttons to render in DOM
    const dirTabBtn = page.locator('.cora-lead-subtab-btn[data-tab="directory"]');
    await expect(dirTabBtn).toBeVisible({ timeout: 10000 });

    // Switch to Leads Directory tab
    await dirTabBtn.click();
    await page.waitForTimeout(500);

    // Verify table container is hidden on mobile
    const tableContainer = page.locator('#cora-directory-table-container');
    await expect(tableContainer).toBeHidden();

    // Verify mobile cards list container is visible
    const mobileCardsWrapper = page.locator('#cora-directory-grid-container');
    await expect(mobileCardsWrapper).toBeVisible();

    // Verify card content renders (e.g., has name and details text)
    const firstCard = mobileCardsWrapper.locator('.cora-lead-card').first();
    await expect(firstCard).toBeVisible();
    
    // Take mobile screenshot of cards view
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/1a191bd8-ca8e-4f43-b8ea-289e35a89b5e/leads-directory-mobile.png' });
  });
  
});
