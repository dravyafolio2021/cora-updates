// ══════════════════════════════════════════════════════════════════════════
// AGENCY SCOPE-CREEP & PROFIT MARGIN ENGINE (PURE COMPUTATION)
// Models planned vs actual delivery hours, effective hourly rate decay,
// gross margin contraction, and explicit change-order quote formulas.
// ══════════════════════════════════════════════════════════════════════════

import { ToolCurrency, CURRENCY_SYMBOLS } from './wp-plugin-cost-engine';

export interface ScopeCreepInputs {
  currency: ToolCurrency;
  quotedProjectValue: number;
  plannedDeliveryHours: number;
  internalHourlyCost: number;
  unbilledCreepHours: number;
  includedRevisionRounds: number;
  actualRevisionRoundsDelivered: number;
  agreedOutofScopeHourlyRate: number;
  standardRevisionRoundFee: number;
  activeProjectsAnnually: number;
}

export interface ScopeCreepResult {
  currency: ToolCurrency;
  currencySymbol: string;
  totalActualHours: number;
  plannedEffectiveHourlyRate: number;
  realizedEffectiveHourlyRate: number;
  effectiveRateDecayPct: number;
  plannedInternalCost: number;
  actualInternalCost: number;
  plannedGrossProfit: number;
  realizedGrossProfit: number;
  plannedMarginPct: number;
  realizedMarginPct: number;
  marginContractionPoints: number;
  unbilledLaborCostInternal: number;
  suggestedChangeOrderAmount: number;
  changeOrderBreakdown: {
    creepHoursComponent: number;
    extraRevisionsComponent: number;
    total: number;
  };
  annualizedUnbilledLaborCost: number;
  annualizedPotentialChangeOrderRecovery: number;
  creepSeverityBand: {
    label: string;
    description: string;
  };
}

export const SCOPE_CREEP_PRESETS: Record<string, { name: string; description: string; inputs: ScopeCreepInputs }> = {
  web_dev_build: {
    name: 'Custom Web Application Build',
    description: 'Fixed-price website or web application project with unplanned feature requests.',
    inputs: {
      currency: 'USD',
      quotedProjectValue: 9500,
      plannedDeliveryHours: 80,
      internalHourlyCost: 55,
      unbilledCreepHours: 24,
      includedRevisionRounds: 2,
      actualRevisionRoundsDelivered: 5,
      agreedOutofScopeHourlyRate: 95,
      standardRevisionRoundFee: 350,
      activeProjectsAnnually: 16,
    },
  },
  branding_design: {
    name: 'Brand Identity & Guidelines Sprint',
    description: 'Design package prone to endless subjective tweaks and extra layout concepts.',
    inputs: {
      currency: 'USD',
      quotedProjectValue: 6000,
      plannedDeliveryHours: 50,
      internalHourlyCost: 50,
      unbilledCreepHours: 18,
      includedRevisionRounds: 2,
      actualRevisionRoundsDelivered: 6,
      agreedOutofScopeHourlyRate: 85,
      standardRevisionRoundFee: 250,
      activeProjectsAnnually: 22,
    },
  },
  monthly_retainer: {
    name: 'Monthly Marketing / Design Retainer',
    description: 'Retainer where out-of-scope requests quietly spill over monthly limits.',
    inputs: {
      currency: 'USD',
      quotedProjectValue: 4000,
      plannedDeliveryHours: 35,
      internalHourlyCost: 45,
      unbilledCreepHours: 10,
      includedRevisionRounds: 1,
      actualRevisionRoundsDelivered: 3,
      agreedOutofScopeHourlyRate: 80,
      standardRevisionRoundFee: 200,
      activeProjectsAnnually: 24,
    },
  },
};

export function calculateScopeCreep(inputs: ScopeCreepInputs): ScopeCreepResult {
  const currency = inputs.currency || 'USD';
  const currencySymbol = CURRENCY_SYMBOLS[currency] || '$';

  // 1. Sanitize inputs
  const quotedValue = Math.max(1, isFinite(inputs.quotedProjectValue) ? inputs.quotedProjectValue : 9500);
  const plannedHours = Math.max(1, isFinite(inputs.plannedDeliveryHours) ? inputs.plannedDeliveryHours : 80);
  const internalCost = Math.max(1, isFinite(inputs.internalHourlyCost) ? inputs.internalHourlyCost : 55);
  const creepHours = Math.max(0, isFinite(inputs.unbilledCreepHours) ? inputs.unbilledCreepHours : 24);
  const includedRounds = Math.max(0, isFinite(inputs.includedRevisionRounds) ? Math.round(inputs.includedRevisionRounds) : 2);
  const actualRounds = Math.max(includedRounds, isFinite(inputs.actualRevisionRoundsDelivered) ? Math.round(inputs.actualRevisionRoundsDelivered) : 5);
  const outOfScopeRate = Math.max(internalCost, isFinite(inputs.agreedOutofScopeHourlyRate) ? inputs.agreedOutofScopeHourlyRate : 95);
  const revisionFee = Math.max(0, isFinite(inputs.standardRevisionRoundFee) ? inputs.standardRevisionRoundFee : 350);
  const projectsAnnual = Math.max(1, isFinite(inputs.activeProjectsAnnually) ? inputs.activeProjectsAnnually : 16);

  // 2. Delivery Time & Effective Hourly Rate
  const totalActualHours = plannedHours + creepHours;
  const plannedEffectiveHourlyRate = Math.round(quotedValue / plannedHours);
  const realizedEffectiveHourlyRate = Math.round(quotedValue / totalActualHours);
  const effectiveRateDecayPct = Math.round(((plannedEffectiveHourlyRate - realizedEffectiveHourlyRate) / plannedEffectiveHourlyRate) * 100);

  // 3. Gross Margin Contraction Math
  const plannedInternalCost = Math.round(plannedHours * internalCost);
  const actualInternalCost = Math.round(totalActualHours * internalCost);
  const unbilledLaborCostInternal = Math.round(creepHours * internalCost);

  const plannedGrossProfit = quotedValue - plannedInternalCost;
  const realizedGrossProfit = quotedValue - actualInternalCost;

  const plannedMarginPct = Math.round((plannedGrossProfit / quotedValue) * 100);
  const realizedMarginPct = Math.round((realizedGrossProfit / quotedValue) * 100);
  const marginContractionPoints = Math.max(0, plannedMarginPct - realizedMarginPct);

  // 4. Suggested Change-Order Calculation (Explicit Formula)
  // Formula: (Creep Hours x Out-of-Scope Rate) + (Extra Revision Rounds x Standard Revision Fee)
  const extraRevisions = Math.max(0, actualRounds - includedRounds);
  const creepHoursComponent = Math.round(creepHours * outOfScopeRate);
  const extraRevisionsComponent = Math.round(extraRevisions * revisionFee);
  const suggestedChangeOrderAmount = creepHoursComponent + extraRevisionsComponent;

  // 5. Annualized Agency Portfolio Impact
  const annualizedUnbilledLaborCost = Math.round(unbilledLaborCostInternal * projectsAnnual);
  const annualizedPotentialChangeOrderRecovery = Math.round(suggestedChangeOrderAmount * projectsAnnual);

  // 6. Qualitative Severity Band
  let bandLabel = 'Controlled Project Scope';
  let bandDesc = 'Scope expansion is within acceptable buffer margins and does not threaten project profitability.';

  if (marginContractionPoints >= 20 || effectiveRateDecayPct >= 25) {
    bandLabel = 'Severe Margin Erosion';
    bandDesc = 'Unbilled scope expansion has significantly contracted project profit margins and reduced effective earning power.';
  } else if (marginContractionPoints >= 10 || effectiveRateDecayPct >= 12) {
    bandLabel = 'Moderate Scope Creep';
    bandDesc = 'Unbilled client revisions are steadily diluting agency hourly earnings.';
  }

  return {
    currency,
    currencySymbol,
    totalActualHours,
    plannedEffectiveHourlyRate,
    realizedEffectiveHourlyRate,
    effectiveRateDecayPct,
    plannedInternalCost,
    actualInternalCost,
    plannedGrossProfit,
    realizedGrossProfit,
    plannedMarginPct,
    realizedMarginPct,
    marginContractionPoints,
    unbilledLaborCostInternal,
    suggestedChangeOrderAmount,
    changeOrderBreakdown: {
      creepHoursComponent,
      extraRevisionsComponent,
      total: suggestedChangeOrderAmount,
    },
    annualizedUnbilledLaborCost,
    annualizedPotentialChangeOrderRecovery,
    creepSeverityBand: {
      label: bandLabel,
      description: bandDesc,
    },
  };
}
