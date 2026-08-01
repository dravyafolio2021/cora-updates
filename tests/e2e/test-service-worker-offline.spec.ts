import { test, expect } from '@playwright/test';

test('verify custom offline fallback page elements and actions', async ({ page }) => {
  // 1. Navigate directly to the custom offline fallback page
  await page.goto('/cora-offline.html');

  // 2. Verify page title
  await expect(page).toHaveTitle('Workspace Connection Idle');

  // 3. Verify System Idle badge and pulsing dot
  const statusBadge = page.locator('.status-badge');
  await expect(statusBadge).toBeVisible();
  await expect(statusBadge.locator('.status-dot')).toBeVisible();
  await expect(statusBadge).toHaveText('SYSTEM IDLE');

  // 4. Verify WiFi-Off icon is visible
  const svgIcon = page.locator('.icon-container svg');
  await expect(svgIcon).toBeVisible();

  // 5. Verify central message headers and text
  await expect(page.locator('h1')).toHaveText('Workspace Connection Idle');
  await expect(page.locator('p')).toContainText('We are currently updating files or performing routine database syncs');

  // 6. Verify CTA buttons are configured properly
  const retryBtn = page.locator('button.btn-primary');
  await expect(retryBtn).toBeVisible();
  await expect(retryBtn).toHaveText('Retry Connection');

  const workspaceLink = page.locator('a.btn-secondary');
  await expect(workspaceLink).toBeVisible();
  await expect(workspaceLink).toHaveAttribute('href', '/workspace');

  const supportLink = page.locator('.footer-link');
  await expect(supportLink).toBeVisible();
  await expect(supportLink).toHaveAttribute('href', 'mailto:support@heycora.in?subject=Workspace%20Maintenance%20Query');

  // 7. Verify theme colors are correctly configured via CSS variables
  const bgColor = await page.evaluate(() => {
    const el = document.documentElement;
    return getComputedStyle(el).getPropertyValue('--bg-color').trim();
  });
  // Light mode background should be the premium zinc-neutral tone
  expect(bgColor).toBe('#fafafa');
});

