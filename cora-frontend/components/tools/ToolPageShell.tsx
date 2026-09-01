'use client';

import React from 'react';
import Link from 'next/link';
import { 
  ArrowLeft, 
  Sparkles, 
  ArrowRight, 
  CheckCircle2, 
  ShieldCheck, 
  Zap, 
  Lock,
  ExternalLink
} from 'lucide-react';

export interface ToolPageShellProps {
  toolId: string;
  badgeTag: string;
  title: string;
  subtitle: string;
  children: React.ReactNode;
  promoTitle: string;
  promoSubtitle: string;
  promoHighlights: string[];
  promoCtaText?: string;
  faqItems?: Array<{ question: string; answer: string }>;
  relatedToolSlugs?: string[];
}

export function ToolPageShell({
  toolId,
  badgeTag,
  title,
  subtitle,
  children,
  promoTitle,
  promoSubtitle,
  promoHighlights,
  promoCtaText = 'Launch Free Workspace',
  faqItems,
  relatedToolSlugs = [],
}: ToolPageShellProps) {
  return (
    <div className="w-full bg-white text-zinc-900 min-h-screen py-10 sm:py-16 selection:bg-zinc-900 selection:text-white">
      <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        
        {/* ── Top Back Navigation ── */}
        <div className="mb-6 sm:mb-8">
          <Link
            href="/tools"
            className="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-500 hover:text-zinc-950 transition-colors"
          >
            <ArrowLeft className="w-3.5 h-3.5" />
            <span>Back to all micro-tools</span>
          </Link>
        </div>

        {/* ── Header Title & Badge ── */}
        <div className="mb-8 sm:mb-10 max-w-3xl">
          <div className="inline-flex items-center gap-1.5 text-[11px] font-mono font-bold text-zinc-800 px-3 py-1 bg-zinc-100 border border-zinc-200/80 rounded-full mb-3">
            <span>{badgeTag}</span>
          </div>
          <h1 className="font-display text-2xl sm:text-4xl lg:text-5xl font-extrabold text-zinc-950 tracking-[-0.035em] leading-[1.18] mb-3">
            {title}
          </h1>
          <p className="text-zinc-600 text-xs sm:text-base font-normal leading-relaxed">
            {subtitle}
          </p>
        </div>

        {/* ── Master 70% / 30% Split Layout ── */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          
          {/* ══════════════════════════════════════════════════════════════
              70% LEFT SECTION (The Actual Working Interactive Tool)
              ══════════════════════════════════════════════════════════════ */}
          <div className="lg:col-span-8 space-y-6 sm:space-y-8">
            
            {/* The Tool Engine */}
            <div className="w-full">
              {children}
            </div>

            {/* Privacy & Trust Bar */}
            <div className="p-4 rounded-2xl bg-zinc-50 border border-zinc-200/80 flex flex-wrap items-center justify-between gap-3 text-xs text-zinc-600">
              <div className="flex items-center gap-2">
                <Lock className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                <span className="font-medium">100% Client-Side Execution (Zero financial or client data stored)</span>
              </div>
              <div className="flex items-center gap-2 font-mono text-[11px] text-zinc-500">
                <Zap className="w-3 h-3 text-amber-500 shrink-0" />
                <span>Zero Login Required</span>
              </div>
            </div>

            {/* Optional FAQ Accordion for the Tool */}
            {faqItems && faqItems.length > 0 && (
              <div className="pt-6 border-t border-zinc-100 space-y-4">
                <h3 className="font-display text-lg font-bold text-zinc-950 tracking-tight">
                  Frequently Asked Questions
                </h3>
                <div className="space-y-3">
                  {faqItems.map((faq, idx) => (
                    <div key={idx} className="p-4 rounded-2xl bg-white border border-zinc-200/80 shadow-2xs">
                      <h4 className="text-xs sm:text-sm font-bold text-zinc-900 mb-1.5">
                        {faq.question}
                      </h4>
                      <p className="text-xs text-zinc-600 leading-relaxed font-normal">
                        {faq.answer}
                      </p>
                    </div>
                  ))}
                </div>
              </div>
            )}

          </div>

          {/* ══════════════════════════════════════════════════════════════
              30% RIGHT SECTION (High-Converting Product Advertisement Card)
              ══════════════════════════════════════════════════════════════ */}
          <div className="lg:col-span-4 lg:sticky lg:top-24 space-y-4">
            
            {/* Main Sponsored Product Ad Billboard */}
            <div className="rounded-3xl bg-gradient-to-b from-[#F4F7FF] via-[#EEF2FF] to-white border-2 border-indigo-200/90 p-6 sm:p-7 shadow-[0_16px_40px_rgba(79,70,229,0.08)] relative overflow-hidden flex flex-col justify-between">
              
              {/* Top Sponsor Pill */}
              <div className="flex items-center justify-between gap-2 mb-4">
                <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-600 text-white text-[10px] font-mono font-bold uppercase tracking-wider shadow-2xs">
                  <Sparkles className="w-3 h-3 text-amber-300" />
                  <span>Cora Platform Ad</span>
                </span>
                <span className="text-[10px] font-mono text-indigo-600 font-bold">
                  Free Tier Available
                </span>
              </div>

              {/* Ad Copy */}
              <div className="space-y-3 mb-5">
                <h3 className="font-display text-lg sm:text-xl font-extrabold tracking-tight text-zinc-950 leading-snug">
                  {promoTitle}
                </h3>
                <p className="text-xs text-zinc-600 font-normal leading-relaxed">
                  {promoSubtitle}
                </p>
              </div>

              {/* Feature Checklist */}
              <div className="space-y-2.5 py-3.5 border-y border-indigo-100/90 mb-5">
                {promoHighlights.map((hl, idx) => (
                  <div key={idx} className="flex items-start gap-2 text-xs text-zinc-800 font-medium">
                    <CheckCircle2 className="w-4 h-4 text-indigo-600 shrink-0 mt-0.5" />
                    <span>{hl}</span>
                  </div>
                ))}
              </div>

              {/* High-Contrast Conversion CTA Button */}
              <div>
                <a
                  href="http://cora.local/workspace/login"
                  className="w-full flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs sm:text-sm transition-all shadow-md hover:scale-[1.02] cursor-pointer"
                >
                  <span>{promoCtaText}</span>
                  <ArrowRight className="w-4 h-4 text-zinc-400" />
                </a>
                <span className="block text-center text-[10.5px] text-zinc-500 mt-2 font-medium">
                  Instant Access • Zero Credit Card • Setup in 2 mins
                </span>
              </div>

            </div>

            {/* Subtle Statutory Tag */}
            <div className="rounded-2xl bg-white border border-zinc-200/80 p-3.5 text-[11px] text-zinc-500 flex items-center gap-2 shadow-2xs">
              <ShieldCheck className="w-4 h-4 text-indigo-600 shrink-0" />
              <span>Compliant with Indian IT Act 2000 &amp; SAC 9983 standard.</span>
            </div>

          </div>

        </div>

      </div>
    </div>
  );
}
