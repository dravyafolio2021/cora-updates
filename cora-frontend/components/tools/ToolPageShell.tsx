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
  Check, 
  ChevronDown, 
  FileText,
  Calculator,
  Code,
  Tag,
  ShieldCheck
} from 'lucide-react';
import { TOOL_AGENT_REGISTRY, ToolAgentData } from '@/lib/tools-agent-config';
import { ToolAiSdrHeroCard } from './ToolAiSdrHeroCard';
import { ToolOutcomeRoiBanner, ToolOutcomeData } from './ToolOutcomeRoiBanner';
import { ToolOutcomeDrawerModal, ToolOutcomeModalData } from './ToolOutcomeDrawerModal';

export interface ToolPageShellProps {
  toolId: string;
  badgeTag: string;
  title: string;
  subtitle: string;
  children: React.ReactNode;
  faqItems?: Array<{ question: string; answer: string }>;
  relatedToolSlugs?: string[];
  activeOutcome?: ToolOutcomeData | null;
  activeOutcomeModal?: ToolOutcomeModalData | null;
  onCloseOutcomeModal?: () => void;
}

export function ToolPageShell({
  toolId,
  badgeTag,
  title,
  subtitle,
  children,
  faqItems,
  relatedToolSlugs = [],
  activeOutcome = null,
  activeOutcomeModal = null,
  onCloseOutcomeModal,
}: ToolPageShellProps) {
  const [openFaqIndex, setOpenFaqIndex] = useState<number | null>(null);

  // Retrieve assigned AI co-founder agent persona
  const agentData: ToolAgentData = TOOL_AGENT_REGISTRY[toolId] || {
    slug: toolId,
    agent: {
      name: 'Kavya Patel',
      role: 'Operations & Legal AI Co-Founder',
      avatar: '/images/cora_agent_legal.jpg',
      status: 'Active in Workspace',
    },
    card1: {
      primaryText: 'Automate client agreements, digital milestone sign-offs, and invoices with zero busywork in your free workspace.',
      image: '/images/cora_esign_vault_3d.jpg',
      headline: 'Autonomous Studio Operations',
      description: 'Zero manual client follow-ups with legally binding digital deeds.',
      badge: 'Client Proofing Shield',
      ctaText: 'Claim Free Workspace',
    },
    card2: {
      title: 'Built as Open Digital Utility Infrastructure',
      description: '100% private in-browser tool execution. Run unlimited operations with zero server storage and zero subscription gates.',
      capabilities: [
        '100% In-Browser Local RAM Execution',
        'Zero Cloud Server Transmission or File Storage',
        'Instant Client-Side Processing Without Waiting Queues',
        'Free Forever Digital Infrastructure for Studios',
      ],
      ctaText: 'Claim Free Workspace →',
    },
  };

  // Strip emojis safely from badge
  const cleanBadgeTag = badgeTag.replace(/[\u{1F300}-\u{1FAFF}\u{2600}-\u{27BF}]/gu, '').trim();

  // Dynamic vector category identifier icon
  const getToolBadgeIcon = () => {
    if (toolId.includes('pdf') || toolId.includes('contract')) return <FileText className="w-3 h-3 text-zinc-700" />;
    if (toolId.includes('gst') || toolId.includes('retainer') || toolId.includes('upi')) return <Calculator className="w-3 h-3 text-zinc-700" />;
    if (toolId.includes('ai') || toolId.includes('listing')) return <Sparkles className="w-3 h-3 text-zinc-700" />;
    if (toolId.includes('embed')) return <Code className="w-3 h-3 text-zinc-700" />;
    return <Tag className="w-3 h-3 text-zinc-700" />;
  };

  const firstName = agentData.agent.name.split(' ')[0];

  return (
    <div className="relative w-full bg-[#FAFAF9] text-zinc-900 min-h-screen pt-[108px] sm:pt-[116px] pb-24 sm:pb-20 selection:bg-zinc-900 selection:text-white overflow-hidden">
      
      {/* ── Seamless Full-Width Background Pattern ── */}
      <div 
        aria-hidden="true"
        className="absolute top-0 inset-x-0 h-[480px] pointer-events-none opacity-[0.45]"
        style={{
          backgroundImage: `
            linear-gradient(to right, rgba(228, 228, 231, 0.7) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(228, 228, 231, 0.7) 1px, transparent 1px)
          `,
          backgroundSize: '40px 40px',
          maskImage: 'radial-gradient(ellipse 90% 70% at 50% 0%, #000 30%, transparent 85%)',
          WebkitMaskImage: 'radial-gradient(ellipse 90% 70% at 50% 0%, #000 30%, transparent 85%)',
        }}
      />

      <div className="relative z-10 w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        
        {/* ── Intro Section: Unboxed & Seamlessly Structured ── */}
        <div className="mb-8 sm:mb-10">
          
          {/* Back Navigation Breadcrumb */}
          <div className="mb-4 sm:mb-5">
            <Link
              href="/tools"
              className="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-500 hover:text-zinc-950 transition-colors group"
            >
              <ArrowLeft className="w-3.5 h-3.5 group-hover:-translate-x-0.5 transition-transform" />
              <span>Back to all micro-tools</span>
            </Link>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
            
            {/* Left Column: Category Badge, H1 Title, Subtitle, Trust Points */}
            <div className="lg:col-span-7 xl:col-span-8 space-y-3 pt-0.5">
              
              {/* Category Pill */}
              <div className="inline-flex items-center gap-1.5 text-[11px] font-mono font-bold text-zinc-800 px-3 py-1 bg-white/90 backdrop-blur-sm border border-zinc-200/90 rounded-full shadow-2xs">
                {getToolBadgeIcon()}
                <span>{cleanBadgeTag}</span>
              </div>

              {/* Main H1 Title */}
              <h1 className="font-display text-2xl sm:text-3xl lg:text-[38px] font-bold text-zinc-950 tracking-[-0.035em] leading-[1.14]">
                {title}
              </h1>

              {/* Subtitle */}
              <p className="text-zinc-600 text-xs sm:text-sm lg:text-[15px] font-normal leading-relaxed max-w-2xl">
                {subtitle}
              </p>

              {/* Trust & Privacy Micro-Badges */}
              <div className="flex flex-wrap items-center gap-2 pt-1">
                <span className="inline-flex items-center gap-1 text-[11px] font-mono font-medium text-zinc-600 bg-white/80 px-2.5 py-1 rounded-md border border-zinc-200/70 shadow-2xs">
                  <Lock className="w-3 h-3 text-emerald-600" />
                  100% Client-Side Private
                </span>
                <span className="inline-flex items-center gap-1 text-[11px] font-mono font-medium text-zinc-600 bg-white/80 px-2.5 py-1 rounded-md border border-zinc-200/70 shadow-2xs">
                  <Zap className="w-3 h-3 text-amber-500" />
                  Zero Server Upload
                </span>
                <span className="inline-flex items-center gap-1 text-[11px] font-mono font-medium text-zinc-600 bg-white/80 px-2.5 py-1 rounded-md border border-zinc-200/70 shadow-2xs">
                  <Check className="w-3 h-3 text-zinc-800" />
                  Free & Unlimited
                </span>
              </div>

            </div>

            {/* Right Column: AI-SDR Value-First Hero Card */}
            <div className="hidden lg:block lg:col-span-5 xl:col-span-4">
              <ToolAiSdrHeroCard toolId={toolId} agentData={agentData} />
            </div>

          </div>

        </div>

        {/* ── Full-Width Tool Engine Container ── */}
        <div className="w-full space-y-6 sm:space-y-8">
          
          {/* The Tool Engine */}
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

      {/* ── Active Outcome Drawer Modal (Triggers when user downloads outcome) ── */}
      {activeOutcomeModal && (
        <ToolOutcomeDrawerModal
          isOpen={true}
          onClose={onCloseOutcomeModal || (() => {})}
          toolId={toolId}
          agentData={agentData}
          outcome={activeOutcomeModal}
        />
      )}

      {/* ══════════════════════════════════════════════════════════════
          MOBILE PEACEFUL VALUE BAR (Non-Intrusive)
          ══════════════════════════════════════════════════════════════ */}
      <aside
        aria-label="Cora AI Co-Founder Free Guarantee"
        className="fixed bottom-3 inset-x-3 z-40 lg:hidden animate-in fade-in slide-in-from-bottom-5 duration-300"
      >
        <div className="relative overflow-hidden rounded-2xl bg-zinc-950/95 backdrop-blur-xl border border-zinc-800 p-3 shadow-[0_12px_40px_rgba(0,0,0,0.35)] text-white">
          <div className="flex items-center justify-between gap-3">
            
            <div className="flex items-center gap-2.5 min-w-0 flex-1">
              <div className="relative w-8 h-8 rounded-xl overflow-hidden shrink-0 border border-white/15 bg-zinc-900 flex items-center justify-center text-emerald-400 font-mono text-xs">
                <ShieldCheck className="w-4 h-4 text-emerald-400" />
              </div>

              <div className="min-w-0 flex-1">
                <div className="text-xs font-bold text-white tracking-tight truncate">
                  Free Client-Side Tool
                </div>
                <div className="text-[10px] text-zinc-400 truncate font-mono">
                  100% In-Browser RAM • Zero Sign-Up
                </div>
              </div>
            </div>

            <div className="flex items-center shrink-0">
              <Link
                href={`/workspace/login?mode=signup&ref=tofu_mobile_${toolId}`}
                className="px-3.5 py-1.5 rounded-xl bg-white hover:bg-zinc-100 text-zinc-950 font-bold text-xs flex items-center gap-1 shadow-md active:scale-95 transition-all cursor-pointer"
              >
                <span>Free Workspace</span>
                <ArrowRight className="w-3 h-3 text-zinc-950" />
              </Link>
            </div>

          </div>
        </div>
      </aside>

    </div>
  );
}
