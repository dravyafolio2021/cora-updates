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

  test('should render App Modules (Feature Hub) with 14 foundation cards and scale modules', async ({ page }) => {
    await login(page, 'owner.profservices@cora.local', 'cora_secure_pass_123');
    await page.goto('/workspace/feature-hub?industry=professional_services');
    
    await page.waitForSelector('.cora-feature-card');
    
    // Verify cards are rendered
    const cards = page.locator('.cora-feature-card');
    const count = await cards.count();
    expect(count).toBeGreaterThanOrEqual(20);
    
    // Verify P0 Foundation cards exist
    await expect(page.locator('text=1. Agency Setup & Profile')).toBeVisible();
    await expect(page.locator('text=2. Teams, Roles & Access')).toBeVisible();
    await expect(page.locator('text=3. Client & Brand Management')).toBeVisible();
    await expect(page.locator('text=4. Leads, Discovery & Briefs')).toBeVisible();
    await expect(page.locator('text=5. Services, Estimates & Proposals')).toBeVisible();
    await expect(page.locator('text=6. Contracts & Client Onboarding')).toBeVisible();
    await expect(page.locator('text=7. Projects, Retainers & Campaigns')).toBeVisible();
    await expect(page.locator('text=8. Client Portal & Communication')).toBeVisible();
    await expect(page.locator('text=9. Deliverables, Feedback & Approvals')).toBeVisible();
    await expect(page.locator('text=10. Reporting & Client Health')).toBeVisible();
    await expect(page.locator('text=11. Billing, Collections & Profitability')).toBeVisible();
    await expect(page.locator('text=12. Templates, Knowledge & AI')).toBeVisible();
    await expect(page.locator('text=13. Automations & Notifications')).toBeVisible();
    await expect(page.locator('text=14. Agency Partner Centre')).toBeVisible();
    
    // Verify P1 Scale cards exist
    await expect(page.locator('text=Advanced Proposals & Canvas')).toBeVisible();
    await expect(page.locator('text=Client Health Intelligence')).toBeVisible();
    await expect(page.locator('text=Operating Economics & Margins')).toBeVisible();
    await expect(page.locator('text=Unified Communication Hub')).toBeVisible();
    
    // Verify P2 Specialized cards exist
    await expect(page.locator('text=Performance Marketing Add-on')).toBeVisible();
    await expect(page.locator('text=Resource & Capacity Planning')).toBeVisible();
    await expect(page.locator('text=Enterprise Controls & Multi-Entity')).toBeVisible();
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
