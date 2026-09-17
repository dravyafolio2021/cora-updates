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
});
