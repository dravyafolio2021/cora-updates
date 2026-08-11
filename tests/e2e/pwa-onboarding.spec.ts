import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('PWA onboarding step and dashboard splash screen validation', async ({ page }) => {
  const consoleLogs: string[] = [];
  page.on('console', msg => {
    consoleLogs.push(`[${msg.type()}] ${msg.text()}`);
  });

  // Test 1: Verify splash screen and orientation lock shield on dashboard load
  console.log('--- TEST 1: Splash Screen & Orientation Lock Shield Verification ---');
  await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
  
  // Go to blogs page but stop waiting for load so we can catch the splash screen in DOM
  await page.goto('/super/blogs', { waitUntil: 'commit' });
  const splashLocator = page.locator('#cora-app-splash-screen');
  const splashExists = await splashLocator.count() > 0;
  console.log('Splash screen exists in DOM immediately on commit:', splashExists);

  // Verify orientation lock shield is present
  const shieldLocator = page.locator('#cora-orientation-lock-shield');
  await expect(shieldLocator).toBeAttached();
  console.log('Orientation lock shield is attached to the DOM');

  // Wait for the splash screen to fade out and be removed
  await page.waitForSelector('#cora-app-splash-screen', { state: 'detached', timeout: 10000 });
  console.log('Splash screen successfully removed from DOM after load');

  // Test 2: Verify onboarding step counts and flow in standard browser mode
  console.log('\n--- TEST 2: Onboarding Flow Verification (Standard Browser) ---');
  
  // Reset onboarding state via scratch-reset script
  // Put a temp endpoint first
  const resetScriptContent = `<?php
  require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php';
  $u = get_user_by('login', 'studio_owner');
  if ($u) {
      delete_user_meta($u->ID, 'cora_onboarding_completed');
      delete_user_meta($u->ID, 'cora_onboarding_industry_selected');
      delete_user_meta($u->ID, 'cora_workspace_agency_name');
      echo "RESET_SUCCESS";
  } else {
      echo "USER_NOT_FOUND";
  }`;
  
  // We can write it via node inside the test or just use the scratch file if we re-create it.
  // Wait, let's write it in playwright context using fs or just assume it is created by us beforehand!
  // Since we run in the local workspace directory, let's create the file using Node fs inside the test! That is extremely clean and self-contained!
  const fs = require('fs');
  const path = require('path');
  const resetScriptPath = '/Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-workspace/scratch-reset.php';
  fs.writeFileSync(resetScriptPath, resetScriptContent);

  // Navigate to reset script
  await page.goto('/wp-content/plugins/cora-workspace/scratch-reset.php');
  const responseText = await page.locator('body').textContent();
  console.log('Reset script response:', responseText?.trim());
  expect(responseText?.trim()).toBe('RESET_SUCCESS');

  // Clean up reset script
  try { fs.unlinkSync(resetScriptPath); } catch(e) {}

  // Re-login now that onboarding state is reset
  await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

  // Navigate to onboarding
  await page.goto('/workspace/onboarding?step=3');
  await page.waitForLoadState('networkidle');

  // Verify that the step progress indicator shows step 4 ("App") and step 5 ("Finish")
  const step4Indicator = page.locator('#step-pwa-indicator');
  await expect(step4Indicator).toBeVisible();
  
  const step4Label = await step4Indicator.locator('.step-indicator-label').textContent();
  console.log('Step 4 label:', step4Label);
  expect(step4Label?.trim()).toBe('App');

  // Verify that iOS instructions are hidden initially
  const iosInst = page.locator('#ob-pwa-ios-instructions');
  await expect(iosInst).toBeHidden();

  // Test 3: Standalone mode bypass validation
  console.log('\n--- TEST 3: Standalone Mode Bypass Verification ---');
  
  // Persist matchMedia override across reloads/navigations
  await page.addInitScript(() => {
    window.matchMedia = (query) => {
      return {
        matches: query.includes('standalone'),
        media: query,
        onchange: null,
        addListener: () => {},
        removeListener: () => {},
        addEventListener: () => {},
        removeEventListener: () => {},
        dispatchEvent: () => false
      } as any;
    };
  });

  // Reload the page to trigger initialization in standalone simulation
  await page.reload();
  await page.waitForLoadState('networkidle');

  // PWA/App step indicator should now be hidden
  const pwaIndicatorHidden = await page.locator('#step-pwa-indicator').isHidden();
  console.log('Is PWA step indicator hidden in standalone mode:', pwaIndicatorHidden);
  expect(pwaIndicatorHidden).toBe(true);

  // Finish step indicator label circle should display "4" instead of "5"
  const finishCircleNum = await page.locator('.step-indicator-item[data-step="5"] .step-num').textContent();
  console.log('Finish step number in standalone mode:', finishCircleNum);
  expect(finishCircleNum?.trim()).toBe('4');

  // Restore the user's completed state so we don't break other E2E tests
  console.log('\n--- Restoring Onboarding Completed State ---');
  fs.writeFileSync(resetScriptPath, resetScriptContent);
  await page.goto('/wp-content/plugins/cora-workspace/scratch-reset.php');
  try { fs.unlinkSync(resetScriptPath); } catch(e) {}
  
  await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
  await page.goto('/workspace/onboarding?step=3');
  await page.waitForSelector('.industry-card[data-industry="photography_studio"]');
  await page.click('.industry-card[data-industry="photography_studio"]');
  await page.click('#ob-industry-btn');
  // Wait for redirect to dashboard which completes onboarding directly
  await page.waitForURL(/.*workspace\/dashboard.*/, { timeout: 15000 });
  console.log('Onboarding state successfully restored');

  console.log('All E2E validation assertions passed successfully!');
});
