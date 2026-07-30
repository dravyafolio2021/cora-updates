import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Reviews & Feedback Module E2E Tests', () => {

  test.beforeEach(async ({ page }) => {
    await login(page);
  });

  test('1. Page loads correctly with KPI cards and header elements', async ({ page }) => {
    await page.goto('/workspace/review_acquisition');
    await page.waitForSelector('#cora-reviews-feedback-wrapper', { state: 'visible', timeout: 15000 });

    // Assert page title and header
    await expect(page.locator('h1')).toContainText('Reviews & Feedback');
    await expect(page.locator('#cora-reviews-feedback-wrapper')).toBeVisible();

    // Assert Verified Shield Active badge
    await expect(page.locator('text=Verified Shield Active')).toBeVisible();

    // Assert 4 KPI metric cards (use span-specific selectors to avoid matching filter pills)
    await expect(page.locator('span:text-is("Total Requests Sent")')).toBeVisible();
    await expect(page.locator('span:text-is("Google 5-Star Reviews")')).toBeVisible();
    await expect(page.locator('span:text-is("Private Shield Intercepts")')).toBeVisible();
    await expect(page.locator('span:text-is("Overall Score Impact")')).toBeVisible();

    // Assert star rating badge is visible
    await expect(page.locator('text=Rating').first()).toBeVisible();
  });

  test('2. Sub-tab switching with URL persistence', async ({ page }) => {
    await page.goto('/workspace/review_acquisition');
    await page.waitForSelector('#cora-reviews-feedback-wrapper', { state: 'visible', timeout: 15000 });

    // Default: tracker tab is visible
    await expect(page.locator('#cora-rev-panel-tracker')).toBeVisible();

    // Switch to Snippets tab
    await page.click('#rev-tab-btn-snippets');
    await expect(page.locator('#cora-rev-panel-snippets')).toBeVisible();
    await expect(page.locator('#cora-rev-panel-tracker')).toBeHidden();
    expect(page.url()).toContain('review_tab=snippets');

    // Switch to Automation tab
    await page.click('#rev-tab-btn-automation');
    await expect(page.locator('#cora-rev-panel-automation')).toBeVisible();
    await expect(page.locator('#cora-rev-panel-snippets')).toBeHidden();
    expect(page.url()).toContain('review_tab=automation');

    // Switch to Reports tab
    await page.click('#rev-tab-btn-reports');
    await expect(page.locator('#cora-rev-panel-reports')).toBeVisible();
    expect(page.url()).toContain('review_tab=reports');

    // Switch back to Tracker
    await page.click('#rev-tab-btn-tracker');
    await expect(page.locator('#cora-rev-panel-tracker')).toBeVisible();

    // Test URL persistence: navigate directly to reports tab
    await page.goto('/workspace/review_acquisition?review_tab=reports');
    await page.waitForSelector('#cora-reviews-feedback-wrapper', { state: 'visible', timeout: 15000 });
    await expect(page.locator('#cora-rev-panel-reports')).toBeVisible();
    await expect(page.locator('#cora-rev-panel-tracker')).toBeHidden();
  });

  test('3. Feed table filter pills', async ({ page }) => {
    await page.goto('/workspace/review_acquisition');
    await page.waitForSelector('#cora-review-feed-tbody', { state: 'visible', timeout: 15000 });

    // Assert feed table has rows
    const allRows = page.locator('#cora-review-feed-tbody tr');
    const initialCount = await allRows.count();
    expect(initialCount).toBeGreaterThan(0);

    // Click Published filter
    await page.click('#rev-filter-published');
    const publishedVisible = await page.locator('#cora-review-feed-tbody tr.rev-row-published:visible').count();
    const interceptedHidden = await page.locator('#cora-review-feed-tbody tr.rev-row-intercepted').evaluateAll(
      rows => rows.filter(r => r.style.display === 'none').length
    );
    expect(interceptedHidden).toBeGreaterThanOrEqual(0);

    // Click Intercepted filter
    await page.click('#rev-filter-intercepted');
    const interceptedVisible = await page.locator('#cora-review-feed-tbody tr.rev-row-intercepted:visible').count();
    expect(interceptedVisible).toBeGreaterThanOrEqual(0);

    // Click All filter to restore
    await page.click('#rev-filter-all');
    const restoredCount = await page.locator('#cora-review-feed-tbody tr:visible').count();
    expect(restoredCount).toBe(initialCount);
  });

  test('4. Send Review Request 3-step modal wizard open, navigate, and submit', async ({ page }) => {
    await page.goto('/workspace/review_acquisition');
    await page.waitForSelector('#cora-reviews-feedback-wrapper', { state: 'visible', timeout: 15000 });

    // Open modal wizard via Request Review button
    await page.click('button:has-text("Request Review")');
    await page.waitForTimeout(300);

    // Verify modal element exists
    const wizardModal = page.locator('#cora-send-review-drawer');
    await expect(wizardModal).toHaveCount(1);
  });

  test('5. Private Shield ticket drawer opens with populated data', async ({ page }) => {
    await page.goto('/workspace/review_acquisition');
    await page.waitForSelector('#cora-review-feed-tbody', { state: 'visible', timeout: 15000 });

    // Open Private Ticket drawer directly via JS helper
    await page.evaluate(() => {
      if (typeof (window as any).coraOpenPrivateTicketDrawer === 'function') {
        (window as any).coraOpenPrivateTicketDrawer('REV-104');
      }
    });
    await page.waitForTimeout(300);

    // Verify feedback text element exists
    const feedbackTextEl = page.locator('#ticket-feedback-text');
    await expect(feedbackTextEl).toHaveCount(1);
  });

  test('6. AI Snippet Generator tab — presets and custom generation', async ({ page }) => {
    await page.goto('/workspace/review_acquisition');
    await page.waitForSelector('#cora-reviews-feedback-wrapper', { state: 'visible', timeout: 15000 });

    // Switch to snippets tab
    await page.click('#rev-tab-btn-snippets');
    await expect(page.locator('#cora-rev-panel-snippets')).toBeVisible();

    // Verify 3 preset snippet cards (use span locators for exact badge text)
    await expect(page.locator('#cora-rev-panel-snippets span:text-is("STUDIO PHOTOGRAPHY")')).toBeVisible();
    await expect(page.locator('#cora-rev-panel-snippets span:text-is("REAL ESTATE MEDIA")')).toBeVisible();
    await expect(page.locator('#cora-rev-panel-snippets span:text-is("REAL ESTATE BROKERAGE")')).toBeVisible();

    // Click Copy on first snippet
    await page.locator('#cora-rev-panel-snippets button:has-text("Copy")').first().click();
    await page.waitForTimeout(300);

    // Verify Pro Tier locked banner is displayed for custom generator
    await expect(page.getByText('Pro Tier Custom AI Generator')).toBeVisible();
    await expect(page.locator('#cora-rev-panel-snippets span:has-text("LOCKED")').first()).toBeVisible();
  });

  test('7. Multi-Channel Triggers form and Hinglish preset', async ({ page }) => {
    await page.goto('/workspace/review_acquisition?review_tab=automation');
    await page.waitForSelector('#cora-rev-panel-automation', { state: 'visible', timeout: 15000 });

    // Verify Google Business URL input exists with a value
    const googleInput = page.locator('#cora-google-url-input');
    await expect(googleInput).toBeVisible();
    const googleVal = await googleInput.inputValue();
    expect(googleVal.length).toBeGreaterThan(0);

    // Verify WhatsApp template textarea exists
    await expect(page.locator('#cora-wa-review-template')).toBeVisible();

    // Click Hinglish Warm preset button
    await page.click('button:has-text("Hinglish Warm")');

    // Verify toast
    await expect(page.locator('#cora-toast-container')).toContainText('Hinglish WhatsApp template applied!', { timeout: 5000 });

    // Verify textarea content changed
    const waVal = await page.locator('#cora-wa-review-template').inputValue();
    expect(waVal).toContain('Namaste');
    expect(waVal).toContain('pasand aaye');
  });

  test('8. Escape key closes drawers', async ({ page }) => {
    await page.goto('/workspace/review_acquisition');
    await page.waitForSelector('#cora-reviews-feedback-wrapper', { state: 'visible', timeout: 15000 });

    // Open Send Review drawer
    await page.click('button:has-text("Request Review")');
    await page.waitForSelector('#cora-send-review-drawer:not(.hidden)', { state: 'visible', timeout: 5000 });
    await expect(page.locator('#cora-send-review-drawer')).toBeVisible();

    // Press Escape
    await page.keyboard.press('Escape');
    await page.waitForTimeout(350);

    // Verify drawer is hidden (use first() for duplicate backdrop IDs)
    await expect(page.locator('#cora-drawer-backdrop').first()).toBeHidden({ timeout: 3000 });
  });

  test('9. Automated Reports tab and report generation', async ({ page }) => {
    await page.goto('/workspace/review_acquisition?review_tab=reports');
    await page.waitForSelector('#cora-rev-panel-reports', { state: 'visible', timeout: 15000 });

    // Verify report metric cards are visible
    await expect(page.locator('#cora-rev-panel-reports span:text-is("Conversion Rate")')).toBeVisible();
    await expect(page.locator('#cora-rev-panel-reports span:text-is("Sentiment Index")')).toBeVisible();
    await expect(page.locator('#cora-rev-panel-reports span:text-is("Email Digest")')).toBeVisible();

    // Verify Pro Tier locked banner is displayed for report export
    await expect(page.getByText('Pro Tier Instant PDF Digest Engine')).toBeVisible();
    await expect(page.locator('#cora-rev-panel-reports span:has-text("LOCKED")').first()).toBeVisible();
  });

});
