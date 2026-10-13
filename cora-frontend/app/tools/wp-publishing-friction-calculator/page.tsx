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
  calculateWpPublishingFriction,
  WP_PUBLISHING_PRESETS,
  WpPublishingFrictionInputs,
} from '@/lib/tools-engines/wp-publishing-friction-engine';
import {
  trackToolStarted,
  trackToolCompleted,
  trackToolResultBand,
} from '@/lib/tools-analytics';

export default function WpPublishingFrictionCalculatorPage() {
  const [inputs, setInputs] = useState<WpPublishingFrictionInputs>({
    articlesPerMonth: 12,
    contentCreatorsCount: 2,
    authorHourlyRate: 65,
    gutenbergBlockFrictionHoursPerPost: 1.5,
    stagingSyncAndFormattingHoursPerPost: 1.0,
    qaAndVisualRegressionHoursPerPost: 0.75,
    seoAndSchemaManualEntryMinutesPerPost: 25,
  });

  const [hasInteracted, setHasInteracted] = useState(false);

  const result = useMemo(() => {
    return calculateWpPublishingFriction(inputs);
  }, [inputs]);

  useEffect(() => {
    if (hasInteracted) {
      trackToolCompleted({
        tool_slug: 'wp-publishing-friction-calculator',
        calculation_mode: 'client_side',
      });
      trackToolResultBand({
        tool_slug: 'wp-publishing-friction-calculator',
        band_name: result.frictionBand.label,
        primary_metric_value: result.annualPublishingFrictionCost,
      });
    }
  }, [hasInteracted, result.frictionBand.label, result.annualPublishingFrictionCost]);

  const handleInputChange = (field: keyof WpPublishingFrictionInputs, val: number) => {
    if (!hasInteracted) {
      setHasInteracted(true);
      trackToolStarted({ tool_slug: 'wp-publishing-friction-calculator' });
    }
    setInputs((prev) => ({ ...prev, [field]: val }));
  };

  const applyPreset = (presetKey: string) => {
    const preset = WP_PUBLISHING_PRESETS[presetKey];
    if (preset) {
      if (!hasInteracted) {
        setHasInteracted(true);
        trackToolStarted({ tool_slug: 'wp-publishing-friction-calculator' });
      }
      setInputs((prev) => ({ ...prev, ...preset.inputs }));
    }
  };

  const findings: FindingItem[] = useMemo(() => {
    const list: FindingItem[] = [];

    if (result.frictionHoursPerArticle > 2.5) {
      list.push({
        id: 'high-formatting-burden',
        severity: 'critical',
        title: 'Excessive Editorial Formatting & QA Overhead',
        description: `Your team spends ${result.frictionHoursPerArticle.toFixed(1)} hours per article wrestling with block formatting, CSS overrides, and staging syncs. This consumes ${result.annualWastedPublishingHours} hours of talent capacity annually ($${result.annualPublishingFrictionCost.toLocaleString()}/yr).`,
        remedy: 'Publish articles through structured Markdown/Notion-like workflows with automated schema and responsive styling.',
        metric: `${result.frictionHoursPerArticle.toFixed(1)} hrs/post wasted`,
      });
    }

    if (inputs.seoAndSchemaManualEntryMinutesPerPost > 20) {
      list.push({
        id: 'manual-schema-entry',
        severity: 'warning',
        title: 'Repetitive Manual Schema & Meta Entry',
        description: `Spending ${inputs.seoAndSchemaManualEntryMinutesPerPost} minutes per post manually filling plugin meta boxes (e.g. RankMath/Yoast) creates human error risk and slows publishing velocity.`,
        remedy: 'Automate JSON-LD schema generation for Articles, Breadcrumbs, Authors, and FAQs based on native content structure.',
        metric: `${inputs.seoAndSchemaManualEntryMinutesPerPost} mins/post on SEO forms`,
      });
    }

    if (list.length === 0) {
      list.push({
        id: 'lean-publishing',
        severity: 'positive',
        title: 'Efficient Editorial Pipeline',
        description: 'Your editorial workflow maintains lean formatting overhead with fast turnaround times.',
        metric: 'Low Friction',
      });
    }

    return list;
  }, [result, inputs]);

  const canonicalUrl = 'https://heycora.in/tools/wp-publishing-friction-calculator';
  const metaTitle = 'WordPress Publishing Friction Calculator | Audit Editorial Labor';
  const metaDescription =
    'Calculate the annual engineering and editorial cost of WordPress block formatting, staging syncs, QA regressions, and manual schema entry.';

  const faqs = [
    {
      question: 'What is WordPress publishing friction?',
      answer:
        'Publishing friction is the non-writing time authors and developers spend pasting drafts, cleaning up broken Gutenberg blocks, fixing mobile layout regressions, syncing staging to production, and manually filling plugin SEO fields.',
    },
    {
      question: 'How much time is typically wasted formatting a WordPress post?',
      answer:
        'Industry audits show content teams spend between 2.5 and 4.5 hours per article simply formatting blocks, fixing responsive tables, aligning imagery, and configuring SEO plugins—often equaling 30% to 50% of the total article creation budget.',
    },
    {
      question: 'How does Cora streamline editorial publishing?',
      answer:
        'Cora provides a clean, distraction-free markdown/Notion-style editor with automated typography, real-time GEO/AI entity validation, auto-generated Article & FAQ schema, and instant 1-click publishing without block crashes.',
    },
  ];

  return (
    <ToolContainer
      categoryLabel="WordPress & Stack Health"
      title="WordPress Publishing Friction & Labor Cost Calculator"
      subtitle="Audit how many hours and editorial dollars your team loses pasting drafts, fixing broken blocks, testing responsive layouts, and filling manual SEO meta boxes."
      badgeTag="Editorial Friction Model"
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

      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
        {/* LEFT COLUMN: INPUTS */}
        <div className="lg:col-span-7 space-y-6">
          
          {/* Preset Buttons */}
          <div className="rounded-2xl border border-zinc-200/90 bg-white p-5 sm:p-6 shadow-xs">
            <div className="flex items-center justify-between gap-2 mb-3">
              <span className="text-xs font-mono font-semibold uppercase tracking-wider text-zinc-500">
                Load Publishing Presets
              </span>
              <span className="text-[11px] font-mono text-zinc-400">One-click model setup</span>
            </div>
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-2">
              <button
                type="button"
                onClick={() => applyPreset('agency_blog')}
                className="px-3 py-2 text-left rounded-xl border border-zinc-200 hover:border-zinc-400 bg-zinc-50 hover:bg-zinc-100 transition-colors text-xs font-medium text-zinc-800"
              >
                <span className="font-semibold block text-zinc-950">Agency Blog</span>
                <span className="text-[11px] text-zinc-500">8 posts/mo · 2.5 hrs friction</span>
              </button>
              <button
                type="button"
                onClick={() => applyPreset('content_marketing_team')}
                className="px-3 py-2 text-left rounded-xl border border-zinc-200 hover:border-zinc-400 bg-zinc-50 hover:bg-zinc-100 transition-colors text-xs font-medium text-zinc-800"
              >
                <span className="font-semibold block text-zinc-950">Content Team</span>
                <span className="text-[11px] text-zinc-500">24 posts/mo · 3.8 hrs friction</span>
              </button>
              <button
                type="button"
                onClick={() => applyPreset('solo_creator')}
                className="px-3 py-2 text-left rounded-xl border border-zinc-200 hover:border-zinc-400 bg-zinc-50 hover:bg-zinc-100 transition-colors text-xs font-medium text-zinc-800"
              >
                <span className="font-semibold block text-zinc-950">Solo Creator</span>
                <span className="text-[11px] text-zinc-500">4 posts/mo · 1.5 hrs friction</span>
              </button>
            </div>
          </div>

          {/* Publishing Volume */}
          <div className="rounded-2xl border border-zinc-200/90 bg-white p-6 sm:p-8 shadow-xs space-y-6">
            <h2 className="font-display text-lg sm:text-xl font-bold text-zinc-950 tracking-tight pb-3 border-b border-zinc-200/70">
              1. Publishing Volume & Editorial Rates
            </h2>

            <ToolNumberInput
              id="articlesPerMonth"
              label="Articles / Case Studies Published Monthly"
              helpText="Total new blog posts, case studies, or knowledge base articles published per month."
              value={inputs.articlesPerMonth}
              onChange={(v) => handleInputChange('articlesPerMonth', v)}
              min={1}
              max={150}
              step={1}
              suffix="articles/mo"
            />

            <ToolNumberInput
              id="authorHourlyRate"
              label="Author / Content Specialist Hourly Rate"
              helpText="Blended internal salary or contractor rate per content creator hour."
              value={inputs.authorHourlyRate}
              onChange={(v) => handleInputChange('authorHourlyRate', v)}
              min={20}
              max={250}
              step={5}
              prefix="$"
              suffix="/hr"
            />
          </div>

          {/* Friction Drivers */}
          <div className="rounded-2xl border border-zinc-200/90 bg-white p-6 sm:p-8 shadow-xs space-y-6">
            <h2 className="font-display text-lg sm:text-xl font-bold text-zinc-950 tracking-tight pb-3 border-b border-zinc-200/70">
              2. Editorial Bottlenecks & Formatting Time
            </h2>

            <ToolNumberInput
              id="gutenbergBlockFrictionHoursPerPost"
              label="Block Formatting & Styling Adjustments"
              helpText="Hours per post spent formatting Gutenberg/Elementor blocks, custom containers, and inline callouts."
              value={inputs.gutenbergBlockFrictionHoursPerPost}
              onChange={(v) => handleInputChange('gutenbergBlockFrictionHoursPerPost', v)}
              min={0.1}
              max={6}
              step={0.25}
              suffix="hrs/post"
            />

            <ToolNumberInput
              id="stagingSyncAndFormattingHoursPerPost"
              label="Staging Sync & Media Upload Overhead"
              helpText="Time spent optimizing images, uploading media, and migrating drafts between staging and production."
              value={inputs.stagingSyncAndFormattingHoursPerPost}
              onChange={(v) => handleInputChange('stagingSyncAndFormattingHoursPerPost', v)}
              min={0.1}
              max={4}
              step={0.25}
              suffix="hrs/post"
            />

            <ToolNumberInput
              id="qaAndVisualRegressionHoursPerPost"
              label="Responsive Layout Testing & QA"
              helpText="Checking mobile layout regressions, browser viewport bugs, and broken font sizes."
              value={inputs.qaAndVisualRegressionHoursPerPost}
              onChange={(v) => handleInputChange('qaAndVisualRegressionHoursPerPost', v)}
              min={0.1}
              max={4}
              step={0.25}
              suffix="hrs/post"
            />

            <ToolNumberInput
              id="seoAndSchemaManualEntryMinutesPerPost"
              label="Manual SEO & Schema Plugin Entry"
              helpText="Minutes spent manually filling meta titles, descriptions, focus keywords, and schema fields."
              value={inputs.seoAndSchemaManualEntryMinutesPerPost}
              onChange={(v) => handleInputChange('seoAndSchemaManualEntryMinutesPerPost', v)}
              min={0}
              max={60}
              step={5}
              suffix="mins/post"
            />
          </div>

        </div>

        {/* RIGHT COLUMN: FRICTION SUMMARY */}
        <div className="lg:col-span-5 space-y-6">
          <div className="rounded-3xl border border-zinc-900 bg-zinc-950 text-white p-6 sm:p-8 shadow-lg sticky top-24">
            <div className="flex items-center justify-between mb-4 border-b border-zinc-800 pb-3">
              <span className="text-xs font-mono font-medium text-zinc-400 uppercase tracking-wider">
                Annual Publishing Friction Cost
              </span>
              <span className="text-[11px] font-mono px-2 py-0.5 rounded-full bg-zinc-800 text-zinc-300">
                100% Client Calculated
              </span>
            </div>

            <div className="font-mono text-4xl sm:text-5xl font-bold tracking-tight text-white mb-2">
              ${result.annualPublishingFrictionCost.toLocaleString()}
              <span className="text-sm font-sans font-normal text-zinc-400 block mt-1">
                spent annually on formatting & QA labor
              </span>
            </div>

            <div className="mt-4 p-3 rounded-xl bg-zinc-900 border border-zinc-800 text-xs text-zinc-300 flex items-center justify-between">
              <span>Wasted Editorial Capacity:</span>
              <span className="font-mono font-bold text-white text-sm">
                {result.annualWastedPublishingHours} hours / yr
              </span>
            </div>

            {/* Savings with Cora */}
            <div className="mt-4 p-3.5 rounded-xl bg-emerald-950/60 border border-emerald-800/80 text-xs text-emerald-200 flex items-center justify-between">
              <div>
                <span className="font-bold text-emerald-300 block">Saved with Cora:</span>
                <span className="text-[11px] text-emerald-400">Via clean markdown & auto schema</span>
              </div>
              <span className="font-mono font-bold text-emerald-300 text-base">
                +${result.costSavedWithCora.toLocaleString()}/yr
              </span>
            </div>

            {/* Friction per post */}
            <div className="mt-6 pt-6 border-t border-zinc-800 space-y-2 font-mono text-xs">
              <div className="flex items-center justify-between text-zinc-400">
                <span>Friction per Article:</span>
                <span className="text-white font-bold">{result.frictionHoursPerArticle.toFixed(1)} hours / post</span>
              </div>
              <div className="flex items-center justify-between text-zinc-400">
                <span>3-Year Wasted Burden:</span>
                <span className="text-rose-400 font-bold">${result.threeYearFrictionCost.toLocaleString()}</span>
              </div>
            </div>

            {/* Friction Severity Band */}
            <div className="mt-6 pt-6 border-t border-zinc-800">
              <div className="flex items-center justify-between mb-2">
                <span className="text-xs font-mono text-zinc-400">Editorial Friction Band:</span>
                <span className="text-xs font-mono font-bold text-amber-400">
                  {result.frictionBand.label}
                </span>
              </div>
              <p className="text-[11px] text-zinc-400 leading-relaxed">
                {result.frictionBand.description}
              </p>
            </div>
          </div>

          <ToolFindingsList findings={findings} />
        </div>
      </div>

      {/* WORKED EXAMPLE */}
      <div className="mb-12">
        <ToolWorkedExample
          scenarioDescription="A content marketing team publishes 12 articles per month at $65/hr author cost. Authors spend 1.5 hrs fixing blocks, 1.0 hr syncing staging, 0.75 hrs on mobile QA, and 25 mins on manual SEO meta fields per article."
          inputsTable={[
            { label: 'Articles / Mo', value: '12 posts' },
            { label: 'Author Rate', value: '$65 / hr' },
            { label: 'Block Formatting', value: '1.5 hrs / post' },
            { label: 'QA Testing', value: '0.75 hrs / post' },
          ]}
          steps={[
            {
              step: 'Calculate Total Friction Time per Post',
              detail: '1.5h blocks + 1.0h staging + 0.75h QA + 0.42h SEO forms',
              result: '3.67 hours / article',
            },
            {
              step: 'Calculate Monthly Non-Writing Labor',
              detail: '12 articles × 3.67 hours friction',
              result: '44 hours / month',
            },
            {
              step: 'Calculate Annual Lost Editorial Capacity',
              detail: '44 hours/mo × 12 months',
              result: '528 hours / year',
            },
            {
              step: 'Calculate Annualized Friction Cost',
              detail: '528 hours × $65/hr author rate',
              result: '$34,320 / year',
            },
          ]}
          conclusion="Switching to a modern, auto-formatted editorial workspace saves this team over 370 hours of tedious formatting labor and $24,000 in editorial payroll annually."
        />
      </div>

      {/* METHODOLOGY & FORMULAS */}
      <div className="mb-12">
        <ToolMethodologyCard
          methodologyVersion="v1.1 (October 2026)"
          formulas={[
            {
              label: 'Friction Hours per Article',
              formula: 'Hours_friction = Hours_blocks + Hours_staging + Hours_qa + (Mins_seo / 60)',
              explanation: 'Sum of non-writing editorial overhead required to take a written draft to live production.',
            },
            {
              label: 'Annual Publishing Overhead',
              formula: 'Cost_annual = Articles_monthly × 12 × Hours_friction × Rate_author',
              explanation: 'Annual dollar investment in formatting, staging syncs, and manual plugin data entry.',
            },
            {
              label: 'Cora Efficiency Gain',
              formula: 'Savings_cora = Cost_annual × 0.70',
              explanation: 'Estimated 70% reduction in non-writing overhead via native markdown, responsive typography, and auto schema.',
            },
          ]}
          assumptions={[
            'Writing and research time are excluded; calculations measure only publishing and technical formatting labor.',
            'Author rates reflect fully loaded hourly employee cost or freelance contractor billing.',
          ]}
          limitations={[
            'Does not measure editorial delay impact on topical freshness rankings.',
            'Varies depending on whether custom Gutenberg block plugins or visual builders are utilized.',
          ]}
        />
      </div>

      {/* CORA CONVERSION BRIDGE */}
      <div className="mb-12">
        <ToolContextualCoraCta
          toolSlug="wp-publishing-friction-calculator"
          badge="The Editorial Efficiency Bridge"
          heading="Publish Clean, AI-Ready Content in Seconds. Zero Broken Blocks."
          description="Cora's editorial engine lets you draft in distraction-free markdown with automatic responsive typography, instant GEO/AI entity validation, auto-generated Article JSON-LD schema, and zero Gutenberg crashes."
          bulletPoints={[
            'Distraction-free Markdown & Notion-style editorial canvas',
            'Automated Article, FAQ & Breadcrumb JSON-LD schema generation',
            'Cut non-writing publishing overhead by over 70%',
          ]}
          ctaText="Explore Cora's Editorial & SEO Engine"
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
      <ToolPrivacyDisclosure toolName="WordPress Publishing Friction Calculator" />

    </ToolContainer>
  );
}
