import React from 'react';
import { Search } from 'lucide-react';
import { SECTOR_CATEGORIES, SectorCategory } from '@/lib/industry-data';
import { ArtisticHeroBackground } from '@/components/features/ArtisticHeroBackground';

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
    <section className="relative w-full pt-24 sm:pt-[100px] pb-16 sm:pb-[100px] overflow-hidden">
      {/* Signature Atmospheric Background Artwork with seamless gradient veil */}
      <ArtisticHeroBackground />

      <div className="relative z-10 w-full max-w-[1000px] mx-auto px-4 sm:px-6 text-center">
        
        {/* Status Pill */}
        <div className="inline-flex items-center gap-2 px-3 py-1 bg-white/90 backdrop-blur-md rounded-full border border-zinc-200/80 text-[11px] font-semibold text-zinc-800 mb-3.5 shadow-2xs">
          <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
          <span>16 Pre-Configured Agency Workspaces • Turnkey Workflows</span>
        </div>

        {/* Headline with Active Agency Language */}
        <h1 className="font-display text-3xl sm:text-4xl md:text-[46px] font-semibold text-zinc-950 leading-[1.26] sm:leading-[1.32] tracking-[-0.03em] max-w-[860px] mx-auto mb-4">
          Purpose-Built Operating Systems for Digital, Design &amp; Dev Agencies
        </h1>

        {/* Subtitle */}
        <p className="text-zinc-600 text-xs sm:text-base font-normal leading-relaxed max-w-[700px] mx-auto mb-7">
          Launch pre-seeded client workspaces equipped with sprint milestone contracts, 18% GST SAC billing, automated client review portals, and multi-tenant AI copilots.
        </p>

        {/* Search Bar */}
        <div className="max-w-[560px] mx-auto mb-7">
          <div className="relative flex items-center">
            <Search className="absolute left-4 w-4 h-4 text-zinc-400 pointer-events-none" />
            <input
              type="text"
              value={searchQuery}
              onChange={(e) => onSearchChange(e.target.value)}
              placeholder="Search by agency (e.g. Software Boutique, Design Studio, Marketing Agency)..."
              className="w-full pl-11 pr-10 py-3.5 bg-white/90 hover:bg-white focus:bg-white text-xs sm:text-sm text-zinc-900 placeholder:text-zinc-400 rounded-2xl border border-zinc-200/90 focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10 transition-all outline-none shadow-2xs font-normal backdrop-blur-xs"
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
                className={`inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all cursor-pointer ${
                  isActive
                    ? 'bg-zinc-950 text-white shadow-xs'
                    : 'bg-white/80 hover:bg-zinc-100 text-zinc-700 hover:text-zinc-950 border border-zinc-200/80 shadow-2xs backdrop-blur-xs'
                }`}
              >
                <span>{sec.label}</span>
                <span className={`text-[10px] font-mono px-1.5 py-0.5 rounded-full ${
                  isActive ? 'bg-zinc-800 text-zinc-200' : 'bg-zinc-100 text-zinc-600 border border-zinc-200/70'
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
