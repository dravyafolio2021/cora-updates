// ══════════════════════════════════════════════════════════════════════════
// WORDPRESS PLUGIN STACK COST ENGINE (PURE COMPUTATION)
// Calculates direct subscription fees, hosting, maintenance labor,
// renewal inflation, fragility heuristic, and itemized TCO.
// ══════════════════════════════════════════════════════════════════════════

export type ToolCurrency = 'USD' | 'EUR' | 'GBP' | 'INR';

export const CURRENCY_SYMBOLS: Record<ToolCurrency, string> = {
  USD: '$',
  EUR: '€',
  GBP: '£',
  INR: '₹',
};

export interface WpPluginCostInputs {
  currency: ToolCurrency;
  premiumPluginsCount: number;
  avgAnnualLicensePerPlugin: number;
  monthlyHostingCost: number;
  includeLaborCosts: boolean;
  developerHourlyRate: number;
  monthlyMaintenanceHours: number;
  annualRenewalInflationPct: number;
  majorUpdateIncidentsPerYear: number;
  avgHoursPerIncident: number;
  annualRevenueDependentOnSite: number;
  estimatedDowntimeHoursPerYear: number;
}

export interface ItemizedCostRow {
  category: string;
  description: string;
  monthlyAmount: number;
  annualAmount: number;
  percentOfTotal: number;
  isLabor: boolean;
}

export interface WpPluginCostResult {
  currency: ToolCurrency;
  currencySymbol: string;
  directLicenseCostAnnual: number;
  hostingCostAnnual: number;
  routineMaintenanceCostAnnual: number;
  incidentLaborCostAnnual: number;
  totalLaborCostAnnual: number;
  downtimeRiskExposureAnnual: number;
  totalAnnualTco: number;
  totalMonthlyTco: number;
  threeYearProjectedTco: number;
  itemizedBreakdown: ItemizedCostRow[];
  fragilityScore: number; // 0 to 100 heuristic
  fragilityBand: {
    label: string;
    description: string;
  };
}

export const WP_PLUGIN_PRESETS: Record<string, { name: string; description: string; inputs: WpPluginCostInputs }> = {
  creative_agency: {
    name: 'Boutique Creative Agency',
    description: 'Typical WordPress site with ACF Pro, Gravity Forms, WPML, and performance tools.',
    inputs: {
      currency: 'USD',
      premiumPluginsCount: 12,
      avgAnnualLicensePerPlugin: 149,
      monthlyHostingCost: 65,
      includeLaborCosts: true,
      developerHourlyRate: 85,
      monthlyMaintenanceHours: 4,
      annualRenewalInflationPct: 8,
      majorUpdateIncidentsPerYear: 3,
      avgHoursPerIncident: 4,
      annualRevenueDependentOnSite: 250000,
      estimatedDowntimeHoursPerYear: 2,
    },
  },
  heavy_elementor_woo: {
    name: 'Heavy Elementor & WooCommerce Stack',
    description: 'Complex store with visual page builders, payment add-ons, CRM connectors, and shipping tools.',
    inputs: {
      currency: 'USD',
      premiumPluginsCount: 22,
      avgAnnualLicensePerPlugin: 179,
      monthlyHostingCost: 150,
      includeLaborCosts: true,
      developerHourlyRate: 95,
      monthlyMaintenanceHours: 8,
      annualRenewalInflationPct: 10,
      majorUpdateIncidentsPerYear: 5,
      avgHoursPerIncident: 6,
      annualRevenueDependentOnSite: 500000,
      estimatedDowntimeHoursPerYear: 4,
    },
  },
  lean_publisher: {
    name: 'Lean Content Publisher',
    description: 'Simple editorial blog with minimal commercial plugins and low maintenance overhead.',
    inputs: {
      currency: 'USD',
      premiumPluginsCount: 5,
      avgAnnualLicensePerPlugin: 99,
      monthlyHostingCost: 35,
      includeLaborCosts: true,
      developerHourlyRate: 65,
      monthlyMaintenanceHours: 1.5,
      annualRenewalInflationPct: 5,
      majorUpdateIncidentsPerYear: 1,
      avgHoursPerIncident: 2,
      annualRevenueDependentOnSite: 80000,
      estimatedDowntimeHoursPerYear: 1,
    },
  },
};

export function calculateWpPluginCost(inputs: WpPluginCostInputs): WpPluginCostResult {
  const currency = inputs.currency || 'USD';
  const currencySymbol = CURRENCY_SYMBOLS[currency] || '$';

  // 1. Sanitize inputs safely (handling empty, negative, NaN, excessive)
  const plugins = Math.min(100, Math.max(0, isFinite(inputs.premiumPluginsCount) ? Math.round(inputs.premiumPluginsCount) : 12));
  const avgLicense = Math.min(10000, Math.max(0, isFinite(inputs.avgAnnualLicensePerPlugin) ? inputs.avgAnnualLicensePerPlugin : 149));
  const monthlyHosting = Math.min(5000, Math.max(0, isFinite(inputs.monthlyHostingCost) ? inputs.monthlyHostingCost : 65));
  const includeLabor = Boolean(inputs.includeLaborCosts);
  const devRate = Math.min(1000, Math.max(1, isFinite(inputs.developerHourlyRate) ? inputs.developerHourlyRate : 85));
  const monthlyMaintHours = Math.min(160, Math.max(0, isFinite(inputs.monthlyMaintenanceHours) ? inputs.monthlyMaintenanceHours : 4));
  const inflationRate = Math.min(50, Math.max(0, isFinite(inputs.annualRenewalInflationPct) ? inputs.annualRenewalInflationPct : 8)) / 100;
  const incidents = Math.min(50, Math.max(0, isFinite(inputs.majorUpdateIncidentsPerYear) ? inputs.majorUpdateIncidentsPerYear : 3));
  const hoursPerInc = Math.min(80, Math.max(0, isFinite(inputs.avgHoursPerIncident) ? inputs.avgHoursPerIncident : 4));
  const revenue = Math.max(0, isFinite(inputs.annualRevenueDependentOnSite) ? inputs.annualRevenueDependentOnSite : 250000);
  const downtimeHours = Math.min(500, Math.max(0, isFinite(inputs.estimatedDowntimeHoursPerYear) ? inputs.estimatedDowntimeHoursPerYear : 2));

  // 2. Direct Software & Hosting Costs
  const directLicenseCostAnnual = Math.round(plugins * avgLicense);
  const hostingCostAnnual = Math.round(monthlyHosting * 12);

  // 3. Labor Costs (Optional)
  const routineMaintenanceCostAnnual = includeLabor ? Math.round(monthlyMaintHours * 12 * devRate) : 0;
  const incidentLaborCostAnnual = includeLabor ? Math.round(incidents * hoursPerInc * devRate) : 0;
  const totalLaborCostAnnual = routineMaintenanceCostAnnual + incidentLaborCostAnnual;

  // 4. Downtime Revenue Exposure (based on 365 days x 8 business hours)
  const hourlyRevenueExposure = revenue / (365 * 8);
  const downtimeRiskExposureAnnual = Math.round(downtimeHours * hourlyRevenueExposure);

  // 5. Total TCO Calculations
  const totalAnnualTco = directLicenseCostAnnual + hostingCostAnnual + totalLaborCostAnnual + downtimeRiskExposureAnnual;
  const totalMonthlyTco = Math.round(totalAnnualTco / 12);

  // 3-Year Projection with annual inflation
  const year1 = totalAnnualTco;
  const year2 = Math.round(directLicenseCostAnnual * (1 + inflationRate) + hostingCostAnnual * 1.05 + totalLaborCostAnnual * 1.05 + downtimeRiskExposureAnnual);
  const year3 = Math.round(directLicenseCostAnnual * Math.pow(1 + inflationRate, 2) + hostingCostAnnual * 1.10 + totalLaborCostAnnual * 1.10 + downtimeRiskExposureAnnual);
  const threeYearProjectedTco = year1 + year2 + year3;

  // 6. Itemized Breakdown Table
  const safeTotal = totalAnnualTco > 0 ? totalAnnualTco : 1;
  const itemizedBreakdown: ItemizedCostRow[] = [
    {
      category: 'Plugin Subscriptions',
      description: `${plugins} commercial plugins renewed annually (~${currencySymbol}${avgLicense}/plugin)`,
      annualAmount: directLicenseCostAnnual,
      monthlyAmount: Math.round(directLicenseCostAnnual / 12),
      percentOfTotal: Math.round((directLicenseCostAnnual / safeTotal) * 100),
      isLabor: false,
    },
    {
      category: 'Hosting & Infrastructure',
      description: `Managed WordPress hosting & staging environment (${currencySymbol}${monthlyHosting}/mo)`,
      annualAmount: hostingCostAnnual,
      monthlyAmount: monthlyHosting,
      percentOfTotal: Math.round((hostingCostAnnual / safeTotal) * 100),
      isLabor: false,
    },
  ];

  if (includeLabor) {
    itemizedBreakdown.push(
      {
        category: 'Routine Maintenance & Staging QA',
        description: `${monthlyMaintHours} hrs/mo checking updates, visual regression & backup integrity (${currencySymbol}${devRate}/hr)`,
        annualAmount: routineMaintenanceCostAnnual,
        monthlyAmount: Math.round(routineMaintenanceCostAnnual / 12),
        percentOfTotal: Math.round((routineMaintenanceCostAnnual / safeTotal) * 100),
        isLabor: true,
      },
      {
        category: 'Incident Debugging & Breakage',
        description: `${incidents} incidents/yr requiring emergency plugin conflict resolution (~${hoursPerInc} hrs/incident)`,
        annualAmount: incidentLaborCostAnnual,
        monthlyAmount: Math.round(incidentLaborCostAnnual / 12),
        percentOfTotal: Math.round((incidentLaborCostAnnual / safeTotal) * 100),
        isLabor: true,
      }
    );
  }

  if (downtimeRiskExposureAnnual > 0) {
    itemizedBreakdown.push({
      category: 'Downtime Revenue Exposure',
      description: `${downtimeHours} hrs estimated downtime/form failure risk against ${currencySymbol}${revenue.toLocaleString()} pipeline`,
      annualAmount: downtimeRiskExposureAnnual,
      monthlyAmount: Math.round(downtimeRiskExposureAnnual / 12),
      percentOfTotal: Math.round((downtimeRiskExposureAnnual / safeTotal) * 100),
      isLabor: false,
    });
  }

  // 7. Fragility Score Heuristic (0 - 100)
  // Heuristic rule: Density (max 45 pts) + Incidents (max 35 pts) + Maintenance hours (max 20 pts)
  const densityPts = Math.min(45, (plugins / 25) * 45);
  const incidentPts = Math.min(35, (incidents / 6) * 35);
  const maintPts = Math.min(20, (monthlyMaintHours / 8) * 20);
  const fragilityScore = Math.min(100, Math.round(densityPts + incidentPts + maintPts));

  let bandLabel = 'Low Dependency Risk';
  let bandDesc = 'Lean plugin footprint with well-contained update maintenance requirements.';

  if (fragilityScore >= 70) {
    bandLabel = 'High Fragility & Sprawl';
    bandDesc = 'Elevated dependency density creates high risk of PHP incompatibilities, database drag, and update regression.';
  } else if (fragilityScore >= 35) {
    bandLabel = 'Moderate Overhead';
    bandDesc = 'Routine testing and plugin conflict resolution consume a notable portion of website operating budget.';
  }

  return {
    currency,
    currencySymbol,
    directLicenseCostAnnual,
    hostingCostAnnual,
    routineMaintenanceCostAnnual,
    incidentLaborCostAnnual,
    totalLaborCostAnnual,
    downtimeRiskExposureAnnual,
    totalAnnualTco,
    totalMonthlyTco,
    threeYearProjectedTco,
    itemizedBreakdown,
    fragilityScore,
    fragilityBand: {
      label: bandLabel,
      description: bandDesc,
    },
  };
}
