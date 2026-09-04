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
  Files,
  Scissors,
  RotateCw,
  ImageIcon,
  FileCheck,
  Stamp,
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
  Files,
  Scissors,
  RotateCw,
  ImageIcon,
  FileCheck,
  Stamp,
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
  'merge-pdf': {
    iconBg: 'bg-rose-50 text-rose-700 border border-rose-200/60',
    borderHover: 'hover:border-rose-300 hover:shadow-[0_8px_24px_rgba(225,29,72,0.06)]',
  },
  'split-pdf': {
    iconBg: 'bg-amber-50 text-amber-700 border border-amber-200/60',
    borderHover: 'hover:border-amber-300 hover:shadow-[0_8px_24px_rgba(245,158,11,0.06)]',
  },
  'rotate-pdf': {
    iconBg: 'bg-blue-50 text-blue-700 border border-blue-200/60',
    borderHover: 'hover:border-blue-300 hover:shadow-[0_8px_24px_rgba(59,130,246,0.06)]',
  },
  'images-to-pdf': {
    iconBg: 'bg-emerald-50 text-emerald-700 border border-emerald-200/60',
    borderHover: 'hover:border-emerald-300 hover:shadow-[0_8px_24px_rgba(16,185,129,0.06)]',
  },
  'esign-pdf': {
    iconBg: 'bg-indigo-50 text-indigo-700 border border-indigo-200/60',
    borderHover: 'hover:border-indigo-300 hover:shadow-[0_8px_24px_rgba(99,102,241,0.06)]',
  },
  'watermark-pdf': {
    iconBg: 'bg-violet-50 text-violet-700 border border-violet-200/60',
    borderHover: 'hover:border-violet-300 hover:shadow-[0_8px_24px_rgba(139,92,246,0.06)]',
  },
};

const CLEAN_DESCRIPTIONS: Record<string, string> = {
  'gst-calculator': 'Calculate 18% CGST/SGST splits and SAC 9983 tax breakdowns.',
  'retainer-calculator': 'Model monthly agency retainers, hourly rates, and scope buffers.',
  'contract-builder': 'Generate Indian IT Act 2000 digital agreements and NDAs.',
  'listing-ai': 'Create luxury property brochures and creative shoot briefs.',
  'upi-qr-generator': 'Generate dynamic 0% fee UPI payment QR codes and links.',
  'embed-builder': 'Zero-code booking and quote widgets for Framer and Webflow.',
  'merge-pdf': 'Combine proposals, pitch decks, and legal annexures with zero uploads.',
  'split-pdf': 'Extract specific pages and isolate signed signature deeds with 1-click.',
  'rotate-pdf': 'Correct sideways scans and landscape blueprints with lossless rotation.',
  'images-to-pdf': 'Convert JPG, PNG, and WebP photos into clean, standardized PDFs.',
  'esign-pdf': 'Sign contracts directly in browser with Section 10A IT Act compliance.',
  'watermark-pdf': 'Stamp draft and confidentiality watermarks with custom opacity controls.',
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
      className={`group rounded-2xl bg-white border border-zinc-200/80 ${theme.borderHover} p-3.5 sm:p-4.5 lg:p-5 flex flex-col justify-between shadow-2xs hover:-translate-y-0.5 transition-all duration-200 min-h-[125px] sm:min-h-[140px]`}
    >
      <div>
        {/* Top Header: Icon & Top-Right Arrow */}
        <div className="flex items-center justify-between gap-2 mb-2.5 sm:mb-3">
          <div className={`w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl ${theme.iconBg} flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform`}>
            <Icon className="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-[1.8]" />
          </div>

          <ArrowUpRight className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-zinc-300 group-hover:text-zinc-950 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all" />
        </div>

        {/* Title */}
        <h3 className="text-xs sm:text-sm lg:text-[15px] font-bold text-zinc-950 tracking-tight group-hover:text-black transition-colors mb-1 line-clamp-1">
          {tool.shortTitle}
        </h3>

        {/* Clean, Simple 1-2 Line Description */}
        <p className="text-[11px] sm:text-xs text-zinc-500 font-normal leading-relaxed line-clamp-2">
          {cleanDesc}
        </p>
      </div>
    </Link>
  );
}
