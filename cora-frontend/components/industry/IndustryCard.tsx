import React from 'react';
import Link from 'next/link';
import { ArrowRight, ChevronRight, Sparkles } from 'lucide-react';
import { IndustryWorkspace } from '@/lib/industry-data';
import { IndustryIcon } from './IndustryIcon';
import { IndustryCardVisual } from './IndustryCardVisual';

interface IndustryCardProps {
  workspace: IndustryWorkspace;
  onOpenDetails: (workspace: IndustryWorkspace) => void;
}

export function IndustryCard({ workspace }: IndustryCardProps) {
  return (
    <Link
      href={`/use-cases/${workspace.slug}`}
      className="group relative rounded-3xl bg-white border border-zinc-200/90 hover:border-zinc-400/90 overflow-hidden flex flex-col justify-between shadow-2xs hover:shadow-[0_20px_50px_rgba(0,0,0,0.08)] transition-all duration-300"
    >
      {/* ── Top Micro-UI Visual Illustration Viewport ── */}
      <div className="w-full relative overflow-hidden">
        <IndustryCardVisual slug={workspace.slug} title={workspace.title} />
      </div>

      {/* ── Concise Content Body (Clean, Minimal, Zero Clutter) ── */}
      <div className="p-5 sm:p-6 flex-1 flex flex-col justify-between">
        <div>
          {/* Header Row: Title & Sector Tag */}
          <div className="flex items-center justify-between gap-2 mb-2">
            <h3 className="text-lg sm:text-xl font-bold text-zinc-950 tracking-tight group-hover:text-black transition-colors">
              {workspace.title}
            </h3>
            <span className="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-500 bg-zinc-100 px-2.5 py-0.5 rounded-full shrink-0 border border-zinc-200/70">
              {workspace.sectorBadge}
            </span>
          </div>

          {/* 1-Line Tagline */}
          <p className="text-xs sm:text-sm text-zinc-600 font-normal leading-relaxed mb-4 line-clamp-2">
            {workspace.tagline}
          </p>

          {/* Clean Feature Tag Chips (2-3 tags only) */}
          <div className="flex flex-wrap items-center gap-1.5 mb-2">
            <span className="text-[11px] font-mono font-medium text-zinc-700 bg-zinc-50 border border-zinc-200/80 px-2.5 py-1 rounded-lg">
              {workspace.sacCode} • {workspace.gstRate}
            </span>
            <span className="text-[11px] font-mono font-medium text-zinc-700 bg-zinc-50 border border-zinc-200/80 px-2.5 py-1 rounded-lg">
              {workspace.stats[0]?.metric} {workspace.stats[0]?.label.split(' ')[0]}
            </span>
            <span className="text-[11px] font-mono font-medium text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-2.5 py-1 rounded-lg">
              Turnkey Workflows
            </span>
          </div>
        </div>

        {/* Bottom Action Row */}
        <div className="pt-4 mt-4 border-t border-zinc-100 flex items-center justify-between">
          <span className="text-xs font-semibold text-zinc-900 group-hover:text-black inline-flex items-center gap-1 transition-colors">
            <span>Explore Solution</span>
            <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-1 transition-transform" />
          </span>

          <span className="text-[11px] font-mono font-medium text-zinc-400 group-hover:text-zinc-600 transition-colors">
            Free Workspace →
          </span>
        </div>
      </div>
    </Link>
  );
}
