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
    <div className="w-full bg-[#FAFAF9] text-zinc-900 min-h-screen pt-[124px] sm:pt-32 pb-24 sm:pb-20 selection:bg-zinc-900 selection:text-white">
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

        {/* ── Intro Section: Left Title/Subtitle + Right 3D Showcase Card ── */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-center mb-8 sm:mb-12">
          
          {/* Left: Title, Badge, Description */}
          <div className="lg:col-span-7 xl:col-span-8 space-y-3">
            <div className="inline-flex items-center gap-1.5 text-[11px] font-mono font-bold text-zinc-800 px-3 py-1 bg-white border border-zinc-200/80 rounded-full shadow-2xs">
              <span>{badgeTag}</span>
            </div>
            <h1 className="font-display text-2xl sm:text-4xl lg:text-5xl font-semibold text-zinc-950 tracking-[-0.035em] leading-[1.16]">
              {title}
            </h1>
            <p className="text-zinc-600 text-xs sm:text-base font-normal leading-relaxed max-w-2xl">
              {subtitle}
            </p>
          </div>

          {/* Right: Immersive 3D Showcase Card (Desktop Only, nestled in Intro Header) */}
          <div className="hidden lg:block lg:col-span-5 xl:col-span-4">
            <div className="relative rounded-3xl overflow-hidden shadow-[0_16px_40px_rgba(0,0,0,0.16)] border border-zinc-800/90 bg-zinc-950 min-h-[300px] flex flex-col justify-between p-5 text-white group">
              
              {/* Full-Bleed 3D Background Artwork */}
              <Image
                src={agentData.card1.image}
                alt={agentData.card1.headline}
                fill
                priority
                className="object-cover object-top group-hover:scale-105 transition-transform duration-700"
                sizes="(max-width: 768px) 100vw, 420px"
              />

              {/* Top soft vignette for badge contrast */}
              <div className="absolute inset-x-0 top-0 h-20 bg-gradient-to-b from-black/80 via-black/30 to-transparent pointer-events-none" />

              {/* Silky smooth bottom gradient melt */}
              <div className="absolute inset-x-0 bottom-0 h-[72%] bg-gradient-to-t from-zinc-950 via-zinc-950/95 via-50% to-transparent pointer-events-none" />

              {/* Top Row: Agent Identity & Feature Pill */}
              <div className="relative z-10 flex items-center justify-between gap-2">
                <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-black/60 backdrop-blur-md text-zinc-200 text-[11px] font-medium border border-white/15 shadow-sm">
                  <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse" />
                  <span>{agentData.agent.name}</span>
                </span>

                <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-black/60 backdrop-blur-md text-white text-[10.5px] font-mono font-bold tracking-wide border border-white/20 shadow-md">
                  <span>⚡</span>
                  <span>{agentData.card1.badge.replace(/^⚡\s*/, '')}</span>
                </span>
              </div>

              {/* Bottom Content Area */}
              <div className="relative z-10 space-y-2.5 mt-auto pt-12">
                <div className="flex items-center justify-between gap-2">
                  <h3 className="font-display text-base font-bold text-white tracking-tight leading-snug drop-shadow-sm truncate">
                    {agentData.card1.headline}
                  </h3>

                  <button
                    type="button"
                    onClick={handleShareTool}
                    title="Share this tool"
                    className="p-1.5 rounded-lg bg-white/15 hover:bg-white text-white hover:text-zinc-950 transition-all cursor-pointer backdrop-blur-md border border-white/20 shadow-sm shrink-0 hover:scale-105"
                  >
                    {copiedShare ? (
                      <Check className="w-3.5 h-3.5 text-emerald-400" />
                    ) : (
                      <Share2 className="w-3.5 h-3.5" />
                    )}
                  </button>
                </div>

                <p className="text-[11.5px] text-zinc-300 font-normal leading-relaxed line-clamp-1 drop-shadow-xs">
                  {agentData.card1.primaryText}
                </p>

                {/* 40% Flash Ribbon */}
                <div className="p-2 rounded-xl bg-amber-400/15 border border-amber-400/40 backdrop-blur-md flex items-center justify-between gap-2">
                  <div className="flex items-center gap-1.5 min-w-0">
                    <Flame className="w-3.5 h-3.5 text-amber-400 shrink-0 fill-amber-400 animate-pulse" />
                    <span className="text-[11px] font-mono font-extrabold text-amber-300 tracking-wide truncate">
                      40% OFF: ₹299/mo
                    </span>
                  </div>
                  <div className="flex items-center gap-1 text-[11px] font-mono font-extrabold text-zinc-950 bg-amber-400 px-2 py-0.5 rounded-lg shadow-xs shrink-0">
                    <Clock className="w-3 h-3 text-zinc-950 shrink-0" />
                    <span>{formatTimer(secondsLeft)}</span>
                  </div>
                </div>

                {/* CTA Button */}
                <Link
                  href={`/pricing?coupon=INDIA40&plan=india_only&tool=${toolId}`}
                  className="w-full py-2.5 px-3 rounded-xl bg-white hover:bg-zinc-100 text-zinc-950 font-bold text-xs flex items-center justify-center gap-1.5 shadow-lg hover:scale-[1.01] active:scale-[0.99] transition-all cursor-pointer"
                >
                  <span>Claim with {agentData.agent.name.split(' ')[0]}</span>
                  <ArrowRight className="w-3.5 h-3.5 text-zinc-950" />
                </Link>
              </div>

            </div>
          </div>

        </div>

        {/* ── Full-Width Tool Engine Container ── */}
        <div className="w-full space-y-6 sm:space-y-8">
          
          {/* The Tool Engine (Now 100% Full Width across the page) */}
          <div className="w-full">
            {children}
          </div>

          {/* Privacy & Trust Bar */}
          <div className="p-4 rounded-2xl bg-white border border-zinc-200/80 flex flex-wrap items-center justify-between gap-3 text-xs text-zinc-600 shadow-2xs">
            <div className="flex items-center gap-2">
              <Lock className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
              <span className="font-medium">100% Client-Side Execution (Zero file or financial data transmitted or stored)</span>
            </div>
            <div className="flex items-center gap-2 font-mono text-[11px] text-zinc-500">
              <Zap className="w-3 h-3 text-amber-500 shrink-0" />
              <span>Zero Login Required</span>
            </div>
          </div>

          {/* Optional FAQ Accordion for the Tool */}
          {faqItems && faqItems.length > 0 && (
            <div className="pt-8 border-t border-zinc-200/60 space-y-3">
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

      </div>

      {/* ══════════════════════════════════════════════════════════════
          MOBILE FLOATING STICKY AD BLOCK (Custom Small Floating Card)
          ══════════════════════════════════════════════════════════════ */}
      <aside
        aria-label="Cora AI Co-Founder Offer"
        className="fixed bottom-3 inset-x-3 z-40 lg:hidden animate-in fade-in slide-in-from-bottom-5 duration-300"
      >
        <div className="relative overflow-hidden rounded-2xl bg-zinc-950/95 backdrop-blur-xl border border-zinc-800 p-3 shadow-[0_12px_40px_rgba(0,0,0,0.35)] text-white">
          
          {/* Subtle top ambient glow */}
          <div className="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-transparent via-amber-400 to-transparent" />

          <div className="flex items-center justify-between gap-3">
            
            {/* Left: Thumbnail & Offer Text */}
            <div className="flex items-center gap-2.5 min-w-0 flex-1">
              <div className="relative w-10 h-10 rounded-xl overflow-hidden shrink-0 border border-white/15 bg-zinc-900">
                <Image
                  src={agentData.card1.image}
                  alt={agentData.card1.headline}
                  fill
                  className="object-cover"
                  sizes="40px"
                />
                <span className="absolute bottom-0.5 right-0.5 w-2 h-2 rounded-full bg-emerald-400 ring-2 ring-zinc-950" />
              </div>

              <div className="min-w-0 flex-1">
                <div className="flex items-center gap-1.5">
                  <span className="text-xs font-bold text-white tracking-tight truncate">
                    {agentData.agent.name.split(' ')[0]} AI Co-Founder
                  </span>
                  <span className="text-[9.5px] font-mono font-extrabold text-amber-300 bg-amber-400/20 px-1.5 py-0.5 rounded border border-amber-400/30 shrink-0">
                    40% OFF
                  </span>
                </div>
                <div className="text-[11px] text-zinc-400 truncate flex items-center gap-1 font-mono">
                  <span className="text-white font-bold">₹299/mo</span>
                  <span>&bull;</span>
                  <span className="text-amber-400 font-semibold">{formatTimer(secondsLeft)}</span>
                </div>
              </div>
            </div>

            {/* Right: 1-Tap CTA */}
            <div className="flex items-center shrink-0">
              <Link
                href={`/pricing?coupon=INDIA40&plan=india_only&tool=${toolId}`}
                className="px-4 py-2 rounded-xl bg-white hover:bg-zinc-100 text-zinc-950 font-bold text-xs flex items-center gap-1.5 shadow-md active:scale-95 transition-all cursor-pointer"
              >
                <span>Claim</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-950" />
              </Link>
            </div>

          </div>

        </div>
      </aside>

    </div>
  );
}
