'use client';

import React from 'react';
import { ShieldCheck, ArrowRight, UploadCloud, CheckCircle2, Lock } from 'lucide-react';
import { CompetitorComparison } from '@/lib/comparisons-data';

export function ComparisonMigrationRoadmap({ comp }: { comp: CompetitorComparison }) {
  const steps = comp.migrationSteps || [
    { step: '01', title: `Export Data from ${comp.competitorName}`, desc: 'Download your contacts, past deals, and project histories as a CSV file.' },
    { step: '02', title: '1-Click Intelligent Ingestion', desc: 'Cora automatically matches custom fields, client profiles, and active workflows.' },
    { step: '03', title: 'Zero-Downtime Launch', desc: 'Send new legal contracts and collect instant GST payments with zero interruption.' }
  ];

  return (
    <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-16 sm:mb-[100px]">
      <div className="bg-white rounded-3xl border border-zinc-200/90 p-6 sm:p-10 shadow-[0_8px_30px_rgba(0,0,0,0.04)]">
        
        {/* Header */}
        <div className="text-center max-w-[680px] mx-auto mb-12">
          <div className="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 rounded-full border border-emerald-200/60 text-xs font-semibold text-emerald-800 mb-3">
            <ShieldCheck className="w-3.5 h-3.5 text-emerald-600" />
            <span>ZERO-RISK MIGRATION GUARANTEE</span>
          </div>
          <h2 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight mb-2">
            Switch from {comp.competitorName} in Under 5 Minutes
          </h2>
          <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed">
            Never worry about losing past client records, active contracts, or billing history. Our white-glove migration pipeline ensures 100% operational continuity.
          </p>
        </div>

        {/* Steps Grid */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 relative">
          {steps.map((item, idx) => (
            <div 
              key={idx}
              className="p-6 rounded-2xl bg-zinc-50 border border-zinc-200/80 space-y-3 relative hover:border-zinc-300 transition-all"
            >
              <div className="flex items-center justify-between">
                <span className="font-mono text-xl font-black text-zinc-300">
                  {item.step}
                </span>
                <span className="w-2.5 h-2.5 rounded-full bg-emerald-500" />
              </div>
              <h3 className="font-display text-sm sm:text-base font-bold text-zinc-950">
                {item.title}
              </h3>
              <p className="text-zinc-600 text-xs leading-relaxed">
                {item.desc}
              </p>
            </div>
          ))}
        </div>

        {/* Trust Badges Bar */}
        <div className="mt-10 pt-6 border-t border-zinc-100 flex flex-wrap items-center justify-center gap-6 sm:gap-12 text-zinc-500 text-xs">
          <div className="flex items-center gap-2">
            <Lock className="w-4 h-4 text-emerald-600" />
            <span>256-Bit Encrypted Database Migration</span>
          </div>
          <div className="flex items-center gap-2">
            <CheckCircle2 className="w-4 h-4 text-emerald-600" />
            <span>Zero Data Loss Guarantee</span>
          </div>
          <div className="flex items-center gap-2">
            <UploadCloud className="w-4 h-4 text-emerald-600" />
            <span>Free White-Glove Onboarding Support</span>
          </div>
        </div>

      </div>
    </section>
  );
}
