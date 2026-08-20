'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { 
  CheckCircle2, 
  ArrowRight, 
  Sparkles, 
  ShieldCheck, 
  Layers, 
  Zap,
  DollarSign,
  Receipt,
  FileText,
  Camera,
  Bot
} from 'lucide-react';
import { COMPARISONS_DATA } from '@/lib/comparisons-data';
import { trackEvent } from '@/components/analytics/Analytics';

export default function CompareDirectoryPage() {
  const comparisons = Object.values(COMPARISONS_DATA);

  return (
    <main className="w-full relative pt-32 sm:pt-40 pb-24 overflow-hidden bg-white">
      
      {/* ── Hero Section ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 text-center mb-16 sm:mb-20">
        <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-zinc-100 rounded-xl border border-zinc-200/80 text-xs font-semibold text-zinc-900 mb-4 shadow-2xs">
          <Sparkles className="w-3.5 h-3.5 text-emerald-500" />
          <span>Competitive Architecture Benchmarks</span>
        </div>

        <h1 className="font-display text-4xl xs:text-5xl sm:text-6xl font-bold text-zinc-950 leading-[1.1] tracking-[-0.035em] max-w-[940px] mx-auto mb-5">
          See how Cora compares to existing market tools
        </h1>

        <p className="text-zinc-600 text-base sm:text-xl font-normal leading-relaxed max-w-[720px] mx-auto mb-8">
          Detailed, transparent head-to-head comparisons showing how Cora replaces 8+ fragmented SaaS subscriptions with one autonomous creative studio operating system.
        </p>
      </section>

      {/* ── Comparison Cards Grid ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-24">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {comparisons.map((comp) => (
            <Link
              key={comp.slug}
              href={`/compare/${comp.slug}`}
              onClick={() => trackEvent('compare_card_clicked', { competitor: comp.competitorName })}
              className="bg-white rounded-[24px] border border-zinc-200/90 p-6 sm:p-7 flex flex-col justify-between hover:shadow-[0_15px_35px_rgba(0,0,0,0.06)] hover:border-zinc-300 hover:-translate-y-1 transition-all group"
            >
              <div className="space-y-4">
                <div className="flex items-center justify-between">
                  <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">
                    {comp.category}
                  </span>
                  <span className="text-[10px] font-mono font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/80 px-2 py-0.5 rounded-md">
                    VS CORA
                  </span>
                </div>

                <div>
                  <h2 className="font-display text-lg sm:text-xl font-bold text-zinc-950 group-hover:text-black">
                    Cora vs {comp.competitorName}
                  </h2>
                  <p className="text-zinc-500 text-xs mt-1">
                    {comp.competitorTagline}
                  </p>
                </div>

                <p className="text-zinc-600 text-xs sm:text-[13px] leading-relaxed line-clamp-3">
                  {comp.verdictSummary}
                </p>
              </div>

              <div className="pt-5 border-t border-zinc-100 mt-5 flex items-center justify-between">
                <span className="text-xs font-bold text-zinc-900 group-hover:text-emerald-600 transition-colors">
                  View Full Comparison
                </span>
                <ArrowRight className="w-4 h-4 text-zinc-400 group-hover:text-emerald-600 group-hover:translate-x-1 transition-all" />
              </div>
            </Link>
          ))}
        </div>
      </section>

      {/* ── Value Summary Banner ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        <div className="w-full rounded-[36px] bg-gradient-to-br from-[#0F172A] via-[#1E293B] to-[#0A0D12] text-white p-8 sm:p-14 text-center relative overflow-hidden border border-zinc-800 shadow-xl">
          <div className="relative z-10 max-w-[680px] mx-auto space-y-6">
            <h2 className="font-display text-3xl sm:text-4xl font-bold tracking-tight">
              Ready to upgrade your studio operating system?
            </h2>
            <p className="text-zinc-400 text-sm sm:text-base leading-relaxed font-normal">
              Join leading commercial photo studios, luxury wedding planners, and creative agencies streamlining their operations on Cora.
            </p>

            <div className="flex items-center justify-center flex-wrap gap-3.5 pt-2">
              <a
                href="https://app.heycora.in/workspace/login?source=compare_hub"
                className="inline-flex items-center gap-2 bg-white text-zinc-950 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-100 transition-all shadow-sm group"
              >
                <span>Get started for Free</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-600 group-hover:translate-x-0.5 transition-transform" />
              </a>

              <a
                href="mailto:dravya.bansal@heycora.in?subject=Competitor%20Migration%20Inquiry"
                className="inline-flex items-center gap-2 bg-zinc-900 text-white border border-zinc-700 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-2xs"
              >
                <span>Chat with Founder</span>
              </a>
            </div>
          </div>
        </div>
      </section>

    </main>
  );
}
