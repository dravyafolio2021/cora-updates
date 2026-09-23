'use client';

import React from 'react';
import { FileText, MessageSquare, Database, Layers } from 'lucide-react';

interface InfographicProps {
  infographicId: 'onboarding-friction' | 'scope-creep-waterfall' | 'reporting-cycle';
  headline: string;
  explanation?: string;
  source?: string;
  sourceUrl?: string;
}

export function BlogInfographics({ infographicId, headline, explanation, source, sourceUrl }: InfographicProps) {
  return (
    <figure className="my-10 rounded-2xl border border-zinc-200 bg-[#FBFaf7] p-5 sm:p-7 shadow-sm overflow-hidden">
      <div className="mb-5 border-b border-zinc-200/80 pb-3">
        <div className="text-[10px] font-mono font-bold uppercase tracking-widest text-zinc-500">
          EDITORIAL BREAKDOWN
        </div>
        <h4 className="mt-1 font-display text-base sm:text-lg font-bold text-zinc-950">
          {headline}
        </h4>
        {explanation && (
          <p className="mt-1 text-xs text-zinc-600 leading-relaxed">
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
        <figcaption className="mt-4 pt-3 border-t border-zinc-200/60 text-[11px] font-mono text-zinc-600 flex items-center gap-1.5">
          <span className="font-semibold text-zinc-700">SOURCE:</span>{' '}
          {sourceUrl ? (
            <a
              href={sourceUrl}
              target="_blank"
              rel="noopener noreferrer"
              className="text-zinc-700 hover:text-zinc-950 underline underline-offset-2 decoration-zinc-300 hover:decoration-zinc-900 transition-colors"
            >
              {source}
            </a>
          ) : (
            <span>{source}</span>
          )}
        </figcaption>
      )}
    </figure>
  );
}

function OnboardingFrictionGraphic() {
  return (
    <div className="grid gap-3 sm:grid-cols-4 items-stretch text-xs">
      <div className="rounded-xl border border-zinc-200 bg-white p-3.5 flex flex-col justify-between">
        <div>
          <div className="inline-flex p-1.5 rounded-lg bg-zinc-100 text-zinc-700 mb-2">
            <FileText className="w-4 h-4" />
          </div>
          <div className="font-bold text-zinc-900">01. Verbal Agreements</div>
          <p className="mt-1 text-[11px] text-zinc-500 leading-normal">
            Timelines and deliverables discussed on calls without formal scope confirmation.
          </p>
        </div>
        <div className="mt-3 text-[10px] font-mono text-amber-600 font-medium">
          Scope Ambiguity
        </div>
      </div>

      <div className="rounded-xl border border-zinc-200 bg-white p-3.5 flex flex-col justify-between">
        <div>
          <div className="inline-flex p-1.5 rounded-lg bg-zinc-100 text-zinc-700 mb-2">
            <MessageSquare className="w-4 h-4" />
          </div>
          <div className="font-bold text-zinc-900">02. Chat Handoff</div>
          <p className="mt-1 text-[11px] text-zinc-500 leading-normal">
            Account access and login credentials shared across unindexed chat threads.
          </p>
        </div>
        <div className="mt-3 text-[10px] font-mono text-red-600 font-medium">
          Scattered Logins
        </div>
      </div>

      <div className="rounded-xl border border-zinc-200 bg-white p-3.5 flex flex-col justify-between">
        <div>
          <div className="inline-flex p-1.5 rounded-lg bg-zinc-100 text-zinc-700 mb-2">
            <Database className="w-4 h-4" />
          </div>
          <div className="font-bold text-zinc-900">03. Drive Folders</div>
          <p className="mt-1 text-[11px] text-zinc-500 leading-normal">
            Low-res logo files, missing fonts, and unapproved draft decks.
          </p>
        </div>
        <div className="mt-3 text-[10px] font-mono text-amber-600 font-medium">
          Kickoff Stalled
        </div>
      </div>

      <div className="rounded-xl border border-zinc-900 bg-zinc-950 text-white p-3.5 flex flex-col justify-between">
        <div>
          <div className="inline-flex p-1.5 rounded-lg bg-emerald-500/20 text-emerald-400 mb-2">
            <Layers className="w-4 h-4" />
          </div>
          <div className="font-bold text-white">Central Intake Portal</div>
          <p className="mt-1 text-[11px] text-zinc-300 leading-normal">
            Digital agreement, deposit invoice, and organized asset intake in one place.
          </p>
        </div>
        <div className="mt-3 text-[10px] font-mono text-emerald-400 font-bold">
          ✓ Clean Kickoff
        </div>
      </div>
    </div>
  );
}

function ScopeCreepGraphic() {
  return (
    <div className="space-y-2 text-xs">
      <div className="rounded-xl border border-zinc-200 bg-white p-3 flex items-center justify-between">
        <div className="flex items-center gap-3">
          <span className="w-6 h-6 rounded-full bg-zinc-100 flex items-center justify-center font-mono font-bold text-zinc-700 text-[11px]">1</span>
          <span className="font-semibold text-zinc-900">Agreed Sprint Scope (Defined Revisions)</span>
        </div>
        <span className="font-mono text-emerald-600 font-bold">Healthy Margins</span>
      </div>
      <div className="rounded-xl border border-zinc-200 bg-white p-3 flex items-center justify-between">
        <div className="flex items-center gap-3">
          <span className="w-6 h-6 rounded-full bg-zinc-100 flex items-center justify-center font-mono font-bold text-zinc-700 text-[11px]">2</span>
          <span className="font-semibold text-zinc-900">Unbilled &ldquo;Quick Tweaks&rdquo;</span>
        </div>
        <span className="font-mono text-amber-600 font-bold">Timeline Friction</span>
      </div>
      <div className="rounded-xl border border-zinc-200 bg-white p-3 flex items-center justify-between">
        <div className="flex items-center gap-3">
          <span className="w-6 h-6 rounded-full bg-zinc-100 flex items-center justify-center font-mono font-bold text-zinc-700 text-[11px]">3</span>
          <span className="font-semibold text-zinc-900">Out-of-Scope Add-ons Without Change Orders</span>
        </div>
        <span className="font-mono text-red-600 font-bold">Eroded Margins</span>
      </div>
    </div>
  );
}

function ReportingCycleGraphic() {
  return (
    <div className="grid gap-3 sm:grid-cols-3 text-xs">
      <div className="rounded-xl border border-zinc-200 bg-white p-3.5">
        <div className="text-[10px] font-mono text-zinc-500 uppercase font-semibold">Section 01</div>
        <div className="mt-1 font-bold text-zinc-900">Completed Milestones</div>
        <p className="mt-1 text-[11px] text-zinc-600">Deliverables and proof of work shipped this week.</p>
      </div>
      <div className="rounded-xl border border-zinc-200 bg-white p-3.5">
        <div className="text-[10px] font-mono text-zinc-500 uppercase font-semibold">Section 02</div>
        <div className="mt-1 font-bold text-zinc-900">Commercial Metrics</div>
        <p className="mt-1 text-[11px] text-zinc-600">Leads generated, pipeline value, or conversion impact.</p>
      </div>
      <div className="rounded-xl border border-zinc-200 bg-white p-3.5">
        <div className="text-[10px] font-mono text-zinc-500 uppercase font-semibold">Section 03</div>
        <div className="mt-1 font-bold text-zinc-900">Upcoming & Blockers</div>
        <p className="mt-1 text-[11px] text-zinc-600">Next sprint deliverables and client inputs needed.</p>
      </div>
    </div>
  );
}
