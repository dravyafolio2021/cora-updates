import React from 'react';
import { ArrowRight, Search, Sparkles, CheckCircle2 } from 'lucide-react';
import { SECTOR_CATEGORIES, SectorCategory } from '@/lib/industry-data';

interface IndustryHeroProps {
  searchQuery: string;
  onSearchChange: (q: string) => void;
  activeSector: string;
  onSectorChange: (sectorId: string) => void;
}

export function IndustryHero({
  searchQuery,
  onSearchChange,
  activeSector,
  onSectorChange,
}: IndustryHeroProps) {
  return (
    <section className="relative w-full pt-28 sm:pt-32 pb-12 sm:pb-16 overflow-hidden border-b border-zinc-100 bg-white">
      {/* Background Decorative Mesh Pattern */}
      <div className="absolute inset-0 pointer-events-none -z-10 opacity-[0.35]">
        <svg className="w-full h-full" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <pattern id="industry-hero-grid" width="48" height="48" patternUnits="userSpaceOnUse">
              <path d="M 48 0 L 0 0 0 48" fill="none" stroke="rgba(0, 0, 0, 0.04)" strokeWidth="1" />
            </pattern>
          </defs>
          <rect width="100%" height="100%" fill="url(#industry-hero-grid)" />
        </svg>
      </div>

      <div className="relative z-10 w-full max-w-[1060px] mx-auto px-4 sm:px-6 text-center">
        
        {/* Status Pill */}
        <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-zinc-50 rounded-full border border-zinc-200/80 text-[11px] font-semibold text-zinc-800 mb-4 shadow-2xs">
          <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
          <span>16 Pre-Seeded Industry Workspaces • 100% Turnkey Workflows</span>
        </div>

        {/* Headline */}
        <h1 className="font-display text-3xl sm:text-4xl md:text-5xl font-bold text-zinc-950 leading-[1.2] tracking-[-0.03em] max-w-[860px] mx-auto mb-4">
          Tailored Workspaces for Modern Service Businesses
        </h1>

        {/* Subtitle */}
        <p className="text-zinc-600 text-sm sm:text-base md:text-lg font-normal leading-relaxed max-w-[720px] mx-auto mb-8">
          Pre-seeded digital contracts, 18% GST SAC tax classification, automated client portals, and industry-specific workflows built specifically for your exact business model.
        </p>

        {/* Search Bar */}
        <div className="max-w-[560px] mx-auto mb-8">
          <div className="relative flex items-center">
            <Search className="absolute left-4 w-4 h-4 text-zinc-400 pointer-events-none" />
            <input
              type="text"
              value={searchQuery}
              onChange={(e) => onSearchChange(e.target.value)}
              placeholder="Search by business (e.g. Lawyers, CA, Software, Clinics, Salons)..."
              className="w-full pl-11 pr-10 py-3.5 bg-zinc-50 hover:bg-white focus:bg-white text-xs sm:text-sm text-zinc-900 placeholder:text-zinc-400 rounded-2xl border border-zinc-200 focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10 transition-all outline-none shadow-2xs font-normal"
            />
            {searchQuery && (
              <button
                onClick={() => onSearchChange('')}
                className="absolute right-3.5 text-xs text-zinc-400 hover:text-zinc-700 bg-zinc-200/60 rounded-full w-5 h-5 flex items-center justify-center font-bold"
                aria-label="Clear search"
              >
                ×
              </button>
            )}
          </div>
        </div>

        {/* Sector Filter Pills */}
        <div className="flex flex-wrap items-center justify-center gap-2">
          {SECTOR_CATEGORIES.map((sec: SectorCategory) => {
            const isActive = activeSector === sec.id;
            return (
              <button
                key={sec.id}
                onClick={() => onSectorChange(sec.id)}
                className={`inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold transition-all ${
                  isActive
                    ? 'bg-zinc-950 text-white shadow-sm ring-2 ring-zinc-950/20'
                    : 'bg-zinc-100/80 hover:bg-zinc-200/70 text-zinc-700 hover:text-zinc-950 border border-zinc-200/60'
                }`}
              >
                <span>{sec.label}</span>
                <span className={`text-[10px] font-mono px-1.5 py-0.5 rounded-full ${
                  isActive ? 'bg-zinc-800 text-zinc-300' : 'bg-white text-zinc-600 border border-zinc-200/80'
                }`}>
                  {sec.count}
                </span>
              </button>
            );
          })}
        </div>

      </div>
    </section>
  );
}
