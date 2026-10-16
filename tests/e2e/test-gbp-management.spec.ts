import { test, expect } from '@playwright/test';
import { login } from './helpers';
import { execSync } from 'child_process';

const PHP_BIN = '/Applications/Local.app/Contents/Resources/extraResources/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php';

function resetGbpOptions() {
  try {
    execSync(`${PHP_BIN} -r "require 'app/public/wp-load.php'; delete_option('cora_gbp_profile'); delete_option('cora_gbp_tokens'); delete_option('cora_gbp_review_replies'); delete_option('cora_gbp_posts');"`, { cwd: process.cwd() });
  } catch (e) {
    console.warn('PHP reset warning:', e);
  }
}

test.describe('Google Business Profile & Maps AI Autopilot Management Hub', () => {

  test.beforeEach(async ({ page }) => {
    page.on('console', msg => console.log('[BROWSER_LOG]', msg.type(), msg.text()));
    page.on('pageerror', err => console.log('[PAGE_ERROR]', err.message));
    // Ensure user is logged into local studio workspace
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
  });

  test('TC-GBP-01: Verifies Unconnected State, Places Search, and Credentials Drawer', async ({ page }) => {
    // Reset GBP connection in WordPress directly via PHP
    resetGbpOptions();

    await page.goto('/workspace/gbp');
    await page.waitForLoadState('networkidle');

    // 1. Verify Header and Page Title
    await expect(page.locator('.cora-workspace-header')).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Google Profile' }).first()).toBeVisible();

    // 2. Verify Unconnected Hero & Connect Actions
    await expect(page.locator('#cora-gbp-oauth-btn')).toBeVisible();
    await expect(page.locator('text=1-Click Sandbox Connect')).toBeVisible();
    await expect(page.locator('#cora-gbp-search-q')).toBeVisible();

    // 3. Test Places Search
    await page.fill('#cora-gbp-search-q', 'Elite Studio Gurugram');
    await page.click('#cora-gbp-search-btn');

    // Wait for search results container to populate
    const resultsWrap = page.locator('#cora-gbp-search-results-wrap');
    await expect(resultsWrap).toBeVisible({ timeout: 5000 });
    await expect(resultsWrap.locator('text=Connect').first()).toBeVisible();

    // 4. Test API Credentials Drawer Open & Dismissal
    const credsBtn = page.locator('button[onclick*="coraGbpToggleKeysPanel"], .cora-workspace-header button:has-text("Credentials")').first();
    await expect(credsBtn).toBeVisible();
    await credsBtn.click();

    const drawer = page.locator('#cora-gbp-keys-panel');
    await expect(drawer).toHaveClass(/open/);
    await expect(page.locator('#cora-gbp-redirect-uri')).toContainText('/workspace/gbp');

    // Test Escape key closes drawer
    await page.keyboard.press('Escape');
    await expect(drawer).not.toHaveClass(/open/);
  });

  test('TC-GBP-02: Authorizes Sandbox Account & Verifies Connected Dashboard KPIs', async ({ page }) => {
    await page.goto('/workspace/gbp');
    await page.waitForLoadState('networkidle');

    // Authorize Demo Sandbox Account if not already connected
    const demoBtn = page.locator('text=1-Click Sandbox Connect').first();
    if (await demoBtn.isVisible()) {
      await demoBtn.click();
      await page.waitForURL('**/workspace/gbp', { timeout: 10000 });
      await page.waitForLoadState('networkidle');
    }

    // Verify Connected State
    await expect(page.locator('#cora-gbp-state-connected')).toBeVisible({ timeout: 8000 });
    await expect(page.locator('text=Live Google Maps Sync')).toBeVisible();
    await expect(page.locator('text=Average Rating')).toBeVisible();
    await expect(page.locator('text=Total Reviews')).toBeVisible();
    await expect(page.locator('text=Search Impressions')).toBeVisible();
    await expect(page.locator('text=Client Interactions')).toBeVisible();
  });

  test('TC-GBP-03: Manages Reviews, Filters, and Generates AI Reply Draft', async ({ page }) => {
    try {
      execSync(`${PHP_BIN} -r "require 'app/public/wp-load.php'; delete_option('cora_gbp_review_replies');"`, { cwd: process.cwd() });
    } catch (e) {}

    await page.goto('/workspace/gbp');
    await page.waitForLoadState('networkidle');

    // Ensure connected via Sandbox Connect if in unconnected state
    const demoBtn = page.locator('text=1-Click Sandbox Connect').first();
    if (await demoBtn.isVisible()) {
      await demoBtn.click();
      await page.waitForURL('**/workspace/gbp', { timeout: 10000 });
      await page.waitForLoadState('networkidle');
    }

    await expect(page.locator('#cora-gbp-state-connected')).toBeVisible({ timeout: 8000 });

    // Ensure on Reviews tab
    await page.click('#tab-btn-reviews');

    // Wait for reviews to load
    const reviewsList = page.locator('#cora-gbp-reviews-list');
    await expect(reviewsList).toBeVisible({ timeout: 8000 });
    await expect(reviewsList.locator('text=Ananya Sharma')).toBeVisible();

    // Test filter pills
    await page.click('button:has-text("Needs Reply")');
    await expect(reviewsList).toBeVisible();

    // Test AI Draft button
    const draftBtn = reviewsList.locator('button:has-text("Draft AI Reply")').first();
    await expect(draftBtn).toBeVisible();
    await draftBtn.click();
    const textarea = reviewsList.locator('textarea').first();
    await expect(textarea).not.toHaveValue('', { timeout: 8000 });

    // Post the reply
    const postReplyBtn = reviewsList.locator('button:has-text("Post Reply to Google")').first();
    await postReplyBtn.click();

    // Check success indication
    await expect(page.locator('text=Your Google Response').first()).toBeVisible({ timeout: 6000 });
  });

  test('TC-GBP-04: Publishes Maps Posts, Updates Operating Hours, and Inspects Local SEO Ranks', async ({ page }) => {
    await page.goto('/workspace/gbp');
    await page.waitForLoadState('networkidle');

    // Ensure connected via Sandbox Connect if in unconnected state
    const demoBtn = page.locator('text=1-Click Sandbox Connect').first();
    if (await demoBtn.isVisible()) {
      await demoBtn.click();
      await page.waitForURL('**/workspace/gbp', { timeout: 10000 });
      await page.waitForLoadState('networkidle');
    }

    await expect(page.locator('#cora-gbp-state-connected')).toBeVisible({ timeout: 8000 });

    // 1. Tab 2: Maps Post Publisher
    await page.click('#tab-btn-posts');
    const postTab = page.locator('#cora-gbp-tab-posts');
    await expect(postTab).toBeVisible();

    await page.fill('#cora-gbp-post-content', 'Exclusive Autumn Fashion & Commercial Photoshoot Slots are now open at Elite Studio! Book your slot today.');
    await page.selectOption('#cora-gbp-post-cta', 'BOOK_NOW');
    await page.click('#cora-gbp-publish-btn');

    // Wait for publish toast or page reload
    await page.waitForLoadState('networkidle');
    await page.click('#tab-btn-posts');
    await expect(page.locator('#cora-gbp-posts-list')).toContainText('Exclusive Autumn Fashion');

    // 2. Tab 3: Business Info & Operating Hours
    await page.click('#tab-btn-hours');
    const hoursTab = page.locator('#cora-gbp-tab-hours');
    await expect(hoursTab).toBeVisible();
    await expect(page.locator('#gbp-info-name')).toHaveValue(/Elite Studio/);
    await page.click('#cora-gbp-save-info-btn');

    // 3. Tab 4: Local SEO & Map-Pack Ranks
    await page.click('#tab-btn-seo');
    const seoTab = page.locator('#cora-gbp-tab-seo');
    await expect(seoTab).toBeVisible();
    await expect(seoTab.locator('text=#1 in 3-Pack').first()).toBeVisible();
    await expect(seoTab.locator('text=94% Visibility Score')).toBeVisible();

    // 4. Tab 5: Multi-Branch Locations
    await page.click('#tab-btn-locations');
    const locTab = page.locator('#cora-gbp-tab-locations');
    await expect(locTab).toBeVisible();
    await expect(locTab.getByText('Active Branch', { exact: true })).toBeVisible();
    await expect(locTab.locator('text=South Delhi Branch')).toBeVisible();
  });

});
