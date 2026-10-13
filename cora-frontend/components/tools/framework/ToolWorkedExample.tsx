'use client';

import React, { useState } from 'react';
import { FileText, ArrowRight, ChevronDown, ChevronUp } from 'lucide-react';

export interface WorkedStep {
  step: string;
  detail: string;
  result: string;
}

export interface ToolWorkedExampleProps {
  title?: string;
  scenarioDescription: string;
  inputsTable: { label: string; value: string }[];
  steps: WorkedStep[];
  conclusion: string;
}

export function ToolWorkedExample({
  title = 'Worked Example & Step-by-Step Calculation',
  scenarioDescription,
  inputsTable,
  steps,
  conclusion,
}: ToolWorkedExampleProps) {
  const [isOpenMobile, setIsOpenMobile] = useState(false);

  return (
    <div className="rounded-2xl border border-zinc-200/90 bg-white p-4 sm:p-6 md:p-8 shadow-xs space-y-4 sm:space-y-5">
      {/* Header with Mobile Accordion Toggle */}
      <div 
        onClick={() => setIsOpenMobile(!isOpenMobile)}
        className="flex items-center justify-between border-b border-zinc-200/70 pb-3 sm:pb-4 cursor-pointer sm:cursor-default select-none"
      >
        <div className="flex items-center gap-2">
          <FileText className="w-4 h-4 sm:w-5 sm:h-5 text-zinc-700" />
          <h3 className="font-display text-base sm:text-xl font-bold text-zinc-950 tracking-tight">
            {title}
          </h3>
        </div>

        <div className="flex items-center gap-2">
          <span className="sm:hidden text-xs text-zinc-500 font-mono">
            {isOpenMobile ? 'Hide' : 'Show details'}
          </span>
          <button
            type="button"
            className="sm:hidden w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-600"
            aria-label="Toggle worked example details"
          >
            {isOpenMobile ? <ChevronUp className="w-4 h-4" /> : <ChevronDown className="w-4 h-4" />}
          </button>
        </div>
      </div>

      {/* Content — Always visible on sm+, collapsible on mobile */}
      <div className={`space-y-4 sm:space-y-5 ${isOpenMobile ? 'block' : 'hidden sm:block'}`}>
        <p className="text-xs sm:text-sm text-zinc-600 leading-relaxed">
          {scenarioDescription}
        </p>

        {/* Input Parameters Matrix */}
        <div className="rounded-xl border border-zinc-200/80 overflow-hidden">
          <div className="bg-zinc-100/80 px-3 py-1.5 sm:px-4 sm:py-2 text-[10px] sm:text-[11px] font-mono font-semibold uppercase tracking-wider text-zinc-700 border-b border-zinc-200">
            Example Baseline Parameters
          </div>
          <div className="grid grid-cols-2 sm:grid-cols-4 divide-x divide-y sm:divide-y-0 divide-zinc-200/80 bg-white">
            {inputsTable.map((item, idx) => (
              <div key={idx} className="p-2.5 sm:p-3 text-xs">
                <span className="text-zinc-500 text-[10px] sm:text-[11px] block">{item.label}</span>
                <span className="font-mono font-bold text-zinc-900 text-xs sm:text-sm mt-0.5 block truncate">{item.value}</span>
              </div>
            ))}
          </div>
        </div>

        {/* Steps List */}
        <div className="space-y-2">
          <span className="text-[10px] sm:text-[11px] font-mono font-semibold uppercase tracking-wider text-zinc-500 block">
            Computational Breakdown
          </span>
          {steps.map((item, idx) => (
            <div
              key={idx}
              className="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 sm:gap-2 p-3 sm:p-3.5 rounded-xl border border-zinc-200/70 bg-zinc-50/50 text-xs"
            >
              <div className="flex items-start gap-2 sm:gap-2.5">
                <span className="w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-zinc-900 text-white font-mono text-[10px] sm:text-[11px] flex items-center justify-center shrink-0 mt-0.5 sm:mt-0 font-bold">
                  {idx + 1}
                </span>
                <div>
                  <span className="font-semibold text-zinc-900 block">{item.step}</span>
                  <span className="text-zinc-600 text-xs">{item.detail}</span>
                </div>
              </div>
              <div className="font-mono font-bold text-zinc-950 bg-white px-2.5 py-1 rounded-md border border-zinc-200/80 self-start sm:self-auto shrink-0 shadow-2xs text-xs">
                {item.result}
              </div>
            </div>
          ))}
        </div>

        {/* Conclusion */}
        <div className="p-3 sm:p-4 rounded-xl border border-emerald-200/80 bg-emerald-50/60 flex items-start gap-2.5 text-xs text-emerald-950">
          <ArrowRight className="w-4 h-4 text-emerald-700 shrink-0 mt-0.5" />
          <div>
            <span className="font-bold block mb-0.5">Outcome Analysis:</span>
            <span>{conclusion}</span>
          </div>
        </div>
      </div>
    </div>
  );
}
