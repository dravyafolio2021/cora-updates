import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Canvas GitHub Integration: Non-Technical Simplification & Connected Dashboard', () => {
    test.beforeEach(async ({ page }) => {
        await login(page);
    });

    test('State A: PAT Connected, Repo Pending shows welcoming, respectful activation card', async ({ page }) => {
        // Intercept cora_github_get_status to simulate PAT-only connected state
        await page.route('**/admin-ajax.php', async (route) => {
            const postData = route.request().postData() || '';
            if (postData.includes('action=cora_github_get_status')) {
                await route.fulfill({
                    status: 200,
                    contentType: 'application/json',
                    body: JSON.stringify({
                        success: true,
                        data: {
                            connected: true,
                            username: 'cora-creator',
                            name: 'Studio Director',
                            repo: '',
                            has_repo: false,
                            sync_mode: 'pr',
                            auto_sync: true,
                            branches: ['main'],
                            pull_requests: [],
                            recent_commits: []
                        }
                    })
                });
                return;
            }
            await route.continue();
        });

        await page.goto('/workspace/canvas');
        await page.waitForLoadState('domcontentloaded');

        // Switch to GitHub tab
        const ghTab = page.locator('#subtab-btn-tab-canvas-github');
        await expect(ghTab).toBeVisible();
        await ghTab.click();
        await page.waitForTimeout(600);

        // Verify the welcoming activation card is visible
        const repoStep = page.locator('#cora-gh-unconnected-repo-step');
        await expect(repoStep).toBeVisible();

        // Check username display
        await expect(page.locator('#cora-gh-connected-user-name')).toHaveText('@cora-creator');

        // Check respectful headline
        await expect(repoStep.locator('h3')).toContainText('Activate Automated Cloud Protection');

        // Check 1-click activation button
        const createBtn = page.locator('#cora-gh-btn-create-repo');
        await expect(createBtn).toBeVisible();
        await expect(createBtn).toContainText('Activate Autonomous Backup & Protection');

        // Check that existing repo link is tucked inside a details element
        const detailsEl = repoStep.locator('details');
        await expect(detailsEl).toBeVisible();
        await expect(detailsEl.locator('summary')).toContainText('Link an existing GitHub repository instead');

        // Capture screenshot
        await page.screenshot({
            path: '/Users/shrutian/.gemini/antigravity/brain/e7db172d-afc3-4021-b0cc-a3073c916f94/cora_canvas_github_pat_connected_welcome.png',
            fullPage: false
        });
    });

    test('State B: Fully Connected shows Live vs Draft Scope Distinction, Timeline & hidden developer tools', async ({ page }) => {
        // Intercept cora_github_get_status to simulate fully connected workspace with Live & Draft commits
        await page.route('**/admin-ajax.php', async (route) => {
            const postData = route.request().postData() || '';
            if (postData.includes('action=cora_github_get_status')) {
                await route.fulfill({
                    status: 200,
                    contentType: 'application/json',
                    body: JSON.stringify({
                        success: true,
                        data: {
                            connected: true,
                            username: 'cora-creator',
                            name: 'Studio Director',
                            repo: 'cora-creator/cora-website-studio',
                            has_repo: true,
                            sync_mode: 'pr',
                            auto_sync: true,
                            branches: ['main', 'draft/2-summer-campaign'],
                            active_branch: 'main',
                            workspace_themes: [
                                { id: 1, name: 'Creative Agency & Video Studio', status: 'live', branch: 'main' },
                                { id: 2, name: 'Summer Campaign', status: 'draft', branch: 'draft/2-summer-campaign' }
                            ],
                            pull_requests: [],
                            recent_commits: [
                                {
                                    sha: 'e4a1b2c',
                                    full_sha: 'e4a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9',
                                    message: 'publish: Home Page Layout & Hero Section · 2026-10-20 12:30 UTC',
                                    date: '2026-10-20T12:30:00Z',
                                    author: 'Studio Admin',
                                    branch: 'main',
                                    url: 'https://github.com/cora-creator/cora-website-studio/commit/e4a1b2c'
                                },
                                {
                                    sha: 'c8f9a0b',
                                    full_sha: 'c8f9a0b1c2d3e4f5a6b7c8d9e0f1a2b3c4d5e6f7',
                                    message: 'draft: About Studio Story & Team Bio · 2026-10-20 11:15 UTC',
                                    date: '2026-10-20T11:15:00Z',
                                    author: 'Studio Admin',
                                    branch: 'draft/2-summer-campaign',
                                    url: 'https://github.com/cora-creator/cora-website-studio/commit/c8f9a0b'
                                },
                                {
                                    sha: 'a1b2c3d',
                                    full_sha: 'a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0',
                                    message: 'chore: initialise Cora manifest',
                                    date: '2026-10-20T10:00:00Z',
                                    author: 'Studio Admin',
                                    branch: 'main',
                                    url: 'https://github.com/cora-creator/cora-website-studio/commit/a1b2c3d'
                                }
                            ]
                        }
                    })
                });
                return;
            }
            await route.continue();
        });

        await page.goto('/workspace/canvas');
        await page.waitForLoadState('domcontentloaded');

        // Switch to GitHub tab
        const ghTab = page.locator('#subtab-btn-tab-canvas-github');
        await expect(ghTab).toBeVisible();
        await ghTab.click();
        await page.waitForTimeout(800);

        // Verify connected dashboard is visible
        const dashboard = page.locator('#cora-gh-connected-dashboard');
        await expect(dashboard).toBeVisible();

        // 1. Status Cards Verification
        const statAccount = page.locator('#cora-gh-stat-account');
        await expect(statAccount).toHaveText('@cora-creator');

        const statRepo = page.locator('#cora-gh-stat-repo-link');
        await expect(statRepo).toHaveText('cora-creator/cora-website-studio');

        const statAuto = page.locator('#cora-gh-stat-auto-status');
        await expect(statAuto).toBeVisible();

        // 2. Scope Filter Verification (Live Theme vs Drafts with human-readable names and zero emojis)
        const scopeFilter = page.locator('#cora-gh-timeline-branch-filter');
        await expect(scopeFilter).toBeVisible();
        await expect(scopeFilter).toContainText('Live Theme: Creative Agency & Video Studio (main)');
        await expect(scopeFilter).toContainText('Draft: Summer Campaign');

        const scopeFilterText = await scopeFilter.innerText();
        expect(scopeFilterText).not.toContain('🟢');
        expect(scopeFilterText).not.toContain('🔵');
        expect(scopeFilterText).not.toContain('⚪');

        const scopeBadge = page.locator('#cora-gh-timeline-scope-badge');
        await expect(scopeBadge).toBeVisible();

        // 3. Snapshot Timeline & 1-Click Restore Verification
        const timeline = page.locator('#cora-gh-commits-list-container');
        await expect(timeline).toBeVisible();

        // Check Live vs Draft scope pills on snapshots
        await expect(timeline).toContainText('Live: Creative Agency & Video Studio');
        await expect(timeline).toContainText('Draft: Summer Campaign');

        const timelineText = await timeline.innerText();
        expect(timelineText).not.toContain('🟢');
        expect(timelineText).not.toContain('🔵');
        expect(timelineText).not.toContain('⚪');

        // Check human-friendly labels
        await expect(timeline).toContainText('Published');
        await expect(timeline).toContainText('Draft Saved');
        await expect(timeline).toContainText('Initialized');
        await expect(timeline).toContainText('Restore Version');

        // 4. Developer Controls Verification: Must be collapsed by default
        const advancedDetails = page.locator('details.cora-advanced-git-details');
        await expect(advancedDetails).toBeVisible();
        const isOpen = await advancedDetails.evaluate((el: HTMLDetailsElement) => el.open);
        expect(isOpen).toBe(false);

        // Capture screenshot of clean, respectful non-technical connected dashboard
        await page.screenshot({
            path: '/Users/shrutian/.gemini/antigravity/brain/e7db172d-afc3-4021-b0cc-a3073c916f94/cora_canvas_github_connected_peaceful.png',
            fullPage: false
        });

        // 5. Expanding Advanced Controls reveals technical tools without errors
        await advancedDetails.locator('summary').click();
        await page.waitForTimeout(300);
        const isOpenAfter = await advancedDetails.evaluate((el: HTMLDetailsElement) => el.open);
        expect(isOpenAfter).toBe(true);

        await expect(page.locator('#cora-gh-mode-card-pr')).toBeVisible();
        await expect(page.locator('#cora-gh-btn-commit-action')).toBeVisible();

        // Capture screenshot of expanded developer controls
        await page.screenshot({
            path: '/Users/shrutian/.gemini/antigravity/brain/e7db172d-afc3-4021-b0cc-a3073c916f94/cora_canvas_github_connected_developer_expanded.png',
            fullPage: false
        });
    });
});
