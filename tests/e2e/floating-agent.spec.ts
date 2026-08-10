import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Cora Content Suite Floating AI Agent E2E Tests', () => {

  test.beforeEach(async ({ page }) => {
    page.on('console', msg => {
      if (msg.type() === 'error') {
        console.log(`PAGE CONSOLE ERROR: "${msg.text()}"`);
      }
    });
    page.on('pageerror', err => {
      console.log(`PAGE UNHANDLED EXCEPTION: "${err.message}"`);
    });
  });

  test('Should render collapsed pill, expand on click, support keyboard shortcuts, run suggestions, and execute agent actions', async ({ page }) => {
    await login(page);

    // 1. Navigate to Content Suite
    await page.goto('/workspace/blogs?industry=photography_studio');
    await page.waitForSelector('.cora-page-title');
    await expect(page.locator('.cora-page-title')).toContainText('Content Suite');

    // 2. Verify Collapsed Pill is visible
    const pill = page.locator('#cora-agent-pill');
    await expect(pill).toBeVisible();
    await expect(pill).toContainText('Search articles, keywords, opportunities...');

    // 3. Click to Expand Agent Board
    await pill.click();
    const board = page.locator('#cora-agent-board');
    await expect(board).toBeVisible();

    // Verify Quick Actions and Usage & Quota sections are present
    await expect(board.locator('.cora-agent-section-title', { hasText: 'Quick Actions' })).toBeVisible();
    await expect(board.locator('.cora-agent-section-title', { hasText: 'Usage & Quota' })).toBeVisible();

    // Verify input is focused
    const input = page.locator('#cora-agent-input-field');
    await expect(input).toBeFocused();

    // Verify initial credits usage matches 0
    const sessionCredits = page.locator('#cora-agent-session-credits');
    await expect(sessionCredits).toContainText('0');

    // 4. Press Escape to collapse
    await page.keyboard.press('Escape');
    await expect(board).toBeHidden();
    await expect(pill).toBeVisible();

    // 5. Press Ctrl+K / Cmd+K shortcut to expand
    // On Mac, meta+k is typical. On other OSes, control+k. We can trigger both.
    await page.keyboard.press('Control+k');
    await expect(board).toBeVisible();
    await expect(input).toBeFocused();

    // 6. Test submission of custom query
    await input.fill('Run organic rank sync audit');
    await page.keyboard.press('Enter');

    // Verify view mode transitioned to conversation
    const convoView = page.locator('#cora-agent-conversation');
    await expect(convoView).toBeVisible();
    await expect(page.locator('#cora-agent-dashboard')).toBeHidden();

    // Verify loading checklist step changes
    const chatLog = page.locator('#cora-agent-chat-messages');
    await expect(chatLog).toContainText('Reading library records...');
    
    // Wait for the simulated agent processing checklist to finish (around 3 seconds)
    await page.waitForTimeout(3500);

    // Verify AI response printed
    await expect(chatLog).toContainText("Cora Assistant");
    
    // Verify dynamic credits increments in display
    await expect(sessionCredits).toContainText('10');

    // Verify reset session restores dashboard state
    await page.click('button:has-text("Reset Session")');
    await expect(page.locator('#cora-agent-dashboard')).toBeVisible();
    await expect(convoView).toBeHidden();

    // 7. Verify quick actions (e.g. New Article)
    const newArtBtn = board.locator('.cora-agent-action-btn', { hasText: 'New Article' });
    await expect(newArtBtn).toBeVisible();
    await newArtBtn.click();

    // Confirm it opens the full page editor
    const editor = page.locator('#cora-full-page-editor');
    await page.waitForSelector('#cora-full-page-editor:not(.hidden)', { state: 'visible' });
    await expect(editor).toBeVisible();

    // Close editor by clicking Back
    await page.click('#cora-full-page-editor button:has-text("Back")');
    await page.waitForSelector('#cora-full-page-editor.hidden', { state: 'hidden' });
  });

});
