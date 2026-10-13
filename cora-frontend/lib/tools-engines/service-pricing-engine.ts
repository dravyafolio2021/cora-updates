// ══════════════════════════════════════════════════════════════════════════
// SERVICE PROJECT PRICING & RETAINER ENGINE (PURE COMPUTATION)
// ══════════════════════════════════════════════════════════════════════════

export interface ServicePricingInputs {
  targetAnnualIncome: number;
  billableHoursPerWeek: number;
  weeksWorkedPerYear: number;
  annualOverheadCosts: number;
  targetProfitMarginPercent: number;
  taxRatePercent: number;
}

export interface ServicePricingResult {
  minimumHourlyRate: number;
  recommendedHourlyRate: number;
  valueBasedHourlyRate: number;
  dailyRate: number;
  weeklyRate: number;
  monthlyRetainerRate: number;
  standardProjectRate: number;
  effectiveAnnualGrossRevenue: number;
}

export const SERVICE_PRICING_PRESETS: Record<string, { name: string; inputs: ServicePricingInputs }> = {
  freelance_consultant: {
    name: 'Solo Consultant',
    inputs: {
      targetAnnualIncome: 90000,
      billableHoursPerWeek: 20,
      weeksWorkedPerYear: 48,
      annualOverheadCosts: 12000,
      targetProfitMarginPercent: 15,
      taxRatePercent: 25,
    },
  },
  boutique_studio: {
    name: 'Boutique Studio',
    inputs: {
      targetAnnualIncome: 180000,
      billableHoursPerWeek: 25,
      weeksWorkedPerYear: 48,
      annualOverheadCosts: 24000,
      targetProfitMarginPercent: 20,
      taxRatePercent: 28,
    },
  },
  established_agency: {
    name: 'Growth Agency',
    inputs: {
      targetAnnualIncome: 350000,
      billableHoursPerWeek: 25,
      weeksWorkedPerYear: 48,
      annualOverheadCosts: 60000,
      targetProfitMarginPercent: 25,
      taxRatePercent: 30,
    },
  },
};

export function calculateServicePricing(inputs: ServicePricingInputs): ServicePricingResult {
  const targetIncome = Math.max(1000, isFinite(inputs.targetAnnualIncome) ? inputs.targetAnnualIncome : 120000);
  const billableHoursWk = Math.max(1, isFinite(inputs.billableHoursPerWeek) ? inputs.billableHoursPerWeek : 25);
  const weeksWorked = Math.max(1, isFinite(inputs.weeksWorkedPerYear) ? inputs.weeksWorkedPerYear : 48);
  const overhead = Math.max(0, isFinite(inputs.annualOverheadCosts) ? inputs.annualOverheadCosts : 18000);
  const targetMargin = Math.min(60, Math.max(0, isFinite(inputs.targetProfitMarginPercent) ? inputs.targetProfitMarginPercent : 20)) / 100;
  const taxRate = Math.min(50, Math.max(0, isFinite(inputs.taxRatePercent) ? inputs.taxRatePercent : 25)) / 100;

  // 1. Total Annual Billable Hours
  const totalBillableHours = billableHoursWk * weeksWorked;

  // 2. Gross revenue needed before taxes & overhead
  const preTaxIncomeRequired = targetIncome / Math.max(0.01, 1 - taxRate);
  const costFloorGross = preTaxIncomeRequired + overhead;
  const effectiveAnnualGrossRevenue = Math.round(costFloorGross / Math.max(0.01, 1 - targetMargin));

  // 3. Hourly Rates
  const minimumHourlyRate = Math.round(costFloorGross / totalBillableHours);
  const recommendedHourlyRate = Math.round(effectiveAnnualGrossRevenue / totalBillableHours);
  const valueBasedHourlyRate = Math.round(recommendedHourlyRate * 1.25);

  // 4. Packaging Rates
  const dailyRate = recommendedHourlyRate * 7;
  const weeklyRate = recommendedHourlyRate * billableHoursWk;
  const monthlyRetainerRate = Math.round((effectiveAnnualGrossRevenue / 12) * 0.40); // 40% of monthly quota
  const standardProjectRate = recommendedHourlyRate * 60; // 60h standard project baseline

  return {
    minimumHourlyRate,
    recommendedHourlyRate,
    valueBasedHourlyRate,
    dailyRate,
    weeklyRate,
    monthlyRetainerRate,
    standardProjectRate,
    effectiveAnnualGrossRevenue,
  };
}
