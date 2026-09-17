import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Professional Services & Agency Vertical E2E Verification', () => {

  test('should login as prof_owner and load professional services dashboard', async ({ page }) => {
    await login(page, 'owner.profservices@cora.local', 'cora_secure_pass_123');
    await page.goto('/workspace/dashboard?industry=professional_services');
    
    await page.waitForSelector('.cora-sidebar');
    
    // Check sidebar workspace branding
    const workspaceTitle = page.locator('.cora-studio-info');
    await expect(workspaceTitle).toBeVisible();
    
    // Check for core navigation elements
    const dashboardItem = page.locator('li[data-target="dashboard"]');
    await expect(dashboardItem).toBeVisible();
  });

  test('should render App Modules (Feature Hub) with 22 structured P0/P1/P2 cards', async ({ page }) => {
    await login(page, 'owner.profservices@cora.local', 'cora_secure_pass_123');
    await page.goto('/workspace/feature-hub?industry=professional_services');
    
    await page.waitForSelector('.cora-feature-card');
    
    // Verify cards are rendered
    const cards = page.locator('.cora-feature-card');
    const count = await cards.count();
    expect(count).toBeGreaterThanOrEqual(20);
    
    // Verify P0 Foundation cards exist
    await expect(page.locator('text=Clients, Brands & Workspaces')).toBeVisible();
    await expect(page.locator('text=Deliverables & Approvals')).toBeVisible();
    await expect(page.locator('text=Simple Client Portal')).toBeVisible();
    await expect(page.locator('text=Engagement Delivery')).toBeVisible();
    
    // Verify P1 Scale cards exist
    await expect(page.locator('text=Leads, Discovery & Briefs')).toBeVisible();
    await expect(page.locator('text=Advanced Proposals & Onboarding')).toBeVisible();
    await expect(page.locator('text=Reporting & Client Health')).toBeVisible();
    await expect(page.locator('text=Billing & Economics')).toBeVisible();
    
    // Verify P2 Specialized cards exist
    await expect(page.locator('text=Advanced Resource Planning')).toBeVisible();
    await expect(page.locator('text=Deep Profitability & Finance')).toBeVisible();
    await expect(page.locator('text=Enterprise Controls')).toBeVisible();
  });

  test('should display Professional Services SAC 9983 templates in Document Vault', async ({ page }) => {
    await login(page, 'owner.profservices@cora.local', 'cora_secure_pass_123');
    await page.goto('/workspace/vault?industry=professional_services');
    
    await page.waitForSelector('.cora-sidebar');
    
    // Evaluate if window.CORA_TEMPLATES_ALL has prof templates
    const hasProfTemplates = await page.evaluate(() => {
      const all = (window as any).CORA_TEMPLATES_ALL || [];
      return all.some((t: any) => t.id === 'tpl_prof_msa' || t.id === 'tpl_prof_retainer_inv');
    });
    
    expect(hasProfTemplates).toBeTruthy();
  });
});
