import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Professional Services & Agency Vertical E2E Verification', () => {

  test('should login as prof_owner and load professional services dashboard', async ({ page }) => {
    await login(page, 'owner.profservices@cora.local', 'cora_secure_pass_123');
    await page.goto('/workspace/dashboard');
    
    await page.waitForSelector('.cora-sidebar');
    
    // Check sidebar workspace branding
    const workspaceTitle = page.locator('.cora-studio-info');
    await expect(workspaceTitle).toBeVisible();
    
    // Check for core navigation elements
    const dashboardItem = page.locator('li[data-target="dashboard"]');
    await expect(dashboardItem).toBeVisible();
  });

  test('should render App Modules (Feature Hub) directly without query parameters', async ({ page }) => {
    await login(page, 'owner.profservices@cora.local', 'cora_secure_pass_123');
    await page.goto('/workspace/feature-hub');
    
    await page.waitForSelector('.cora-feature-card');
    
    // Verify cards are rendered
    const cards = page.locator('.cora-feature-card');
    const count = await cards.count();
    expect(count).toBeGreaterThanOrEqual(20);
    
    // Verify platform module cards across categories exist
    await expect(page.locator('.cora-feature-title:has-text("Content Suite & CMS")')).toBeVisible();
    await expect(page.locator('.cora-feature-title:has-text("Financials & GST Invoicing")')).toBeVisible();
    await expect(page.locator('.cora-feature-title:has-text("User & Role Governance")')).toBeVisible();
    await expect(page.locator('.cora-feature-title:has-text("File & Document Vault")')).toBeVisible();
    await expect(page.locator('.cora-feature-title:has-text("Leads CRM Pipeline")')).toBeVisible();
    await expect(page.locator('.cora-feature-title:has-text("Canvas Website Builder")')).toBeVisible();
    await expect(page.locator('.cora-feature-title:has-text("Google Business Profile")')).toBeVisible();
    await expect(page.locator('.cora-feature-title:has-text("AI Tools MCP Gateway")')).toBeVisible();
    await expect(page.locator('.cora-feature-title:has-text("Agency Setup & Profile")')).toBeVisible();
    await expect(page.locator('.cora-feature-title:has-text("Client & Brand Workspaces")')).toBeVisible();
    await expect(page.locator('.cora-feature-title:has-text("Services, Estimates & Proposals")')).toBeVisible();
    await expect(page.locator('.cora-feature-title:has-text("Contracts & Client Onboarding")')).toBeVisible();
    await expect(page.locator('.cora-feature-title:has-text("Client Portal & Approvals")')).toBeVisible();
    await expect(page.locator('.cora-feature-title:has-text("Client Health & Retention")')).toBeVisible();
    await expect(page.locator('.cora-feature-title:has-text("Operating Economics & Margins")')).toBeVisible();
    await expect(page.locator('.cora-feature-title:has-text("Agency Partner Centre")')).toBeVisible();
    await expect(page.locator('.cora-feature-title:has-text("Media Manager")')).toBeVisible();
    await expect(page.locator('.cora-feature-title:has-text("Property Listings & Showings")')).toBeVisible();
  });

  test('should display Professional Services SAC 9983 templates in Document Vault', async ({ page }) => {
    await login(page, 'owner.profservices@cora.local', 'cora_secure_pass_123');
    await page.goto('/workspace/vault');
    
    await page.waitForSelector('.cora-sidebar');
    
    // Evaluate if window.CORA_TEMPLATES_ALL has prof templates
    const hasProfTemplates = await page.evaluate(() => {
      const all = (window as any).CORA_TEMPLATES_ALL || [];
      return all.some((t: any) => t.id === 'tpl_prof_msa' || t.id === 'tpl_prof_retainer_inv');
    });
    
    expect(hasProfTemplates).toBeTruthy();
  });

  test('should hide disabled module from sidebar navigation', async ({ page }) => {
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
    await page.goto('/workspace/feature-hub');
    await page.waitForSelector('.cora-feature-card');

    // Ensure properties is unchecked
    await page.evaluate(() => {
      const el = document.querySelector('input[name="features[]"][value="properties"]') as HTMLInputElement;
      if (el && el.checked) {
        el.checked = false;
        el.dispatchEvent(new Event('change', { bubbles: true }));
      }
    });
    await page.locator('#cora-fh-save-top-btn').click();
    await page.waitForLoadState('load');
    await page.waitForTimeout(1000);

    // Verify Property Listings is NOT in sidebar
    await page.goto('/workspace/dashboard');
    await page.waitForSelector('.cora-sidebar');
    const propertySidebarItem = page.locator('.cora-sidebar [data-target="properties"], .cora-sidebar [data-target="equipment"]:has-text("Property Listings")');
    await expect(propertySidebarItem).toHaveCount(0);
  });

  test('should render Agency User & Role Governance module with correct tabs and clean agency copy', async ({ page }) => {
    await login(page, 'owner.profservices@cora.local', 'cora_secure_pass_123');
    await page.goto('/workspace/team-roles?industry=professional_services');
    await page.waitForSelector('.cora-sidebar');
    
    // Verify Header Title & Subtitle
    await expect(page.locator('h1, h2, .cora-page-title').filter({ hasText: 'User & Role Governance' })).toBeVisible();
    await expect(page.locator('text=Manage agency partners, consultants, project leads')).toBeVisible();
    
    // Verify Tabs
    await expect(page.locator('.cora-sub-tab[data-target="tab-active-members"]').first()).toBeVisible();
    await expect(page.locator('.cora-sub-tab[data-target="tab-pending-invites"]').first()).toBeVisible();
    await expect(page.locator('.cora-sub-tab[data-target="tab-permissions-matrix"]').first()).toBeVisible();
    await expect(page.locator('.cora-sub-tab[data-target="tab-client-access"]').first()).toBeVisible();
    await expect(page.locator('.cora-sub-tab[data-target="tab-custom-roles"]').first()).toBeVisible();
    
    // Verify vertical clutter (Attendance Logs, Field Ops) is not present
    await expect(page.locator('.cora-sub-tab[data-target="tab-attendance-logs"]')).toHaveCount(0);
    await expect(page.locator('.cora-sub-tab[data-target="tab-field-ops-tracking"]')).toHaveCount(0);
    
    // Verify Table Column
    await expect(page.locator('#tab-active-members th:has-text("Practice / Client Workspace")')).toBeVisible();
  });

  test('should render Client Portals & Access tab with multi-tenant container isolation', async ({ page }) => {
    await login(page, 'owner.profservices@cora.local', 'cora_secure_pass_123');
    await page.goto('/workspace/team-roles?industry=professional_services');
    await page.waitForSelector('.cora-sidebar');
    
    // Switch to Client Portals tab
    await page.locator('.cora-sub-tab[data-target="tab-client-access"]').first().click();
    await expect(page.locator('#tab-client-access')).toBeVisible();
    
    // Verify Client Isolation text & Table
    await expect(page.locator('text=Multi-Tenant Isolation Active')).toBeVisible();
    await expect(page.locator('text=Acme Corporation')).toBeVisible();
    await expect(page.locator('text=TechFlow Innovations')).toBeVisible();
    await expect(page.locator('text=Apex Retail Brands')).toBeVisible();
  });

  test('should render Agency categories in Permissions Matrix', async ({ page }) => {
    await login(page, 'owner.profservices@cora.local', 'cora_secure_pass_123');
    await page.goto('/workspace/team-roles?industry=professional_services');
    await page.waitForSelector('.cora-sidebar');
    
    // Switch to Permissions Matrix tab
    await page.locator('.cora-sub-tab[data-target="tab-permissions-matrix"]').first().click();
    await expect(page.locator('#tab-permissions-matrix')).toBeVisible();
    
    // Verify Matrix Category headers
    await expect(page.locator('#cora-permissions-matrix-table th:has-text("FIRM & PORTAL")')).toBeVisible();
    await expect(page.locator('#cora-permissions-matrix-table th:has-text("GROWTH & PROPOSALS")')).toBeVisible();
    await expect(page.locator('#cora-permissions-matrix-table th:has-text("FINANCE & GOVERNANCE")')).toBeVisible();
  });

  test('should open Tab Customizer drawer on desktop and control tab visibility & reordering', async ({ page }) => {
    await login(page, 'owner.profservices@cora.local', 'cora_secure_pass_123');
    await page.setViewportSize({ width: 1280, height: 800 });
    await page.goto('/workspace/team-roles?industry=professional_services');
    await page.waitForSelector('.cora-sidebar');

    // Desktop Settings button should be visible
    const customizerBtn = page.locator('#btn-open-tab-customizer');
    await expect(customizerBtn).toBeVisible();

    // Open Drawer
    await customizerBtn.click();
    const drawer = page.locator('#cora-customize-tabs-drawer');
    await expect(drawer).toBeVisible();
    await expect(drawer).toHaveClass(/translate-x-0/);

    // Verify Tab Customizer cards are rendered with draggable attribute and 6-dot drag handles
    const firstCard = page.locator('#cora-tab-customizer-list [data-tab-id="tab-active-members"]');
    await expect(firstCard).toBeVisible();
    await expect(firstCard).toHaveAttribute('draggable', 'true');
    await expect(firstCard.locator('.cora-drag-handle')).toBeVisible();
    await expect(firstCard.locator('text=Locked')).toBeVisible();

    // Verify other customizable tabs
    const clientAccessCard = page.locator('#cora-tab-customizer-list [data-tab-id="tab-client-access"]');
    await expect(clientAccessCard).toBeVisible();
    await expect(clientAccessCard).toHaveAttribute('draggable', 'true');

    // Toggle off Client Portals tab in the drawer
    await page.locator('#cora-tab-customizer-list [data-tab-id="tab-client-access"] input[type="checkbox"]').setChecked(false, { force: true });

    // Click Apply Preferences
    await page.locator('#cora-customize-tabs-drawer button:has-text("Apply Preferences")').click();

    // Verify Tab Customizer drawer closes
    await expect(drawer).toHaveClass(/translate-x-full/);

    // Verify Client Portals tab is hidden in the DOM
    const clientAccessTab = page.locator('.cora-sub-tabs-container.hidden.md\\:flex .cora-sub-tab[data-target="tab-client-access"]');
    await expect(clientAccessTab).toBeHidden();

    // Reopen customizer and verify drag handles
    await customizerBtn.click();
    await expect(drawer).toHaveClass(/translate-x-0/);

    // Verify 6-dot drag grip handle exists on cards
    const dragHandles = page.locator('#cora-tab-customizer-list .cora-drag-handle');
    await expect(dragHandles.first()).toBeVisible();

    // Verify arrow buttons are removed
    const moveUpBtns = page.locator('#cora-tab-customizer-list button[title="Move Up"]');
    await expect(moveUpBtns).toHaveCount(0);

    // Reset back to defaults
    await page.locator('#cora-customize-tabs-drawer button:has-text("Reset to Default")').click();
    await expect(clientAccessTab).toBeVisible();
  });

  test('should keep mobile view clean with zero setting icon clutter', async ({ page }) => {
    await login(page, 'owner.profservices@cora.local', 'cora_secure_pass_123');
    await page.setViewportSize({ width: 375, height: 667 });
    await page.goto('/workspace/team-roles?industry=professional_services');
    await page.waitForSelector('.cora-sidebar, .cora-sub-tabs-container');

    // Desktop button should not be visible on mobile screen
    const customizerBtn = page.locator('#btn-open-tab-customizer');
    await expect(customizerBtn).toBeHidden();
  });

  test('should dynamically configure custom roles with active workspace features exclusively for workspace owners', async ({ page }) => {
    await login(page, 'owner.profservices@cora.local', 'cora_secure_pass_123');
    await page.setViewportSize({ width: 1280, height: 800 });
    await page.goto('/workspace/team-roles?industry=professional_services');
    await page.waitForSelector('.cora-sidebar');

    // Switch to Custom Roles Tab
    await page.locator('.cora-sub-tab[data-target="tab-custom-roles"]').first().click();
    await expect(page.locator('#tab-custom-roles')).toBeVisible();

    // Verify Workspace Owner "Create Custom Role" button exists
    const createRoleBtn = page.locator('#tab-custom-roles button:has-text("Create Custom Role")');
    await expect(createRoleBtn).toBeVisible();

    // Open Define Custom Role Drawer
    await createRoleBtn.click();
    const roleDrawer = page.locator('#cora-create-custom-role-drawer');
    await expect(roleDrawer).toBeVisible();
    await expect(roleDrawer).toHaveClass(/translate-x-0/);

    // Verify Owner Action badge
    await expect(roleDrawer.locator('text=Owner Action')).toBeVisible();

    // Verify Dynamic Agency Capabilities are rendered
    await expect(roleDrawer.locator('text=Clients & Engagements')).toBeVisible();
    await expect(roleDrawer.locator('text=Milestones & Deliverables')).toBeVisible();
    await expect(roleDrawer.locator('text=Retainers & SAC 9983 Billing')).toBeVisible();
    await expect(roleDrawer.locator('text=SOW & Contracts Vault')).toBeVisible();
    await expect(roleDrawer.locator('text=Proposals & Landing Pages')).toBeVisible();
    await expect(roleDrawer.locator('text=Firm Knowledge Base & RAG')).toBeVisible();
    await expect(roleDrawer.locator('text=AI Copilots & MCP Tools')).toBeVisible();

    // Verify hardcoded irrelevant vertical checkboxes are NOT present
    await expect(roleDrawer.locator('text=Camera Equipment & Gear')).toHaveCount(0);
    await expect(roleDrawer.locator('text=Shoots & Bookings')).toHaveCount(0);
    await expect(roleDrawer.locator('text=Crew Attendance & Shifts')).toHaveCount(0);

    // Test Base Role Template Dynamic Auto-population
    await roleDrawer.locator('#custom-role-base-template').selectOption('cora_practice_lead');
    
    // Verify Role Display Name is auto-filled
    const roleNameInput = roleDrawer.locator('#custom-role-name');
    await expect(roleNameInput).toHaveValue('Practice Lead / Account Director');

    // Test Quick Select All / Clear All
    await roleDrawer.locator('button:has-text("Clear All")').click();
    const checkedCountAfterClear = await roleDrawer.locator('.custom-role-perm-cb:checked').count();
    expect(checkedCountAfterClear).toBe(0);

    await roleDrawer.locator('button:has-text("Select All")').click();
    const checkedCountAfterSelectAll = await roleDrawer.locator('.custom-role-perm-cb:checked').count();
    expect(checkedCountAfterSelectAll).toBeGreaterThanOrEqual(7);

    // Close Drawer
    await roleDrawer.locator('button:has-text("Cancel")').click();
    await expect(roleDrawer).toHaveClass(/translate-x-full/);
  });
});
