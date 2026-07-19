import { test, expect } from '@playwright/test';

test('onboarding landing page loads and successfully provisions workspace', async ({ page }) => {
  // Clear any existing cookies/localStorage to act as a guest user
  const context = page.context();
  await context.clearCookies();

  // 1. Visit the landing page
  await page.goto('/home-5/?cv_preview_theme=44&preview_theme_id=44');

  // Verify page title and header
  await expect(page).toHaveTitle(/Cora AI — Real Estate Tech Audit/);
  await expect(page.locator('.logo-block')).toContainText('Cora AI');

  // Verify interactive calculator default totals
  await expect(page.locator('#calc-outflow')).toContainText('₹49,500');
  await expect(page.locator('#calc-savings')).toContainText('₹5,70,000');

  // Uncheck a checkbox and verify values recalculate
  await page.uncheck('#calc-crm');
  await expect(page.locator('#calc-outflow')).toContainText('₹24,500');
  await expect(page.locator('#calc-savings')).toContainText('₹2,70,000');

  // 2. Fill in Onboarding Sandbox form
  await page.click('button:has-text("Start 30-day free trial")');
  
  // Click through Step 1: Sign up with Google
  await page.click('#google-signup-btn');
  
  // Click through Step 2: Email Verification
  await page.click('#verify-email-btn');

  const uniqueAgencyName = `E2E Agency ${Math.floor(Math.random() * 10000)}`;
  await page.fill('#signup-name', 'E2E Owner');
  await page.fill('#signup-agency', uniqueAgencyName);
  await page.fill('#signup-whatsapp', '+919999999999');
  await page.fill('#signup-city', 'Gurgaon Delhi NCR');

  // Submit the form
  await page.click('#submit-btn');

  // 3. Verify onboarding redirect and automatic authentication
  // The AJAX request handles signup, logs user in, and redirects to dashboard
  await page.waitForURL(/.*workspace\/dashboard.*/, { timeout: 15000 });

  // Verify dashboard layout shows the updated workspace name
  // The sidebar or admin header widget usually displays the workspace name
  await expect(page.locator('body')).toContainText(uniqueAgencyName);
});
