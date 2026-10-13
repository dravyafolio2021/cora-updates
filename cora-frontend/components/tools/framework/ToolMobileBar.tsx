'use client';

import React from 'react';
import { ArrowUpRight, BarChart3, Download, SlidersHorizontal, ChevronUp } from 'lucide-react';

export interface ToolMobileBarProps {
  primaryLabel?: string;
  primaryValue?: string;
  primaryMetricLabel?: string;
  primaryMetricValue?: string;
  secondaryLabel?: string;
  secondaryValue?: string;
  secondaryMetricLabel?: string;
  secondaryMetricValue?: string;
  activeTab: 'inputs' | 'results';
  onTabChange?: (tab: 'inputs' | 'results') => void;
  onToggleTab?: () => void;
  onOpenDetails?: () => void;
  onExport?: () => void;
  onExportCsv?: () => void;
  inputsLabel?: string;
  resultsLabel?: string;
}

export function ToolMobileBar({
  primaryLabel,
  primaryValue,
  primaryMetricLabel,
  primaryMetricValue,
  secondaryLabel,
  secondaryValue,
  secondaryMetricLabel,
  secondaryMetricValue,
  activeTab,
  onTabChange,
  onToggleTab,
  onOpenDetails,
  onExport,
  onExportCsv,
  inputsLabel = 'Adjust Inputs',
  resultsLabel = 'View Audit',
}: ToolMobileBarProps) {
  const pLabel = primaryMetricLabel || primaryLabel || 'Metric';
  const pVal = primaryMetricValue || primaryValue || '';
  const sVal = secondaryMetricValue || secondaryValue;
  const handleToggle = onToggleTab || (() => onTabChange && onTabChange(activeTab === 'inputs' ? 'results' : 'inputs'));
  const handleExport = onExportCsv || onExport;
  return (
    <div className="fixed bottom-0 inset-x-0 z-40 lg:hidden bg-white/95 backdrop-blur-md border-t border-zinc-200/90 shadow-[0_-8px_24px_rgba(0,0,0,0.08)] px-4 py-3 pb-[max(12px,env(safe-area-inset-bottom))] transition-all">
      <div className="flex items-center justify-between gap-3">
        {/* Metric Summary Area */}
        <div 
          onClick={handleToggle}
          className="cursor-pointer select-none"
        >
          <span className="text-[10px] font-mono font-medium uppercase tracking-wider text-zinc-500 block leading-tight">
            {pLabel}
          </span>
          <div className="flex items-baseline gap-1.5 mt-0.5">
            <span className="font-mono text-lg font-bold text-zinc-950 leading-none">
              {pVal}
            </span>
            {sVal && (
              <span className="text-[11px] font-mono text-zinc-500 font-medium">
                {sVal}
              </span>
            )}
          </div>
        </div>

        {/* Action Buttons */}
        <div className="flex items-center gap-2">
          {handleExport && (
            <button
              type="button"
              onClick={handleExport}
              aria-label="Export Data"
              className="min-h-[44px] min-w-[44px] flex items-center justify-center rounded-xl border border-zinc-200 bg-zinc-50 hover:bg-zinc-100 text-zinc-700 active:scale-95 transition-all touch-manipulation cursor-pointer"
            >
              <Download className="w-4 h-4 text-zinc-600" />
            </button>
          )}

          {activeTab === 'inputs' ? (
            <button
              type="button"
              onClick={() => {
                if (onOpenDetails) {
                  onOpenDetails();
                } else {
                  handleToggle();
                }
              }}
              className="min-h-[44px] px-4 rounded-xl bg-zinc-900 text-white font-medium text-xs flex items-center gap-1.5 shadow-sm active:scale-95 transition-all touch-manipulation cursor-pointer"
            >
              <BarChart3 className="w-3.5 h-3.5" />
              <span>{resultsLabel}</span>
              <ChevronUp className="w-3.5 h-3.5" />
            </button>
          ) : (
            <button
              type="button"
              onClick={handleToggle}
              className="min-h-[44px] px-4 rounded-xl bg-zinc-900 text-white font-medium text-xs flex items-center gap-1.5 shadow-sm active:scale-95 transition-all touch-manipulation cursor-pointer"
            >
              <SlidersHorizontal className="w-3.5 h-3.5" />
              <span>{inputsLabel}</span>
            </button>
          )}
        </div>
      </div>
    </div>
  );
}
