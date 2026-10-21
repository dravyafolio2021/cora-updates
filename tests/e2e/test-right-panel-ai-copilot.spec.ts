import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('Verify AI Copilot Integrated into Right Side Panel Without View-Blocking Popups', async ({ page }) => {
  const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/58e87118-a119-4a20-9906-b30972fba7f6';

  await page.setViewportSize({ width: 1366, height: 850 });
  await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

  // 1. Navigate to Content Suite
  await page.goto('/workspace/blogs?ct=ct-library');
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(1000);

  // 2. Open Full-Page Article Editor
  await page.evaluate(() => {
    if (typeof window.coraToggleContentDrawer === 'function') {
      window.coraToggleContentDrawer(true);
    }
  });
  await page.waitForTimeout(800);

  const fullEditor = page.locator('#cora-full-page-editor');
  await expect(fullEditor).toBeVisible();

  // 3. Verify Inspector Navigation Tabs include Copilot, SEO, AI Visibility, Details
  const copilotTabBtn = page.locator('#btn-sidebar-copilot');
  const seoTabBtn = page.locator('#btn-sidebar-seo');
  const geoTabBtn = page.locator('#btn-sidebar-geo');
  const detailsTabBtn = page.locator('#btn-sidebar-meta');

  await expect(copilotTabBtn).toBeVisible();
  await expect(seoTabBtn).toBeVisible();
  await expect(geoTabBtn).toBeVisible();
  await expect(detailsTabBtn).toBeVisible();

  // 4. Click the Canvas "✦ AI Copilot (⌘K)" Button
  const aiTriggerBtn = page.locator('#cora-ai-palette-btn');
  await expect(aiTriggerBtn).toBeVisible();
  await aiTriggerBtn.click();
  await page.waitForTimeout(600);

  // Verify: NO popup modal is displayed over the editor canvas!
  const legacyPopup = page.locator('#cora-ai-writing-palette');
  await expect(legacyPopup).toBeHidden();

  // Verify: Right side inspector switched to Copilot tab
  await expect(copilotTabBtn).toHaveClass(/tab-active/);
  const copilotChatPanel = page.locator('#panel-inspector-copilot-chat');
  await expect(copilotChatPanel).toBeVisible();

  // Verify: Prompt input is visible in the right side panel
  const promptTextarea = page.locator('#cora-copilot-chat-input');
  await expect(promptTextarea).toBeVisible();
  await expect(page.locator('#cora-copilot-context-badge')).toBeVisible();

  // Verify Quick Action buttons in panel (including Generate Article Outline)
  await expect(copilotChatPanel.locator('button:has-text("Generate Article Outline")')).toBeVisible();
  await expect(copilotChatPanel.locator('button:has-text("Write Intro")')).toBeVisible();
  await expect(copilotChatPanel.locator('button:has-text("Continue")')).toBeVisible();
  await expect(copilotChatPanel.locator('button:has-text("Expand Section")')).toBeVisible();
  await expect(copilotChatPanel.locator('button:has-text("Improve Clarity")')).toBeVisible();

  // Capture screenshot: AI Copilot live in right sidebar with zero harsh outlines
  await page.screenshot({ path: `${artifactDir}/copilot_right_panel_active.png` });
  const panelLocator = page.locator('#panel-inspector-copilot-chat');
  await panelLocator.screenshot({ path: `${artifactDir}/copilot_right_panel_no_outlines.png` });
  console.log('Captured copilot_right_panel_active.png and copilot_right_panel_no_outlines.png');

  // 5. Test Prompt Execution in the Right Side Panel
  await promptTextarea.fill('Write an executive overview on modern architectural composition.');
  const generateBtn = page.locator('#cora-copilot-chat-submit-btn');
  await generateBtn.click();

  // Wait for output preview card
  const outputCard = page.locator('#cora-copilot-panel-output');
  await expect(outputCard).toBeVisible({ timeout: 15000 });

  const previewText = await page.locator('#cora-copilot-output-preview').innerText();
  console.log('Generated Preview Text:', previewText.substring(0, 80));
  expect(previewText.length).toBeGreaterThan(10);

  // 6. Insert Generated Output at Cursor
  const insertBtn = page.locator('#cora-copilot-btn-insert-cursor');
  await expect(insertBtn).toBeVisible();
  await insertBtn.click();
  await page.waitForTimeout(600);

  // Verify Quill Canvas received the generated content
  const quillText = await page.locator('#cora-quill-editor').innerText();
  console.log('Quill Text after Insert:', quillText.substring(0, 80));
  expect(quillText.length).toBeGreaterThan(10);

  // Capture screenshot: Content inserted into Quill while side panel remains available
  await page.screenshot({ path: `${artifactDir}/copilot_inserted_live_into_canvas.png` });
  console.log('Captured copilot_inserted_live_into_canvas.png');

  // 7. Verify Tab Switching between Inspector Tabs
  await seoTabBtn.click();
  await page.waitForTimeout(300);
  await expect(page.locator('#panel-inspector-seo')).toBeVisible();
  await expect(copilotChatPanel).toBeHidden();

  await geoTabBtn.click();
  await page.waitForTimeout(300);
  await expect(page.locator('#panel-inspector-copilot')).toBeVisible();
  await expect(page.locator('#panel-sidebar-geo')).toBeVisible();

  await detailsTabBtn.click();
  await page.waitForTimeout(300);
  await expect(page.locator('#panel-inspector-meta')).toBeVisible();

  // Switch back to Copilot
  await copilotTabBtn.click();
  await page.waitForTimeout(300);
  await expect(copilotChatPanel).toBeVisible();

  await page.screenshot({ path: `${artifactDir}/copilot_tab_switching_verified.png` });
  console.log('Captured copilot_tab_switching_verified.png');
});
