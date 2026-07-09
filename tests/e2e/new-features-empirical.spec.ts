import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Verify New Features Empirically', () => {

  // Capture console errors
  test.beforeEach(async ({ page }) => {
    page.on('console', msg => {
      if (msg.type() === 'error') {
        console.log(`PAGE CONSOLE ERROR: "${msg.text()}"`);
      }
    });
    page.on('pageerror', err => {
      console.log(`PAGE UNHANDLED EXCEPTION: "${err.message}"`);
    });
  });

  test('1. Webhook REST endpoint (cora/v1/leads)', async ({ request }) => {
    // Fire REST request
    let response = await request.post('/wp-json/cora/v1/leads', {
      data: {
        names: 'Empirical REST Lead',
        email: 'rest_empirical@example.com',
        scale: 'Large',
        city: 'Delhi',
        price: '₹5.5Cr',
        notes: 'Inquiry from E2E rest webhook validation'
      }
    });

    let text = await response.text();
    if (response.status() === 404 || text.trim().startsWith('<!doctype') || text.trim().startsWith('<!DOCTYPE')) {
      // Fallback for plain permalinks
      response = await request.post('/?rest_route=/cora/v1/leads', {
        data: {
          names: 'Empirical REST Lead',
          email: 'rest_empirical@example.com',
          scale: 'Large',
          city: 'Delhi',
          price: '₹5.5Cr',
          notes: 'Inquiry from E2E rest webhook validation'
        }
      });
      text = await response.text();
    }

    expect(response.status()).toBe(200);
    const body = JSON.parse(text);
    expect(body.success).toBe(true);
    expect(body.lead.names).toBe('Empirical REST Lead');
    expect(body.lead.email).toBe('rest_empirical@example.com');
  });

  test('2. Lead shortcode/form on frontend', async ({ page }) => {
    const rand = Math.floor(Math.random() * 100000);
    const pageTitle = `E2E Lead Form Page ${rand}`;
    const pageSlug = `e2e-lead-form-page-${rand}`;

    // Create a WordPress page with the [cora_lead_form] shortcode
    await login(page);
    await page.goto('/workspace/pages');
    await page.click('button:has-text("New Page")');
    await page.waitForSelector('#cora-drawer-page:not(.translate-x-full)', { state: 'visible' });

    await page.fill('#cora-page-title-input', pageTitle);
    await page.fill('#cora-page-slug-input', pageSlug);
    await page.selectOption('#cora-page-status-input', 'publish');
    
    // Fill Quill editor with the shortcode
    await page.locator('#cora-page-quill-editor .ql-editor').fill('[cora_lead_form]');
    
    // Save Page
    await page.click('#cora-drawer-page button:has-text("Save Page")');
    await expect(page.locator('#cora-toast-container')).toContainText('Page saved successfully.');
    await page.waitForLoadState('networkidle');

    // Go to the newly created page
    await page.goto(`/${pageSlug}/`);
    await expect(page.locator('.cora-lead-form-container')).toBeVisible();

    // Fill form and submit
    const leadName = `Shortcode Lead ${rand}`;
    await page.fill('#cora-lead-names', leadName);
    await page.fill('#cora-lead-email', `shortcode_${rand}@example.com`);
    await page.fill('#cora-lead-city', 'Gurgaon');
    await page.fill('#cora-lead-price', '₹3.2Cr');
    await page.selectOption('#cora-lead-scale', 'Medium');
    await page.fill('#cora-lead-notes', 'Looking for immediate possession penthouse.');
    
    await page.click('#cora-lead-submit-btn');

    // Verify success feedback on the page
    const feedback = page.locator('#cora-lead-form-feedback');
    await expect(feedback).toBeVisible();
    await expect(feedback).toContainText('Inquiry logged successfully!');

    // Check leads list in workspace
    await page.goto('/workspace/leads');
    const leadCard = page.locator(`.cora-lead-card:has-text("${leadName}")`).first();
    await expect(leadCard).toBeVisible();

    // Cleanup: delete page
    await page.goto('/workspace/pages');
    const row = page.locator(`tr.cora-page-row:has-text("${pageTitle}")`).first();
    await row.waitFor({ state: 'visible' });
    await row.locator('button:has-text("Delete")').click();
    await page.click('#cora-confirm-btn');
    await expect(page.locator('#cora-toast-container')).toContainText('Page deleted successfully.');
  });

  test('3. 3rd-party Sync & AI SEO meta-data generation', async ({ page }) => {
    await login(page);
    await page.goto('/workspace/equipment');

    // Open add listing drawer
    await page.click('button:has-text("Add Listing")');
    await page.waitForSelector('#cora-listing-drawer:not(.collapsed)', { state: 'visible' });

    // Sync a 3rd party listing URL (e.g. Zillow link)
    await page.fill('#cora-listing-sync-link', 'https://www.zillow.com/homedetails/123456_zpid/');
    await page.click('#cora-listing-sync-btn');

    // Check that fields were synced automatically
    await expect(page.locator('#cora-listing-name')).toHaveValue('Zillow Sunset Villa');
    await expect(page.locator('#cora-listing-category')).toHaveValue('Villa');
    await expect(page.locator('#cora-listing-rera-id')).toHaveValue('ZIL-ERA-1049281');
    await expect(page.locator('#cora-listing-notes')).toHaveValue(/beachfront villa/i);

    // Let's verify that AI SEO meta-data section has fields but they are blank initially
    await expect(page.locator('#cora-listing-seo-title')).toHaveValue('');
    await expect(page.locator('#cora-listing-seo-description')).toHaveValue('');
    await expect(page.locator('#cora-listing-seo-keywords')).toHaveValue('');

    // Save Listing (it should auto-generate SEO metadata because fields are left blank)
    await page.click('#cora-save-listing-btn');
    await expect(page.locator('#cora-toast-container')).toContainText('Listing added successfully.');

    // Wait for page reload to complete (triggered after 800ms toast delay)
    await page.waitForTimeout(1000);
    await page.waitForLoadState('networkidle');

    // Find and click on the name cell to open its drawer and check details
    const nameCell = page.locator('tr.cora-eq-row:has-text("Zillow Sunset Villa") td').nth(1);
    await expect(nameCell).toBeVisible();
    await nameCell.click();

    // Verify AI-generated SEO fields
    await expect(page.locator('#cora-listing-seo-title')).toHaveValue('Premium Villa - Zillow Sunset Villa | RERA ID: ZIL-ERA-1049281');
    await expect(page.locator('#cora-listing-seo-description')).toHaveValue(/ZIL-ERA-1049281/);
    await expect(page.locator('#cora-listing-seo-description')).toHaveValue(/luxurious Villa/);
    await expect(page.locator('#cora-listing-seo-keywords')).toHaveValue('villa, zillow-sunset-villa, real-estate, property-listing, cora-platform, zil-era-1049281');

    // Close the drawer
    await page.click('#cora-listing-drawer button:has-text("Cancel")');

    // Cleanup: delete the listing
    const rowToDelete = page.locator('tr.cora-eq-row:has-text("Zillow Sunset Villa")').first();
    await rowToDelete.locator('button.cora-delete-eq-btn').click();
    await expect(page.locator('#cora-toast-container')).toContainText('Equipment asset deleted.');
  });

});
