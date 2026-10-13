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
  calculateWpPluginCost,
  WP_PLUGIN_PRESETS,
  WpPluginCostInputs,
  ToolCurrency,
  CURRENCY_SYMBOLS,
} from '@/lib/tools-engines/wp-plugin-cost-engine';
import {
  trackToolStarted,
  trackToolCompleted,
  trackToolResultBand,
} from '@/lib/tools-analytics';
import { Printer, Download, RotateCcw, Sparkles, Check, HelpCircle, Layers, ShieldCheck, ToggleLeft, ToggleRight, DollarSign } from 'lucide-react';

export default function WpPluginCostCalculatorPage() {
  const [mobileTab, setMobileTab] = useState<'inputs' | 'results'>('inputs');
  const [inputs, setInputs] = useState<WpPluginCostInputs>({
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
  });

  const [viewCadence, setViewCadence] = useState<'annual' | 'monthly'>('annual');
  const [showFragilityDetails, setShowFragilityDetails] = useState<boolean>(true);
  const [hasInteracted, setHasInteracted] = useState(false);
  const [copiedSummary, setCopiedSummary] = useState(false);

  const result = useMemo(() => {
    return calculateWpPluginCost(inputs);
  }, [inputs]);

  useEffect(() => {
    if (hasInteracted) {
      trackToolCompleted({
        tool_slug: 'wp-plugin-cost-calculator',
        calculation_mode: 'client_side',
      });
      trackToolResultBand({
        tool_slug: 'wp-plugin-cost-calculator',
        band_name: result.fragilityBand.label,
        primary_metric_value: result.totalAnnualTco,
      });
    }
  }, [hasInteracted, result.fragilityBand.label, result.totalAnnualTco]);

  const handleInputChange = (field: keyof WpPluginCostInputs, val: any) => {
    if (!hasInteracted) {
      setHasInteracted(true);
      trackToolStarted({ tool_slug: 'wp-plugin-cost-calculator' });
    }
    setInputs((prev) => ({ ...prev, [field]: val }));
  };

  const applyPreset = (presetKey: string) => {
    const preset = WP_PLUGIN_PRESETS[presetKey];
    if (preset) {
      if (!hasInteracted) {
        setHasInteracted(true);
        trackToolStarted({ tool_slug: 'wp-plugin-cost-calculator' });
      }
      setInputs((prev) => ({ ...prev, ...preset.inputs, currency: prev.currency }));
    }
  };

  const resetToDefault = () => {
    applyPreset('creative_agency');
  };

  const handlePrint = () => {
    if (typeof window !== 'undefined') {
      window.print();
    }
  };

  const exportSummaryCsv = () => {
    const sym = result.currencySymbol;
    let csv = `Category,Description,Monthly (${sym}),Annual (${sym}),% of Total,Labor\n`;
    result.itemizedBreakdown.forEach((row) => {
      csv += `"${row.category}","${row.description}",${row.monthlyAmount},${row.annualAmount},${row.percentOfTotal}%,${row.isLabor ? 'Yes' : 'No'}\n`;
    });
    csv += `\nTotal Annual TCO,${sym}${result.totalAnnualTco.toLocaleString()}\n`;
    csv += `Total Monthly Equivalent,${sym}${result.totalMonthlyTco.toLocaleString()}\n`;
    csv += `3-Year Projected TCO,${sym}${result.threeYearProjectedTco.toLocaleString()}\n`;
    csv += `Stack Fragility Heuristic,${result.fragilityScore}/100 (${result.fragilityBand.label})\n`;

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `wp-plugin-cost-audit-${result.currency.toLowerCase()}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    setCopiedSummary(true);
    setTimeout(() => setCopiedSummary(false), 2000);
  };

  const findings: FindingItem[] = useMemo(() => {
    const list: FindingItem[] = [];
    const sym = result.currencySymbol;

    if (inputs.includeLaborCosts && result.totalLaborCostAnnual > result.directLicenseCostAnnual) {
      list.push({
        id: 'hidden-labor',
        severity: 'critical',
        title: 'Maintenance Labor Exceeds Direct License Costs',
        description: `Engineering time spent staging updates, visual testing, and troubleshooting plugin conflicts (${sym}${result.totalLaborCostAnnual.toLocaleString()}/yr) is higher than the direct software renewal fees (${sym}${result.directLicenseCostAnnual.toLocaleString()}/yr).`,
        remedy: 'Consider consolidating separate form, proposal, e-sign, and client portal plugins into unified business software, while retaining WordPress for marketing frontend themes.',
        metric: `${sym}${result.totalLaborCostAnnual.toLocaleString()}/yr Labor`,
      });
    }

    if (inputs.premiumPluginsCount >= 18) {
      list.push({
        id: 'plugin-density',
        severity: 'warning',
        title: 'High Dependency Density',
        description: `With ${inputs.premiumPluginsCount} active commercial plugins, update conflicts and PHP version regression probabilities increase non-linearly.`,
        remedy: 'Audit active plugins quarterly to decommission single-feature utility add-ons.',
        metric: `${inputs.premiumPluginsCount} Plugins`,
      });
    }

    if (result.downtimeRiskExposureAnnual > 1000) {
      list.push({
        id: 'downtime-exposure',
        severity: 'warning',
        title: 'Downtime Pipeline Exposure',
        description: `Estimated update failure downtime exposes approximately ${sym}${result.downtimeRiskExposureAnnual.toLocaleString()}/yr in pipeline disruption.`,
        remedy: 'Decouple client lead capture and contract signoff workflows from WordPress core database updates.',
        metric: `${sym}${result.downtimeRiskExposureAnnual.toLocaleString()} Exposure`,
      });
    }

    if (list.length === 0) {
      list.push({
        id: 'healthy-stack',
        severity: 'positive',
        title: 'Lean & Contained Plugin Architecture',
        description: 'Your WordPress plugin configuration maintains manageable labor overhead and low dependency density.',
        metric: 'Low Overhead',
      });
    }

    return list;
  }, [result, inputs]);

  const canonicalUrl = 'https://heycora.in/tools/wp-plugin-cost-calculator';
  const metaTitle = 'WordPress Plugin Stack Cost Calculator | Calculate Annual TCO & Labor';
  const metaDescription =
    'Audit the true annual Total Cost of Ownership of your WordPress plugin stack. Calculate direct license renewals, hosting, optional maintenance labor, and update fragility.';

  const faqs = [
    {
      question: 'What is the Total Cost of Ownership (TCO) of a WordPress plugin stack?',
      answer:
        'The Total Cost of Ownership includes direct commercial plugin renewal subscriptions, managed hosting/staging infrastructure, routine developer maintenance hours (testing updates on staging, checking visual regressions), emergency incident debugging, and potential revenue loss during unexpected plugin update crashes.',
    },
    {
      question: 'Why can developer labor exceed direct plugin license fees?',
      answer:
        'When multiple complex plugins coexist (e.g. Elementor, WooCommerce, form builders, membership tools), every core update or PHP upgrade requires compatibility testing. A developer spending just 4 hours per month at $85/hr equals $4,080/yr in labor—often 2x to 3x the direct software subscription cost.',
    },
    {
      question: 'Can I calculate pure software costs without developer labor?',
      answer:
        'Yes. Use the "Include Engineering & Maintenance Labor" toggle switch above to view pure direct software and hosting expenses, or include labor for full operational visibility.',
    },
    {
      question: 'How does Cora help reduce WordPress plugin sprawl?',
      answer:
        'Cora provides native form intake, e-signatures, GST invoices, and client portals in a connected business workspace. Agencies can keep their existing WordPress marketing site while decoupling critical business workflows from fragile third-party PHP plugins.',
    },
    {
      question: 'Are my entered figures stored or transmitted to any server?',
      answer:
        'No. This tool operates 100% in-browser using local JavaScript. No entered numbers, financial figures, or site details are sent to Cora servers or logged in telemetry.',
    },
  ];

  const sym = result.currencySymbol;

  return (
    <ToolContainer
      categoryLabel="WordPress & Stack Health"
      title="WordPress Plugin Stack Cost & TCO Calculator"
      subtitle="Audit the full Total Cost of Ownership of your WordPress plugin stack. Calculate direct license renewals, hosting infrastructure, optional maintenance labor, and update fragility risks."
      badgeTag="TCO Diagnostic Model"
      executionMode="browser-only"
      lastReviewedDate="October 2026"
    >
      <ToolJsonLd
        toolTitle={metaTitle}
        toolDescription={metaDescription}
        canonicalUrl={canonicalUrl}
        categoryName="WordPress & Stack Health"
        faqs={faqs}
      />

      {/* ── TOP CONTROLS & CURRENCY BAR ── */}
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

        {/* View Cadence Toggle & Actions */}
        <div className="flex items-center gap-2 overflow-x-auto scrollbar-none py-0.5">
          {/* Monthly / Annual Toggle */}
          <div className="inline-flex rounded-lg border border-zinc-200 bg-white p-0.5 shadow-2xs shrink-0">
            <button
              type="button"
              onClick={() => setViewCadence('annual')}
              className={`px-3 py-1 text-xs font-semibold rounded-md transition-colors touch-manipulation ${
                viewCadence === 'annual' ? 'bg-zinc-900 text-white shadow-2xs' : 'text-zinc-600 hover:text-zinc-900'
              }`}
            >
              Annual View
            </button>
            <button
              type="button"
              onClick={() => setViewCadence('monthly')}
              className={`px-3 py-1 text-xs font-semibold rounded-md transition-colors touch-manipulation ${
                viewCadence === 'monthly' ? 'bg-zinc-900 text-white shadow-2xs' : 'text-zinc-600 hover:text-zinc-900'
              }`}
            >
              Monthly View
            </button>
          </div>

          {/* Reset Action */}
          <button
            type="button"
            onClick={resetToDefault}
            className="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-zinc-600 hover:text-zinc-900 bg-white border border-zinc-200 rounded-lg hover:bg-zinc-50 transition-colors shadow-2xs shrink-0 touch-manipulation"
            title="Reset to default values"
          >
            <RotateCcw className="w-3.5 h-3.5" />
            <span className="hidden sm:inline">Reset</span>
          </button>

          {/* Export / Print Actions */}
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
            <span className="hidden sm:inline">Print</span>
          </button>
        </div>
      </div>

      {/* ── MOBILE VIEW SWITCHER TABS (lg:hidden) ── */}
      <ToolMobileViewToggle
        activeTab={mobileTab}
        onTabChange={setMobileTab}
        inputsCount={inputs.includeLaborCosts ? 8 : 4}
        resultsBadge={`${sym}${viewCadence === 'annual' ? result.totalAnnualTco.toLocaleString() : result.totalMonthlyTco.toLocaleString()}`}
      />

      {/* ── WORKSPACE GRID ── */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
        
        {/* LEFT COLUMN: CONTROLS & INPUTS (7 cols) */}
        <div className={`lg:col-span-7 space-y-6 ${mobileTab === 'inputs' ? 'block' : 'hidden lg:block'}`}>
          
          {/* Preset Buttons Bar (Swipeable on Mobile) */}
          <div className="rounded-2xl border border-zinc-200/90 bg-white p-4 sm:p-6 shadow-xs">
            <div className="flex items-center justify-between gap-2 mb-3">
              <span className="text-xs font-mono font-semibold uppercase tracking-wider text-zinc-500">
                Architectural Baseline Presets
              </span>
              <span className="text-[11px] font-mono text-zinc-400">One-click setup</span>
            </div>
            <div className="flex overflow-x-auto scrollbar-none gap-2 py-1 -mx-2 px-2 sm:mx-0 sm:px-0 sm:grid sm:grid-cols-3 touch-pan-x">
              {Object.entries(WP_PLUGIN_PRESETS).map(([key, p]) => (
                <button
                  key={key}
                  type="button"
                  onClick={() => applyPreset(key)}
                  className="shrink-0 min-w-[200px] sm:min-w-0 sm:flex-1 px-3 py-2 text-left rounded-xl border border-zinc-200 hover:border-zinc-400 bg-zinc-50 hover:bg-zinc-100 transition-colors text-xs font-medium text-zinc-800 touch-manipulation"
                >
                  <span className="font-semibold block text-zinc-950 truncate">{p.name}</span>
                  <span className="text-[11px] text-zinc-500 line-clamp-1">{p.inputs.premiumPluginsCount} plugins · {sym}{p.inputs.developerHourlyRate}/hr</span>
                </button>
              ))}
            </div>
          </div>

          {/* Section 1: Software & Hosting Subscriptions */}
          <div className="rounded-2xl border border-zinc-200/90 bg-white p-6 sm:p-8 shadow-xs space-y-6">
            <h2 className="font-display text-lg sm:text-xl font-bold text-zinc-950 tracking-tight pb-3 border-b border-zinc-200/70">
              1. Commercial Plugins & Hosting
            </h2>

            <ToolNumberInput
              id="premiumPluginsCount"
              label="Active Paid / Commercial Plugins"
              helpText="Number of commercial plugins requiring annual renewals (e.g. ACF Pro, Gravity Forms, Elementor Pro, WP Rocket)."
              value={inputs.premiumPluginsCount}
              onChange={(v) => handleInputChange('premiumPluginsCount', v)}
              min={0}
              max={60}
              step={1}
            />

            <ToolNumberInput
              id="avgAnnualLicensePerPlugin"
              label="Average Annual Renewal Cost per Plugin"
              helpText="Average annual license fee across commercial plugins."
              value={inputs.avgAnnualLicensePerPlugin}
              onChange={(v) => handleInputChange('avgAnnualLicensePerPlugin', v)}
              min={0}
              max={1500}
              step={10}
              prefix={sym}
            />

            <ToolNumberInput
              id="monthlyHostingCost"
              label="Monthly Managed Hosting & Staging"
              helpText="Hosting infrastructure fee (WP Engine, Kinsta, Cloudways, VPS) including staging environment."
              value={inputs.monthlyHostingCost}
              onChange={(v) => handleInputChange('monthlyHostingCost', v)}
              min={0}
              max={2000}
              step={5}
              prefix={sym}
              suffix="/mo"
            />
          </div>

          {/* Section 2: Developer Labor & Maintenance */}
          <div className="rounded-2xl border border-zinc-200/90 bg-white p-6 sm:p-8 shadow-xs space-y-6">
            <div className="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-zinc-200/70">
              <h2 className="font-display text-lg sm:text-xl font-bold text-zinc-950 tracking-tight">
                2. Developer & Maintenance Labor
              </h2>
              {/* Labor Toggle */}
              <button
                type="button"
                onClick={() => handleInputChange('includeLaborCosts', !inputs.includeLaborCosts)}
                className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold transition-colors ${
                  inputs.includeLaborCosts
                    ? 'bg-emerald-50 text-emerald-800 border border-emerald-200'
                    : 'bg-zinc-100 text-zinc-600 border border-zinc-200'
                }`}
              >
                <span>{inputs.includeLaborCosts ? 'Labor Costs Included' : 'Labor Costs Excluded'}</span>
              </button>
            </div>

            {inputs.includeLaborCosts ? (
              <>
                <ToolNumberInput
                  id="monthlyMaintenanceHours"
                  label="Routine Monthly Maintenance & Update Testing"
                  helpText="Hours per month spent testing plugin updates on staging, visual verification, and cache clearing."
                  value={inputs.monthlyMaintenanceHours}
                  onChange={(v) => handleInputChange('monthlyMaintenanceHours', v)}
                  min={0}
                  max={40}
                  step={0.5}
                  suffix="hrs/mo"
                />

                <ToolNumberInput
                  id="developerHourlyRate"
                  label="Blended Developer / Agency Hourly Rate"
                  helpText="Engineering billing or internal cost rate for maintenance and troubleshooting."
                  value={inputs.developerHourlyRate}
                  onChange={(v) => handleInputChange('developerHourlyRate', v)}
                  min={10}
                  max={400}
                  step={5}
                  prefix={sym}
                  suffix="/hr"
                />

                <ToolNumberInput
                  id="majorUpdateIncidentsPerYear"
                  label="Major Update Conflict Incidents Annually"
                  helpText="Times per year a plugin update triggers critical errors, checkout malfunction, or broken layouts."
                  value={inputs.majorUpdateIncidentsPerYear}
                  onChange={(v) => handleInputChange('majorUpdateIncidentsPerYear', v)}
                  min={0}
                  max={12}
                  step={1}
                  suffix="incidents/yr"
                />

                <ToolNumberInput
                  id="avgHoursPerIncident"
                  label="Average Labor per Conflict Incident"
                  helpText="Hours required to roll back, isolate plugin conflict, test patch, and redeploy."
                  value={inputs.avgHoursPerIncident}
                  onChange={(v) => handleInputChange('avgHoursPerIncident', v)}
                  min={1}
                  max={24}
                  step={0.5}
                  suffix="hrs"
                />
              </>
            ) : (
              <div className="p-4 rounded-xl bg-zinc-50 border border-zinc-200 text-xs text-zinc-600">
                <p>
                  Developer and staging labor costs are currently excluded. Click the toggle above to include engineering maintenance overhead in the TCO calculation.
                </p>
              </div>
            )}
          </div>

          {/* Section 3: Editable Assumptions & Business Context */}
          <div className="rounded-2xl border border-zinc-200/90 bg-white p-6 sm:p-8 shadow-xs space-y-6">
            <h2 className="font-display text-lg sm:text-xl font-bold text-zinc-950 tracking-tight pb-3 border-b border-zinc-200/70">
              3. Assumptions & Revenue Exposure
            </h2>

            <ToolNumberInput
              id="annualRenewalInflationPct"
              label="Estimated Annual Subscription Inflation"
              helpText="Expected annual price increase on commercial plugin renewals (typically 5% to 15%)."
              value={inputs.annualRenewalInflationPct}
              onChange={(v) => handleInputChange('annualRenewalInflationPct', v)}
              min={0}
              max={25}
              step={1}
              suffix="%"
            />

            <ToolNumberInput
              id="annualRevenueDependentOnSite"
              label="Annual Revenue Dependent on Website"
              helpText="Annual client project pipeline or transaction value driven through website inquiries."
              value={inputs.annualRevenueDependentOnSite}
              onChange={(v) => handleInputChange('annualRevenueDependentOnSite', v)}
              min={0}
              max={10000000}
              step={10000}
              prefix={sym}
            />

            <ToolNumberInput
              id="estimatedDowntimeHoursPerYear"
              label="Estimated Breakage / Form Outage Hours per Year"
              helpText="Cumulative hours where form submissions, booking links, or layouts fail due to plugin conflicts."
              value={inputs.estimatedDowntimeHoursPerYear}
              onChange={(v) => handleInputChange('estimatedDowntimeHoursPerYear', v)}
              min={0}
              max={48}
              step={0.5}
              suffix="hours"
            />
          </div>

        </div>

        {/* RIGHT COLUMN: REAL-TIME AUDIT SUMMARY (5 cols) */}
        <div className={`lg:col-span-5 space-y-6 ${mobileTab === 'results' ? 'block' : 'hidden lg:block'}`}>
          
          {/* Main TCO Summary Card */}
          <div className="rounded-3xl border border-zinc-900 bg-zinc-950 text-white p-6 sm:p-8 shadow-lg sticky top-24">
            
            <div className="flex items-center justify-between mb-4 border-b border-zinc-800 pb-3">
              <span className="text-xs font-mono font-medium text-zinc-400 uppercase tracking-wider">
                {viewCadence === 'annual' ? 'Total Annual Stack TCO' : 'Monthly Equivalent TCO'}
              </span>
              <span className="text-[11px] font-mono px-2 py-0.5 rounded-full bg-zinc-800 text-zinc-300">
                Browser-Only
              </span>
            </div>

            <div className="font-mono text-4xl sm:text-5xl font-bold tracking-tight text-white mb-2">
              {sym}
              {viewCadence === 'annual'
                ? result.totalAnnualTco.toLocaleString()
                : result.totalMonthlyTco.toLocaleString()}
              <span className="text-sm font-sans font-normal text-zinc-400 block mt-1">
                {viewCadence === 'annual'
                  ? 'estimated annual total cost of ownership'
                  : 'estimated monthly equivalent operating cost'}
              </span>
            </div>

            {/* 3-Year Projection Pill */}
            <div className="mt-4 p-3 rounded-xl bg-zinc-900 border border-zinc-800 text-xs text-zinc-300 flex items-center justify-between">
              <span>3-Year Cumulative TCO:</span>
              <span className="font-mono font-bold text-white text-sm">
                {sym}{result.threeYearProjectedTco.toLocaleString()}
              </span>
            </div>

            {/* Itemized Cost Breakdown Table */}
            <div className="mt-6 pt-6 border-t border-zinc-800">
              <span className="text-xs font-mono uppercase tracking-wider text-zinc-400 block mb-3 font-semibold">
                Itemized Cost Breakdown ({viewCadence === 'annual' ? 'Annual' : 'Monthly'})
              </span>
              <div className="space-y-2 text-xs font-mono">
                {result.itemizedBreakdown.map((row, idx) => {
                  const val = viewCadence === 'annual' ? row.annualAmount : row.monthlyAmount;
                  return (
                    <div key={idx} className="p-2.5 rounded-lg bg-zinc-900 border border-zinc-800/80">
                      <div className="flex items-center justify-between text-white font-semibold">
                        <span className="font-sans text-xs text-zinc-300 truncate pr-2">{row.category}</span>
                        <span>{sym}{val.toLocaleString()}</span>
                      </div>
                      <div className="flex items-center justify-between text-[11px] text-zinc-400 mt-1">
                        <span className="truncate pr-2">{row.description}</span>
                        <span className="shrink-0 text-zinc-500">{row.percentOfTotal}%</span>
                      </div>
                    </div>
                  );
                })}
              </div>
            </div>

            {/* Optional Fragility Heuristic Card */}
            <div className="mt-6 pt-6 border-t border-zinc-800">
              <div className="flex items-center justify-between mb-2">
                <span className="text-xs font-mono text-zinc-400">Cora Fragility Heuristic (v1.2):</span>
                <button
                  type="button"
                  onClick={() => setShowFragilityDetails(!showFragilityDetails)}
                  className="text-xs font-mono font-bold text-amber-400 hover:underline"
                >
                  {result.fragilityScore}/100 ({result.fragilityBand.label})
                </button>
              </div>
              <div className="w-full bg-zinc-800 h-2 rounded-full overflow-hidden">
                <div
                  className={`h-full rounded-full transition-all duration-300 ${
                    result.fragilityScore >= 70
                      ? 'bg-rose-500'
                      : result.fragilityScore >= 35
                      ? 'bg-amber-400'
                      : 'bg-emerald-500'
                  }`}
                  style={{ width: `${result.fragilityScore}%` }}
                />
              </div>
              {showFragilityDetails && (
                <p className="text-[11px] text-zinc-400 mt-2 leading-relaxed">
                  {result.fragilityBand.description}
                </p>
              )}
            </div>

          </div>

          {/* Diagnostic Findings List */}
          <ToolFindingsList findings={findings} />

        </div>

      </div>

      {/* ── 3. WORKED STEP-BY-STEP EXAMPLE ── */}
      <div className="mb-12">
        <ToolWorkedExample
          scenarioDescription="Below is an illustrative audit of a creative agency running WordPress with Elementor Pro, Advanced Custom Fields Pro, Gravity Forms, WPML, and 8 other commercial utility plugins with managed hosting and monthly developer support."
          inputsTable={[
            { label: 'Commercial Plugins', value: '12 plugins' },
            { label: 'Avg Renewal', value: `${sym}149 / yr` },
            { label: 'Hosting & Staging', value: `${sym}65 / mo` },
            { label: 'Dev Maintenance', value: `4 hrs / mo (${sym}85/hr)` },
          ]}
          steps={[
            {
              step: 'Calculate Direct Plugin Renewals',
              detail: `12 plugins × ${sym}149 average annual license renewal`,
              result: `${sym}1,788 / yr`,
            },
            {
              step: 'Calculate Managed Hosting & Staging',
              detail: `${sym}65/mo × 12 months`,
              result: `${sym}780 / yr`,
            },
            {
              step: 'Calculate Routine Maintenance Labor',
              detail: `4 hours/mo × 12 months × ${sym}85/hr developer rate`,
              result: `${sym}4,080 / yr`,
            },
            {
              step: 'Calculate Update Incident & Conflict Debugging',
              detail: `3 incidents/yr × 4 hours recovery × ${sym}85/hr`,
              result: `${sym}1,020 / yr`,
            },
            {
              step: 'Sum Total Annual Total Cost of Ownership',
              detail: `${sym}1,788 (plugins) + ${sym}780 (hosting) + ${sym}4,080 (routine) + ${sym}1,020 (incidents)`,
              result: `${sym}7,668 / yr`,
            },
          ]}
          conclusion="In this scenario, direct software licensing represents only 23% of total cost, while engineering maintenance labor accounts for over 66% of the annual burden."
        />
      </div>

      {/* ── 4. TRANSPARENT METHODOLOGY & FORMULAS ── */}
      <div className="mb-12">
        <ToolMethodologyCard
          methodologyVersion="v1.2 (October 2026)"
          formulas={[
            {
              label: 'Direct License Total',
              formula: 'Cost_direct = N_plugins × Price_avg_license',
              explanation: 'Sum of all commercial recurring subscriptions for themes, page builders, and utility plugins.',
            },
            {
              label: 'Routine Maintenance Labor',
              formula: 'Cost_routine = Hours_monthly × 12 × Rate_hourly',
              explanation: 'Developer time allocated to testing staging updates, reviewing visual regressions, and updating databases.',
            },
            {
              label: 'Incident Recovery Labor',
              formula: 'Cost_incidents = Incidents_yr × Hours_per_incident × Rate_hourly',
              explanation: 'Labor spent resolving production crashes, plugin incompatibilities, and PHP deprecation errors.',
            },
            {
              label: 'Downtime Exposure',
              formula: 'Cost_downtime = (Revenue_annual / (365 × 8)) × Hours_downtime',
              explanation: 'Estimated business pipeline exposure during website crashes, form capture failures, or checkout outages.',
            },
          ]}
          assumptions={[
            'Plugin licenses renew annually at standard commercial rates without multi-site bulk discounts.',
            'Developer hourly rates reflect either internal burdened engineering compensation or external agency retainer billing.',
            'Annual subscription inflation defaults to 8% based on historical SaaS pricing adjustments, but remains user-editable.',
          ]}
          limitations={[
            'Does not measure non-quantifiable brand perception damage during a broken website layout incident.',
            'The Fragility Score is an informational Cora heuristic based on dependency density, not a verified code-level security vulnerability audit.',
          ]}
        />
      </div>

      {/* ── 5. CONTEXTUAL CORA CONVERSION BRIDGE ── */}
      <div className="mb-12">
        <ToolContextualCoraCta
          toolSlug="wp-plugin-cost-calculator"
          badge="The Operational Alternative"
          heading="Keep Your WordPress Marketing Website. Consolidate Business Operations in Cora."
          description="Cora provides native form intake, interactive client proposals, e-signatures, and GST billing. You can connect Cora to your existing website to reduce the need for multiple fragmented backend plugins, without having to rebuild your design."
          bulletPoints={[
            'Native form intake and lead routing without separate form add-ons',
            'Interactive client proposals with legally binding digital e-signatures',
            'GST-compliant retainer billing and client payment portals built in',
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
      <ToolPrivacyDisclosure toolName="WordPress Plugin Stack Cost Calculator" />

      {/* ── 8. MOBILE STICKY LIVE SUMMARY BAR (lg:hidden) ── */}
      <ToolMobileBar
        primaryLabel={`Total ${viewCadence === 'annual' ? 'Annual' : 'Monthly'} TCO`}
        primaryValue={`${sym}${viewCadence === 'annual' ? result.totalAnnualTco.toLocaleString() : result.totalMonthlyTco.toLocaleString()}${viewCadence === 'annual' ? '/yr' : '/mo'}`}
        secondaryValue={viewCadence === 'annual' ? `${sym}${result.totalMonthlyTco.toLocaleString()}/mo` : undefined}
        activeTab={mobileTab}
        onTabChange={setMobileTab}
        onExport={exportSummaryCsv}
      />

    </ToolContainer>
  );
}
