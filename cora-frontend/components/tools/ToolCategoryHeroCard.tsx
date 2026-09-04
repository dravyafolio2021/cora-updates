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
  ArrowRight,
  Layers,
  Scissors,
  RotateCw,
  FileText
} from 'lucide-react';
import { ToolCategoryBlock } from '@/lib/tools-data';

const ICONS_MAP: Record<string, any> = {
  Calculator,
  Receipt,
  Sparkles,
  Scale,
  Code,
  QrCode,
  Layers,
  Scissors,
  RotateCw,
  FileText,
};

interface ToolCategoryHeroCardProps {
  block: ToolCategoryBlock;
}

export function ToolCategoryHeroCard({ block }: ToolCategoryHeroCardProps) {
  const Icon = ICONS_MAP[block.iconName] || Sparkles;

  return (
    <Link
      href={`/tools/${block.featuredToolSlug}`}
      className={`w-full flex flex-col rounded-2xl sm:rounded-3xl bg-white border ${block.borderColor} ${block.borderHover} overflow-hidden shadow-2xs hover:-translate-y-1 transition-all duration-300 group`}
    >
      {/* ── Top Luminous Header Block ── */}
      <div className={`${block.cardBg} p-3 sm:p-4 flex flex-col justify-between h-[112px] sm:h-[134px] relative`}>
        
        {/* Top Icon & Real Capability Tag */}
        <div className="flex items-center justify-between">
          <div className={`w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl ${block.iconBg} flex items-center justify-center group-hover:scale-105 transition-transform`}>
            <Icon className="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-[2]" />
          </div>
          <span className={`text-[9px] sm:text-[10px] font-mono font-bold ${block.badgeBg} px-2 py-0.5 rounded-full border border-black/5`}>
            {block.badgeTag}
          </span>
        </div>

        {/* Title, Subtitle & Arrow */}
        <div className="flex items-end justify-between gap-1 pt-1.5 sm:pt-2">
          <div className="min-w-0 flex-1">
            <h3 className="text-xs sm:text-sm font-bold text-zinc-950 tracking-tight leading-tight group-hover:text-black">
              {block.title}
            </h3>
            <p className="text-[9.5px] sm:text-[10.5px] text-zinc-600 font-normal leading-tight mt-0.5 sm:mt-1">
              {block.subtitle}
            </p>
          </div>
          <ArrowRight className={`w-3 h-3 sm:w-3.5 sm:h-3.5 ${block.accentText} group-hover:translate-x-0.5 transition-all shrink-0`} />
        </div>

      </div>

      {/* ── Bottom White Action Bar ── */}
      <div className="bg-white px-3 py-2 sm:px-3.5 sm:py-2.5 flex items-center justify-between border-t border-zinc-100/80 text-[9.5px] sm:text-[10.5px]">
        <span className="text-zinc-400 font-medium">Launch :</span>
        <span className={`font-bold ${block.accentText} group-hover:underline underline-offset-2`}>
          {block.featuredToolName}
        </span>
      </div>
    </Link>
  );
}
