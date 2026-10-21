import { test, expect } from '@playwright/test';
import { login } from './helpers';

test('Verify Overview and Opportunities Tabs Transformed into Strategic Decision KPIs and Mobile-First Presentation', async ({ page }) => {
  const artifactDir = '/Users/shrutian/.gemini/antigravity/brain/58e87118-a119-4a20-9906-b30972fba7f6';

  // 1. Desktop Test: 1366x850
  await page.setViewportSize({ width: 1366, height: 850 });
  await login(page, 'owner.studio@cora.local', 'cora_secure_pass_123');

  // Navigate to Content Suite Overview Tab
  await page.goto('/workspace/blogs?ct=ct-overview');
  await page.waitForLoadState('domcontentloaded');
  await page.waitForTimeout(1000);

  // ==========================================
  // PART 1: OVERVIEW TAB STRATEGIC DECISION KPIS
  // ==========================================
  const overviewPanel = page.locator('#panel-ct-overview');
  await expect(overviewPanel).toBeVisible();

  // Verify the 5 Strategic Decision KPI Cards
  const kpiGrid = page.locator('#cora-overview-metrics-grid');
  await expect(kpiGrid).toBeVisible();

  // Card 1: Publishing Velocity & Cadence
  const velocityCard = kpiGrid.locator('div:has-text("Publishing Velocity")').first();
  await expect(velocityCard).toBeVisible();
  const velocityText = await velocityCard.innerText();
  console.log('Velocity Card Text:', velocityText.replace(/\n+/g, ' '));
  expect(velocityText).toMatch(/\/\s*\d+\s*Live/i);
  expect(velocityText.toLowerCase()).toContain('weekly target');

  // Card 2: Pipeline Queue & Throughput
  const queueCard = kpiGrid.locator('div:has-text("Pipeline Queue")').first();
  await expect(queueCard).toBeVisible();
  const queueText = await queueCard.innerText();
  console.log('Queue Card Text:', queueText.replace(/\n+/g, ' '));
  expect(queueText).toContain('In Progress');

  // Card 3: AI Search Visibility (GEO Index)
  const geoCard = kpiGrid.locator('div:has-text("AI Search (GEO)")').first();
  await expect(geoCard).toBeVisible();
  const geoText = await geoCard.innerText();
  console.log('GEO Card Text:', geoText.replace(/\n+/g, ' '));
  expect(geoText).toContain('AI Primed');

  // Card 4: Inbound Organic Leads & Content ROI
  const leadsCard = kpiGrid.locator('div:has-text("Inbound Leads")').first();
  await expect(leadsCard).toBeVisible();
  const leadsText = await leadsCard.innerText();
  console.log('Leads Card Text:', leadsText.replace(/\n+/g, ' '));
  expect(leadsText).toContain('Direct Inquiries');

  // Card 5: Niche Pillar Coverage & Topical Depth
  const pillarCard = kpiGrid.locator('div:has-text("Topic Coverage")').first();
  await expect(pillarCard).toBeVisible();
  const pillarText = await pillarCard.innerText();
  console.log('Pillar Card Text:', pillarText.replace(/\n+/g, ' '));
  expect(pillarText).toContain('Pillar Depth');

  // Verify Editorial Activity has real posts (NOT "No articles created yet")
  const recentArticlesContainer = page.locator('#panel-ct-overview div:has-text("Recent Editorial Activity")').first();
  await expect(recentArticlesContainer).toBeVisible();
  const emptyCheck = await recentArticlesContainer.locator('text=No articles created yet').count();
  expect(emptyCheck).toBe(0);

  // Verify Library Distribution has real words count
  const wordsEl = page.locator('#panel-ct-overview span:has-text("words")');
  await expect(wordsEl).toBeVisible();
  const wordsText = await wordsEl.innerText();
  console.log('Words Total:', wordsText);
  expect(wordsText).not.toBe('0 words');

  // Screenshot Overview Tab
  await page.screenshot({ path: `${artifactDir}/overview_tab_strategic_kpis.png` });
  console.log('Captured overview_tab_strategic_kpis.png');

  // ==========================================
  // PART 2: OPPORTUNITIES TAB STRATEGIC GROWTH ENGINE
  // ==========================================
  // Switch to Opportunities Tab
  await page.evaluate(() => {
    if (typeof window.switchContentTab === 'function') {
      window.switchContentTab('ct-opportunities');
    }
  });
  await page.waitForTimeout(1200);

  const oppPanel = page.locator('#panel-ct-opportunities');
  await expect(oppPanel).toBeVisible();

  // Wait for opportunities to populate
  await page.waitForFunction(() => {
    const el = document.getElementById('opp-stat-value');
    return el && el.textContent.trim() !== '-' && el.textContent.trim() !== '';
  }, { timeout: 10000 });

  // Verify 4 Strategic Business Value KPIs
  const statValue = await page.locator('#opp-stat-value').innerText();
  const statQuickWins = await page.locator('#opp-stat-quickwins').innerText();
  const statCommercial = await page.locator('#opp-stat-commercial').innerText();
  const statRunway = await page.locator('#opp-stat-runway').innerText();
  const countBadge = await page.locator('#opp-count-badge').innerText();

  console.log('Opportunities KPIs:');
  console.log('- Est. Ad Spend Value:', statValue);
  console.log('- Quick-Win Gems:', statQuickWins);
  console.log('- Commercial Share:', statCommercial);
  console.log('- Editorial Runway:', statRunway);
  console.log('- Active Badge:', countBadge);

  expect(statValue).toContain('₹');
  expect(statQuickWins).toContain('Topics');
  expect(statCommercial).toContain('%');
  expect(statRunway).toContain('Wks');
  expect(countBadge).toContain('Topics Active');

  // Verify Dynamic Service Pillar Filter Bar
  const pillarContainer = page.locator('#opp-pillar-pills');
  await expect(pillarContainer).toBeVisible();
  const pillarButtons = pillarContainer.locator('button');
  const pillarCount = await pillarButtons.count();
  console.log('Service Pillar count:', pillarCount);
  expect(pillarCount).toBeGreaterThan(1);

  // Verify Desktop Table & Decision Impact Callouts
  const oppRows = page.locator('#cora-opportunities-table-body tr');
  const rowCount = await oppRows.count();
  console.log('Opportunities rendered rows:', rowCount);
  expect(rowCount).toBeGreaterThan(0);

  // Verify that the Decision Impact / "Why this matters" quote is present in the first row
  const firstRow = oppRows.first();
  const decisionCallout = firstRow.locator('strong:has-text("Why this matters:")');
  await expect(decisionCallout).toBeVisible();
  const rowText = await firstRow.innerText();
  console.log('Sample Opportunity Row:\n', rowText.substring(0, 150));

  // Screenshot Opportunities Tab Desktop
  await page.screenshot({ path: `${artifactDir}/opportunities_tab_strategic_growth_matrix.png` });
  console.log('Captured opportunities_tab_strategic_growth_matrix.png');

  // Test Pillar Filtering: Click the second pillar button
  const secondPillarBtn = pillarButtons.nth(1);
  const secondPillarName = await secondPillarBtn.innerText();
  console.log('Testing filter by pillar:', secondPillarName);
  await secondPillarBtn.click();
  await page.waitForTimeout(400);

  const activeLabel = await page.locator('#opp-pillar-active-label').innerText();
  expect(activeLabel).toContain('Filtering by:');

  // Reset filters
  await page.evaluate(() => window.coraResetOppFilters());
  await page.waitForTimeout(400);

  // ==========================================
  // PART 3: MOBILE-FIRST PRESENTATION VERIFICATION
  // ==========================================
  await page.setViewportSize({ width: 375, height: 812 });
  await page.waitForTimeout(500);

  // In mobile, desktop table should be hidden and mobile cards visible
  const mobileCardsContainer = page.locator('#cora-opportunities-mobile-cards');
  await expect(mobileCardsContainer).toBeVisible();

  const mobileCards = mobileCardsContainer.locator('> div');
  const mobileCardCount = await mobileCards.count();
  console.log('Mobile Opportunities Card count:', mobileCardCount);
  expect(mobileCardCount).toBeGreaterThan(0);

  // Verify mobile card has Decision Impact callout and One-Click Draft button
  const firstMobileCard = mobileCards.first();
  await expect(firstMobileCard.locator('strong:has-text("Why this matters:")')).toBeVisible();
  await expect(firstMobileCard.locator('button:has-text("One-Click Draft")')).toBeVisible();

  // Screenshot Mobile Opportunities View
  await page.screenshot({ path: `${artifactDir}/opportunities_tab_mobile_cards.png` });
  console.log('Captured opportunities_tab_mobile_cards.png');

  // Switch to Overview tab on mobile
  await page.evaluate(() => {
    if (typeof window.switchContentTab === 'function') {
      window.switchContentTab('ct-overview');
    }
  });
  await page.waitForTimeout(500);

  // Screenshot Mobile Overview View
  await page.screenshot({ path: `${artifactDir}/overview_tab_mobile_kpis.png` });
  console.log('Captured overview_tab_mobile_kpis.png');

  // ==========================================
  // PART 4: AI COPILOT OFFICIAL ICON & CLIPPING RESOLUTION
  // ==========================================
  await page.setViewportSize({ width: 1366, height: 850 });
  await page.waitForTimeout(300);

  // Open Full-Page Article Editor
  await page.evaluate(() => {
    if (typeof window.coraToggleContentDrawer === 'function') {
      window.coraToggleContentDrawer(true);
    }
  });
  await page.waitForTimeout(800);

  // Verify Copilot button in Inspector top bar
  const copilotBtn = page.locator('#btn-sidebar-copilot');
  await expect(copilotBtn).toBeVisible();

  // Verify native title attribute is completely removed to prevent tooltip clipping
  const titleAttr = await copilotBtn.getAttribute('title');
  expect(titleAttr).toBeNull();

  // Verify SVG icon uses official dual-spark and has overflow: visible
  const svgEl = copilotBtn.locator('svg');
  await expect(svgEl).toBeVisible();
  const svgStyle = await svgEl.getAttribute('style');
  expect(svgStyle).toContain('overflow: visible');

  // Click Copilot to open panel and verify header icon
  await copilotBtn.click();
  await page.waitForTimeout(500);

  const copilotHeader = page.locator('#panel-inspector-copilot-chat');
  await expect(copilotHeader).toBeVisible();

  await page.screenshot({ path: `${artifactDir}/copilot_icon_unclipped_official.png` });
  console.log('Captured copilot_icon_unclipped_official.png');
});
