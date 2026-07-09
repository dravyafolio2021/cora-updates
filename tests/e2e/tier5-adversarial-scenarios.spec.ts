import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Tier 5: Adversarial E2E Scenarios', () => {

  test.beforeEach(async ({ page }) => {
    await login(page);
  });

  test('Pages - Parent-Child Relationship, Ordering, & Hierarchy', async ({ page }) => {
    const rand = Math.floor(Math.random() * 10000);
    const parentTitle = `E2E Parent Page ${rand}`;
    const parentSlug = `e2e-parent-${rand}`;
    const childTitle = `E2E Child Page ${rand}`;
    const childSlug = `e2e-child-${rand}`;

    // 1. Create Parent Page (must be published to show up in dropdown)
    await page.goto('/workspace/pages');
    await page.click('button:has-text("New Page")');
    await page.waitForSelector('#cora-drawer-page:not(.translate-x-full)', { state: 'visible' });

    await page.fill('#cora-page-title-input', parentTitle);
    await page.fill('#cora-page-slug-input', parentSlug);
    await page.selectOption('#cora-page-status-input', 'publish');
    await page.click('#cora-drawer-page button:has-text("Save Page")');
    await expect(page.locator('#cora-toast-container')).toContainText('Page saved successfully.');
    await page.waitForLoadState('networkidle');

    // 2. Create Child Page and assign Parent & Order
    await page.goto('/workspace/pages');
    await page.click('button:has-text("New Page")');
    await page.waitForSelector('#cora-drawer-page:not(.translate-x-full)', { state: 'visible' });

    await page.fill('#cora-page-title-input', childTitle);
    await page.fill('#cora-page-slug-input', childSlug);
    await page.selectOption('#cora-page-status-input', 'draft');
    
    // Select the parent page in dropdown
    await page.selectOption('#cora-page-parent-input', { label: parentTitle });
    // Set order to 15
    await page.fill('#cora-page-order-input', '15');

    await page.click('#cora-drawer-page button:has-text("Save Page")');
    await expect(page.locator('#cora-toast-container')).toContainText('Page saved successfully.');
    await page.waitForLoadState('networkidle');

    // 3. Verify on pages list that child lists parent page
    await page.goto('/workspace/pages');
    const childRow = page.locator(`tr.cora-page-row:has-text("${childTitle}")`).first();
    await expect(childRow).toBeVisible();
    await expect(childRow.locator('td').nth(2)).toContainText(parentTitle); // Parent page column is 3rd (index 2)

    // 4. Edit child page and change parent to none, order to 0
    await childRow.click();
    await page.waitForSelector('#cora-drawer-page:not(.translate-x-full)', { state: 'visible' });
    await expect(page.locator('#cora-page-title-input')).not.toHaveValue('Loading...');
    await page.selectOption('#cora-page-parent-input', '0'); // value 0 is No Parent
    await page.fill('#cora-page-order-input', '0');
    await page.click('#cora-drawer-page button:has-text("Save Page")');
    await expect(page.locator('#cora-toast-container')).toContainText('Page saved successfully.');
    await page.waitForLoadState('networkidle');

    // 5. Verify on list that parent is now "—"
    await page.goto('/workspace/pages');
    const updatedChildRow = page.locator(`tr.cora-page-row:has-text("${childTitle}")`).first();
    await expect(updatedChildRow).toBeVisible();
    await expect(updatedChildRow.locator('td').nth(2)).toContainText('—');

    // Clean up: delete both pages
    // Delete Child Page
    await updatedChildRow.locator('button:has-text("Delete")').click();
    await page.waitForSelector('#cora-confirm-modal:not(.hidden)', { state: 'visible' });
    await page.click('#cora-confirm-btn');
    await expect(page.locator('#cora-toast-container')).toContainText('Page deleted successfully.');
    await page.waitForLoadState('networkidle');

    // Delete Parent Page
    await page.goto('/workspace/pages');
    const parentRow = page.locator(`tr.cora-page-row:has-text("${parentTitle}")`).first();
    await parentRow.locator('button:has-text("Delete")').click();
    await page.waitForSelector('#cora-confirm-modal:not(.hidden)', { state: 'visible' });
    await page.click('#cora-confirm-btn');
    await expect(page.locator('#cora-toast-container')).toContainText('Page deleted successfully.');
  });

  test('Comments - Trash, Restore, and Permanent Delete Moderation Lifecycle', async ({ page }) => {
    const rand = Math.floor(Math.random() * 100000);
    const commentText = `Adversarial Comment Text ${rand}`;

    // 1. Submit comment on frontend (using canonical plain URL p=1 to guarantee single post view)
    await page.goto('/?p=1');
    await page.fill('#comment', commentText);
    await page.click('#submit');
    await page.waitForLoadState('networkidle');

    // 2. Go to workspace/comments, expect comment to be visible
    await page.goto('/workspace/comments');
    const commentRow = page.locator(`div[id^="cora-comment-"]:has-text("${commentText}")`).first();
    await expect(commentRow).toBeVisible();

    // 3. Move comment to Trash
    const trashBtn = commentRow.locator('button:has-text("Trash")');
    await expect(trashBtn).toBeVisible();
    await trashBtn.click();
    await expect(page.locator('#cora-toast-container')).toContainText('Comment status updated successfully.');
    await page.waitForLoadState('networkidle');

    // 4. Go to Trash tab and verify comment is there
    await page.goto('/workspace/comments?comment_status=trash');
    const trashedRow = page.locator(`div[id^="cora-comment-"]:has-text("${commentText}")`).first();
    await expect(trashedRow).toBeVisible();

    // 5. Restore the comment
    const restoreBtn = trashedRow.locator('button:has-text("Restore")');
    await expect(restoreBtn).toBeVisible();
    await restoreBtn.click();
    await expect(page.locator('#cora-toast-container')).toContainText('Comment status updated successfully.');
    await page.waitForLoadState('networkidle');

    // 6. Go to All tab, verify comment is back
    await page.goto('/workspace/comments');
    const restoredRow = page.locator(`div[id^="cora-comment-"]:has-text("${commentText}")`).first();
    await expect(restoredRow).toBeVisible();

    // 7. Move comment to Trash again
    await restoredRow.locator('button:has-text("Trash")').click();
    await expect(page.locator('#cora-toast-container')).toContainText('Comment status updated successfully.');
    await page.waitForLoadState('networkidle');

    // 8. Go to Trash tab, click Delete Permanently
    await page.goto('/workspace/comments?comment_status=trash');
    const trashedRow2 = page.locator(`div[id^="cora-comment-"]:has-text("${commentText}")`).first();
    await expect(trashedRow2).toBeVisible();
    
    await trashedRow2.locator('button:has-text("Delete Permanently")').click();
    await page.waitForSelector('#cora-confirm-modal:not(.hidden)', { state: 'visible' });
    await page.click('#cora-confirm-btn');
    await expect(page.locator('#cora-toast-container')).toContainText('Comment permanently deleted.');
    await page.waitForLoadState('networkidle');

    // 9. Verify it is no longer in Trash tab
    await page.goto('/workspace/comments?comment_status=trash');
    const deletedRow = page.locator(`div[id^="cora-comment-"]:has-text("${commentText}")`).first();
    await expect(deletedRow).not.toBeVisible();
  });

});
