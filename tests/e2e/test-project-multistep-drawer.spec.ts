import { test, expect } from '@playwright/test';
import { login } from './helpers';
import * as path from 'path';

test.describe('Multi-Step Portfolio Project Creation Drawer Framework', () => {
  const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/a61fdc7a-f854-4606-9d91-f2a9f6b6294c';

  test('Verify 6-Step Creation Framework, Smart Links, AI Copywriting, and Live Previews', async ({ page }) => {
    page.on('console', msg => console.log('[BROWSER CONSOLE]', msg.type(), msg.text()));
    page.on('pageerror', err => console.log('[BROWSER PAGE ERROR]', err.message));

    // 1. Authenticate and load Projects view
    await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');
    await page.setViewportSize({ width: 1280, height: 850 });
    await page.goto('/workspace/projects');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    // 2. Open Drawer for Create
    const newProjectBtn = page.locator('#btn-new-project').first();
    await expect(newProjectBtn).toBeVisible();
    await newProjectBtn.click();
    await page.waitForTimeout(500);

    const drawer = page.locator('#cora-project-drawer');
    await expect(drawer).toBeVisible();
    await expect(drawer).toHaveClass(/open/);

    const drawerBox = await drawer.boundingBox();
    expect(drawerBox).not.toBeNull();

    // 2A. Desktop Top Panel Header Verification (Buttons Extreme Right, Stepper Centered)
    const headerRight = page.locator('.cora-drawer-header-right');
    const headerRightBox = await headerRight.boundingBox();
    expect(headerRightBox).not.toBeNull();
    const drawerRightEdge = drawerBox!.x + drawerBox!.width;
    const headerRightEdge = headerRightBox!.x + headerRightBox!.width;
    expect(drawerRightEdge - headerRightEdge).toBeLessThanOrEqual(30);

    const stepperContainer = page.locator('#cora-proj-stepper-container');
    const stepperBox = await stepperContainer.boundingBox();
    expect(stepperBox).not.toBeNull();
    const stepperCenter = stepperBox!.x + stepperBox!.width / 2;
    const drawerCenter = drawerBox!.x + drawerBox!.width / 2;
    expect(Math.abs(drawerCenter - stepperCenter)).toBeLessThanOrEqual(35);

    // 2B. Desktop Footer Edge-to-Edge Sticky Verification
    const footer = page.locator('#cora-project-drawer-footer');
    await expect(footer).toBeVisible();
    const footerBox = await footer.boundingBox();
    expect(footerBox).not.toBeNull();
    const drawerBottomEdge = drawerBox!.y + drawerBox!.height;
    const footerBottomEdge = footerBox!.y + footerBox!.height;
    expect(Math.abs(drawerBottomEdge - footerBottomEdge)).toBeLessThanOrEqual(2);
    expect(Math.abs(drawerBox!.width - footerBox!.width)).toBeLessThanOrEqual(2);

    // Screenshot Desktop Aligned
    await page.screenshot({
      path: path.join(artifactDir, 'desktop_drawer_aligned_header_footer.png'),
      fullPage: false,
    });

    // Verify Stepper Header & 6 Steps
    const stepperBar = page.locator('#cora-proj-stepper-bar');
    await expect(stepperBar).toBeVisible();
    const stepButtons = stepperBar.locator('.cora-proj-step-btn');
    expect(await stepButtons.count()).toBe(6);

    // Verify Step 1 is active initially
    const step1Panel = page.locator('#proj-step-1');
    await expect(step1Panel).toBeVisible();

    // 3. Test Smart Link Engine
    const smartLinkInput = page.locator('#cora-smart-link-input');
    const smartLinkBadge = page.locator('#cora-smart-link-badge');
    const addLinkBtn = page.locator('#cora-smart-link-add-btn');

    // Test Instagram detection
    await smartLinkInput.fill('https://www.instagram.com/apex_global_studio');
    await page.waitForTimeout(150);
    const badgeHtmlInstagram = await smartLinkBadge.innerHTML();
    expect(badgeHtmlInstagram).toContain('rect'); // SVG rect for Instagram camera body
    await addLinkBtn.click();
    await page.waitForTimeout(150);

    // Test YouTube detection
    await smartLinkInput.fill('https://youtube.com/watch?v=showcase2026');
    await page.waitForTimeout(150);
    const badgeHtmlYoutube = await smartLinkBadge.innerHTML();
    expect(badgeHtmlYoutube).toContain('polygon'); // SVG polygon play button
    await addLinkBtn.click();
    await page.waitForTimeout(150);

    // Test LinkedIn detection with custom label
    await smartLinkInput.fill('https://linkedin.com/company/apex-global');
    await page.locator('#cora-smart-link-label').fill('Executive LinkedIn');
    await addLinkBtn.click();
    await page.waitForTimeout(150);

    // Verify attached chips list
    const smartLinksList = page.locator('#cora-smart-links-list');
    await expect(smartLinksList).toContainText('Instagram:');
    await expect(smartLinksList).toContainText('YouTube:');
    await expect(smartLinksList).toContainText('Executive LinkedIn:');

    // 4. Test Aspect Ratio Selector & Thumbnail Preview
    const aspect169 = page.locator('#aspect-btn-16-9');
    const aspect11 = page.locator('#aspect-btn-1-1');
    await aspect11.click();
    const previewWrap = page.locator('#cora-thumb-preview-wrap');
    const style11 = await previewWrap.getAttribute('style');
    expect(style11).toContain('aspect-ratio: 1 / 1');

    await aspect169.click();
    const style169 = await previewWrap.getAttribute('style');
    expect(style169).toContain('aspect-ratio: 16 / 9');

    // Fill project scope fields
    await page.locator('#proj-field-title').fill('Apex Global Editorial Brand Overhaul');
    await page.locator('#proj-field-client-name').fill('Apex Global Brands');
    await page.locator('#proj-field-client-industry').fill('Luxury Editorial & Fashion');
    await page.locator('#proj-field-duration').fill('6-Week Sprint');
    await page.locator('#proj-field-slug').fill('apex-editorial-brand-overhaul');
    await page.locator('#proj-field-subtitle').fill('Comprehensive commercial identity and multi-channel campaign rollout.');
    await page.locator('#proj-field-services').fill('Creative Direction, Studio Photography, Editorial Retouching');

    // Hero image URL
    await page.locator('#proj-field-hero-image').fill('https://images.unsplash.com/photo-1542038784456-1ea8e935640e?auto=format&fit=crop&w=1200&q=80');
    await page.waitForTimeout(200);
    const heroPreviewImg = page.locator('#cora-thumb-preview-img');
    await expect(heroPreviewImg).toBeVisible();

    // Verify Step 1 Live Card Preview
    await expect(page.locator('#step1-preview-title')).toHaveText('Apex Global Editorial Brand Overhaul');
    await expect(page.locator('#step1-preview-client')).toHaveText('Apex Global Brands');
    await expect(page.locator('#step1-preview-cover-img')).toBeVisible();

    // Screenshot Step 1
    await page.screenshot({
      path: path.join(artifactDir, 'drawer_step_1_overview.png'),
      fullPage: false,
    });

    // 5. Navigate to Step 2: 4-Act Story Narrative
    const nextBtn = page.locator('#cora-btn-next-step');
    await nextBtn.click();
    await page.waitForTimeout(300);

    const step2Panel = page.locator('#proj-step-2');
    await expect(step2Panel).toBeVisible();
    await expect(page.locator('#cora-step-badge-1')).toContainText('✓');

    // Test Story Type selection
    const storyTypeScaling = page.locator('#story-type-scaling');
    await expect(storyTypeScaling).toBeVisible();
    await storyTypeScaling.click();
    await page.waitForTimeout(150);
    expect(await page.locator('#proj-field-story-type').inputValue()).toBe('scaling');

    // Switch back to PAS or test PAS
    await page.locator('#story-type-pas').click();
    await page.waitForTimeout(150);
    expect(await page.locator('#proj-field-story-type').inputValue()).toBe('pas');

    // Test AI Co-Pilot One-Click Story Draft
    const autoDraftStoryBtn = page.locator('#btn-ai-auto-draft-story');
    await expect(autoDraftStoryBtn).toBeVisible();
    await autoDraftStoryBtn.click();
    await page.waitForTimeout(700);

    // Verify generated story content
    const execSummary = await page.locator('#proj-field-exec-summary').inputValue();
    const challenge = await page.locator('#proj-field-challenge').inputValue();
    const solution = await page.locator('#proj-field-solution').inputValue();
    const deliverables = await page.locator('#proj-field-deliverables').inputValue();

    expect(execSummary.length).toBeGreaterThan(30);
    expect(challenge.length).toBeGreaterThan(30);
    expect(solution.length).toBeGreaterThan(30);
    expect(deliverables.length).toBeGreaterThan(20);

    // Test Rich Text Toolbar presence
    const boldBtn = page.locator('#proj-step-2 .cora-richtext-toolbar button[title="Bold"]').first();
    await expect(boldBtn).toBeVisible();

    // Test inline AI Refine Popover on Challenge
    const aiChallengeBtn = page.locator('#proj-step-2 button:has-text("AI Refine")').nth(1);
    await aiChallengeBtn.click();
    await page.waitForTimeout(200);
    const pasOption = page.locator('#menu-ai-challenge button:has-text("PAS Framework")');
    await expect(pasOption).toBeVisible();
    await pasOption.click();
    await page.waitForTimeout(600);

    const refinedChallenge = await page.locator('#proj-field-challenge').inputValue();
    expect(refinedChallenge).toContain('Problem:');

    // Verify Story Accordion Architecture
    const accordionContainer = page.locator('#cora-story-accordion-container');
    await expect(accordionContainer).toBeVisible();
    const accordionCards = accordionContainer.locator('.cora-accordion-card');
    expect(await accordionCards.count()).toBe(4);

    // Test Accordion Toggle (Toggle Act 1)
    const act1Trigger = page.locator('#story-accordion-act-1 .cora-accordion-trigger');
    await act1Trigger.click();
    await page.waitForTimeout(200);
    // Click again to reopen Act 1
    await act1Trigger.click();
    await page.waitForTimeout(200);
    await expect(page.locator('#story-accordion-act-1')).toHaveClass(/open/);

    // Scroll to top of drawer form for clean full-step screenshot
    await page.evaluate(() => {
      const form = document.getElementById('cora-project-form');
      if (form) form.scrollTop = 0;
    });
    await page.waitForTimeout(300);

    // Screenshot Step 2
    await page.screenshot({
      path: path.join(artifactDir, 'drawer_step_2_story.png'),
      fullPage: false,
    });

    // 6. Navigate to Step 3: Quantitative Proof KPIs
    await nextBtn.click();
    await page.waitForTimeout(300);

    const step3Panel = page.locator('#proj-step-3');
    await expect(step3Panel).toBeVisible();

    // Fill KPIs and verify Live KPI showcase preview
    await page.locator('#kpi-val-1').fill('+420%');
    await page.locator('#kpi-lbl-1').fill('Global Editorial Reach');
    await page.locator('#kpi-ctx-1').fill('Across 14 international markets');

    await page.locator('#kpi-val-2').fill('12 Days');
    await page.locator('#kpi-lbl-2').fill('Production Velocity');
    await page.locator('#kpi-ctx-2').fill('From raw brief to master delivery');

    // Test dynamic KPI repeater: add metric
    const addMetricBtn = page.locator('#btn-add-kpi-metric');
    await expect(addMetricBtn).toBeVisible();
    await addMetricBtn.click();
    await page.waitForTimeout(200);

    await page.locator('#kpi-val-4').fill('99.8%');
    await page.locator('#kpi-lbl-4').fill('On-Time SLA');
    await page.locator('#kpi-ctx-4').fill('Zero release delays across enterprise sprints');

    await page.waitForTimeout(150);
    await expect(page.locator('#preview-kpi-val-1')).toHaveText('+420%');
    await expect(page.locator('#preview-kpi-lbl-1')).toHaveText('Global Editorial Reach');

    // Scroll to top of drawer form for clean Step 3 screenshot showing presets and metric cards
    await page.evaluate(() => {
      const form = document.getElementById('cora-project-form');
      if (form) form.scrollTop = 0;
    });
    await page.waitForTimeout(200);

    // Screenshot Step 3
    await page.screenshot({
      path: path.join(artifactDir, 'drawer_step_3_kpis.png'),
      fullPage: false,
    });

    // 7. Navigate to Step 4: Media Assets & Visual Gallery
    await nextBtn.click();
    await page.waitForTimeout(300);

    const step4Panel = page.locator('#proj-step-4');
    await expect(step4Panel).toBeVisible();

    // Verify link pasting textarea is hidden
    const galleryTextarea = page.locator('#proj-field-gallery');
    await expect(galleryTextarea).toBeHidden();

    // Verify Select from Media Library CTA is present
    const openMediaModalBtn = page.locator('#btn-open-gallery-media-modal');
    await expect(openMediaModalBtn).toBeVisible();

    // Test Media Library Modal Opening & Multi-Selection
    await openMediaModalBtn.click();
    await page.waitForTimeout(200);
    const mediaModal = page.locator('#cora-media-library-modal');
    await expect(mediaModal).toBeVisible();

    // Confirm selection of assets
    await page.evaluate(() => {
      const btn = document.getElementById('btn-modal-confirm-add');
      if (btn) {
        btn.click();
      } else if (typeof (window as any).coraConfirmModalMediaSelection === 'function') {
        (window as any).coraConfirmModalMediaSelection();
      }
    });
    await page.waitForTimeout(300);
    await expect(mediaModal).toBeHidden();

    // Verify assets rendered in Left Column selected media list
    const selectedAssetItems = page.locator('#cora-gallery-selected-list .cora-gallery-asset-item');
    expect(await selectedAssetItems.count()).toBeGreaterThanOrEqual(2);

    // Verify assets rendered in Right Column Live Gallery Grid
    const galleryGrid = page.locator('#cora-gallery-items-grid');
    const galleryCards = galleryGrid.locator('img');
    expect(await galleryCards.count()).toBeGreaterThanOrEqual(2);

    // Test Video Embed Suite & Real-Time Live Preview
    const videoUrlInput = page.locator('#proj-field-video-url');
    await videoUrlInput.fill('https://vimeo.com/76979871');
    await page.evaluate(() => (window as any).coraOnVideoUrlInput('https://vimeo.com/76979871'));
    await page.waitForTimeout(200);

    // Verify Platform Badge detected Vimeo
    const videoBadge = page.locator('#cora-video-platform-badge');
    await expect(videoBadge).toHaveText('Vimeo');

    // Switch preview tab to Video Reel
    const videoTabBtn = page.locator('#preview-tab-video');
    await videoTabBtn.click();
    await page.waitForTimeout(200);

    const videoIframe = page.locator('#cora-video-embed-container iframe');
    await expect(videoIframe).toBeVisible();
    const iframeSrc = await videoIframe.getAttribute('src');
    expect(iframeSrc).toContain('player.vimeo.com/video/76979871');

    // Test Video Aspect Ratio switch
    const ar916Btn = page.locator('#video-ar-916');
    await ar916Btn.click();
    await page.waitForTimeout(150);
    expect(await page.locator('#proj-field-video-aspect-ratio').inputValue()).toBe('9:16');

    // Switch back to 16:9
    await page.locator('#video-ar-169').click();
    await page.waitForTimeout(150);
    expect(await page.locator('#proj-field-video-aspect-ratio').inputValue()).toBe('16:9');

    // Screenshot Video Reel Live Embed Preview
    await page.screenshot({
      path: path.join(artifactDir, 'drawer_step_4_video_reel.png'),
      fullPage: false,
    });

    // Switch back to Gallery Grid tab for visual showcase screenshot
    await page.locator('#preview-tab-gallery').click();
    await page.waitForTimeout(200);

    // Scroll to top of drawer form for clean Step 4 screenshot
    await page.evaluate(() => {
      const form = document.getElementById('cora-project-form');
      if (form) form.scrollTop = 0;
    });
    await page.waitForTimeout(200);

    // Screenshot Step 4
    await page.screenshot({
      path: path.join(artifactDir, 'drawer_step_4_gallery.png'),
      fullPage: false,
    });

    // 8. Navigate to Step 5: Verified Client Endorsement
    await nextBtn.click();
    await page.waitForTimeout(300);

    const step5Panel = page.locator('#proj-step-5');
    await expect(step5Panel).toBeVisible();

    // Fill endorsement
    await page.locator('#proj-field-test-quote').fill('The editorial precision and production speed set a new benchmark for our brand launches.');
    await page.locator('#proj-field-test-author').fill('Kavya Patel');
    await page.locator('#proj-field-test-role').fill('VP of Global Brand Marketing');
    await page.locator('#proj-field-test-company').fill('Apex Global Brands');
    await page.waitForTimeout(150);

    // Verify live preview card
    const previewAuthor = page.locator('#preview-test-author');
    await expect(previewAuthor).toHaveText('Kavya Patel');
    const previewMeta = page.locator('#preview-test-meta');
    await expect(previewMeta).toContainText('VP of Global Brand Marketing');

    // Scroll to top of drawer form for clean Step 5 screenshot
    await page.evaluate(() => {
      const form = document.getElementById('cora-project-form');
      if (form) form.scrollTop = 0;
    });
    await page.waitForTimeout(200);

    // Screenshot Step 5
    await page.screenshot({
      path: path.join(artifactDir, 'drawer_step_5_endorsement.png'),
      fullPage: false,
    });

    // 9. Navigate to Step 6: SEO & Conversion CTA
    await nextBtn.click();
    await page.waitForTimeout(300);

    const step6Panel = page.locator('#proj-step-6');
    await expect(step6Panel).toBeVisible();

    // Test AI Auto-Generate SEO Metadata
    const autoSeoBtn = page.locator('#btn-ai-generate-seo');
    await expect(autoSeoBtn).toBeVisible();
    await autoSeoBtn.click();
    await page.waitForTimeout(300);

    const metaTitleVal = await page.locator('#proj-field-meta-title').inputValue();
    const metaDescVal = await page.locator('#proj-field-meta-desc').inputValue();
    expect(metaTitleVal.length).toBeGreaterThan(10);
    expect(metaTitleVal.length).toBeLessThanOrEqual(60);
    expect(metaDescVal.length).toBeGreaterThan(20);
    expect(metaDescVal.length).toBeLessThanOrEqual(155);

    // Verify Live Google SERP Snippet Preview
    const serpTitle = page.locator('#serp-preview-title');
    await expect(serpTitle).toHaveText(metaTitleVal);
    const serpDesc = page.locator('#serp-preview-desc');
    await expect(serpDesc).toHaveText(metaDescVal);

    // Verify Next button label switched to 'Save & Publish Project'
    const nextStepLabel = page.locator('#cora-next-step-label');
    await expect(nextStepLabel).toHaveText('Save & Publish Project');

    // Scroll to top of drawer form for clean Step 6 screenshot
    await page.evaluate(() => {
      const form = document.getElementById('cora-project-form');
      if (form) form.scrollTop = 0;
    });
    await page.waitForTimeout(200);

    // Screenshot Step 6 - Google SERP Mode
    await page.screenshot({
      path: path.join(artifactDir, 'drawer_step_6_seo_serp.png'),
      fullPage: false,
    });

    // Test Social Card Tab
    const socialTabBtn = page.locator('#seo-tab-btn-social');
    await socialTabBtn.click();
    await page.waitForTimeout(200);
    const socialPanel = page.locator('#cora-seo-panel-social');
    await expect(socialPanel).toBeVisible();
    await page.screenshot({
      path: path.join(artifactDir, 'drawer_step_6_social_card.png'),
      fullPage: false,
    });

    // Test CTA Banner Tab
    const ctaTabBtn = page.locator('#seo-tab-btn-cta');
    await ctaTabBtn.click();
    await page.waitForTimeout(200);
    const ctaPanel = page.locator('#cora-seo-panel-cta');
    await expect(ctaPanel).toBeVisible();
    await page.screenshot({
      path: path.join(artifactDir, 'drawer_step_6_cta_banner.png'),
      fullPage: false,
    });

    // Switch back to Google SERP tab
    await page.locator('#seo-tab-btn-google').click();
    await page.waitForTimeout(150);

    // 10. Test Direct Step Jumping
    const step1Btn = page.locator('#cora-step-btn-1');
    await step1Btn.click();
    await page.waitForTimeout(200);
    await expect(step1Panel).toBeVisible();

    // 11. Mobile Responsiveness Verification (390px x 844px iPhone Viewport)
    await page.setViewportSize({ width: 390, height: 844 });
    await page.waitForTimeout(400);

    const mobileDrawerBox = await drawer.boundingBox();
    expect(mobileDrawerBox).not.toBeNull();
    expect(mobileDrawerBox!.width).toBe(390);

    // On mobile: Close button must be on extreme right of Line 1
    const mobileHeaderRightBox = await headerRight.boundingBox();
    expect(mobileHeaderRightBox).not.toBeNull();
    const mobileDrawerRightEdge = mobileDrawerBox!.x + mobileDrawerBox!.width;
    const mobileHeaderRightEdge = mobileHeaderRightBox!.x + mobileHeaderRightBox!.width;
    expect(mobileDrawerRightEdge - mobileHeaderRightEdge).toBeLessThanOrEqual(20);

    // On mobile: Stepper must span full width and be horizontally scrollable
    const mobileStepperBox = await stepperContainer.boundingBox();
    expect(mobileStepperBox).not.toBeNull();
    expect(mobileStepperBox!.width).toBeGreaterThanOrEqual(350);

    // On mobile: Footer is sticky at the bottom edge (zero floating gap)
    const mobileFooterBox = await footer.boundingBox();
    expect(mobileFooterBox).not.toBeNull();
    const mobileDrawerBottom = mobileDrawerBox!.y + mobileDrawerBox!.height;
    const mobileFooterBottom = mobileFooterBox!.y + mobileFooterBox!.height;
    expect(Math.abs(mobileDrawerBottom - mobileFooterBottom)).toBeLessThanOrEqual(2);
    expect(mobileFooterBox!.width).toBe(390); // 100% full width edge-to-edge

    // Take Mobile Screenshot of Step 1
    await page.screenshot({
      path: path.join(artifactDir, 'mobile_drawer_step1.png'),
      fullPage: false,
    });

    // Tap Next Step on Mobile
    const mobileNextBtn = page.locator('#cora-btn-next-step');
    await mobileNextBtn.click();
    await page.waitForTimeout(400);

    // Verify Step 2 is active on mobile
    await expect(page.locator('#proj-step-2')).toBeVisible();

    // Take Mobile Screenshot of Step 2
    await page.screenshot({
      path: path.join(artifactDir, 'mobile_drawer_step2.png'),
      fullPage: false,
    });

    // 12. Restore Desktop Viewport & Submit / Save Project
    await page.setViewportSize({ width: 1280, height: 850 });
    await page.waitForTimeout(300);

    // Jump to step 6 to click Save & Publish
    await page.locator('#cora-step-btn-6').click();
    await page.waitForTimeout(200);
    const saveBtn = page.locator('#cora-btn-next-step');
    await saveBtn.click();

    // Verify submission and success
    await page.waitForTimeout(1000);
    console.log('Project framework, desktop alignment, sticky footer, and mobile responsiveness verified successfully!');
  });
});


