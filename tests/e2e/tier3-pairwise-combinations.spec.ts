import { test, expect, Page } from '@playwright/test';
import { login } from './helpers';

async function ensureMenuExists(page: Page) {
  await page.goto('/workspace/appearance');
  const select = page.locator('#cora-nav-menu-select');
  await select.waitFor({ state: 'visible' });
  const options = await select.locator('option').evaluateAll(elOpts => elOpts.map(opt => (opt as HTMLOptionElement).value));
  
  if (options.length === 0 || (options.length === 1 && options[0] === '0')) {
    await page.click('button[onclick="coraOpenNewMenuDrawer()"]');
    await page.waitForSelector('#cora-drawer-new-menu:not(.translate-x-full)', { state: 'visible' });
    await page.fill('#cora-new-menu-name', 'E2E Main Menu');
    await page.click('#cora-drawer-new-menu button:has-text("Create Menu")');
    await expect(page.locator('#cora-toast-container')).toContainText('Menu created successfully.');
    await page.waitForLoadState('networkidle');
  }
}

test.describe('Tier 3: Pairwise Combinations', () => {

  test.beforeEach(async ({ page }) => {
    await login(page);
  });

  test('1. Pages + Settings-Suite (Reading Homepage Assignment)', async ({ page }) => {
    const rand = Math.floor(Math.random() * 10000);
    const title = `E2E Front Page ${rand}`;
    const slug = `e2e-front-page-${rand}`;

    // Create a page
    await page.goto('/workspace/pages');
    await page.click('button:has-text("New Page")');
    await page.waitForSelector('#cora-drawer-page:not(.translate-x-full)', { state: 'visible' });
    await page.fill('#cora-page-title-input', title);
    await page.fill('#cora-page-slug-input', slug);
    await page.selectOption('#cora-page-status-input', 'publish');
    await page.click('#cora-drawer-page button:has-text("Save Page")');
    await expect(page.locator('#cora-toast-container')).toContainText('Page saved successfully.');
    await page.waitForLoadState('networkidle');

    // Extract page ID
    const row = page.locator(`tr.cora-page-row:has-text("${title}")`).first();
    await row.waitFor({ state: 'visible' });
    const onclick = await row.getAttribute('onclick');
    const pageId = onclick ? onclick.match(/\d+/)?.[0] : null;
    expect(pageId).not.toBeNull();

    // Assign as homepage
    await page.goto('/workspace/settings-suite?settings_tab=reading');
    await page.check('input[name="show_on_front"][value="page"]');
    await page.selectOption('select[name="page_on_front"]', pageId!);
    await page.click('button:has-text("Save All Settings")');
    await expect(page.locator('#cora-toast-container')).toContainText('Global system settings updated successfully.');

    // Verify Homepage on frontend
    await page.goto('/');
    await expect(page.locator('body')).toContainText(title);

    // Reset settings
    await page.goto('/workspace/settings-suite?settings_tab=reading');
    await page.check('input[name="show_on_front"][value="posts"]');
    await page.click('button:has-text("Save All Settings")');
    await expect(page.locator('#cora-toast-container')).toContainText('Global system settings updated successfully.');
  });

  test('2. Media-Editor + Appearance (Logo Setting)', async ({ page }) => {
    // Navigate to media-editor to copy the first image URL
    await page.goto('/workspace/media-editor');
    const imgElement = page.locator('#cora-editor-preview-img');
    await imgElement.waitFor({ state: 'visible' });
    const imgUrl = await imgElement.getAttribute('src');
    expect(imgUrl).not.toBeNull();

    // Set as logo in Appearance
    await page.goto('/workspace/appearance');
    await page.fill('#cora-brand-logo-url', imgUrl!);
    await page.click('button:has-text("Save All Settings")');
    await expect(page.locator('#cora-toast-container')).toContainText('Appearance settings saved successfully.');
  });

  test('3. Pages + Comments (Discussion Moderation Flow)', async ({ page }) => {
    const rand = Math.floor(Math.random() * 10000);
    const title = `E2E Comments Page ${rand}`;
    const slug = `e2e-comments-page-${rand}`;

    // Create page
    await page.goto('/workspace/pages');
    await page.click('button:has-text("New Page")');
    await page.waitForSelector('#cora-drawer-page:not(.translate-x-full)', { state: 'visible' });
    await page.fill('#cora-page-title-input', title);
    await page.fill('#cora-page-slug-input', slug);
    await page.selectOption('#cora-page-status-input', 'publish');
    await page.click('#cora-drawer-page button:has-text("Save Page")');
    await expect(page.locator('#cora-toast-container')).toContainText('Page saved successfully.');
    await page.waitForLoadState('networkidle');

    // Extract page ID
    const row = page.locator(`tr.cora-page-row:has-text("${title}")`).first();
    await row.waitFor({ state: 'visible' });
    const onclick = await row.getAttribute('onclick');
    const pageId = onclick ? onclick.match(/\d+/)?.[0] : null;
    expect(pageId).not.toBeNull();

    // Post comment via POST directly to bypass theme template limitations
    await page.goto(`/${slug}/`);
    const commentText = `E2E Comment text ${rand}`;
    await page.evaluate(async ({ pid, text }) => {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = '/wp-comments-post.php';
      
      const commentInput = document.createElement('input');
      commentInput.type = 'hidden';
      commentInput.name = 'comment';
      commentInput.value = text;
      form.appendChild(commentInput);

      const postInput = document.createElement('input');
      postInput.type = 'hidden';
      postInput.name = 'comment_post_ID';
      postInput.value = pid;
      form.appendChild(postInput);

      document.body.appendChild(form);
      form.submit();
    }, { pid: pageId!, text: commentText });
    
    await page.waitForLoadState('networkidle');

    // Go to comments moderation
    await page.goto('/workspace/comments');
    const commentRow = page.locator(`div[id^="cora-comment-"]:has-text("${commentText}")`).first();
    await expect(commentRow).toBeVisible();

    // Moderate/Unapprove comment
    const unapproveBtn = commentRow.locator('button:has-text("Unapprove")');
    if (await unapproveBtn.isVisible()) {
      await unapproveBtn.click();
      await expect(page.locator('#cora-toast-container')).toContainText('Comment status updated successfully.');
    }
  });

  test('4. Pages + Appearance (Menu Link Insertion)', async ({ page }) => {
    await ensureMenuExists(page);

    const rand = Math.floor(Math.random() * 10000);
    const title = `E2E Menu Page ${rand}`;

    // Create page as PUBLISHED so it shows in get_pages()
    await page.goto('/workspace/pages');
    await page.click('button:has-text("New Page")');
    await page.waitForSelector('#cora-drawer-page:not(.translate-x-full)', { state: 'visible' });
    await page.fill('#cora-page-title-input', title);
    await page.selectOption('#cora-page-status-input', 'publish');
    await page.click('#cora-drawer-page button:has-text("Save Page")');
    await expect(page.locator('#cora-toast-container')).toContainText('Page saved successfully.');
    await page.waitForLoadState('networkidle');

    // Add to Menu
    await page.goto('/workspace/appearance');
    await page.click('button:has-text("Add Menu Link")');
    await page.waitForSelector('#cora-drawer-menu-item:not(.translate-x-full)', { state: 'visible' });

    await page.selectOption('#cora-menu-item-type', 'page');
    await page.selectOption('#cora-menu-page-id', { label: title });
    await page.click('button:has-text("Add to Menu")');
    await expect(page.locator('#cora-toast-container')).toContainText('Menu item added successfully.');
  });

  test('5. Tools + Pages (Selective XML Data Backup)', async ({ page }) => {
    const rand = Math.floor(Math.random() * 10000);
    const title = `E2E Export Page ${rand}`;

    // Create page
    await page.goto('/workspace/pages');
    await page.click('button:has-text("New Page")');
    await page.waitForSelector('#cora-drawer-page:not(.translate-x-full)', { state: 'visible' });
    await page.fill('#cora-page-title-input', title);
    await page.click('#cora-drawer-page button:has-text("Save Page")');
    await expect(page.locator('#cora-toast-container')).toContainText('Page saved successfully.');
    await page.waitForLoadState('networkidle');

    // XML Export Pages only
    await page.goto('/workspace/tools');
    await page.check('input[name="cora_export_type"][value="pages"]');
    await page.click('button:has-text("Download XML Export File")');
    await expect(page.locator('#cora-toast-container')).toContainText('XML WXR export initiated successfully.');

    // Delete the page
    await page.goto('/workspace/pages');
    const row = page.locator(`tr.cora-page-row:has-text("${title}")`).first();
    await row.waitFor({ state: 'visible' });
    await row.locator('button:has-text("Delete")').click();
    await page.click('#cora-confirm-btn');
    await expect(page.locator('#cora-toast-container')).toContainText('Page deleted successfully.');
  });

  test('6. Settings-Suite (Permalinks) + Pages (Format Switching)', async ({ page }) => {
    const rand = Math.floor(Math.random() * 10000);
    const title = `E2E Perm Switch Page ${rand}`;

    // Create page (must be published so permalinks change)
    await page.goto('/workspace/pages');
    await page.click('button:has-text("New Page")');
    await page.waitForSelector('#cora-drawer-page:not(.translate-x-full)', { state: 'visible' });
    await page.fill('#cora-page-title-input', title);
    await page.selectOption('#cora-page-status-input', 'publish');
    await page.click('#cora-drawer-page button:has-text("Save Page")');
    await expect(page.locator('#cora-toast-container')).toContainText('Page saved successfully.');
    await page.waitForLoadState('networkidle');

    // 1. Change permalinks to Plain
    await page.goto('/workspace/settings-suite?settings_tab=permalinks');
    await page.check('input[name="permalink_structure"][value=""]');
    await page.click('button:has-text("Save All Settings")');
    await expect(page.locator('#cora-toast-container')).toContainText('Global system settings updated successfully.');

    // 2. View page link format
    await page.goto('/workspace/pages');
    const firstSlug = page.locator(`tr.cora-page-row:has-text("${title}") td span.font-mono`).first();
    await expect(firstSlug).toBeVisible();
    const plainFormatText = await firstSlug.getAttribute('title');

    // 3. Change permalinks back to Post Name
    await page.goto('/workspace/settings-suite?settings_tab=permalinks');
    await page.check('input[name="permalink_structure"][value="/%postname%/"]');
    await page.click('button:has-text("Save All Settings")');
    await expect(page.locator('#cora-toast-container')).toContainText('Global system settings updated successfully.');

    // 4. Verify link format updated
    await page.goto('/workspace/pages');
    const updatedSlug = page.locator(`tr.cora-page-row:has-text("${title}") td span.font-mono`).first();
    await expect(updatedSlug).toBeVisible();
    const updatedFormatText = await updatedSlug.getAttribute('title');

    expect(plainFormatText).not.toBe(updatedFormatText);
  });

});
