import React from 'react';
import { ArrowRight, Sparkles, CheckCircle2, ShieldCheck } from 'lucide-react';
import Link from 'next/link';

export function IndustryCtaBanner() {
  return (
    <section className="w-full py-16 sm:py-20 bg-zinc-950 text-white relative overflow-hidden">
      {/* Background Subtle Gradient Mesh */}
      <div className="absolute inset-0 pointer-events-none opacity-20">
        <div className="absolute -top-40 -right-40 w-96 h-96 bg-zinc-700 rounded-full blur-3xl" />
        <div className="absolute -bottom-40 -left-40 w-96 h-96 bg-zinc-800 rounded-full blur-3xl" />
      </div>

      <div className="relative z-10 max-w-[1060px] mx-auto px-4 sm:px-6 text-center">
        
        <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-zinc-900 border border-zinc-800 text-[11px] font-mono font-semibold text-zinc-300 mb-4 shadow-sm">
          <Sparkles className="w-3.5 h-3.5 text-amber-400" />
          <span>Need a Custom Industry Workflow?</span>
        </div>

        <h2 className="font-display text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold tracking-tight mb-4 max-w-[800px] mx-auto leading-tight">
          Ready to launch your industry operating system?
        </h2>

        <p className="text-xs sm:text-sm md:text-base text-zinc-400 font-normal leading-relaxed max-w-[620px] mx-auto mb-8">
          Join over 1,200+ modern agencies, law firms, CA practices, and studios running on Cora with zero setup fees.
        </p>

        {/* Action Buttons */}
        <div className="flex flex-col sm:flex-row items-center justify-center gap-3.5 mb-8">
          <a
            href="https://app.heycora.in/workspace/login?source=use_cases_cta"
            className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl bg-white text-zinc-950 text-xs sm:text-sm font-semibold hover:bg-zinc-100 transition-all shadow-sm group"
          >
            <span>Launch Free Industry Workspace</span>
            <ArrowRight className="w-4 h-4 text-zinc-600 group-hover:translate-x-0.5 transition-transform" />
          </a>

          <Link
            href="/demo"
            className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white border border-zinc-800 text-xs sm:text-sm font-semibold transition-all"
          >
            <span>Explore Interactive Demo</span>
          </Link>
        </div>

        {/* Trust Badges */}
        <div className="flex flex-wrap items-center justify-center gap-4 sm:gap-8 text-[11.5px] font-mono text-zinc-400">
          <div className="flex items-center gap-1.5">
            <CheckCircle2 className="w-3.5 h-3.5 text-emerald-400" />
            <span>Zero Credit Card Required</span>
          </div>
          <div className="flex items-center gap-1.5">
            <CheckCircle2 className="w-3.5 h-3.5 text-emerald-400" />
            <span>64+ Pre-Seeded Templates</span>
          </div>
          <div className="flex items-center gap-1.5">
            <CheckCircle2 className="w-3.5 h-3.5 text-emerald-400" />
            <span>100% IT Act 2000 Compliant</span>
          </div>
        </div>

      </div>
    </section>
  );
}
