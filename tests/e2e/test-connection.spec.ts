import { test, expect } from '@playwright/test';

test('inspect media-editor attachment options', async ({ page }) => {
  await page.goto('/wp-login.php');
  await page.waitForSelector('#user_login');
  await page.evaluate(() => {
    (document.querySelector('#user_login') as HTMLInputElement).value = 'cora_admin';
    (document.querySelector('#user_pass') as HTMLInputElement).value = 'cora_secure_pass_123';
  });
  await page.click('#wp-submit');

  
  await page.waitForURL(/.*(wp-admin|workspace).*/);


  await page.goto('/workspace/media-editor');
  
  const select = page.locator('#cora-editor-media-select');
  await expect(select).toBeVisible();
  const options = await select.locator('option').allTextContents();
  console.log('Attachment Options:', options);
  expect(options.length).toBeGreaterThan(0);
});


