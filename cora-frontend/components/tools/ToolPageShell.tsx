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
  ShieldCheck,
  ExternalLink 
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
    <div className="w-full bg-[#FAFAF9] text-zinc-900 min-h-screen py-8 sm:py-14 selection:bg-zinc-900 selection:text-white">
      <div className="w-full max-w-[1440px] mx-auto px-3 sm:px-6">
        
        {/* ── Top Header Bar (Back Navigation + Title Lock) ── */}
        <div className="max-w-4xl mx-auto text-center mb-8 sm:mb-12">
          <div className="flex items-center justify-center gap-3 mb-3">
            <Link
              href="/tools"
              className="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-500 hover:text-zinc-950 transition-colors"
            >
              <ArrowLeft className="w-3.5 h-3.5" />
              <span>All micro-tools</span>
            </Link>
            <span className="text-zinc-300">•</span>
            <div className="inline-flex items-center gap-1 text-[11px] font-mono font-bold text-zinc-800 px-2.5 py-0.5 bg-white border border-zinc-200/80 rounded-full shadow-2xs">
              <span>{badgeTag}</span>
            </div>
          </div>

          <h1 className="font-display text-2xl sm:text-4xl lg:text-5xl font-extrabold text-zinc-950 tracking-[-0.035em] leading-[1.18] mb-2.5">
            {title}
          </h1>
          <p className="text-zinc-600 text-xs sm:text-base font-normal leading-relaxed max-w-2xl mx-auto">
            {subtitle}
          </p>
        </div>

        {/* ══════════════════════════════════════════════════════════════
            3-COLUMN EXPERIMENTAL LAYOUT: [10% LEFT AD | 80% CENTER TOOL | 10% RIGHT AD]
            ══════════════════════════════════════════════════════════════ */}
        <div className="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
          
          {/* ──────────────────────────────────────────────────────────
              LEFT COLUMN (10-15% Skyscraper Ad: AI Agent Co-Founder)
              ────────────────────────────────────────────────────────── */}
          <aside className="hidden xl:block xl:col-span-2 xl:sticky xl:top-24 space-y-4">
            <div className="rounded-2xl bg-white border border-zinc-200/90 shadow-[0_8px_30px_rgba(0,0,0,0.04)] overflow-hidden transition-all hover:shadow-[0_12px_40px_rgba(0,0,0,0.08)]">
              
              {/* Agent Header */}
              <div className="p-3 border-b border-zinc-100 bg-zinc-50/50 flex items-center justify-between">
                <div className="flex items-center gap-2 min-w-0">
                  <div className="relative w-8 h-8 rounded-full overflow-hidden border border-zinc-200 shadow-2xs shrink-0">
                    <Image
                      src={agentData.agent.avatar}
                      alt={agentData.agent.name}
                      fill
                      className="object-cover object-top"
                      sizes="32px"
                    />
                    <span className="absolute bottom-0 right-0 w-2 h-2 rounded-full bg-emerald-500 ring-2 ring-white" />
                  </div>
                  <div className="min-w-0">
                    <div className="text-[11.5px] font-bold text-zinc-950 truncate leading-tight">
                      {agentData.agent.name}
                    </div>
                    <div className="text-[9.5px] text-zinc-500 truncate font-medium">
                      {agentData.agent.role}
                    </div>
                  </div>
                </div>

                <button
                  type="button"
                  onClick={handleShareTool}
                  title="Share tool"
                  className="p-1 rounded text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer"
                >
                  {copiedShare ? <Check className="w-3.5 h-3.5 text-emerald-600" /> : <Share2 className="w-3.5 h-3.5" />}
                </button>
              </div>

              {/* Hook Text */}
              <div className="p-3">
                <p className="text-[11px] text-zinc-700 leading-relaxed font-normal">
                  {agentData.card1.primaryText}
                </p>
              </div>

              {/* 3D Visual */}
              <div className="relative w-full h-32 bg-zinc-950 overflow-hidden">
                <Image
                  src={agentData.card1.image}
                  alt={agentData.card1.headline}
                  fill
                  className="object-cover object-center"
                  sizes="220px"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent" />
                <div className="absolute bottom-2 left-2.5 right-2.5">
                  <h4 className="text-[11px] font-bold text-white leading-tight">
                    {agentData.card1.headline}
                  </h4>
                </div>
              </div>

              {/* CTA Action */}
              <div className="p-3 bg-zinc-50 border-t border-zinc-100">
                <a
                  href="http://cora.local/workspace/login?utm_source=left_skyscraper"
                  className="w-full flex items-center justify-center gap-1 py-2 px-2.5 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-[11px] transition-all shadow-xs cursor-pointer text-center"
                >
                  <span>{agentData.card1.ctaText}</span>
                  <ArrowRight className="w-3 h-3 text-zinc-400" />
                </a>
              </div>

            </div>
          </aside>

          {/* ──────────────────────────────────────────────────────────
              CENTER COLUMN (70-80% Main Stage: Interactive Tool Engine)
              ────────────────────────────────────────────────────────── */}
          <main className="col-span-1 xl:col-span-8 space-y-6 sm:space-y-8">
            
            {/* The Actual Tool Engine */}
            <div className="w-full">
              {children}
            </div>

            {/* Privacy & Trust Bar */}
            <div className="p-3.5 sm:p-4 rounded-2xl bg-white border border-zinc-200/80 flex flex-wrap items-center justify-between gap-3 text-xs text-zinc-600 shadow-2xs">
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

            {/* Tablet & Mobile Fallback: Bottom 2-Col Showcase Grid */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4 xl:hidden pt-4 border-t border-zinc-200/60">
              {/* Card 1 Mobile */}
              <div className="rounded-2xl bg-white border border-zinc-200/90 p-4 shadow-xs space-y-3">
                <div className="flex items-center gap-2">
                  <div className="relative w-8 h-8 rounded-full overflow-hidden border border-zinc-200">
                    <Image src={agentData.agent.avatar} alt={agentData.agent.name} fill className="object-cover" sizes="32px" />
                  </div>
                  <div>
                    <div className="text-xs font-bold text-zinc-950">{agentData.agent.name}</div>
                    <div className="text-[10px] text-zinc-500">{agentData.agent.role}</div>
                  </div>
                </div>
                <p className="text-xs text-zinc-700 leading-relaxed">{agentData.card1.primaryText}</p>
                <a
                  href="http://cora.local/workspace/login"
                  className="w-full py-2 rounded-xl bg-zinc-950 text-white font-bold text-xs flex items-center justify-center gap-1.5"
                >
                  <span>{agentData.card1.ctaText}</span>
                  <ArrowRight className="w-3.5 h-3.5" />
                </a>
              </div>

              {/* Card 2 Mobile */}
              <div className="rounded-2xl bg-white border border-zinc-200/90 p-4 shadow-xs space-y-3">
                <div className="inline-flex items-center gap-1 text-[10px] font-mono font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full">
                  <Sparkles className="w-3 h-3 text-indigo-600" />
                  <span>Cora Autonomous OS</span>
                </div>
                <h4 className="text-xs font-bold text-zinc-950">{agentData.card2.title}</h4>
                <div className="space-y-1 text-[11px] text-zinc-700">
                  {agentData.card2.capabilities.slice(0, 3).map((c, i) => (
                    <div key={i} className="flex items-center gap-1.5">
                      <CheckCircle2 className="w-3 h-3 text-emerald-600 shrink-0" />
                      <span className="truncate">{c}</span>
                    </div>
                  ))}
                </div>
                <a
                  href="http://cora.local/workspace/login"
                  className="w-full py-2 rounded-xl bg-zinc-950 text-white font-bold text-xs flex items-center justify-center gap-1.5"
                >
                  <span>{agentData.card2.ctaText}</span>
                  <ArrowRight className="w-3.5 h-3.5" />
                </a>
              </div>
            </div>

          </main>

          {/* ──────────────────────────────────────────────────────────
              RIGHT COLUMN (10-15% Skyscraper Ad: Autonomous OS Ecosystem)
              ────────────────────────────────────────────────────────── */}
          <aside className="hidden xl:block xl:col-span-2 xl:sticky xl:top-24 space-y-4">
            <div className="rounded-2xl bg-white border border-zinc-200/90 p-3.5 shadow-[0_8px_30px_rgba(0,0,0,0.04)] space-y-3 transition-all hover:shadow-[0_12px_40px_rgba(0,0,0,0.08)]">
              
              {/* Header Badge */}
              <div className="flex items-center justify-between">
                <div className="inline-flex items-center gap-1 text-[9.5px] font-mono font-bold text-indigo-700 bg-indigo-50 border border-indigo-200/80 px-2 py-0.5 rounded-full">
                  <Sparkles className="w-2.5 h-2.5 text-indigo-600" />
                  <span>Cora OS</span>
                </div>
                <span className="text-[9.5px] font-mono text-emerald-700 font-bold">
                  Free Forever
                </span>
              </div>

              {/* Title & Description */}
              <div>
                <h4 className="text-xs font-bold text-zinc-950 leading-tight">
                  {agentData.card2.title}
                </h4>
                <p className="text-[10.5px] text-zinc-600 font-normal leading-relaxed mt-1">
                  {agentData.card2.description}
                </p>
              </div>

              {/* Dynamic Capabilities List */}
              <div className="space-y-1.5 pt-2 border-t border-zinc-100 text-[10.5px]">
                {agentData.card2.capabilities.map((cap, idx) => (
                  <div key={idx} className="flex items-start gap-1.5 text-zinc-800 font-medium">
                    <CheckCircle2 className="w-3 h-3 text-emerald-600 shrink-0 mt-0.5" />
                    <span className="leading-tight">{cap}</span>
                  </div>
                ))}
              </div>

              {/* High-Converting Primary CTA Button */}
              <div className="pt-2 border-t border-zinc-100">
                <a
                  href="http://cora.local/workspace/login?utm_source=right_skyscraper"
                  className="w-full flex items-center justify-center gap-1 py-2 px-2.5 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-[11px] transition-all shadow-xs cursor-pointer text-center"
                >
                  <span>{agentData.card2.ctaText}</span>
                  <ArrowRight className="w-3 h-3 text-zinc-400" />
                </a>
                <span className="block text-center text-[9.5px] text-zinc-400 mt-1 font-medium">
                  Zero Card • Setup in 90s
                </span>
              </div>

            </div>
          </aside>

        </div>

      </div>
    </div>
  );
}
