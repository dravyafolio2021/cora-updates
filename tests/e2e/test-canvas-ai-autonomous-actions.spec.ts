import { test, expect } from '@playwright/test';
import { login } from './helpers';
import { execSync } from 'child_process';

test('Verify Autonomous AI executes bulk draft theme deletion directly from chat', async ({ page }) => {
  const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/e7db172d-afc3-4021-b0cc-a3073c916f94';
  const phpBin = '/Applications/Local.app/Contents/Resources/extraResources/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php';

  // 1. Seed 3 test draft themes with pages in database
  execSync(`${phpBin} -r "
    require 'app/public/wp-load.php';
    global \\$wpdb;
    \\$agency_id = 1;
    // Remove any leftover non-live themes
    \\$wpdb->query('DELETE FROM ' . \\$wpdb->prefix . 'cora_canvas_themes WHERE status != \\'live\\';');
    \\$wpdb->query('DELETE FROM ' . \\$wpdb->prefix . 'cora_canvas_pages WHERE theme_id NOT IN (SELECT id FROM ' . \\$wpdb->prefix . 'cora_canvas_themes WHERE status = \\'live\\');');

    // Insert 3 new draft themes
    \\$names = array('Design System Pitch Deck Theme', 'E-Commerce Showcase Draft', 'Creative Portfolio Beta');
    foreach (\\$names as \\$n) {
      \\$wpdb->insert(\\$wpdb->prefix . 'cora_canvas_themes', array('name' => \\$n, 'status' => 'draft', 'agency_id' => \\$agency_id));
      \\$th_id = \\$wpdb->insert_id;
      \\$wpdb->insert(\\$wpdb->prefix . 'cora_canvas_pages', array('theme_id' => \\$th_id, 'title' => \\$n . ' Home', 'slug' => sanitize_title(\\$n . '-home'), 'status' => 'draft'));
      \\$wpdb->insert(\\$wpdb->prefix . 'cora_canvas_pages', array('theme_id' => \\$th_id, 'title' => \\$n . ' About', 'slug' => sanitize_title(\\$n . '-about'), 'status' => 'draft'));
    }
  "`);

  // Verify seeding
  const seedCheck = execSync(`${phpBin} -r "
    require 'app/public/wp-load.php';
    global \\$wpdb;
    echo \\$wpdb->get_var('SELECT COUNT(*) FROM ' . \\$wpdb->prefix . 'cora_canvas_themes WHERE status != \\'live\\';');
  "`).toString().trim();
  expect(seedCheck).toBe('3');

  await page.setViewportSize({ width: 1366, height: 850 });
  await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

  // 2. Navigate to Canvas Builder
  await page.goto('/workspace/canvas');
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(1000);

  // 3. Verify Canvas displays the 3 draft themes
  const badge = page.locator('#canvas-draft-themes-badge');
  await expect(badge).toBeVisible();
  const badgeText = await badge.innerText();
  console.log('Initial draft themes badge:', badgeText);
  expect(badgeText).toContain('3 / 20 Drafts');

  const draftCardsBefore = page.locator('#canvas-draft-themes-list [data-draft-theme-id]');
  await expect(draftCardsBefore).toHaveCount(3);

  // Capture screenshot of Canvas before deletion
  await page.screenshot({ path: `${artifactDir}/canvas_before_ai_draft_cleanup.png` });

  // 4. Open AI Copilot Sidebar via top button or API
  await page.evaluate(() => {
    if (typeof window.coraOpenAIPanel === 'function') {
      window.coraOpenAIPanel('chat');
    }
  });
  await page.waitForTimeout(600);

  const chatInput = page.locator('#cora-sidebar-chat-input');
  await expect(chatInput).toBeVisible();

  // 5. Type user inquiry with exact typo pattern: "can yo udelete all the draft themes"
  await chatInput.fill('can yo udelete all the draft themes');
  await chatInput.press('Enter');

  // 6. Wait for AI response in chat
  const chatPane = page.locator('#cora-sidebar-chat');
  await expect(chatPane).toBeVisible();

  // Wait for typing skeleton to be removed and real reply to appear
  await expect(chatPane.locator('[id^="typing-"]')).toHaveCount(0, { timeout: 15000 });
  const aiBubble = chatPane.locator('.chat-bubble.ai:not([id^="typing-"])').last();
  await expect(aiBubble).toBeVisible({ timeout: 10000 });

  const aiReplyText = await aiBubble.innerText();
  console.log('AI Reply Text:', aiReplyText);

  // Verify: Autonomous AI did NOT refuse or ask user to run SQL scripts in database terminal!
  expect(aiReplyText).not.toContain('I cannot execute bulk deletions');
  expect(aiReplyText).not.toContain('database terminal');
  expect(aiReplyText).not.toContain('cleanup script for you to run');

  // Verify: AI confirmed execution of deletion
  expect(aiReplyText).toContain('3 draft themes');
  expect(aiReplyText).toContain('Creative Agency & Video Studio');

  // 7. Verify UI Synchronization: Draft cards are completely removed from DOM
  await page.waitForTimeout(600);
  const draftCardsAfter = page.locator('#canvas-draft-themes-list [data-draft-theme-id]');
  await expect(draftCardsAfter).toHaveCount(0);

  // Verify badge updated to 0 / 20 Drafts
  const badgeAfter = await badge.innerText();
  console.log('Draft badge after AI purge:', badgeAfter);
  expect(badgeAfter).toContain('0 / 20 Drafts');

  // Verify empty state notice is displayed
  const emptyNotice = page.locator('#canvas-no-drafts-notice');
  await expect(emptyNotice).toBeVisible();

  // Capture final screenshot showing AI Copilot execution and decluttered Canvas
  await page.screenshot({ path: `${artifactDir}/canvas_after_ai_draft_cleanup.png` });

  // 8. Verify Database State: 0 draft themes remaining, live theme 100% preserved
  const dbDraftsAfter = execSync(`${phpBin} -r "
    require 'app/public/wp-load.php';
    global \\$wpdb;
    echo \\$wpdb->get_var('SELECT COUNT(*) FROM ' . \\$wpdb->prefix . 'cora_canvas_themes WHERE status != \\'live\\';');
  "`).toString().trim();
  expect(dbDraftsAfter).toBe('0');

  const dbLivePages = execSync(`${phpBin} -r "
    require 'app/public/wp-load.php';
    global \\$wpdb;
    echo \\$wpdb->get_var('SELECT COUNT(*) FROM ' . \\$wpdb->prefix . 'cora_canvas_pages WHERE theme_id = 870;');
  "`).toString().trim();
  expect(parseInt(dbLivePages, 10)).toBeGreaterThanOrEqual(40);
  console.log('Verified: Database has 0 draft themes and live theme 870 has', dbLivePages, 'pages intact.');
});
