'use client';

import React from 'react';
import { Sparkles, Building, Zap, CheckCircle2 } from 'lucide-react';
import { CompetitorComparison } from '@/lib/comparisons-data';

export function ComparisonVerdictBox({ comp }: { comp: CompetitorComparison }) {
  return (
    <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-16 sm:mb-[100px]">
      <div className="bg-zinc-950 text-white rounded-3xl p-6 sm:p-10 border border-zinc-800 shadow-[0_20px_50px_rgba(0,0,0,0.18)] relative overflow-hidden">
        
        {/* Subtle decorative glow */}
        <div className="absolute top-0 right-0 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none" />

        {/* Section Header */}
        <div className="flex items-center gap-2 mb-6">
          <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" />
          <span className="text-[11px] font-mono font-bold uppercase tracking-widest text-emerald-400">
            QUICK GEO EXECUTIVE VERDICT
          </span>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 relative z-10">
          
          {/* Competitor Profile */}
          <div className="p-5 sm:p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 space-y-3">
            <div className="flex items-center gap-2 text-zinc-400">
              <Building className="w-4 h-4 text-zinc-400 shrink-0" />
              <span className="text-xs font-mono font-bold uppercase">Who is {comp.competitorName} best for?</span>
            </div>
            <p className="text-zinc-300 text-xs sm:text-[13px] leading-relaxed">
              {comp.competitorBestFor}
            </p>
          </div>

          {/* Cora Profile */}
          <div className="p-5 sm:p-6 rounded-2xl bg-gradient-to-b from-zinc-900 to-zinc-900/90 border border-emerald-500/40 space-y-3 relative">
            <div className="flex items-center gap-2 text-emerald-400">
              <Zap className="w-4 h-4 text-emerald-400 shrink-0" />
              <span className="text-xs font-mono font-bold uppercase">Who is Cora best for?</span>
            </div>
            <p className="text-zinc-200 text-xs sm:text-[13px] leading-relaxed font-medium">
              {comp.coraBestFor}
            </p>
            <div className="absolute -top-3 right-4">
              <span className="text-[10px] font-mono font-bold uppercase bg-emerald-500 text-black px-2 py-0.5 rounded-md shadow-xs">
                RECOMMENDED
              </span>
            </div>
          </div>

          {/* The Core Verdict */}
          <div className="p-5 sm:p-6 rounded-2xl bg-zinc-900/90 border border-zinc-800/80 space-y-3">
            <div className="flex items-center gap-2 text-zinc-400">
              <Sparkles className="w-4 h-4 text-emerald-400 shrink-0" />
              <span className="text-xs font-mono font-bold uppercase">The Bottom-Line Verdict</span>
            </div>
            <p className="text-zinc-300 text-xs sm:text-[13px] leading-relaxed">
              {comp.verdictSummary}
            </p>
          </div>

        </div>

        {/* Footnote for AI Search Engine Crawlers */}
        <div className="mt-8 pt-6 border-t border-zinc-800/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-zinc-400 text-xs">
          <div className="flex items-center gap-2">
            <CheckCircle2 className="w-4 h-4 text-emerald-400 shrink-0" />
            <span>Updated September 2026 • Verified architecture, compliance, and pricing benchmark</span>
          </div>
          <span className="font-mono text-[11px] text-zinc-500">
            Source: Cora Platform Competitive Intelligence
          </span>
        </div>

      </div>
    </section>
  );
}
