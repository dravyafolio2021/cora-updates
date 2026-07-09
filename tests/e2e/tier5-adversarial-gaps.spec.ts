import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Tier 5: Adversarial E2E Gaps', () => {

  test('1. Public REST API - Fetch Team Members', async ({ request }) => {
    let response = await request.get('/wp-json/cora/v1/team');
    let text = await response.text();
    if (text.startsWith('<!DOCTYPE') || response.status() === 404) {
      // Fallback for plain permalinks or missing rewrites
      response = await request.get('/?rest_route=/cora/v1/team');
      text = await response.text();
    }
    console.log("REST API RESPONSE:", text);
    expect(response.ok()).toBeTruthy();
    const team = JSON.parse(text);
    expect(Array.isArray(team)).toBeTruthy();
    expect(team.length).toBeGreaterThan(0);
    const member = team[0];
    expect(member).toHaveProperty('id');
    expect(member).toHaveProperty('name');
    expect(member).toHaveProperty('role');
    expect(member).toHaveProperty('email');
    expect(member).toHaveProperty('avatar_url');
  });

  test('2. Public Document Share - Generate and Verify Preview', async ({ page }) => {
    // Login and go to Vault
    await login(page);
    await page.goto('/workspace/vault');

    // Click share button on the first document row
    const shareBtn = page.locator('tr.cora-doc-row button.cora-share-doc-btn').first();
    await shareBtn.waitFor({ state: 'visible' });
    await shareBtn.click();

    // Drawer should open (remove 'collapsed' class)
    await expect(page.locator('#cora-share-drawer')).not.toHaveClass(/collapsed/);

    // Fill sharing email
    const uniqueEmail = `test-vault-share-${Date.now()}@example.com`;
    await page.fill('#cora-share-email', uniqueEmail);

    // Check "Never Expires" checkbox
    const neverExpiresCb = page.locator('#cora-share-no-expiry');
    const isChecked = await neverExpiresCb.isChecked();
    if (!isChecked) {
      await neverExpiresCb.check();
    }

    // Submit sharing
    await page.click('#cora-share-submit-btn');

    // Toast notification should verify success
    await expect(page.locator('#cora-toast-container')).toContainText('Document shared via email successfully.');

    // Extract the share link from result box
    const resultBox = page.locator('#cora-share-result-box');
    await resultBox.waitFor({ state: 'visible' });
    const shareLink = await page.inputValue('#cora-share-link-input');
    expect(shareLink).toContain('/shared-doc/');

    // Log out by clearing cookies
    await page.context().clearCookies();

    // Access the shared document link publicly
    await page.goto(shareLink);

    // Verify document contents on public preview page
    const docTitle = page.locator('.doc-title');
    await expect(docTitle).toBeVisible();
    await expect(page.locator('.container')).toContainText('Protected Link');
    await expect(page.locator('.expiry-text')).toContainText('This secure sharing link is active and permanent');
  });

  test('3. Public Portfolio - Unprotected View & Interaction', async ({ page }) => {
    // Go directly to the public unprotected portfolio
    await page.goto('/shared-portfolio/listing-ceremony');

    // Verify header title
    console.log("PAGE BODY FOR UNPROTECTED:", await page.locator('body').innerText());
    const headerTitle = page.locator('.portfolio-title');
    await expect(headerTitle).toContainText('Arjun & Priya - Listing Ceremony');

    // Check initial select count
    const selectCounter = page.locator('#selected-count-label');
    const initialText = await selectCounter.innerText();
    const initialCount = parseInt(initialText.trim()) || 0;

    // Toggle filter: click Photos only
    await page.click('button:has-text("Photos")');

    // Like / Heart the first card (handling pre-existing like state)
    const firstHeartBtn = page.locator('.portfolio-card button.heart-btn').first();
    await firstHeartBtn.waitFor({ state: 'visible' });
    const isLiked = await firstHeartBtn.evaluate(el => el.classList.contains('liked'));
    if (isLiked) {
      // Unlike it first to reset state, wait for AJAX to update, then verify
      await firstHeartBtn.click();
      await expect(selectCounter).toHaveText(`${initialCount - 1}`);
      
      // Now re-heart it to verify increment
      await firstHeartBtn.click();
      await expect(selectCounter).toHaveText(`${initialCount}`);
    } else {
      // Just like it
      await firstHeartBtn.click();
      await expect(selectCounter).toHaveText(`${initialCount + 1}`);
    }


    // Click on card to open lightbox
    const firstCard = page.locator('.portfolio-card').first();
    await firstCard.click();

    // Lightbox should have active class
    const lightbox = page.locator('.cora-public-lightbox-overlay');
    await expect(lightbox).toHaveClass(/active/);

    // Close lightbox
    await page.click('.cora-lightbox-close');
    await expect(lightbox).not.toHaveClass(/active/);
  });

  test('4. Public Portfolio - Password Protection & Unlock', async ({ page }) => {
    // Go directly to the public password-protected portfolio
    await page.goto('/shared-portfolio/pre-listing-goa');

    // Lock screen should be visible
    console.log("PAGE BODY FOR LOCKED:", await page.locator('body').innerText());
    const lockTitle = page.locator('.lock-title');
    await expect(lockTitle).toContainText('Password Protected');

    // Submit wrong password
    await page.fill('input[name="portfolio_password"]', 'wrongpassword');
    await page.click('button:has-text("Unlock Gallery")');
    await expect(page.locator('.lock-error')).toContainText('Incorrect access code');

    // Submit correct password
    await page.fill('input[name="portfolio_password"]', 'goa2026');
    await page.click('button:has-text("Unlock Gallery")');

    // Gallery should unlock and show title
    const headerTitle = page.locator('.portfolio-title');
    await expect(headerTitle).toContainText('Site Walkthrough (Goa)');
  });

});
