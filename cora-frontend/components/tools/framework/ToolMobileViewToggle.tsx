'use client';

import React from 'react';
import { SlidersHorizontal, BarChart3 } from 'lucide-react';

export interface ToolMobileViewToggleProps {
  activeTab: 'inputs' | 'results';
  onTabChange: (tab: 'inputs' | 'results') => void;
  inputsCount?: number;
  resultsBadge?: string;
  inputsLabel?: string;
  resultsLabel?: string;
}

export function ToolMobileViewToggle({
  activeTab,
  onTabChange,
  inputsCount,
  resultsBadge,
  inputsLabel = 'Adjust Inputs',
  resultsLabel = 'Audit Results',
}: ToolMobileViewToggleProps) {
  return (
    <div className="lg:hidden w-full mb-6 sticky top-[72px] z-20 bg-[#FBFaf7]/90 backdrop-blur-md pt-2 pb-3">
      <div className="flex rounded-xl bg-zinc-200/80 p-1 border border-zinc-200 shadow-2xs">
        <button
          type="button"
          onClick={() => onTabChange('inputs')}
          className={`flex-1 min-h-[44px] flex items-center justify-center gap-2 rounded-lg text-xs font-semibold transition-all touch-manipulation cursor-pointer ${
            activeTab === 'inputs'
              ? 'bg-zinc-950 text-white shadow-xs'
              : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100/50'
          }`}
        >
          <SlidersHorizontal className="w-3.5 h-3.5" />
          <span>{inputsLabel}</span>
          {inputsCount !== undefined && (
            <span
              className={`text-[10px] font-mono px-1.5 py-0.2 rounded-md ${
                activeTab === 'inputs' ? 'bg-zinc-800 text-zinc-300' : 'bg-zinc-300 text-zinc-700'
              }`}
            >
              {inputsCount}
            </span>
          )}
        </button>

        <button
          type="button"
          onClick={() => onTabChange('results')}
          className={`flex-1 min-h-[44px] flex items-center justify-center gap-2 rounded-lg text-xs font-semibold transition-all touch-manipulation cursor-pointer ${
            activeTab === 'results'
              ? 'bg-zinc-950 text-white shadow-xs'
              : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100/50'
          }`}
        >
          <BarChart3 className="w-3.5 h-3.5" />
          <span>{resultsLabel}</span>
          {resultsBadge && (
            <span
              className={`text-[10px] font-mono px-1.5 py-0.2 rounded-md ${
                activeTab === 'results' ? 'bg-zinc-800 text-zinc-300' : 'bg-zinc-300 text-zinc-700'
              }`}
            >
              {resultsBadge}
            </span>
          )}
        </button>
      </div>
    </div>
  );
}
