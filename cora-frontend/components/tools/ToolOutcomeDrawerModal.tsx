'use client';

import React from 'react';
import Link from 'next/link';
import { 
  CheckCircle2, 
  ShieldCheck, 
  Clock, 
  ArrowRight, 
  Sparkles, 
  X,
  Share2,
  Lock,
  ArrowUpRight
} from 'lucide-react';
import { ToolAgentData } from '@/lib/tools-agent-config';

export interface ToolOutcomeModalData {
  summaryTitle: string; // e.g. "Compression Complete: 199.8 KB -> 175.8 KB (12% Saved)"
  timeSavedEstimate: string; // e.g. "~15 mins administrative turnaround time saved"
  securityProof: string; // e.g. "0 bytes sent to cloud servers • 100% In-Browser RAM"
  downloadFileName?: string;
  suggestedNextStep: {
    badge: string; // e.g. "Recommended Next Action"
    headline: string; // e.g. "Delivering this compressed file to a client?"
    description: string; // e.g. "Share via a branded, high-speed client portal with live open-tracking, feedback threads, and zero email attachment limits."
    ctaLabel: string; // e.g. "Create Free Client Portal with Rohan"
    ctaHref: string;
  };
}

interface ToolOutcomeDrawerModalProps {
  isOpen: boolean;
  onClose: () => void;
  toolId: string;
  agentData: ToolAgentData;
  outcome: ToolOutcomeModalData;
}

export function ToolOutcomeDrawerModal({
  isOpen,
  onClose,
  toolId,
  agentData,
  outcome,
}: ToolOutcomeDrawerModalProps) {
  if (!isOpen) return null;

  const firstName = agentData.agent.name.split(' ')[0];

  return (
    <div 
      className="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 transition-all duration-300"
      role="dialog"
      aria-modal="true"
    >
      {/* Blurred Backdrop Overlay */}
      <div 
        className="fixed inset-0 bg-zinc-950/60 backdrop-blur-md animate-in fade-in duration-200"
        onClick={onClose}
      />

      {/* Main Drawer Sheet (Bottom-up on Mobile, Centered Modal Card on Desktop) */}
      <div 
        className="relative z-10 w-full sm:max-w-lg bg-white rounded-t-[32px] sm:rounded-3xl border border-zinc-200 shadow-[0_24px_70px_rgba(0,0,0,0.25)] overflow-hidden animate-in slide-in-from-bottom-6 sm:zoom-in-95 duration-200 text-zinc-900"
      >
        {/* Mobile Drag Indicator Handle */}
        <div className="flex sm:hidden justify-center pt-3 pb-1">
          <div className="w-10 h-1 rounded-full bg-zinc-300" />
        </div>

        {/* Top Header Strip */}
        <div className="flex items-center justify-between px-6 pt-5 pb-3 border-b border-zinc-100">
          <div className="flex items-center gap-2">
            <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
            <span className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-600">
              Download Completed
            </span>
          </div>

          <button
            type="button"
            onClick={onClose}
            className="w-8 h-8 rounded-full bg-zinc-100 hover:bg-zinc-200 text-zinc-500 hover:text-zinc-900 flex items-center justify-center transition-colors cursor-pointer"
          >
            <X className="w-4 h-4" />
          </button>
        </div>

        <div className="p-6 space-y-6">
          
          {/* Telemetry Proof Box */}
          <div className="p-4 rounded-2xl bg-zinc-50 border border-zinc-200/80 space-y-2">
            <div className="flex items-center gap-2">
              <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0" />
              <h4 className="text-sm font-bold text-zinc-950">
                {outcome.summaryTitle}
              </h4>
            </div>

            <div className="flex flex-wrap items-center gap-3 text-[11px] font-mono text-zinc-500 pt-1 border-t border-zinc-200/50">
              <span className="inline-flex items-center gap-1 text-zinc-700 font-medium">
                <Clock className="w-3 h-3 text-zinc-500" />
                {outcome.timeSavedEstimate}
              </span>
              <span>&bull;</span>
              <span className="inline-flex items-center gap-1 text-emerald-700 font-medium">
                <ShieldCheck className="w-3 h-3 text-emerald-600" />
                {outcome.securityProof}
              </span>
            </div>
          </div>

          {/* AI-SDR Next Logical Action Pitch */}
          <div className="space-y-3">
            <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-zinc-900 text-white text-[10px] font-mono font-semibold uppercase tracking-wider">
              <Sparkles className="w-2.5 h-2.5 text-amber-300" />
              <span>{outcome.suggestedNextStep.badge}</span>
            </div>

            <div className="space-y-1.5">
              <h3 className="text-base sm:text-lg font-bold text-zinc-950 tracking-tight leading-snug">
                {outcome.suggestedNextStep.headline}
              </h3>
              <p className="text-xs sm:text-sm text-zinc-600 leading-relaxed">
                {outcome.suggestedNextStep.description}
              </p>
            </div>
          </div>

          {/* Action CTAs */}
          <div className="space-y-2.5 pt-2">
            <Link
              href={outcome.suggestedNextStep.ctaHref}
              onClick={onClose}
              className="w-full py-3.5 px-5 rounded-2xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-sm flex items-center justify-center gap-2 shadow-md active:scale-[0.99] transition-all group/btn text-center"
            >
              <span>{outcome.suggestedNextStep.ctaLabel}</span>
              <ArrowRight className="w-4 h-4 text-white group-hover/btn:translate-x-0.5 transition-transform" />
            </Link>

            <button
              type="button"
              onClick={onClose}
              className="w-full py-2.5 text-center text-xs font-semibold text-zinc-500 hover:text-zinc-800 transition-colors"
            >
              Stay on tool / Close
            </button>
          </div>

          {/* Trust Footnote */}
          <div className="pt-2 text-center text-[11px] font-mono text-zinc-400">
            Free forever account &bull; Zero credit card required &bull; 100% private
          </div>

        </div>

      </div>
    </div>
  );
}
