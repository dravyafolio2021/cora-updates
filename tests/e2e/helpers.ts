import { Page } from '@playwright/test';

export async function login(page: Page) {
  await page.goto('/wp-login.php');
  // If already logged in and redirected to wp-admin or workspace, return immediately
  if (page.url().includes('/wp-admin') || page.url().includes('/workspace')) {
    await page.evaluate(() => {
      localStorage.setItem('cora_re_tour_completed', 'true');
      localStorage.setItem('cora_studio_tour_completed', 'true');
    });
    return;
  }
  try {
    await page.waitForSelector('#user_login', { timeout: 5000 });
    await page.evaluate(() => {
      const u = document.querySelector('#user_login') as HTMLInputElement;
      const p = document.querySelector('#user_pass') as HTMLInputElement;
      if (u) u.value = 'cora_admin';
      if (p) p.value = 'cora_secure_pass_123';
    });
    await page.click('#wp-submit');
  } catch (e) {
    if (page.url().includes('/wp-admin') || page.url().includes('/workspace')) {
      await page.evaluate(() => {
        localStorage.setItem('cora_re_tour_completed', 'true');
        localStorage.setItem('cora_studio_tour_completed', 'true');
      });
      return;
    }
    throw e;
  }
  await page.waitForURL(url => url.pathname.includes('/wp-admin') || url.pathname.includes('/workspace'));
  await page.evaluate(() => {
    localStorage.setItem('cora_re_tour_completed', 'true');
    localStorage.setItem('cora_studio_tour_completed', 'true');
  });
}

