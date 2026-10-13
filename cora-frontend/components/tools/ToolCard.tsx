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

const DEFAULT_THEME = {
  iconBg: 'bg-zinc-100 text-zinc-900 border border-zinc-200/80',
  borderHover: 'hover:border-zinc-300 hover:shadow-xs',
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
  const cleanDesc = CLEAN_DESCRIPTIONS[tool.slug] || tool.tagline;

  return (
    <Link
      href={`/tools/${tool.slug}`}
      className={`group rounded-2xl bg-white border border-zinc-200/80 ${DEFAULT_THEME.borderHover} p-3.5 sm:p-4.5 lg:p-5 flex flex-col justify-between shadow-2xs hover:-translate-y-0.5 transition-all duration-200 min-h-[125px] sm:min-h-[140px] touch-manipulation active:scale-[0.99]`}
    >
      <div>
        {/* Top Header: Icon & Top-Right Arrow */}
        <div className="flex items-center justify-between gap-2 mb-2.5 sm:mb-3">
          <div className={`w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl ${DEFAULT_THEME.iconBg} flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform`}>
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
