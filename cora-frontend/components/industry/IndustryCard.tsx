import React from 'react';
import Link from 'next/link';
import { ArrowRight, CheckCircle2, ShieldCheck, Receipt, Sparkles, FileText, ChevronRight } from 'lucide-react';
import { IndustryWorkspace } from '@/lib/industry-data';
import { IndustryIcon } from './IndustryIcon';

interface IndustryCardProps {
  workspace: IndustryWorkspace;
  onOpenDetails: (workspace: IndustryWorkspace) => void;
}

export function IndustryCard({ workspace, onOpenDetails }: IndustryCardProps) {
  return (
    <div className="group relative rounded-3xl bg-white border border-zinc-200/90 hover:border-zinc-400/80 p-6 sm:p-7 flex flex-col justify-between shadow-2xs hover:shadow-[0_16px_40px_rgba(0,0,0,0.06)] transition-all duration-300">
      
      <div>
        {/* Top Bar: Sector Badge & SAC Code */}
        <div className="flex items-center justify-between mb-4">
          <div className="flex items-center gap-2">
            <div className={`w-10 h-10 rounded-2xl ${workspace.accentBg} ${workspace.accentText} border ${workspace.accentBorder} flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform`}>
              <IndustryIcon name={workspace.iconName} className="w-5 h-5" />
            </div>
            <span className="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-500 bg-zinc-100 px-2.5 py-1 rounded-full border border-zinc-200/80">
              {workspace.sectorBadge}
            </span>
          </div>

          <span className="text-[10px] font-mono font-semibold text-zinc-600 bg-zinc-50 px-2.5 py-1 rounded-md border border-zinc-200/60">
            {workspace.sacCode} • {workspace.gstRate}
          </span>
        </div>

        {/* Title & Tagline */}
        <Link href={`/use-cases/${workspace.slug}`}>
          <h3 className="text-xl font-bold text-zinc-950 tracking-tight group-hover:text-black hover:underline underline-offset-4 decoration-zinc-300 mb-2 transition-all">
            {workspace.title}
          </h3>
        </Link>
        <p className="text-xs sm:text-sm text-zinc-600 font-normal leading-relaxed mb-5">
          {workspace.tagline}
        </p>

        {/* 3 Metric Pills */}
        <div className="grid grid-cols-3 gap-2 p-3 rounded-2xl bg-zinc-50/80 border border-zinc-200/60 mb-5">
          {workspace.stats.map((stat, idx) => (
            <div key={idx} className="text-center">
              <span className="block text-[13px] font-mono font-bold text-zinc-950 tracking-tight">
                {stat.metric}
              </span>
              <span className="block text-[10px] text-zinc-500 leading-tight line-clamp-1 mt-0.5">
                {stat.label}
              </span>
            </div>
          ))}
        </div>

        {/* Pre-Seeded Templates List */}
        <div className="space-y-1.5 mb-5">
          <span className="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400 block mb-1">
            Pre-Seeded Workflows &amp; Contracts:
          </span>
          {workspace.preSeededTemplates.slice(0, 3).map((tmpl, idx) => (
            <div key={idx} className="flex items-center gap-2 text-xs text-zinc-700 font-medium">
              <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
              <span className="line-clamp-1">{tmpl}</span>
            </div>
          ))}
        </div>

        {/* Module Stack Pills */}
        <div className="flex flex-wrap items-center gap-1.5 mb-6">
          <span className="text-[10px] font-mono text-zinc-400 uppercase tracking-wider mr-1">
            Stack:
          </span>
          {workspace.recommendedModules.map((mod) => (
            <span
              key={mod.id}
              className="inline-flex items-center gap-1 text-[11px] font-mono text-zinc-600 bg-white border border-zinc-200 px-2 py-0.5 rounded-md shadow-2xs"
            >
              <IndustryIcon name={mod.icon} className="w-3 h-3 text-zinc-500" />
              <span>{mod.title}</span>
            </span>
          ))}
        </div>
      </div>

      {/* Bottom Actions */}
      <div className="pt-4 border-t border-zinc-100 flex items-center justify-between gap-3">
        <Link
          href={`/use-cases/${workspace.slug}`}
          className="text-xs font-semibold text-zinc-700 hover:text-zinc-950 inline-flex items-center gap-1 transition-colors"
        >
          <span>Explore Solution Page</span>
          <ChevronRight className="w-3.5 h-3.5 text-zinc-400" />
        </Link>

        <a
          href={`https://app.heycora.in/workspace/login?industry=${workspace.id}&source=use_cases_card`}
          className="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-zinc-950 text-white text-xs font-semibold hover:bg-zinc-800 transition-all shadow-2xs group-hover:bg-zinc-900"
        >
          <span>Launch Workspace</span>
          <ArrowRight className="w-3 h-3 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
        </a>
      </div>

    </div>
  );
}
