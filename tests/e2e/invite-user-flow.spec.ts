import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Cora User Invitation Flow E2E Tests', () => {

  test('Open invite drawer, verify dynamic labels, preview role permissions, toggle welcome msg, and send invite', async ({ page }) => {
    // 1. Log in and navigate to members/roles page
    await login(page);
    await page.goto('/workspace/team-roles?industry=real_estate');
    await page.waitForLoadState('networkidle');

    // 2. Click the "Invite User" button (or filter buttons)
    const inviteBtn = page.locator('button:has-text("Invite User")').first();
    await expect(inviteBtn).toBeVisible();
    await inviteBtn.click();

    // 3. Verify that the Invite Drawer is visible
    const inviteDrawer = page.locator('#cora-invite-user-drawer');
    await expect(inviteDrawer).toBeVisible();

    // 4. Verify the dynamic industry adapt title "Invite Brokerage Member"
    const titleText = await inviteDrawer.locator('#invite-drawer-title').textContent();
    expect(titleText).toContain('Invite Brokerage Member');

    // 5. Verify the dynamic branch label
    const branchLabel = await inviteDrawer.locator('#invite-branch-label').textContent();
    expect(branchLabel).toContain('Assign Branch');

    // 6. Test the live permissions preview card triggers on role change
    const roleSelect = inviteDrawer.locator('#invite-role');
    await expect(roleSelect).toBeVisible();
    
    // Select Branch Manager
    await roleSelect.selectOption('cora_branch_manager');

    // Preview card should be visible
    const previewCard = inviteDrawer.locator('#invite-role-preview-card');
    await expect(previewCard).toBeVisible();

    // Verify badge text in preview card is "System Role"
    const badgeText = await previewCard.locator('#role-preview-badge').textContent();
    expect(badgeText).toContain('System Role');

    // Verify grid has content
    const gridItems = previewCard.locator('#role-preview-grid div');
    await expect(gridItems.first()).toBeVisible();

    // 7. Toggle personal welcome message visibility
    const toggleMsgBtn = inviteDrawer.locator('button:has-text("Add personal welcome message")');
    await expect(toggleMsgBtn).toBeVisible();
    await toggleMsgBtn.click();

    // Welcome message container should slide visible/be shown
    const msgTextarea = inviteDrawer.locator('#invite-personal-message');
    await expect(msgTextarea).toBeVisible();
    await msgTextarea.fill('Welcome to Cora real estate brokerage, looking forward to working with you!');

    // 8. Fill in guest name and email
    const uniqueEmail = `test.guest.${Date.now()}@heycora.in`;
    await inviteDrawer.locator('#invite-first-name').fill('Vikas');
    await inviteDrawer.locator('#invite-last-name').fill('Mehta');
    await inviteDrawer.locator('#invite-email').fill(uniqueEmail);

    // 9. Click send invitation
    const sendBtn = inviteDrawer.locator('#send-invite-btn');
    await expect(sendBtn).toBeVisible();
    await sendBtn.click();

    // 10. Check success feedback toast
    const toast = page.locator('#cora-toast-container');
    await expect(toast).toContainText(/Invitation sent successfully/i);

    // Wait for the drawer to close
    await expect(inviteDrawer).toBeHidden();
  });

});
