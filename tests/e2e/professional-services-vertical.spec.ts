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
    const propertiesCheckbox = page.locator('input[name="features[]"][value="properties"]');
    if (await propertiesCheckbox.isChecked()) {
      await propertiesCheckbox.uncheck();
      await page.locator('#cora-fh-save-top-btn').click();
      await page.waitForLoadState('networkidle');
    }

    // Verify Property Listings is NOT in sidebar
    await page.goto('/workspace/dashboard');
    await page.waitForSelector('.cora-sidebar');
    const propertySidebarItem = page.locator('.cora-sidebar [data-target="properties"], .cora-sidebar [data-target="equipment"]:has-text("Property Listings")');
    await expect(propertySidebarItem).toHaveCount(0);
  });
});
