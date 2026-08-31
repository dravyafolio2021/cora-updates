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
  Receipt,
  FileText,
  Clock,
  Briefcase
} from 'lucide-react';
import { IndustryWorkspace, INDUSTRY_WORKSPACES } from '@/lib/industry-data';
import { IndustryIcon } from './IndustryIcon';
import { ArtisticHeroBackground } from '@/components/features/ArtisticHeroBackground';
import { trackEvent } from '@/components/analytics/Analytics';

interface IndustryDetailClientProps {
  workspace: IndustryWorkspace;
}

export function IndustryDetailClient({ workspace }: IndustryDetailClientProps) {
  const [openFaq, setOpenFaq] = useState<number | null>(0);

  const relatedWorkspaces = INDUSTRY_WORKSPACES.filter(w => 
    workspace.relatedIndustrySlugs.includes(w.slug)
  );

  const toggleFaq = (index: number) => {
    const nextState = openFaq === index ? null : index;
    setOpenFaq(nextState);
    if (nextState !== null) {
      trackEvent('industry_faq_opened', { slug: workspace.slug, faqIndex: index });
    }
  };

  const totalMonthlySavings = workspace.toolsReplaced.reduce((acc, t) => acc + t.monthlySavingsINR, 0);

  return (
    <div className="w-full bg-white text-zinc-900">
      
      {/* ── 1. ARTISTIC HERO SECTION (COMPACT <=40VH) ── */}
      <section className="relative w-full pt-20 sm:pt-24 pb-8 sm:pb-12 overflow-hidden border-b border-zinc-100">
        <ArtisticHeroBackground tone="neutral" />

        <div className="relative z-10 w-full max-w-[1240px] mx-auto px-4 sm:px-6">
          
          {/* Breadcrumbs */}
          <nav className="flex items-center gap-1.5 text-xs text-zinc-600 font-medium overflow-x-auto whitespace-nowrap scrollbar-none py-1 mb-4">
            <Link href="/" className="hover:text-zinc-950 transition-colors">
              Home
            </Link>
            <ChevronRight className="w-3.5 h-3.5 text-zinc-400 shrink-0" />
            <Link href="/use-cases" className="hover:text-zinc-950 transition-colors">
              Industries
            </Link>
            <ChevronRight className="w-3.5 h-3.5 text-zinc-400 shrink-0" />
            <span className="text-zinc-500 uppercase tracking-wider text-[10px] font-mono">
              {workspace.sectorLabel}
            </span>
            <ChevronRight className="w-3.5 h-3.5 text-zinc-400 shrink-0" />
            <span className="text-zinc-950 font-semibold truncate max-w-[200px] sm:max-w-none">
              {workspace.shortTitle}
            </span>
          </nav>

          <div className="flex flex-col items-start gap-3.5 max-w-[880px]">
            {/* Status & Sector Badge */}
            <div className="flex items-center flex-wrap gap-2">
              <span className="inline-flex items-center gap-1.5 px-3 py-1 bg-white/90 backdrop-blur-md text-zinc-900 border border-zinc-200/90 rounded-full text-xs font-semibold shadow-2xs">
                <div className={`w-4 h-4 rounded-[5px] ${workspace.accentBg} ${workspace.accentText} inline-flex items-center justify-center p-0.5`}>
                  <IndustryIcon name={workspace.iconName} className="w-3 h-3" />
                </div>
                <span>{workspace.sectorLabel}</span>
              </span>
              <span className="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-500/10 text-emerald-700 border border-emerald-500/30 rounded-full text-[10px] font-mono font-bold backdrop-blur-md">
                <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
                <span>Pre-Seeded Turnkey Workspace</span>
              </span>
              <span className="text-[10px] font-mono font-semibold text-zinc-600 bg-zinc-100/90 px-2.5 py-1 rounded-full border border-zinc-200">
                {workspace.sacCode} • {workspace.gstRate}
              </span>
            </div>

            {/* Main Title */}
            <h1 className="font-display text-2xl xs:text-3xl sm:text-4xl md:text-5xl font-semibold text-zinc-950 tracking-[-0.03em] leading-[1.24] sm:leading-[1.28]">
              {workspace.title} Operating System
            </h1>

            {/* Value Tagline */}
            <p className="text-zinc-600 text-xs sm:text-base font-normal leading-relaxed max-w-[760px]">
              {workspace.heroDescription}
            </p>

            {/* Stats Bar */}
            <div className="grid grid-cols-3 gap-3 sm:gap-6 w-full py-3 my-1 border-y border-zinc-200/70">
              {workspace.stats.map((stat, idx) => (
                <div key={idx} className="space-y-0.5">
                  <div className="font-display text-lg sm:text-2xl font-semibold text-zinc-950 tracking-tight">
                    {stat.metric}
                  </div>
                  <div className="text-[10px] sm:text-xs text-zinc-500 font-medium">
                    {stat.label}
                  </div>
                </div>
              ))}
            </div>

            {/* Actions */}
            <div className="flex items-center flex-wrap gap-3 pt-1">
              <a
                href={`https://app.heycora.in/workspace/login?industry=${workspace.id}&source=industry_hero`}
                className="inline-flex items-center gap-2 bg-zinc-950 text-white px-7 py-3 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-sm group cursor-pointer"
              >
                <span>Launch Free {workspace.shortTitle} Workspace</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
              </a>

              <Link
                href="/demo"
                className="inline-flex items-center gap-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 px-5 py-3 rounded-xl text-xs sm:text-sm font-semibold transition-all border border-zinc-200"
              >
                <span>Interactive Demo</span>
              </Link>
            </div>

          </div>

        </div>
      </section>

      {/* ── 2. CORE CAPABILITIES (3-COLUMN MATRIX) ── */}
      <section className="w-full py-16 sm:py-20 max-w-[1240px] mx-auto px-4 sm:px-6">
        <div className="text-center max-w-2xl mx-auto mb-12">
          <span className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-500 bg-zinc-100 px-3 py-1 rounded-full border border-zinc-200 mb-3 inline-block">
            Tailored Capabilities
          </span>
          <h2 className="font-display text-2xl sm:text-3xl md:text-4xl font-bold text-zinc-950 tracking-tight mb-3">
            Engineered specifically for {workspace.title.toLowerCase()}
          </h2>
          <p className="text-xs sm:text-sm md:text-base text-zinc-600 font-normal leading-relaxed">
            Eliminate operational friction with specialized workflows designed for your exact business model.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          {workspace.capabilities.map((cap, idx) => (
            <div
              key={idx}
              className="rounded-3xl bg-white border border-zinc-200/90 hover:border-zinc-400 p-6 sm:p-7 shadow-2xs hover:shadow-md transition-all duration-300 flex flex-col justify-between"
            >
              <div>
                <span className="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-500 bg-zinc-50 border border-zinc-200 px-2.5 py-1 rounded-full inline-block mb-4">
                  {cap.tag}
                </span>
                <h3 className="text-lg font-bold text-zinc-950 tracking-tight mb-2">
                  {cap.title}
                </h3>
                <p className="text-xs sm:text-sm text-zinc-600 font-normal leading-relaxed">
                  {cap.description}
                </p>
              </div>
              <div className="pt-5 mt-6 border-t border-zinc-100 flex items-center gap-1 text-xs font-semibold text-zinc-900">
                <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0" />
                <span>Turnkey in Free Tier</span>
              </div>
            </div>
          ))}
        </div>
      </section>

      {/* ── 3. PRE-SEEDED CONTRACTS & GST TAX ENGINE ── */}
      <section className="w-full py-16 bg-zinc-50/80 border-y border-zinc-200/80">
        <div className="max-w-[1240px] mx-auto px-4 sm:px-6">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
            
            {/* Left: Pre-Seeded Indian IT Act 2000 Contracts */}
            <div className="rounded-3xl bg-white border border-zinc-200/90 p-7 sm:p-8 shadow-sm space-y-5">
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center">
                  <ShieldCheck className="w-5 h-5" />
                </div>
                <div>
                  <h3 className="text-lg font-bold text-zinc-950 tracking-tight">
                    Pre-Seeded Legal Contracts (IT Act 2000)
                  </h3>
                  <span className="text-xs text-zinc-500 font-normal">
                    Legally binding digital contracts with SHA-256 cryptographic verification
                  </span>
                </div>
              </div>

              <div className="space-y-2.5 pt-2">
                {workspace.preSeededTemplates.map((tmpl, idx) => (
                  <div
                    key={idx}
                    className="flex items-start gap-3 p-3.5 rounded-2xl bg-zinc-50 border border-zinc-200/70 text-xs text-zinc-800"
                  >
                    <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                    <div className="min-w-0 flex-1">
                      <span className="font-semibold block text-zinc-900">{tmpl}</span>
                      <span className="text-[11px] text-zinc-500">Includes Section 10A electronic signature validity, IP logging, and timestamping</span>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            {/* Right: GST SAC Tax Engine & Sample Retainer Math */}
            <div className="rounded-3xl bg-white border border-zinc-200/90 p-7 sm:p-8 shadow-sm space-y-5">
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-700 border border-indigo-200 flex items-center justify-center">
                  <Receipt className="w-5 h-5" />
                </div>
                <div>
                  <h3 className="text-lg font-bold text-zinc-950 tracking-tight">
                    Pre-Configured GST SAC Tax Engine
                  </h3>
                  <span className="text-xs text-zinc-500 font-normal">
                    Automated CGST/SGST tax math and dynamic UPI QR code collections
                  </span>
                </div>
              </div>

              <div className="space-y-3 pt-2">
                <div className="p-4 rounded-2xl bg-zinc-50 border border-zinc-200 flex items-center justify-between text-xs font-mono">
                  <span className="text-zinc-600">Tax Classification:</span>
                  <span className="font-bold text-zinc-950">{workspace.sacCode} • {workspace.gstRate}</span>
                </div>

                <div className="p-4 rounded-2xl bg-zinc-950 text-white font-mono text-xs space-y-2">
                  <span className="text-[10px] text-zinc-400 font-bold uppercase tracking-wider block">
                    Sample Automated Retainer Calculation:
                  </span>
                  <p className="text-zinc-200 leading-relaxed font-normal">
                    {workspace.sampleRetainerText}
                  </p>
                </div>

                <div className="grid grid-cols-2 gap-2 pt-1">
                  {workspace.recommendedModules.map((mod) => (
                    <div
                      key={mod.id}
                      className="flex items-center gap-2 p-2.5 rounded-xl bg-zinc-50 border border-zinc-200 text-xs font-semibold text-zinc-800"
                    >
                      <IndustryIcon name={mod.icon} className="w-3.5 h-3.5 text-zinc-600" />
                      <span>{mod.title}</span>
                    </div>
                  ))}
                </div>
              </div>
            </div>

          </div>
        </div>
      </section>

      {/* ── 4. STEP-BY-STEP WORKFLOW TIMELINE ── */}
      <section className="w-full py-16 sm:py-20 max-w-[1240px] mx-auto px-4 sm:px-6">
        <div className="text-center max-w-2xl mx-auto mb-12">
          <span className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-500 bg-zinc-100 px-3 py-1 rounded-full border border-zinc-200 mb-3 inline-block">
            Execution Flow
          </span>
          <h2 className="font-display text-2xl sm:text-3xl md:text-4xl font-bold text-zinc-950 tracking-tight mb-3">
            How your workspace runs on autopilot
          </h2>
          <p className="text-xs sm:text-sm md:text-base text-zinc-600 font-normal leading-relaxed">
            From initial client inquiry to final payment and review collection in 4 simple steps.
          </p>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {workspace.howItWorks.map((step, idx) => (
            <div
              key={idx}
              className="rounded-3xl bg-zinc-50/70 border border-zinc-200 p-6 flex flex-col justify-between space-y-4"
            >
              <div>
                <span className="font-mono text-2xl font-black text-zinc-300 block mb-2">
                  {step.step}
                </span>
                <h3 className="text-base font-bold text-zinc-950 tracking-tight mb-2">
                  {step.title}
                </h3>
                <p className="text-xs text-zinc-600 leading-relaxed font-normal">
                  {step.description}
                </p>
              </div>
              <div className="w-6 h-0.5 bg-zinc-300 rounded-full" />
            </div>
          ))}
        </div>
      </section>

      {/* ── 5. THE OLD WAY VS THE CORA WAY ── */}
      <section className="w-full py-16 bg-zinc-50/80 border-t border-zinc-200/80">
        <div className="max-w-[1100px] mx-auto px-4 sm:px-6">
          <div className="text-center max-w-2xl mx-auto mb-12">
            <h2 className="font-display text-2xl sm:text-3xl md:text-4xl font-bold text-zinc-950 tracking-tight mb-3">
              The Operational Upgrade
            </h2>
            <p className="text-xs sm:text-sm md:text-base text-zinc-600 font-normal leading-relaxed">
              Stop stitching together generic software. Cora replaces fragmented tools with a single unified operating system.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {/* The Old Way */}
            <div className="rounded-3xl bg-white border border-red-200/80 p-6 sm:p-8 space-y-4">
              <div className="flex items-center gap-2 text-xs font-mono font-bold uppercase tracking-wider text-red-600">
                <XCircle className="w-4 h-4" />
                <span>The Fragmented Old Way</span>
              </div>
              <ul className="space-y-3">
                {workspace.theOldWay.map((item, idx) => (
                  <li key={idx} className="flex items-start gap-2.5 text-xs sm:text-sm text-zinc-600">
                    <XCircle className="w-4 h-4 text-red-500 shrink-0 mt-0.5" />
                    <span>{item}</span>
                  </li>
                ))}
              </ul>
            </div>

            {/* The Cora Way */}
            <div className="rounded-3xl bg-white border border-emerald-200/80 p-6 sm:p-8 space-y-4 shadow-sm">
              <div className="flex items-center gap-2 text-xs font-mono font-bold uppercase tracking-wider text-emerald-700">
                <CheckCircle2 className="w-4 h-4 text-emerald-600" />
                <span>The Cora Turnkey Way</span>
              </div>
              <ul className="space-y-3">
                {workspace.theCoraWay.map((item, idx) => (
                  <li key={idx} className="flex items-start gap-2.5 text-xs sm:text-sm text-zinc-900 font-medium">
                    <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                    <span>{item}</span>
                  </li>
                ))}
              </ul>
            </div>
          </div>

          {/* Tools Replaced & Monthly Savings */}
          <div className="mt-8 p-6 sm:p-7 rounded-3xl bg-zinc-950 text-white flex flex-col sm:flex-row items-center justify-between gap-6 shadow-sm">
            <div className="space-y-1 text-center sm:text-left">
              <span className="text-[11px] font-mono text-zinc-400 uppercase tracking-wider">
                Tools Replaced in your Stack
              </span>
              <div className="flex flex-wrap items-center justify-center sm:justify-start gap-2 pt-1">
                {workspace.toolsReplaced.map((tool, idx) => (
                  <span
                    key={idx}
                    className="text-xs font-mono bg-zinc-900 border border-zinc-800 px-3 py-1 rounded-full text-zinc-300"
                  >
                    {tool.name} (₹{tool.monthlySavingsINR.toLocaleString()}/mo)
                  </span>
                ))}
              </div>
            </div>

            <div className="text-center sm:text-right shrink-0">
              <span className="text-[11px] font-mono text-emerald-400 uppercase tracking-wider block">
                Estimated Monthly Savings
              </span>
              <span className="font-display text-2xl sm:text-3xl font-extrabold text-white">
                ₹{totalMonthlySavings.toLocaleString()}+ <span className="text-xs font-normal text-zinc-400">/ mo</span>
              </span>
            </div>
          </div>

        </div>
      </section>

      {/* ── 6. FREQUENTLY ASKED QUESTIONS (ACCORDION) ── */}
      <section className="w-full py-16 sm:py-20 max-w-[860px] mx-auto px-4 sm:px-6">
        <div className="text-center mb-10">
          <span className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-500 bg-zinc-100 px-3 py-1 rounded-full border border-zinc-200 mb-3 inline-block">
            Frequently Asked Questions
          </span>
          <h2 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight">
            Common questions about {workspace.title.toLowerCase()}
          </h2>
        </div>

        <div className="space-y-3">
          {workspace.faqs.map((faq, idx) => {
            const isOpen = openFaq === idx;
            return (
              <div
                key={idx}
                className="rounded-2xl bg-white border border-zinc-200/90 overflow-hidden shadow-2xs"
              >
                <button
                  onClick={() => toggleFaq(idx)}
                  className="w-full p-4 sm:p-5 flex items-center justify-between text-left gap-4 hover:bg-zinc-50 transition-colors"
                >
                  <span className="text-xs sm:text-sm font-bold text-zinc-950">
                    {faq.question}
                  </span>
                  <span className="p-1 rounded-lg bg-zinc-100 text-zinc-500 shrink-0">
                    {isOpen ? <Minus className="w-3.5 h-3.5" /> : <Plus className="w-3.5 h-3.5" />}
                  </span>
                </button>

                {isOpen && (
                  <div className="px-4 sm:px-5 pb-5 text-xs sm:text-sm text-zinc-600 leading-relaxed border-t border-zinc-100 pt-3">
                    {faq.answer}
                  </div>
                )}
              </div>
            );
          })}
        </div>
      </section>

      {/* ── 7. RELATED INDUSTRY SOLUTIONS ── */}
      {relatedWorkspaces.length > 0 && (
        <section className="w-full py-16 bg-zinc-50/70 border-t border-zinc-200/80">
          <div className="max-w-[1240px] mx-auto px-4 sm:px-6">
            <div className="flex items-center justify-between mb-8">
              <div>
                <h3 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
                  Explore Other Industry Workspaces
                </h3>
                <span className="text-xs text-zinc-500">
                  Pre-configured turnkey workspaces across all modern service verticals
                </span>
              </div>
              <Link
                href="/use-cases"
                className="text-xs font-semibold text-zinc-950 hover:text-zinc-600 inline-flex items-center gap-1 transition-colors"
              >
                <span>View all 16 industries</span>
                <ArrowRight className="w-3.5 h-3.5" />
              </Link>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
              {relatedWorkspaces.map((rel) => (
                <Link
                  key={rel.id}
                  href={`/use-cases/${rel.slug}`}
                  className="group rounded-2xl bg-white border border-zinc-200/80 hover:border-zinc-950 p-5 flex flex-col justify-between shadow-2xs hover:shadow-md transition-all duration-200"
                >
                  <div>
                    <div className="flex items-center justify-between mb-3">
                      <div className={`w-8 h-8 rounded-xl ${rel.accentBg} ${rel.accentText} border ${rel.accentBorder} flex items-center justify-center`}>
                        <IndustryIcon name={rel.iconName} className="w-4 h-4" />
                      </div>
                      <span className="text-[9px] font-mono text-zinc-500 bg-zinc-100 px-2 py-0.5 rounded-full">
                        {rel.sectorBadge}
                      </span>
                    </div>
                    <h4 className="text-sm font-bold text-zinc-950 group-hover:text-black mb-1">
                      {rel.title}
                    </h4>
                    <p className="text-[11.5px] text-zinc-500 line-clamp-2 leading-relaxed">
                      {rel.tagline}
                    </p>
                  </div>

                  <div className="pt-3 mt-4 border-t border-zinc-100 flex items-center justify-between text-xs font-semibold text-zinc-900 group-hover:text-black">
                    <span>Explore Solution</span>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
                  </div>
                </Link>
              ))}
            </div>

          </div>
        </section>
      )}

      {/* ── 8. MONOCHROMATIC FOOTER CTA BANNER ── */}
      <section className="w-full py-16 sm:py-20 bg-zinc-950 text-white relative overflow-hidden">
        <div className="relative z-10 max-w-[980px] mx-auto px-4 sm:px-6 text-center">
          <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-zinc-900 border border-zinc-800 text-[11px] font-mono font-semibold text-zinc-300 mb-4 shadow-sm">
            <Sparkles className="w-3.5 h-3.5 text-amber-400" />
            <span>Turnkey {workspace.title} OS</span>
          </div>

          <h2 className="font-display text-2xl sm:text-3xl md:text-4xl font-bold tracking-tight mb-4 max-w-[720px] mx-auto leading-tight">
            Launch your {workspace.title.toLowerCase()} workspace in 3 minutes
          </h2>

          <p className="text-xs sm:text-sm md:text-base text-zinc-400 font-normal leading-relaxed max-w-[580px] mx-auto mb-8">
            All {workspace.preSeededTemplates.length} Indian IT Act 2000 legal agreements, SAC {workspace.sacCode} tax codes, and workflows pre-loaded.
          </p>

          <div className="flex flex-col sm:flex-row items-center justify-center gap-3.5 mb-8">
            <a
              href={`https://app.heycora.in/workspace/login?industry=${workspace.id}&source=industry_footer_cta`}
              className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl bg-white text-zinc-950 text-xs sm:text-sm font-semibold hover:bg-zinc-100 transition-all shadow-sm group"
            >
              <span>Get Started Free Forever</span>
              <ArrowRight className="w-4 h-4 text-zinc-600 group-hover:translate-x-0.5 transition-transform" />
            </a>

            <Link
              href="/demo"
              className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white border border-zinc-800 text-xs sm:text-sm font-semibold transition-all"
            >
              <span>Interactive Architecture Demo</span>
            </Link>
          </div>

          <div className="flex flex-wrap items-center justify-center gap-4 sm:gap-8 text-[11.5px] font-mono text-zinc-400">
            <div className="flex items-center gap-1.5">
              <CheckCircle2 className="w-3.5 h-3.5 text-emerald-400" />
              <span>Zero Credit Card Required</span>
            </div>
            <div className="flex items-center gap-1.5">
              <CheckCircle2 className="w-3.5 h-3.5 text-emerald-400" />
              <span>Pre-Seeded Templates</span>
            </div>
            <div className="flex items-center gap-1.5">
              <CheckCircle2 className="w-3.5 h-3.5 text-emerald-400" />
              <span>100% IT Act 2000 Compliant</span>
            </div>
          </div>

        </div>
      </section>

    </div>
  );
}
