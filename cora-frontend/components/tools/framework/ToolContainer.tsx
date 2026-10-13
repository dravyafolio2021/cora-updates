'use client';

import React from 'react';
import Link from 'next/link';
import { ChevronRight, Lock, Zap } from 'lucide-react';
import { ArtisticHeroBackground } from '@/components/features/ArtisticHeroBackground';

export interface ToolContainerProps {
  categorySlug?: string;
  categoryLabel: string;
  title: string;
  subtitle: string;
  badgeTag?: string;
  executionMode?: 'browser-only' | 'remote-audit' | 'ai-assisted';
  lastReviewedDate?: string;
  children: React.ReactNode;
}

export function ToolContainer({
  categoryLabel,
  title,
  subtitle,
  badgeTag = '100% Client-Side Engine',
  executionMode = 'browser-only',
  lastReviewedDate = 'October 2026',
  children,
}: ToolContainerProps) {
  return (
    <div className="w-full min-h-screen bg-[#FBFaf7] text-zinc-900 selection:bg-zinc-900 selection:text-white pb-32 sm:pb-24">
      {/* ── 1. EDITORIAL HEADER SECTION (COMPACT ON MOBILE) ── */}
      <section className="relative w-full pt-[88px] sm:pt-[120px] md:pt-[128px] pb-6 sm:pb-10 md:pb-12 overflow-hidden border-b border-zinc-200/80">
        <ArtisticHeroBackground tone="studio" />

        <div className="relative z-10 w-full max-w-[1100px] mx-auto px-4 sm:px-6">
          
          {/* Breadcrumb Bar */}
          <nav className="flex items-center gap-1.5 text-[11px] sm:text-xs font-mono text-zinc-500 mb-3 sm:mb-5 overflow-x-auto whitespace-nowrap scrollbar-none py-0.5" aria-label="Breadcrumb">
            <Link href="/" className="hover:text-zinc-900 transition-colors">Cora</Link>
            <ChevronRight className="w-3 h-3 text-zinc-400 shrink-0" />
            <Link href="/tools" className="hover:text-zinc-900 transition-colors">Tools</Link>
            <ChevronRight className="w-3 h-3 text-zinc-400 shrink-0" />
            <span className="text-zinc-800 font-bold">{categoryLabel}</span>
          </nav>

          {/* Badge & Execution Mode */}
          <div className="flex flex-wrap items-center gap-1.5 sm:gap-2 mb-2.5 sm:mb-4">
            <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full bg-white text-zinc-800 border border-zinc-200/90 text-[11px] sm:text-xs font-semibold shadow-2xs">
              <Zap className="w-3 h-3 sm:w-3.5 sm:h-3.5 text-amber-600" />
              <span>{badgeTag}</span>
            </span>

            <span className="inline-flex items-center gap-1 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200/70 text-[10px] sm:text-[11px] font-mono font-medium">
              <Lock className="w-2.5 h-2.5 sm:w-3 sm:h-3 text-emerald-600" />
              <span>Zero Server Transmission</span>
            </span>

            <span className="text-zinc-400 text-xs font-mono hidden md:inline-block">
              Reviewed: {lastReviewedDate}
            </span>
          </div>

          {/* Exactly ONE Clear H1 */}
          <h1 className="font-display text-2xl sm:text-4xl md:text-5xl font-bold text-zinc-950 tracking-tight leading-[1.18] sm:leading-[1.15] mb-2 sm:mb-4 max-w-[900px]">
            {title}
          </h1>

          {/* Direct Editorial Explanation */}
          <p className="text-zinc-600 text-xs sm:text-base md:text-lg font-normal leading-relaxed max-w-[780px]">
            {subtitle}
          </p>

        </div>
      </section>

      {/* ── 2. MAIN TOOL CANVAS ── */}
      <main className="w-full max-w-[1100px] mx-auto px-4 sm:px-6 pt-4 sm:pt-6 md:pt-8">
        {children}
      </main>
    </div>
  );
}
