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
      className="w-[220px] sm:w-[230px] lg:w-full shrink-0 flex flex-col rounded-2xl sm:rounded-3xl overflow-hidden shadow-xs hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 group border border-zinc-100"
    >
      {/* ── Top Vibrant Colored Block ── */}
      <div className={`${block.bgColor} p-4 sm:p-5 flex flex-col justify-between h-[132px] relative`}>
        
        {/* Top Icon & Count Pill */}
        <div className="flex items-center justify-between">
          <div className="w-8 h-8 rounded-xl bg-white/20 backdrop-blur-xs text-white flex items-center justify-center shadow-2xs group-hover:scale-110 transition-transform">
            <Icon className="w-4 h-4 stroke-[2.2]" />
          </div>
          <span className="text-[10px] font-mono font-bold text-white/90 bg-black/15 px-2 py-0.5 rounded-full">
            {block.count}
          </span>
        </div>

        {/* Title, Subtitle & Arrow */}
        <div className="flex items-end justify-between gap-2">
          <div>
            <h3 className="text-base font-bold text-white tracking-tight leading-tight">
              {block.title}
            </h3>
            <p className="text-[11px] text-white/80 leading-tight mt-0.5">
              {block.subtitle}
            </p>
          </div>
          <ArrowRight className="w-4 h-4 text-white/80 group-hover:text-white group-hover:translate-x-1 transition-all shrink-0" />
        </div>

      </div>

      {/* ── Bottom White Featured Tool Bar ── */}
      <div className="bg-white px-4 py-2.5 flex items-center justify-between border-t border-zinc-100/80 text-[11px]">
        <span className="text-zinc-400 font-medium">Featured Tool :</span>
        <span className="font-bold text-zinc-900 group-hover:text-black">
          {block.featuredToolName}
        </span>
      </div>
    </Link>
  );
}
