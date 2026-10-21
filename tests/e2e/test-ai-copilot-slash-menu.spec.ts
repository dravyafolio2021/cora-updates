import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('Verify Redesigned AI Copilot Command Palette & Dynamic Slash Menu', async ({ page }) => {
  const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/58e87118-a119-4a20-9906-b30972fba7f6';

  await page.setViewportSize({ width: 1280, height: 850 });
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

  // 3. Test AI Copilot Command Palette
  const aiTriggerBtn = page.locator('#cora-ai-palette-btn');
  await expect(aiTriggerBtn).toBeVisible();
  await aiTriggerBtn.click();
  await page.waitForTimeout(500);

  // Verify: Right side panel AI Copilot is active and blocking popup modal is hidden
  const aiPalette = page.locator('#cora-ai-writing-palette');
  await expect(aiPalette).toBeHidden();

  const copilotTabBtn = page.locator('#btn-sidebar-copilot');
  await expect(copilotTabBtn).toHaveClass(/tab-active/);

  const copilotPanel = page.locator('#panel-inspector-copilot-chat');
  await expect(copilotPanel).toBeVisible();

  // Verify input in right panel is visible
  const promptInput = page.locator('#cora-copilot-chat-input');
  await expect(promptInput).toBeVisible();
  const inputPlaceholder = await promptInput.getAttribute('placeholder');
  console.log('AI Prompt Placeholder:', inputPlaceholder);
  expect(inputPlaceholder).toContain('Ask AI Copilot');

  // Verify Quick Action chips in panel
  await expect(copilotPanel.locator('button:has-text("Write Intro")')).toBeVisible();
  await expect(copilotPanel.locator('button:has-text("Continue")')).toBeVisible();
  await expect(copilotPanel.locator('button:has-text("Expand Section")')).toBeVisible();
  await expect(copilotPanel.locator('button:has-text("Improve Clarity")')).toBeVisible();

  // Capture screenshot of the right panel AI Copilot
  await page.screenshot({ path: `${artifactDir}/ai_copilot_palette_centered_redesigned.png` });
  console.log('Captured ai_copilot_palette_centered_redesigned.png');

  // 4. Test Dynamic Slash Command Menu
  const quillEditor = page.locator('#cora-quill-editor .ql-editor');
  await expect(quillEditor).toBeVisible();
  await quillEditor.click();
  await page.waitForTimeout(200);

  // Type '/' to open slash menu
  await page.keyboard.type('/');
  await page.waitForTimeout(500);

  const slashMenu = page.locator('#cora-editor-slash-menu');
  await expect(slashMenu).toBeVisible();

  // Verify modular sections exist dynamically
  const slashText = await slashMenu.innerText();
  console.log('Slash Menu Sections Rendered:', slashText);

  // Capture full dynamic slash menu
  await page.screenshot({ path: `${artifactDir}/slash_menu_dynamic_modules_verified.png` });
  console.log('Captured slash_menu_dynamic_modules_verified.png');

  // 5. Test Dynamic Search Filtering in Slash Menu
  await page.keyboard.type('rate');
  await page.waitForTimeout(400);

  // The Rates item should be visible
  const rateItem = slashMenu.locator('.cora-slash-item:has-text("Rate Card")');
  await expect(rateItem).toBeVisible();

  // Capture filtered state (category headers without matches are cleanly hidden)
  await page.screenshot({ path: `${artifactDir}/slash_menu_dynamic_filtered_rate.png` });
  console.log('Captured slash_menu_dynamic_filtered_rate.png');

  // Press Escape to dismiss slash menu
  await page.keyboard.press('Escape');
  await page.waitForTimeout(300);
});
