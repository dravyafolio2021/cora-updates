'use client';

import React from 'react';
import Link from 'next/link';
import { ArrowRight, ShieldCheck, Sparkles, MessageSquare } from 'lucide-react';
import { CompetitorComparison } from '@/lib/comparisons-data';

export function ComparisonCtaBanner({ comp }: { comp: CompetitorComparison }) {
  return (
    <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-24">
      <div className="rounded-3xl bg-zinc-950 text-white p-8 sm:p-14 border border-zinc-800 text-center relative overflow-hidden shadow-[0_24px_60px_rgba(0,0,0,0.2)]">
        
        {/* Ambient Glow */}
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[300px] bg-emerald-500/10 rounded-full blur-[100px] pointer-events-none" />

        <div className="relative z-10 max-w-[760px] mx-auto space-y-6">
          <div className="inline-flex items-center gap-2 px-3 py-1 bg-zinc-900 rounded-full border border-zinc-700 text-xs font-semibold text-emerald-400">
            <Sparkles className="w-3.5 h-3.5" />
            <span>SWITCH TO AUTONOMOUS AGENCY OPERATIONS</span>
          </div>

          <h2 className="font-display text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight">
            Ready to upgrade from {comp.competitorName}?
          </h2>

          <p className="text-zinc-400 text-sm sm:text-base leading-relaxed">
            Join hundreds of high-throughput studios and agencies saving ₹{comp.priceComparison.savingsPerYear.includes('Save ₹') ? comp.priceComparison.savingsPerYear.split('Save ₹')[1].split('/')[0] : '75,000'}+ annually with Cora. Get instant access with all 20 modules unlocked.
          </p>

          <div className="flex flex-col sm:flex-row items-center justify-center gap-4 pt-2">
            <a
              href={`https://app.heycora.in/workspace/login?source=compare_cta_${comp.slug}`}
              className="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-emerald-500 text-black px-7 py-4 rounded-xl text-sm font-bold hover:bg-emerald-400 transition-all shadow-lg hover:shadow-emerald-500/25 group cursor-pointer"
            >
              <span>Get Started Free on Cora</span>
              <ArrowRight className="w-4 h-4 text-black group-hover:translate-x-1 transition-transform" />
            </a>

            <Link
              href="/contact"
              className="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-zinc-900 border border-zinc-700 text-zinc-200 px-7 py-4 rounded-xl text-sm font-bold hover:bg-zinc-800 hover:text-white transition-all shadow-xs"
            >
              <MessageSquare className="w-4 h-4 text-zinc-400" />
              <span>Request Custom Migration Audit</span>
            </Link>
          </div>

          <div className="flex items-center justify-center gap-6 pt-4 text-xs text-zinc-500 font-medium">
            <span>✓ No credit card required</span>
            <span>✓ Free 1-click data import</span>
            <span>✓ Cancel anytime</span>
          </div>
        </div>

      </div>
    </section>
  );
}
