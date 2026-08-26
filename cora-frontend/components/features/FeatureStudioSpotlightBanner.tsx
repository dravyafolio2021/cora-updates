'use client';

import React from 'react';
import { ArrowRight, Search, CheckCircle2 } from 'lucide-react';
import { FeatureModule } from '@/lib/features-data';

interface FeatureStudioSpotlightBannerProps {
  feature: FeatureModule;
}

export function FeatureStudioSpotlightBanner({ feature }: FeatureStudioSpotlightBannerProps) {
  return (
    <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-24 sm:mb-32">
      <div className="w-full rounded-[32px] sm:rounded-[36px] bg-[#FBFaf7] border border-zinc-200/90 p-6 sm:p-10 lg:p-12 shadow-sm relative overflow-hidden select-none">
        
        {/* Top Header with Headline & CTA (Pure Light Mode) */}
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8 sm:mb-10 relative z-10">
          <div className="space-y-2 max-w-xl">
            <span className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-500 block">
              REAL-TIME PRODUCTION INTELLIGENCE
            </span>
            <h3 className="font-display text-2xl sm:text-4xl font-bold tracking-tight text-zinc-950 leading-snug">
              Build custom studio reports you can actually work from
            </h3>
          </div>

          <div className="flex flex-col sm:flex-row items-start sm:items-center gap-3 shrink-0">
            <a
              href={`https://app.heycora.in/workspace/login?feature=${feature.slug}&source=feature_spotlight`}
              className="inline-flex items-center gap-2 bg-zinc-950 hover:bg-zinc-800 text-white px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all shadow-xs group cursor-pointer"
            >
              <span>Get started. It&apos;s FREE</span>
              <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
            </a>
            <span className="text-[11px] text-zinc-500 font-mono sm:text-right block">
              No credit card. Free forever.
            </span>
          </div>
        </div>

        {/* Embedded Pure Light-Mode macOS Studio Interface Card */}
        <div className="w-full bg-white rounded-2xl sm:rounded-3xl border border-zinc-200/90 shadow-[0_12px_36px_rgba(0,0,0,0.05)] overflow-hidden text-zinc-900">
          
          {/* Top Window Navigation Bar */}
          <div className="px-4 sm:px-6 py-3 bg-zinc-50/90 border-b border-zinc-200/80 flex items-center justify-between gap-4 flex-wrap">
            <div className="flex items-center gap-3">
              <div className="flex items-center gap-1.5">
                <span className="w-2.5 h-2.5 rounded-full bg-zinc-300 block" />
                <span className="w-2.5 h-2.5 rounded-full bg-zinc-300 block" />
                <span className="w-2.5 h-2.5 rounded-full bg-zinc-300 block" />
              </div>
              <div className="flex items-center gap-2 text-xs font-bold text-zinc-900 border-l border-zinc-200 pl-3">
                <span className="w-2 h-2 rounded-full bg-emerald-500" />
                <span>Production OS @cora</span>
              </div>
            </div>

            {/* Quick Search Mock */}
            <div className="hidden sm:flex items-center gap-2 px-3 py-1 rounded-lg bg-zinc-100/80 border border-zinc-200/70 text-xs text-zinc-500 font-mono">
              <Search className="w-3.5 h-3.5 text-zinc-400" />
              <span>Search studio... ⌘K</span>
            </div>
          </div>

          {/* Sub-tabs Navigation */}
          <div className="px-4 sm:px-6 py-2.5 bg-white border-b border-zinc-100 flex items-center gap-2 sm:gap-4 overflow-x-auto text-xs font-medium text-zinc-600">
            <span className="px-3 py-1 rounded-lg bg-zinc-100 text-zinc-950 font-bold">
              📊 Overview
            </span>
            <span className="hover:text-zinc-950 cursor-pointer">
              📈 Analytics
            </span>
            <span className="text-zinc-950 font-bold border-b-2 border-zinc-950 pb-0.5">
              🚩 Priorities &amp; Holds
            </span>
            <span className="hover:text-zinc-950 cursor-pointer">
              👥 Crew Teams
            </span>
          </div>

          {/* 3 Metric Leaderboards in Monochromatic Design */}
          <div className="p-4 sm:p-6 bg-zinc-50/40 grid grid-cols-1 md:grid-cols-3 gap-4">
            
            {/* Box 1: Team Production Leaderboard */}
            <div className="p-4 rounded-2xl bg-white border border-zinc-200/80 space-y-3 shadow-2xs">
              <div className="flex items-center justify-between">
                <span className="text-xs font-bold text-zinc-900">Crew Leaderboard</span>
                <span className="text-[10px] font-mono text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-2 py-0.5 rounded font-bold">
                  98% ON-TIME
                </span>
              </div>
              <div className="flex items-center gap-1.5 -space-x-1">
                <div className="w-7 h-7 rounded-full bg-zinc-900 text-white text-[10px] font-bold flex items-center justify-center border-2 border-white">
                  KP
                </div>
                <div className="w-7 h-7 rounded-full bg-zinc-800 text-white text-[10px] font-bold flex items-center justify-center border-2 border-white">
                  RV
                </div>
                <div className="w-7 h-7 rounded-full bg-zinc-700 text-white text-[10px] font-bold flex items-center justify-center border-2 border-white">
                  AM
                </div>
                <div className="w-7 h-7 rounded-full bg-zinc-200 text-zinc-700 text-[10px] font-bold flex items-center justify-center border-2 border-white">
                  +8
                </div>
              </div>
              <div className="text-[11px] text-zinc-500 font-medium">
                Kavya Patel (Lead Director) • 14 shoots completed
              </div>
            </div>

            {/* Box 2: Active Calendar Shoot Holds */}
            <div className="p-4 rounded-2xl bg-white border border-zinc-200/80 space-y-3 shadow-2xs">
              <div className="flex items-center justify-between">
                <span className="text-xs font-bold text-zinc-900">Active Shoot Holds</span>
                <span className="text-[10px] font-mono text-blue-700 bg-blue-50 border border-blue-200/60 px-2 py-0.5 rounded font-bold">
                  6 CONFIRMED
                </span>
              </div>
              <div className="space-y-1.5 text-xs">
                <div className="flex items-center justify-between text-[11px]">
                  <span className="font-semibold text-zinc-900">Raymond Autumn Campaign</span>
                  <span className="font-mono text-zinc-500">Sat-Sun</span>
                </div>
                <div className="flex items-center justify-between text-[11px]">
                  <span className="font-semibold text-zinc-900">Titan Commercial Video</span>
                  <span className="font-mono text-zinc-500">Tue</span>
                </div>
              </div>
            </div>

            {/* Box 3: GST Invoicing & Milestones */}
            <div className="p-4 rounded-2xl bg-white border border-zinc-200/80 space-y-3 shadow-2xs">
              <div className="flex items-center justify-between">
                <span className="text-xs font-bold text-zinc-900">Settlement Status</span>
                <span className="text-[10px] font-mono text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-2 py-0.5 rounded font-bold">
                  100% RECONCILED
                </span>
              </div>
              <div className="space-y-1">
                <div className="font-display text-lg font-black text-zinc-950">
                  ₹14,80,000
                </div>
                <p className="text-[10px] text-zinc-500 font-mono">
                  All 18% CGST/SGST ledger tax filings auto-calculated
                </p>
              </div>
            </div>

          </div>

        </div>

      </div>
    </section>
  );
}
