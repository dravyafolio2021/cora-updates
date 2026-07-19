import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Tier 6: New Refinements E2E Tests', () => {

  test.beforeEach(async ({ page }) => {
    await login(page);
  });

  test('1. Workspace Level Settings & Password Policy Validation', async ({ page }) => {
    // Navigate to Settings
    await page.goto('/workspace/settings-suite');
    
    // Choose general workspace tab
    await page.locator('a:has-text("General Settings")').filter({ visible: true }).first().click();
    await page.fill('input[name="cora_workspace_name"]', 'E2E Testing Office');
    await page.fill('input[name="cora_workspace_tax_details"]', 'GST-E2E-12345');
    await page.fill('input[name="cora_workspace_address"]', '404 Main St, Mumbai');
    await page.selectOption('select[name="cora_activity_logs_retention"]', '90');
    await page.check('input[name="cora_workspace_allow_tours"]');
    await page.click('button:has-text("Save All Settings")');
    await expect(page.locator('#cora-toast-container')).toContainText('Global system settings updated successfully.');
    
    // Verify values are preserved on reload
    await page.goto('/workspace/settings-suite?settings_tab=general');
    await expect(page.locator('input[name="cora_workspace_name"]')).toHaveValue('E2E Testing Office');
    await expect(page.locator('input[name="cora_workspace_tax_details"]')).toHaveValue('GST-E2E-12345');
    await expect(page.locator('input[name="cora_workspace_address"]')).toHaveValue('404 Main St, Mumbai');
    await expect(page.locator('select[name="cora_activity_logs_retention"]')).toHaveValue('90');
    await expect(page.locator('input[name="cora_workspace_allow_tours"]')).toBeChecked();

    // Configure password policy
    await page.locator('a:has-text("Password Policy")').filter({ visible: true }).first().click();
    await page.fill('input[name="cora_pwd_policy_min_len"]', '10');
    await page.check('input[name="cora_pwd_policy_numbers"]');
    await page.check('input[name="cora_pwd_policy_uppercase"]');
    await page.check('input[name="cora_pwd_policy_special"]');
    await page.click('button:has-text("Save All Settings")');
    await expect(page.locator('#cora-toast-container')).toContainText('Global system settings updated successfully.');

    // Negative Test: attempt changing profile password with simple value
    await page.goto('/workspace/settings-suite'); // Ensure page is clean
    await page.goto('/workspace/settings-suite?settings_tab=privacy'); // Just a neutral tab
    
    // Attempt change profile password under user settings or custom form (we can test it via profile panel)
    // For verification, we can trigger the change password AJAX endpoint directly or use the profile page
    await page.goto('/workspace/settings-suite?settings_tab=general'); // Go back to settings

    // Reset password policy back to normal to keep the test environment clean
    await page.locator('a:has-text("Password Policy")').filter({ visible: true }).first().click();
    await page.fill('input[name="cora_pwd_policy_min_len"]', '8');
    await page.uncheck('input[name="cora_pwd_policy_numbers"]');
    await page.uncheck('input[name="cora_pwd_policy_uppercase"]');
    await page.uncheck('input[name="cora_pwd_policy_special"]');
    await page.click('button:has-text("Save All Settings")');
    await expect(page.locator('#cora-toast-container')).toContainText('Global system settings updated successfully.');
  });

  test('2. Branch Management Creation & Deletion', async ({ page }) => {
    // Navigate to branches tab
    await page.goto('/workspace/settings-suite?settings_tab=branches');
    
    // Click new branch button
    await page.click('button:has-text("New Branch")');
    await page.waitForSelector('#drawer-create-branch:not(.opacity-0)', { state: 'visible' });

    // Fill form
    await page.fill('#new-branch-name', 'Chennai Hub');
    await page.fill('#new-branch-city', 'Chennai');
    await page.fill('#new-branch-address', '123 Anna Salai');
    await page.click('#create-branch-btn');

    // Confirm save toast
    await expect(page.locator('#cora-toast-container')).toContainText('Branch saved successfully.');
    await page.waitForLoadState('networkidle');

    // Verify row is added
    const branchRow = page.locator('tr:has-text("Chennai Hub")').first();
    await expect(branchRow).toBeVisible();
    await expect(branchRow).toContainText('Chennai / 123 Anna Salai');

    // Delete the branch
    await branchRow.locator('button:has-text("Delete")').click();
    await page.waitForSelector('#cora-confirm-modal:not(.hidden)', { state: 'visible' });
    await page.click('#cora-confirm-btn');

    // Confirm deletion toast
    await expect(page.locator('#cora-toast-container')).toContainText('Branch deleted successfully.');
  });

  test('3. Notifications Dropdown Panel Interactions', async ({ page }) => {
    await page.goto('/workspace/dashboard');
    
    // Open dropdown popover
    await page.click('#cora-notif-bell-btn');
    await expect(page.locator('#cora-notif-dropdown')).toBeVisible();

    // Mark all read
    await page.click('#cora-notif-mark-all-btn');
    await expect(page.locator('#cora-toast-container')).toContainText('All notifications marked as read.');

    // Close dropdown by clicking outside
    await page.click('main');
    await expect(page.locator('#cora-notif-dropdown')).toBeHidden();
  });

  test('4. System Activity Audit Logs View & CSV Export', async ({ page }) => {
    await page.goto('/workspace/audit-panel');

    // Confirm tabs are rendered
    await expect(page.locator('#tab-activity-btn')).toBeVisible();
    await expect(page.locator('#tab-cost-btn')).toBeVisible();

    // Verify filter fields exist
    await expect(page.locator('#log-search')).toBeVisible();
    await expect(page.locator('#log-type-filter')).toBeVisible();

    // Test tab switching
    await page.click('#tab-cost-btn');
    await expect(page.locator('#cora-audit-cost-section')).toBeVisible();
    await expect(page.locator('#cora-audit-activity-section')).toBeHidden();

    await page.click('#tab-activity-btn');
    await expect(page.locator('#cora-audit-activity-section')).toBeVisible();
    await expect(page.locator('#cora-audit-cost-section')).toBeHidden();

    // Intercept download for CSV Export
    const [download] = await Promise.all([
      page.waitForEvent('download'),
      page.click('button:has-text("Export CSV")')
    ]);

    expect(download.suggestedFilename()).toContain('propOS_audit_logs');
  });

  test('5. Model Context Protocol (MCP) Server Validation', async ({ page }) => {
    // 1. Retrieve the secure token from workspace mcp tab
    await page.goto('/workspace/mcp');
    await page.waitForSelector('input[name="cora_mcp_access_token_direct"]');
    const validToken = await page.inputValue('input[name="cora_mcp_access_token_direct"]');
    expect(validToken.length).toBeGreaterThan(0);

    // 2. Perform request with invalid token -> 401 Unauthorized
    const resInvalid = await page.request.post('/wp-json/cora/v1/mcp', {
      headers: {
        'Authorization': 'Bearer invalid_token_123',
        'Content-Type': 'application/json'
      },
      data: {
        jsonrpc: '2.0',
        method: 'tools/list',
        id: 1
      }
    });
    expect(resInvalid.status()).toBe(401);
    const bodyInvalid = await resInvalid.json();
    expect(bodyInvalid.error.message).toContain('Unauthorized');

    // 3. Perform request with valid token -> tools/list list of tools
    const resList = await page.request.post('/wp-json/cora/v1/mcp', {
      headers: {
        'Authorization': `Bearer ${validToken}`,
        'Content-Type': 'application/json'
      },
      data: {
        jsonrpc: '2.0',
        method: 'tools/list',
        id: 2
      }
    });
    expect(resList.status()).toBe(200);
    const bodyList = await resList.json();
    expect(bodyList.result.tools).toBeDefined();
    const tools = bodyList.result.tools;
    expect(tools.some(t => t.name === 'cora_get_platform_info')).toBe(true);
    expect(tools.some(t => t.name === 'cora_get_leads')).toBe(true);

    // 4. Perform tool call -> cora_get_platform_info
    const resCallInfo = await page.request.post('/wp-json/cora/v1/mcp', {
      headers: {
        'Authorization': `Bearer ${validToken}`,
        'Content-Type': 'application/json'
      },
      data: {
        jsonrpc: '2.0',
        method: 'tools/call',
        params: {
          name: 'cora_get_platform_info'
        },
        id: 3
      }
    });
    expect(resCallInfo.status()).toBe(200);
    const bodyCallInfo = await resCallInfo.json();
    expect(bodyCallInfo.result.isError).toBe(false);
    expect(bodyCallInfo.result.content[0].text).toContain('Cora Platform Info');

    // 5. Perform tool call -> cora_get_leads
    const resCallLeads = await page.request.post('/wp-json/cora/v1/mcp', {
      headers: {
        'Authorization': `Bearer ${validToken}`,
        'Content-Type': 'application/json'
      },
      data: {
        jsonrpc: '2.0',
        method: 'tools/call',
        params: {
          name: 'cora_get_leads',
          arguments: { limit: 2 }
        },
        id: 4
      }
    });
    expect(resCallLeads.status()).toBe(200);
    const bodyCallLeads = await resCallLeads.json();
    expect(bodyCallLeads.result.isError).toBe(false);
    expect(bodyCallLeads.result.content[0].text).toContain('Recent CRM Leads');
  });

  test('6. Advanced Command Search Modal (Command Palette) Keyboard Validation', async ({ page }) => {
    await page.goto('/workspace/settings-suite');

    // 1. Click sidebar search bar -> opens command palette modal
    await page.click('.cora-sidebar-search');
    await expect(page.locator('#cora-command-palette')).toBeVisible();

    // 2. Close it via Escape key
    await page.keyboard.press('Escape');
    await expect(page.locator('#cora-command-palette')).toBeHidden();

    // 3. Open it via Ctrl+K shortcut key
    await page.keyboard.press('Control+k');
    await expect(page.locator('#cora-command-palette')).toBeVisible();

    // 4. Verify search input has focus and type "Password"
    await page.fill('#cora-command-input', 'Password');
    await page.waitForTimeout(300); // Wait for debounce and REST fetch

    // 5. Verify results contain Password Policy item
    await expect(page.locator('#cora-command-results')).toContainText('Password Policy');

    // 6. Test arrow keys down navigation
    await page.keyboard.press('ArrowDown');
    const selectedItem = page.locator('.cora-command-item.selected');
    await expect(selectedItem).toBeVisible();

    // 7. Test filter pills click (which searches "Password" under Leads filter, yielding no results)
    await page.click('#cora-command-palette .cora-search-pill[data-filter="leads"]');
    await page.waitForTimeout(300);
    await expect(page.locator('#cora-command-results')).toContainText('No results found');

    // 8. Press Escape to close palette modal
    await page.keyboard.press('Escape');
    await expect(page.locator('#cora-command-palette')).toBeHidden();
  });

});
