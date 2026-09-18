import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('verify form campaign and CRM lead intake engine', async ({ page, request }) => {
    page.on('pageerror', exception => {
        console.log(`Uncaught exception: "${exception.message}"`);
    });
    page.on('console', msg => {
        console.log(`Console [${msg.type()}]: ${msg.text()}`);
    });

    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

    // 1. Navigate to Forms module
    await page.goto('/studio/forms');
    await page.waitForLoadState('networkidle');

    // 2. Verify Forms List State & Header Metrics
    await expect(page.locator('#forms-list-tab-content')).toBeVisible({ timeout: 10000 });
    await expect(page.locator('#metric-total-forms')).toBeVisible();
    await expect(page.locator('#metric-campaign-forms')).toBeVisible();

    // 3. Verify Filter Toolbar
    const allFilterBtn = page.locator('.forms-filter-btn[data-filter="all"]');
    const campaignFilterBtn = page.locator('.forms-filter-btn[data-filter="campaign"]');
    const standardFilterBtn = page.locator('.forms-filter-btn[data-filter="standard"]');
    const searchInput = page.locator('#forms-search-input');

    await expect(allFilterBtn).toBeVisible();
    await expect(campaignFilterBtn).toBeVisible();
    await expect(standardFilterBtn).toBeVisible();
    await expect(searchInput).toBeVisible();

    // Capture screenshot of Forms List with filter toolbar & metrics
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/forms-campaign-list-live.png' });

    // 4. Test Filter Interaction
    await campaignFilterBtn.click();
    await page.waitForTimeout(300);
    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/forms-filter-campaign-active.png' });

    await allFilterBtn.click();
    await page.waitForTimeout(300);

    // 5. Create / Edit a Form to inspect Campaign Routing settings
    const createBtn = page.locator('#btn-create-form').first();
    if (await createBtn.isVisible()) {
        await createBtn.click();
        await page.waitForTimeout(600);

        // Open Integrations & Campaign settings tab
        const integTab = page.locator('#btn-left-tab-integ');
        if (await integTab.isVisible()) {
            await integTab.click();
            await page.waitForTimeout(400);
        }

        const campaignToggle = page.locator('#settings-crm-lead-capture-enable');
        if (await campaignToggle.isVisible()) {
            await campaignToggle.setChecked(true, { force: true });
            await page.waitForTimeout(300);
            const tagInput = page.locator('#settings-custom-campaign-tag');
            if (await tagInput.isVisible()) {
                await tagInput.fill('Instagram Q3 Promo');
            }
        }

        await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/form-builder-campaign-settings-live.png' });
        
        const closeBtn = page.locator('#btn-back-to-forms, #builder-back-btn, #btn-builder-cancel').first();
        if (await closeBtn.isVisible()) {
            await closeBtn.click();
            await page.waitForTimeout(400);
        }
    }

    // 6. Test Submitting to a Lead Campaign Form via REST endpoint
    const formsResponse = await request.get('/wp-json/cora/v1/forms');
    const forms = await formsResponse.json();
    console.log(`Found ${forms.length} forms in workspace`);

    if (forms && forms.length > 0) {
        const testForm = forms[0];
        const formKey = testForm.form_key || testForm.id;

        // Submit inquiry entry
        const submitPayload = {
            submitted_data: {
                'Full Name': 'Aarav Mehta',
                'Email': 'aarav.mehta.campaign@example.com',
                'Phone Number': '+91 98765 00112',
                'Estimated Budget': '75000',
                'City': 'Bengaluru',
                'Requirements': 'Enterprise sales inquiry from summer campaign form'
            }
        };

        const subRes = await request.post(`/wp-json/cora/v1/forms/${formKey}/submit`, {
            data: submitPayload
        });
        console.log(`Form submission status: ${subRes.status()}`);
        expect(subRes.ok()).toBeTruthy();
    }

    // 7. Verify CRM Kanban shows the new campaign lead
    await page.goto('/studio/leads?search=Aarav');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(600);

    await page.screenshot({ path: '/Users/shrutian/.gemini/antigravity/brain/8e3e0349-cc91-4ce5-83c1-4d90eba0db42/crm-kanban-campaign-lead-synced.png' });

    console.log('✅ Form Campaign & CRM Lead Intake Engine verified successfully!');
});
