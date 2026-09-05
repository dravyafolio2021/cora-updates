'use client';

import React from 'react';
import Link from 'next/link';
import { 
  CheckCircle2, 
  ShieldCheck, 
  Clock, 
  ArrowRight, 
  Sparkles,
  FileCheck,
  Send,
  Zap
} from 'lucide-react';
import { ToolAgentData } from '@/lib/tools-agent-config';

export interface ToolOutcomeData {
  summaryTitle: string; // e.g. "3 Documents Merged Successfully"
  timeSavedEstimate: string; // e.g. "18 minutes of formatting saved"
  securityProof: string; // e.g. "0 bytes sent to external servers • 100% In-Browser RAM"
  suggestedNextStep: {
    headline: string; // e.g. "Sending this proposal to a client for e-sign & payment?"
    description: string; // e.g. "Turn this PDF into a court-admissible proposal with Section 10A signatures and collect 0% fee UPI advance."
    ctaLabel: string; // e.g. "Send for E-Sign & UPI Payment (Free)"
    ctaHref: string;
  };
}

interface ToolOutcomeRoiBannerProps {
  toolId: string;
  agentData: ToolAgentData;
  outcome: ToolOutcomeData;
  onDismiss?: () => void;
}

export function ToolOutcomeRoiBanner({
  toolId,
  agentData,
  outcome,
}: ToolOutcomeRoiBannerProps) {
  const firstName = agentData.agent.name.split(' ')[0];

  return (
    <div className="relative rounded-3xl overflow-hidden bg-white border border-zinc-200/90 shadow-[0_12px_40px_rgba(0,0,0,0.06)] p-5 sm:p-6 animate-in fade-in slide-in-from-top-3 duration-300">
      
      {/* Background Subtle Gradient Grid */}
      <div 
        aria-hidden="true"
        className="absolute top-0 right-0 w-80 h-full pointer-events-none opacity-40 bg-gradient-to-l from-zinc-100/80 via-transparent to-transparent"
      />

      <div className="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-5">
        
        {/* Left: Immediate Telemetry Proof & Outcome */}
        <div className="space-y-2.5 max-w-xl">
          <div className="flex flex-wrap items-center gap-2">
            <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 border border-emerald-200/70 text-emerald-800 text-xs font-bold font-mono">
              <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600" />
              <span>{outcome.summaryTitle}</span>
            </span>

            <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-zinc-100 border border-zinc-200 text-zinc-700 text-xs font-mono font-medium">
              <Clock className="w-3 h-3 text-zinc-500" />
              <span>{outcome.timeSavedEstimate}</span>
            </span>
          </div>

          <div className="space-y-1">
            <h3 className="text-base sm:text-lg font-bold text-zinc-950 tracking-tight flex items-center gap-2">
              <span>{outcome.suggestedNextStep.headline}</span>
            </h3>
            <p className="text-xs sm:text-sm text-zinc-600 leading-relaxed font-normal">
              {outcome.suggestedNextStep.description}
            </p>
          </div>

          {/* Local RAM Privacy Stamp */}
          <div className="flex items-center gap-1.5 text-[11px] font-mono text-zinc-500 pt-0.5">
            <ShieldCheck className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
            <span>{outcome.securityProof}</span>
          </div>
        </div>

        {/* Right: Peaceful TOFU Free Account Pitch */}
        <div className="w-full md:w-auto shrink-0 flex flex-col sm:flex-row md:flex-col gap-2.5 items-stretch md:items-end">
          <Link
            href={outcome.suggestedNextStep.ctaHref}
            className="px-5 py-3 rounded-2xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-md active:scale-[0.99] transition-all group/cta text-center"
          >
            <Sparkles className="w-4 h-4 text-zinc-300" />
            <span>{outcome.suggestedNextStep.ctaLabel}</span>
            <ArrowRight className="w-4 h-4 text-white group-hover/cta:translate-x-0.5 transition-transform" />
          </Link>

          <p className="text-[11px] font-mono text-zinc-400 text-center md:text-right">
            Free forever tier • Zero credit card required
          </p>
        </div>

      </div>

    </div>
  );
}
