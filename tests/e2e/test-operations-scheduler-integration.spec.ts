import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Operations Scheduler & Roster E2E Integration', () => {
  test('verify timeline and roster dynamic actions', async ({ page }) => {
    page.on('pageerror', exception => {
      console.log(`Uncaught exception: "${exception.message}"`);
    });
    page.on('console', msg => console.log('BROWSER LOG:', msg.text()));

    await page.setViewportSize({ width: 1440, height: 900 });
    await login(page);

    // 1. Load Crew Scheduler page
    await page.goto('/workspace/crew_scheduler');
    await page.waitForLoadState('networkidle');

    // Verify roster layout elements
    const heading = page.locator('h1', { hasText: 'Team Scheduler' }).first();
    await expect(heading).toBeVisible();

    const wrapper = page.locator('#cora-scheduler-unified-wrapper');
    await expect(wrapper).toBeVisible();
    
    const cardGrid = page.locator('#cora-shift-roster-cards-grid');
    await expect(cardGrid).toBeVisible();

    // 2. Open Assign Shift drawer
    const assignBtn = page.locator('button:has-text("Assign Shift")').first();
    await expect(assignBtn).toBeVisible();
    await assignBtn.click();
    await page.waitForTimeout(500);

    // Verify Add Shift drawer is visible
    const addDrawer = page.locator('#cora-add-shift-drawer');
    await expect(addDrawer).not.toHaveClass(/collapsed/);

    // Step 1: Fill in staff details
    await page.selectOption('#sh-staff-select', 'Rohan Verma');
    await page.locator('#cora-btn-shift-next').click();
    await page.waitForTimeout(300);

    // Step 2: Fill project & venue
    await page.fill('#sh-project-title', 'E2E Test Commercial Shoot');
    await page.fill('#sh-venue', 'Sector 62 office hub, Noida');
    await page.locator('#cora-btn-shift-next').click();
    await page.waitForTimeout(300);

    // Step 3: Fill date, time & payout
    await page.fill('#sh-date', '2026-08-15');
    await page.fill('#sh-time-start', '08:00 AM');
    await page.fill('#sh-time-end', '04:00 PM');
    await page.fill('#sh-day-rate', '20000');

    // Click Assign Shift
    const submitShiftBtn = page.locator('#cora-btn-shift-submit');
    await submitShiftBtn.click();
    await page.waitForTimeout(1500);

    // Verify shift was added in roster matrix
    await page.goto('/workspace/crew_scheduler');
    await page.waitForLoadState('networkidle');
    const newShiftProjectCell = page.locator('.sh-card-project-title:has-text("E2E Test Commercial Shoot")');
    await expect(newShiftProjectCell).toBeVisible();

    // 3. Switch to Itinerary Timeline tab
    const timelineTabBtn = page.locator('#tab-btn-timeline');
    await expect(timelineTabBtn).toBeVisible();
    await timelineTabBtn.click();
    await page.waitForTimeout(500);

    // Verify Itinerary timeline elements
    const addBlockBtn = page.locator('button:has-text("Add Time Block")');
    await expect(addBlockBtn).toBeVisible();

    // Open Add Time Block drawer
    await addBlockBtn.click();
    await page.waitForTimeout(500);

    const addBlockDrawer = page.locator('#cora-add-timeline-drawer');
    await expect(addBlockDrawer).not.toHaveClass(/collapsed/);

    // Fill block details
    await page.fill('#blk-activity-title', 'E2E Setup & Aerial Mapping');
    await page.fill('#blk-time-start', '08:30 AM');
    await page.fill('#blk-time-end', '10:00 AM');
    await page.fill('#blk-venue-address', 'Sector 62 main entrance, Noida');
    await page.fill('#blk-crew-member', 'Rohan Verma');

    // Submit
    const submitBlockBtn = addBlockDrawer.locator('button:has-text("Add Schedule Block")');
    await submitBlockBtn.click();
    await page.waitForTimeout(1500);

    // Verify block was added
    const newBlockTitle = page.locator('h3:has-text("E2E Setup & Aerial Mapping")').first();
    await expect(newBlockTitle).toBeVisible();

    // Take screenshot of dynamically populated scheduler
    await page.screenshot({ path: 'tests/e2e/operations-scheduler-verified.png' });
  });
});
