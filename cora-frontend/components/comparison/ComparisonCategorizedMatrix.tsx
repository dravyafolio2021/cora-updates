'use client';

import React, { useState } from 'react';
import { CheckCircle2, XCircle, Info, Sparkles } from 'lucide-react';
import { CompetitorComparison } from '@/lib/comparisons-data';

export function ComparisonCategorizedMatrix({ comp }: { comp: CompetitorComparison }) {
  // Extract unique categories
  const categories = Array.from(new Set(comp.featuresTable.map(f => f.category || 'General Capabilities')));
  const [selectedCategory, setSelectedCategory] = useState<string>('All');

  const filteredFeatures = selectedCategory === 'All'
    ? comp.featuresTable
    : comp.featuresTable.filter(f => (f.category || 'General Capabilities') === selectedCategory);

  return (
    <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-16 sm:mb-[100px]">
      
      {/* Matrix Header */}
      <div className="text-center max-w-[720px] mx-auto mb-10">
        <span className="text-[11px] font-mono font-bold uppercase tracking-widest text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200/60 inline-block mb-3">
          TECHNICAL MATRIX
        </span>
        <h2 className="font-display text-2xl sm:text-4xl font-bold text-zinc-950 tracking-tight mb-3">
          Side-by-Side Architectural Breakdown
        </h2>
        <p className="text-zinc-600 text-xs sm:text-sm">
          Examine exact feature capabilities, compliance standards, and automation limits between Cora and {comp.competitorName}.
        </p>
      </div>

      {/* Category Filter Pills */}
      {categories.length > 1 && (
        <div className="flex items-center justify-center flex-wrap gap-2 mb-8">
          <button
            onClick={() => setSelectedCategory('All')}
            className={`px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all ${
              selectedCategory === 'All'
                ? 'bg-zinc-950 text-white shadow-xs'
                : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200/70 hover:text-zinc-900'
            }`}
          >
            All Features ({comp.featuresTable.length})
          </button>
          {categories.map((cat, idx) => (
            <button
              key={idx}
              onClick={() => setSelectedCategory(cat)}
              className={`px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all ${
                selectedCategory === cat
                  ? 'bg-zinc-950 text-white shadow-xs'
                  : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200/70 hover:text-zinc-900'
              }`}
            >
              {cat}
            </button>
          ))}
        </div>
      )}

      {/* Comparison Table */}
      <div className="w-full overflow-x-auto rounded-[28px] border border-zinc-200/90 bg-white shadow-[0_12px_36px_rgba(0,0,0,0.04)]">
        <table className="w-full text-left border-collapse min-w-[620px]">
          <thead>
            <tr className="bg-zinc-50/90 border-b border-zinc-200 text-zinc-900 text-xs font-bold">
              <th className="p-4 sm:p-5 w-[42%] font-display">Capability / Workflow</th>
              <th className="p-4 sm:p-5 w-[29%] bg-emerald-500/10 text-emerald-950 font-display font-extrabold text-sm border-x border-emerald-500/20">
                <div className="flex items-center gap-1.5">
                  <span>Cora Platform</span>
                  <span className="text-[10px] font-mono px-1.5 py-0.2 bg-emerald-600 text-white rounded font-bold">OS</span>
                </div>
              </th>
              <th className="p-4 sm:p-5 w-[29%] text-zinc-600 font-display">
                {comp.competitorName}
              </th>
            </tr>
          </thead>
          <tbody className="divide-y divide-zinc-100 text-xs">
            {filteredFeatures.map((row, idx) => (
              <tr key={idx} className="hover:bg-zinc-50/60 transition-colors">
                
                {/* Feature & Note */}
                <td className="p-4 sm:p-5">
                  <div className="font-bold text-zinc-900 text-[13px]">{row.feature}</div>
                  {row.note && (
                    <div className="text-[11px] text-zinc-500 mt-1 flex items-start gap-1">
                      <Info className="w-3.5 h-3.5 text-zinc-400 shrink-0 mt-0.5" />
                      <span>{row.note}</span>
                    </div>
                  )}
                </td>

                {/* Cora Column */}
                <td className="p-4 sm:p-5 bg-emerald-50/20 border-x border-emerald-500/10 font-semibold text-zinc-950">
                  {typeof row.cora === 'boolean' ? (
                    row.cora ? (
                      <span className="inline-flex items-center gap-1.5 text-emerald-700 font-bold">
                        <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0" />
                        <span>Included Free</span>
                      </span>
                    ) : (
                      <span className="inline-flex items-center gap-1.5 text-zinc-400">
                        <XCircle className="w-4 h-4 text-zinc-300 shrink-0" />
                        <span>Not Available</span>
                      </span>
                    )
                  ) : (
                    <span className="text-emerald-900 font-bold flex items-center gap-1.5">
                      <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0" />
                      {row.cora}
                    </span>
                  )}
                </td>

                {/* Competitor Column */}
                <td className="p-4 sm:p-5 text-zinc-600">
                  {typeof row.competitor === 'boolean' ? (
                    row.competitor ? (
                      <span className="inline-flex items-center gap-1.5 text-zinc-700 font-medium">
                        <CheckCircle2 className="w-4 h-4 text-zinc-400 shrink-0" />
                        <span>Available</span>
                      </span>
                    ) : (
                      <span className="inline-flex items-center gap-1.5 text-rose-500 font-semibold">
                        <XCircle className="w-4 h-4 text-rose-400 shrink-0" />
                        <span>Missing / Extra Fee</span>
                      </span>
                    )
                  ) : (
                    <span className="text-zinc-600">{row.competitor}</span>
                  )}
                </td>

              </tr>
            ))}
          </tbody>
        </table>
      </div>

    </section>
  );
}
