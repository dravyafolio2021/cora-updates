import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('Article editor responsiveness and functional metrics validation', async ({ page }) => {
  page.on('console', msg => {
    console.log(`[BROWSER CONSOLE] [${msg.type()}] ${msg.text()}`);
  });

  page.on('pageerror', error => {
    console.log(`[BROWSER UNCAUGHT EXCEPTION] ${error.stack || error.message}`);
  });

  await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
  await page.goto('/super/blogs');
  await page.waitForLoadState('networkidle');

  // Open editor for the first article
  await page.waitForSelector('.ct-row', { timeout: 15000 });
  const postId = await page.evaluate(() => {
    const row = document.querySelector('.ct-row');
    return row ? row.getAttribute('data-post-id') : null;
  });
  console.log('Opening editor for Post ID:', postId);

  await page.evaluate((id) => {
    window.coraEditArticle(parseInt(id));
  }, postId);

  // Wait for the editor to render
  const editorSelector = '#cora-quill-editor';
  await page.waitForSelector(editorSelector, { state: 'visible', timeout: 15000 });
  console.log('Article editor successfully initialized and visible');

  // Allow a short duration for AJAX content rendering to parse and populate metrics
  await page.waitForTimeout(2000);

  // Inject a heading so that headings count > 0 and outline hierarchy gets populated
  await page.evaluate(() => {
    if (window.coraQuillListingCoordinator) {
      window.coraQuillListingCoordinator.clipboard.dangerouslyPasteHTML(0, '<h2>Insight Section</h2>');
    }
  });
  await page.waitForTimeout(500);

  // Test 1: Desktop responsiveness width check
  console.log('--- TEST 1: Desktop Editor Width Check ---');
  await page.setViewportSize({ width: 1280, height: 800 });
  await page.waitForTimeout(500);

  let overflowMetrics = await page.evaluate(() => {
    const mainEl = document.querySelector('#cora-full-page-editor main');
    if (!mainEl) return null;
    return {
      scrollWidth: mainEl.scrollWidth,
      clientWidth: mainEl.clientWidth,
      hasHorizontalOverflow: mainEl.scrollWidth > mainEl.clientWidth
    };
  });
  console.log('Desktop viewport metrics:', JSON.stringify(overflowMetrics));
  expect(overflowMetrics?.hasHorizontalOverflow).toBe(false);

  // Test 2: Sidebar dynamic metrics and outline verification
  console.log('\n--- TEST 2: Sidebar Dynamic Metrics & Outline Verification ---');
  
  // Wait for word count to update to be non-zero
  const editorTextContent = await page.locator('#cora-quill-editor').innerText();
  console.log('DEBUG: #cora-quill-editor inner text:', JSON.stringify(editorTextContent));
  
  const debugQuillVal = await page.evaluate(() => {
    return window.coraQuillListingCoordinator ? {
      text: window.coraQuillListingCoordinator.getText(),
      html: window.coraQuillListingCoordinator.root.innerHTML,
      wordCountText: document.getElementById('left-stat-words')?.textContent
    } : 'NO_QUILL';
  });
  console.log('DEBUG: Quill internal state:', JSON.stringify(debugQuillVal));

  await expect(page.locator('#left-stat-words')).not.toHaveText('0', { timeout: 10000 });
  const wordsText = await page.locator('#left-stat-words').textContent();
  const wordsCount = parseInt(wordsText || '0', 10);
  console.log('Document word count extracted from sidebar:', wordsCount);
  expect(wordsCount).toBeGreaterThan(0);

  // Wait for headings count to update to be non-zero
  await expect(page.locator('#left-stat-headings')).not.toHaveText('0', { timeout: 10000 });
  const headingsText = await page.locator('#left-stat-headings').textContent();
  const headingsCount = parseInt(headingsText || '0', 10);
  console.log('Document headings count extracted from sidebar:', headingsCount);
  expect(headingsCount).toBeGreaterThan(0);

  // Wait for the outline panel to be populated with heading links
  const outlineSelector = '#cora-outline-hierarchy-list a';
  await page.waitForSelector(outlineSelector, { state: 'attached', timeout: 10000 });
  const outlineLinksCount = await page.locator(outlineSelector).count();
  console.log('Outline hierarchy links count:', outlineLinksCount);
  expect(outlineLinksCount).toBeGreaterThan(0);

  // Test 3: Mobile responsiveness width check (resize viewport to 375px)
  console.log('\n--- TEST 3: Mobile Editor Width Check ---');
  await page.setViewportSize({ width: 375, height: 667 });
  await page.waitForTimeout(500);

  // Close the inspector sheet on mobile to reveal the canvas fully
  const closeButton = page.locator('#cora-article-inspector button[aria-label="Close Inspector Sheet"]');
  if (await closeButton.isVisible()) {
    await closeButton.click();
    console.log('Mobile inspector sheet successfully closed');
    await page.waitForTimeout(300);
  }

  // Take a screenshot of the responsive mobile view
  await page.screenshot({ path: 'test-results/editor-responsive-mobile.png' });

  overflowMetrics = await page.evaluate(() => {
    const mainEl = document.querySelector('#cora-full-page-editor main');
    if (!mainEl) return null;
    return {
      scrollWidth: mainEl.scrollWidth,
      clientWidth: mainEl.clientWidth,
      hasHorizontalOverflow: mainEl.scrollWidth > mainEl.clientWidth
    };
  });
  console.log('Mobile viewport metrics:', JSON.stringify(overflowMetrics));
  expect(overflowMetrics?.hasHorizontalOverflow).toBe(false);

  console.log('Editor responsiveness & metrics E2E checks passed perfectly!');
});
