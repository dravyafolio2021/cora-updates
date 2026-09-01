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
      className={`w-[200px] sm:w-[220px] lg:w-full shrink-0 flex flex-col rounded-3xl bg-white border ${block.borderColor} ${block.borderHover} overflow-hidden shadow-2xs hover:-translate-y-1.5 transition-all duration-300 group`}
    >
      {/* ── Top Luminous Header Block ── */}
      <div className={`${block.cardBg} p-4 flex flex-col justify-between h-[134px] relative`}>
        
        {/* Top Icon & Real Capability Tag */}
        <div className="flex items-center justify-between">
          <div className={`w-8 h-8 rounded-xl ${block.iconBg} flex items-center justify-center group-hover:scale-110 transition-transform`}>
            <Icon className="w-4 h-4 stroke-[2]" />
          </div>
          <span className={`text-[10px] font-mono font-bold ${block.badgeBg} px-2 py-0.5 rounded-full border border-black/5`}>
            {block.badgeTag}
          </span>
        </div>

        {/* Title, Subtitle & Arrow */}
        <div className="flex items-end justify-between gap-1.5 pt-2">
          <div className="min-w-0 flex-1">
            <h3 className="text-sm font-bold text-zinc-950 tracking-tight leading-tight group-hover:text-black">
              {block.title}
            </h3>
            <p className="text-[10.5px] text-zinc-600 font-normal leading-tight mt-1">
              {block.subtitle}
            </p>
          </div>
          <ArrowRight className={`w-3.5 h-3.5 ${block.accentText} group-hover:translate-x-1 transition-all shrink-0`} />
        </div>

      </div>

      {/* ── Bottom White Action Bar ── */}
      <div className="bg-white px-3.5 py-2.5 flex items-center justify-between border-t border-zinc-100/80 text-[10.5px]">
        <span className="text-zinc-400 font-medium">Launch :</span>
        <span className={`font-bold ${block.accentText} group-hover:underline underline-offset-2`}>
          {block.featuredToolName}
        </span>
      </div>
    </Link>
  );
}
