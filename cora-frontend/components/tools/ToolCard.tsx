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

const THEME_MAP: Record<string, { iconBg: string; borderHover: string }> = {
  'gst-calculator': {
    iconBg: 'bg-emerald-50 text-emerald-700 border border-emerald-200/60',
    borderHover: 'hover:border-emerald-300 hover:shadow-[0_8px_24px_rgba(16,185,129,0.06)]',
  },
  'retainer-calculator': {
    iconBg: 'bg-orange-50 text-orange-700 border border-orange-200/60',
    borderHover: 'hover:border-orange-300 hover:shadow-[0_8px_24px_rgba(249,115,22,0.06)]',
  },
  'contract-builder': {
    iconBg: 'bg-blue-50 text-blue-700 border border-blue-200/60',
    borderHover: 'hover:border-blue-300 hover:shadow-[0_8px_24px_rgba(37,99,235,0.06)]',
  },
  'listing-ai': {
    iconBg: 'bg-rose-50 text-rose-700 border border-rose-200/60',
    borderHover: 'hover:border-rose-300 hover:shadow-[0_8px_24px_rgba(244,63,94,0.06)]',
  },
  'upi-qr-generator': {
    iconBg: 'bg-purple-50 text-purple-700 border border-purple-200/60',
    borderHover: 'hover:border-purple-300 hover:shadow-[0_8px_24px_rgba(147,51,234,0.06)]',
  },
  'embed-builder': {
    iconBg: 'bg-teal-50 text-teal-700 border border-teal-200/60',
    borderHover: 'hover:border-teal-300 hover:shadow-[0_8px_24px_rgba(13,148,136,0.06)]',
  },
};

const CLEAN_DESCRIPTIONS: Record<string, string> = {
  'gst-calculator': 'Calculate 18% CGST/SGST splits and SAC 9983 tax breakdowns.',
  'retainer-calculator': 'Model monthly agency retainers, hourly rates, and scope buffers.',
  'contract-builder': 'Generate Indian IT Act 2000 digital agreements and NDAs.',
  'listing-ai': 'Create luxury property brochures and creative shoot briefs.',
  'upi-qr-generator': 'Generate dynamic 0% fee UPI payment QR codes and links.',
  'embed-builder': 'Zero-code booking and quote widgets for Framer and Webflow.',
};

interface ToolCardProps {
  tool: ToolItem;
}

export function ToolCard({ tool }: ToolCardProps) {
  const Icon = ICONS_MAP[tool.iconName] || Sparkles;
  const theme = THEME_MAP[tool.slug] || {
    iconBg: 'bg-zinc-100 text-zinc-800 border border-zinc-200',
    borderHover: 'hover:border-zinc-400 hover:shadow-xs',
  };
  const cleanDesc = CLEAN_DESCRIPTIONS[tool.slug] || tool.tagline;

  return (
    <Link
      href={`/tools/${tool.slug}`}
      className={`group rounded-2xl bg-white border border-zinc-200/80 ${theme.borderHover} p-4 sm:p-5 flex flex-col justify-between shadow-2xs hover:-translate-y-0.5 transition-all duration-200 min-h-[135px]`}
    >
      <div>
        {/* Top Header: Icon & Top-Right Arrow */}
        <div className="flex items-center justify-between gap-2 mb-3">
          <div className={`w-8 h-8 rounded-xl ${theme.iconBg} flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform`}>
            <Icon className="w-4 h-4 stroke-[1.8]" />
          </div>

          <ArrowUpRight className="w-4 h-4 text-zinc-300 group-hover:text-zinc-950 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all" />
        </div>

        {/* Title */}
        <h3 className="text-sm sm:text-base font-bold text-zinc-950 tracking-tight group-hover:text-black transition-colors mb-1">
          {tool.shortTitle}
        </h3>

        {/* Clean, Simple 1-2 Line Description */}
        <p className="text-xs text-zinc-500 font-normal leading-relaxed">
          {cleanDesc}
        </p>
      </div>
    </Link>
  );
}
