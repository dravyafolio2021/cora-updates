// ══════════════════════════════════════════════════════════════════════════
// WEBSITE LEAD-LEAKAGE ENGINE (PURE COMPUTATION)
// Models speed-to-lead response decay, intake drop-off, proposal turnaround
// friction, and compares observed baseline against hypothetical reduced-friction models.
// ══════════════════════════════════════════════════════════════════════════

import { ToolCurrency, CURRENCY_SYMBOLS } from './wp-plugin-cost-engine';

export interface LeadLeakageInputs {
  currency: ToolCurrency;
  monthlyInboundLeads: number;
  averageDealValue: number;
  firstResponseTimeHours: number;
  contactQualificationRatePct: number; // % of leads that get qualified
  proposalDispatchRatePct: number; // % of qualified leads sent a proposal
  proposalCloseRatePct: number; // % of proposals closed
  proposalTurnaroundDays: number;
}

export interface FunnelScenarioStage {
  stageName: string;
  observedInflow: number;
  observedOutflow: number;
  observedDropoff: number;
  hypotheticalInflow: number;
  hypotheticalOutflow: number;
  hypotheticalDropoff: number;
  dropoffReduction: number;
}

export interface LeadLeakageResult {
  currency: ToolCurrency;
  currencySymbol: string;
  // Observed Scenario Metrics
  observedQualifiedLeads: number;
  observedProposalsSent: number;
  observedDealsWon: number;
  observedMonthlyRevenue: number;
  observedAnnualRevenue: number;
  
  // Hypothetical Low-Friction Scenario Metrics
  hypotheticalQualifiedLeads: number;
  hypotheticalProposalsSent: number;
  hypotheticalDealsWon: number;
  hypotheticalMonthlyRevenue: number;
  hypotheticalAnnualRevenue: number;

  // Comparison & Opportunity Gap
  monthlyRevenueOpportunityGap: number;
  annualRevenueOpportunityGap: number;
  potentialWonDealsDifferenceMonthly: number;
  overallFunnelEfficiencyObservedPct: number;
  overallFunnelEfficiencyHypotheticalPct: number;

  // Stage Comparison Breakdown
  stages: FunnelScenarioStage[];

  // Heuristic Diagnostic Band
  funnelLeakageBand: {
    label: string;
    description: string;
  };
}

export const LEAD_LEAKAGE_PRESETS: Record<string, { name: string; description: string; inputs: LeadLeakageInputs }> = {
  creative_studio: {
    name: 'Boutique Creative Studio',
    description: 'Inbound design inquiries handled manually by founder or account director.',
    inputs: {
      currency: 'USD',
      monthlyInboundLeads: 25,
      averageDealValue: 4800,
      firstResponseTimeHours: 6,
      contactQualificationRatePct: 40,
      proposalDispatchRatePct: 65,
      proposalCloseRatePct: 35,
      proposalTurnaroundDays: 4,
    },
  },
  marketing_agency: {
    name: 'Growth Marketing Agency',
    inputs: {
      currency: 'USD',
      monthlyInboundLeads: 60,
      averageDealValue: 3500,
      firstResponseTimeHours: 12,
      contactQualificationRatePct: 35,
      proposalDispatchRatePct: 60,
      proposalCloseRatePct: 30,
      proposalTurnaroundDays: 5,
    },
    description: 'Lead volume driven by inbound content and forms, with delayed sales team routing.',
  },
  high_ticket_consultancy: {
    name: 'High-Ticket Specialist Consultancy',
    description: 'Low-volume, high-value consulting inquiries where deal loss has high financial impact.',
    inputs: {
      currency: 'USD',
      monthlyInboundLeads: 12,
      averageDealValue: 14000,
      firstResponseTimeHours: 4,
      contactQualificationRatePct: 50,
      proposalDispatchRatePct: 75,
      proposalCloseRatePct: 40,
      proposalTurnaroundDays: 6,
    },
  },
};

export function calculateLeadLeakage(inputs: LeadLeakageInputs): LeadLeakageResult {
  const currency = inputs.currency || 'USD';
  const currencySymbol = CURRENCY_SYMBOLS[currency] || '$';

  // 1. Sanitize inputs
  const leads = Math.max(1, isFinite(inputs.monthlyInboundLeads) ? inputs.monthlyInboundLeads : 25);
  const dealValue = Math.max(10, isFinite(inputs.averageDealValue) ? inputs.averageDealValue : 4800);
  const responseHours = Math.max(0.1, isFinite(inputs.firstResponseTimeHours) ? inputs.firstResponseTimeHours : 6);
  const qualRate = Math.min(100, Math.max(1, isFinite(inputs.contactQualificationRatePct) ? inputs.contactQualificationRatePct : 40)) / 100;
  const propRate = Math.min(100, Math.max(1, isFinite(inputs.proposalDispatchRatePct) ? inputs.proposalDispatchRatePct : 65)) / 100;
  const closeRate = Math.min(100, Math.max(1, isFinite(inputs.proposalCloseRatePct) ? inputs.proposalCloseRatePct : 35)) / 100;
  const turnaroundDays = Math.max(0.5, isFinite(inputs.proposalTurnaroundDays) ? inputs.proposalTurnaroundDays : 4);

  // 2. Response Decay Factor for Observed Scenario (Empirical speed-to-lead curve)
  // Decay factor: Sub-15m = 1.0; 1h = 0.85; 4h = 0.65; 12h = 0.45; 24h+ = 0.30
  let speedPenaltyFactor = 1.0;
  if (responseHours > 0.25) {
    if (responseHours <= 1) speedPenaltyFactor = 1.0 - ((responseHours - 0.25) / 0.75) * 0.15;
    else if (responseHours <= 4) speedPenaltyFactor = 0.85 - ((responseHours - 1) / 3) * 0.20;
    else if (responseHours <= 12) speedPenaltyFactor = 0.65 - ((responseHours - 4) / 8) * 0.20;
    else speedPenaltyFactor = Math.max(0.25, 0.45 - ((responseHours - 12) / 24) * 0.20);
  }

  // Proposal Lag Factor (Turnaround delay drag): 1 day = 1.0; 4 days = 0.85; 7 days = 0.70
  const propLagFactor = Math.max(0.50, 1.0 - Math.min(0.50, (turnaroundDays - 1) * 0.05));

  // 3. Observed Scenario Flow
  const observedEffectiveQualRate = qualRate * speedPenaltyFactor;
  const observedQualifiedLeads = Math.round(leads * observedEffectiveQualRate * 10) / 10;
  const observedProposalsSent = Math.round(observedQualifiedLeads * propRate * 10) / 10;
  const observedEffectiveCloseRate = closeRate * propLagFactor;
  const observedDealsWon = Math.round(observedProposalsSent * observedEffectiveCloseRate * 10) / 10;
  const observedMonthlyRevenue = Math.round(observedDealsWon * dealValue);
  const observedAnnualRevenue = observedMonthlyRevenue * 12;

  // 4. Hypothetical Low-Friction Scenario Flow (Fast sub-15m response + 24h proposal turnaround)
  // Hypothetical model assumes full baseline qualification rate without response decay penalty
  // and proposal turnaround within 24 hours.
  const hypotheticalQualifiedLeads = Math.round(leads * qualRate * 10) / 10;
  const hypotheticalProposalsSent = Math.round(hypotheticalQualifiedLeads * propRate * 10) / 10;
  const hypotheticalDealsWon = Math.round(hypotheticalProposalsSent * closeRate * 10) / 10;
  const hypotheticalMonthlyRevenue = Math.round(hypotheticalDealsWon * dealValue);
  const hypotheticalAnnualRevenue = hypotheticalMonthlyRevenue * 12;

  // 5. Gap / Opportunity Difference
  const monthlyRevenueOpportunityGap = Math.max(0, hypotheticalMonthlyRevenue - observedMonthlyRevenue);
  const annualRevenueOpportunityGap = monthlyRevenueOpportunityGap * 12;
  const potentialWonDealsDifferenceMonthly = Math.round((hypotheticalDealsWon - observedDealsWon) * 10) / 10;

  const overallFunnelEfficiencyObservedPct = leads > 0 ? Math.round((observedDealsWon / leads) * 100 * 10) / 10 : 0;
  const overallFunnelEfficiencyHypotheticalPct = leads > 0 ? Math.round((hypotheticalDealsWon / leads) * 100 * 10) / 10 : 0;

  // 6. Stage-by-Stage Comparison Data
  const stages: FunnelScenarioStage[] = [
    {
      stageName: 'First Response & Intake',
      observedInflow: leads,
      observedOutflow: observedQualifiedLeads,
      observedDropoff: Math.round((leads - observedQualifiedLeads) * 10) / 10,
      hypotheticalInflow: leads,
      hypotheticalOutflow: hypotheticalQualifiedLeads,
      hypotheticalDropoff: Math.round((leads - hypotheticalQualifiedLeads) * 10) / 10,
      dropoffReduction: Math.round((hypotheticalQualifiedLeads - observedQualifiedLeads) * 10) / 10,
    },
    {
      stageName: 'Discovery & Proposal Dispatch',
      observedInflow: observedQualifiedLeads,
      observedOutflow: observedProposalsSent,
      observedDropoff: Math.round((observedQualifiedLeads - observedProposalsSent) * 10) / 10,
      hypotheticalInflow: hypotheticalQualifiedLeads,
      hypotheticalOutflow: hypotheticalProposalsSent,
      hypotheticalDropoff: Math.round((hypotheticalQualifiedLeads - hypotheticalProposalsSent) * 10) / 10,
      dropoffReduction: Math.round((hypotheticalProposalsSent - observedProposalsSent) * 10) / 10,
    },
    {
      stageName: 'Proposal Review & Agreement Close',
      observedInflow: observedProposalsSent,
      observedOutflow: observedDealsWon,
      observedDropoff: Math.round((observedProposalsSent - observedDealsWon) * 10) / 10,
      hypotheticalInflow: hypotheticalProposalsSent,
      hypotheticalOutflow: hypotheticalDealsWon,
      hypotheticalDropoff: Math.round((hypotheticalProposalsSent - hypotheticalDealsWon) * 10) / 10,
      dropoffReduction: Math.round((hypotheticalDealsWon - observedDealsWon) * 10) / 10,
    },
  ];

  // 7. Heuristic Severity Band
  const opportunityGapRatio = hypotheticalMonthlyRevenue > 0
    ? (monthlyRevenueOpportunityGap / hypotheticalMonthlyRevenue) * 100
    : 0;

  let bandLabel = 'Responsive Conversion Funnel';
  let bandDesc = 'Observed response and turnaround times align closely with low-friction sales benchmarks.';

  if (opportunityGapRatio >= 40) {
    bandLabel = 'Significant Response Drag';
    bandDesc = 'Multi-hour response lag and extended proposal turnaround create material pipeline drop-off.';
  } else if (opportunityGapRatio >= 20) {
    bandLabel = 'Moderate Intake Friction';
    bandDesc = 'Notable potential drop-off between inquiry receipt and proposal delivery.';
  }

  return {
    currency,
    currencySymbol,
    observedQualifiedLeads,
    observedProposalsSent,
    observedDealsWon,
    observedMonthlyRevenue,
    observedAnnualRevenue,
    hypotheticalQualifiedLeads,
    hypotheticalProposalsSent,
    hypotheticalDealsWon,
    hypotheticalMonthlyRevenue,
    hypotheticalAnnualRevenue,
    monthlyRevenueOpportunityGap,
    annualRevenueOpportunityGap,
    potentialWonDealsDifferenceMonthly,
    overallFunnelEfficiencyObservedPct,
    overallFunnelEfficiencyHypotheticalPct,
    stages,
    funnelLeakageBand: {
      label: bandLabel,
      description: bandDesc,
    },
  };
}
