'use client';

import React from 'react';
import Link from 'next/link';
import { 
  Calculator, 
  Receipt, 
  Scale, 
  Sparkles, 
  QrCode, 
  Code, 
  ArrowRight,
  ArrowUpRight 
} from 'lucide-react';
import { ToolItem } from '@/lib/tools-data';

const ICONS_MAP: Record<string, any> = {
  Calculator,
  Receipt,
  Scale,
  Sparkles,
  QrCode,
  Code,
};

const THEME_MAP: Record<string, { iconBg: string; tagBg: string; borderHover: string }> = {
  'gst-calculator': {
    iconBg: 'bg-emerald-50 text-emerald-700 border border-emerald-200/70',
    tagBg: 'bg-emerald-50/80 text-emerald-800 border-emerald-200/60',
    borderHover: 'hover:border-emerald-300 hover:shadow-[0_12px_32px_rgba(16,185,129,0.08)]',
  },
  'retainer-calculator': {
    iconBg: 'bg-orange-50 text-orange-700 border border-orange-200/70',
    tagBg: 'bg-orange-50/80 text-orange-800 border-orange-200/60',
    borderHover: 'hover:border-orange-300 hover:shadow-[0_12px_32px_rgba(249,115,22,0.08)]',
  },
  'contract-builder': {
    iconBg: 'bg-blue-50 text-blue-700 border border-blue-200/70',
    tagBg: 'bg-blue-50/80 text-blue-800 border-blue-200/60',
    borderHover: 'hover:border-blue-300 hover:shadow-[0_12px_32px_rgba(37,99,235,0.08)]',
  },
  'listing-ai': {
    iconBg: 'bg-rose-50 text-rose-700 border border-rose-200/70',
    tagBg: 'bg-rose-50/80 text-rose-800 border-rose-200/60',
    borderHover: 'hover:border-rose-300 hover:shadow-[0_12px_32px_rgba(244,63,94,0.08)]',
  },
  'upi-qr-generator': {
    iconBg: 'bg-purple-50 text-purple-700 border border-purple-200/70',
    tagBg: 'bg-purple-50/80 text-purple-800 border-purple-200/60',
    borderHover: 'hover:border-purple-300 hover:shadow-[0_12px_32px_rgba(147,51,234,0.08)]',
  },
  'embed-builder': {
    iconBg: 'bg-teal-50 text-teal-700 border border-teal-200/70',
    tagBg: 'bg-teal-50/80 text-teal-800 border-teal-200/60',
    borderHover: 'hover:border-teal-300 hover:shadow-[0_12px_32px_rgba(13,148,136,0.08)]',
  },
};

interface ToolCardProps {
  tool: ToolItem;
}

export function ToolCard({ tool }: ToolCardProps) {
  const Icon = ICONS_MAP[tool.iconName] || Sparkles;
  const theme = THEME_MAP[tool.slug] || {
    iconBg: 'bg-zinc-100 text-zinc-800 border border-zinc-200',
    tagBg: 'bg-zinc-100 text-zinc-800 border-zinc-200',
    borderHover: 'hover:border-zinc-400 hover:shadow-sm',
  };

  return (
    <Link
      href={`/tools/${tool.slug}`}
      className={`group relative rounded-2xl sm:rounded-3xl bg-white border border-zinc-200/80 ${theme.borderHover} p-5 sm:p-6 flex flex-col justify-between shadow-2xs hover:-translate-y-1 transition-all duration-200`}
    >
      <div>
        {/* ── Top Row: Squircle Icon & Capability Tag ── */}
        <div className="flex items-center justify-between gap-3 mb-3.5">
          <div className={`w-10 h-10 rounded-xl sm:rounded-2xl ${theme.iconBg} flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform`}>
            <Icon className="w-5 h-5 stroke-[1.8]" />
          </div>

          <span className={`text-[10px] font-mono font-semibold uppercase tracking-wider px-2.5 py-1 rounded-full border ${theme.tagBg}`}>
            {tool.badge}
          </span>
        </div>

        {/* ── Direct Title (Short & Punchy) ── */}
        <h3 className="text-base sm:text-lg font-bold text-zinc-950 tracking-tight group-hover:text-black transition-colors mb-1.5">
          {tool.shortTitle}
        </h3>

        {/* ── 2-Line Clear Description ── */}
        <p className="text-xs sm:text-sm text-zinc-600 font-normal leading-relaxed mb-4 line-clamp-2">
          {tool.tagline}
        </p>

        {/* ── Inline Key Highlights ── */}
        <div className="flex flex-wrap items-center gap-1.5 mb-5">
          {tool.highlights.slice(0, 3).map((hl, idx) => (
            <span
              key={idx}
              className="text-[10.5px] font-mono font-medium text-zinc-600 bg-zinc-50 border border-zinc-200/60 px-2 py-0.5 rounded-md"
            >
              {hl}
            </span>
          ))}
        </div>
      </div>

      {/* ── Bottom Action Strip ── */}
      <div className="pt-3.5 border-t border-zinc-100 flex items-center justify-between text-xs">
        <span className="font-semibold text-zinc-900 group-hover:text-black inline-flex items-center gap-1 transition-colors">
          <span>Launch Tool</span>
          <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-1 transition-transform" />
        </span>

        <span className="text-[11px] font-mono text-zinc-400">
          Free Forever
        </span>
      </div>
    </Link>
  );
}
