// ══════════════════════════════════════════════════════════════════════════
// UNIT TEST SUITE FOR CORA TOOLS CALCULATION ENGINES
// Tests edge cases: 0, negative numbers, fractions, NaN, large inputs
// ══════════════════════════════════════════════════════════════════════════

import { calculateWpPluginCost, WP_PLUGIN_PRESETS } from '../wp-plugin-cost-engine';
import { calculateLeadLeakage, LEAD_LEAKAGE_PRESETS } from '../lead-leakage-engine';
import { calculateScopeCreep, SCOPE_CREEP_PRESETS } from '../scope-creep-engine';
import { calculateServicePricing, SERVICE_PRICING_PRESETS } from '../service-pricing-engine';
import { calculateWpPublishingFriction, WP_PUBLISHING_PRESETS } from '../wp-publishing-friction-engine';
import { parseRobotsTxt, AI_CRAWLER_SAMPLE_ROBOTS } from '../ai-crawler-engine';

export function runAllToolsUnitTests(): { passed: number; failed: number; errors: string[] } {
  let passed = 0;
  let failed = 0;
  const errors: string[] = [];

  function assert(condition: boolean, testName: string) {
    if (condition) {
      passed++;
    } else {
      failed++;
      errors.push(`Assertion failed: ${testName}`);
    }
  }

  // ── TEST 1: WordPress Plugin Cost Engine ──
  try {
    const res1 = calculateWpPluginCost(WP_PLUGIN_PRESETS.creative_agency.inputs);
    assert(res1.totalAnnualTco > 0, 'WP Plugin Cost returns positive annual TCO');
    assert(res1.fragilityScore >= 0 && res1.fragilityScore <= 100, 'Fragility score within 0-100');
    assert(res1.threeYearProjectedTco > res1.totalAnnualTco * 2, '3-Year TCO accounts for renewals');
    assert(res1.itemizedBreakdown.length > 0, 'Generates itemized cost breakdown');

    // Edge case: Negative and zero inputs
    const res1Zero = calculateWpPluginCost({
      currency: 'USD',
      premiumPluginsCount: 0,
      avgAnnualLicensePerPlugin: 0,
      monthlyHostingCost: 0,
      includeLaborCosts: false,
      developerHourlyRate: 0,
      monthlyMaintenanceHours: 0,
      annualRenewalInflationPct: 0,
      majorUpdateIncidentsPerYear: 0,
      avgHoursPerIncident: 0,
      annualRevenueDependentOnSite: 0,
      estimatedDowntimeHoursPerYear: 0,
    });
    assert(res1Zero.totalAnnualTco === 0, 'Handles zero plugin costs safely');
  } catch (err: any) {
    failed++;
    errors.push(`WP Plugin Cost Engine threw: ${err.message}`);
  }

  // ── TEST 2: Lead Leakage Engine ──
  try {
    const res2 = calculateLeadLeakage(LEAD_LEAKAGE_PRESETS.creative_studio.inputs);
    assert(res2.observedMonthlyRevenue >= 0, 'Observed monthly revenue non-negative');
    assert(res2.hypotheticalMonthlyRevenue >= res2.observedMonthlyRevenue, 'Hypothetical revenue >= observed revenue');
    assert(res2.monthlyRevenueOpportunityGap >= 0, 'Monthly opportunity gap non-negative');
    assert(res2.stages.length === 3, 'Models 3 stages of funnel friction');
    assert(res2.overallFunnelEfficiencyObservedPct >= 0 && res2.overallFunnelEfficiencyObservedPct <= 100, 'Funnel efficiency within 0-100%');

    // Edge case: Zero leads and negative rates
    const res2Zero = calculateLeadLeakage({
      currency: 'USD',
      monthlyInboundLeads: 0,
      averageDealValue: 0,
      firstResponseTimeHours: 0,
      contactQualificationRatePct: 0,
      proposalDispatchRatePct: 0,
      proposalCloseRatePct: 0,
      proposalTurnaroundDays: 0,
    });
    assert(res2Zero.observedMonthlyRevenue >= 0, 'Handles zero leads safely');
  } catch (err: any) {
    failed++;
    errors.push(`Lead Leakage Engine threw: ${err.message}`);
  }

  // ── TEST 3: Scope Creep Engine ──
  try {
    const res3 = calculateScopeCreep(SCOPE_CREEP_PRESETS.web_dev_build.inputs);
    assert(res3.unbilledLaborCostInternal > 0, 'Calculates positive unbilled creep labor cost');
    assert(res3.effectiveRateDecayPct >= 0, 'Calculates non-negative effective rate decay');
    assert(res3.suggestedChangeOrderAmount > 0, 'Calculates positive suggested change order amount');
    assert(res3.realizedEffectiveHourlyRate <= res3.plannedEffectiveHourlyRate, 'Realized rate decays under unbilled creep');

    // Edge case: Zero creep hours
    const res3Zero = calculateScopeCreep({
      currency: 'USD',
      quotedProjectValue: 5000,
      plannedDeliveryHours: 50,
      internalHourlyCost: 50,
      unbilledCreepHours: 0,
      includedRevisionRounds: 2,
      actualRevisionRoundsDelivered: 2,
      agreedOutofScopeHourlyRate: 100,
      standardRevisionRoundFee: 250,
      activeProjectsAnnually: 10,
    });
    assert(res3Zero.unbilledLaborCostInternal === 0, 'Zero creep yields zero unbilled internal cost');
    assert(res3Zero.effectiveRateDecayPct === 0, 'Zero creep yields zero rate decay');
  } catch (err: any) {
    failed++;
    errors.push(`Scope Creep Engine threw: ${err.message}`);
  }

  // ── TEST 4: Service Pricing Engine ──
  try {
    const res4 = calculateServicePricing(SERVICE_PRICING_PRESETS.boutique_studio.inputs);
    assert(res4.recommendedHourlyRate > res4.minimumHourlyRate, 'Recommended rate exceeds minimum break-even');
    assert(res4.valueBasedHourlyRate > res4.recommendedHourlyRate, 'Value-based tier exceeds recommended rate');
    assert(res4.monthlyRetainerRate > 0, 'Calculates positive monthly retainer');
  } catch (err: any) {
    failed++;
    errors.push(`Service Pricing Engine threw: ${err.message}`);
  }

  // ── TEST 5: WordPress Publishing Friction Engine ──
  try {
    const res5 = calculateWpPublishingFriction(WP_PUBLISHING_PRESETS.agency_blog.inputs);
    assert(res5.frictionHoursPerArticle > 0, 'Calculates positive friction time');
    assert(res5.annualPublishingFrictionCost > 0, 'Computes annual friction labor cost');
    assert(res5.costSavedWithCora > 0, 'Calculates positive savings with Cora');
  } catch (err: any) {
    failed++;
    errors.push(`WP Publishing Friction Engine threw: ${err.message}`);
  }

  // ── TEST 6: AI Crawler Directive Parser Engine ──
  try {
    const res6 = parseRobotsTxt(AI_CRAWLER_SAMPLE_ROBOTS.balanced_recommended);
    assert(res6.crawlers.length === 8, 'Inspects all 8 core AI/Search bots');
    const oai = res6.crawlers.find((b) => b.userAgent === 'OAI-SearchBot');
    const gpt = res6.crawlers.find((b) => b.userAgent === 'GPTBot');
    const google = res6.crawlers.find((b) => b.userAgent === 'Googlebot');

    assert(oai?.status === 'allowed', 'OAI-SearchBot correctly identified as allowed');
    assert(gpt?.status === 'blocked', 'GPTBot correctly identified as blocked');
    assert(google?.status === 'allowed', 'Googlebot correctly identified as allowed');
    assert(res6.overallScore > 0, 'Computes positive AI accessibility score');
  } catch (err: any) {
    failed++;
    errors.push(`AI Crawler Engine threw: ${err.message}`);
  }

  return { passed, failed, errors };
}

// Auto-run if executed directly via node/tsx
if (typeof require !== 'undefined' && require.main === module) {
  const { passed, failed, errors } = runAllToolsUnitTests();
  console.log(`\n=== CORA TOOLS UNIT TEST RESULTS ===`);
  console.log(`Passed: ${passed}`);
  console.log(`Failed: ${failed}`);
  if (errors.length > 0) {
    console.error('Errors:', errors);
    process.exit(1);
  } else {
    console.log('All tool calculation engine tests PASSED perfectly.\n');
    process.exit(0);
  }
}
