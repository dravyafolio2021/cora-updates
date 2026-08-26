'use client';

import React from 'react';
import { ShieldCheck, Lock, CheckCircle2 } from 'lucide-react';

export function FeatureSecurityTrustBar() {
  return (
    <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-24 sm:mb-32">
      <div className="w-full bg-white rounded-3xl sm:rounded-[36px] border border-zinc-200 shadow-xs p-8 sm:p-12">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
          
          {/* Left Column: Heading & 24/7 Support */}
          <div className="lg:col-span-4 space-y-4">
            <div>
              <h3 className="font-display text-3xl sm:text-4xl font-bold tracking-tight text-zinc-950">
                Security
              </h3>
              <span className="font-display text-3xl sm:text-4xl font-bold tracking-tight text-zinc-400 block">
                Everywhere
              </span>
            </div>

            <div className="pt-2 flex items-center gap-2 text-xs font-mono font-bold text-zinc-500">
              <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
              <span>24/7 DEDICATED SYSTEM AUDITING</span>
            </div>
          </div>

          {/* Right Column: 4 Certification Badges (Matching Reference Image 2) */}
          <div className="lg:col-span-8 grid grid-cols-2 sm:grid-cols-4 gap-4 divide-y sm:divide-y-0 sm:divide-x divide-zinc-200">
            
            {/* Badge 1: SOC 2 TYPE II */}
            <div className="flex flex-col items-center justify-center text-center p-3 sm:px-4 space-y-2">
              <div className="w-14 h-14 rounded-full border-2 border-zinc-900 flex flex-col items-center justify-center p-1 font-mono">
                <span className="text-[8px] font-bold text-zinc-400 leading-none">AICPA</span>
                <span className="text-[10px] font-black text-zinc-950 leading-tight">SOC 2</span>
                <span className="text-[7px] text-zinc-500 uppercase">TYPE II</span>
              </div>
              <span className="text-xs font-mono font-bold text-zinc-900 uppercase tracking-wider block">
                SOC 2 TYPE II
              </span>
            </div>

            {/* Badge 2: ISO 27001 */}
            <div className="flex flex-col items-center justify-center text-center p-3 sm:px-4 space-y-2 pt-4 sm:pt-3">
              <div className="w-14 h-14 rounded-full border-2 border-zinc-900 flex flex-col items-center justify-center p-1 font-mono bg-zinc-950 text-white">
                <span className="text-[11px] font-black tracking-tighter leading-none">ISO</span>
                <span className="text-[8px] font-bold text-zinc-300">27001</span>
              </div>
              <span className="text-xs font-mono font-bold text-zinc-900 uppercase tracking-wider block">
                ISO 27001
              </span>
            </div>

            {/* Badge 3: GDPR */}
            <div className="flex flex-col items-center justify-center text-center p-3 sm:px-4 space-y-2 pt-4 sm:pt-3">
              <div className="w-14 h-14 rounded-full border border-dashed border-zinc-400 flex flex-col items-center justify-center p-1 font-mono">
                <span className="text-[9px] font-black text-zinc-900 tracking-wider">★ GDPR ★</span>
                <span className="text-[7px] text-zinc-500">COMPLIANT</span>
              </div>
              <span className="text-xs font-mono font-bold text-zinc-900 uppercase tracking-wider block">
                GDPR PRIVACY
              </span>
            </div>

            {/* Badge 4: IT ACT 2000 */}
            <div className="flex flex-col items-center justify-center text-center p-3 sm:px-4 space-y-2 pt-4 sm:pt-3">
              <div className="w-14 h-14 rounded-2xl bg-emerald-50 border border-emerald-300 flex flex-col items-center justify-center p-1 text-emerald-800">
                <ShieldCheck className="w-5 h-5 text-emerald-700" />
                <span className="text-[7px] font-mono font-bold mt-0.5">SEC 10A</span>
              </div>
              <span className="text-xs font-mono font-bold text-zinc-900 uppercase tracking-wider block">
                IT ACT 2000
              </span>
            </div>

          </div>

        </div>
      </div>
    </section>
  );
}
