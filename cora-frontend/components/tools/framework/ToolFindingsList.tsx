'use client';

import React from 'react';
import { AlertCircle, AlertTriangle, CheckCircle2, Info, ArrowRight } from 'lucide-react';

export interface FindingItem {
  id: string;
  severity: 'critical' | 'warning' | 'info' | 'positive';
  title: string;
  description: string;
  remedy?: string;
  metric?: string;
}

export interface ToolFindingsListProps {
  title?: string;
  findings: FindingItem[];
  emptyMessage?: string;
}

export function ToolFindingsList({
  title = 'Diagnostic Findings & Bottlenecks',
  findings,
  emptyMessage = 'No operational risks detected under current configuration.',
}: ToolFindingsListProps) {
  if (!findings || findings.length === 0) {
    return (
      <div className="rounded-xl border border-zinc-200/90 bg-white p-6 text-center text-sm text-zinc-500">
        <CheckCircle2 className="w-5 h-5 mx-auto mb-2 text-emerald-600" />
        <p>{emptyMessage}</p>
      </div>
    );
  }

  return (
    <div className="rounded-2xl border border-zinc-200/90 bg-white p-6 sm:p-8 shadow-xs">
      <h3 className="font-display text-lg sm:text-xl font-bold text-zinc-950 tracking-tight mb-4 flex items-center justify-between">
        <span>{title}</span>
        <span className="text-xs font-mono text-zinc-500 font-normal">
          {findings.length} item{findings.length === 1 ? '' : 's'} identified
        </span>
      </h3>

      <div className="space-y-3">
        {findings.map((finding) => {
          const isCritical = finding.severity === 'critical';
          const isWarning = finding.severity === 'warning';
          const isPositive = finding.severity === 'positive';

          const bgClass = isCritical
            ? 'bg-rose-50/70 border-rose-200/80 text-rose-950'
            : isWarning
            ? 'bg-amber-50/70 border-amber-200/80 text-amber-950'
            : isPositive
            ? 'bg-emerald-50/70 border-emerald-200/80 text-emerald-950'
            : 'bg-zinc-50 border-zinc-200/80 text-zinc-900';

          const iconClass = isCritical
            ? 'text-rose-600'
            : isWarning
            ? 'text-amber-600'
            : isPositive
            ? 'text-emerald-600'
            : 'text-zinc-600';

          const IconComponent = isCritical
            ? AlertCircle
            : isWarning
            ? AlertTriangle
            : isPositive
            ? CheckCircle2
            : Info;

          return (
            <div
              key={finding.id}
              className={`rounded-xl border p-4 transition-colors ${bgClass}`}
            >
              <div className="flex items-start gap-3">
                <IconComponent className={`w-5 h-5 shrink-0 mt-0.5 ${iconClass}`} />
                <div className="flex-1 min-w-0">
                  <div className="flex flex-wrap items-center justify-between gap-2 mb-1">
                    <h4 className="font-semibold text-sm sm:text-base leading-snug">
                      {finding.title}
                    </h4>
                    {finding.metric && (
                      <span className="font-mono text-xs font-semibold px-2 py-0.5 rounded-md bg-white/80 border border-zinc-200/60 shadow-2xs">
                        {finding.metric}
                      </span>
                    )}
                  </div>
                  <p className="text-xs sm:text-sm text-zinc-600 leading-relaxed">
                    {finding.description}
                  </p>
                  {finding.remedy && (
                    <div className="mt-2.5 pt-2.5 border-t border-zinc-200/60 flex items-start gap-1.5 text-xs text-zinc-700 font-medium">
                      <ArrowRight className="w-3.5 h-3.5 text-zinc-400 shrink-0 mt-0.5" />
                      <span><strong>Fix:</strong> {finding.remedy}</span>
                    </div>
                  )}
                </div>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
}
