import { test, expect } from '@playwright/test';
import { login } from './helpers';
import * as path from 'path';

test.describe('Canvas GitHub Real Integration: Draft Theme Names, Zero Emojis & Standard Tab Styling', () => {

    test('Loads live workspace draft themes by name, zero emojis, and standard uniform tab', async ({ page }) => {
        // 1. Authenticate with Studio Owner credentials
        await page.setViewportSize({ width: 1366, height: 900 });
        await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

        // 2. Open Canvas on GitHub tab directly
        await page.goto('/workspace/canvas?tab=github');
        await page.waitForLoadState('domcontentloaded');
        await page.waitForTimeout(1500);

        // 3. Verify GitHub Subtab has zero special treatment and matches all other tabs
        const ghTab = page.locator('#subtab-btn-tab-canvas-github');
        await expect(ghTab).toBeVisible();
        await expect(ghTab).toContainText('GitHub');

        // Verify absence of overstyling (no rings, no rounded card backgrounds, no special badge)
        const tabClasses = await ghTab.getAttribute('class') || '';
        expect(tabClasses).not.toContain('ring-1');
        expect(tabClasses).not.toContain('bg-emerald-50');
        expect(tabClasses).not.toContain('rounded-lg');

        // Verify badge inside tab button is gone or not rendered
        const badge = ghTab.locator('#cora-github-subtab-badge');
        await expect(badge).toHaveCount(0);

        // 4. Verify Theme Scope filter dropdown
        const scopeFilter = page.locator('#cora-gh-timeline-branch-filter');
        await expect(scopeFilter).toBeVisible();

        const scopeText = await scopeFilter.innerText();
        console.log('Scope selector options:\n', scopeText);

        // Assert Live Theme name is shown
        await expect(scopeFilter).toContainText('Live Theme: Creative Agency & Video Studio (main)');

        // Assert Draft Themes from database are shown by their human names
        await expect(scopeFilter).toContainText('Draft: Luxury Real Estate & Property Showcase');
        await expect(scopeFilter).toContainText('Draft: Claraverse');

        // Assert strictly zero emojis across the scope selector
        expect(scopeText).not.toContain('🟢');
        expect(scopeText).not.toContain('🔵');
        expect(scopeText).not.toContain('⚪');

        // 5. Verify Timeline Scope Badge
        const scopeBadge = page.locator('#cora-gh-timeline-scope-badge');
        await expect(scopeBadge).toBeVisible();
        const badgeText = await scopeBadge.innerText();
        expect(badgeText).toContain('Currently showing');
        expect(badgeText).toContain('snapshots');
        expect(badgeText).not.toContain('🟢');
        expect(badgeText).not.toContain('🔵');
        expect(badgeText).not.toContain('⚪');

        // 6. Test switching scope to a draft theme
        const luxuryOption = scopeFilter.locator('option', { hasText: 'Luxury Real Estate' });
        const luxuryOptionValue = await luxuryOption.getAttribute('value');
        if (luxuryOptionValue) {
            await scopeFilter.selectOption(luxuryOptionValue);

            // Scope badge should update to the selected draft theme
            await expect(scopeBadge).toContainText('Luxury Real Estate & Property Showcase', { timeout: 10000 });
            const updatedBadgeText = await scopeBadge.innerText();
            expect(updatedBadgeText).not.toContain('🟢');
            expect(updatedBadgeText).not.toContain('🔵');
        }

        // 7. Capture visual confirmation screenshot
        const screenshotPath = path.resolve(
            '/Users/shrutian/.gemini/antigravity/brain/e7db172d-afc3-4021-b0cc-a3073c916f94/cora_canvas_github_live_draft_themes_verified.png'
        );
        await page.screenshot({ path: screenshotPath, fullPage: false });
        console.log('Saved screenshot to:', screenshotPath);
    });

    test('Mobile Viewport (390x844): Highly optimized compact layout, single-row status cards & timeline visible', async ({ page }) => {
        // 1. Mobile viewport (iPhone 12/13/14/15 size)
        await page.setViewportSize({ width: 390, height: 844 });
        await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

        // 2. Open Canvas on GitHub tab directly
        await page.goto('/workspace/canvas?tab=github');
        await page.waitForLoadState('domcontentloaded');
        await page.waitForTimeout(2000);

        // 3. Verify Connected Dashboard is visible
        const connectedDash = page.locator('#cora-gh-connected-dashboard');
        await expect(connectedDash).toBeVisible();

        // 4. Verify 3-card micro grid exists and has grid-cols-3
        const microGrid = connectedDash.locator('.grid.grid-cols-3');
        await expect(microGrid).toBeVisible();

        // Verify all 3 cards exist
        const statAccount = page.locator('#cora-gh-stat-account');
        const statRepo = page.locator('#cora-gh-stat-repo-link');
        const statShield = page.locator('#cora-gh-stat-auto-status');
        await expect(statAccount).toBeVisible();
        await expect(statRepo).toBeVisible();
        await expect(statShield).toBeVisible();

        // 5. Verify Timeline Card is visible above or within comfortable view
        const timelineHeading = page.locator('h3:has-text("Workspace Snapshot Timeline")');
        await expect(timelineHeading).toBeVisible();

        // 6. Verify Scope selector is fully visible and usable
        const scopeSelect = page.locator('#cora-gh-timeline-branch-filter');
        await expect(scopeSelect).toBeVisible();

        // 7. Verify NO horizontal overflow on the body / document
        const scrollWidth = await page.evaluate(() => document.documentElement.scrollWidth);
        const clientWidth = await page.evaluate(() => document.documentElement.clientWidth);
        expect(scrollWidth).toBeLessThanOrEqual(clientWidth + 2); // allowing subpixel tolerance

        // 8. Capture mobile screenshot
        const mobileScreenshot = path.resolve(
            '/Users/shrutian/.gemini/antigravity/brain/e7db172d-afc3-4021-b0cc-a3073c916f94/cora_canvas_github_mobile_optimized.png'
        );
        await page.screenshot({ path: mobileScreenshot, fullPage: false });
        console.log('Saved mobile screenshot to:', mobileScreenshot);
    });

});
