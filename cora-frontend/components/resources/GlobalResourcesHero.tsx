'use client';

import React from 'react';
import { Search } from 'lucide-react';
import { ArtisticHeroBackground } from '@/components/features/ArtisticHeroBackground';

export interface ResourcePillar {
  icon: React.ReactNode;
  text: string;
}

export interface ResourceSearchProps {
  value: string;
  onChange: (val: string) => void;
  placeholder?: string;
}

export interface GlobalResourcesHeroProps {
  badgeIcon?: React.ReactNode;
  badgeText: string;
  titleLine1: string;
  titleLine2?: string;
  description: string;
  pillars?: ResourcePillar[];
  searchProps?: ResourceSearchProps;
  children?: React.ReactNode;
  tone?: 'studio' | 'neutral' | 'warm';
}

export function GlobalResourcesHero({
  badgeIcon,
  badgeText,
  titleLine1,
  titleLine2,
  description,
  pillars,
  searchProps,
  children,
  tone = 'studio'
}: GlobalResourcesHeroProps) {
  return (
    <section className="relative w-full pt-[128px] sm:pt-36 pb-10 sm:pb-16 overflow-hidden">
      <ArtisticHeroBackground tone={tone} />

      <div className="relative z-10 w-full max-w-[1240px] mx-auto px-4 sm:px-6 text-center">
        
        {/* Status Badge */}
        <div className="mb-4 sm:mb-6">
          <span className="inline-flex items-center gap-1.5 px-3.5 py-1.5 sm:px-4 sm:py-2 rounded-full bg-white/95 backdrop-blur-md text-zinc-900 border border-zinc-200/80 text-xs sm:text-sm font-medium shadow-2xs">
            {badgeIcon}
            <span>{badgeText}</span>
          </span>
        </div>

        {/* 2-Line Headline */}
        <h1 className="font-display text-4xl sm:text-6xl md:text-7xl font-semibold text-zinc-950 tracking-[-0.035em] leading-[1.12] sm:leading-[1.08] mb-4 sm:mb-6 max-w-[980px] mx-auto">
          <span className="block">{titleLine1}</span>
          {titleLine2 && (
            <span className="block mt-1 sm:mt-2 text-zinc-600 font-normal text-2xl sm:text-4xl md:text-5xl">
              {titleLine2}
            </span>
          )}
        </h1>

        {/* Subtitle */}
        <p className="text-zinc-600 text-sm sm:text-base md:text-lg font-normal leading-relaxed max-w-[680px] mx-auto mb-7 sm:mb-9 px-3">
          {description}
        </p>

        {/* Value Trust Pillars */}
        {pillars && pillars.length > 0 && (
          <div className="flex flex-wrap items-center justify-center gap-2.5 sm:gap-4 mb-8 text-xs sm:text-sm font-medium text-zinc-700">
            {pillars.map((pillar, idx) => (
              <div 
                key={idx} 
                className="flex items-center gap-1.5 bg-white/80 backdrop-blur-xs px-3 py-1 rounded-full border border-zinc-200/70 shadow-2xs"
              >
                {pillar.icon}
                <span>{pillar.text}</span>
              </div>
            ))}
          </div>
        )}

        {/* Central Search Bar */}
        {searchProps && (
          <div className="max-w-[620px] mx-auto mb-4">
            <div className="relative flex items-center bg-white/95 backdrop-blur-xl border border-zinc-200/90 rounded-full px-4 py-2.5 shadow-[0_8px_30px_rgba(0,0,0,0.06)] hover:border-zinc-300 transition-all">
              <Search className="w-4 h-4 text-zinc-400 mr-3 shrink-0" />
              <input
                type="text"
                value={searchProps.value}
                onChange={(e) => searchProps.onChange(e.target.value)}
                placeholder={searchProps.placeholder || "Search..."}
                className="w-full bg-transparent text-xs sm:text-sm text-zinc-950 placeholder:text-zinc-400 focus:outline-none font-sans"
              />
              {searchProps.value && (
                <button
                  type="button"
                  onClick={() => searchProps.onChange('')}
                  className="text-xs font-mono text-zinc-400 hover:text-zinc-900 px-2 py-0.5 rounded cursor-pointer"
                >
                  Clear
                </button>
              )}
            </div>
          </div>
        )}

        {/* Custom Actions / Forms / Children */}
        {children}

      </div>
    </section>
  );
}
