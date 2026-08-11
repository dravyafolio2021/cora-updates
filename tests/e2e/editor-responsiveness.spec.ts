import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('Article editor responsiveness and overflow prevention validation', async ({ page }) => {
  const consoleLogs: string[] = [];
  page.on('console', msg => {
    consoleLogs.push(`[${msg.type()}] ${msg.text()}`);
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

  // Test 2: Mobile responsiveness width check (resize viewport to 375px)
  console.log('\n--- TEST 2: Mobile Editor Width Check ---');
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

  console.log('Editor responsiveness E2E checks passed perfectly!');
});
