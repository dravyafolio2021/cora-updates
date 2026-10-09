'use client';

import React from 'react';
import { 
  Search, 
  X, 
  Layers, 
  Bot, 
  Users, 
  FileCheck, 
  Receipt, 
  SlidersHorizontal, 
  Rocket, 
  CheckCircle2, 
  Clock, 
  ArrowRight, 
  Sparkles,
  Briefcase,
  Camera,
  Clapperboard,
  Building,
  Heart,
  Palette,
  Check,
  Code,
  Scale
} from 'lucide-react';
import { CATEGORIES, INDUSTRIES, IndustryItem } from '@/lib/features-data';
import Link from 'next/link';

interface FeaturesSidebarProps {
  activeCategory: string;
  onSelectCategory: (id: string) => void;
  selectedIndustry: string;
  onSelectIndustry: (id: string) => void;
  searchQuery: string;
  onSearchChange: (q: string) => void;
  statusFilter: 'all' | 'live' | 'roadmap';
  onStatusFilterChange: (status: 'all' | 'live' | 'roadmap') => void;
  totalBuiltCount: number;
  totalRoadmapCount: number;
  industryCounts: Record<string, number>;
  onCloseMobileDrawer?: () => void;
}

// Icon mapper for categories
const getCategoryIcon = (categoryId: string) => {
  switch (categoryId) {
    case 'intelligence':
      return <Bot className="w-4 h-4" />;
    case 'sales':
      return <Users className="w-4 h-4" />;
    case 'operations':
      return <FileCheck className="w-4 h-4" />;
    case 'finance':
      return <Receipt className="w-4 h-4" />;
    case 'platform':
      return <SlidersHorizontal className="w-4 h-4" />;
    case 'roadmap':
      return <Rocket className="w-4 h-4" />;
    case 'all':
    default:
      return <Layers className="w-4 h-4" />;
  }
};

// Icon mapper for industries
const getIndustryIcon = (iconName: string, isSelected: boolean) => {
  const iconClass = isSelected ? 'text-white' : 'text-zinc-700';
  switch (iconName) {
    case 'Code':
      return <Code className={`w-4 h-4 ${iconClass}`} />;
    case 'Sparkles':
      return <Sparkles className={`w-4 h-4 ${iconClass}`} />;
    case 'Scale':
      return <Scale className={`w-4 h-4 ${iconClass}`} />;
    case 'Clapperboard':
      return <Clapperboard className={`w-4 h-4 ${iconClass}`} />;
    case 'Building':
      return <Building className={`w-4 h-4 ${iconClass}`} />;
    case 'Palette':
      return <Palette className={`w-4 h-4 ${iconClass}`} />;
    case 'Briefcase':
    default:
      return <Briefcase className={`w-4 h-4 ${iconClass}`} />;
  }
};

export function FeaturesSidebar({
  activeCategory,
  onSelectCategory,
  selectedIndustry,
  onSelectIndustry,
  searchQuery,
  onSearchChange,
  statusFilter,
  onStatusFilterChange,
  totalBuiltCount,
  totalRoadmapCount,
  industryCounts,
  onCloseMobileDrawer
}: FeaturesSidebarProps) {

  return (
    <aside className="w-full space-y-6">
      
      {/* ── 1. SEARCH INPUT ── */}
      <div>
        <div className="relative flex items-center">
          <Search className="w-4 h-4 text-zinc-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" aria-hidden="true" />
          <input
            id="sidebar-search-input"
            type="text"
            placeholder="Search features..."
            value={searchQuery}
            onChange={(e) => onSearchChange(e.target.value)}
            className="w-full pl-10 pr-9 py-2.5 bg-zinc-50/80 hover:bg-zinc-100/70 focus:bg-white border border-zinc-200/90 focus:border-zinc-950 rounded-xl text-xs font-medium text-zinc-900 placeholder-zinc-400 outline-none transition-all shadow-2xs"
          />
          {searchQuery && (
            <button
              type="button"
              onClick={() => onSearchChange('')}
              className="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 rounded-md text-zinc-400 hover:text-zinc-800 transition-colors cursor-pointer"
              aria-label="Clear search input"
            >
              <X className="w-3.5 h-3.5" />
            </button>
          )}
        </div>
      </div>

      {/* ── 2. SEGMENTED STATUS TABS ── */}
      <div>
        <div className="p-1 bg-zinc-100/90 rounded-xl flex items-center gap-1 border border-zinc-200/60 text-xs">
          <button
            type="button"
            onClick={() => onStatusFilterChange('all')}
            className={`flex-1 py-2 px-2.5 rounded-lg font-medium text-xs transition-all cursor-pointer flex items-center justify-center gap-1.5 ${
              statusFilter === 'all'
                ? 'bg-white text-zinc-950 shadow-2xs font-semibold'
                : 'text-zinc-500 hover:text-zinc-900'
            }`}
          >
            <span>All</span>
            <span className="text-[11px] font-mono text-zinc-400">{totalBuiltCount + totalRoadmapCount}</span>
          </button>

          <button
            type="button"
            onClick={() => onStatusFilterChange('live')}
            className={`flex-1 py-2 px-2.5 rounded-lg font-medium text-xs transition-all cursor-pointer flex items-center justify-center gap-1.5 ${
              statusFilter === 'live'
                ? 'bg-white text-zinc-950 shadow-2xs font-semibold'
                : 'text-zinc-500 hover:text-zinc-900'
            }`}
          >
            <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0" />
            <span>Live</span>
            <span className="text-[11px] font-mono text-emerald-700 font-semibold">{totalBuiltCount}</span>
          </button>

          <button
            type="button"
            onClick={() => onStatusFilterChange('roadmap')}
            className={`flex-1 py-2 px-2.5 rounded-lg font-medium text-xs transition-all cursor-pointer flex items-center justify-center gap-1.5 ${
              statusFilter === 'roadmap'
                ? 'bg-white text-zinc-950 shadow-2xs font-semibold'
                : 'text-zinc-500 hover:text-zinc-900'
            }`}
          >
            <span className="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse shrink-0" />
            <span>Roadmap</span>
            <span className="text-[11px] font-mono text-amber-700 font-semibold">{totalRoadmapCount}</span>
          </button>
        </div>
      </div>

      {/* ── 3. INDUSTRY WORKSPACES ── */}
      <div>
        <div className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-400 mb-2.5 px-1 flex items-center justify-between">
          <span>Workspaces</span>
          <span className="text-[10px] text-zinc-400 font-normal">By Industry</span>
        </div>

        <div className="space-y-1" role="group" aria-label="Agency workspace filters">
          {INDUSTRIES.map((ind) => {
            const isSelected = selectedIndustry === ind.id;
            const count = industryCounts[ind.id] ?? 28;

            return (
              <button
                key={ind.id}
                type="button"
                onClick={() => {
                  onSelectIndustry(ind.id);
                  if (onCloseMobileDrawer) onCloseMobileDrawer();
                }}
                className={`w-full px-3 py-2 rounded-xl text-left transition-all cursor-pointer flex items-center justify-between gap-2.5 group text-xs ${
                  isSelected
                    ? 'bg-zinc-950 text-white font-medium shadow-2xs'
                    : 'text-zinc-700 hover:bg-zinc-100 hover:text-zinc-950'
                }`}
              >
                <div className="flex items-center gap-2.5 min-w-0">
                  <span className={`${isSelected ? 'text-white' : 'text-zinc-400 group-hover:text-zinc-700'}`}>
                    {getIndustryIcon(ind.iconName, isSelected)}
                  </span>
                  <span className="truncate text-xs font-medium">
                    {ind.label}
                  </span>
                </div>

                <div className="flex items-center gap-1.5 shrink-0">
                  {isSelected && (
                    <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse" />
                  )}
                  <span className={`text-[11px] font-mono font-medium px-2 py-0.5 rounded-md ${
                    isSelected ? 'bg-white/20 text-white' : 'bg-zinc-100 text-zinc-500 group-hover:bg-zinc-200/70 group-hover:text-zinc-800'
                  }`}>
                    {count}
                  </span>
                </div>
              </button>
            );
          })}
        </div>
      </div>

      {/* ── 4. CATEGORIES NAVIGATION ── */}
      <div className="pt-4.5 border-t border-zinc-100">
        <div className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-400 mb-2.5 px-1 flex items-center justify-between">
          <span>Categories</span>
          <span className="text-[10px] text-zinc-400 font-normal">Functional Area</span>
        </div>

        <nav className="space-y-1" aria-label="Feature categories">
          {CATEGORIES.map((cat) => {
            const isActive = activeCategory === cat.id;
            return (
              <button
                key={cat.id}
                type="button"
                onClick={() => {
                  onSelectCategory(cat.id);
                  if (onCloseMobileDrawer) onCloseMobileDrawer();
                }}
                className={`w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs transition-all cursor-pointer group text-left ${
                  isActive
                    ? 'bg-zinc-950 text-white shadow-2xs font-medium'
                    : 'text-zinc-700 hover:bg-zinc-100 hover:text-zinc-950'
                }`}
              >
                <div className="flex items-center gap-2.5 min-w-0">
                  <span className={`${isActive ? 'text-white' : 'text-zinc-400 group-hover:text-zinc-700'}`}>
                    {getCategoryIcon(cat.id)}
                  </span>
                  <span className="truncate text-xs font-medium">{cat.label.replace(/\s*\(\d+\)$/, '')}</span>
                </div>

                <span className={`text-[11px] font-mono font-medium px-2 py-0.5 rounded-md shrink-0 ${
                  isActive 
                    ? 'bg-white/20 text-white' 
                    : 'bg-zinc-100 text-zinc-500 group-hover:bg-zinc-200/70 group-hover:text-zinc-800'
                }`}>
                  {cat.label.match(/\((\d+)\)/)?.[1] || '0'}
                </span>
              </button>
            );
          })}
        </nav>
      </div>

      {/* ── 5. HIGHLIGHT TILE ── */}
      <div className="p-3.5 rounded-2xl bg-zinc-50 border border-zinc-200/70 text-zinc-900">
        <div className="flex items-center justify-between gap-1 mb-1.5">
          <div className="flex items-center gap-1.5 text-xs font-bold text-zinc-950">
            <Sparkles className="w-3.5 h-3.5 text-zinc-700" />
            <span>Save ₹35,000+/mo</span>
          </div>
          <Link
            href="/pricing"
            className="text-xs font-bold text-zinc-900 hover:text-black inline-flex items-center gap-1 group"
          >
            <span>Pricing</span>
            <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
          </Link>
        </div>
        <p className="text-[11px] text-zinc-500 leading-relaxed">
          Replaces DocuSign, HubSpot, Jira, Calendly &amp; Typeform in 1 simple OS.
        </p>
      </div>

    </aside>
  );
}
