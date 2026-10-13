'use client';

import React from 'react';
import { AlertTriangle, CheckCircle2, Info, XCircle } from 'lucide-react';

export interface ToolScoreBandProps {
  score: number; // 0 to 100
  title: string;
  subtitle: string;
  band: 'low' | 'moderate' | 'high' | 'critical' | 'optimized' | 'healthy' | 'at_risk' | 'underwater';
  metrics?: { label: string; value: string | number; subtext?: string }[];
}

export function ToolScoreBand({
  score,
  title,
  subtitle,
  band,
  metrics = [],
}: ToolScoreBandProps) {
  let badgeColor = 'bg-zinc-100 text-zinc-800 border-zinc-200';
  let badgeLabel = 'Moderate';
  let IconComponent = Info;

  if (band === 'optimized' || band === 'healthy' || band === 'low') {
    badgeColor = 'bg-emerald-50 text-emerald-800 border-emerald-200';
    badgeLabel = 'Optimal / Healthy';
    IconComponent = CheckCircle2;
  } else if (band === 'moderate' || band === 'at_risk') {
    badgeColor = 'bg-amber-50 text-amber-800 border-amber-200';
    badgeLabel = 'Action Needed';
    IconComponent = AlertTriangle;
  } else if (band === 'high' || band === 'critical' || band === 'underwater') {
    badgeColor = 'bg-rose-50 text-rose-800 border-rose-200';
    badgeLabel = 'Critical Friction';
    IconComponent = XCircle;
  }

  return (
    <div className="w-full rounded-2xl sm:rounded-3xl bg-white border border-zinc-200/90 p-5 sm:p-7 shadow-[0_8px_30px_rgba(0,0,0,0.04)]">
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-zinc-100">
        <div>
          <div className="flex items-center gap-2 mb-1.5">
            <span className={`inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-mono font-bold border ${badgeColor}`}>
              <IconComponent className="w-3.5 h-3.5" />
              <span>{badgeLabel}</span>
            </span>
            <span className="text-xs font-mono text-zinc-400">Score Index</span>
          </div>
          <h2 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
            {title}
          </h2>
          <p className="text-xs sm:text-sm text-zinc-600 mt-1 max-w-xl font-normal">
            {subtitle}
          </p>
        </div>

        <div className="flex items-center gap-3 shrink-0 self-start sm:self-center">
          <div className="text-right">
            <span className="block text-3xl sm:text-4xl font-extrabold font-mono text-zinc-950 tracking-tight">
              {score}<span className="text-zinc-400 text-lg sm:text-xl font-normal">/100</span>
            </span>
          </div>
        </div>
      </div>

      {metrics.length > 0 && (
        <div className="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 pt-5">
          {metrics.map((m, idx) => (
            <div key={idx} className="p-3.5 rounded-xl bg-zinc-50 border border-zinc-100">
              <span className="block text-[11px] font-mono text-zinc-500 uppercase tracking-wider mb-1">
                {m.label}
              </span>
              <span className="block text-lg sm:text-xl font-bold font-mono text-zinc-950 tracking-tight">
                {m.value}
              </span>
              {m.subtext && (
                <span className="block text-[11px] text-zinc-500 mt-0.5">
                  {m.subtext}
                </span>
              )}
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
