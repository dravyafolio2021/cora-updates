'use client';

import React, { useState, useMemo, useEffect } from 'react';
import Link from 'next/link';
import {
  ToolContainer,
  ToolNumberInput,
  ToolFindingsList,
  ToolMethodologyCard,
  ToolWorkedExample,
  ToolPrivacyDisclosure,
  ToolContextualCoraCta,
  ToolFaqAccordion,
  ToolJsonLd,
  ToolMobileBar,
  ToolMobileViewToggle,
  FindingItem,
} from '@/components/tools/framework';
import {
  calculateLeadLeakage,
  LEAD_LEAKAGE_PRESETS,
  LeadLeakageInputs,
} from '@/lib/tools-engines/lead-leakage-engine';
import { ToolCurrency, CURRENCY_SYMBOLS } from '@/lib/tools-engines/wp-plugin-cost-engine';
import {
  trackToolStarted,
  trackToolCompleted,
  trackToolResultBand,
} from '@/lib/tools-analytics';
import { Printer, Download, RotateCcw, Clock, FileCheck, ArrowRight, ShieldCheck } from 'lucide-react';

export default function LeadLeakageCalculatorPage() {
  const [mobileTab, setMobileTab] = useState<'inputs' | 'results'>('inputs');
  const [inputs, setInputs] = useState<LeadLeakageInputs>({
    currency: 'USD',
    monthlyInboundLeads: 25,
    averageDealValue: 4800,
    firstResponseTimeHours: 6,
    contactQualificationRatePct: 40,
    proposalDispatchRatePct: 65,
    proposalCloseRatePct: 35,
    proposalTurnaroundDays: 4,
  });

  const [hasInteracted, setHasInteracted] = useState(false);
  const [copiedSummary, setCopiedSummary] = useState(false);

  const result = useMemo(() => {
    return calculateLeadLeakage(inputs);
  }, [inputs]);

  useEffect(() => {
    if (hasInteracted) {
      trackToolCompleted({
        tool_slug: 'lead-leakage-calculator',
        calculation_mode: 'client_side',
      });
      trackToolResultBand({
        tool_slug: 'lead-leakage-calculator',
        band_name: result.funnelLeakageBand.label,
        primary_metric_value: result.monthlyRevenueOpportunityGap,
      });
    }
  }, [hasInteracted, result.funnelLeakageBand.label, result.monthlyRevenueOpportunityGap]);

  const handleInputChange = (field: keyof LeadLeakageInputs, val: any) => {
    if (!hasInteracted) {
      setHasInteracted(true);
      trackToolStarted({ tool_slug: 'lead-leakage-calculator' });
    }
    setInputs((prev) => ({ ...prev, [field]: val }));
  };

  const applyPreset = (presetKey: string) => {
    const preset = LEAD_LEAKAGE_PRESETS[presetKey];
    if (preset) {
      if (!hasInteracted) {
        setHasInteracted(true);
        trackToolStarted({ tool_slug: 'lead-leakage-calculator' });
      }
      setInputs((prev) => ({ ...prev, ...preset.inputs, currency: prev.currency }));
    }
  };

  const resetToDefault = () => {
    applyPreset('creative_studio');
  };

  const handlePrint = () => {
    if (typeof window !== 'undefined') {
      window.print();
    }
  };

  const exportSummaryCsv = () => {
    const sym = result.currencySymbol;
    let csv = `Stage,Observed Inflow,Observed Outflow,Observed Dropoff,Hypothetical Inflow,Hypothetical Outflow,Hypothetical Dropoff,Dropoff Reduction\n`;
    result.stages.forEach((st) => {
      csv += `"${st.stageName}",${st.observedInflow},${st.observedOutflow},${st.observedDropoff},${st.hypotheticalInflow},${st.hypotheticalOutflow},${st.hypotheticalDropoff},${st.dropoffReduction}\n`;
    });
    csv += `\nMetric,Observed Baseline Model,Hypothetical Low-Friction Model,Opportunity Difference\n`;
    csv += `Qualified Leads/mo,${result.observedQualifiedLeads},${result.hypotheticalQualifiedLeads},+${(result.hypotheticalQualifiedLeads - result.observedQualifiedLeads).toFixed(1)}\n`;
    csv += `Proposals Sent/mo,${result.observedProposalsSent},${result.hypotheticalProposalsSent},+${(result.hypotheticalProposalsSent - result.observedProposalsSent).toFixed(1)}\n`;
    csv += `Deals Won/mo,${result.observedDealsWon},${result.hypotheticalDealsWon},+${result.potentialWonDealsDifferenceMonthly}\n`;
    csv += `Monthly Revenue,${sym}${result.observedMonthlyRevenue.toLocaleString()},${sym}${result.hypotheticalMonthlyRevenue.toLocaleString()},+${sym}${result.monthlyRevenueOpportunityGap.toLocaleString()}\n`;
    csv += `Annual Revenue,${sym}${result.observedAnnualRevenue.toLocaleString()},${sym}${result.hypotheticalAnnualRevenue.toLocaleString()},+${sym}${result.annualRevenueOpportunityGap.toLocaleString()}\n`;
    csv += `Overall Funnel Efficiency,${result.overallFunnelEfficiencyObservedPct}%,${result.overallFunnelEfficiencyHypotheticalPct}%,+${(result.overallFunnelEfficiencyHypotheticalPct - result.overallFunnelEfficiencyObservedPct).toFixed(1)}%\n`;

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `lead-leakage-audit-${result.currency.toLowerCase()}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    setCopiedSummary(true);
    setTimeout(() => setCopiedSummary(false), 2000);
  };

  const findings: FindingItem[] = useMemo(() => {
    const list: FindingItem[] = [];

    if (inputs.firstResponseTimeHours > 2) {
      list.push({
        id: 'response-delay',
        severity: 'critical',
        title: 'Extended First-Response Window Reduces Intake Qualification',
        description: `Average first response time of ${inputs.firstResponseTimeHours} hours subjects inquiries to significant decay. Empirical speed-to-lead analyses show lead engagement drops substantially after 2 hours.`,
        remedy: 'Deploy automated instant intake acknowledgments, immediate calendar links, or webhook triggers.',
        metric: `${inputs.firstResponseTimeHours} hrs response lag`,
      });
    }

    if (inputs.proposalTurnaroundDays > 2) {
      list.push({
        id: 'proposal-lag',
        severity: 'warning',
        title: 'Multi-Day Proposal Generation Cools Buying Intent',
        description: `Taking ${inputs.proposalTurnaroundDays} days to deliver scopes allows client attention to dissipate or competitor quotes to arrive first.`,
        remedy: 'Standardize project scope templates and interactive agreement links so proposals can be dispatched within 24 hours of discovery.',
        metric: `${inputs.proposalTurnaroundDays} days delivery lag`,
      });
    }

    if (result.monthlyRevenueOpportunityGap > 0) {
      list.push({
        id: 'opportunity-gap',
        severity: 'info',
        title: 'Mathematical Pipeline Opportunity Gap',
        description: `In a hypothetical low-friction model with rapid response and 24-hour turnaround, pipeline capacity yields approximately ${result.potentialWonDealsDifferenceMonthly} additional closed engagements monthly (${result.currencySymbol}${result.monthlyRevenueOpportunityGap.toLocaleString()}/mo).`,
        remedy: 'Review stage conversion rates and replace manual email handoffs with connected workspace intake.',
        metric: `+${result.currencySymbol}${result.monthlyRevenueOpportunityGap.toLocaleString()}/mo potential`,
      });
    }

    return list;
  }, [inputs, result]);

  const faqs = [
    {
      question: 'Does this calculator guarantee additional revenue if I use Cora?',
      answer:
        'No. This diagnostic tool is a deterministic mathematical simulation comparing your observed baseline funnel metrics against a hypothetical low-friction model. Business outcomes depend on service demand, lead source quality, pricing competitiveness, and sales execution. No causal revenue guarantees are made.',
    },
    {
      question: 'How is the first-response decay factor modeled?',
      answer:
        'The engine applies empirical decay curves documented across B2B sales benchmark literature (such as InsideSales and HBR response-time studies). Inquiries responded to within 15 minutes retain baseline qualification rates, while response lags extending beyond 2 to 12 hours apply progressive qualification decay factors down to 45%-30%.',
    },
    {
      question: 'How does proposal turnaround lag affect close rates?',
      answer:
        'Proposals dispatched within 24 hours of discovery capture peak client engagement. For every additional business day of delivery lag beyond day one, the simulation applies a compounding 5% drag on close probability (capped at a 50% drag factor for week-long turnaround delays).',
    },
    {
      question: 'Is my agency intake and financial data stored or transmitted?',
      answer:
        'No. All calculations run strictly in your browser using local client-side JavaScript. No lead counts, deal values, or firmographic details are transmitted to external servers or logged in tracking databases.',
    },
  ];

  const sym = result.currencySymbol;
  const canonicalUrl = 'https://heycora.in/tools/lead-leakage-calculator';
  const metaTitle = 'Website Lead-Leakage Calculator | Inbound Intake & Response Decay Audit';
  const metaDescription =
    'Evaluate how inquiry response delays, intake qualification drop-off, and proposal turnaround lag impact closed revenue with zero signup.';

  return (
    <ToolContainer
      categoryLabel="Studio Operations"
      title="Website Lead-Leakage Calculator"
      subtitle="Evaluate how inquiry response delays, qualification friction, and proposal turnaround lag impact closed revenue. Compare your observed baseline against a low-friction scenario."
      badgeTag="Funnel Diagnostic"
      executionMode="browser-only"
      lastReviewedDate="October 2026"
    >
      <ToolJsonLd
        toolTitle={metaTitle}
        toolDescription={metaDescription}
        canonicalUrl={canonicalUrl}
        categoryName="Studio Operations"
        faqs={faqs}
      />

      {/* ── TOP CONTROLS & CURRENCY BAR ── */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6 pb-4 border-b border-zinc-200/80">
        <div className="flex items-center gap-2 overflow-x-auto scrollbar-none py-0.5">
          <span className="text-xs font-mono font-medium text-zinc-500 uppercase tracking-wider shrink-0">Currency:</span>
          <div className="inline-flex rounded-lg border border-zinc-200 bg-white p-0.5 shadow-2xs shrink-0">
            {(['USD', 'EUR', 'GBP', 'INR'] as ToolCurrency[]).map((cur) => (
              <button
                key={cur}
                type="button"
                onClick={() => handleInputChange('currency', cur)}
                className={`px-2.5 py-1 text-xs font-mono font-semibold rounded-md transition-colors touch-manipulation ${
                  inputs.currency === cur
                    ? 'bg-zinc-900 text-white shadow-2xs'
                    : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100'
                }`}
              >
                {cur} ({CURRENCY_SYMBOLS[cur]})
              </button>
            ))}
          </div>
        </div>

        <div className="flex items-center gap-2 overflow-x-auto scrollbar-none py-0.5">
          <button
            type="button"
            onClick={resetToDefault}
            className="px-3 py-1.5 text-xs font-medium text-zinc-600 hover:text-zinc-900 bg-white hover:bg-zinc-50 border border-zinc-200 rounded-lg shadow-2xs transition-colors flex items-center gap-1.5 shrink-0 touch-manipulation"
          >
            <RotateCcw className="w-3.5 h-3.5 text-zinc-400" />
            <span className="hidden sm:inline">Reset Baseline</span>
          </button>
          <button
            type="button"
            onClick={exportSummaryCsv}
            className="px-3 py-1.5 text-xs font-medium text-zinc-600 hover:text-zinc-900 bg-white hover:bg-zinc-50 border border-zinc-200 rounded-lg shadow-2xs transition-colors flex items-center gap-1.5 shrink-0 touch-manipulation"
          >
            <Download className="w-3.5 h-3.5 text-zinc-400" />
            <span className="hidden sm:inline">{copiedSummary ? 'Exported!' : 'Export CSV'}</span>
          </button>
          <button
            type="button"
            onClick={handlePrint}
            className="px-3 py-1.5 text-xs font-medium text-zinc-600 hover:text-zinc-900 bg-white hover:bg-zinc-50 border border-zinc-200 rounded-lg shadow-2xs transition-colors flex items-center gap-1.5 shrink-0 touch-manipulation"
          >
            <Printer className="w-3.5 h-3.5 text-zinc-400" />
            <span className="hidden sm:inline">Print Audit</span>
          </button>
        </div>
      </div>

      {/* ── MOBILE VIEW SWITCHER TABS (lg:hidden) ── */}
      <ToolMobileViewToggle
        activeTab={mobileTab}
        onTabChange={setMobileTab}
        inputsCount={7}
        resultsBadge={`+${sym}${result.monthlyRevenueOpportunityGap.toLocaleString()}/mo`}
      />

      {/* ── WORKSPACE GRID ── */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
        {/* LEFT COLUMN: INPUT FORM CONTROLS (7 cols) */}
        <div className={`lg:col-span-7 space-y-6 ${mobileTab === 'inputs' ? 'block' : 'hidden lg:block'}`}>
          {/* Preset Buttons (Swipeable on Mobile) */}
          <div className="rounded-2xl border border-zinc-200/90 bg-white p-4 sm:p-6 shadow-xs">
            <div className="flex items-center justify-between gap-2 mb-3">
              <span className="text-xs font-mono font-semibold uppercase tracking-wider text-zinc-500">
                Studio Pipeline Presets
              </span>
              <span className="text-[11px] font-mono text-zinc-400">One-click model setup</span>
            </div>
            <div className="flex overflow-x-auto scrollbar-none gap-2 py-1 -mx-2 px-2 sm:mx-0 sm:px-0 sm:grid sm:grid-cols-3 touch-pan-x">
              {Object.entries(LEAD_LEAKAGE_PRESETS).map(([key, p]) => (
                <button
                  key={key}
                  type="button"
                  onClick={() => applyPreset(key)}
                  className="shrink-0 min-w-[200px] sm:min-w-0 sm:flex-1 px-3 py-2 text-left rounded-xl border border-zinc-200 hover:border-zinc-400 bg-zinc-50 hover:bg-zinc-100 transition-colors text-xs font-medium text-zinc-800 touch-manipulation"
                >
                  <span className="font-semibold block text-zinc-950 truncate">{p.name}</span>
                  <span className="text-[11px] text-zinc-500 line-clamp-1">{p.inputs.monthlyInboundLeads} leads/mo · {sym}{p.inputs.averageDealValue}</span>
                </button>
              ))}
            </div>
          </div>

          {/* Section 1: Inbound Pipeline Baseline */}
          <div className="rounded-2xl border border-zinc-200/90 bg-white p-6 sm:p-8 shadow-xs space-y-6">
            <h2 className="font-display text-lg sm:text-xl font-bold text-zinc-950 tracking-tight pb-3 border-b border-zinc-200/70">
              1. Inbound Inquiries & Deal Size
            </h2>

            <ToolNumberInput
              id="monthlyInboundLeads"
              label="Monthly Inbound Inquiries"
              helpText="Average new website inquiries, contact form submissions, and direct leads per month."
              value={inputs.monthlyInboundLeads}
              onChange={(v) => handleInputChange('monthlyInboundLeads', v)}
              min={1}
              max={1000}
              step={1}
            />

            <ToolNumberInput
              id="averageDealValue"
              label="Average Closed Deal Value"
              helpText="Average contract revenue generated from a single signed client project or initial retainer."
              value={inputs.averageDealValue}
              onChange={(v) => handleInputChange('averageDealValue', v)}
              min={100}
              max={250000}
              step={250}
              prefix={sym}
            />

            <ToolNumberInput
              id="firstResponseTimeHours"
              label="First-Touch Response Time"
              helpText="Average business hours elapsed before a submitted website lead receives personal contact."
              value={inputs.firstResponseTimeHours}
              onChange={(v) => handleInputChange('firstResponseTimeHours', v)}
              min={0.1}
              max={72}
              step={0.5}
              suffix="hours"
            />
          </div>

          {/* Section 2: Conversion & Velocity Gates */}
          <div className="rounded-2xl border border-zinc-200/90 bg-white p-6 sm:p-8 shadow-xs space-y-6">
            <h2 className="font-display text-lg sm:text-xl font-bold text-zinc-950 tracking-tight pb-3 border-b border-zinc-200/70">
              2. Qualification & Proposal Velocity
            </h2>

            <ToolNumberInput
              id="contactQualificationRatePct"
              label="Baseline Lead Qualification Rate"
              helpText="Percentage of inquiries that meet budget, fit, and project scope criteria when reached promptly."
              value={inputs.contactQualificationRatePct}
              onChange={(v) => handleInputChange('contactQualificationRatePct', v)}
              min={1}
              max={100}
              step={1}
              suffix="%"
            />

            <ToolNumberInput
              id="proposalDispatchRatePct"
              label="Proposal Dispatch Rate"
              helpText="Percentage of qualified leads that proceed to receiving a formal proposal or statement of work."
              value={inputs.proposalDispatchRatePct}
              onChange={(v) => handleInputChange('proposalDispatchRatePct', v)}
              min={1}
              max={100}
              step={1}
              suffix="%"
            />

            <ToolNumberInput
              id="proposalTurnaroundDays"
              label="Proposal Turnaround Time"
              helpText="Business days taken between completing discovery and delivering the client proposal."
              value={inputs.proposalTurnaroundDays}
              onChange={(v) => handleInputChange('proposalTurnaroundDays', v)}
              min={0.5}
              max={20}
              step={0.5}
              suffix="days"
            />

            <ToolNumberInput
              id="proposalCloseRatePct"
              label="Baseline Proposal Close Rate"
              helpText="Percentage of delivered proposals that convert into signed agreements and paid deposits."
              value={inputs.proposalCloseRatePct}
              onChange={(v) => handleInputChange('proposalCloseRatePct', v)}
              min={1}
              max={100}
              step={1}
              suffix="%"
            />
          </div>
        </div>

        {/* RIGHT COLUMN: COMPARATIVE AUDIT RESULTS (5 cols) */}
        <div className={`lg:col-span-5 space-y-6 ${mobileTab === 'results' ? 'block' : 'hidden lg:block'}`}>
          {/* Diagnostic Assessment Card */}
          <div className="rounded-2xl border border-zinc-200/90 bg-white p-6 sm:p-8 shadow-xs space-y-6">
            <div className="flex items-center justify-between pb-4 border-b border-zinc-200/70">
              <div>
                <span className="text-xs font-mono font-semibold uppercase tracking-wider text-zinc-500">Diagnostic Result</span>
                <h3 className="text-base font-bold text-zinc-950 mt-0.5">{result.funnelLeakageBand.label}</h3>
              </div>
              <span className="text-xs font-mono px-2.5 py-1 rounded-full bg-zinc-100 text-zinc-700 border border-zinc-200">
                {inputs.currency} Model
              </span>
            </div>

            {/* Side-by-Side Comparative Cards */}
            <div className="space-y-4">
              <div className="p-4 rounded-xl border border-zinc-200 bg-zinc-50 space-y-2">
                <div className="flex items-center justify-between">
                  <span className="text-xs font-mono font-medium text-zinc-500">Observed Baseline Model</span>
                  <Clock className="w-3.5 h-3.5 text-zinc-400" />
                </div>
                <div className="text-2xl font-bold font-mono text-zinc-950">
                  {sym}{result.observedMonthlyRevenue.toLocaleString()}
                  <span className="text-xs text-zinc-500 font-sans font-normal"> /mo</span>
                </div>
                <p className="text-xs text-zinc-600">
                  {result.observedDealsWon} deals won/mo from {inputs.monthlyInboundLeads} inquiries ({result.overallFunnelEfficiencyObservedPct}% overall conversion).
                </p>
                <div className="pt-2 border-t border-zinc-200 text-xs text-zinc-500 font-mono">
                  Annualized: {sym}{result.observedAnnualRevenue.toLocaleString()}
                </div>
              </div>

              <div className="p-4 rounded-xl border border-zinc-200 bg-zinc-50 space-y-2">
                <div className="flex items-center justify-between">
                  <span className="text-xs font-mono font-medium text-zinc-500">Hypothetical Low-Friction Model</span>
                  <FileCheck className="w-3.5 h-3.5 text-zinc-400" />
                </div>
                <div className="text-2xl font-bold font-mono text-zinc-950">
                  {sym}{result.hypotheticalMonthlyRevenue.toLocaleString()}
                  <span className="text-xs text-zinc-500 font-sans font-normal"> /mo</span>
                </div>
                <p className="text-xs text-zinc-600">
                  {result.hypotheticalDealsWon} deals won/mo under sub-15m response & 24h turnaround ({result.overallFunnelEfficiencyHypotheticalPct}% conversion).
                </p>
                <div className="pt-2 border-t border-zinc-200 text-xs text-zinc-500 font-mono">
                  Annualized: {sym}{result.hypotheticalAnnualRevenue.toLocaleString()}
                </div>
              </div>
            </div>

            {/* Simulated Opportunity Gap */}
            <div className="p-4 rounded-xl border border-zinc-200 bg-zinc-100 space-y-1">
              <span className="text-xs font-mono uppercase tracking-wider text-zinc-600 font-semibold">
                Simulated Monthly Opportunity Gap
              </span>
              <div className="text-xl font-bold font-mono text-zinc-950">
                +{sym}{result.monthlyRevenueOpportunityGap.toLocaleString()}/mo
              </div>
              <p className="text-xs text-zinc-600">
                Difference in pipeline conversion attributable to response delay and proposal lag drag factors (+{result.potentialWonDealsDifferenceMonthly} deals/mo).
              </p>
            </div>

            <p className="text-[11px] text-zinc-500 italic">
              Notice: Figures reflect a mathematical comparison between stated inputs and low-friction benchmark equations. They do not constitute a revenue projection or guaranteed outcome.
            </p>
          </div>

          {/* Stage Breakdown Table */}
          <div className="rounded-2xl border border-zinc-200/90 bg-white p-6 shadow-xs space-y-4">
            <h3 className="text-xs font-mono font-semibold uppercase tracking-wider text-zinc-500">
              Stage-by-Stage Funnel Breakdown
            </h3>
            <div className="overflow-x-auto">
              <table className="w-full text-xs text-left">
                <thead className="bg-zinc-50 text-zinc-600 font-mono uppercase border-b border-zinc-200">
                  <tr>
                    <th className="py-2.5 px-3">Funnel Stage</th>
                    <th className="py-2.5 px-2 text-right">Inflow</th>
                    <th className="py-2.5 px-2 text-right">Observed</th>
                    <th className="py-2.5 px-2 text-right">Hypothetical</th>
                    <th className="py-2.5 px-2 text-right">Gain</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-zinc-100 font-mono">
                  {result.stages.map((st, idx) => (
                    <tr key={idx} className="hover:bg-zinc-50/60">
                      <td className="py-2.5 px-3 font-sans text-zinc-800 font-medium">{st.stageName}</td>
                      <td className="py-2.5 px-2 text-right text-zinc-500">{st.observedInflow}</td>
                      <td className="py-2.5 px-2 text-right text-zinc-700">{st.observedOutflow}</td>
                      <td className="py-2.5 px-2 text-right text-zinc-900 font-semibold">{st.hypotheticalOutflow}</td>
                      <td className="py-2.5 px-2 text-right font-semibold text-zinc-950">
                        +{st.dropoffReduction}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>

          {/* Findings List */}
          <ToolFindingsList findings={findings} />
        </div>
      </div>

      {/* ── WORKED EXAMPLE ── */}
      <div className="mb-12">
        <ToolWorkedExample
          scenarioDescription="Below is an illustrative funnel audit of a 6-person boutique creative studio receiving 25 inbound website inquiries per month with an average project contract value of $4,800. Inquiries are checked once daily (6-hour response lag) and proposals require 4 business days to draft."
          inputsTable={[
            { label: 'Monthly Inquiries', value: '25 leads / mo' },
            { label: 'Average Deal Value', value: `${sym}4,800` },
            { label: 'First Response Time', value: '6 hours' },
            { label: 'Proposal Turnaround', value: '4 days' },
          ]}
          steps={[
            {
              step: 'Observed Qualification Flow',
              detail: '25 leads × (40% baseline × 0.60 response decay factor)',
              result: '6.0 qualified leads / mo',
            },
            {
              step: 'Observed Proposal Delivery & Close',
              detail: '6.0 qualified leads × 65% proposal rate = 3.9 proposals. 3.9 × (35% close rate × 0.85 proposal lag factor)',
              result: `1.2 deals won / mo (${sym}5,760/mo)`,
            },
            {
              step: 'Hypothetical Low-Friction Flow',
              detail: '25 leads × 40% = 10.0 qualified leads. 10.0 × 65% = 6.5 proposals. 6.5 × 35% = 2.3 deals won',
              result: `2.3 deals won / mo (${sym}11,040/mo)`,
            },
            {
              step: 'Simulated Pipeline Opportunity Gap',
              detail: `${sym}11,040 hypothetical monthly revenue - ${sym}5,760 observed monthly revenue`,
              result: `+${sym}5,280 / mo opportunity gap (+1.1 closed deals/mo)`,
            },
          ]}
          conclusion="The largest drop-off occurs between initial website inquiry submission and first contact. Shortening initial intake friction and standardizing proposal turnaround can double realized pipeline efficiency without increasing marketing ad spend."
        />
      </div>

      {/* ── METHODOLOGY & FORMULAS ── */}
      <div className="mb-12">
        <ToolMethodologyCard
          methodologyVersion="v1.2 (October 2026)"
          formulas={[
            {
              label: 'Response Time Decay Factor',
              formula: 'Decay_factor = f(Response_hours)',
              explanation: 'Sub-15m = 1.0; 1h = 0.85; 4h = 0.65; 12h = 0.45; 24h+ = 0.30 based on empirical speed-to-lead benchmark curves.',
            },
            {
              label: 'Proposal Delivery Lag Drag',
              formula: 'Drag_prop = max(0.50, 1.0 - (Days_turnaround - 1) × 0.05)',
              explanation: 'Applies a 5% compounding reduction per day beyond 24 hours to model fading client buying intent.',
            },
            {
              label: 'Observed Monthly Revenue',
              formula: 'Revenue_obs = Leads × Qual_rate × Decay_factor × Prop_rate × Close_rate × Drag_prop × Deal_value',
              explanation: 'Simulated realization factoring in both response delay and proposal delivery lag.',
            },
            {
              label: 'Opportunity Gap',
              formula: 'Gap_monthly = Revenue_hypothetical - Revenue_observed',
              explanation: 'Mathematical difference between observed metrics and a low-friction operational benchmark.',
            },
          ]}
          assumptions={[
            'Inbound inquiries experience diminishing qualification probability as first-touch response time increases beyond 15 minutes.',
            'Multi-day lags in scope delivery allow prospect momentum to dissipate and increase competitive bidding exposure.',
            'Hypothetical low-friction model assumes sub-15-minute intake routing and sub-24-hour proposal dispatch at baseline qualification and close rates.',
            'Pure mathematical simulation: Results illustrate pipeline mechanics and opportunity cost; they do not guarantee revenue increases.',
          ]}
          limitations={[
            'Does not account for qualitative service market reputation, referral warmness, or macro pricing competitiveness.',
            'Calculations model linear conversion stages; real-world B2B buying cycles may involve irregular committee approvals.',
          ]}
        />
      </div>

      {/* ── CONTEXTUAL CORA CONVERSION BRIDGE ── */}
      <div className="mb-12">
        <ToolContextualCoraCta
          toolSlug="lead-leakage-calculator"
          badge="The Operational Alternative"
          heading="Connect Website Inquiries Directly to Delivery with Cora"
          description="Cora provides native website enquiry capture, automated qualification routing, integrated proposal builders with tamper-evident e-signatures, and instant retainer billing in one workspace. Rather than stitching disconnected plugins and spreadsheets, inquiries flow directly into structured client delivery."
          bulletPoints={[
            'Native form intake and immediate webhook notifications without third-party form plugins',
            'Interactive client proposals with legally binding digital e-signatures dispatched in minutes',
            'Connected client workspace tracking inquiries, scopes, and contracts in one place',
          ]}
          ctaText="Explore Lead Pipeline Workspace"
          ctaHref="/features/lead-crm"
          secondaryText="Review Plans & Pricing"
          secondaryHref="/pricing"
        />
      </div>

      {/* ── SCHEMA-READY FAQ ACCORDION ── */}
      <div className="mb-12">
        <ToolFaqAccordion items={faqs} />
      </div>

      {/* ── ZERO-SERVER DATA PRIVACY DISCLOSURE ── */}
      <ToolPrivacyDisclosure toolName="Website Lead-Leakage Calculator" />

      {/* ── MOBILE STICKY LIVE SUMMARY BAR (lg:hidden) ── */}
      <ToolMobileBar
        primaryLabel="Simulated Opportunity Gap"
        primaryValue={`+${sym}${result.monthlyRevenueOpportunityGap.toLocaleString()}/mo`}
        secondaryValue={`(+${result.potentialWonDealsDifferenceMonthly} deals/mo)`}
        activeTab={mobileTab}
        onTabChange={setMobileTab}
        onExport={exportSummaryCsv}
      />
    </ToolContainer>
  );
}
