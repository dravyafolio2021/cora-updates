// ══════════════════════════════════════════════════════════════════════════
// WORDPRESS PUBLISHING FRICTION & PRODUCTION COST ENGINE (PURE COMPUTATION)
// Quantifies hours and payroll wasted per article on Gutenberg formatting,
// broken block troubleshooting, manual SEO entry, and multi-tool handoffs.
// ══════════════════════════════════════════════════════════════════════════

export interface WpPublishingFrictionInput {
  articlesPerMonth: number;
  authorHourlyRate: number;
  gutenbergBlockFrictionHoursPerPost: number;
  stagingSyncAndFormattingHoursPerPost: number;
  qaAndVisualRegressionHoursPerPost: number;
  seoAndSchemaManualEntryMinutesPerPost: number;
  contentCreatorsCount?: number;
}

export type WpPublishingFrictionInputs = WpPublishingFrictionInput;

export interface WpPublishingFrictionResult {
  frictionHoursPerArticle: number;
  annualWastedPublishingHours: number;
  annualPublishingFrictionCost: number;
  monthlyFrictionCost: number;
  costSavedWithCora: number;
  hoursSavedWithCora: number;
  threeYearFrictionCost: number;
  frictionBand: {
    label: string;
    description: string;
  };
}

export const WP_PUBLISHING_PRESETS: Record<string, { name: string; inputs: WpPublishingFrictionInput }> = {
  agency_blog: {
    name: 'Agency Blog',
    inputs: {
      articlesPerMonth: 8,
      authorHourlyRate: 65,
      gutenbergBlockFrictionHoursPerPost: 1.25,
      stagingSyncAndFormattingHoursPerPost: 0.75,
      qaAndVisualRegressionHoursPerPost: 0.5,
      seoAndSchemaManualEntryMinutesPerPost: 20,
    },
  },
  content_marketing_team: {
    name: 'Content Marketing Team',
    inputs: {
      articlesPerMonth: 24,
      authorHourlyRate: 75,
      gutenbergBlockFrictionHoursPerPost: 1.75,
      stagingSyncAndFormattingHoursPerPost: 1.0,
      qaAndVisualRegressionHoursPerPost: 1.0,
      seoAndSchemaManualEntryMinutesPerPost: 30,
    },
  },
  solo_creator: {
    name: 'Solo Creator',
    inputs: {
      articlesPerMonth: 4,
      authorHourlyRate: 50,
      gutenbergBlockFrictionHoursPerPost: 0.75,
      stagingSyncAndFormattingHoursPerPost: 0.5,
      qaAndVisualRegressionHoursPerPost: 0.25,
      seoAndSchemaManualEntryMinutesPerPost: 15,
    },
  },
};

export function calculateWpPublishingFriction(input: WpPublishingFrictionInput): WpPublishingFrictionResult {
  const articlesMonthly = Math.max(1, isFinite(input.articlesPerMonth) ? input.articlesPerMonth : 12);
  const authorRate = Math.max(1, isFinite(input.authorHourlyRate) ? input.authorHourlyRate : 65);
  const blockHours = Math.max(0, isFinite(input.gutenbergBlockFrictionHoursPerPost) ? input.gutenbergBlockFrictionHoursPerPost : 1.5);
  const stagingHours = Math.max(0, isFinite(input.stagingSyncAndFormattingHoursPerPost) ? input.stagingSyncAndFormattingHoursPerPost : 1.0);
  const qaHours = Math.max(0, isFinite(input.qaAndVisualRegressionHoursPerPost) ? input.qaAndVisualRegressionHoursPerPost : 0.75);
  const seoHours = Math.max(0, isFinite(input.seoAndSchemaManualEntryMinutesPerPost) ? input.seoAndSchemaManualEntryMinutesPerPost / 60 : 0.4);

  const frictionHoursPerArticle = Math.round((blockHours + stagingHours + qaHours + seoHours) * 100) / 100;
  const annualWastedPublishingHours = Math.round(articlesMonthly * 12 * frictionHoursPerArticle);
  const annualPublishingFrictionCost = Math.round(annualWastedPublishingHours * authorRate);
  const monthlyFrictionCost = Math.round(annualPublishingFrictionCost / 12);

  const hoursSavedWithCora = Math.round(annualWastedPublishingHours * 0.70);
  const costSavedWithCora = Math.round(annualPublishingFrictionCost * 0.70);
  const threeYearFrictionCost = Math.round(annualPublishingFrictionCost * 3);

  let bandLabel = 'Low Editorial Overhead';
  let bandDesc = 'Your team maintains a lean publishing workflow with minimal block formatting drag.';

  if (frictionHoursPerArticle >= 3.0) {
    bandLabel = 'Severe Publishing Drag';
    bandDesc = 'Over 3 hours per article are consumed by formatting, staging syncs, and manual SEO forms.';
  } else if (frictionHoursPerArticle >= 1.75) {
    bandLabel = 'Moderate Formatting Friction';
    bandDesc = 'A notable portion of editorial time is diverted to technical layout QA and block styling.';
  }

  return {
    frictionHoursPerArticle,
    annualWastedPublishingHours,
    annualPublishingFrictionCost,
    monthlyFrictionCost,
    costSavedWithCora,
    hoursSavedWithCora,
    threeYearFrictionCost,
    frictionBand: {
      label: bandLabel,
      description: bandDesc,
    },
  };
}
