import { test, expect } from '@playwright/test';
import { login } from './helpers';
import * as path from 'path';

test.describe('Canvas Brand Tab: Connected Ecosystem & External Assets Hub', () => {

  test('Verify connected tools, asset library, filter tabs, add asset drawer, and in-app viewer', async ({ page }) => {
    // 1. Authenticate with Studio Owner credentials
    await page.setViewportSize({ width: 1366, height: 900 });
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

    // 2. Navigate directly to Canvas Brand Tab (?tab=brand)
    await page.goto('/workspace/canvas?tab=brand');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1000);

    // 3. Verify Brand tab is visible and active
    const brandTabContent = page.locator('#tab-canvas-brand');
    await expect(brandTabContent).toBeVisible();
    await expect(brandTabContent).not.toHaveClass(/hidden/);

    const brandTabBtn = page.locator('#subtab-btn-tab-canvas-brand');
    await expect(brandTabBtn).toHaveClass(/border-zinc-950/);

    // 4. Verify Section 2: Connected Brand Ecosystem
    const ecosystemHeading = brandTabContent.locator('h3:has-text("Connected Brand Ecosystem")');
    await expect(ecosystemHeading).toBeVisible();

    // Verify connector cards: Figma, Canva, Pitch, Google Fonts, Notion, Miro
    await expect(brandTabContent.locator('h4:has-text("Figma Design System")')).toBeVisible();
    await expect(brandTabContent.locator('h4:has-text("Canva Collateral")')).toBeVisible();
    await expect(brandTabContent.locator('h4:has-text("Pitch & Keynotes")')).toBeVisible();
    await expect(brandTabContent.locator('h4:has-text("Typography Catalog")')).toBeVisible();
    await expect(brandTabContent.locator('h4:has-text("Notion Brand Wiki")')).toBeVisible();
    await expect(brandTabContent.locator('h4:has-text("Miro & Canvases")')).toBeVisible();

    // 5. Verify Section 3: Universal External Brand Assets & Workspace Library
    const libraryHeading = brandTabContent.locator('h3:has-text("External Brand Assets & Workspace Library")');
    await expect(libraryHeading).toBeVisible();

    // Verify initial count badge (4 Assets)
    const countBadge = page.locator('#cora-brand-assets-count-badge');
    await expect(countBadge).toContainText('4 Assets');

    // Verify the 4 default assets are rendered
    const assetsGrid = page.locator('#cora-brand-assets-grid');
    await expect(assetsGrid).toBeVisible();

    const assetCards = assetsGrid.locator('.cora-brand-asset-card');
    await expect(assetCards).toHaveCount(4);

    await expect(assetsGrid.locator('h4:has-text("Product Architecture & User Journey Map")')).toBeVisible();
    await expect(assetsGrid.locator('h4:has-text("Studio Brand Strategy & Keynote 2026")')).toBeVisible();
    await expect(assetsGrid.locator('h4:has-text("Brand Guidelines & Tone of Voice SOP")')).toBeVisible();
    await expect(assetsGrid.locator('h4:has-text("Component Wireframes & Wireflows")')).toBeVisible();

    // 6. Test Category Filter Bar (Level 01 Segmented Controls)
    const filterBar = page.locator('#cora-brand-assets-filter-bar');

    // Filter: Whiteboards (Miro / Excalidraw) -> 2 cards
    const whiteboardFilterBtn = filterBar.locator('button[data-filter="whiteboard"]');
    await whiteboardFilterBtn.click();
    await page.waitForTimeout(300);
    await expect(assetsGrid.locator('.cora-brand-asset-card')).toHaveCount(2);

    // Filter: Decks (Pitch / Gamma) -> 1 card
    const deckFilterBtn = filterBar.locator('button[data-filter="presentation"]');
    await deckFilterBtn.click();
    await page.waitForTimeout(300);
    await expect(assetsGrid.locator('.cora-brand-asset-card')).toHaveCount(1);
    await expect(assetsGrid.locator('h4:has-text("Studio Brand Strategy & Keynote 2026")')).toBeVisible();

    // Filter: Guidelines (Notion / Wiki) -> 1 card
    const guidelinesFilterBtn = filterBar.locator('button[data-filter="guidelines"]');
    await guidelinesFilterBtn.click();
    await page.waitForTimeout(300);
    await expect(assetsGrid.locator('.cora-brand-asset-card')).toHaveCount(1);
    await expect(assetsGrid.locator('h4:has-text("Brand Guidelines & Tone of Voice SOP")')).toBeVisible();

    // Filter: All -> restores 4 cards
    const allFilterBtn = filterBar.locator('button[data-filter="all"]');
    await allFilterBtn.click();
    await page.waitForTimeout(300);
    await expect(assetsGrid.locator('.cora-brand-asset-card')).toHaveCount(4);

    // 7. Test Add External Asset Docked Drawer (#drawer-add-brand-asset)
    const addAssetBtn = page.locator('#btn-add-brand-asset');
    await addAssetBtn.click();
    await page.waitForTimeout(400);

    const addDrawer = page.locator('#drawer-add-brand-asset');
    await expect(addDrawer).toHaveClass(/open/);
    await expect(addDrawer).not.toHaveClass(/collapsed/);

    // Input URL (Miro share link)
    const urlInput = page.locator('#cora-asset-input-url');
    await urlInput.fill('https://miro.com/app/board/uXjVO889900_sprint_roadmap/');

    // Check auto-detection updates badge to "Miro Board"
    const detectedBadge = page.locator('#cora-add-asset-detected-tool-badge');
    await expect(detectedBadge).toHaveText('Miro Board');

    // Input Title
    const titleInput = page.locator('#cora-asset-input-title');
    await titleInput.fill('Design Sprint & Q4 Roadmap');

    // Input Description
    const descInput = page.locator('#cora-asset-input-desc');
    await descInput.fill('Bi-weekly sprint planning and roadmap milestones.');

    // Capture screenshot of the Add Asset Docked Drawer
    const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/e7db172d-afc3-4021-b0cc-a3073c916f94';
    await page.screenshot({ path: path.join(artifactDir, 'cora_canvas_add_brand_asset_drawer.png'), fullPage: false });

    // Save Asset
    const saveAssetBtn = page.locator('#btn-save-new-brand-asset');
    await saveAssetBtn.click();
    await page.waitForTimeout(500);

    // Assert drawer closed
    await expect(addDrawer).toHaveClass(/collapsed/);

    // Assert count updated to 5 Assets
    await expect(countBadge).toContainText('5 Assets');

    // Assert newly created asset is rendered in the grid
    await expect(assetsGrid.locator('h4:has-text("Design Sprint & Q4 Roadmap")')).toBeVisible();

    // 8. Test Interactive Viewer Docked Drawer (#drawer-view-brand-asset)
    const firstPreviewBtn = assetsGrid.locator('.cora-brand-asset-card').first().locator('button:has-text("Preview")');
    await firstPreviewBtn.click();
    await page.waitForTimeout(400);

    const viewerDrawer = page.locator('#drawer-view-brand-asset');
    await expect(viewerDrawer).toHaveClass(/open/);
    await expect(viewerDrawer).not.toHaveClass(/collapsed/);

    // Assert viewer header title matches
    const viewerTitle = page.locator('#view-brand-asset-title');
    await expect(viewerTitle).toHaveText('Design Sprint & Q4 Roadmap');

    // Assert live embed iframe exists and has source
    const viewerIframe = page.locator('#cora-viewer-iframe');
    await expect(viewerIframe).toBeVisible();
    const iframeSrc = await viewerIframe.getAttribute('src');
    expect(iframeSrc).toContain('miro.com');

    // Take screenshot of the open interactive viewer
    await page.screenshot({ path: path.join(artifactDir, 'cora_canvas_brand_asset_viewer_open.png'), fullPage: false });

    // 9. Close viewer via Escape key (Rule 15 SOP)
    await page.keyboard.press('Escape');
    await page.waitForTimeout(400);
    await expect(viewerDrawer).toHaveClass(/collapsed/);

    // 10. Capture full view screenshot of Brand tab with Connected Tools & Library
    await page.screenshot({ path: path.join(artifactDir, 'cora_canvas_brand_hub_and_external_assets.png'), fullPage: false });
    await page.screenshot({ path: path.join(artifactDir, 'cora_canvas_brand_official_logos_verified.png'), fullPage: true });
  });

});
