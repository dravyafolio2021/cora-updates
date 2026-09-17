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
  TrendingDown,
  Building2,
  FileText
} from 'lucide-react';
import { COMPARISONS_DATA } from '@/lib/comparisons-data';
import { trackEvent } from '@/components/analytics/Analytics';

export default function CompareDirectoryPage() {
  const comparisons = Object.values(COMPARISONS_DATA);
  const categories = ['All', 'Studio CRMs', 'Enterprise CRMs', 'E-Sign & Legal', 'Generic SaaS', 'Productivity & Workspace'];
  const [selectedCategory, setSelectedCategory] = useState('All');

  const filtered = selectedCategory === 'All'
    ? comparisons
    : comparisons.filter(c => c.category === selectedCategory);

  return (
    <main className="w-full relative pt-32 sm:pt-40 pb-24 overflow-hidden bg-white">
      
      {/* ── Hero Section ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 text-center mb-16 sm:mb-20">
        <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-zinc-100 rounded-xl border border-zinc-200/80 text-xs font-semibold text-zinc-900 mb-4 shadow-2xs">
          <Sparkles className="w-3.5 h-3.5 text-emerald-600" />
          <span>Competitive Architecture Benchmarks • 2026 Edition</span>
        </div>

        <h1 className="font-display text-4xl xs:text-5xl sm:text-6xl font-bold text-zinc-950 leading-[1.1] tracking-[-0.035em] max-w-[960px] mx-auto mb-5">
          See why agencies are switching from legacy tools to Cora
        </h1>

        <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[760px] mx-auto mb-10">
          Transparent, head-to-head comparisons showing how Cora replaces 6+ disconnected software subscriptions (DocuSign, QuickBooks, HoneyBook, Dropbox) with one autonomous agency operating system.
        </p>

        {/* Highlight Stats Ticker */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-[940px] mx-auto">
          <div className="p-4 rounded-2xl bg-zinc-50 border border-zinc-200/80 text-left">
            <span className="text-[11px] font-mono font-bold text-zinc-400 uppercase">ANNUAL SAVINGS</span>
            <div className="font-display text-lg sm:text-xl font-bold text-zinc-950 mt-0.5">Up to ₹4.5L/yr</div>
            <p className="text-[11px] text-zinc-500 mt-0.5">By eliminating add-ons</p>
          </div>

          <div className="p-4 rounded-2xl bg-zinc-50 border border-zinc-200/80 text-left">
            <span className="text-[11px] font-mono font-bold text-zinc-400 uppercase">CONSOLIDATION</span>
            <div className="font-display text-lg sm:text-xl font-bold text-zinc-950 mt-0.5">20 Apps in 1</div>
            <p className="text-[11px] text-zinc-500 mt-0.5">Single unified login</p>
          </div>

          <div className="p-4 rounded-2xl bg-zinc-50 border border-zinc-200/80 text-left">
            <span className="text-[11px] font-mono font-bold text-zinc-400 uppercase">TAX AUTOMATION</span>
            <div className="font-display text-lg sm:text-xl font-bold text-zinc-950 mt-0.5">18% GST & UPI</div>
            <p className="text-[11px] text-zinc-500 mt-0.5">Instant settlement</p>
          </div>

          <div className="p-4 rounded-2xl bg-zinc-50 border border-zinc-200/80 text-left">
            <span className="text-[11px] font-mono font-bold text-zinc-400 uppercase">MIGRATION TIME</span>
            <div className="font-display text-lg sm:text-xl font-bold text-zinc-950 mt-0.5">Sub-5 Minutes</div>
            <p className="text-[11px] text-zinc-500 mt-0.5">Zero client downtime</p>
          </div>
        </div>
      </section>

      {/* ── Category Filter Pills ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-10">
        <div className="flex items-center justify-center flex-wrap gap-2">
          {categories.map((cat, idx) => (
            <button
              key={idx}
              onClick={() => setSelectedCategory(cat)}
              className={`px-4 py-2 rounded-xl text-xs font-semibold transition-all cursor-pointer ${
                selectedCategory === cat
                  ? 'bg-zinc-950 text-white shadow-xs'
                  : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200/70 hover:text-zinc-900'
              }`}
            >
              {cat}
            </button>
          ))}
        </div>
      </section>

      {/* ── Comparison Cards Grid ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-24">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {filtered.map((comp) => (
            <Link
              key={comp.slug}
              href={`/compare/${comp.slug}`}
              onClick={() => trackEvent('compare_card_clicked', { competitor: comp.competitorName })}
              className="bg-white rounded-[26px] border border-zinc-200/90 p-6 sm:p-7 flex flex-col justify-between hover:shadow-[0_16px_40px_rgba(0,0,0,0.06)] hover:border-zinc-300 hover:-translate-y-1 transition-all group cursor-pointer relative"
            >
              <div className="space-y-4">
                <div className="flex items-center justify-between">
                  <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">
                    {comp.category}
                  </span>
                  <span className="text-[10px] font-mono font-bold text-emerald-800 bg-emerald-50 border border-emerald-200/80 px-2 py-0.5 rounded-md">
                    VS CORA
                  </span>
                </div>

                <div>
                  <h2 className="font-display text-lg sm:text-xl font-bold text-zinc-950 group-hover:text-black transition-colors">
                    Cora vs {comp.competitorName}
                  </h2>
                  <p className="text-zinc-500 text-xs mt-1">
                    {comp.competitorTagline}
                  </p>
                </div>

                <p className="text-zinc-600 text-xs sm:text-[13px] leading-relaxed line-clamp-3">
                  {comp.verdictSummary}
                </p>

                {/* Savings Pill */}
                <div className="pt-2">
                  <span className="inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-800 bg-emerald-50/80 px-2.5 py-1 rounded-lg border border-emerald-200/50">
                    <TrendingDown className="w-3.5 h-3.5 text-emerald-600" />
                    <span>{comp.priceComparison.savingsPerYear.split(' ')[0]} {comp.priceComparison.savingsPerYear.split(' ')[1]}</span>
                  </span>
                </div>
              </div>

              <div className="pt-5 border-t border-zinc-100 mt-5 flex items-center justify-between">
                <span className="text-xs font-bold text-zinc-900 group-hover:text-emerald-700 transition-colors">
                  View Full Breakdown
                </span>
                <ArrowRight className="w-4 h-4 text-zinc-400 group-hover:text-emerald-700 group-hover:translate-x-1 transition-all" />
              </div>
            </Link>
          ))}
        </div>
      </section>

      {/* ── Global Migration Assistance Banner ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        <div className="rounded-3xl bg-zinc-950 text-white p-8 sm:p-12 border border-zinc-800 flex flex-col md:flex-row items-center justify-between gap-6 shadow-sm">
          <div className="space-y-2 text-center md:text-left">
            <span className="text-[11px] font-mono font-bold uppercase tracking-widest text-emerald-400">
              FREE MIGRATION ASSISTANCE
            </span>
            <h3 className="font-display text-2xl sm:text-3xl font-bold">
              Switching from another agency tool?
            </h3>
            <p className="text-zinc-400 text-xs sm:text-sm max-w-[580px]">
              Our engineering team will help you migrate your active clients, past invoices, rate cards, and proposal templates into Cora with zero downtime.
            </p>
          </div>

          <div className="shrink-0 flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
            <a
              href="https://app.heycora.in/workspace/login?source=compare_directory_cta"
              className="w-full sm:w-auto text-center bg-emerald-500 text-black font-bold px-6 py-3.5 rounded-xl text-xs hover:bg-emerald-400 transition-all shadow-xs"
            >
              Start Free Trial
            </a>
            <Link
              href="/contact"
              className="w-full sm:w-auto text-center bg-zinc-900 border border-zinc-700 text-zinc-200 font-bold px-6 py-3.5 rounded-xl text-xs hover:bg-zinc-800 transition-all"
            >
              Book Migration Call
            </Link>
          </div>
        </div>
      </section>

    </main>
  );
}
