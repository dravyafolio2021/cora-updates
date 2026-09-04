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
  Clock,
  Flame
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
            
            {/* ── Prominent & Confident 3D Product Ad Card ── */}
            <div className="rounded-[28px] overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.12)] border border-zinc-800 bg-zinc-950 flex flex-col group transition-all">
              
              {/* Top: 100% Bright & Unobstructed 3D Artwork Stage */}
              <div className="relative w-full aspect-[4/3] bg-zinc-950 overflow-hidden">
                <Image
                  src={agentData.card1.image}
                  alt={agentData.card1.headline}
                  fill
                  priority
                  className="object-cover object-center group-hover:scale-105 transition-transform duration-700"
                  sizes="(max-width: 768px) 100vw, 420px"
                />

                {/* Subtle top rim vignette for badge contrast */}
                <div className="absolute inset-x-0 top-0 h-16 bg-gradient-to-b from-black/60 to-transparent pointer-events-none" />

                {/* Top Right Frosted Feature Pill */}
                <div className="absolute top-3.5 right-3.5 z-10">
                  <span className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-zinc-950/85 backdrop-blur-md text-white text-[11px] font-mono font-bold tracking-wide border border-white/20 shadow-lg">
                    <span>⚡</span>
                    <span>{agentData.card1.badge.replace(/^⚡\s*/, '')}</span>
                  </span>
                </div>

                {/* Top Left Agent Co-Founder Pill */}
                <div className="absolute top-3.5 left-3.5 z-10">
                  <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-zinc-950/85 backdrop-blur-md text-zinc-300 text-[11px] font-medium border border-white/15 shadow-sm">
                    <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse" />
                    <span>{agentData.agent.name}</span>
                  </span>
                </div>
              </div>

              {/* Bottom: Sleek High-Contrast Offer & Action Panel */}
              <div className="p-5 sm:p-5.5 bg-zinc-950 text-white flex flex-col gap-3.5 border-t border-zinc-800/80">
                
                {/* 1-Liner Headline + Share Button */}
                <div className="flex items-center justify-between gap-2.5">
                  <h3 className="font-display text-base sm:text-lg font-bold text-white tracking-tight leading-snug truncate">
                    {agentData.card1.headline}
                  </h3>

                  {/* Share Tool Icon Button */}
                  <button
                    type="button"
                    onClick={handleShareTool}
                    title="Share this tool"
                    className="p-2 rounded-xl bg-white/10 hover:bg-white text-zinc-300 hover:text-zinc-950 transition-all cursor-pointer backdrop-blur-md border border-white/15 shadow-sm shrink-0 hover:scale-105"
                  >
                    {copiedShare ? (
                      <Check className="w-3.5 h-3.5 text-emerald-400" />
                    ) : (
                      <Share2 className="w-3.5 h-3.5" />
                    )}
                  </button>
                </div>

                {/* 2-Liner Description */}
                <p className="text-xs text-zinc-400 font-normal leading-relaxed line-clamp-2">
                  {agentData.card1.primaryText}
                </p>

                {/* ── HIGH-CONTRAST 40% DISCOUNT & COUNTDOWN TIMER RIBBON ── */}
                <div className="p-3 rounded-2xl bg-amber-500/10 border border-amber-400/40 flex items-center justify-between gap-2">
                  <div className="flex items-center gap-2 min-w-0">
                    <Flame className="w-4 h-4 text-amber-400 shrink-0 fill-amber-400 animate-pulse" />
                    <div className="truncate">
                      <span className="text-xs font-mono font-extrabold text-amber-300 tracking-wide block leading-tight">
                        40% FLASH OFF: ₹299/mo
                      </span>
                      <span className="text-[10.5px] text-zinc-400 font-mono">
                        Reg. ₹499 &bull; Save ₹2,400/yr
                      </span>
                    </div>
                  </div>
                  <div className="flex items-center gap-1.5 text-xs font-mono font-extrabold text-zinc-950 bg-amber-400 px-2.5 py-1 rounded-xl shadow-md shrink-0">
                    <Clock className="w-3.5 h-3.5 text-zinc-950 shrink-0" />
                    <span>{formatTimer(secondsLeft)}</span>
                  </div>
                </div>

                {/* Full-Width High-Contrast Primary Action Button */}
                <Link
                  href={`/pricing?coupon=INDIA40&plan=india_only&tool=${toolId}`}
                  className="w-full py-3.5 px-4 rounded-2xl bg-white hover:bg-zinc-100 text-zinc-950 font-extrabold text-xs flex items-center justify-center gap-2 shadow-xl hover:scale-[1.01] active:scale-[0.99] transition-all cursor-pointer"
                >
                  <span>Claim 40% Off with {agentData.agent.name.split(' ')[0]}</span>
                  <ArrowRight className="w-3.5 h-3.5 text-zinc-950" />
                </Link>

              </div>

            </div>

          </div>

        </div>

      </div>
    </div>
  );
}
