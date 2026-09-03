'use client';

import React from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { 
  ArrowLeft, 
  Sparkles, 
  ArrowRight, 
  ExternalLink,
  Lock,
  Zap,
  MoreHorizontal,
  Globe,
  ThumbsUp,
  MessageCircle,
  Share2,
  Star,
  Check
} from 'lucide-react';

export interface ToolPageShellProps {
  toolId: string;
  badgeTag: string;
  title: string;
  subtitle: string;
  children: React.ReactNode;
  
  // Ad 1: Meta / Facebook Style Sponsored Feed Ad
  metaAd: {
    primaryText: string;
    image: string;
    headline: string;
    description: string;
    ctaText?: string;
    badge?: string;
  };

  // Ad 2: Google Performance / Discovery Sponsored Ad
  googleAd: {
    title: string;
    description: string;
    sitelinks: string[];
    ctaText?: string;
    image?: string;
  };

  faqItems?: Array<{ question: string; answer: string }>;
  relatedToolSlugs?: string[];
}

export function ToolPageShell({
  toolId,
  badgeTag,
  title,
  subtitle,
  children,
  metaAd,
  googleAd,
  faqItems,
  relatedToolSlugs = [],
}: ToolPageShellProps) {
  return (
    <div className="w-full bg-[#FAFAF9] text-zinc-900 min-h-screen py-10 sm:py-16 selection:bg-zinc-900 selection:text-white">
      <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        
        {/* ── Top Back Navigation ── */}
        <div className="mb-6 sm:mb-8">
          <Link
            href="/tools"
            className="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-500 hover:text-zinc-950 transition-colors"
          >
            <ArrowLeft className="w-3.5 h-3.5" />
            <span>Back to all micro-tools</span>
          </Link>
        </div>

        {/* ── Header Title & Badge ── */}
        <div className="mb-8 sm:mb-10 max-w-3xl">
          <div className="inline-flex items-center gap-1.5 text-[11px] font-mono font-bold text-zinc-800 px-3 py-1 bg-white border border-zinc-200/80 rounded-full mb-3 shadow-2xs">
            <span>{badgeTag}</span>
          </div>
          <h1 className="font-display text-2xl sm:text-4xl lg:text-5xl font-extrabold text-zinc-950 tracking-[-0.035em] leading-[1.18] mb-3">
            {title}
          </h1>
          <p className="text-zinc-600 text-xs sm:text-base font-normal leading-relaxed">
            {subtitle}
          </p>
        </div>

        {/* ── Master 70% / 30% Split Layout ── */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          
          {/* ══════════════════════════════════════════════════════════════
              70% LEFT SECTION (The Actual Working Interactive Tool)
              ══════════════════════════════════════════════════════════════ */}
          <div className="lg:col-span-8 space-y-6 sm:space-y-8">
            
            {/* The Tool Engine */}
            <div className="w-full">
              {children}
            </div>

            {/* Privacy & Trust Bar */}
            <div className="p-4 rounded-2xl bg-white border border-zinc-200/80 flex flex-wrap items-center justify-between gap-3 text-xs text-zinc-600 shadow-2xs">
              <div className="flex items-center gap-2">
                <Lock className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                <span className="font-medium">100% Client-Side Execution (Zero financial data transmitted or stored)</span>
              </div>
              <div className="flex items-center gap-2 font-mono text-[11px] text-zinc-500">
                <Zap className="w-3 h-3 text-amber-500 shrink-0" />
                <span>Zero Login Required</span>
              </div>
            </div>

            {/* Optional FAQ Accordion for the Tool */}
            {faqItems && faqItems.length > 0 && (
              <div className="pt-6 border-t border-zinc-200/60 space-y-4">
                <h3 className="font-display text-lg font-bold text-zinc-950 tracking-tight">
                  Frequently Asked Questions
                </h3>
                <div className="space-y-3">
                  {faqItems.map((faq, idx) => (
                    <div key={idx} className="p-4 rounded-2xl bg-white border border-zinc-200/80 shadow-2xs">
                      <h4 className="text-xs sm:text-sm font-bold text-zinc-900 mb-1.5">
                        {faq.question}
                      </h4>
                      <p className="text-xs text-zinc-600 leading-relaxed font-normal">
                        {faq.answer}
                      </p>
                    </div>
                  ))}
                </div>
              </div>
            )}

          </div>

          {/* ══════════════════════════════════════════════════════════════
              30% RIGHT SECTION (TWO HIGH-CONVERTING SPONSORED ADS)
              ══════════════════════════════════════════════════════════════ */}
          <div className="lg:col-span-4 lg:sticky lg:top-24 space-y-5">
            
            {/* ──────────────────────────────────────────────────────────
                AD 1: META / FACEBOOK SPONSORED FEED AD CREATIVE
                ────────────────────────────────────────────────────────── */}
            <div className="rounded-2xl bg-white border border-zinc-200/90 shadow-[0_8px_30px_rgba(0,0,0,0.04)] overflow-hidden transition-all hover:shadow-[0_12px_40px_rgba(0,0,0,0.08)]">
              
              {/* Sponsored Ad Header */}
              <div className="p-3.5 sm:p-4 flex items-center justify-between border-b border-zinc-100">
                <div className="flex items-center gap-2.5">
                  {/* Brand Avatar */}
                  <div className="w-8 h-8 rounded-full bg-zinc-950 text-white flex items-center justify-center font-bold text-xs shadow-2xs">
                    C
                  </div>
                  <div>
                    <div className="flex items-center gap-1">
                      <span className="text-xs font-bold text-zinc-950 leading-tight">Cora</span>
                      <span className="text-[10px] text-blue-600 font-bold">✓</span>
                    </div>
                    <div className="flex items-center gap-1 text-[10px] text-zinc-400 font-medium">
                      <span>Sponsored</span>
                      <span>•</span>
                      <Globe className="w-2.5 h-2.5 text-zinc-400" />
                    </div>
                  </div>
                </div>
                <MoreHorizontal className="w-4 h-4 text-zinc-400 hover:text-zinc-600 cursor-pointer" />
              </div>

              {/* Primary Ad Copy Text */}
              <div className="px-3.5 sm:px-4 pt-3 pb-2.5">
                <p className="text-xs text-zinc-800 font-normal leading-relaxed">
                  {metaAd.primaryText}
                </p>
              </div>

              {/* Ad Creative Image Banner */}
              <div className="relative w-full h-44 sm:h-48 bg-zinc-900 overflow-hidden">
                <Image
                  src={metaAd.image}
                  alt={metaAd.headline}
                  fill
                  className="object-cover object-center hover:scale-105 transition-transform duration-500"
                  sizes="(max-width: 768px) 100vw, 400px"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent" />
                
                {metaAd.badge && (
                  <div className="absolute top-2.5 right-2.5">
                    <span className="px-2.5 py-1 rounded-full bg-zinc-950/85 backdrop-blur-md text-white text-[10px] font-mono font-bold tracking-wide border border-white/20 shadow-xs">
                      {metaAd.badge}
                    </span>
                  </div>
                )}
              </div>

              {/* Ad Link Preview Footer & CTA */}
              <div className="p-3.5 sm:p-4 bg-zinc-50 border-t border-zinc-100 flex items-center justify-between gap-3">
                <div className="min-w-0 pr-1">
                  <span className="block text-[10px] font-mono text-zinc-400 uppercase tracking-wider truncate">
                    heycora.in
                  </span>
                  <h4 className="text-xs font-bold text-zinc-950 truncate leading-snug">
                    {metaAd.headline}
                  </h4>
                  <p className="text-[11px] text-zinc-500 truncate font-normal">
                    {metaAd.description}
                  </p>
                </div>
                <a
                  href="http://cora.local/workspace/login?utm_source=meta_ad_unit"
                  className="shrink-0 px-3.5 py-2 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs transition-all shadow-xs flex items-center gap-1.5 cursor-pointer"
                >
                  <span>{metaAd.ctaText || 'Sign Up'}</span>
                  <ExternalLink className="w-3 h-3 text-zinc-400" />
                </a>
              </div>

              {/* Simulated Social Engagement Bar */}
              <div className="px-3.5 py-2 bg-white border-t border-zinc-100 flex items-center justify-between text-[11px] text-zinc-500 font-medium">
                <div className="flex items-center gap-1.5">
                  <span className="flex -space-x-1">
                    <span className="w-4 h-4 rounded-full bg-blue-500 flex items-center justify-center text-[9px] text-white">👍</span>
                    <span className="w-4 h-4 rounded-full bg-rose-500 flex items-center justify-center text-[9px] text-white">❤️</span>
                  </span>
                  <span>4.8k</span>
                </div>
                <div className="flex items-center gap-3">
                  <span>384 comments</span>
                  <span>1.2k shares</span>
                </div>
              </div>

            </div>

            {/* ──────────────────────────────────────────────────────────
                AD 2: GOOGLE DISPLAY / SEARCH DISCOVERY SPONSORED AD UNIT
                ────────────────────────────────────────────────────────── */}
            <div className="rounded-2xl bg-white border border-zinc-200/90 p-4 sm:p-5 shadow-[0_8px_30px_rgba(0,0,0,0.04)] space-y-3 transition-all hover:shadow-[0_12px_40px_rgba(0,0,0,0.08)]">
              
              {/* Google Ad Badge & URL */}
              <div className="flex items-center justify-between">
                <div className="flex items-center gap-1.5">
                  <span className="px-1.5 py-0.5 rounded bg-amber-100/80 border border-amber-200 text-amber-900 font-bold text-[10px] font-mono leading-none">
                    Ad
                  </span>
                  <span className="text-[11px] text-zinc-500 font-medium truncate">
                    https://heycora.in/workspace/free
                  </span>
                </div>
                <div className="flex items-center gap-0.5 text-amber-500">
                  {[...Array(5)].map((_, i) => (
                    <Star key={i} className="w-2.5 h-2.5 fill-current" />
                  ))}
                </div>
              </div>

              {/* Ad Title */}
              <div>
                <a
                  href="http://cora.local/workspace/login?utm_source=google_ad_unit"
                  className="text-sm font-bold text-blue-600 hover:underline leading-snug cursor-pointer block"
                >
                  {googleAd.title}
                </a>
                <p className="text-xs text-zinc-600 font-normal leading-relaxed mt-1">
                  {googleAd.description}
                </p>
              </div>

              {/* Sitelink Extensions */}
              <div className="grid grid-cols-2 gap-2 pt-2 border-t border-zinc-100 text-[11px]">
                {googleAd.sitelinks.map((link, idx) => (
                  <div key={idx} className="flex items-center gap-1 text-zinc-700 font-medium">
                    <Check className="w-3 h-3 text-emerald-600 shrink-0" />
                    <span className="truncate">{link}</span>
                  </div>
                ))}
              </div>

              {/* Big High-Converting Signup Action */}
              <div className="pt-2">
                <a
                  href="http://cora.local/workspace/login?utm_source=google_ad_unit_cta"
                  className="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs transition-all shadow-xs cursor-pointer"
                >
                  <span>{googleAd.ctaText || 'Claim Free Workspace'}</span>
                  <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                </a>
                <span className="block text-center text-[10px] text-zinc-400 mt-1.5 font-medium">
                  100% Free Forever • Zero Credit Card • 90-sec Setup
                </span>
              </div>

            </div>

          </div>

        </div>

      </div>
    </div>
  );
}
