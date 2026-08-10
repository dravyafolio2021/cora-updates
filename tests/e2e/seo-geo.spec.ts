import { test, expect } from '@playwright/test';
import { login } from './helpers';

test.describe('Cora AI Content Suite & GEO Optimization E2E Tests', () => {

  test.beforeEach(async ({ page }) => {
    page.on('console', msg => {
      if (msg.type() === 'error') {
        console.log(`PAGE CONSOLE ERROR: "${msg.text()}"`);
      }
    });
    page.on('pageerror', err => {
      console.log(`PAGE UNHANDLED EXCEPTION: "${err.message}"`);
    });
  });

  test('Should navigate blogs dashboard, test GEO tracking tab, and perform auto-optimization in editor', async ({ page }) => {
    await login(page);

    // 1. Navigate to Content & Blogs
    await page.goto('/workspace/blogs?industry=real_estate');
    await page.waitForSelector('.cora-page-title');
    await expect(page.locator('.cora-page-title')).toContainText('AI Content Suite');

    // 2. Verify Tab Selectors are visible
    await expect(page.locator('#btn-tab-articles-list')).toBeVisible();
    await expect(page.locator('#btn-tab-geo-analytics')).toBeVisible();
    await expect(page.locator('#btn-tab-keywords-explorer')).toBeVisible();

    // 3. Toggle to Generative Search & GEO Tracking tab
    await page.click('#btn-tab-geo-analytics');
    await page.waitForSelector('#cora-blogs-geo-panel', { state: 'visible' });
    await expect(page.locator('#cora-blogs-geo-panel')).toContainText('Google Gemini');
    await expect(page.locator('#cora-blogs-geo-panel')).toContainText('Generative Search Intents & Queries');

    // 4. Toggle to Local Intent Keyword Explorer
    await page.click('#btn-tab-keywords-explorer');
    await page.waitForSelector('#cora-blogs-keywords-panel', { state: 'visible' });
    await expect(page.locator('#cora-blogs-keywords-panel')).toContainText('Local Search Intents & Query Volume');
    await expect(page.locator('#cora-blogs-keywords-panel')).toContainText('luxury builder floor in Vasant Vihar Delhi');

    // 5. Test One-Click Write Draft
    await page.click('button:has-text("One-Click Write")');
    await page.waitForSelector('#cora-full-page-editor:not(.hidden)', { state: 'visible' });

    // Assert pre-filled fields
    const keywordInput = page.locator('#cora-seo-keyword');
    const titleInput = page.locator('#cora-article-title');
    await expect(keywordInput).toHaveValue('luxury builder floor in Vasant Vihar Delhi');
    await expect(titleInput).toHaveValue('Modern Luxury Builder Floors for Sale in Vasant Vihar');

    // Assert SEO/GEO sidebar tabs are present
    await expect(page.locator('#btn-sidebar-seo')).toBeVisible();
    await expect(page.locator('#btn-sidebar-geo')).toBeVisible();

    // 6. Switch to GEO / AISEO Sidebar Tab
    await page.click('#btn-sidebar-geo');
    await page.waitForSelector('#panel-inspector-copilot:not(.hidden)', { state: 'visible' });

    // Verify initial unchecked optimization checkboxes
    const checkAnswer = page.locator('#chk-geo-direct-answer');
    const checkDensity = page.locator('#chk-geo-info-density');
    expect(await checkAnswer.isChecked()).toBe(false);
    expect(await checkDensity.isChecked()).toBe(false);

    // Verify initial GEO score is 65
    await expect(page.locator('#cora-geo-score-display')).toContainText('65');

    // 7. Inject Property Valuation Form CTA
    await page.evaluate(() => {
      const aside = document.querySelector('#cora-article-inspector');
      if (aside) aside.scrollTop = aside.scrollHeight;
    });
    await page.click('button:has-text("Property Valuation Form")');
    const toast = page.locator('#cora-toast-container');
    await expect(toast).toContainText('In-post lead capture form inserted successfully!');

    // Verify Quill contains form wrapper
    const quillContent = page.locator('#cora-quill-editor');
    await expect(quillContent).toContainText('Get a Free Professional Property Valuation');

    // 8. Click Run GEO Auto-Optimize
    await page.click('button:has-text("Run GEO Auto-Optimize")');
    await expect(toast).toContainText('Generative Engine Optimization (GEO) applied successfully!');

    // Assert checkboxes are now checked
    expect(await checkAnswer.isChecked()).toBe(true);
    expect(await checkDensity.isChecked()).toBe(true);

    // Assert GEO score is updated to 95 and traditional SEO score updated to 92
    await expect(page.locator('#cora-geo-score-display')).toContainText('95');
    await expect(page.locator('#cora-seo-score-display')).toContainText('92');

    // 9. Toggle JSON-LD Schema preview
    await page.click('button:has-text("JSON-LD Schema Preview")', { force: true });
    await page.waitForSelector('#cora-schema-preview-container:not(.hidden)');
    await expect(page.locator('#cora-schema-preview-block')).toContainText('Apex Realty Group');
    await expect(page.locator('#cora-schema-preview-block')).toContainText('Nitin Arora');

    // Close full-page editor
    await page.click('#btn-editor-back');
    await page.waitForSelector('#cora-full-page-editor.hidden', { state: 'hidden' });
  });

  test('Should simulate submitting a blog lead form and verify lead attribution drawer displays it', async ({ page }) => {
    await login(page);
    await page.goto('/workspace/blogs?industry=real_estate');
    await page.waitForSelector('.cora-page-title');

    // Switch to Articles List tab
    await page.click('#btn-tab-articles-list');
    await page.waitForSelector('#panel-ct-library:not(.hidden)', { state: 'visible' });

    // Ensure at least one article exists
    if (await page.locator('#cora-articles-table-body tr.ct-row').count() === 0) {
      await page.click('#btn-tab-keywords-explorer');
      await page.waitForSelector('#cora-blogs-keywords-panel', { state: 'visible' });
      await page.click('button:has-text("One-Click Write")');
      await page.waitForSelector('#cora-full-page-editor:not(.hidden)', { state: 'visible' });
      await page.click('#cora-btn-save-draft');
      await page.goto('/workspace/blogs?industry=real_estate');
      await page.click('#btn-tab-articles-list');
      await page.waitForSelector('#panel-ct-library:not(.hidden)', { state: 'visible' });
    }

    await page.waitForSelector('#cora-articles-table-body tr.ct-row', { state: 'attached' });

    // Dynamically retrieve the first post's ID from its data-post-id attribute
    const firstArticleRow = page.locator('#cora-articles-table-body tr.ct-row').first();
    const postIdStr = await firstArticleRow.getAttribute('data-post-id');
    const postId = postIdStr ? parseInt(postIdStr, 10) : 1;

    // Make an AJAX call in the browser window context to submit a lead generated from this Post ID
    const ajaxResult = await page.evaluate(async (post_id) => {
      const url = (window as any).ajaxurl || '/wp-admin/admin-ajax.php';
      return new Promise((resolve, reject) => {
        jQuery.post(url, {
          action: 'cora_submit_blog_lead',
          post_id: post_id,
          first_name: 'Attributed',
          last_name: 'Lead User',
          email: 'attributed_lead@example.com',
          phone: '+91-99999-77777',
          notes: 'Interested in Golf Course Extension Villa'
        }, function(response) {
          resolve(response);
        }).fail(reject);
      });
    }, postId);

    console.log('DEBUG POST ID:', postId);
    console.log('AJAX RESULT:', ajaxResult);
    expect((ajaxResult as any).success).toBe(true);

    // Reload blogs page to fetch fresh lead counts
    await page.goto('/workspace/blogs?industry=real_estate');
    await page.waitForSelector('.cora-page-title');
    await page.click('#btn-tab-articles-list');
    await page.waitForSelector('#panel-ct-library:not(.hidden)', { state: 'visible' });
    await page.waitForSelector('#cora-articles-table-body tr.ct-row', { state: 'attached' });

    // Verify leads count button is visible on that same row
    const targetRow = page.locator('#cora-articles-table-body tr.ct-row').first();
    const leadCountButton = targetRow.locator('button:has-text("Leads")');
    await expect(leadCountButton).toBeVisible();

    // Click the leads button to slide open the Captured Leads drawer
    await leadCountButton.click();
    await page.waitForSelector('#drawer-article-leads:not(.translate-x-full)', { state: 'visible' });

    // Verify leads details are listed inside the drawer table
    await expect(page.locator('#cora-article-leads-list')).toContainText('Attributed Lead User');
    await expect(page.locator('#cora-article-leads-list')).toContainText('attributed_lead@example.com');
    await expect(page.locator('#cora-article-leads-list')).toContainText('Interested in Golf Course Extension Villa');

    // Close drawer
    await page.click('#drawer-article-leads header button');
    await page.waitForSelector('#drawer-article-leads.translate-x-full', { state: 'attached' });
  });

  test('Should execute collaborative review cycle: assign, submit review, reject with feedback, approve, publish, and verify leaderboard attribution', async ({ page }) => {
    await login(page);
    await page.goto('/workspace/blogs?industry=real_estate');
    await page.waitForSelector('.cora-page-title');

    // 1. Trigger One-Click Write from Keyword Explorer
    await page.click('#btn-tab-keywords-explorer');
    await page.waitForSelector('#cora-blogs-keywords-panel', { state: 'visible' });
    
    // Choose the DLF CyberCity query
    await page.click('tr:has-text("DLF CyberCity") button:has-text("One-Click Write")');
    await page.waitForSelector('#cora-full-page-editor:not(.hidden)', { state: 'visible' });

    // 2. Assign to Shravya (user ID 8)
    await page.selectOption('#cora-article-assignee', '8');

    // 3. Save Draft
    await page.click('button:has-text("Save Draft")');
    const toast = page.locator('#cora-toast-container');
    await expect(toast).toContainText('Article saved successfully!');
    const postId = await page.inputValue('#cora-article-id');

    // Refresh page & switch to Articles List tab
    await page.goto('/workspace/blogs?industry=real_estate');
    await page.waitForSelector('.cora-page-title');
    await page.click('#btn-tab-articles-list');
    await page.waitForSelector('#panel-ct-library:not(.hidden)', { state: 'visible' });
    await page.waitForSelector('#cora-articles-table-body tr.ct-row', { state: 'attached' });
    const targetRow = page.locator(`tr[data-post-id="${postId}"]`);
    const firstRowAuthor = targetRow.locator('td:nth-child(2) span');
    await expect(firstRowAuthor).toContainText('Shravya');

    const firstRowStatus = targetRow.locator('td:nth-child(3) span');
    await expect(firstRowStatus).toContainText('Draft');

    // 4. Edit Article again
    await targetRow.locator('button:has-text("Edit Article")').click({ force: true });
    await page.waitForSelector('#cora-full-page-editor:not(.hidden)', { state: 'visible' });
    await page.waitForFunction(() => document.querySelector('#cora-editor-status')?.textContent !== 'Loading...');
    await expect(page.locator('#cora-article-title')).toHaveValue('Corporate Commercial Lease Space Rates inside DLF CyberCity Gurgaon');

    // 5. Submit for Review
    await page.click('#cora-btn-submit-review');
    await expect(toast).toContainText('Article submitted for review successfully!');

    // Wait for reload & switch to Articles List tab
    await page.waitForTimeout(1200);
    await page.goto('/workspace/blogs?industry=real_estate');
    await page.waitForSelector('.cora-page-title');
    await page.click('#btn-tab-articles-list');
    await page.waitForSelector('#panel-ct-library:not(.hidden)', { state: 'visible' });
    await page.waitForSelector('#cora-articles-table-body tr.ct-row', { state: 'attached' });
    await expect(firstRowStatus).toContainText('In Review');

    // 6. Edit again & verify review banner shows up
    await targetRow.locator('button:has-text("Edit Article")').click({ force: true });
    await page.waitForSelector('#cora-full-page-editor:not(.hidden)', { state: 'visible' });
    await page.waitForFunction(() => document.querySelector('#cora-editor-status')?.textContent !== 'Loading...');
    await expect(page.locator('#cora-article-title')).toHaveValue('Corporate Commercial Lease Space Rates inside DLF CyberCity Gurgaon');
    await expect(page.locator('#cora-editorial-banner')).toBeVisible();
    await expect(page.locator('#cora-editorial-banner-status')).toContainText('Draft Pending Review');
    await expect(page.locator('#cora-editorial-banner-author')).toContainText('Shravya');

    // 7. Request revisions (reject)
    await page.click('#cora-editorial-banner button:has-text("Request Revisions")');
    await page.waitForSelector('#cora-feedback-input-container:not(.hidden)', { state: 'visible' });
    await page.fill('#cora-feedback-input-field', 'Need Vasant Vihar stats');
    await page.click('#cora-feedback-input-container button:has-text("Submit Feedback")');
    await expect(toast).toContainText('Revisions requested successfully!');

    // Wait for reload & switch to Articles List tab
    await page.waitForTimeout(1200);
    await page.goto('/workspace/blogs?industry=real_estate');
    await page.waitForSelector('.cora-page-title');
    await page.click('#btn-tab-articles-list');
    await page.waitForSelector('#panel-ct-library:not(.hidden)', { state: 'visible' });
    await page.waitForSelector('#cora-articles-table-body tr.ct-row', { state: 'attached' });
    await expect(firstRowStatus).toContainText('Draft');

    // Edit again to assert feedback message is in the sidebar
    await targetRow.locator('button:has-text("Edit Article")').click({ force: true });
    await page.waitForSelector('#cora-full-page-editor:not(.hidden)', { state: 'visible' });
    await page.waitForFunction(() => document.querySelector('#cora-editor-status')?.textContent !== 'Loading...');
    await expect(page.locator('#cora-article-title')).toHaveValue('Corporate Commercial Lease Space Rates inside DLF CyberCity Gurgaon');
    await page.click('#btn-sidebar-meta', { force: true });
    await expect(page.locator('#panel-inspector-meta')).not.toHaveClass(/hidden/);
    await expect(page.locator('#cora-editorial-feedback-box')).toBeVisible();
    await expect(page.locator('#cora-editorial-feedback-text')).toContainText('Need Vasant Vihar stats');

    // 8. Submit for review again
    await page.click('#cora-btn-submit-review');
    await expect(toast).toContainText('Article submitted for review successfully!');
    await page.waitForTimeout(1200);
    await page.goto('/workspace/blogs?industry=real_estate');
    await page.waitForSelector('.cora-page-title');
    await page.click('#btn-tab-articles-list');
    await page.waitForSelector('#panel-ct-library:not(.hidden)', { state: 'visible' });
    await page.waitForSelector('#cora-articles-table-body tr.ct-row', { state: 'attached' });

    // Edit to approve
    await targetRow.locator('button:has-text("Edit Article")').click({ force: true });
    await page.waitForSelector('#cora-full-page-editor:not(.hidden)', { state: 'visible' });
    await page.waitForFunction(() => document.querySelector('#cora-editor-status')?.textContent !== 'Loading...');
    await expect(page.locator('#cora-article-title')).toHaveValue('Corporate Commercial Lease Space Rates inside DLF CyberCity Gurgaon');
    await page.click('button:has-text("Approve Draft")');
    await expect(toast).toContainText('Article approved successfully!');

    // Wait for reload & switch to Articles List tab
    await page.waitForTimeout(1200);
    await page.goto('/workspace/blogs?industry=real_estate');
    await page.waitForSelector('.cora-page-title');
    await page.click('#btn-tab-articles-list');
    await page.waitForSelector('#panel-ct-library:not(.hidden)', { state: 'visible' });
    await page.waitForSelector('#cora-articles-table-body tr.ct-row', { state: 'attached' });
    await expect(firstRowStatus).toContainText('Approved');

    // 9. Publish Live
    await targetRow.locator('button:has-text("Edit Article")').click({ force: true });
    await page.waitForSelector('#cora-full-page-editor:not(.hidden)', { state: 'visible' });
    await page.waitForFunction(() => document.querySelector('#cora-editor-status')?.textContent !== 'Loading...');
    await expect(page.locator('#cora-article-title')).toHaveValue('Corporate Commercial Lease Space Rates inside DLF CyberCity Gurgaon');
    await page.click('button:has-text("Publish Live")');
    await expect(toast).toContainText('Article published successfully!');

    // Wait for reload & switch to Articles List tab
    await page.waitForTimeout(1200);
    await page.goto('/workspace/blogs?industry=real_estate');
    await page.waitForSelector('.cora-page-title');
    await page.click('#btn-tab-articles-list');
    await page.waitForSelector('#panel-ct-library:not(.hidden)', { state: 'visible' });
    await page.waitForSelector('#cora-articles-table-body tr.ct-row', { state: 'attached' });
    await expect(firstRowStatus).toContainText('Published');

    // 10. Switch to Generative Tracking and verify Shravya is on the leaderboard
    await page.click('#btn-tab-geo-analytics');
    await page.waitForSelector('#cora-blogs-geo-panel', { state: 'visible' });
    await expect(page.locator('#cora-blogs-geo-panel')).toContainText('Shravya');
    await expect(page.locator('#cora-blogs-geo-panel')).toContainText('Leads');
  });

});
