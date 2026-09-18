import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.use({
    viewport: { width: 375, height: 667 },
    isMobile: true,
    hasTouch: true
});

test('verify mobile view of Clients Directory, mobile cards, search/filter, and bottom-sheet drawer', async ({ page }) => {
    page.on('pageerror', exception => {
        console.log(`Uncaught exception: "${exception.message}"`);
    });

    // 1. Login & navigate to Clients Directory
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
    await page.goto('/studio/clients');
    await page.waitForLoadState('networkidle');

    // 2. Verify Mobile Header CTA text
    const headerCta = page.locator('#btn-add-client:visible');
    await expect(headerCta).toBeVisible({ timeout: 10000 });
    const ctaText = await headerCta.innerText();
    console.log('Mobile CTA Text:', ctaText.trim());
    // Verify it doesn't have duplicate plus
    expect(ctaText.trim()).not.toContain('+ +');

    // 3. Verify KPI Grid is compact and visible
    await expect(page.locator('#metric-total-clients')).toBeVisible();

    // 4. Verify Desktop table is hidden and Mobile Cards are visible
    const desktopTable = page.locator('.client-table-row').first();
    await expect(desktopTable).toBeHidden();

    const mobileCards = page.locator('.client-mobile-card');
    const cardCount = await mobileCards.count();
    console.log(`Mobile cards rendered: ${cardCount}`);
    expect(cardCount).toBeGreaterThanOrEqual(1);
    await expect(mobileCards.first()).toBeVisible();

    // Take screenshot of mobile Directory
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/mobile-clients-directory-cards.png' });

    // 5. Test Filter on Mobile Cards
    const activeFilterBtn = page.locator('.clients-filter-btn[data-filter="active"]');
    await activeFilterBtn.click();
    await page.waitForTimeout(300);
    const visibleActiveCards = page.locator('.client-mobile-card:visible');
    expect(await visibleActiveCards.count()).toBeGreaterThanOrEqual(1);

    const vipFilterBtn = page.locator('.clients-filter-btn[data-filter="vip"]');
    await vipFilterBtn.click();
    await page.waitForTimeout(300);

    const allFilterBtn = page.locator('.clients-filter-btn[data-filter="all"]');
    await allFilterBtn.click();
    await page.waitForTimeout(300);

    // 6. Test Search on Mobile Cards
    const searchInput = page.locator('#clients-search-input');
    await searchInput.fill('Rohan');
    await page.waitForTimeout(300);
    const searchCard = page.locator('.client-mobile-card:visible');
    await expect(searchCard.first()).toContainText('Rohan');
    await searchInput.clear();
    await page.waitForTimeout(300);

    // 7. Test Mobile Bottom Sheet Drawer
    const detailsBtn = page.locator('.client-mobile-card:visible button:has-text("Details →")').first();
    await detailsBtn.click();
    await page.waitForTimeout(400);

    const drawer = page.locator('#cora-client-drawer');
    await expect(drawer).toHaveClass(/open/);
    await expect(page.locator('#drawer-client-name')).toBeVisible();

    // Verify Mobile Drag Handle
    const dragHandle = drawer.locator('.bg-zinc-300.rounded-full');
    await expect(dragHandle).toBeVisible();

    // Take screenshot of Mobile Bottom Sheet Drawer
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/mobile-client-bottom-sheet-drawer.png' });

    // Test Drawer tabs on mobile
    await page.locator('#drawer-tab-btn-financials').click();
    await page.waitForTimeout(300);
    await expect(page.locator('#drawer-view-financials')).toBeVisible();

    await page.locator('#drawer-tab-btn-portal').click();
    await page.waitForTimeout(300);
    await expect(page.locator('#drawer-view-portal')).toBeVisible();

    // Close Drawer
    await page.locator('#btn-close-client-drawer').click();
    await page.waitForTimeout(400);
    await expect(drawer).not.toHaveClass(/open/);

    // 8. Test Tab 3: Invoices & Retainers on Mobile
    const moreBtn = page.locator('#mobile-tabs-more-btn');
    if (await moreBtn.isVisible()) {
        await moreBtn.click();
        await page.waitForTimeout(300);
        const mobileInvoiceTab = page.locator('#mobile-tabs-more-dropdown [data-target="invoices"], #mobile-tab-clients-invoices').first();
        await mobileInvoiceTab.click();
    } else {
        await page.evaluate(() => window.coraSwitchClientSubtab('invoices'));
    }
    await page.waitForTimeout(400);

    const invoicesPanel = page.locator('#clients-tab-content-invoices');
    await expect(invoicesPanel).toBeVisible();
    const invoiceCards = page.locator('#invoices-mobile-card-list > div');
    expect(await invoiceCards.count()).toBeGreaterThanOrEqual(1);
    await expect(invoiceCards.first()).toBeVisible();

    // Take screenshot of Mobile Invoices
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/mobile-clients-invoices-cards.png' });
});
