import { test, expect } from '@playwright/test';

test('test preview bar dropdown toggle and click item', async ({ page }) => {
  await page.goto('http://cora.local/?cv_preview_theme=195', { waitUntil: 'networkidle' });

  const trigger = page.locator('#cpb-dropdown-trigger-btn');
  await expect(trigger).toBeVisible({ timeout: 5000 });

  const menu = page.locator('#cpb-dropdown-menu-list');
  await expect(menu).not.toHaveClass(/show/);

  // Click trigger
  await trigger.click();
  await expect(menu).toHaveClass(/show/);

  await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/a4976989-efa2-4d6f-8a0a-3f15bbcd8c07/.tempmediaStorage/dropdown_menu_open.png' });

  // Click item 'Contact'
  const contactItem = page.locator('.cpb-dropdown-item', { hasText: 'Contact' });
  await contactItem.click();

  await page.waitForLoadState('networkidle');
  console.log('Navigated URL:', page.url());
  await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/a4976989-efa2-4d6f-8a0a-3f15bbcd8c07/.tempmediaStorage/contact_page_preview.png' });
});
