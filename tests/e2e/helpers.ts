import { Page } from '@playwright/test';

export async function login(page: Page) {
  await page.goto('/wp-login.php');
  await page.fill('#user_login', 'cora_admin');
  await page.fill('#user_pass', 'cora_secure_pass_123');
  await page.click('#wp-submit');
  await page.waitForURL(url => url.pathname.includes('/wp-admin') || url.pathname.includes('/workspace'));
}

