import { Page } from '@playwright/test';

export async function login(page: Page) {
  await page.goto('/wp-login.php');
  // If already logged in and redirected to wp-admin or workspace, return immediately
  const isDocWorkspace = (urlStr: string) => {
    return urlStr.includes('/wp-admin') || (urlStr.includes('/workspace') && !urlStr.includes('/login') && !urlStr.includes('/register') && !urlStr.includes('/forgot-password') && !urlStr.includes('/reset-password'));
  };

  if (isDocWorkspace(page.url())) {
    await page.evaluate(() => {
      localStorage.setItem('cora_re_tour_completed', 'true');
      localStorage.setItem('cora_studio_tour_completed', 'true');
    });
    return;
  }
  try {
    // Wait for either the custom login input or default WordPress login input to appear
    await Promise.race([
      page.waitForSelector('#login-email', { timeout: 10000 }),
      page.waitForSelector('#user_login', { timeout: 10000 })
    ]);
    
    const customEmail = await page.$('#login-email');
    if (customEmail) {
      // Custom login page
      await page.fill('#login-email', 'admin@cora.local');
      await page.fill('#login-password', 'cora_secure_pass_123');
      await page.click('#login-btn');
    } else {
      // Default WordPress login page
      await page.fill('#user_login', 'cora_admin');
      await page.fill('#user_pass', 'cora_secure_pass_123');
      await page.click('#wp-submit');
    }
  } catch (e) {
    if (isDocWorkspace(page.url())) {
      await page.evaluate(() => {
        localStorage.setItem('cora_re_tour_completed', 'true');
        localStorage.setItem('cora_studio_tour_completed', 'true');
      });
      return;
    }
    throw e;
  }
  await page.waitForURL(url => isDocWorkspace(url.href), { timeout: 20000 });
  await page.evaluate(() => {
    localStorage.setItem('cora_re_tour_completed', 'true');
    localStorage.setItem('cora_studio_tour_completed', 'true');
  });
}

