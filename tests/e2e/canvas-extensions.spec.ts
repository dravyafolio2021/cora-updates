import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Canvas Advanced Extensions & Competitor Alignment E2E Tests', () => {

  test('Should test AI Page Generator, Template Presets, and Header/Footer configuration', async ({ page }) => {
    // 1. Login and go to Canvas Hub
    await login(page);
    await page.goto('/workspace/canvas');
    await page.waitForSelector('#canvas-level-1', { state: 'visible' });

    // Assert Level 1 headers and new extensions panels are visible
    await expect(page.locator('#canvas-level-1 h1')).toContainText('Canvas');
    await expect(page.locator('h3:has-text("AI Page Creator")')).toBeVisible();
    await expect(page.locator('h3:has-text("Canvas Analytics")')).toBeVisible();

    // 2. Test Template Preset instantiation
    await page.click('button:has-text("Virtual Tour")');
    // Wait for reload and page recovery
    await page.waitForSelector('#canvas-level-1', { state: 'visible' });

    // 3. Test AI Prompt to Page creation
    await page.fill('#canvas-ai-prompt', 'Dark luxury villa collection in Vasant Vihar');
    await page.click('button:has-text("Generate")');
    // Wait for page creation AJAX and reload
    await page.waitForSelector('#canvas-level-1', { state: 'visible' });

    // 4. Go to Level 2 (Theme Dashboard) by clicking "Edit Theme"
    await page.click('button:has-text("Edit Theme")');
    await page.waitForSelector('#canvas-level-2', { state: 'visible' });

    // 5. Switch to Theme Settings tab
    await page.click('#tab-btn-settings');
    await page.waitForSelector('#tab-content-settings:not(.hidden)', { state: 'visible' });

    // Fill Header & Footer Configuration options
    await page.fill('#setting-facebook-link', 'https://facebook.com/apexrealty');
    await page.fill('#setting-twitter-link', 'https://twitter.com/apexrealty');
    await page.fill('#setting-linkedin-link', 'https://linkedin.com/in/apexrealty');
    await page.fill('#setting-copyright-text', '© 2026 Apex Luxury Holdings. All rights reserved.');

    // Save configurations
    await page.click('button:has-text("Save Settings")');
    const toast = page.locator('#cora-toast-container');
    await expect(toast).toContainText('Settings parameters synchronized successfully.');

    // Exit Level 2 back to Level 1 Theme Dashboard
    await page.click('button[onclick="backToCanvasHub()"]');
    await page.waitForSelector('#canvas-level-1', { state: 'visible' });
  });

});
