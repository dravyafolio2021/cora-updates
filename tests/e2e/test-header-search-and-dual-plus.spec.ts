import { test, expect } from '@playwright/test';
import { login } from './helpers';
import * as path from 'path';

test.describe('Header Search Dead-Center & Single Plus Icon Verification', () => {

  const viewports = [
    { name: '1920x1080_fhd', width: 1920, height: 1080 },
    { name: '1440x900_macbook', width: 1440, height: 900 },
    { name: '1366x768_laptop', width: 1366, height: 768 },
    { name: '1280x800_desktop', width: 1280, height: 800 },
    { name: '1024x768_compact', width: 1024, height: 768 }
  ];

  for (const vp of viewports) {
    test(`Verify search centering at ${vp.name}`, async ({ page }) => {
      const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/e7db172d-afc3-4021-b0cc-a3073c916f94';

      await page.setViewportSize({ width: vp.width, height: vp.height });
      await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

      await page.goto('/workspace/canvas?tab=brand');
      await page.waitForLoadState('domcontentloaded');
      await page.waitForTimeout(600);

      const topbar = page.locator('#cora-global-topbar');
      await expect(topbar).toBeVisible();

      const searchTrigger = page.locator('#cora-global-topbar .cora-topbar-desktop [onclick*="coraOpenCommandPalette"]');
      await expect(searchTrigger).toBeVisible();

      const topbarBox = await topbar.boundingBox();
      const searchBox = await searchTrigger.boundingBox();

      expect(topbarBox).not.toBeNull();
      expect(searchBox).not.toBeNull();

      if (topbarBox && searchBox) {
        const topbarMidX = topbarBox.x + topbarBox.width / 2;
        const searchMidX = searchBox.x + searchBox.width / 2;
        const offset = Math.abs(topbarMidX - searchMidX);

        console.log(`[${vp.name}] Topbar Mid: ${topbarMidX}, Search Mid: ${searchMidX}, Offset: ${offset.toFixed(2)}px`);
        expect(offset).toBeLessThanOrEqual(1.5); // Sub-pixel precision dead center
      }

      // Check "+ Link Brand Asset" button
      const addBrandAssetBtn = page.locator('#btn-add-brand-asset');
      await expect(addBrandAssetBtn).toBeVisible();

      const btnDetails = await addBrandAssetBtn.evaluate((el) => {
        const span = el.querySelector('span');
        return {
          fullText: el.innerText.trim(),
          spanText: span ? span.innerText.trim() : '',
          svgCount: el.querySelectorAll('svg').length
        };
      });

      console.log(`[${vp.name}] Button text: "${btnDetails.spanText}", SVGs: ${btnDetails.svgCount}`);
      expect(btnDetails.spanText).toBe('Link Brand Asset');
      expect(btnDetails.spanText.startsWith('+')).toBe(false);
      expect(btnDetails.svgCount).toBe(1);

      // Capture screenshot
      await page.screenshot({
        path: path.join(artifactDir, `header_search_centered_${vp.name}.png`),
        clip: { x: 0, y: 0, width: vp.width, height: 180 }
      });
    });
  }
});
