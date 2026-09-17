'use client';

import React from 'react';
import { Layers, ArrowRight, CheckCircle2, TrendingDown, DollarSign } from 'lucide-react';
import { CompetitorComparison } from '@/lib/comparisons-data';

export function ComparisonConsolidationStack({ comp }: { comp: CompetitorComparison }) {
  if (!comp.consolidatedTools || comp.consolidatedTools.length === 0) {
    return null;
  }

  return (
    <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-16 sm:mb-[100px]">
      <div className="bg-zinc-50 rounded-3xl border border-zinc-200/90 p-6 sm:p-10">
        
        {/* Section Headline */}
        <div className="text-center max-w-[680px] mx-auto mb-10">
          <span className="text-[11px] font-mono font-bold uppercase tracking-widest text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200/60 inline-block mb-3">
            SUBSCRIPTION CONSOLIDATION
          </span>
          <h2 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight mb-2">
            Eliminate {comp.consolidatedTools.length}+ Fragmented Subscriptions
          </h2>
          <p className="text-zinc-600 text-xs sm:text-sm">
            Stop paying separate monthly invoices for e-signatures, CRM, storage, and invoicing. Cora consolidates your operational tool stack into one unified, low-overhead workspace.
          </p>
        </div>

        {/* Tools Breakdown Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
          {comp.consolidatedTools.map((tool, idx) => (
            <div 
              key={idx}
              className="bg-white rounded-2xl border border-zinc-200/80 p-5 shadow-2xs hover:border-zinc-300 transition-all flex flex-col justify-between"
            >
              <div className="space-y-1.5">
                <div className="flex items-center justify-between">
                  <span className="text-xs font-bold text-zinc-900">{tool.toolName}</span>
                  <span className="text-xs font-mono font-semibold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md">
                    {tool.estimatedCost}
                  </span>
                </div>
                <div className="flex items-center gap-1.5 text-zinc-400 text-xs pt-2 border-t border-zinc-100">
                  <ArrowRight className="w-3 h-3 text-emerald-600 shrink-0" />
                  <span className="text-[11px] text-zinc-600 font-medium">Replaced by Cora: <strong className="text-zinc-900">{tool.coraReplacement}</strong></span>
                </div>
              </div>
            </div>
          ))}
        </div>

        {/* Total Savings Card */}
        <div className="bg-emerald-600 text-white rounded-2xl p-6 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
              <TrendingDown className="w-5 h-5 text-white" />
            </div>
            <div>
              <h4 className="font-display text-base font-bold">
                {comp.priceComparison.savingsPerYear}
              </h4>
              <p className="text-emerald-100 text-xs">
                {comp.priceComparison.monthlyEquivalent}
              </p>
            </div>
          </div>

          <a
            href="https://app.heycora.in/workspace/login?source=compare_savings"
            className="bg-white text-zinc-950 font-bold px-5 py-2.5 rounded-xl text-xs hover:bg-zinc-100 transition-all shrink-0 shadow-xs"
          >
            Start Consolidating Free
          </a>
        </div>

      </div>
    </section>
  );
}
