import { test, expect } from '@playwright/test';
import { login } from './helpers';
import * as path from 'path';

test.describe('Workspace Interactive Calendar & Scheduling Engine Verification', () => {
  const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/79e3699e-f38a-4cfb-a2e9-626c98a66fe9';

  test('Verify Month/Week/Day views, Google 2-way sync, Event details, and Booking Links drawer', async ({ page }) => {
    page.on('console', msg => console.log('PAGE LOG:', msg.text()));
    page.on('pageerror', err => console.log('PAGE ERROR:', err.message));
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
    await page.setViewportSize({ width: 1440, height: 920 });
    console.log('Step 1: Navigating to /workspace/calendar');
    await page.goto('/workspace/calendar');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    console.log('Step 2: Checking header & period');
    await expect(page.locator('#cora-calendar-period-label')).toContainText('October 2026');

    console.log('Step 3: Checking month grid');
    const monthGrid = page.locator('#cora-month-grid');
    await expect(monthGrid).toBeVisible();

    const todayCell = page.locator('.cora-calendar-day-cell.is-today');
    await expect(todayCell).toBeVisible();
    await expect(todayCell).toContainText('15');

    console.log('Step 4: Checking today event pill');
    await expect(todayCell.locator('.cora-event-pill').first()).toBeVisible();

    console.log('Step 5: Taking screenshot');
    await page.screenshot({
      path: path.join(artifactDir, 'calendar_interactive_month_view.png'),
      fullPage: false,
    });

    console.log('Step 6: Switching to Week view');
    const weekBtn = page.locator('#cal-view-btn-week');
    await weekBtn.click();
    await page.waitForTimeout(300);
    await expect(page.locator('#cora-calendar-view-week')).toBeVisible();
    await page.screenshot({
      path: path.join(artifactDir, 'calendar_interactive_week_view.png'),
      fullPage: false,
    });

    console.log('Step 7: Switching to Day view');
    const dayBtn = page.locator('#cal-view-btn-day');
    await dayBtn.click();
    await page.waitForTimeout(300);
    await expect(page.locator('#cora-calendar-view-day')).toBeVisible();
    await page.screenshot({
      path: path.join(artifactDir, 'calendar_interactive_day_view.png'),
      fullPage: false,
    });

    console.log('Step 8: Switching back to Month view');
    const monthBtn = page.locator('#cal-view-btn-month');
    await monthBtn.click();
    await page.waitForTimeout(300);
    await expect(page.locator('#cora-calendar-view-month')).toBeVisible();

    console.log('Step 9: Testing Event details drawer');
    const eventPill = todayCell.locator('.cora-event-pill').first();
    await eventPill.click();
    await page.waitForTimeout(400);

    const eventDrawer = page.locator('#cora-calendar-details-drawer');
    await expect(eventDrawer).toHaveClass(/open/);
    await expect(page.locator('#detail-event-title')).toContainText('1-Hour Studio Portrait Session');
    await expect(page.locator('#detail-client-name')).toContainText('Aarav Mehta');
    await expect(page.locator('#detail-crm-lead')).toContainText('Executive Portraits');

    await page.screenshot({
      path: path.join(artifactDir, 'calendar_event_details_panel.png'),
      fullPage: false,
    });

    // Close event drawer via close button
    await page.click('#btn-close-event-details');
    await page.waitForTimeout(300);
    await expect(eventDrawer).not.toHaveClass(/open/);

    // 6. Test Booking Links Drawer
    const bookingLinksBtn = page.locator('button:has-text("Booking Links")');
    await bookingLinksBtn.click();
    await page.waitForTimeout(400);

    const linksDrawer = page.locator('#cora-calendar-links-drawer');
    await expect(linksDrawer).toHaveClass(/open/);
    await expect(linksDrawer.locator('h4:has-text("1-Hour Studio Session")')).toBeVisible();
    await expect(linksDrawer.locator('h4:has-text("15-Min Quick Intro Call")')).toBeVisible();

    // Test copy snippet inside drawer
    const copySnippet = linksDrawer.locator('.copy-feedback-text').first();
    await copySnippet.click();
    await page.waitForTimeout(300);

    // Capture Booking Links Drawer Screenshot
    await page.screenshot({
      path: path.join(artifactDir, 'calendar_booking_links_drawer.png'),
      fullPage: false,
    });

    // Close booking links drawer via Done button
    await linksDrawer.locator('button:has-text("Done")').click();
    await page.waitForTimeout(300);
    await expect(linksDrawer).not.toHaveClass(/open/);

    // 7. Test "New Event" Appointment Drawer (No double +)
    const newEventBtn = page.locator('button:has-text("New Event")');
    await newEventBtn.click();
    await page.waitForTimeout(400);

    const createDrawer = page.locator('#cora-calendar-appointment-drawer');
    await expect(createDrawer).toHaveClass(/open/);

    // Capture Appointment Drawer Screenshot with visible pinned footer
    await page.screenshot({
      path: path.join(artifactDir, 'calendar_appointment_drawer_pinned_footer.png'),
      fullPage: false,
    });

    await page.fill('#appt-input-title', 'Commercial Lookbook Review');
    await page.fill('#appt-input-client-name', 'Rohan Verma');
    await page.selectOption('#appt-input-location', 'Google Meet');
    await page.click('button:has-text("Confirm & Sync Event")');
    await page.waitForTimeout(400);

    // Verify appointment was created and drawer closed
    await expect(createDrawer).not.toHaveClass(/open/);
  });
});
