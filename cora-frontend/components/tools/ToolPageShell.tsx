'use client';

import React, { useState } from 'react';
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
  CheckCircle2, 
  ShieldCheck 
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

        {/* ── Master 2-Column Split Layout (Main Tool + Dynamic Right Showcase) ── */}
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
              RIGHT SECTION (DYNAMIC AI AGENT & ECOSYSTEM SHOWCASE CARDS)
              ══════════════════════════════════════════════════════════════ */}
          <div className="lg:col-span-4 lg:sticky lg:top-24 space-y-5">
            
            {/* ──────────────────────────────────────────────────────────
                CARD 1: DYNAMIC AI AGENT SHOWCASE CARD
                ────────────────────────────────────────────────────────── */}
            <div className="rounded-2xl bg-white border border-zinc-200/90 shadow-[0_8px_30px_rgba(0,0,0,0.04)] overflow-hidden transition-all hover:shadow-[0_12px_40px_rgba(0,0,0,0.08)]">
              
              {/* Dynamic AI Agent Header */}
              <div className="p-3.5 sm:p-4 flex items-center justify-between border-b border-zinc-100 bg-zinc-50/50">
                <div className="flex items-center gap-2.5">
                  {/* Agent Avatar with Live Status Pulse */}
                  <div className="relative w-9 h-9 rounded-full overflow-hidden border border-zinc-200 shadow-2xs shrink-0">
                    <Image
                      src={agentData.agent.avatar}
                      alt={agentData.agent.name}
                      fill
                      className="object-cover object-top"
                      sizes="36px"
                    />
                    <span className="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-white" />
                  </div>
                  <div>
                    <div className="flex items-center gap-1.5">
                      <span className="text-xs font-bold text-zinc-950 leading-tight">
                        {agentData.agent.name}
                      </span>
                      <span className="px-1.5 py-0.2 rounded bg-indigo-50 text-indigo-700 text-[9.5px] font-mono font-bold">
                        AI
                      </span>
                    </div>
                    <div className="text-[10.5px] text-zinc-500 font-medium leading-tight">
                      {agentData.agent.role}
                    </div>
                  </div>
                </div>

                {/* Interactive Share Tool Button */}
                <button
                  type="button"
                  onClick={handleShareTool}
                  title="Share this tool"
                  className="p-1.5 rounded-lg text-zinc-400 hover:text-zinc-900 hover:bg-zinc-100 transition-colors cursor-pointer"
                >
                  {copiedShare ? (
                    <Check className="w-4 h-4 text-emerald-600" />
                  ) : (
                    <Share2 className="w-4 h-4" />
                  )}
                </button>
              </div>

              {/* Primary Transformation Copy */}
              <div className="px-3.5 sm:px-4 pt-3.5 pb-2.5">
                <p className="text-xs text-zinc-800 font-normal leading-relaxed">
                  {agentData.card1.primaryText}
                </p>
              </div>

              {/* Dynamic 3D Feature Visual */}
              <div className="relative w-full h-44 sm:h-48 bg-zinc-950 overflow-hidden">
                <Image
                  src={agentData.card1.image}
                  alt={agentData.card1.headline}
                  fill
                  className="object-cover object-center hover:scale-105 transition-transform duration-500"
                  sizes="(max-width: 768px) 100vw, 400px"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent" />
                
                {/* Dynamic Feature Badge */}
                <div className="absolute top-2.5 right-2.5">
                  <span className="px-2.5 py-1 rounded-full bg-zinc-950/85 backdrop-blur-md text-white text-[10px] font-mono font-bold tracking-wide border border-white/20 shadow-xs">
                    {agentData.card1.badge}
                  </span>
                </div>

                {/* Bottom Headline Overlay */}
                <div className="absolute bottom-2.5 left-3.5 right-3.5">
                  <h4 className="text-xs font-bold text-white leading-snug drop-shadow-sm">
                    {agentData.card1.headline}
                  </h4>
                </div>
              </div>

              {/* Action Bar */}
              <div className="p-3.5 sm:p-4 bg-zinc-50 border-t border-zinc-100 flex items-center justify-between gap-3">
                <div className="min-w-0 pr-1">
                  <span className="block text-[10px] font-mono text-zinc-400 uppercase tracking-wider truncate">
                    heycora.in/workspace
                  </span>
                  <p className="text-[11px] text-zinc-600 truncate font-normal">
                    {agentData.card1.description}
                  </p>
                </div>
                <a
                  href="http://cora.local/workspace/login?utm_source=tool_agent_unit"
                  className="shrink-0 px-3.5 py-2 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs transition-all shadow-xs flex items-center gap-1.5 cursor-pointer"
                >
                  <span>{agentData.card1.ctaText}</span>
                  <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                </a>
              </div>

            </div>

            {/* ──────────────────────────────────────────────────────────
                CARD 2: AUTONOMOUS ECOSYSTEM & CAPABILITY SHOWCASE
                ────────────────────────────────────────────────────────── */}
            <div className="rounded-2xl bg-white border border-zinc-200/90 p-4 sm:p-5 shadow-[0_8px_30px_rgba(0,0,0,0.04)] space-y-3.5 transition-all hover:shadow-[0_12px_40px_rgba(0,0,0,0.08)]">
              
              {/* Category Header */}
              <div className="flex items-center justify-between">
                <div className="inline-flex items-center gap-1.5 text-[10.5px] font-mono font-bold text-indigo-700 bg-indigo-50 border border-indigo-200/80 px-2.5 py-0.5 rounded-full">
                  <Sparkles className="w-3 h-3 text-indigo-600" />
                  <span>Cora Autonomous OS</span>
                </div>
                <span className="text-[10.5px] font-mono text-emerald-700 font-bold">
                  100% Free Tier
                </span>
              </div>

              {/* Title & Description */}
              <div>
                <h4 className="text-sm font-bold text-zinc-950 leading-snug">
                  {agentData.card2.title}
                </h4>
                <p className="text-xs text-zinc-600 font-normal leading-relaxed mt-1">
                  {agentData.card2.description}
                </p>
              </div>

              {/* 4 Dynamic Capabilities */}
              <div className="space-y-2 pt-2 border-t border-zinc-100 text-xs">
                {agentData.card2.capabilities.map((cap, idx) => (
                  <div key={idx} className="flex items-center gap-2 text-zinc-800 font-medium">
                    <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                    <span className="truncate">{cap}</span>
                  </div>
                ))}
              </div>

              {/* Primary Signup Action */}
              <div className="pt-2">
                <a
                  href="http://cora.local/workspace/login?utm_source=tool_ecosystem_cta"
                  className="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs transition-all shadow-xs cursor-pointer"
                >
                  <span>{agentData.card2.ctaText}</span>
                  <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                </a>
                <span className="block text-center text-[10px] text-zinc-400 mt-1.5 font-medium">
                  100% Free Forever • Zero Credit Card • Setup in 90s
                </span>
              </div>

            </div>

          </div>

        </div>

      </div>
    </div>
  );
}
