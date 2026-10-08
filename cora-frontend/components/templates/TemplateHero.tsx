'use client';

import React from 'react';
import { Sparkles, Search, Check, Zap, Lock, ShieldCheck } from 'lucide-react';
import { ArtisticHeroBackground } from '@/components/features/ArtisticHeroBackground';

interface TemplateHeroProps {
  searchQuery: string;
  onSearchChange: (query: string) => void;
  totalTemplatesCount: number;
}

export function TemplateHero({
  searchQuery,
  onSearchChange,
  totalTemplatesCount,
}: TemplateHeroProps) {
  return (
    <section className="relative w-full pt-[128px] sm:pt-36 pb-10 sm:pb-16 overflow-hidden">
      <ArtisticHeroBackground tone="studio" />

      <div className="relative z-10 w-full max-w-[1240px] mx-auto px-4 sm:px-6 text-center">
        
        {/* Status Badge */}
        <div className="mb-4 sm:mb-6">
          <span className="inline-flex items-center gap-1.5 px-3.5 py-1.5 sm:px-4 sm:py-2 rounded-full bg-white/95 backdrop-blur-md text-zinc-900 border border-zinc-200/80 text-xs sm:text-sm font-medium shadow-2xs">
            <Sparkles className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-purple-600" />
            <span>16+ Production-Ready Schemas &amp; Kits</span>
          </span>
        </div>

        {/* 2-Line Headline */}
        <h1 className="font-display text-4xl sm:text-6xl md:text-7xl font-semibold text-zinc-950 tracking-[-0.035em] leading-[1.12] sm:leading-[1.08] mb-4 sm:mb-6 max-w-[980px] mx-auto">
          <span className="block">Template &amp; Theme Library</span>
          <span className="block mt-1 sm:mt-2 text-zinc-600 font-normal text-2xl sm:text-4xl md:text-5xl">
            Pre-built websites, workspace schemas &amp; turnkey kits
          </span>
        </h1>

        {/* Subtitle */}
        <p className="text-zinc-600 text-sm sm:text-base md:text-lg font-normal leading-relaxed max-w-[660px] mx-auto mb-7 sm:mb-9 px-3">
          Deploy complete agency websites, client proofing portals, SHA-256 contracts, and 18% GST invoicing schemas with one click.
        </p>

        {/* Value Trust Pillars */}
        <div className="hidden sm:flex flex-wrap items-center justify-center gap-4 sm:gap-6 mb-8 text-xs sm:text-sm font-medium text-zinc-700">
          <div className="flex items-center gap-1.5 bg-white/70 backdrop-blur-xs px-3 py-1 rounded-full border border-zinc-200/60 shadow-2xs">
            <Check className="w-3.5 h-3.5 text-emerald-600 font-bold" />
            <span>100% Free Forever</span>
          </div>
          <div className="flex items-center gap-1.5 bg-white/70 backdrop-blur-xs px-3 py-1 rounded-full border border-zinc-200/60 shadow-2xs">
            <Zap className="w-3.5 h-3.5 text-purple-600" />
            <span>1-Click Workspace Deploy</span>
          </div>
          <div className="flex items-center gap-1.5 bg-white/70 backdrop-blur-xs px-3 py-1 rounded-full border border-zinc-200/60 shadow-2xs">
            <Lock className="w-3.5 h-3.5 text-blue-600" />
            <span>Pre-Configured Indian GST</span>
          </div>
          <div className="flex items-center gap-1.5 bg-white/70 backdrop-blur-xs px-3 py-1 rounded-full border border-zinc-200/60 shadow-2xs">
            <ShieldCheck className="w-3.5 h-3.5 text-indigo-600" />
            <span>IT Act Contract E-Signs</span>
          </div>
        </div>

        {/* Central Search Bar */}
        <div className="max-w-[620px] mx-auto mb-4">
          <div className="relative flex items-center bg-white/95 backdrop-blur-xl border border-zinc-200/90 rounded-full px-4 py-2.5 shadow-[0_8px_30px_rgba(0,0,0,0.06)] hover:border-zinc-300 transition-all">
            <Search className="w-4 h-4 text-zinc-400 mr-3 shrink-0" />
            <input
              type="text"
              value={searchQuery}
              onChange={(e) => onSearchChange(e.target.value)}
              placeholder="Search templates e.g. web dev, branding, CA firm, law firm, real estate..."
              className="w-full bg-transparent text-xs sm:text-sm text-zinc-950 placeholder:text-zinc-400 focus:outline-none font-sans"
            />
            {searchQuery && (
              <button
                type="button"
                onClick={() => onSearchChange('')}
                className="text-xs font-mono text-zinc-400 hover:text-zinc-900 px-2 py-0.5 rounded cursor-pointer"
              >
                Clear
              </button>
            )}
          </div>
        </div>

      </div>
    </section>
  );
}
