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
  Clock
} from 'lucide-react';
import { FeatureModule, BUILT_MODULES } from '@/lib/features-data';
import { FeatureIcon } from './FeatureIcon';
import { CapabilityVisualCard } from './CapabilityVisualCard';
import { ModuleCardVisual } from './ModuleCardVisual';
import { FeaturePlusEverythingGrid } from './FeaturePlusEverythingGrid';
import { FeaturePlatformBeginningGrid } from './FeaturePlatformBeginningGrid';
import { FeatureSecurityTrustBar } from './FeatureSecurityTrustBar';
import { FeatureStudioSpotlightBanner } from './FeatureStudioSpotlightBanner';
import { ArtisticHeroBackground } from './ArtisticHeroBackground';
import { trackEvent } from '@/components/analytics/Analytics';

// Concise 5-6 word punchy descriptions for related module cards
const MODULE_MICRO_DESCRIPTIONS: Record<string, string> = {
  'ai-cofounder': 'Automate proposals and daily studio operations.',
  'content-ai': 'Generate viral scripts and social copy.',
  'rag-mcp': 'Self-learning memory with living studio context.',
  'voice-to-scope': 'Convert audio briefs into structured scopes.',
  'lead-crm': 'Track deals and automated client outreach.',
  'canvas-builder': 'Build high-converting landing pages visually.',
  'form-builder': 'Capture qualified leads with embeddable forms.',
  'review-portal': 'Collect 5-star Google client reviews automatically.',
  'esign-vault': 'Legally binding digital contracts and signatures.',
  'crew-dispatch': 'Schedule crew call sheets without conflicts.',
  'master-calendar': 'Manage multi-location shoot bookings seamlessly.',
  'task-board': 'Track milestones and post-production workflows.',
  'gst-invoicing': 'Automated Indian B2B tax invoice calculations.',
  'asset-gear': 'Track equipment check-ins and studio inventory.',
  'media-hub': 'Store and deliver 8K RAW footage.',
  'rbac-system': 'Role-based permissions with audit activity logs.',
  'email-smtp': 'Custom domain email with verified deliverability.',
  'pwa-push': 'Instant shoot alerts across mobile devices.',
  'docs-portal': 'Interactive API docs and testing playground.',
  'super-admin': 'Govern studio branches from one hub.',
  'onboarding-wizard': 'Launch your workspace in 3 minutes.',
};

interface FeatureDetailClientProps {
  feature: FeatureModule;
}

export function FeatureDetailClient({ feature }: FeatureDetailClientProps) {
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

      {/* ── SECTION 4: PLUS, EVERYTHING YOU NEED TO GET IT DONE (9-GRID MICRO CAPABILITIES) ── */}
      <FeaturePlusEverythingGrid feature={feature} />

      {/* ── SECTION 5: REAL-TIME PRODUCTION INTELLIGENCE SPOTLIGHT (CLICKUP DASHBOARD STYLE IN CORA MONOCHROME) ── */}
      <FeatureStudioSpotlightBanner feature={feature} />

      {/* ── SECTION 6: THE CORA PLATFORM (16 CONVERGED STUDIO OS MODULES GRID) ── */}
      <FeaturePlatformBeginningGrid feature={feature} />

      {/* ── SECTION 7: 3-STEP WORKFLOW STEPPER ── */}
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

      {/* ── SECTION 8: ENTERPRISE TRUST & SECURITY EVERYWHERE (SOC 2, ISO 27001, GDPR, IT ACT) ── */}
      <FeatureSecurityTrustBar />

      {/* ── SECTION 9: REPLACED TOOLS & SAVINGS ── */}
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

      {/* ── SECTION 10: FAQS ACCORDION ── */}
      <section className="w-full max-w-[860px] mx-auto px-4 sm:px-6 mb-24 sm:mb-32">
        <div className="text-center mb-10 sm:mb-12">
          <h2 className="font-display text-4xl sm:text-5xl font-bold text-zinc-950 tracking-tight">
            FAQ<span className="text-zinc-400 font-semibold">s</span>
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

          <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 sm:gap-7">
            {relatedModules.map((rel) => (
              <Link
                key={rel.slug}
                href={`/features/${rel.slug}`}
                className="bg-white rounded-[28px] sm:rounded-[32px] border border-zinc-200/90 overflow-hidden flex flex-col justify-between hover:shadow-[0_20px_45px_rgba(0,0,0,0.08)] hover:-translate-y-1.5 transition-all duration-300 group cursor-pointer block"
              >
                {/* Top Tactile 3D UI Illustration Area */}
                <div className="w-full h-[195px] sm:h-[210px] overflow-hidden border-b border-zinc-100 relative group-hover:scale-[1.02] transition-transform duration-300 select-none">
                  <ModuleCardVisual slug={rel.slug} category={rel.category} title={rel.shortTitle} />
                </div>

                {/* Bottom Content Body */}
                <div className="p-5 sm:p-6 flex flex-col justify-between flex-1 space-y-3.5">
                  <div>
                    <h4 className="font-display text-lg sm:text-xl font-bold text-zinc-950 leading-snug group-hover:text-zinc-700 transition-colors">
                      {rel.shortTitle}
                    </h4>

                    <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed mt-1.5 font-normal">
                      {MODULE_MICRO_DESCRIPTIONS[rel.slug] || rel.tagline}
                    </p>
                  </div>

                  {/* Explore Feature CTA */}
                  <div className="pt-2 border-t border-zinc-100">
                    <span className="inline-flex items-center gap-1.5 text-xs sm:text-[13px] font-bold text-zinc-950 group-hover:text-zinc-600 transition-colors">
                      <span>Explore Feature</span>
                      <ArrowRight className="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
                    </span>
                  </div>
                </div>
              </Link>
            ))}
          </div>
        </section>
      )}

    </div>
  );
}
