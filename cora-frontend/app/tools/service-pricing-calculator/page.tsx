'use client';

import React, { useState, useMemo, useEffect } from 'react';
import Link from 'next/link';
import {
  ToolContainer,
  ToolNumberInput,
  ToolScoreBand,
  ToolFindingsList,
  ToolMethodologyCard,
  ToolWorkedExample,
  ToolPrivacyDisclosure,
  ToolContextualCoraCta,
  ToolFaqAccordion,
  ToolJsonLd,
  FindingItem,
} from '@/components/tools/framework';
import {
  calculateServicePricing,
  SERVICE_PRICING_PRESETS,
  ServicePricingInputs,
} from '@/lib/tools-engines/service-pricing-engine';
import {
  trackToolStarted,
  trackToolCompleted,
  trackToolResultBand,
} from '@/lib/tools-analytics';

export default function ServicePricingCalculatorPage() {
  const [inputs, setInputs] = useState<ServicePricingInputs>({
    targetAnnualIncome: 120000,
    billableHoursPerWeek: 25,
    weeksWorkedPerYear: 48,
    annualOverheadCosts: 18000,
    targetProfitMarginPercent: 20,
    taxRatePercent: 25,
  });

  const [hasInteracted, setHasInteracted] = useState(false);

  const result = useMemo(() => {
    return calculateServicePricing(inputs);
  }, [inputs]);

  useEffect(() => {
    if (hasInteracted) {
      trackToolCompleted({
        tool_slug: 'service-pricing-calculator',
        calculation_mode: 'client_side',
      });
      trackToolResultBand({
        tool_slug: 'service-pricing-calculator',
        band_name: 'Recommended Rate',
        primary_metric_value: result.recommendedHourlyRate,
      });
    }
  }, [hasInteracted, result.recommendedHourlyRate]);

  const handleInputChange = (field: keyof ServicePricingInputs, val: number) => {
    if (!hasInteracted) {
      setHasInteracted(true);
      trackToolStarted({ tool_slug: 'service-pricing-calculator' });
    }
    setInputs((prev) => ({ ...prev, [field]: val }));
  };

  const applyPreset = (presetKey: string) => {
    const preset = SERVICE_PRICING_PRESETS[presetKey];
    if (preset) {
      if (!hasInteracted) {
        setHasInteracted(true);
        trackToolStarted({ tool_slug: 'service-pricing-calculator' });
      }
      setInputs((prev) => ({ ...prev, ...preset.inputs }));
    }
  };

  const findings: FindingItem[] = useMemo(() => {
    const list: FindingItem[] = [];

    if (inputs.billableHoursPerWeek > 30) {
      list.push({
        id: 'utilization-risk',
        severity: 'warning',
        title: 'High Billable Utilization Assumption',
        description: `Targeting ${inputs.billableHoursPerWeek} billable hours/week leaves under 10 hours for business development, client proposals, invoicing, and admin, risking chronic burnout.`,
        remedy: 'Target 20-25 billable hours/week and increase your base project rates accordingly.',
        metric: `${inputs.billableHoursPerWeek} hrs/wk planned`,
      });
    }

    if (result.recommendedHourlyRate < 75) {
      list.push({
        id: 'rate-floor',
        severity: 'info',
        title: 'Entry-Tier Market Positioning',
        description: `Your recommended hourly rate ($${result.recommendedHourlyRate}/hr) positions your studio at the lower end of professional service agencies.`,
        remedy: 'Package services into fixed-scope outcomes or retainers to capture value-based pricing premiums.',
        metric: `$${result.recommendedHourlyRate}/hr`,
      });
    } else {
      list.push({
        id: 'sustainable-model',
        severity: 'positive',
        title: 'Healthy Financial Sustainability',
        description: `Your rate model cleanly covers target salary ($${inputs.targetAnnualIncome.toLocaleString()}), studio overhead ($${inputs.annualOverheadCosts.toLocaleString()}), and a ${inputs.targetProfitMarginPercent}% agency profit cushion.`,
        metric: 'Balanced Model',
      });
    }

    return list;
  }, [result, inputs]);

  const canonicalUrl = 'https://heycora.in/tools/service-pricing-calculator';
  const metaTitle = 'Service Project Pricing Calculator | Agency Hourly & Retainer Rates';
  const metaDescription =
    'Calculate your ideal agency hourly rate, project pricing, day rates, and monthly retainer fees based on billable capacity, overhead, and profit targets.';

  const faqs = [
    {
      question: 'How should creative agencies and studios calculate their hourly rate?',
      answer:
        'Start with your target take-home salary, add annual operational overhead (software, rent, legal, accounting), factor in estimated income taxes, and add an agency profit margin (15-25%). Divide this required gross revenue by realistic annual billable hours (typically 1,000 to 1,200 hours per year per person).',
    },
    {
      question: 'Why should agencies transition from hourly billing to value-based project pricing?',
      answer:
        'Hourly billing penalizes speed and efficiency—the faster you work, the less you earn. Fixed project pricing and monthly retainers align incentives around client outcomes while protecting agency profitability.',
    },
    {
      question: 'How does Cora help agencies generate profitable proposals faster?',
      answer:
        'Cora lets you assemble structured rate cards, deliverable packages, and GST-compliant milestone schedules into interactive digital proposals that clients can review, e-sign, and deposit in seconds.',
    },
  ];

  return (
    <ToolContainer
      categoryLabel="Agency Margins & Billing"
      title="Service Project Pricing & Retainer Rate Calculator"
      subtitle="Determine sustainable agency hourly rates, fixed project fees, day rates, and monthly retainer packages based on your billable capacity and profit targets."
      badgeTag="Pricing Model"
      executionMode="browser-only"
      lastReviewedDate="October 2026"
    >
      <ToolJsonLd
        toolTitle={metaTitle}
        toolDescription={metaDescription}
        canonicalUrl={canonicalUrl}
        categoryName="Agency Margins & Billing"
        faqs={faqs}
      />

      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
        {/* LEFT COLUMN: INPUTS */}
        <div className="lg:col-span-7 space-y-6">
          
          {/* Preset Buttons */}
          <div className="rounded-2xl border border-zinc-200/90 bg-white p-5 sm:p-6 shadow-xs">
            <div className="flex items-center justify-between gap-2 mb-3">
              <span className="text-xs font-mono font-semibold uppercase tracking-wider text-zinc-500">
                Load Studio Persona Presets
              </span>
              <span className="text-[11px] font-mono text-zinc-400">One-click model setup</span>
            </div>
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-2">
              <button
                type="button"
                onClick={() => applyPreset('freelance_consultant')}
                className="px-3 py-2 text-left rounded-xl border border-zinc-200 hover:border-zinc-400 bg-zinc-50 hover:bg-zinc-100 transition-colors text-xs font-medium text-zinc-800"
              >
                <span className="font-semibold block text-zinc-950">Solo Consultant</span>
                <span className="text-[11px] text-zinc-500">$90k target · 20 hrs/wk</span>
              </button>
              <button
                type="button"
                onClick={() => applyPreset('boutique_studio')}
                className="px-3 py-2 text-left rounded-xl border border-zinc-200 hover:border-zinc-400 bg-zinc-50 hover:bg-zinc-100 transition-colors text-xs font-medium text-zinc-800"
              >
                <span className="font-semibold block text-zinc-950">Boutique Studio</span>
                <span className="text-[11px] text-zinc-500">$180k target · 25 hrs/wk</span>
              </button>
              <button
                type="button"
                onClick={() => applyPreset('established_agency')}
                className="px-3 py-2 text-left rounded-xl border border-zinc-200 hover:border-zinc-400 bg-zinc-50 hover:bg-zinc-100 transition-colors text-xs font-medium text-zinc-800"
              >
                <span className="font-semibold block text-zinc-950">Growth Agency</span>
                <span className="text-[11px] text-zinc-500">$350k target · 25% margin</span>
              </button>
            </div>
          </div>

          {/* Target Financial Goals */}
          <div className="rounded-2xl border border-zinc-200/90 bg-white p-6 sm:p-8 shadow-xs space-y-6">
            <h2 className="font-display text-lg sm:text-xl font-bold text-zinc-950 tracking-tight pb-3 border-b border-zinc-200/70">
              1. Income & Financial Targets
            </h2>

            <ToolNumberInput
              id="targetAnnualIncome"
              label="Target Annual Net Income / Owner Pay"
              helpText="Desired net take-home earnings per year before business profit distributions."
              value={inputs.targetAnnualIncome}
              onChange={(v) => handleInputChange('targetAnnualIncome', v)}
              min={20000}
              max={1000000}
              step={5000}
              prefix="$"
            />

            <ToolNumberInput
              id="annualOverheadCosts"
              label="Annual Operating Overhead & Software"
              helpText="SaaS tools, accounting, legal, insurance, hardware, hosting, and office expenses."
              value={inputs.annualOverheadCosts}
              onChange={(v) => handleInputChange('annualOverheadCosts', v)}
              min={0}
              max={250000}
              step={1000}
              prefix="$"
            />

            <ToolNumberInput
              id="targetProfitMarginPercent"
              label="Target Agency Profit Margin"
              helpText="Operating profit retained in the business beyond owner compensation (typically 15% to 30%)."
              value={inputs.targetProfitMarginPercent}
              onChange={(v) => handleInputChange('targetProfitMarginPercent', v)}
              min={0}
              max={50}
              step={5}
              suffix="%"
            />
          </div>

          {/* Billable Capacity */}
          <div className="rounded-2xl border border-zinc-200/90 bg-white p-6 sm:p-8 shadow-xs space-y-6">
            <h2 className="font-display text-lg sm:text-xl font-bold text-zinc-950 tracking-tight pb-3 border-b border-zinc-200/70">
              2. Billable Time & Working Capacity
            </h2>

            <ToolNumberInput
              id="billableHoursPerWeek"
              label="Realistic Billable Hours per Week"
              helpText="Direct client work hours (excluding marketing, admin, sales, and internal projects; 20-25 hrs is typical)."
              value={inputs.billableHoursPerWeek}
              onChange={(v) => handleInputChange('billableHoursPerWeek', v)}
              min={5}
              max={45}
              step={1}
              suffix="hrs/wk"
            />

            <ToolNumberInput
              id="weeksWorkedPerYear"
              label="Weeks Worked per Year"
              helpText="52 weeks minus planned vacation, holidays, and sick leave (typically 46 to 48 weeks)."
              value={inputs.weeksWorkedPerYear}
              onChange={(v) => handleInputChange('weeksWorkedPerYear', v)}
              min={35}
              max={52}
              step={1}
              suffix="weeks"
            />

            <ToolNumberInput
              id="taxRatePercent"
              label="Estimated Effective Tax Rate"
              helpText="Combined personal/corporate income tax and local statutory withholdings."
              value={inputs.taxRatePercent}
              onChange={(v) => handleInputChange('taxRatePercent', v)}
              min={0}
              max={50}
              step={1}
              suffix="%"
            />
          </div>

        </div>

        {/* RIGHT COLUMN: PRICING MATRIX */}
        <div className="lg:col-span-5 space-y-6">
          <div className="rounded-3xl border border-zinc-900 bg-zinc-950 text-white p-6 sm:p-8 shadow-lg sticky top-24">
            <div className="flex items-center justify-between mb-4 border-b border-zinc-800 pb-3">
              <span className="text-xs font-mono font-medium text-zinc-400 uppercase tracking-wider">
                Recommended Hourly Rate
              </span>
              <span className="text-[11px] font-mono px-2 py-0.5 rounded-full bg-zinc-800 text-zinc-300">
                100% Client Calculated
              </span>
            </div>

            <div className="font-mono text-4xl sm:text-5xl font-bold tracking-tight text-white mb-2">
              ${result.recommendedHourlyRate}
              <span className="text-sm font-sans font-normal text-zinc-400 block mt-1">
                per billable hour to meet financial goals
              </span>
            </div>

            {/* Rate Tiers Table */}
            <div className="mt-6 pt-6 border-t border-zinc-800 space-y-3 font-mono text-xs">
              <div className="flex items-center justify-between text-zinc-400">
                <span>Minimum Break-Even Rate</span>
                <span className="text-zinc-300 font-bold">${result.minimumHourlyRate} / hr</span>
              </div>
              <div className="flex items-center justify-between text-zinc-400">
                <span>Recommended Standard Rate</span>
                <span className="text-white font-bold">${result.recommendedHourlyRate} / hr</span>
              </div>
              <div className="flex items-center justify-between text-zinc-400">
                <span>Value-Based Tier (+25%)</span>
                <span className="text-emerald-400 font-bold">${result.valueBasedHourlyRate} / hr</span>
              </div>
            </div>

            {/* Packaging Rates Matrix */}
            <div className="mt-6 pt-6 border-t border-zinc-800">
              <span className="text-xs font-mono text-zinc-400 uppercase tracking-wider block mb-3">
                Service Packaging Reference
              </span>
              <div className="grid grid-cols-2 gap-2 text-xs font-mono">
                <div className="p-3 rounded-xl bg-zinc-900 border border-zinc-800">
                  <span className="text-zinc-500 text-[10px] block">Day Rate (7 hrs)</span>
                  <span className="text-white font-bold text-sm">${result.dailyRate.toLocaleString()}</span>
                </div>
                <div className="p-3 rounded-xl bg-zinc-900 border border-zinc-800">
                  <span className="text-zinc-500 text-[10px] block">Weekly Sprint</span>
                  <span className="text-white font-bold text-sm">${result.weeklyRate.toLocaleString()}</span>
                </div>
                <div className="p-3 rounded-xl bg-zinc-900 border border-zinc-800">
                  <span className="text-zinc-500 text-[10px] block">Monthly Retainer</span>
                  <span className="text-emerald-400 font-bold text-sm">${result.monthlyRetainerRate.toLocaleString()}/mo</span>
                </div>
                <div className="p-3 rounded-xl bg-zinc-900 border border-zinc-800">
                  <span className="text-zinc-500 text-[10px] block">Standard Build (60h)</span>
                  <span className="text-white font-bold text-sm">${result.standardProjectRate.toLocaleString()}</span>
                </div>
              </div>
            </div>
          </div>

          <ToolFindingsList findings={findings} />
        </div>
      </div>

      {/* WORKED EXAMPLE */}
      <div className="mb-12">
        <ToolWorkedExample
          scenarioDescription="A solo digital consultant wants $120,000 net take-home pay with $18,000 annual business expenses, paying a 25% effective tax rate while targeting 25 billable hours/week for 48 weeks with a 20% agency profit margin."
          inputsTable={[
            { label: 'Target Net Income', value: '$120,000' },
            { label: 'Overhead', value: '$18,000 / yr' },
            { label: 'Billable Hours', value: '25 hrs / wk' },
            { label: 'Weeks Worked', value: '48 weeks' },
          ]}
          steps={[
            {
              step: 'Calculate Total Annual Billable Hours',
              detail: '25 billable hours/week × 48 weeks',
              result: '1,200 billable hours / yr',
            },
            {
              step: 'Gross Up for Personal Taxes',
              detail: '$120,000 net income ÷ (1 - 0.25 tax rate)',
              result: '$160,000 gross required',
            },
            {
              step: 'Add Overhead and Target Profit Margin',
              detail: '($160,000 gross + $18,000 overhead) ÷ (1 - 0.20 margin)',
              result: '$222,500 total revenue needed',
            },
            {
              step: 'Calculate Recommended Hourly Rate',
              detail: '$222,500 total gross revenue ÷ 1,200 billable hours',
              result: '$185 / hour',
            },
          ]}
          conclusion="To sustainably take home $120k while covering overhead and funding agency reserves, the consultant must bill at $185/hr or sell monthly retainers starting at $7,400/month."
        />
      </div>

      {/* METHODOLOGY & FORMULAS */}
      <div className="mb-12">
        <ToolMethodologyCard
          methodologyVersion="v1.2 (October 2026)"
          formulas={[
            {
              label: 'Billable Annual Capacity',
              formula: 'Hours_annual = Hours_billable_weekly × Weeks_worked_annual',
              explanation: 'Total sellable hours after removing internal administrative and marketing time.',
            },
            {
              label: 'Required Gross Revenue',
              formula: 'Revenue_required = ((Income_target / (1 - Tax_rate)) + Overhead) / (1 - Margin)',
              explanation: 'Gross billing target required to cover taxes, business operating costs, and profit cushion.',
            },
            {
              label: 'Recommended Rate',
              formula: 'Rate_hourly = Revenue_required / Hours_annual',
              explanation: 'Standard rate per billable delivery hour.',
            },
          ]}
          assumptions={[
            'Non-billable admin time (invoicing, prospecting, emails) takes roughly 35-40% of total working hours.',
            'Profit margin represents retained business reserves for hiring, equipment upgrades, and lean quarters.',
          ]}
          limitations={[
            'Market acceptance varies based on geography, specialization, and proven client case study results.',
            'Value-based pricing may exceed mathematical cost-plus minimums significantly.',
          ]}
        />
      </div>

      {/* CORA CONVERSION BRIDGE */}
      <div className="mb-12">
        <ToolContextualCoraCta
          toolSlug="service-pricing-calculator"
          badge="The Proposal & Retainer Bridge"
          heading="Turn Your Rate Card into Signed, GST-Compliant Retainers."
          description="Stop sending loose PDF quotes. Cora packages your hourly rates, fixed-fee milestones, and monthly retainers into interactive digital proposals with 1-click e-signatures and integrated UPI QR / card checkout."
          bulletPoints={[
            'Assemble custom multi-tiered proposals in under 2 minutes',
            'Automated 18% GST calculation and HSN/SAC code compliance',
            'Collect upfront retainer deposits directly inside the proposal link',
          ]}
          ctaText="Explore Cora's Proposal Engine"
          ctaHref="/pricing"
          secondaryText="View Feature Modules"
          secondaryHref="/features"
        />
      </div>

      {/* FAQS */}
      <div className="mb-12">
        <ToolFaqAccordion items={faqs} />
      </div>

      {/* PRIVACY */}
      <ToolPrivacyDisclosure toolName="Service Project Pricing Calculator" />

    </ToolContainer>
  );
}
