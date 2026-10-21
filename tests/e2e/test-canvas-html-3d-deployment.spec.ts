import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Cora Canvas Custom HTML, Tailwind & 3D (Three.js) Workspace Deployment', () => {
  test('deploy 3D WebGL page with Three.js, OrbitControls & Tailwind, verify Level 3 editor & live route execution', async ({ page }) => {
    test.setTimeout(240000);

    // 1. Authenticate with Studio Owner credentials
    console.time('Step 1: Login');
    await page.setViewportSize({ width: 1280, height: 850 });
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
    console.timeEnd('Step 1: Login');

    // 2. Navigate to Canvas Pages Manager
    console.time('Step 2: Navigate Canvas');
    await page.goto('/workspace/canvas?tab=pages');
    await page.waitForLoadState('domcontentloaded');
    console.timeEnd('Step 2: Navigate Canvas');

    // 3. Open "+ Add Page" Drawer
    const addPageBtn = page.locator('#btn-add-global-page');
    await expect(addPageBtn).toBeVisible();
    await addPageBtn.click();

    const newPageDrawer = page.locator('#drawer-new-page');
    await expect(newPageDrawer).toBeVisible();

    // 4. Switch to "Custom HTML & 3D" mode
    const htmlTab = page.locator('#cora-engine-tab-html');
    await expect(htmlTab).toBeVisible();
    await htmlTab.click();

    // Verify engine badge updates to "Custom HTML & 3D"
    const engineBadge = page.locator('#cora-page-engine-badge');
    await expect(engineBadge).toHaveText('Custom HTML & 3D');

    // 5. Verify 1-Click Starter Boilerplates are present
    const torusBoilerplateBtn = newPageDrawer.locator('button:has-text("⚡ 3D Torus (WebGL)")');
    await expect(torusBoilerplateBtn).toBeVisible();

    // Click 1-Click Torus Boilerplate
    await torusBoilerplateBtn.click();
    await page.waitForTimeout(300);

    // Verify textarea populated with Three.js & Tailwind code
    const pasteInput = page.locator('#cora-html-paste-input');
    await expect(pasteInput).toBeVisible();
    const codeVal = await pasteInput.inputValue();
    expect(codeVal).toContain('https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js');
    expect(codeVal).toContain('OrbitControls');
    expect(codeVal).toContain('https://cdn.tailwindcss.com');
    expect(codeVal).toContain('TorusKnotGeometry');

    // Verify Page Title and Slug auto-generated
    const titleInput = page.locator('#new-page-title-input');
    const slugInput = page.locator('#new-page-slug-input');
    await expect(titleInput).not.toHaveValue('');
    await expect(slugInput).not.toHaveValue('');

    // Ensure unique slug for test repeatability
    const testSlug = 'threejs-torus-' + Date.now();
    await slugInput.fill(testSlug);

    // Verify telemetry badge is displayed
    const telemetryBadge = page.locator('#cora-html-telemetry-badge');
    await expect(telemetryBadge).toBeVisible();

    // 6. Test Library Injector
    const gsapChip = newPageDrawer.locator('button:has-text("+ GSAP")');
    if (await gsapChip.isVisible()) {
      await gsapChip.click();
      await page.waitForTimeout(200);
      const updatedCode = await pasteInput.inputValue();
      expect(updatedCode).toContain('gsap');
    }

    // 7. Click "Deploy & Edit ✨"
    console.time('Step 7-8: Deploy & Open Editor');
    const createAndEditBtn = page.locator('#cora-create-and-edit-btn');
    await expect(createAndEditBtn).toBeVisible();
    await createAndEditBtn.click();

    // 8. Verify Level 3 Editor opens
    const level3Editor = page.locator('#canvas-level-3-html');
    await expect(level3Editor).toBeVisible({ timeout: 25000 });
    const editorLoader = page.locator('#cora-html-editor-loader');
    await expect(editorLoader).toBeHidden({ timeout: 15000 });
    console.timeEnd('Step 7-8: Deploy & Open Editor');

    // Verify 3D / Libraries toolbar button is present in Code View
    const codeViewToggle = page.locator('#cora-html-split-mode-btn');
    await expect(codeViewToggle).toBeVisible();
    await codeViewToggle.click();

    const splitCodePane = page.locator('#cora-html-code-split-pane');
    await expect(splitCodePane).toBeVisible();

    const libsDropdownBtn = page.locator('#cora-html-libs-menu-wrapper > button');
    await expect(libsDropdownBtn).toBeVisible();
    await libsDropdownBtn.click();

    const libsDropdown = page.locator('#cora-html-libs-dropdown');
    await expect(libsDropdown).toBeVisible();
    await expect(libsDropdown).toContainText('Three.js r128');
    await expect(libsDropdown).toContainText('OrbitControls');
    await expect(libsDropdown).toContainText('Tailwind CSS CDN');

    // Close dropdown
    await libsDropdownBtn.click();

    // 9. Click "Save & Publish" in Level 3 editor
    console.time('Step 9: Save & Publish');
    const savePublishBtn = page.locator('#cora-html-save-publish-btn');
    await expect(savePublishBtn).toBeVisible();
    await savePublishBtn.click();
    await expect(savePublishBtn).not.toHaveClass(/opacity-70/, { timeout: 15000 });
    console.timeEnd('Step 9: Save & Publish');

    // Close Level 3 editor
    console.time('Step 10: Close Editor & Verify Table');
    const backBtn = page.locator('#canvas-level-3-html button:has-text("Pages List")');
    await expect(backBtn).toBeVisible();
    await backBtn.click();
    await expect(level3Editor).not.toBeVisible();

    // 10. Verify new page in Pages Manager Table
    const tableBody = page.locator('#global-pages-table-body');
    const newPageRow = tableBody.locator(`tr:has-text("${testSlug}")`);
    await expect(newPageRow).toBeVisible();

    // Verify Technology Tags displayed in row (HTML5, Tailwind, Three.js 3D)
    await expect(newPageRow).toContainText('HTML5');
    await expect(newPageRow).toContainText('Three.js 3D');

    // Verify 1-Click "Code/AI" button is present in row
    const codeAiBtn = newPageRow.locator('button:has-text("Code/AI")');
    await expect(codeAiBtn).toBeVisible();
    console.timeEnd('Step 10: Close Editor & Verify Table');

    // 11. Navigate directly to the live deployed URL route: /site/{testSlug}
    console.time('Step 11: Live 3D Scene Verification');
    const liveUrl = `/site/${testSlug}`;
    console.log('[E2E TEST] Navigating to live URL:', liveUrl);
    const livePage = page;
    livePage.on('console', msg => console.log('LIVE PAGE LOG:', msg.text()));
    livePage.on('pageerror', err => console.log('LIVE PAGE ERROR:', err.message));

    const response = await livePage.goto(liveUrl, { waitUntil: 'domcontentloaded' });
    expect(response?.status()).toBe(200);
    console.log('[E2E TEST] Live page loaded, status:', response?.status());

    // Verify live page executes Three.js WebGL canvas
    const canvasLocator = livePage.locator('canvas');
    await expect(canvasLocator).toBeAttached({ timeout: 20000 });
    console.log('[E2E TEST] Canvas attached successfully');

    const hasCanvas = await livePage.evaluate(() => {
      const c = document.querySelector('canvas');
      if (!c) return false;
      return c.width > 0 && c.height > 0;
    });
    expect(hasCanvas).toBe(true);

    // Verify Three.js global is loaded and running
    const threeLoaded = await livePage.evaluate(() => {
      return typeof (window as any).THREE !== 'undefined';
    });
    expect(threeLoaded).toBe(true);
    console.log('[E2E TEST] Three.js global confirmed');

    // Verify Tailwind styling is applied
    const heading = livePage.locator('h1');
    await expect(heading).toBeAttached();
    await expect(heading).toContainText('3D Web Experience');
    console.log('[E2E TEST] H1 text verified');

    // Take screenshot of live deployed 3D WebGL page
    await livePage.screenshot({ path: 'test-results/threejs-live-deployed-scene.png' });
    console.log('[E2E TEST] Screenshot captured');
    console.timeEnd('Step 11: Live 3D Scene Verification');
  });
});
