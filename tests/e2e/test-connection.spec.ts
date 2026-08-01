import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('inspect media-editor attachment options', async ({ page }) => {
  await login(page);

  await page.goto('/workspace/media-editor');
  
  const select = page.locator('#cora-editor-media-select');
  await expect(select).toBeVisible();
  const options = await select.locator('option').allTextContents();
  console.log('Attachment Options:', options);
  expect(options.length).toBeGreaterThan(0);
});


