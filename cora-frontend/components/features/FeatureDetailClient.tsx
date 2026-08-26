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
import { FeatureVisualMockup } from './FeatureVisualMockup';
import { CapabilityVisualCard } from './CapabilityVisualCard';
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

      {/* ── INTERACTIVE WORKSPACE WINDOW MOCKUP (PURE LIGHT MONOCHROMATIC AESTHETIC) ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-20 sm:mb-28">
        <div className="w-full rounded-2xl sm:rounded-[32px] bg-zinc-100/90 border border-zinc-200/90 p-2 sm:p-3 shadow-[0_20px_50px_rgba(0,0,0,0.06)] overflow-hidden">
          
          {/* macOS Browser Header Frame */}
          <div className="bg-white rounded-xl sm:rounded-[24px] border border-zinc-200/90 overflow-hidden text-zinc-900 shadow-xs">
            
            {/* Top Window Bar */}
            <div className="px-4 py-3 bg-zinc-50/90 border-b border-zinc-200/80 flex items-center justify-between gap-4">
              <div className="flex items-center gap-2">
                <span className="w-3 h-3 rounded-full bg-zinc-200 border border-zinc-300 block" />
                <span className="w-3 h-3 rounded-full bg-zinc-200 border border-zinc-300 block" />
                <span className="w-3 h-3 rounded-full bg-zinc-200 border border-zinc-300 block" />
              </div>
              <div className="text-[11px] font-mono text-zinc-500 truncate flex items-center gap-1.5">
                <Laptop className="w-3.5 h-3.5 text-zinc-400" />
                <span>{feature.mockup.windowTitle}</span>
              </div>
              <div className="w-12 text-right flex items-center justify-end gap-1.5">
                <span className="w-2 h-2 rounded-full bg-emerald-500 inline-block" />
                <span className="text-[10px] font-mono text-zinc-400 hidden sm:inline">Active</span>
              </div>
            </div>

            {/* Mockup Workspace Tabs Header */}
            <div className="px-4 sm:px-6 pt-3.5 pb-3 bg-white border-b border-zinc-100 flex items-center justify-between flex-wrap gap-3">
              <div className="flex items-center gap-1.5 overflow-x-auto scrollbar-none">
                {feature.mockup.tabs.map((tab) => (
                  <button
                    key={tab.id}
                    onClick={() => setActiveMockupTab(tab.id)}
                    className={`px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 cursor-pointer ${
                      activeMockupTab === tab.id
                        ? 'bg-zinc-950 text-white shadow-xs'
                        : 'text-zinc-600 hover:text-zinc-950 hover:bg-zinc-100'
                    }`}
                  >
                    <span>{tab.label}</span>
                    {tab.badge && (
                      <span className={`px-1.5 py-0.5 text-[10px] font-mono rounded font-normal ${
                        activeMockupTab === tab.id
                          ? 'bg-zinc-800 text-zinc-300'
                          : 'bg-zinc-100 text-zinc-600 border border-zinc-200/80'
                      }`}>
                        {tab.badge}
                      </span>
                    )}
                  </button>
                ))}
              </div>

              <button
                type="button"
                className="px-3.5 py-1.5 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold transition-all shadow-xs cursor-default shrink-0"
              >
                {feature.mockup.primaryActionLabel}
              </button>
            </div>

            {/* Mockup Operational Content Panel */}
            <div className="p-3 sm:p-5 bg-[#FCFCFD]">
              <FeatureVisualMockup feature={feature} />
            </div>

          </div>
        </div>
      </section>

      {/* ── SECTION 2: "A BETTER WAY TO WORK" COMPARISON (CLICKUP AESTHETIC) ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-24 sm:mb-32">
        
        {/* Section Heading: "A better way to work" */}
        <div className="text-center max-w-[700px] mx-auto mb-10 sm:mb-14">
          <h2 className="font-display text-3xl sm:text-5xl font-bold tracking-tight text-zinc-950">
            A better way to <span className="text-zinc-400 font-semibold">work</span>
          </h2>
        </div>

        {/* 2-Panel Side-by-Side Unified Card (Exact Reference Matching) */}
        <div className="max-w-[1060px] mx-auto bg-white rounded-3xl sm:rounded-[36px] border border-zinc-200 shadow-xs overflow-hidden">
          <div className="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-zinc-200">
            
            {/* Left Panel: Without ClickUp Tasks / Without [Feature] */}
            <div className="p-8 sm:p-12 md:p-14 space-y-6 bg-white">
              <h3 className="font-display text-xl sm:text-2xl font-bold text-zinc-800 tracking-tight">
                Without {feature.shortTitle}
              </h3>

              <ul className="space-y-4">
                {feature.theOldWay.map((point, idx) => (
                  <li key={idx} className="flex items-start gap-3.5 text-zinc-600 text-xs sm:text-[14px] leading-relaxed">
                    <span className="text-rose-500 font-bold text-sm sm:text-base leading-none select-none mt-1 shrink-0">
                      ✕
                    </span>
                    <span>{point}</span>
                  </li>
                ))}
              </ul>
            </div>

            {/* Right Panel: With ClickUp Tasks / With [Feature] */}
            <div className="p-8 sm:p-12 md:p-14 space-y-6 bg-white">
              <h3 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
                With {feature.shortTitle}
              </h3>

              <ul className="space-y-4">
                {feature.theCoraWay.map((point, idx) => (
                  <li key={idx} className="flex items-start gap-3.5 text-zinc-900 text-xs sm:text-[14px] leading-relaxed font-medium">
                    <span className="text-emerald-600 font-bold text-sm sm:text-base leading-none select-none mt-1 shrink-0">
                      ✓
                    </span>
                    <span>{point}</span>
                  </li>
                ))}
              </ul>
            </div>

          </div>
        </div>

      </section>

      {/* ── SECTION 3: THE FOUNDATION FOR EVERY WORKFLOW (CLICKUP 2-COLUMN ALTERNATING GRID) ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-24 sm:mb-32">
        
        {/* Section Heading */}
        <div className="text-center max-w-[760px] mx-auto mb-12 sm:mb-16">
          <h2 className="font-display text-3xl sm:text-5xl font-bold tracking-tight text-zinc-950 mb-3 sm:mb-4">
            The foundation for every <span className="text-zinc-400 font-semibold">workflow</span>
          </h2>
          <p className="text-zinc-600 text-sm sm:text-base leading-relaxed">
            {feature.shortTitle} powers critical operations across your workspace, keeping your projects organized, connected, and moving no matter how complex the production.
          </p>
        </div>

        {/* 2-Column Alternating Grid with Clean Dividing Lines */}
        <div className="w-full bg-white rounded-3xl sm:rounded-[36px] border border-zinc-200 shadow-xs overflow-hidden">
          <div className="divide-y divide-zinc-200">
            {feature.capabilities.map((cap, idx) => {
              const isEven = idx % 2 === 0;
              return (
                <div
                  key={idx}
                  className="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-zinc-200 items-stretch"
                >
                  {/* Left Column */}
                  <div className={`${isEven ? 'p-6 sm:p-10 lg:p-14 flex flex-col justify-center order-1' : 'p-4 sm:p-6 lg:p-10 bg-[#FAFAFA]/70 flex items-center justify-center order-2 lg:order-1'}`}>
                    {isEven ? (
                      <div className="space-y-4 max-w-[480px]">
                        <span className={`inline-flex items-center gap-1.5 text-[11px] font-mono font-bold uppercase tracking-wider px-2.5 py-1 rounded-lg border ${
                          idx === 0 
                            ? 'text-amber-800 bg-amber-50 border-amber-200' 
                            : idx === 1 
                              ? 'text-emerald-800 bg-emerald-50 border-emerald-200' 
                              : idx === 2 
                                ? 'text-blue-800 bg-blue-50 border-blue-200' 
                                : 'text-purple-800 bg-purple-50 border-purple-200'
                        }`}>
                          <span className="w-1.5 h-1.5 rounded-full bg-current" />
                          {cap.tag}
                        </span>
                        <h3 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight">
                          {cap.title}
                        </h3>
                        <p className="text-zinc-600 text-sm sm:text-base leading-relaxed font-normal">
                          {cap.description}
                        </p>
                      </div>
                    ) : (
                      <div className="w-full flex items-center justify-center">
                        <CapabilityVisualCard cap={cap} feature={feature} index={idx} />
                      </div>
                    )}
                  </div>

                  {/* Right Column */}
                  <div className={`${isEven ? 'p-4 sm:p-6 lg:p-10 bg-[#FAFAFA]/70 flex items-center justify-center order-2' : 'p-6 sm:p-10 lg:p-14 flex flex-col justify-center order-1 lg:order-2'}`}>
                    {isEven ? (
                      <div className="w-full flex items-center justify-center">
                        <CapabilityVisualCard cap={cap} feature={feature} index={idx} />
                      </div>
                    ) : (
                      <div className="space-y-4 max-w-[480px]">
                        <span className={`inline-flex items-center gap-1.5 text-[11px] font-mono font-bold uppercase tracking-wider px-2.5 py-1 rounded-lg border ${
                          idx === 0 
                            ? 'text-amber-800 bg-amber-50 border-amber-200' 
                            : idx === 1 
                              ? 'text-emerald-800 bg-emerald-50 border-emerald-200' 
                              : idx === 2 
                                ? 'text-blue-800 bg-blue-50 border-blue-200' 
                                : 'text-purple-800 bg-purple-50 border-purple-200'
                        }`}>
                          <span className="w-1.5 h-1.5 rounded-full bg-current" />
                          {cap.tag}
                        </span>
                        <h3 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight">
                          {cap.title}
                        </h3>
                        <p className="text-zinc-600 text-sm sm:text-base leading-relaxed font-normal">
                          {cap.description}
                        </p>
                      </div>
                    )}
                  </div>
                </div>
              );
            })}
          </div>
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

    </div>
  );
}
