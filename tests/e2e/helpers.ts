import { Page } from '@playwright/test';

export async function login(page: Page, username?: string, password?: string) {
  if (username) {
    await page.context().clearCookies();
  }

  await page.goto('/wp-login.php');
  // If already logged in and redirected to wp-admin or workspace, return immediately
  const isDocWorkspace = (urlStr: string) => {
    return urlStr.includes('/wp-admin') || (urlStr.includes('/workspace') && !urlStr.includes('/login') && !urlStr.includes('/register') && !urlStr.includes('/forgot-password') && !urlStr.includes('/reset-password'));
  };

  if (isDocWorkspace(page.url()) && !username) {
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
      await page.fill('#login-email', username || 'admin@cora.local');
      await page.fill('#login-password', password || 'cora_secure_pass_123');
      await page.click('#login-btn');
    } else {
      // Default WordPress login page
      await page.fill('#user_login', username || 'cora_admin');
      await page.fill('#user_pass', password || 'cora_secure_pass_123');
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

export function cleanupE2EThemes() {
  const phpBin = '/Applications/Local.app/Contents/Resources/extraResources/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php';
  const phpCode = `
    require 'app/public/wp-load.php';
    global $wpdb;
    $ids = $wpdb->get_col("SELECT id FROM {$wpdb->prefix}cora_canvas_themes WHERE name LIKE 'E2E%' OR name LIKE 'Temp E2E%' OR name LIKE '%Catalog Theme%' OR name LIKE '%Draft Starter Theme%'");
    if (!empty($ids)) {
      $p = implode(',', array_map('intval', $ids));
      $pids = $wpdb->get_col("SELECT wp_post_id FROM {$wpdb->prefix}cora_canvas_pages WHERE theme_id IN ($p)");
      foreach ($pids as $pid) {
        if ($pid > 0) wp_delete_post(intval($pid), true);
      }
      $wpdb->query("DELETE FROM {$wpdb->prefix}cora_canvas_pages WHERE theme_id IN ($p)");
      $wpdb->query("DELETE FROM {$wpdb->prefix}cora_canvas_themes WHERE id IN ($p)");
    }
  `;
  try {
    const { execFileSync } = require('child_process');
    execFileSync(phpBin, ['-r', phpCode], { stdio: 'pipe' });
  } catch (e) {}
}

