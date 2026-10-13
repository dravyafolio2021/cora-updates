'use client';

import React from 'react';
import Link from 'next/link';
import { ArrowRight, CheckCircle2, ShieldCheck, Sparkles } from 'lucide-react';
import { trackToolCoraCtaClicked } from '@/lib/tools-analytics';

export interface ToolContextualCoraCtaProps {
  toolSlug: string;
  badge?: string;
  heading: string;
  description: string;
  bulletPoints: string[];
  ctaText?: string;
  ctaHref?: string;
  secondaryText?: string;
  secondaryHref?: string;
}

export function ToolContextualCoraCta({
  toolSlug,
  badge = 'The Operational Bridge',
  heading,
  description,
  bulletPoints,
  ctaText = 'Explore the Connected Studio Workspace',
  ctaHref = '/pricing',
  secondaryText = 'See All Features',
  secondaryHref = '/features',
}: ToolContextualCoraCtaProps) {
  const handleClick = (buttonType: string) => {
    trackToolCoraCtaClicked({
      tool_slug: toolSlug,
      cta_location: 'bottom_editorial_bridge',
      destination_url: ctaHref,
      button_text: buttonType,
    });
  };

  return (
    <div className="relative rounded-3xl border border-zinc-300/90 bg-white p-6 sm:p-10 shadow-xs overflow-hidden">
      {/* Background Subtle Gradient & Accents */}
      <div className="absolute top-0 right-0 w-96 h-96 bg-zinc-100/60 rounded-full blur-3xl pointer-events-none -mr-20 -mt-20" />
      
      <div className="relative z-10">
        {/* Monochromatic Pill Badge */}
        <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-zinc-100 text-zinc-800 border border-zinc-200/90 text-xs font-mono font-semibold mb-4">
          <Sparkles className="w-3.5 h-3.5 text-zinc-600" />
          <span>{badge}</span>
        </div>

        {/* Heading */}
        <h3 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight leading-tight mb-3 max-w-[800px]">
          {heading}
        </h3>

        {/* Description */}
        <p className="text-zinc-600 text-sm sm:text-base leading-relaxed mb-6 max-w-[760px]">
          {description}
        </p>

        {/* 3-Part Value Checklist */}
        <div className="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-8">
          {bulletPoints.map((point, idx) => (
            <div
              key={idx}
              className="flex items-start gap-2.5 p-3.5 rounded-xl border border-zinc-200/70 bg-zinc-50/70 text-xs text-zinc-700"
            >
              <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
              <span className="font-medium leading-snug">{point}</span>
            </div>
          ))}
        </div>

        {/* Action Buttons */}
        <div className="flex flex-wrap items-center gap-3">
          <Link
            href={ctaHref}
            onClick={() => handleClick('primary_cta')}
            className="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-zinc-900 text-white font-semibold text-sm hover:bg-zinc-800 transition-colors shadow-xs group"
          >
            <span>{ctaText}</span>
            <ArrowRight className="w-4 h-4 group-hover:translate-x-0.5 transition-transform" />
          </Link>

          {secondaryText && secondaryHref && (
            <Link
              href={secondaryHref}
              onClick={() => handleClick('secondary_cta')}
              className="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-zinc-100 text-zinc-800 font-semibold text-sm hover:bg-zinc-200 border border-zinc-200/80 transition-colors"
            >
              <span>{secondaryText}</span>
            </Link>
          )}

          <div className="w-full sm:w-auto mt-2 sm:mt-0 sm:ml-auto flex items-center gap-1.5 text-[11px] font-mono text-zinc-500">
            <ShieldCheck className="w-4 h-4 text-zinc-400" />
            <span>Works alongside your existing WordPress or custom stack</span>
          </div>
        </div>
      </div>
    </div>
  );
}
