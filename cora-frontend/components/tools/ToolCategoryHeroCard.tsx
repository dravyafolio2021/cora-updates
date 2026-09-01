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
      className={`w-[220px] sm:w-[230px] lg:w-full shrink-0 flex flex-col rounded-3xl bg-white border ${block.borderColor} ${block.borderHover} overflow-hidden shadow-2xs hover:-translate-y-1.5 transition-all duration-300 group`}
    >
      {/* ── Top Luminous Pastel Header Block ── */}
      <div className={`${block.cardBg} p-4 sm:p-5 flex flex-col justify-between h-[138px] relative`}>
        
        {/* Top Icon & Count Pill */}
        <div className="flex items-center justify-between">
          <div className={`w-9 h-9 rounded-2xl ${block.iconBg} flex items-center justify-center group-hover:scale-110 transition-transform`}>
            <Icon className="w-4 h-4 stroke-[2]" />
          </div>
          <span className={`text-[10px] font-mono font-bold ${block.badgeBg} px-2.5 py-0.5 rounded-full border border-black/5`}>
            {block.count}
          </span>
        </div>

        {/* Title, Subtitle & Arrow */}
        <div className="flex items-end justify-between gap-2">
          <div className="min-w-0 flex-1">
            <h3 className="text-sm sm:text-base font-bold text-zinc-950 tracking-tight leading-tight group-hover:text-black">
              {block.title}
            </h3>
            <p className="text-[11.5px] text-zinc-600 font-normal leading-tight mt-1 truncate">
              {block.subtitle}
            </p>
          </div>
          <ArrowRight className={`w-4 h-4 ${block.accentText} group-hover:translate-x-1 transition-all shrink-0`} />
        </div>

      </div>

      {/* ── Bottom White Featured Tool Bar ── */}
      <div className="bg-white px-4 py-2.5 flex items-center justify-between border-t border-zinc-100/80 text-[11px]">
        <span className="text-zinc-400 font-medium">Featured :</span>
        <span className={`font-bold ${block.accentText} group-hover:underline underline-offset-2`}>
          {block.featuredToolName}
        </span>
      </div>
    </Link>
  );
}
