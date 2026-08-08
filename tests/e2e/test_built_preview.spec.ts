import { test, expect } from '@playwright/test';

test('debug lovable preview page after build', async ({ page }) => {
  const consoleLogs: string[] = [];
  const pageErrors: string[] = [];

  page.on('console', msg => consoleLogs.push(`[${msg.type()}] ${msg.text()}`));
  page.on('pageerror', err => pageErrors.push(`[PAGE ERROR] ${err.message}`));

  await page.goto('http://cora.local/?cv_preview_theme=195', { waitUntil: 'networkidle' });
  await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/a4976989-efa2-4d6f-8a0a-3f15bbcd8c07/.tempmediaStorage/built_preview_test.png', fullPage: true });

  console.log('Console Logs:', consoleLogs);
  console.log('Page Errors:', pageErrors);
});
