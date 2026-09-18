import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('verify clean non-technical prospect detail workspace drawer', async ({ page }) => {
    page.on('pageerror', exception => {
        console.log(`Uncaught exception: "${exception.message}"`);
    });
    page.on('console', msg => {
        console.log(`Console [${msg.type()}]: ${msg.text()}`);
    });
    await login(page);
    await page.goto('/workspace/leads');
    await page.waitForLoadState('networkidle');

    // Wait for Kanban board to load
    const leadCard = page.locator('.cora-lead-card').first();
    await expect(leadCard).toBeVisible({ timeout: 10000 });
    await leadCard.click();
    await page.waitForTimeout(400);

    const drawerInfo = await page.evaluate(() => {
        const d = document.getElementById('cora-lead-detail-drawer');
        const scroll = document.getElementById('cora-drawer-scroll-container');
        const tabOverview = document.getElementById('cora-lead-detail-tab-overview');
        return {
            drawerExists: !!d,
            drawerDisplay: d ? window.getComputedStyle(d).display : null,
            drawerWidth: d ? window.getComputedStyle(d).width : null,
            scrollExists: !!scroll,
            scrollDisplay: scroll ? window.getComputedStyle(scroll).display : null,
            scrollHeight: scroll ? window.getComputedStyle(scroll).height : null,
            scrollInnerHTML: scroll ? scroll.innerHTML.slice(0, 300) : null,
            tabOverviewExists: !!tabOverview,
            tabOverviewDisplay: tabOverview ? window.getComputedStyle(tabOverview).display : null,
            tabOverviewClasses: tabOverview ? tabOverview.className : null,
            tabOverviewChildren: tabOverview ? tabOverview.children.length : 0,
            avatarInitial: document.getElementById('cora-drawer-avatar-initial') ? document.getElementById('cora-drawer-avatar-initial').outerHTML : null,
            leadNameEl: document.getElementById('cora-drawer-lead-name') ? document.getElementById('cora-drawer-lead-name').outerHTML : null
        };
    });
    console.log('DRAWER INFO EVALUATED:', JSON.stringify(drawerInfo, null, 2));

    const drawer = page.locator('#cora-lead-detail-drawer');
    await expect(drawer).toBeVisible();

    // Take screenshot of Tab 1: Overview
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/prospect-drawer-overview-live.png' });

    // 2. Click 'Automation' tab
    await page.click('#cora-lead-detail-tab-btn-automation');
    await page.waitForTimeout(300);
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/prospect-drawer-automation-live.png' });

    // 3. Click 'Checklist' tab
    await page.click('#cora-lead-detail-tab-btn-checklist');
    await page.waitForTimeout(300);
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/prospect-drawer-checklist-live.png' });

    // 4. Click 'Activity Log' tab
    await page.click('#cora-lead-detail-tab-btn-audit');
    await page.waitForTimeout(300);
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/prospect-drawer-audit-live.png' });
});
