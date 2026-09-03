'use client';

import React, { useState, useEffect } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { 
  ArrowLeft, 
  Sparkles, 
  ArrowRight, 
  Lock, 
  Zap, 
  Share2, 
  Check, 
  ChevronDown,
  Clock
} from 'lucide-react';
import { TOOL_AGENT_REGISTRY, ToolAgentData } from '@/lib/tools-agent-config';
import { useToast } from '@/components/ui/Toast';

export interface ToolPageShellProps {
  toolId: string;
  badgeTag: string;
  title: string;
  subtitle: string;
  children: React.ReactNode;
  faqItems?: Array<{ question: string; answer: string }>;
  relatedToolSlugs?: string[];
}

export function ToolPageShell({
  toolId,
  badgeTag,
  title,
  subtitle,
  children,
  faqItems,
  relatedToolSlugs = [],
}: ToolPageShellProps) {
  const { showToast } = useToast();
  const [copiedShare, setCopiedShare] = useState(false);
  const [openFaqIndex, setOpenFaqIndex] = useState<number | null>(null);

  // 10-minute dynamic countdown timer for 40% discount on India Only plan
  const [secondsLeft, setSecondsLeft] = useState(600);

  useEffect(() => {
    const timer = setInterval(() => {
      setSecondsLeft((prev) => (prev > 0 ? prev - 1 : 600));
    }, 1000);
    return () => clearInterval(timer);
  }, []);

  const formatTimer = (totalSeconds: number) => {
    const mins = Math.floor(totalSeconds / 60);
    const secs = totalSeconds % 60;
    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
  };

  // Retrieve dynamic AI Agent and Showcase config for this specific tool
  const agentData: ToolAgentData = TOOL_AGENT_REGISTRY[toolId] || TOOL_AGENT_REGISTRY['gst-calculator'];

  const handleShareTool = () => {
    if (typeof window !== 'undefined') {
      navigator.clipboard.writeText(window.location.href);
      setCopiedShare(true);
      showToast('Tool link copied to clipboard!');
      setTimeout(() => setCopiedShare(false), 2200);
    }
  };

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

        {/* ── Master 2-Column Split Layout ── */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          
          {/* ══════════════════════════════════════════════════════════════
              LEFT SECTION (The Actual Working Interactive Tool Engine)
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

            {/* Optional FAQ Accordion for the Tool (Compact Collapsible Toggles) */}
            {faqItems && faqItems.length > 0 && (
              <div className="pt-6 border-t border-zinc-200/60 space-y-3">
                <div className="flex items-center justify-between mb-2">
                  <h3 className="font-display text-base sm:text-lg font-bold text-zinc-950 tracking-tight">
                    Frequently Asked Questions
                  </h3>
                  <span className="text-[11px] font-mono text-zinc-400">
                    {faqItems.length} FAQs
                  </span>
                </div>
                <div className="space-y-2">
                  {faqItems.map((faq, idx) => {
                    const isOpen = openFaqIndex === idx;
                    return (
                      <div 
                        key={idx} 
                        className="rounded-2xl bg-white border border-zinc-200/80 shadow-2xs overflow-hidden transition-all"
                      >
                        <button
                          type="button"
                          onClick={() => setOpenFaqIndex(isOpen ? null : idx)}
                          className="w-full p-4 text-left flex items-center justify-between gap-3 hover:bg-zinc-50/60 transition-colors cursor-pointer"
                        >
                          <h4 className="text-xs sm:text-sm font-bold text-zinc-900 leading-snug">
                            {faq.question}
                          </h4>
                          <ChevronDown 
                            className={`w-4 h-4 text-zinc-400 shrink-0 transition-transform duration-200 ${
                              isOpen ? 'rotate-180 text-zinc-900' : ''
                            }`} 
                          />
                        </button>
                        {isOpen && (
                          <div className="px-4 pb-4 pt-1 text-xs text-zinc-600 leading-relaxed font-normal border-t border-zinc-100 bg-zinc-50/30 animate-in fade-in duration-150">
                            {faq.answer}
                          </div>
                        )}
                      </div>
                    );
                  })}
                </div>
              </div>
            )}

          </div>

          {/* ══════════════════════════════════════════════════════════════
              RIGHT SECTION (IMMERSIVE FULL-BLEED 3D CREATIVE CARD)
              ══════════════════════════════════════════════════════════════ */}
          <div className="lg:col-span-4 lg:sticky lg:top-24">
            
            {/* ── Immersive Full-Bleed Creative Card ── */}
            <div className="relative rounded-[28px] overflow-hidden shadow-[0_16px_48px_rgba(0,0,0,0.14)] border border-zinc-200/80 bg-zinc-950 min-h-[490px] sm:min-h-[530px] flex flex-col justify-between p-5 sm:p-6 text-white group">
              
              {/* Full-Bleed 3D Visual Artwork */}
              <Image
                src={agentData.card1.image}
                alt={agentData.card1.headline}
                fill
                priority
                className="object-cover object-center group-hover:scale-105 transition-transform duration-700"
                sizes="(max-width: 768px) 100vw, 420px"
              />

              {/* Multi-Stop Dark Vignettes for Contrast & Legibility */}
              <div className="absolute inset-x-0 top-0 h-28 bg-gradient-to-b from-black/60 via-transparent to-transparent pointer-events-none" />
              <div className="absolute inset-x-0 bottom-0 h-80 bg-gradient-to-t from-black/95 via-black/80 to-transparent pointer-events-none" />

              {/* Top Right Frosted Feature Pill */}
              <div className="relative z-10 flex justify-end">
                <span className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-zinc-950/80 backdrop-blur-md text-white text-[11px] font-mono font-bold tracking-wide border border-white/20 shadow-md">
                  <span>⚡</span>
                  <span>{agentData.card1.badge.replace(/^⚡\s*/, '')}</span>
                </span>
              </div>

              {/* Bottom Content Overlay */}
              <div className="relative z-10 space-y-3 pt-20">
                
                {/* 1-Liner Headline + Share Button */}
                <div className="flex items-center justify-between gap-2.5">
                  <h3 className="font-display text-base sm:text-lg font-bold text-white tracking-tight leading-snug truncate drop-shadow-sm">
                    {agentData.card1.headline}
                  </h3>

                  {/* Share Tool Icon Button */}
                  <button
                    type="button"
                    onClick={handleShareTool}
                    title="Share this tool"
                    className="p-2 rounded-xl bg-white/90 hover:bg-white text-zinc-950 transition-all cursor-pointer backdrop-blur-md shadow-sm shrink-0 hover:scale-105"
                  >
                    {copiedShare ? (
                      <Check className="w-3.5 h-3.5 text-emerald-600" />
                    ) : (
                      <Share2 className="w-3.5 h-3.5" />
                    )}
                  </button>
                </div>

                {/* 2-Liner Description */}
                <p className="text-xs text-zinc-200 font-normal leading-relaxed line-clamp-2 drop-shadow-xs">
                  {agentData.card1.primaryText}
                </p>

                {/* Bottom Action Bar with 40% India Flash Discount & Live 10-Min Timer */}
                <div className="flex items-center justify-between gap-2 pt-3 border-t border-white/15">
                  <div className="min-w-0 pr-1">
                    <div className="flex items-center gap-1.5 text-[10.5px] font-mono uppercase tracking-wider text-amber-300 font-bold">
                      <Sparkles className="w-3 h-3 shrink-0" />
                      <span className="truncate">40% OFF &bull; INDIA PLAN</span>
                    </div>
                    <div className="flex items-center gap-1 text-[11px] font-mono text-zinc-300 font-medium mt-0.5">
                      <Clock className="w-3 h-3 text-zinc-400 shrink-0" />
                      <span>{formatTimer(secondsLeft)} min Left</span>
                    </div>
                  </div>

                  {/* Primary Launch & Claim Action Pill */}
                  <Link
                    href={`/pricing?coupon=INDIA40&plan=india_only&tool=${toolId}`}
                    className="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-zinc-950/90 hover:bg-black text-white font-bold text-xs border border-white/25 shadow-lg backdrop-blur-md hover:scale-105 transition-all cursor-pointer shrink-0"
                  >
                    <span>{agentData.card1.ctaText}</span>
                    <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                  </Link>
                </div>

              </div>

            </div>

          </div>

        </div>

      </div>
    </div>
  );
}
