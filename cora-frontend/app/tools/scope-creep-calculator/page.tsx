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
  calculateScopeCreep,
  SCOPE_CREEP_PRESETS,
  ScopeCreepInputs,
} from '@/lib/tools-engines/scope-creep-engine';
import { ToolCurrency, CURRENCY_SYMBOLS } from '@/lib/tools-engines/wp-plugin-cost-engine';
import {
  trackToolStarted,
  trackToolCompleted,
  trackToolResultBand,
} from '@/lib/tools-analytics';
import { Printer, Download, RotateCcw, HelpCircle, Receipt, ArrowRight, ShieldCheck } from 'lucide-react';

export default function ScopeCreepCalculatorPage() {
  const [mobileTab, setMobileTab] = useState<'inputs' | 'results'>('inputs');
  const [inputs, setInputs] = useState<ScopeCreepInputs>({
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
  });

  const [hasInteracted, setHasInteracted] = useState(false);
  const [copiedSummary, setCopiedSummary] = useState(false);

  const result = useMemo(() => {
    return calculateScopeCreep(inputs);
  }, [inputs]);

  useEffect(() => {
    if (hasInteracted) {
      trackToolCompleted({
        tool_slug: 'scope-creep-calculator',
        calculation_mode: 'client_side',
      });
      trackToolResultBand({
        tool_slug: 'scope-creep-calculator',
        band_name: result.creepSeverityBand.label,
        primary_metric_value: result.unbilledLaborCostInternal,
      });
    }
  }, [hasInteracted, result.creepSeverityBand.label, result.unbilledLaborCostInternal]);

  const handleInputChange = (field: keyof ScopeCreepInputs, val: any) => {
    if (!hasInteracted) {
      setHasInteracted(true);
      trackToolStarted({ tool_slug: 'scope-creep-calculator' });
    }
    setInputs((prev) => ({ ...prev, [field]: val }));
  };

  const applyPreset = (presetKey: string) => {
    const preset = SCOPE_CREEP_PRESETS[presetKey];
    if (preset) {
      if (!hasInteracted) {
        setHasInteracted(true);
        trackToolStarted({ tool_slug: 'scope-creep-calculator' });
      }
      setInputs((prev) => ({ ...prev, ...preset.inputs, currency: prev.currency }));
    }
  };

  const resetToDefault = () => {
    applyPreset('web_dev_build');
  };

  const handlePrint = () => {
    if (typeof window !== 'undefined') {
      window.print();
    }
  };

  const exportSummaryCsv = () => {
    const sym = result.currencySymbol;
    let csv = `Metric,Value\n`;
    csv += `Quoted Project Value,${sym}${inputs.quotedProjectValue.toLocaleString()}\n`;
    csv += `Planned Delivery Hours,${inputs.plannedDeliveryHours} hrs\n`;
    csv += `Unbilled Creep Hours,${inputs.unbilledCreepHours} hrs\n`;
    csv += `Total Actual Hours,${result.totalActualHours} hrs\n`;
    csv += `Planned Effective Rate,${sym}${result.plannedEffectiveHourlyRate}/hr\n`;
    csv += `Realized Effective Rate,${sym}${result.realizedEffectiveHourlyRate}/hr\n`;
    csv += `Effective Rate Decay,${result.effectiveRateDecayPct}%\n`;
    csv += `Planned Gross Profit,${sym}${result.plannedGrossProfit.toLocaleString()} (${result.plannedMarginPct}%)\n`;
    csv += `Realized Gross Profit,${sym}${result.realizedGrossProfit.toLocaleString()} (${result.realizedMarginPct}%)\n`;
    csv += `Margin Contraction Points,-${result.marginContractionPoints}%\n`;
    csv += `Internal Cost of Unbilled Labor,${sym}${result.unbilledLaborCostInternal.toLocaleString()}\n`;
    csv += `Suggested Change Order Amount,${sym}${result.suggestedChangeOrderAmount.toLocaleString()}\n`;
    csv += `Annualized Unbilled Labor (${inputs.activeProjectsAnnually} projects),${sym}${result.annualizedUnbilledLaborCost.toLocaleString()}\n`;

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `scope-creep-audit-${result.currency.toLowerCase()}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    setCopiedSummary(true);
    setTimeout(() => setCopiedSummary(false), 2000);
  };

  const findings: FindingItem[] = useMemo(() => {
    const list: FindingItem[] = [];
    const sym = result.currencySymbol;

    if (result.marginContractionPoints >= 15) {
      list.push({
        id: 'margin-erosion',
        severity: 'critical',
        title: 'Severe Margin Contraction Detected',
        description: `Unbilled delivery hours contract your gross profit margin by ${result.marginContractionPoints} percentage points (from ${result.plannedMarginPct}% down to ${result.realizedMarginPct}%), reducing project profit by ${sym}${result.unbilledLaborCostInternal.toLocaleString()}.`,
        remedy: 'Define explicit milestone deliverable bounds in project proposals and trigger change-order addenda for out-of-scope work.',
        metric: `-${result.marginContractionPoints}% Margin Loss`,
      });
    }

    if (result.effectiveRateDecayPct >= 20) {
      list.push({
        id: 'rate-decay',
        severity: 'warning',
        title: 'Effective Hourly Rate Dilution',
        description: `Your effective earning rate dropped from ${sym}${result.plannedEffectiveHourlyRate}/hr to ${sym}${result.realizedEffectiveHourlyRate}/hr due to ${inputs.unbilledCreepHours} unbilled hours.`,
        remedy: 'Set a formal out-of-scope rate (${sym}${inputs.agreedOutofScopeHourlyRate}/hr) in initial contracts and track hours transparently.',
        metric: `${result.effectiveRateDecayPct}% Rate Dilution`,
      });
    }

    if (inputs.actualRevisionRoundsDelivered > inputs.includedRevisionRounds) {
      const extra = inputs.actualRevisionRoundsDelivered - inputs.includedRevisionRounds;
      list.push({
        id: 'extra-revisions',
        severity: 'warning',
        title: `${extra} Unbilled Revision Rounds Delivered`,
        description: `The project included ${inputs.includedRevisionRounds} rounds but required ${inputs.actualRevisionRoundsDelivered}. At standard fee rates, this represents ~${sym}${(extra * inputs.standardRevisionRoundFee).toLocaleString()} in unbilled review time.`,
        remedy: 'Limit proposals to 2 structured feedback rounds; extra rounds require formal client addendum signoff.',
        metric: `+${extra} Extra Rounds`,
      });
    }

    if (list.length === 0) {
      list.push({
        id: 'healthy-scope',
        severity: 'positive',
        title: 'Controlled Scope Boundaries',
        description: 'Your project delivery maintains tight alignment with planned hours and protects planned profit margins.',
        metric: 'Protected Margin',
      });
    }

    return list;
  }, [result, inputs]);

  const canonicalUrl = 'https://heycora.in/tools/scope-creep-calculator';
  const metaTitle = 'Agency Scope-Creep Calculator | Calculate Unbilled Hours & Margin Loss';
  const metaDescription =
    'Calculate the cost of unbilled scope creep, extra client revisions, and effective hourly rate decay. Generate explicit change-order quotes with zero signup.';

  const faqs = [
    {
      question: 'What is agency scope creep?',
      answer:
        'Scope creep occurs when client deliverables, technical features, or revision rounds expand beyond the initial contract or Statement of Work (SOW) without a corresponding budget adjustment or timeline extension.',
    },
    {
      question: 'How is the suggested change-order quote calculated?',
      answer:
        'The suggested change-order amount is calculated using an explicit formula: (Unbilled Creep Hours × Agreed Out-of-Scope Hourly Rate) + (Extra Revision Rounds Beyond Limit × Standard Revision Round Fee). It provides an objective baseline for client discussions.',
    },
    {
      question: 'What is the difference between internal cost and effective hourly rate?',
      answer:
        'Internal hourly cost is what you pay your team (or your burdened cost per hour). Effective hourly rate is the quoted project fee divided by the total hours worked. When scope creeps, your internal cost stays the same while your effective hourly rate drops.',
    },
    {
      question: 'How does Cora help manage scope creep?',
      answer:
        'Cora provides an integrated proposal and document vault with digital e-signatures where original deliverables and revision limits are formally recorded. When a client requests extra work, agencies can issue a structured change-order addendum with 1-click digital client signoff.',
    },
  ];

  const sym = result.currencySymbol;

  return (
    <ToolContainer
      categoryLabel="Agency Margins & Billing"
      title="Agency Scope-Creep & Margin Erosion Calculator"
      subtitle="Calculate how unbilled hours, extra revision rounds, and informal client tweaks erode your effective hourly rate and gross profit margins. Generate explicit change-order estimates."
      badgeTag="Margin Audit Model"
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

      {/* ── TOP CONTROLS BAR ── */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6 pb-4 border-b border-zinc-200/80">
        {/* Currency Selector */}
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

        {/* Actions */}
        <div className="flex items-center gap-2 overflow-x-auto scrollbar-none py-0.5">
          <button
            type="button"
            onClick={resetToDefault}
            className="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-zinc-600 hover:text-zinc-900 bg-white border border-zinc-200 rounded-lg hover:bg-zinc-50 transition-colors shadow-2xs shrink-0 touch-manipulation"
            title="Reset to default values"
          >
            <RotateCcw className="w-3.5 h-3.5" />
            <span className="hidden sm:inline">Reset</span>
          </button>

          <button
            type="button"
            onClick={exportSummaryCsv}
            className="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-zinc-700 hover:text-zinc-950 bg-white border border-zinc-200 rounded-lg hover:bg-zinc-50 transition-colors shadow-2xs shrink-0 touch-manipulation"
          >
            <Download className="w-3.5 h-3.5" />
            <span className="hidden sm:inline">{copiedSummary ? 'Exported!' : 'Export CSV'}</span>
          </button>

          <button
            type="button"
            onClick={handlePrint}
            className="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-zinc-700 hover:text-zinc-950 bg-white border border-zinc-200 rounded-lg hover:bg-zinc-50 transition-colors shadow-2xs shrink-0 touch-manipulation"
          >
            <Printer className="w-3.5 h-3.5" />
            <span className="hidden sm:inline">Print Report</span>
          </button>
        </div>
      </div>

      {/* ── MOBILE VIEW SWITCHER TABS (lg:hidden) ── */}
      <ToolMobileViewToggle
        activeTab={mobileTab}
        onTabChange={setMobileTab}
        inputsCount={8}
        resultsBadge={`${sym}${result.realizedEffectiveHourlyRate}/hr`}
      />

      {/* ── WORKSPACE GRID ── */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
        
        {/* LEFT COLUMN: INPUTS */}
        <div className={`lg:col-span-7 space-y-6 ${mobileTab === 'inputs' ? 'block' : 'hidden lg:block'}`}>
          
          {/* Preset Buttons (Swipeable on Mobile) */}
          <div className="rounded-2xl border border-zinc-200/90 bg-white p-4 sm:p-6 shadow-xs">
            <div className="flex items-center justify-between gap-2 mb-3">
              <span className="text-xs font-mono font-semibold uppercase tracking-wider text-zinc-500">
                Agency Scenario Presets
              </span>
              <span className="text-[11px] font-mono text-zinc-400">One-click model setup</span>
            </div>
            <div className="flex overflow-x-auto scrollbar-none gap-2 py-1 -mx-2 px-2 sm:mx-0 sm:px-0 sm:grid sm:grid-cols-3 touch-pan-x">
              {Object.entries(SCOPE_CREEP_PRESETS).map(([key, p]) => (
                <button
                  key={key}
                  type="button"
                  onClick={() => applyPreset(key)}
                  className="shrink-0 min-w-[200px] sm:min-w-0 sm:flex-1 px-3 py-2 text-left rounded-xl border border-zinc-200 hover:border-zinc-400 bg-zinc-50 hover:bg-zinc-100 transition-colors text-xs font-medium text-zinc-800 touch-manipulation"
                >
                  <span className="font-semibold block text-zinc-950 truncate">{p.name}</span>
                  <span className="text-[11px] text-zinc-500 line-clamp-1">{sym}{p.inputs.quotedProjectValue.toLocaleString()} · {p.inputs.unbilledCreepHours}h creep</span>
                </button>
              ))}
            </div>
          </div>

          {/* Section 1: Quoted Project Economics */}
          <div className="rounded-2xl border border-zinc-200/90 bg-white p-6 sm:p-8 shadow-xs space-y-6">
            <h2 className="font-display text-lg sm:text-xl font-bold text-zinc-950 tracking-tight pb-3 border-b border-zinc-200/70">
              1. Project Contract & Planned Baseline
            </h2>

            <ToolNumberInput
              id="quotedProjectValue"
              label="Quoted Project Contract Fee"
              helpText="Agreed fixed price fee for the project scope (excluding out-of-scope additions)."
              value={inputs.quotedProjectValue}
              onChange={(v) => handleInputChange('quotedProjectValue', v)}
              min={500}
              max={250000}
              step={250}
              prefix={sym}
            />

            <ToolNumberInput
              id="plannedDeliveryHours"
              label="Planned Delivery Hours"
              helpText="Total delivery hours originally scoped and budgeted for the team."
              value={inputs.plannedDeliveryHours}
              onChange={(v) => handleInputChange('plannedDeliveryHours', v)}
              min={5}
              max={500}
              step={5}
              suffix="hours"
            />

            <ToolNumberInput
              id="internalHourlyCost"
              label="Internal Hourly Burdened Cost"
              helpText="Internal labor cost per hour including team compensation, taxes, and operational overhead."
              value={inputs.internalHourlyCost}
              onChange={(v) => handleInputChange('internalHourlyCost', v)}
              min={10}
              max={300}
              step={5}
              prefix={sym}
              suffix="/hr"
            />
          </div>

          {/* Section 2: Scope Expansion & Revisions */}
          <div className="rounded-2xl border border-zinc-200/90 bg-white p-6 sm:p-8 shadow-xs space-y-6">
            <h2 className="font-display text-lg sm:text-xl font-bold text-zinc-950 tracking-tight pb-3 border-b border-zinc-200/70">
              2. Scope Expansion & Revision Overages
            </h2>

            <ToolNumberInput
              id="unbilledCreepHours"
              label="Unbilled Creep Hours"
              helpText="Extra hours logged on out-of-scope tweaks, additional layouts, and informal feature requests."
              value={inputs.unbilledCreepHours}
              onChange={(v) => handleInputChange('unbilledCreepHours', v)}
              min={0}
              max={150}
              step={1}
              suffix="hours"
            />

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <ToolNumberInput
                id="includedRevisionRounds"
                label="Included Revision Rounds"
                helpText="Rounds promised in initial scope."
                value={inputs.includedRevisionRounds}
                onChange={(v) => handleInputChange('includedRevisionRounds', v)}
                min={0}
                max={10}
                step={1}
                suffix="rounds"
              />

              <ToolNumberInput
                id="actualRevisionRoundsDelivered"
                label="Actual Rounds Delivered"
                helpText="Total revision rounds delivered."
                value={inputs.actualRevisionRoundsDelivered}
                onChange={(v) => handleInputChange('actualRevisionRoundsDelivered', v)}
                min={inputs.includedRevisionRounds}
                max={15}
                step={1}
                suffix="rounds"
              />
            </div>
          </div>

          {/* Section 3: Change-Order Pricing Assumptions */}
          <div className="rounded-2xl border border-zinc-200/90 bg-white p-6 sm:p-8 shadow-xs space-y-6">
            <h2 className="font-display text-lg sm:text-xl font-bold text-zinc-950 tracking-tight pb-3 border-b border-zinc-200/70">
              3. Change-Order Pricing Assumptions
            </h2>

            <ToolNumberInput
              id="agreedOutofScopeHourlyRate"
              label="Agreed Out-of-Scope Hourly Rate"
              helpText="Hourly rate to apply to unbilled extra hours when issuing a formal change order."
              value={inputs.agreedOutofScopeHourlyRate}
              onChange={(v) => handleInputChange('agreedOutofScopeHourlyRate', v)}
              min={inputs.internalHourlyCost}
              max={400}
              step={5}
              prefix={sym}
              suffix="/hr"
            />

            <ToolNumberInput
              id="standardRevisionRoundFee"
              label="Standard Fee per Extra Revision Round"
              helpText="Flat fee to charge for full review rounds beyond contract limit."
              value={inputs.standardRevisionRoundFee}
              onChange={(v) => handleInputChange('standardRevisionRoundFee', v)}
              min={0}
              max={2500}
              step={25}
              prefix={sym}
            />

            <ToolNumberInput
              id="activeProjectsAnnually"
              label="Active Client Projects Annually"
              helpText="Total similar projects delivered across your agency per year to model portfolio-wide impact."
              value={inputs.activeProjectsAnnually}
              onChange={(v) => handleInputChange('activeProjectsAnnually', v)}
              min={1}
              max={100}
              step={1}
              suffix="projects/yr"
            />
          </div>

        </div>

        {/* RIGHT COLUMN: CREEP SUMMARY & CHANGE ORDER */}
        <div className={`lg:col-span-5 space-y-6 ${mobileTab === 'results' ? 'block' : 'hidden lg:block'}`}>
          
          <div className="rounded-3xl border border-zinc-900 bg-zinc-950 text-white p-6 sm:p-8 shadow-lg sticky top-24">
            
            <div className="flex items-center justify-between mb-4 border-b border-zinc-800 pb-3">
              <span className="text-xs font-mono font-medium text-zinc-400 uppercase tracking-wider">
                Internal Unbilled Labor Cost
              </span>
              <span className="text-[11px] font-mono px-2 py-0.5 rounded-full bg-zinc-800 text-zinc-300">
                Browser-Only
              </span>
            </div>

            <div className="font-mono text-4xl sm:text-5xl font-bold tracking-tight text-white mb-2">
              {sym}{result.unbilledLaborCostInternal.toLocaleString()}
              <span className="text-sm font-sans font-normal text-zinc-400 block mt-1">
                internal delivery cost of unbilled creep on this project
              </span>
            </div>

            {/* Effective Hourly Rate Comparison */}
            <div className="mt-4 p-3 rounded-xl bg-zinc-900 border border-zinc-800 text-xs font-mono space-y-1.5">
              <div className="flex items-center justify-between text-zinc-300">
                <span>Planned Effective Rate:</span>
                <span className="text-white font-bold">{sym}{result.plannedEffectiveHourlyRate} / hr</span>
              </div>
              <div className="flex items-center justify-between text-zinc-400">
                <span>Realized Effective Rate:</span>
                <span className="text-amber-400 font-bold">{sym}{result.realizedEffectiveHourlyRate} / hr</span>
              </div>
              <div className="text-[11px] text-zinc-500 pt-1 border-t border-zinc-800 flex items-center justify-between">
                <span>Earning Rate Decay:</span>
                <span className="text-rose-400 font-bold">-{result.effectiveRateDecayPct}% dilution</span>
              </div>
            </div>

            {/* Profit Margin Contraction */}
            <div className="mt-4 p-3 rounded-xl bg-zinc-900 border border-zinc-800 text-xs font-mono space-y-1.5">
              <div className="flex items-center justify-between text-zinc-300">
                <span>Planned Gross Margin:</span>
                <span className="text-white font-bold">{result.plannedMarginPct}% ({sym}{result.plannedGrossProfit.toLocaleString()})</span>
              </div>
              <div className="flex items-center justify-between text-zinc-400">
                <span>Realized Gross Margin:</span>
                <span className="text-rose-400 font-bold">{result.realizedMarginPct}% ({sym}{result.realizedGrossProfit.toLocaleString()})</span>
              </div>
              <div className="text-[11px] text-zinc-500 pt-1 border-t border-zinc-800 flex items-center justify-between">
                <span>Margin Contraction:</span>
                <span className="text-rose-400 font-bold">-{result.marginContractionPoints} percentage points</span>
              </div>
            </div>

            {/* Suggested Change Order Callout */}
            <div className="mt-6 pt-6 border-t border-zinc-800">
              <div className="p-4 rounded-2xl bg-emerald-950/60 border border-emerald-800/80 text-emerald-200">
                <span className="text-[11px] font-mono uppercase tracking-wider text-emerald-400 font-bold block mb-1">
                  Suggested Change-Order Quote
                </span>
                <div className="font-mono text-2xl font-bold text-emerald-300 mb-2">
                  {sym}{result.suggestedChangeOrderAmount.toLocaleString()}
                </div>
                <p className="text-xs text-emerald-300/90 leading-relaxed mb-3">
                  Based on {inputs.unbilledCreepHours} unbilled hours @ {sym}{inputs.agreedOutofScopeHourlyRate}/hr plus {Math.max(0, inputs.actualRevisionRoundsDelivered - inputs.includedRevisionRounds)} extra revisions @ {sym}{inputs.standardRevisionRoundFee}/round.
                </p>
                <div className="text-[11px] font-mono text-emerald-400/80 pt-2 border-t border-emerald-800/60 flex items-center justify-between">
                  <span>Portfolio Recovery Potential:</span>
                  <span className="font-bold text-emerald-200">{sym}{result.annualizedPotentialChangeOrderRecovery.toLocaleString()} / yr</span>
                </div>
              </div>
            </div>

          </div>

          <ToolFindingsList findings={findings} />

        </div>

      </div>

      {/* ── 3. WORKED STEP-BY-STEP EXAMPLE ── */}
      <div className="mb-12">
        <ToolWorkedExample
          scenarioDescription="A design and development studio contracts a custom client web application at a fixed price of $9,500. The project is planned for 80 hours with an internal cost of $55/hr and 2 included revision rounds. The client requests 24 hours of out-of-scope tweaks and 3 additional revision rounds."
          inputsTable={[
            { label: 'Contract Fee', value: `${sym}9,500` },
            { label: 'Planned Hours', value: '80 hrs' },
            { label: 'Unbilled Creep', value: '24 hrs' },
            { label: 'Internal Cost', value: `${sym}55 / hr` },
          ]}
          steps={[
            {
              step: 'Calculate Total Delivery Hours',
              detail: '80 planned hours + 24 unbilled creep hours',
              result: '104 total hours',
            },
            {
              step: 'Calculate Effective Hourly Rate Decay',
              detail: `${sym}9,500 ÷ 104 total hours vs ${sym}9,500 ÷ 80 planned hours`,
              result: `${sym}91/hr (down from ${sym}119/hr, -24%)`,
            },
            {
              step: 'Calculate Gross Profit Margin Contraction',
              detail: `Planned margin was 54% (${sym}5,100 profit); realized margin is 40% (${sym}3,780 profit)`,
              result: '-14 percentage points lost',
            },
            {
              step: 'Calculate Suggested Change Order Quote',
              detail: `(24 creep hours × ${sym}95 out-of-scope rate) + (3 extra revisions × ${sym}350/round)`,
              result: `${sym}3,330 suggested change order`,
            },
          ]}
          conclusion="By establishing formal change-order protocols before commencing out-of-scope revisions, this studio preserves its 54% profit margin and recovers up to $3,330 in authorized project billings."
        />
      </div>

      {/* ── 4. TRANSPARENT METHODOLOGY & FORMULAS ── */}
      <div className="mb-12">
        <ToolMethodologyCard
          methodologyVersion="v1.2 (October 2026)"
          formulas={[
            {
              label: 'Realized Effective Hourly Rate',
              formula: 'Rate_realized = Fee_quoted / (Hours_planned + Hours_creep)',
              explanation: 'The actual dollar earnings generated per delivery hour after absorbing unbilled client revisions.',
            },
            {
              label: 'Gross Margin Contraction',
              formula: 'Contraction = ((Fee - (Hours_planned × Cost)) / Fee) - ((Fee - (Hours_actual × Cost)) / Fee)',
              explanation: 'Direct contraction in project operating margin resulting from uncompensated delivery time.',
            },
            {
              label: 'Suggested Change Order Formula',
              formula: 'Quote_change_order = (Hours_creep × Rate_agreed) + (Revisions_extra × Fee_revision)',
              explanation: 'Explicit mathematical basis for billing out-of-scope additions fairly and objectively.',
            },
          ]}
          assumptions={[
            'Internal hourly cost reflects fully loaded team compensation, payroll taxes, and baseline agency operational overhead.',
            'Unbilled hours represent productive working capacity that could otherwise generate billable client revenue.',
          ]}
          limitations={[
            'Does not measure client relationship goodwill that may occasionally lead to long-term retainer renewals.',
            'Fixed-price milestone contracts are modeled against internal time tracking.',
          ]}
        />
      </div>

      {/* ── 5. CONTEXTUAL CORA CONVERSION BRIDGE ── */}
      <div className="mb-12">
        <ToolContextualCoraCta
          toolSlug="scope-creep-calculator"
          badge="The SOW Protection Alternative"
          heading="Lock Deliverables in E-Sign. Issue Structured Change Orders in 1 Click."
          description="Cora provides an integrated proposal and document vault where deliverables, milestone scopes, and revision limits are formally recorded with digital e-signatures. When clients ask for extra revisions or new features, generate an interactive change-order addendum with 1-click client signoff."
          bulletPoints={[
            'Record explicit deliverable scopes in digital proposals with legal e-signatures',
            'Issue 1-click change-order addenda when out-of-scope requests arise',
            'Track client revision rounds and milestone approvals in a unified workspace',
          ]}
          ctaText="Explore Cora Features"
          ctaHref="/features"
          secondaryText="Review Documentation"
          secondaryHref="/docs"
        />
      </div>

      {/* ── 6. SCHEMA-READY FAQ ACCORDION ── */}
      <div className="mb-12">
        <ToolFaqAccordion items={faqs} />
      </div>

      {/* ── 7. ZERO-SERVER DATA PRIVACY DISCLOSURE ── */}
      <ToolPrivacyDisclosure toolName="Agency Scope-Creep Calculator" />

      {/* ── 8. MOBILE STICKY LIVE SUMMARY BAR (lg:hidden) ── */}
      <ToolMobileBar
        primaryLabel="Realized Effective Rate"
        primaryValue={`${sym}${result.realizedEffectiveHourlyRate}/hr`}
        secondaryValue={`(-${result.effectiveRateDecayPct}% decay)`}
        activeTab={mobileTab}
        onTabChange={setMobileTab}
        onExport={exportSummaryCsv}
      />

    </ToolContainer>
  );
}
