import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Tenant Preview Isolation and Cookie Protection', () => {

  test.beforeEach(async ({ page }) => {
    // Log in as Platform Super Admin to have a valid session
    await login(page, 'admin@cora.local', 'cora_secure_pass_123');
  });

  test('Valid Preview of Agency 1 draft theme on Agency 1 context', async ({ page }) => {
    // Agency 1 (home page) with theme 192 (its draft theme)
    await page.goto('http://cora.local/?cv_preview_theme=192', { waitUntil: 'networkidle' });

    // Verify URL contains cv_preview_theme=192 (sticky rewrite is active)
    expect(page.url()).toContain('cv_preview_theme=192');

    // Verify cookie is set
    const cookies = await page.context().cookies();
    const previewCookie = cookies.find(c => c.name === 'cora_preview_theme_id');
    expect(previewCookie).toBeDefined();
    expect(previewCookie?.value).toBe('192');
  });

  test('Valid Preview of Agency 5 draft theme on Agency 5 context', async ({ page }) => {
    // Agency 5 (slug: test-studio) with theme 555 (its draft theme)
    await page.goto('http://cora.local/test-studio/?cv_preview_theme=555', { waitUntil: 'networkidle' });

    // Verify URL contains cv_preview_theme=555 (sticky rewrite is active)
    expect(page.url()).toContain('cv_preview_theme=555');

    // Verify cookie is set
    const cookies = await page.context().cookies();
    const previewCookie = cookies.find(c => c.name === 'cora_preview_theme_id');
    expect(previewCookie).toBeDefined();
    expect(previewCookie?.value).toBe('555');
  });

  test('Cross-Tenant Preview Restriction: Agency 1 theme on Agency 5 context', async ({ page }) => {
    // Clear cookies first to start fresh
    await page.context().clearCookies();
    await login(page, 'admin@cora.local', 'cora_secure_pass_123');

    // Attempt to preview Agency 1 theme 192 on Agency 5 path (/test-studio/)
    await page.goto('http://cora.local/test-studio/?cv_preview_theme=192', { waitUntil: 'networkidle' });

    // The cookie should NOT be set to 192
    const cookies = await page.context().cookies();
    const previewCookie = cookies.find(c => c.name === 'cora_preview_theme_id');
    expect(previewCookie?.value).not.toBe('192');

    // The query param cv_preview_theme should be removed from the URL by JS rewrite since it's not a preview
    expect(page.url()).not.toContain('cv_preview_theme=192');
  });

  test('Live Theme Exclusion: Live theme 44 on Agency 1 context should not preview', async ({ page }) => {
    // Clear cookies first to start fresh
    await page.context().clearCookies();
    await login(page, 'admin@cora.local', 'cora_secure_pass_123');

    // Attempt to preview live theme 44 on Agency 1 path (/)
    await page.goto('http://cora.local/?cv_preview_theme=44', { waitUntil: 'networkidle' });

    // The cookie should NOT be set
    const cookies = await page.context().cookies();
    const previewCookie = cookies.find(c => c.name === 'cora_preview_theme_id');
    expect(previewCookie).toBeUndefined();

    // The query param cv_preview_theme should be deleted from the URL by JS history rewrite
    expect(page.url()).not.toContain('cv_preview_theme');
  });

  test('Query Param Cleanup: Removing cv_preview_theme parameter manually from URL cleans the session', async ({ page }) => {
    // Start by accessing valid preview
    await page.goto('http://cora.local/?cv_preview_theme=192', { waitUntil: 'networkidle' });
    expect(page.url()).toContain('cv_preview_theme=192');

    // Navigate to homepage with exit_preview parameter to clear session
    await page.goto('http://cora.local/?exit_preview=1', { waitUntil: 'networkidle' });

    // Verify cookie is cleared
    const cookies = await page.context().cookies();
    const previewCookie = cookies.find(c => c.name === 'cora_preview_theme_id');
    expect(previewCookie).toBeUndefined();
  });

});
