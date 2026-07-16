import { test, expect } from '@playwright/test';
import { login } from './helpers';
import * as fs from 'fs';

test('debug workspace dashboard layout', async ({ page }) => {
  await login(page);
  await page.goto('/workspace/dashboard');
  
  // Wait for dashboard section to load
  await page.waitForSelector('#cora-page-dashboard');
  
  // Capture HTML
  const content = await page.innerHTML('#cora-page-dashboard');
  fs.writeFileSync('tests/e2e/dashboard-debug.html', content);
  
  // Capture styling of greeting & metrics grid
  const metricsStyle = await page.evaluate(() => {
    const el = document.querySelector('.bg-white\\/80.dark\\:bg-zinc-900\\/60');
    if (!el) return 'NOT FOUND';
    const computed = window.getComputedStyle(el);
    return {
      display: computed.display,
      visibility: computed.visibility,
      opacity: computed.opacity,
      position: computed.position,
      height: computed.height,
      width: computed.width,
      margin: computed.margin,
      padding: computed.padding,
    };
  });
  
  const greetingStyle = await page.evaluate(() => {
    const el = document.getElementById('cora-dynamic-greeting-title');
    if (!el) return 'NOT FOUND';
    const parent = el.closest('div.text-center');
    if (!parent) return 'PARENT NOT FOUND';
    const computed = window.getComputedStyle(parent);
    return {
      display: computed.display,
      visibility: computed.visibility,
      opacity: computed.opacity,
      position: computed.position,
      height: computed.height,
      width: computed.width,
      margin: computed.margin,
      padding: computed.padding,
    };
  });

  console.log('METRICS STYLE:', JSON.stringify(metricsStyle, null, 2));
  console.log('GREETING STYLE:', JSON.stringify(greetingStyle, null, 2));
  
  // Take screenshot
  await page.screenshot({ path: 'tests/e2e/dashboard-debug.png', fullPage: true });
});
