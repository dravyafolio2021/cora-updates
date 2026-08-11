import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('comprehensive editor functionality test', async ({ page }) => {
  const consoleLogs: string[] = [];
  const ajaxRequests: { url: string; method: string; status?: number; response?: string }[] = [];

  page.on('console', msg => {
    consoleLogs.push(`[${msg.type()}] ${msg.text()}`);
  });

  // Capture AJAX requests to cora endpoints
  page.on('response', async (response) => {
    const url = response.url();
    if (url.includes('admin-ajax.php') || url.includes('cora')) {
      const entry: any = { url, method: response.request().method(), status: response.status() };
      try {
        const body = await response.text();
        entry.response = body.substring(0, 500); // First 500 chars
      } catch(e) {}
      ajaxRequests.push(entry);
    }
  });

  try {
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
    await page.goto('/super/blogs');
    await page.waitForLoadState('networkidle');

    // Open editor for the first article
    await page.waitForSelector('.ct-row', { timeout: 15000 });
    const postId = await page.evaluate(() => {
      const row = document.querySelector('.ct-row');
      return row ? row.getAttribute('data-post-id') : null;
    });
    console.log('Post ID:', postId);

    await page.evaluate((id) => {
      window.coraEditArticle(parseInt(id));
    }, postId);

    await page.waitForSelector('#cora-quill-editor', { state: 'visible', timeout: 15000 });
    console.log('Editor is visible');

    // === TEST 1: Slash Command === 
    console.log('\n=== TEST 1: Slash Command / ===');
    const qlEditor = page.locator('#cora-quill-editor .ql-editor');
    await qlEditor.click();
    await page.waitForTimeout(500);
    
    // Clear any existing content, then type /
    await page.keyboard.press('Control+A');
    await page.keyboard.press('Backspace');
    await page.waitForTimeout(300);
    await page.keyboard.type('/');
    await page.waitForTimeout(1000);
    
    const slashMenuState = await page.evaluate(() => {
      const el = document.getElementById('cora-editor-slash-menu');
      if (!el) return 'NOT_FOUND';
      const style = window.getComputedStyle(el);
      return {
        hasHiddenClass: el.classList.contains('hidden'),
        display: style.display,
        top: el.style.top,
        left: el.style.left,
        parentId: el.parentElement?.id || el.parentElement?.className
      };
    });
    console.log('Slash menu state:', JSON.stringify(slashMenuState));

    // Check what the Quill text is after typing /
    const quillText = await page.evaluate(() => {
      return window.coraQuillListingCoordinator ? window.coraQuillListingCoordinator.getText() : 'NO_QUILL';
    });
    console.log('Quill text after typing /:', JSON.stringify(quillText));

    // === TEST 2: AI Writing Assistant ===
    console.log('\n=== TEST 2: AI Writing Assistant ===');
    
    // Check if the AI Writing Assistant input exists
    const aiInput = page.locator('input[placeholder*="Ask AI"], textarea[placeholder*="Ask AI"]');
    const aiInputVisible = await aiInput.isVisible().catch(() => false);
    console.log('AI input visible:', aiInputVisible);

    // Check if "Write introduction" button exists
    const writeIntroBtn = page.locator('button:has-text("Write introduction")');
    const writeIntroBtnVisible = await writeIntroBtn.isVisible().catch(() => false);
    console.log('Write introduction button visible:', writeIntroBtnVisible);

    if (writeIntroBtnVisible) {
      // Clear editor first
      await page.evaluate(() => {
        if (window.coraQuillListingCoordinator) {
          window.coraQuillListingCoordinator.root.innerHTML = '';
        }
      });
      
      await writeIntroBtn.click();
      console.log('Clicked Write introduction');
      
      // Wait for AJAX response
      await page.waitForTimeout(5000);
      
      // Check if content was inserted
      const editorContent = await page.evaluate(() => {
        return window.coraQuillListingCoordinator ? window.coraQuillListingCoordinator.root.innerHTML : '';
      });
      console.log('Editor content after Write intro:', editorContent.substring(0, 200));
    }

    // === TEST 3: Check AJAX endpoint ===
    console.log('\n=== TEST 3: AJAX Requests ===');
    ajaxRequests.forEach(r => {
      console.log(`[${r.method}] ${r.url} -> ${r.status}`);
      if (r.response) console.log('  Response:', r.response.substring(0, 200));
    });

    // Take a screenshot of the final state
    await page.screenshot({ path: 'test-results/editor-final-state.png', fullPage: false });

  } finally {
    console.log('\n=== CONSOLE LOGS ===');
    consoleLogs.forEach(log => console.log(log));
  }
});
