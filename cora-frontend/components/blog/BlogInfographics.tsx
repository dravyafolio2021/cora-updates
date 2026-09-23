'use client';

import React from 'react';
import { ArrowDown, Check, ArrowRight, ShieldAlert, FileText, MessageSquare, AlertCircle, Database, Layers } from 'lucide-react';

interface InfographicProps {
  infographicId: 'onboarding-friction' | 'scope-creep-waterfall' | 'reporting-cycle';
  headline: string;
  explanation?: string;
  source?: string;
}

export function BlogInfographics({ infographicId, headline, explanation, source }: InfographicProps) {
  return (
    <figure className="my-10 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-[#FBFaf7] dark:bg-zinc-900/60 p-5 sm:p-7 shadow-sm overflow-hidden">
      <div className="mb-5 border-b border-zinc-200/80 dark:border-zinc-800 pb-3">
        <div className="text-[10px] font-mono font-bold uppercase tracking-widest text-zinc-500">
          CORA EDITORIAL INFOGRAPHIC
        </div>
        <h4 className="mt-1 font-display text-base sm:text-lg font-bold text-zinc-950 dark:text-zinc-50">
          {headline}
        </h4>
        {explanation && (
          <p className="mt-1 text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
            {explanation}
          </p>
        )}
      </div>

      <div className="py-2">
        {infographicId === 'onboarding-friction' && <OnboardingFrictionGraphic />}
        {infographicId === 'scope-creep-waterfall' && <ScopeCreepGraphic />}
        {infographicId === 'reporting-cycle' && <ReportingCycleGraphic />}
      </div>

      {source && (
        <figcaption className="mt-4 pt-3 border-t border-zinc-200/60 dark:border-zinc-800/80 text-[11px] font-mono text-zinc-600 dark:text-zinc-400 flex items-center gap-1.5">
          <span className="font-semibold text-zinc-700 dark:text-zinc-300">SOURCE:</span> {source}
        </figcaption>
      )}
    </figure>
  );
}

function OnboardingFrictionGraphic() {
  return (
    <div className="grid gap-3 sm:grid-cols-4 items-stretch text-xs">
      <div className="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 p-3.5 flex flex-col justify-between">
        <div>
          <div className="inline-flex p-1.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 mb-2">
            <FileText className="w-4 h-4" />
          </div>
          <div className="font-bold text-zinc-900 dark:text-zinc-100">01. Verbal Close</div>
          <p className="mt-1 text-[11px] text-zinc-500 leading-normal">
            Sales promises timelines & custom deliverables without formal scope lock.
          </p>
        </div>
        <div className="mt-3 text-[10px] font-mono text-amber-600 dark:text-amber-400 font-medium">
          ⚠️ Scope Ambiguity
        </div>
      </div>

      <div className="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 p-3.5 flex flex-col justify-between">
        <div>
          <div className="inline-flex p-1.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 mb-2">
            <MessageSquare className="w-4 h-4" />
          </div>
          <div className="font-bold text-zinc-900 dark:text-zinc-100">02. WhatsApp Handoff</div>
          <p className="mt-1 text-[11px] text-zinc-500 leading-normal">
            Passwords, logins, and ad accounts shared across unstructured group chats.
          </p>
        </div>
        <div className="mt-3 text-[10px] font-mono text-red-600 dark:text-red-400 font-medium">
          🔴 Lost Credentials
        </div>
      </div>

      <div className="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 p-3.5 flex flex-col justify-between">
        <div>
          <div className="inline-flex p-1.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 mb-2">
            <Database className="w-4 h-4" />
          </div>
          <div className="font-bold text-zinc-900 dark:text-zinc-100">03. Drive Chaos</div>
          <p className="mt-1 text-[11px] text-zinc-500 leading-normal">
            Wrong logo files, missing vector typography, and unapproved brand decks.
          </p>
        </div>
        <div className="mt-3 text-[10px] font-mono text-amber-600 dark:text-amber-400 font-medium">
          ⚠️ Delivery Stalled
        </div>
      </div>

      <div className="rounded-xl border border-zinc-900 dark:border-zinc-700 bg-zinc-950 text-white p-3.5 flex flex-col justify-between">
        <div>
          <div className="inline-flex p-1.5 rounded-lg bg-emerald-500/20 text-emerald-400 mb-2">
            <Layers className="w-4 h-4" />
          </div>
          <div className="font-bold text-white">Cora Unified Vault</div>
          <p className="mt-1 text-[11px] text-zinc-300 leading-normal">
            1-click SHA-256 agreement, GST deposit, and access intake in one portal.
          </p>
        </div>
        <div className="mt-3 text-[10px] font-mono text-emerald-400 font-bold">
          ✓ Sub-48h Kickoff
        </div>
      </div>
    </div>
  );
}

function ScopeCreepGraphic() {
  return (
    <div className="space-y-2 text-xs">
      <div className="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 p-3 flex items-center justify-between">
        <div className="flex items-center gap-3">
          <span className="w-6 h-6 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-mono font-bold text-zinc-700 dark:text-zinc-300 text-[11px]">1</span>
          <span className="font-semibold text-zinc-900 dark:text-zinc-100">Agreed Sprint Scope (2 Revisions)</span>
        </div>
        <span className="font-mono text-emerald-600 dark:text-emerald-400 font-bold">45% Profit Margin</span>
      </div>
      <div className="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 p-3 flex items-center justify-between">
        <div className="flex items-center gap-3">
          <span className="w-6 h-6 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-mono font-bold text-zinc-700 dark:text-zinc-300 text-[11px]">2</span>
          <span className="font-semibold text-zinc-900 dark:text-zinc-100">Unbilled "Quick Tweaks" (+3 Rounds)</span>
        </div>
        <span className="font-mono text-amber-600 dark:text-amber-400 font-bold">24% Profit Margin</span>
      </div>
      <div className="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 p-3 flex items-center justify-between">
        <div className="flex items-center gap-3">
          <span className="w-6 h-6 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-mono font-bold text-zinc-700 dark:text-zinc-300 text-[11px]">3</span>
          <span className="font-semibold text-zinc-900 dark:text-zinc-100">Out-of-Scope Landing Page Variant</span>
        </div>
        <span className="font-mono text-red-600 dark:text-red-400 font-bold">11% Profit Margin (Eroded)</span>
      </div>
    </div>
  );
}

function ReportingCycleGraphic() {
  return (
    <div className="grid gap-3 sm:grid-cols-3 text-xs">
      <div className="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 p-3.5">
        <div className="text-[10px] font-mono text-zinc-500 uppercase font-semibold">Section 01</div>
        <div className="mt-1 font-bold text-zinc-900 dark:text-zinc-100">Completed Milestones</div>
        <p className="mt-1 text-[11px] text-zinc-600 dark:text-zinc-400">Proof of work shipped this week.</p>
      </div>
      <div className="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 p-3.5">
        <div className="text-[10px] font-mono text-zinc-500 uppercase font-semibold">Section 02</div>
        <div className="mt-1 font-bold text-zinc-900 dark:text-zinc-100">Commercial Metrics</div>
        <p className="mt-1 text-[11px] text-zinc-600 dark:text-zinc-400">ROAS, leads, and pipeline generated.</p>
      </div>
      <div className="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 p-3.5">
        <div className="text-[10px] font-mono text-zinc-500 uppercase font-semibold">Section 03</div>
        <div className="mt-1 font-bold text-zinc-900 dark:text-zinc-100">Upcoming & Blockers</div>
        <p className="mt-1 text-[11px] text-zinc-600 dark:text-zinc-400">Next sprint focus + client approvals needed.</p>
      </div>
    </div>
  );
}
