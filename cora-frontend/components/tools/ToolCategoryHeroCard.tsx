'use client';

import React from 'react';
import Link from 'next/link';
import { 
  Calculator, 
  Receipt, 
  Sparkles, 
  Scale, 
  Code, 
  QrCode, 
  ArrowRight 
} from 'lucide-react';
import { ToolCategoryBlock } from '@/lib/tools-data';

const ICONS_MAP: Record<string, any> = {
  Calculator,
  Receipt,
  Sparkles,
  Scale,
  Code,
  QrCode,
};

interface ToolCategoryHeroCardProps {
  block: ToolCategoryBlock;
}

export function ToolCategoryHeroCard({ block }: ToolCategoryHeroCardProps) {
  const Icon = ICONS_MAP[block.iconName] || Sparkles;

  return (
    <Link
      href={`/tools/${block.featuredToolSlug}`}
      className="w-[220px] sm:w-[230px] lg:w-full shrink-0 flex flex-col rounded-2xl sm:rounded-3xl bg-white border border-zinc-200/90 hover:border-zinc-950 overflow-hidden shadow-2xs hover:shadow-[0_16px_36px_rgba(0,0,0,0.06)] hover:-translate-y-1 transition-all duration-300 group"
    >
      {/* ── Top Header Area (Cora Monochromatic & Subtle Neutral Surface) ── */}
      <div className="bg-gradient-to-b from-zinc-50/80 via-white to-white p-4 sm:p-5 flex flex-col justify-between h-[136px] relative">
        
        {/* Top Icon & Count Pill */}
        <div className="flex items-center justify-between">
          <div className="w-9 h-9 rounded-xl bg-white border border-zinc-200/80 text-zinc-900 flex items-center justify-center shadow-2xs group-hover:scale-105 group-hover:bg-zinc-950 group-hover:text-white group-hover:border-zinc-950 transition-all duration-200">
            <Icon className="w-4 h-4 stroke-[1.8]" />
          </div>
          <span className="text-[10px] font-mono font-bold text-zinc-500 bg-zinc-100 px-2.5 py-0.5 rounded-full border border-zinc-200/70">
            {block.count}
          </span>
        </div>

        {/* Title, Subtitle & Arrow */}
        <div className="flex items-end justify-between gap-2">
          <div className="min-w-0 flex-1">
            <h3 className="text-sm sm:text-base font-bold text-zinc-950 tracking-tight leading-tight group-hover:text-black">
              {block.title}
            </h3>
            <p className="text-[11.5px] text-zinc-500 font-normal leading-tight mt-1 truncate">
              {block.subtitle}
            </p>
          </div>
          <ArrowRight className="w-4 h-4 text-zinc-400 group-hover:text-zinc-950 group-hover:translate-x-1 transition-all shrink-0" />
        </div>

      </div>

      {/* ── Bottom Featured Tool Bar ── */}
      <div className="bg-zinc-50/90 px-4 py-2.5 flex items-center justify-between border-t border-zinc-100 text-[11px]">
        <span className="text-zinc-400 font-mono font-medium">Featured:</span>
        <span className="font-mono font-bold text-zinc-900 group-hover:text-black">
          {block.featuredToolName}
        </span>
      </div>
    </Link>
  );
}
