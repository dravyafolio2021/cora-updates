'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { 
  ArrowRight, 
  CheckCircle2, 
  XCircle, 
  ChevronRight, 
  Plus, 
  Minus, 
  ExternalLink,
  ShieldCheck, 
  Sparkles,
  Layers,
  ArrowUpRight,
  Clock,
  Laptop
} from 'lucide-react';
import { FeatureModule, BUILT_MODULES } from '@/lib/features-data';
import { FeatureIcon } from './FeatureIcon';
import { ArtisticHeroBackground } from './ArtisticHeroBackground';
import { trackEvent } from '@/components/analytics/Analytics';

interface FeatureDetailClientProps {
  feature: FeatureModule;
}

export function FeatureDetailClient({ feature }: FeatureDetailClientProps) {
  const [activeMockupTab, setActiveMockupTab] = useState(feature.mockup.tabs[0]?.id || 'tab1');
  const [openFaq, setOpenFaq] = useState<number | null>(0);

  const relatedModules = BUILT_MODULES.filter(m => feature.relatedFeatureSlugs.includes(m.slug));

  const toggleFaq = (index: number) => {
    const nextState = openFaq === index ? null : index;
    setOpenFaq(nextState);
    if (nextState !== null) {
      trackEvent('feature_faq_opened', { slug: feature.slug, faqIndex: index });
    }
  };

  // Dynamic artistic tone mapped to module category
  const toneMap: Record<string, 'blue' | 'emerald' | 'purple' | 'zinc' | 'amber'> = {
    intelligence: 'purple',
    sales: 'blue',
    operations: 'zinc',
    finance: 'emerald',
    platform: 'blue'
  };
  const categoryTone = toneMap[feature.category] || 'blue';

  return (
    <div className="w-full">
      {/* ── ARTISTIC BLENDED HERO SECTION (COMPACT <=40VH) ── */}
      <section className="relative w-full pt-20 sm:pt-24 pb-8 sm:pb-12 overflow-hidden">
        {/* Monochromatic Background & Gradient Veil that melts down into the page */}
        <ArtisticHeroBackground tone="neutral" />

        <div className="relative z-10 w-full max-w-[1240px] mx-auto px-4 sm:px-6">
          {/* Breadcrumbs */}
          <nav className="flex items-center gap-1.5 text-xs text-zinc-600 font-medium overflow-x-auto whitespace-nowrap scrollbar-none py-1 mb-4">
            <Link href="/" className="hover:text-zinc-950 transition-colors">
              Home
            </Link>
            <ChevronRight className="w-3.5 h-3.5 text-zinc-400 shrink-0" />
            <Link href="/features" className="hover:text-zinc-950 transition-colors">
              Features
            </Link>
            <ChevronRight className="w-3.5 h-3.5 text-zinc-400 shrink-0" />
            <span className="text-zinc-500 uppercase tracking-wider text-[10px] font-mono">
              {feature.categoryLabel}
            </span>
            <ChevronRight className="w-3.5 h-3.5 text-zinc-400 shrink-0" />
            <span className="text-zinc-950 font-semibold truncate max-w-[200px] sm:max-w-none">
              {feature.shortTitle}
            </span>
          </nav>

          <div className="flex flex-col items-start gap-3.5 max-w-[880px]">
            {/* Status & Category Badge */}
            <div className="flex items-center flex-wrap gap-2">
              <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-white/90 backdrop-blur-md text-zinc-900 border border-zinc-200/90 rounded-full text-xs font-semibold shadow-2xs">
                <FeatureIcon name={feature.iconName} className="w-3.5 h-3.5 text-zinc-800" />
                <span>{feature.categoryLabel}</span>
              </span>
              <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-emerald-500/10 text-emerald-700 border border-emerald-500/30 rounded-full text-[10px] font-mono font-bold backdrop-blur-md">
                <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
                {feature.status}
              </span>
            </div>

            {/* Main Title */}
            <h1 className="font-display text-2xl xs:text-3xl sm:text-4xl md:text-5xl font-semibold text-zinc-950 tracking-[-0.03em] leading-[1.24] sm:leading-[1.28]">
              {feature.title}
            </h1>

            {/* Value Tagline */}
            <p className="text-zinc-600 text-xs sm:text-base font-normal leading-relaxed max-w-[760px]">
              {feature.heroDescription}
            </p>

            {/* Stats Bar */}
            <div className="grid grid-cols-3 gap-3 sm:gap-6 w-full py-3 my-1 border-y border-zinc-200/70">
              {feature.stats.map((stat, idx) => (
                <div key={idx} className="space-y-0.5">
                  <div className="font-display text-lg sm:text-2xl font-extrabold text-zinc-950 tracking-tight">
                    {stat.metric}
                  </div>
                  <div className="text-[10px] sm:text-xs text-zinc-500 font-medium">
                    {stat.label}
                  </div>
                </div>
              ))}
            </div>

            {/* Dual Action Buttons */}
            <div className="flex items-center flex-wrap gap-2.5 pt-0.5">
              <a
                href={`https://app.heycora.in/workspace/login?feature=${feature.slug}`}
                onClick={() => trackEvent('feature_detail_try_free', { slug: feature.slug })}
                className="inline-flex items-center gap-2 bg-zinc-950 hover:bg-zinc-800 text-white px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all shadow-sm group cursor-pointer"
              >
                <span>Try {feature.shortTitle} Free</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
              </a>

              <Link
                href="/pricing"
                className="inline-flex items-center gap-2 bg-white text-zinc-900 border border-zinc-200 hover:border-zinc-300 hover:bg-zinc-50/80 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all shadow-2xs"
              >
                <span>Pricing Plans</span>
              </Link>
            </div>

          </div>
        </div>
      </section>

      {/* ── INTERACTIVE WORKSPACE WINDOW MOCKUP ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-20 sm:mb-28">
        <div className="w-full rounded-2xl sm:rounded-[32px] bg-zinc-900 border border-zinc-800 p-2 sm:p-3 shadow-2xl overflow-hidden">
          
          {/* macOS Browser Header Frame */}
          <div className="bg-zinc-950 rounded-xl sm:rounded-[24px] border border-zinc-800/80 overflow-hidden text-zinc-100">
            
            {/* Top Window Bar */}
            <div className="px-4 py-3 bg-zinc-900/90 border-b border-zinc-800 flex items-center justify-between gap-4">
              <div className="flex items-center gap-2">
                <span className="w-3 h-3 rounded-full bg-[#FF5F56] border border-[#E0443E]/50 block" />
                <span className="w-3 h-3 rounded-full bg-[#FFBD2E] border border-[#DEA123]/50 block" />
                <span className="w-3 h-3 rounded-full bg-[#27C93F] border border-[#1AAB29]/50 block" />
              </div>
              <div className="text-[11px] font-mono text-zinc-400 truncate flex items-center gap-1.5">
                <Laptop className="w-3.5 h-3.5 text-zinc-500" />
                <span>{feature.mockup.windowTitle}</span>
              </div>
              <div className="w-12 text-right">
                <span className="w-2 h-2 rounded-full bg-emerald-500 inline-block animate-pulse" />
              </div>
            </div>

            {/* Mockup Workspace Tabs Header */}
            <div className="px-4 sm:px-6 pt-4 pb-3 bg-zinc-950 border-b border-zinc-800/80 flex items-center justify-between flex-wrap gap-3">
              <div className="flex items-center gap-2 overflow-x-auto scrollbar-none">
                {feature.mockup.tabs.map((tab) => (
                  <button
                    key={tab.id}
                    onClick={() => setActiveMockupTab(tab.id)}
                    className={`px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-2 cursor-pointer ${
                      activeMockupTab === tab.id
                        ? 'bg-zinc-800 text-white shadow-xs'
                        : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-900'
                    }`}
                  >
                    <span>{tab.label}</span>
                    {tab.badge && (
                      <span className="px-1.5 py-0.5 bg-emerald-500/20 text-emerald-300 text-[10px] font-mono rounded font-normal">
                        {tab.badge}
                      </span>
                    )}
                  </button>
                ))}
              </div>

              <button
                type="button"
                className="px-3.5 py-1.5 rounded-lg bg-zinc-100 hover:bg-white text-zinc-950 text-xs font-bold transition-all shadow-xs cursor-default shrink-0"
              >
                {feature.mockup.primaryActionLabel}
              </button>
            </div>

            {/* Mockup Operational Content Panel */}
            <div className="p-4 sm:p-6 sm:p-8 space-y-6 bg-zinc-950">
              
              {/* Header Info & Live Metric Pills */}
              <div className="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 pb-4 border-b border-zinc-800/80">
                <div>
                  <h3 className="font-display text-lg sm:text-xl font-bold text-white">
                    {feature.mockup.headerTitle}
                  </h3>
                  <p className="text-xs text-zinc-400 mt-0.5">
                    {feature.mockup.headerSubtitle}
                  </p>
                </div>

                <div className="flex items-center gap-3 shrink-0">
                  <div className="px-3 py-1.5 rounded-xl bg-zinc-900 border border-zinc-800 text-left">
                    <span className="text-[10px] font-mono text-zinc-500 block">{feature.mockup.metric1.label}</span>
                    <span className="text-xs font-mono font-bold text-emerald-400">{feature.mockup.metric1.value}</span>
                  </div>
                  <div className="px-3 py-1.5 rounded-xl bg-zinc-900 border border-zinc-800 text-left">
                    <span className="text-[10px] font-mono text-zinc-500 block">{feature.mockup.metric2.label}</span>
                    <span className="text-xs font-mono font-bold text-zinc-200">{feature.mockup.metric2.value}</span>
                  </div>
                </div>
              </div>

              {/* Data Table Matrix Preview */}
              <div className="w-full overflow-x-auto rounded-xl border border-zinc-800 bg-zinc-900/50">
                <table className="w-full text-left text-xs border-collapse">
                  <thead>
                    <tr className="border-b border-zinc-800 bg-zinc-900/90 text-zinc-400 font-mono text-[11px] uppercase">
                      {feature.mockup.tableHeaders.map((header, idx) => (
                        <th key={idx} className="py-3 px-4 font-semibold">{header}</th>
                      ))}
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-zinc-800/60 font-medium">
                    {feature.mockup.rows.map((row, rIdx) => (
                      <tr key={rIdx} className="hover:bg-zinc-800/40 transition-colors">
                        <td className="py-3.5 px-4 text-zinc-200 font-semibold">{row.col1}</td>
                        <td className="py-3.5 px-4 text-zinc-400">{row.col2}</td>
                        <td className="py-3.5 px-4 text-zinc-300 font-mono">{row.col3}</td>
                        <td className="py-3.5 px-4">
                          <span className={`inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10px] font-mono font-bold ${
                            row.statusType === 'success' 
                              ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20'
                              : row.statusType === 'warning'
                                ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20'
                                : row.statusType === 'info'
                                  ? 'bg-sky-500/10 text-sky-400 border border-sky-500/20'
                                  : 'bg-zinc-800 text-zinc-400'
                          }`}>
                            <span className="w-1.5 h-1.5 rounded-full bg-current" />
                            {row.statusText}
                          </span>
                        </td>
                        <td className="py-3.5 px-4 text-right">
                          <span className="text-[11px] font-bold text-zinc-300 hover:text-white transition-colors cursor-default underline underline-offset-4">
                            {row.actionText} →
                          </span>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>

              {/* Mockup Footer Caption */}
              <div className="flex items-center justify-between text-[11px] text-zinc-500 pt-1 font-mono">
                <span>⚡ Powered by Cora Atomic UI Engine</span>
                <span>SHA-256 Verified • AES-256 Encrypted</span>
              </div>

            </div>

          </div>
        </div>
      </section>

      {/* ── THE OLD WAY VS THE CORA WAY ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-24 sm:mb-32">
        <div className="text-center max-w-[680px] mx-auto mb-12">
          <span className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-400 block mb-2">
            ARCHITECTURAL ADVANTAGE
          </span>
          <h2 className="font-display text-2xl sm:text-4xl font-bold text-zinc-950 tracking-tight">
            The Old Broken Stack vs. The Cora Studio OS
          </h2>
          <p className="text-zinc-600 text-sm sm:text-base mt-2">
            Why single-purpose tools drain your team’s hours and compromise your bottom line.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-[1040px] mx-auto">
          
          {/* THE OLD WAY (Pain Points) */}
          <div className="p-6 sm:p-8 rounded-[28px] bg-zinc-50 border border-rose-200/60 space-y-5">
            <div className="flex items-center gap-2.5 text-rose-700 font-bold text-sm uppercase tracking-wider font-mono">
              <XCircle className="w-4 h-4 text-rose-600" />
              <span>The Old Fragmented Way</span>
            </div>

            <ul className="space-y-4">
              {feature.theOldWay.map((point, idx) => (
                <li key={idx} className="flex items-start gap-3 text-xs sm:text-sm text-zinc-700 leading-relaxed">
                  <span className="w-1.5 h-1.5 rounded-full bg-rose-500 mt-2 shrink-0" />
                  <span>{point}</span>
                </li>
              ))}
            </ul>
          </div>

          {/* THE CORA WAY (Solutions) */}
          <div className="p-6 sm:p-8 rounded-[28px] bg-zinc-950 text-white border border-zinc-800 space-y-5 shadow-xl">
            <div className="flex items-center gap-2.5 text-emerald-400 font-bold text-sm uppercase tracking-wider font-mono">
              <CheckCircle2 className="w-4 h-4 text-emerald-400" />
              <span>The Cora OS Solution</span>
            </div>

            <ul className="space-y-4">
              {feature.theCoraWay.map((point, idx) => (
                <li key={idx} className="flex items-start gap-3 text-xs sm:text-sm text-zinc-300 leading-relaxed">
                  <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 mt-2 shrink-0" />
                  <span>{point}</span>
                </li>
              ))}
            </ul>
          </div>

        </div>
      </section>

      {/* ── DEEP CAPABILITIES GRID (4-6 PILLARS) ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-24 sm:mb-32">
        <div className="text-center max-w-[680px] mx-auto mb-14">
          <span className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-400 block mb-2">
            GRANULAR ARCHITECTURE
          </span>
          <h2 className="font-display text-2xl sm:text-4xl font-bold text-zinc-950 tracking-tight">
            Engineered Capabilities for High-Velocity Studios
          </h2>
          <p className="text-zinc-600 text-sm sm:text-base mt-2">
            Every feature in {feature.shortTitle} is built to withstand demanding production loads with zero friction.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
          {feature.capabilities.map((cap, idx) => (
            <div
              key={idx}
              className="bg-white rounded-[24px] border border-zinc-200/90 p-6 flex flex-col justify-between hover:shadow-lg hover:border-zinc-300 transition-all group"
            >
              <div className="space-y-3">
                <span className="text-[10px] font-mono font-bold text-zinc-500 bg-zinc-100 px-2 py-0.5 rounded-md border border-zinc-200/60 inline-block">
                  {cap.tag}
                </span>
                <h3 className="font-display text-base font-bold text-zinc-950 group-hover:text-zinc-800 transition-colors">
                  {cap.title}
                </h3>
                <p className="text-zinc-600 text-xs sm:text-[13px] leading-relaxed">
                  {cap.description}
                </p>
              </div>
            </div>
          ))}
        </div>
      </section>

      {/* ── 3-STEP WORKFLOW STEPPER ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-24 sm:mb-32">
        <div className="bg-zinc-50 rounded-[32px] p-8 sm:p-12 md:p-14 border border-zinc-200/90">
          <div className="max-w-[680px] mb-12">
            <span className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-400 block mb-2">
              HOW IT WORKS IN PRACTICE
            </span>
            <h2 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight">
              3-Step Workflow Execution
            </h2>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
            {feature.howItWorks.map((step, idx) => (
              <div key={idx} className="space-y-3 relative">
                <div className="font-display text-4xl sm:text-5xl font-black text-zinc-300">
                  {step.step}
                </div>
                <h4 className="font-display text-base sm:text-lg font-bold text-zinc-950">
                  {step.title}
                </h4>
                <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed">
                  {step.description}
                </p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ── REPLACED TOOLS & SAVINGS ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-24 sm:mb-32">
        <div className="p-6 sm:p-8 rounded-[28px] bg-white border border-zinc-200/90 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">
          <div className="space-y-1 text-center md:text-left">
            <span className="text-[11px] font-mono font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200/80 uppercase">
              SUBSCRIPTION REPLACEMENT
            </span>
            <h3 className="font-display text-lg sm:text-xl font-bold text-zinc-950 pt-1">
              Stop paying separately for point solutions
            </h3>
            <p className="text-xs sm:text-sm text-zinc-500">
              {feature.shortTitle} natively eliminates the need for these standalone tools:
            </p>
          </div>

          <div className="flex items-center flex-wrap gap-2.5 justify-center md:justify-end">
            {feature.toolsReplaced.map((tool, idx) => (
              <div key={idx} className="px-3.5 py-2 rounded-xl bg-zinc-50 border border-zinc-200/80 text-center">
                <span className="text-xs font-bold text-zinc-900 block">{tool.name}</span>
                <span className="text-[10px] text-zinc-400 block font-mono">
                  Saves ₹{tool.monthlySavingsINR.toLocaleString('en-IN')}/mo
                </span>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ── FAQS ACCORDION ── */}
      <section className="w-full max-w-[860px] mx-auto px-4 sm:px-6 mb-24 sm:mb-32">
        <div className="text-center mb-10">
          <span className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-400 block mb-2">
            FREQUENTLY ASKED QUESTIONS
          </span>
          <h2 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight">
            Everything you need to know about {feature.shortTitle}
          </h2>
        </div>

        <div className="space-y-3">
          {feature.faqs.map((faq, idx) => (
            <div
              key={idx}
              className="rounded-2xl border border-zinc-200/80 bg-white overflow-hidden transition-all"
            >
              <button
                type="button"
                onClick={() => toggleFaq(idx)}
                className="w-full py-4 px-5 text-left flex items-center justify-between gap-4 font-semibold text-xs sm:text-sm text-zinc-950 hover:bg-zinc-50/50 transition-colors cursor-pointer"
              >
                <span>{faq.question}</span>
                <span className="w-5 h-5 rounded-full bg-zinc-100 flex items-center justify-center shrink-0">
                  {openFaq === idx ? (
                    <Minus className="w-3.5 h-3.5 text-zinc-700" />
                  ) : (
                    <Plus className="w-3.5 h-3.5 text-zinc-700" />
                  )}
                </span>
              </button>

              {openFaq === idx && (
                <div className="px-5 pb-4 pt-1 text-xs sm:text-sm text-zinc-600 leading-relaxed border-t border-zinc-100">
                  {faq.answer}
                </div>
              )}
            </div>
          ))}
        </div>
      </section>

      {/* ── RELATED MODULES CROSS-LINKS ── */}
      {relatedModules.length > 0 && (
        <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-24 sm:mb-32">
          <div className="flex items-center justify-between mb-8">
            <div>
              <span className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-400 block">
                COMPLEMENTARY CAPABILITIES
              </span>
              <h3 className="font-display text-xl sm:text-2xl font-bold text-zinc-950">
                Explore Related Cora OS Modules
              </h3>
            </div>
            <Link
              href="/features"
              className="text-xs font-bold text-zinc-950 hover:text-zinc-600 transition-colors flex items-center gap-1"
            >
              <span>View All 20 Modules</span>
              <ArrowRight className="w-3.5 h-3.5" />
            </Link>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
            {relatedModules.map((rel) => (
              <Link
                key={rel.slug}
                href={`/features/${rel.slug}`}
                className="bg-white rounded-[24px] border border-zinc-200/90 p-6 flex flex-col justify-between hover:shadow-lg hover:border-zinc-300 transition-all group"
              >
                <div className="space-y-3">
                  <div className="w-10 h-10 rounded-xl bg-zinc-950 text-white flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform">
                    <FeatureIcon name={rel.iconName} className="w-5 h-5" />
                  </div>
                  <div>
                    <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block">
                      {rel.categoryLabel}
                    </span>
                    <h4 className="font-display text-base font-bold text-zinc-950 group-hover:text-zinc-800 transition-colors">
                      {rel.title}
                    </h4>
                  </div>
                  <p className="text-zinc-600 text-xs line-clamp-2 leading-relaxed">
                    {rel.tagline}
                  </p>
                </div>

                <div className="pt-4 mt-4 border-t border-zinc-100 flex items-center justify-between text-xs font-bold text-zinc-950 group-hover:translate-x-0.5 transition-transform">
                  <span>Deep Dive</span>
                  <ArrowRight className="w-3.5 h-3.5" />
                </div>
              </Link>
            ))}
          </div>
        </section>
      )}

      {/* ── BOTTOM CONVERSION CTA ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-16">
        <div className="w-full rounded-[36px] bg-gradient-to-br from-[#0F172A] via-[#1E293B] to-[#0A0D12] text-white p-8 sm:p-14 text-center relative overflow-hidden border border-zinc-800 shadow-xl">
          <div className="relative z-10 max-w-[680px] mx-auto space-y-6">
            <h2 className="font-display text-3xl sm:text-4xl font-bold tracking-tight">
              Ready to automate your studio with {feature.shortTitle}?
            </h2>
            <p className="text-zinc-400 text-sm sm:text-base leading-relaxed font-normal">
              Activate your workspace now with 1,000 free operations and full access to all 20 built modules. No credit card required.
            </p>

            <div className="flex items-center justify-center flex-wrap gap-3.5 pt-2">
              <a
                href={`https://app.heycora.in/workspace/login?feature=${feature.slug}&source=cta_bottom`}
                className="inline-flex items-center gap-2 bg-white text-zinc-950 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-100 transition-all shadow-sm group cursor-pointer"
              >
                <span>Get started for Free</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-600 group-hover:translate-x-0.5 transition-transform" />
              </a>

              <Link
                href="/pricing"
                className="inline-flex items-center gap-2 bg-zinc-900 text-white border border-zinc-700 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-2xs"
              >
                <span>Explore Pricing Plans</span>
              </Link>
            </div>
          </div>
        </div>
      </section>

    </div>
  );
}
