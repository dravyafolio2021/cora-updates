'use client';

import React from 'react';
import { ShieldCheck } from 'lucide-react';

export function FeatureSecurityTrustBar() {
  return (
    <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-16 sm:mb-24">
      <div className="w-full bg-white rounded-3xl sm:rounded-[36px] border border-zinc-200/90 shadow-2xs p-7 sm:p-10 lg:p-12">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
          
          {/* Left Column: Heading & 24/7 Dedicated Audit Indicator */}
          <div className="lg:col-span-4 space-y-3.5">
            <div>
              <h3 className="font-display text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-zinc-950 leading-tight">
                Security
              </h3>
              <span className="font-display text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-zinc-400 block leading-tight">
                Everywhere
              </span>
            </div>

            <div className="pt-1 flex items-center gap-2 text-xs font-mono font-bold text-zinc-500 tracking-wider">
              <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
              <span>24/7 DEDICATED SYSTEM AUDITING</span>
            </div>
          </div>

          {/* Right Column: 4 Official Compliance & Security Seals */}
          <div className="lg:col-span-8 grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6 divide-y sm:divide-y-0 sm:divide-x divide-zinc-200/80">
            
            {/* Badge 1: AICPA SOC 2 TYPE II */}
            <div className="flex flex-col items-center justify-center text-center p-3 sm:px-4 space-y-2.5">
              <div className="w-16 h-16 rounded-full border border-zinc-900 bg-white flex flex-col items-center justify-center p-1.5 shadow-2xs relative">
                <div className="w-full h-full rounded-full border border-dashed border-zinc-300 flex flex-col items-center justify-center">
                  <span className="text-[7.5px] font-mono font-bold text-zinc-400 tracking-wider leading-none">AICPA</span>
                  <span className="text-[11px] font-black text-zinc-950 tracking-tight leading-tight my-0.5">SOC 2</span>
                  <span className="text-[6.5px] font-mono font-bold text-zinc-500 tracking-widest leading-none uppercase">TYPE II</span>
                </div>
              </div>
              <span className="text-[11px] font-mono font-bold text-zinc-800 uppercase tracking-wider block">
                SOC 2 TYPE II
              </span>
            </div>

            {/* Badge 2: ISO / IEC 27001 */}
            <div className="flex flex-col items-center justify-center text-center p-3 sm:px-4 space-y-2.5 pt-4 sm:pt-3">
              <div className="w-16 h-16 rounded-full border border-zinc-900 bg-zinc-950 text-white flex flex-col items-center justify-center p-1.5 shadow-2xs relative">
                <div className="w-full h-full rounded-full border border-zinc-800 flex flex-col items-center justify-center">
                  <span className="text-[12px] font-black tracking-tight leading-none">ISO</span>
                  <span className="text-[8.5px] font-mono font-bold text-zinc-300 tracking-wider mt-0.5">27001</span>
                </div>
              </div>
              <span className="text-[11px] font-mono font-bold text-zinc-800 uppercase tracking-wider block">
                ISO 27001
              </span>
            </div>

            {/* Badge 3: GDPR PRIVACY */}
            <div className="flex flex-col items-center justify-center text-center p-3 sm:px-4 space-y-2.5 pt-4 sm:pt-3">
              <div className="w-16 h-16 rounded-full border border-zinc-400 border-dashed bg-zinc-50/60 flex flex-col items-center justify-center p-1.5 shadow-2xs relative">
                <div className="w-full h-full rounded-full border border-zinc-200/80 flex flex-col items-center justify-center">
                  <div className="flex items-center gap-0.5 text-zinc-700 text-[6px]">
                    <span>★</span>
                    <span className="text-[8px] font-black tracking-wider text-zinc-900">GDPR</span>
                    <span>★</span>
                  </div>
                  <span className="text-[6.5px] font-mono font-bold text-zinc-500 tracking-wider mt-0.5">COMPLIANT</span>
                </div>
              </div>
              <span className="text-[11px] font-mono font-bold text-zinc-800 uppercase tracking-wider block">
                GDPR PRIVACY
              </span>
            </div>

            {/* Badge 4: IT ACT 2000 */}
            <div className="flex flex-col items-center justify-center text-center p-3 sm:px-4 space-y-2.5 pt-4 sm:pt-3">
              <div className="w-16 h-16 rounded-2xl bg-emerald-50/80 border border-emerald-300 flex flex-col items-center justify-center p-1.5 shadow-2xs">
                <ShieldCheck className="w-5 h-5 text-emerald-700 stroke-[2.2]" />
                <span className="text-[7.5px] font-mono font-bold text-emerald-800 tracking-wider mt-0.5">SEC 10A</span>
              </div>
              <span className="text-[11px] font-mono font-bold text-zinc-800 uppercase tracking-wider block">
                IT ACT 2000
              </span>
            </div>

          </div>

        </div>
      </div>
    </section>
  );
}
