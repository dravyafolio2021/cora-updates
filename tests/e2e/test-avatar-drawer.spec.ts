import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('verify user avatar select and update functionality under photography_studio', async ({ page }) => {
  // 1. Log in and navigate to members/roles page under photography_studio
  await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
  await page.goto('/workspace/team-roles?industry=photography_studio');
  await page.waitForLoadState('networkidle');

  // Print all enqueued scripts to see which admin-script.js is loaded
  const scriptSrcs = await page.evaluate(() => {
    return Array.from(document.querySelectorAll('script'))
      .map(s => s.src)
      .filter(src => src.includes('admin-script.js'));
  });
  console.log('Enqueued admin-script.js URLs on photography studio page:', scriptSrcs);

  // 2. Locate first member row and click Edit
  const memberRow = page.locator('#tab-active-members table tbody tr').first();
  await expect(memberRow).toBeVisible();
  const manageBtn = memberRow.locator('button').filter({ hasText: /edit/i }).first();
  await expect(manageBtn).toBeVisible();
  await manageBtn.click();

  // 3. Verify Edit User drawer is visible
  const editDrawer = page.locator('#cora-edit-user-drawer');
  await expect(editDrawer).toBeVisible();

  // 4. Click the avatar preview to open the media modal
  const avatarPreview = page.locator('#edit-avatar-preview');
  await expect(avatarPreview).toBeVisible();
  await avatarPreview.click();

  // 5. Verify the media modal opens
  const mediaModal = page.locator('.media-modal');
  await expect(mediaModal).toBeVisible();

  // 6. Verify our custom select button is appended and visible
  const customSelectBtn = page.locator('.cora-media-select-btn');
  await expect(customSelectBtn).toBeVisible();
  await expect(customSelectBtn).toBeDisabled();

  // 7. Click on the Media Library tab
  const mediaLibraryTab = page.locator('.media-modal #menu-item-browse');
  await expect(mediaLibraryTab).toBeVisible();
  await mediaLibraryTab.click();

  // 8. Select the first attachment item in the library
  const attachment = page.locator('.media-modal .attachments-browser .attachments .attachment').first();
  await expect(attachment).toBeVisible();
  await attachment.click();

  // 9. Verify the custom select button is now enabled
  await expect(customSelectBtn).toBeEnabled();

  // 10. Click the custom select button
  await customSelectBtn.click();

  // 11. Verify the media modal closes
  await expect(mediaModal).toBeHidden();

  // 12. Verify the avatar preview image has updated and is visible
  const avatarImg = page.locator('#edit-avatar-img');
  await expect(avatarImg).toBeVisible();
  const imgSrc = await avatarImg.getAttribute('src');
  console.log('Successfully updated avatar image under photography studio to:', imgSrc);
  expect(imgSrc).not.toBeNull();
  expect(imgSrc?.length).toBeGreaterThan(0);
});
