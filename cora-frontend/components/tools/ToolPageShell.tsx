'use client';

import React from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { 
  ArrowLeft, 
  Sparkles, 
  ArrowRight, 
  CheckCircle2, 
  ShieldCheck, 
  Zap, 
  Lock,
  XCircle
} from 'lucide-react';

export interface ToolPageShellProps {
  toolId: string;
  badgeTag: string;
  title: string;
  subtitle: string;
  children: React.ReactNode;
  promoImage?: string;
  promoTitle: string;
  promoSubtitle: string;
  painPoints: Array<{ problem: string; solution: string }>;
  promoCtaText?: string;
  faqItems?: Array<{ question: string; answer: string }>;
  relatedToolSlugs?: string[];
}

export function ToolPageShell({
  toolId,
  badgeTag,
  title,
  subtitle,
  children,
  promoImage = '/images/cora_gst_upi_3d.jpg',
  promoTitle,
  promoSubtitle,
  painPoints = [],
  promoCtaText = 'Automate Your Entire Business Free',
  faqItems,
  relatedToolSlugs = [],
}: ToolPageShellProps) {
  return (
    <div className="w-full bg-white text-zinc-900 min-h-screen py-10 sm:py-16 selection:bg-zinc-900 selection:text-white">
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
          <div className="inline-flex items-center gap-1.5 text-[11px] font-mono font-bold text-zinc-800 px-3 py-1 bg-zinc-100 border border-zinc-200/80 rounded-full mb-3">
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
            <div className="p-4 rounded-2xl bg-zinc-50 border border-zinc-200/80 flex flex-wrap items-center justify-between gap-3 text-xs text-zinc-600">
              <div className="flex items-center gap-2">
                <Lock className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                <span className="font-medium">100% Client-Side Execution (Zero financial or client data stored)</span>
              </div>
              <div className="flex items-center gap-2 font-mono text-[11px] text-zinc-500">
                <Zap className="w-3 h-3 text-amber-500 shrink-0" />
                <span>Zero Login Required</span>
              </div>
            </div>

            {/* Optional FAQ Accordion for the Tool */}
            {faqItems && faqItems.length > 0 && (
              <div className="pt-6 border-t border-zinc-100 space-y-4">
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
              30% RIGHT SECTION (Inspiring Borderless Ad Creative)
              ══════════════════════════════════════════════════════════════ */}
          <div className="lg:col-span-4 lg:sticky lg:top-24 space-y-4">
            
            {/* Inspiring Ad Creative Container (No Outline, Elevated Shadow) */}
            <div className="rounded-3xl bg-white shadow-[0_20px_50px_rgba(0,0,0,0.08)] overflow-hidden flex flex-col justify-between">
              
              {/* Top Hero Image Banner */}
              <div className="relative w-full h-44 sm:h-48 overflow-hidden bg-zinc-950">
                <Image
                  src={promoImage}
                  alt={promoTitle}
                  fill
                  className="object-cover object-center group-hover:scale-105 transition-transform duration-500"
                  sizes="(max-width: 768px) 100vw, 400px"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-zinc-950/80 via-transparent to-black/20" />
                
                {/* Floating Sponsor Pill */}
                <div className="absolute top-3 left-3">
                  <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/90 backdrop-blur-md text-zinc-950 text-[10px] font-mono font-bold uppercase tracking-wider shadow-sm">
                    <Sparkles className="w-3 h-3 text-amber-500" />
                    <span>Cora Autonomous OS</span>
                  </span>
                </div>

                {/* Bottom Image Headline Overlay */}
                <div className="absolute bottom-3 left-4 right-4">
                  <span className="text-[10.5px] font-mono text-emerald-400 font-bold uppercase tracking-wider block mb-0.5">
                    Upgrade to Autopilot
                  </span>
                  <h3 className="text-base sm:text-lg font-bold text-white leading-tight tracking-tight">
                    {promoTitle}
                  </h3>
                </div>
              </div>

              {/* Ad Creative Body */}
              <div className="p-5 sm:p-6 space-y-4">
                
                {/* Subtitle */}
                <p className="text-xs text-zinc-600 leading-relaxed font-normal">
                  {promoSubtitle}
                </p>

                {/* Pain Points vs Solutions Transformation */}
                <div className="space-y-3 pt-2 border-t border-zinc-100">
                  <span className="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400 block">
                    Why Manual Tools Slow You Down:
                  </span>
                  
                  {painPoints.map((item, idx) => (
                    <div key={idx} className="space-y-1 text-xs">
                      {/* Pain Point */}
                      <div className="flex items-start gap-1.5 text-zinc-400 line-through decoration-zinc-300">
                        <XCircle className="w-3.5 h-3.5 text-rose-400 shrink-0 mt-0.5" />
                        <span className="text-[11px] leading-tight">{item.problem}</span>
                      </div>
                      {/* Cora Solution */}
                      <div className="flex items-start gap-1.5 text-zinc-900 font-semibold pl-5">
                        <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600 shrink-0 mt-0.5" />
                        <span className="text-[11.5px] leading-tight">{item.solution}</span>
                      </div>
                    </div>
                  ))}
                </div>

                {/* High-Converting Full Platform CTA */}
                <div className="pt-2">
                  <a
                    href="http://cora.local/workspace/login"
                    className="w-full flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs sm:text-sm transition-all shadow-md hover:scale-[1.02] cursor-pointer"
                  >
                    <span>{promoCtaText}</span>
                    <ArrowRight className="w-4 h-4 text-zinc-400" />
                  </a>
                  <span className="block text-center text-[10.5px] text-zinc-500 mt-2 font-medium">
                    100% Free Forever • Zero Setup Fees
                  </span>
                </div>

              </div>

            </div>

          </div>

        </div>

      </div>
    </div>
  );
}
