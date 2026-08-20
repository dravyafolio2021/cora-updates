'use client';

import React from 'react';
import Link from 'next/link';
import { Calculator, Sparkles, ArrowRight, ArrowUpRight } from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

export function ToolsBanner() {
  return (
    <section className="py-12 relative z-10 bg-zinc-950 text-white border-t border-zinc-800">
      <div className="w-full max-w-[1140px] mx-auto px-4 sm:px-6">
        <div className="flex flex-col md:flex-row items-center justify-between gap-8">
          
          <div className="max-w-[540px]">
            <div className="inline-flex items-center gap-1.5 px-3 py-1 bg-white/10 rounded-full text-xs font-semibold text-zinc-300 mb-3">
              <Sparkles className="w-3.5 h-3.5 text-purple-400" />
              <span>100% Free Public Micro-Tools</span>
            </div>
            <h3 className="font-display text-2xl font-bold text-white mb-2">
              Free Utilities for Founders & Studios
            </h3>
            <p className="text-xs sm:text-sm text-zinc-400 leading-relaxed">
              No account or credit card required. Calculate GST invoices or generate high-converting listing descriptions instantly in your browser.
            </p>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full md:w-auto">
            {/* Tool 1: GST Calculator */}
            <Link
              href="/tools/gst-calculator"
              onClick={() => trackEvent('banner_tool_clicked', { tool: 'gst-calculator' })}
              className="bg-zinc-900/90 border border-zinc-800 hover:border-zinc-700 p-4 rounded-xl flex items-center justify-between gap-4 group transition-all hover:-translate-y-0.5 shadow-sm"
            >
              <div className="flex items-center gap-3">
                <div className="w-9 h-9 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                  <Calculator className="w-4 h-4" />
                </div>
                <div>
                  <div className="text-xs font-bold text-white group-hover:text-emerald-400 transition-colors">
                    GST Calculator
                  </div>
                  <div className="text-[0.6875rem] text-zinc-400">
                    B2B Invoices & Tax Math
                  </div>
                </div>
              </div>
              <ArrowUpRight className="w-4 h-4 text-zinc-500 group-hover:text-white transition-colors" />
            </Link>

            {/* Tool 2: Listing AI */}
            <Link
              href="/tools/listing-ai"
              onClick={() => trackEvent('banner_tool_clicked', { tool: 'listing-ai' })}
              className="bg-zinc-900/90 border border-zinc-800 hover:border-zinc-700 p-4 rounded-xl flex items-center justify-between gap-4 group transition-all hover:-translate-y-0.5 shadow-sm"
            >
              <div className="flex items-center gap-3">
                <div className="w-9 h-9 rounded-lg bg-purple-500/10 text-purple-400 flex items-center justify-center border border-purple-500/20">
                  <Sparkles className="w-4 h-4" />
                </div>
                <div>
                  <div className="text-xs font-bold text-white group-hover:text-purple-400 transition-colors">
                    Listing AI Copilot
                  </div>
                  <div className="text-[0.6875rem] text-zinc-400">
                    GEO & SEO Generator
                  </div>
                </div>
              </div>
              <ArrowUpRight className="w-4 h-4 text-zinc-500 group-hover:text-white transition-colors" />
            </Link>
          </div>

        </div>
      </div>
    </section>
  );
}
