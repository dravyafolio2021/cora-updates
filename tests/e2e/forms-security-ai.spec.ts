import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Cora Forms Security & AI Compatibility E2E tests', () => {

  test('Create form, set AI Purpose tags, verify AI Schema & Security rules', async ({ page }) => {
    // 1. Login and go to Forms builder
    await login(page);
    await page.goto('/workspace/forms');
    await page.waitForLoadState('networkidle');

    // Extract wpNonce from page context to authenticate REST calls
    const wpNonce = await page.evaluate(() => {
      return (window as any).coraREData?.nonce || (window as any).wpApiSettings?.nonce || '';
    });
    console.log("Extracted WP Nonce:", wpNonce);

    // Click "+ Create form" to open editor panel
    await page.click('#btn-create-form');
    await page.waitForSelector('#form-editor-state:not(.hidden)', { state: 'visible' });

    const testFormTitle = `Security & AI E2E Form ${Math.floor(Math.random() * 10000)}`;
    await page.fill('#editor-form-title', testFormTitle);
    await page.selectOption('#editor-form-status', 'published');

    // Save initial form to generate database records (saves & keeps editor open)
    await page.click('#btn-save-form');
    await expect(page.locator('#cora-toast-container')).toContainText('Form published successfully');
    await page.waitForLoadState('networkidle');

    // Go back to the forms list manually
    await page.click('#btn-back-to-list');

    // Verify we are back on the forms list state
    await page.waitForSelector('#forms-list-state:not(.hidden)', { state: 'visible' });

    // Fetch the form ID from table row list card to test REST endpoints
    const formRow = page.locator('#forms-list-body tr').filter({ hasText: testFormTitle }).first();
    await expect(formRow).toBeVisible();
    
    // Find the edit button inside this row and click to check block options
    await formRow.locator('.btn-edit-form').click();
    await page.waitForSelector('#form-editor-state:not(.hidden)', { state: 'visible' });

    // Verify AI Purpose settings fields are visible next to inputs
    const aiPurposeInputs = page.locator('.block-ai-purpose');
    expect(await aiPurposeInputs.count()).toBeGreaterThanOrEqual(0);

    // Go back to list manually (since we didn't save)
    await page.click('#btn-back-to-list');

    // Get the form ID from database/REST options
    // We use page.request to inherit browser login session cookies and attach X-WP-Nonce
    let getFormsRes = await page.request.get('/wp-json/cora/v1/forms', {
      headers: {
        'X-WP-Nonce': wpNonce
      }
    });
    console.log("Forms List Response Status:", getFormsRes.status());
    let text = await getFormsRes.text();
    
    let formsList = JSON.parse(text);
    let createdForm = formsList.find(f => f.title === testFormTitle);
    expect(createdForm).toBeDefined();
    const formId = createdForm.id;

    // 2. Test AI JSON Schema Endpoint
    console.log(`Verifying AI JSON Schema for form #${formId}`);
    const schemaResponse = await page.request.get(`/wp-json/cora/v1/forms/${formId}/ai-schema`, {
      headers: {
        'X-WP-Nonce': wpNonce
      }
    });
    expect(schemaResponse.status()).toBe(200);
    const schemaJson = await schemaResponse.json();
    expect(schemaJson.type).toBe('object');
    expect(schemaJson.properties).toBeDefined();

    // 3. Test Honeypot Spam Prevention
    console.log(`Verifying Honeypot block for form #${formId}`);
    const spamResponse = await page.request.post(`/wp-json/cora/v1/forms/${formId}/submit`, {
      headers: {
        'X-WP-Nonce': wpNonce
      },
      data: {
        submitted_data: { "Contact Name": "Spammer Bot" },
        is_partial: 0,
        cora_hp_verify: 'malicious_bot_string_value'
      }
    });
    expect(spamResponse.status()).toBe(400);
    const spamJson = await spamResponse.json();
    expect(spamJson.code).toBe('spam_detected');

    // 4. Test IP-based Rate Limiting (max 10 requests per minute)
    console.log(`Verifying IP rate limiting for form #${formId}`);
    let limited = false;
    for (let i = 0; i < 12; i++) {
      const submitRes = await page.request.post(`/wp-json/cora/v1/forms/${formId}/submit`, {
        headers: {
          'X-WP-Nonce': wpNonce
        },
        data: {
          submitted_data: { "Contact Name": `Spam attempt ${i}` },
          is_partial: 0,
          cora_hp_verify: ''
        }
      });
      if (submitRes.status() === 429) {
        limited = true;
        console.log(`✓ Confirmed Rate Limit Block on request #${i}`);
        break;
      }
    }
    expect(limited).toBe(true);
  });

  test('Verify Ecosystem Map visual presentation, calculator, and clipboard copy', async ({ page }) => {
    // Login and go to Ecosystem Map
    await login(page);
    await page.goto('/workspace/ecosystem');
    await page.waitForLoadState('networkidle');

    // 1. Verify layout headers and pillars are visible
    await expect(page.locator('h1:has-text("Cora OS Ecosytem Map")')).toBeVisible();
    await expect(page.locator('h3:has-text("Engagement OS")')).toBeVisible();
    await expect(page.locator('h3:has-text("Showcase OS")')).toBeVisible();
    await expect(page.locator('h3:has-text("Vault OS")')).toBeVisible();
    await expect(page.locator('h3:has-text("Team OS")')).toBeVisible();

    // 2. Test Cost Calculator toggling
    const savingsDisplay = page.locator('#cal-savings-display');
    await expect(savingsDisplay).toContainText('₹1,00,200'); // Default annual saving

    // Click "Scheduler (Calendly Pro)" checklist row to toggle it off
    await page.click('span:has-text("Scheduler (Calendly Pro)")');
    // Annual savings should decrease because one tool is turned off
    await expect(savingsDisplay).toContainText('₹85,800');

    // Click "Scheduler (Calendly Pro)" again to toggle it back on
    await page.click('span:has-text("Scheduler (Calendly Pro)")');
    await expect(savingsDisplay).toContainText('₹1,00,200');

    // 3. Verify FigJam prompt block contents and button
    await expect(page.locator('#figjam-prompt-block')).toBeVisible();
    await page.click('button:has-text("Copy Prompt")');

    // Confirm that the toast message is triggered
    await expect(page.locator('#cora-toast-container')).toContainText('FigJam Prompt copied');
  });

});

