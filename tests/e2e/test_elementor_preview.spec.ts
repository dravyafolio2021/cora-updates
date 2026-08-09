import { test, expect } from '@playwright/test';

test('test elementor supported theme internal page preview', async ({ page }) => {
  const logs: string[] = [];
  page.on('console', msg => logs.push(msg.text()));

  // Preview Theme 44 homepage
  await page.goto('http://cora.local/?cv_preview_theme=44', { waitUntil: 'networkidle' });
  await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/a4976989-efa2-4d6f-8a0a-3f15bbcd8c07/.tempmediaStorage/theme_44_home.png', fullPage: true });

  // Preview Theme 44 Services page (wp_post_id: 2032)
  await page.goto('http://cora.local/?page_id=2032&cv_preview_theme=44', { waitUntil: 'networkidle' });
  await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/a4976989-efa2-4d6f-8a0a-3f15bbcd8c07/.tempmediaStorage/theme_44_services.png', fullPage: true });

  // Preview Theme 44 Contact page (wp_post_id: 2039)
  await page.goto('http://cora.local/?page_id=2039&cv_preview_theme=44', { waitUntil: 'networkidle' });
  await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/a4976989-efa2-4d6f-8a0a-3f15bbcd8c07/.tempmediaStorage/theme_44_contact.png', fullPage: true });
});
