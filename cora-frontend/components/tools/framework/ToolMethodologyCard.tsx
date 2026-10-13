'use client';

import React, { useState } from 'react';
import { BookOpen, Calculator, HelpCircle, ShieldAlert, ChevronDown, ChevronUp } from 'lucide-react';

export interface MethodologyFormula {
  label: string;
  formula: string;
  explanation: string;
}

export interface ToolMethodologyCardProps {
  methodologyVersion?: string;
  formulas: MethodologyFormula[];
  assumptions: string[];
  limitations: string[];
}

export function ToolMethodologyCard({
  methodologyVersion = 'v1.2 (October 2026)',
  formulas,
  assumptions,
  limitations,
}: ToolMethodologyCardProps) {
  const [isOpenMobile, setIsOpenMobile] = useState(false);

  return (
    <div className="rounded-2xl border border-zinc-200/90 bg-white p-4 sm:p-6 md:p-8 shadow-xs space-y-4 sm:space-y-6">
      {/* Header with Mobile Accordion Toggle */}
      <div 
        onClick={() => setIsOpenMobile(!isOpenMobile)}
        className="flex items-center justify-between gap-2 border-b border-zinc-200/70 pb-3 sm:pb-4 cursor-pointer sm:cursor-default select-none"
      >
        <div className="flex items-center gap-2">
          <BookOpen className="w-4 h-4 sm:w-5 sm:h-5 text-zinc-700" />
          <h3 className="font-display text-base sm:text-xl font-bold text-zinc-950 tracking-tight">
            Methodology &amp; Formulas
          </h3>
        </div>

        <div className="flex items-center gap-2">
          <span className="text-[10px] sm:text-xs font-mono px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-md bg-zinc-100 text-zinc-700 border border-zinc-200">
            {methodologyVersion}
          </span>
          <button
            type="button"
            className="sm:hidden w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-600"
            aria-label="Toggle methodology details"
          >
            {isOpenMobile ? <ChevronUp className="w-4 h-4" /> : <ChevronDown className="w-4 h-4" />}
          </button>
        </div>
      </div>

      {/* Content — Always visible on sm+, collapsible on mobile */}
      <div className={`space-y-4 sm:space-y-6 ${isOpenMobile ? 'block' : 'hidden sm:block'}`}>
        {/* Formulas */}
        <div>
          <h4 className="text-[11px] sm:text-xs font-mono uppercase tracking-wider text-zinc-500 font-semibold mb-2.5 sm:mb-3 flex items-center gap-1.5">
            <Calculator className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-zinc-600" />
            Core Computational Formulas
          </h4>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-2.5 sm:gap-3">
            {formulas.map((item, idx) => (
              <div
                key={idx}
                className="rounded-xl border border-zinc-200/80 bg-[#FAF9F5] p-3 sm:p-4 font-mono text-xs"
              >
                <span className="text-[10px] sm:text-[11px] uppercase tracking-wider text-zinc-500 block mb-1 font-sans font-bold">
                  {item.label}
                </span>
                <code className="block bg-white p-2 sm:p-2.5 rounded-lg border border-zinc-200/80 text-zinc-900 font-bold mb-2 text-[11px] sm:text-xs overflow-x-auto">
                  {item.formula}
                </code>
                <p className="text-zinc-600 font-sans text-xs leading-relaxed">
                  {item.explanation}
                </p>
              </div>
            ))}
          </div>
        </div>

        {/* Assumptions & Limitations Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 pt-1">
          {/* Baseline Assumptions */}
          <div className="rounded-xl border border-zinc-200/70 bg-zinc-50/60 p-3.5 sm:p-4">
            <h4 className="text-[11px] sm:text-xs font-mono uppercase tracking-wider text-zinc-700 font-bold mb-2.5 flex items-center gap-1.5">
              <HelpCircle className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-zinc-600" />
              Baseline Assumptions
            </h4>
            <ul className="space-y-1.5 sm:space-y-2 text-xs text-zinc-600 leading-relaxed list-disc list-inside">
              {assumptions.map((assumption, idx) => (
                <li key={idx} className="text-zinc-600">
                  <span className="text-zinc-800">{assumption}</span>
                </li>
              ))}
            </ul>
          </div>

          {/* Known Limitations */}
          <div className="rounded-xl border border-zinc-200/70 bg-zinc-50/60 p-3.5 sm:p-4">
            <h4 className="text-[11px] sm:text-xs font-mono uppercase tracking-wider text-zinc-700 font-bold mb-2.5 flex items-center gap-1.5">
              <ShieldAlert className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-zinc-600" />
              Analytical Limitations
            </h4>
            <ul className="space-y-1.5 sm:space-y-2 text-xs text-zinc-600 leading-relaxed list-disc list-inside">
              {limitations.map((limitation, idx) => (
                <li key={idx} className="text-zinc-600">
                  <span className="text-zinc-800">{limitation}</span>
                </li>
              ))}
            </ul>
          </div>
        </div>
      </div>
    </div>
  );
}
