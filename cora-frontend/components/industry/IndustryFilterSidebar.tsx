import React from 'react';
import { Layers, RotateCcw, ShieldCheck, Receipt, Sparkles, Filter, X } from 'lucide-react';
import { SECTOR_CATEGORIES, SectorCategory } from '@/lib/industry-data';
import { IndustryIcon } from './IndustryIcon';

interface IndustryFilterSidebarProps {
  activeSector: string;
  onSectorChange: (sectorId: string) => void;
  totalFilteredCount: number;
  totalWorkspacesCount: number;
  onResetFilters: () => void;
  isMobileDrawer?: boolean;
  onCloseMobileDrawer?: () => void;
}

export function IndustryFilterSidebar({
  activeSector,
  onSectorChange,
  totalFilteredCount,
  totalWorkspacesCount,
  onResetFilters,
  isMobileDrawer = false,
  onCloseMobileDrawer,
}: IndustryFilterSidebarProps) {
  return (
    <aside className={`w-full ${isMobileDrawer ? '' : 'lg:w-64 xl:w-72 shrink-0'} space-y-6`}>
      
      {/* Mobile Drawer Header */}
      {isMobileDrawer && (
        <div className="flex items-center justify-between pb-4 border-b border-zinc-200">
          <div className="flex items-center gap-2">
            <Filter className="w-4 h-4 text-zinc-950" />
            <span className="text-sm font-bold text-zinc-950">Filter Industry Workspaces</span>
          </div>
          <button
            onClick={onCloseMobileDrawer}
            className="p-1.5 rounded-lg text-zinc-400 hover:text-zinc-900 hover:bg-zinc-100 transition-colors"
          >
            <X className="w-5 h-5" />
          </button>
        </div>
      )}

      {/* Sector Category Group */}
      <div className="space-y-2">
        <div className="flex items-center justify-between pb-2 mb-1 border-b border-zinc-100">
          <span className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-400">
            Industry Sectors
          </span>
          <span className="text-[10px] font-mono font-bold text-zinc-500 bg-zinc-100 px-2 py-0.5 rounded-full">
            {totalFilteredCount} of {totalWorkspacesCount}
          </span>
        </div>

        <div className="space-y-1">
          {SECTOR_CATEGORIES.map((sec: SectorCategory) => {
            const isSelected = activeSector === sec.id;
            return (
              <button
                key={sec.id}
                onClick={() => {
                  onSectorChange(sec.id);
                  if (isMobileDrawer && onCloseMobileDrawer) {
                    onCloseMobileDrawer();
                  }
                }}
                className={`w-full flex items-center justify-between px-3 py-2.5 rounded-2xl text-xs font-semibold transition-all text-left ${
                  isSelected
                    ? 'bg-zinc-950 text-white shadow-sm'
                    : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100/80'
                }`}
              >
                <div className="flex items-center gap-2.5 min-w-0">
                  <IndustryIcon
                    name={sec.iconName}
                    className={`w-4 h-4 ${isSelected ? 'text-white' : 'text-zinc-400'}`}
                  />
                  <span className="truncate">{sec.label}</span>
                </div>
                <span
                  className={`text-[10px] font-mono px-2 py-0.5 rounded-full shrink-0 ${
                    isSelected
                      ? 'bg-zinc-800 text-zinc-200'
                      : 'bg-zinc-100 text-zinc-500 border border-zinc-200/60'
                  }`}
                >
                  {sec.count}
                </span>
              </button>
            );
          })}
        </div>
      </div>

      {/* Trust & Pre-Seeded Highlights Card */}
      <div className="p-4 rounded-2xl bg-zinc-50 border border-zinc-200/80 space-y-3">
        <div className="flex items-center gap-2 text-xs font-bold text-zinc-950">
          <ShieldCheck className="w-4 h-4 text-emerald-600" />
          <span>Turnkey India Ready</span>
        </div>
        <p className="text-[11.5px] text-zinc-600 leading-relaxed font-normal">
          Every industry workspace includes pre-seeded Indian IT Act 2000 digital contracts, SAC tax codes, and automated 18% GST invoice splitting.
        </p>
        <div className="pt-2 border-t border-zinc-200/60 flex items-center justify-between text-[10px] font-mono text-zinc-500">
          <span>64+ Total Legal Templates</span>
          <span className="text-emerald-700 font-bold">100% Pre-Seeded</span>
        </div>
      </div>

      {/* Reset Filter Button */}
      {activeSector !== 'all' && (
        <button
          onClick={onResetFilters}
          className="w-full flex items-center justify-center gap-1.5 py-2 px-3 text-xs font-semibold text-zinc-600 hover:text-zinc-950 bg-white hover:bg-zinc-100 border border-zinc-200 rounded-xl transition-all"
        >
          <RotateCcw className="w-3.5 h-3.5" />
          <span>Reset to All Sectors</span>
        </button>
      )}

    </aside>
  );
}
