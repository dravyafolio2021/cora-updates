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
  Check
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
const getCategoryIcon = (id: string) => {
  switch (id) {
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
        <label htmlFor="sidebar-search-input" className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-400 mb-2 px-1 block">
          Search Directory
        </label>
        <div className="relative flex items-center">
          <Search className="w-4 h-4 text-zinc-400 absolute left-3.5 pointer-events-none" aria-hidden="true" />
          <input
            id="sidebar-search-input"
            type="text"
            placeholder="Search all modules..."
            value={searchQuery}
            onChange={(e) => onSearchChange(e.target.value)}
            className="w-full pl-10 pr-8 py-2.5 bg-white hover:bg-zinc-50 focus:bg-white border border-zinc-200 focus:border-zinc-950 rounded-xl text-xs sm:text-sm font-medium text-zinc-900 placeholder-zinc-400 outline-none transition-all shadow-2xs"
          />
          {searchQuery && (
            <button
              type="button"
              onClick={() => onSearchChange('')}
              className="absolute right-2.5 p-1 rounded-md text-zinc-400 hover:text-zinc-800 hover:bg-zinc-100 transition-colors cursor-pointer"
              aria-label="Clear search input"
            >
              <X className="w-3.5 h-3.5" />
            </button>
          )}
        </div>
      </div>

      {/* ── 2. CREATIVE STUDIO WORKSPACE PRESETS (CONSOLIDATED) ── */}
      <div>
        <div className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-400 mb-2.5 px-1 flex items-center justify-between">
          <span>Studio Workspace</span>
          <span className="text-[10px] text-zinc-400 font-normal">Industry Focus</span>
        </div>

        <div className="space-y-2" role="group" aria-label="Studio workspace filters">
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
                className={`w-full p-3 rounded-2xl text-left transition-all cursor-pointer border group relative overflow-hidden ${
                  isSelected
                    ? 'bg-zinc-950 text-white border-zinc-900 shadow-sm ring-1 ring-zinc-900'
                    : 'bg-zinc-50/70 hover:bg-white border-zinc-200/80 hover:border-zinc-300 text-zinc-900'
                }`}
              >
                {/* Top Row: Icon + Title + Count */}
                <div className="flex items-center justify-between gap-2 mb-1">
                  <div className="flex items-center gap-2 min-w-0">
                    <div className={`w-6 h-6 rounded-lg flex items-center justify-center shrink-0 ${
                      isSelected ? 'bg-white/15' : 'bg-zinc-200/70 group-hover:bg-zinc-200'
                    }`}>
                      {getIndustryIcon(ind.iconName, isSelected)}
                    </div>
                    <span className="font-display text-xs font-bold truncate">
                      {ind.label}
                    </span>
                  </div>

                  <span className={`text-[10px] font-mono font-bold px-1.5 py-0.5 rounded-md shrink-0 ${
                    isSelected ? 'bg-white/20 text-white' : 'bg-zinc-200/60 text-zinc-600'
                  }`}>
                    {count}
                  </span>
                </div>

                {/* Subtitle & Badge */}
                <div className="flex items-center justify-between gap-2 pl-8">
                  <span className={`text-[10px] truncate ${
                    isSelected ? 'text-zinc-400' : 'text-zinc-500'
                  }`}>
                    {ind.subtitle}
                  </span>

                  {isSelected && (
                    <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 shrink-0 animate-pulse" />
                  )}
                </div>
              </button>
            );
          })}
        </div>
      </div>

      {/* ── 3. BUSINESS VERTICALS NAVIGATION ── */}
      <div className="pt-4 border-t border-zinc-100">
        <div className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-400 mb-2.5 px-1 flex items-center justify-between">
          <span>Business Verticals</span>
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
                className={`w-full flex items-center justify-between px-3.5 py-2 rounded-xl text-xs font-semibold transition-all cursor-pointer group text-left ${
                  isActive
                    ? 'bg-zinc-950 text-white shadow-xs font-bold'
                    : 'text-zinc-700 hover:bg-zinc-100 hover:text-zinc-950'
                }`}
              >
                <div className="flex items-center gap-2.5 min-w-0">
                  <span className={`${isActive ? 'text-white' : 'text-zinc-400 group-hover:text-zinc-700'}`}>
                    {getCategoryIcon(cat.id)}
                  </span>
                  <span className="truncate">{cat.label.replace(/\s*\(\d+\)$/, '')}</span>
                </div>

                <span className={`text-[11px] font-mono px-2 py-0.5 rounded-md shrink-0 ${
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

      {/* ── 4. RELEASE STATUS ── */}
      <div className="pt-4 border-t border-zinc-100">
        <div className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-400 mb-2.5 px-1">
          Release Status
        </div>
        
        <div className="space-y-1">
          <button
            type="button"
            onClick={() => onStatusFilterChange('all')}
            className={`w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-colors cursor-pointer ${
              statusFilter === 'all'
                ? 'bg-zinc-100 text-zinc-950 font-bold'
                : 'text-zinc-600 hover:bg-zinc-50 hover:text-zinc-900'
            }`}
          >
            <span>All Releases</span>
            <span className="text-[10px] font-mono text-zinc-400">28</span>
          </button>

          <button
            type="button"
            onClick={() => onStatusFilterChange('live')}
            className={`w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-colors cursor-pointer ${
              statusFilter === 'live'
                ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200/80'
                : 'text-zinc-600 hover:bg-zinc-50 hover:text-zinc-900'
            }`}
          >
            <div className="flex items-center gap-2">
              <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" />
              <span>Live in Product</span>
            </div>
            <span className="text-[10px] font-mono text-emerald-600 font-semibold">{totalBuiltCount}</span>
          </button>

          <button
            type="button"
            onClick={() => onStatusFilterChange('roadmap')}
            className={`w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-colors cursor-pointer ${
              statusFilter === 'roadmap'
                ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200/80'
                : 'text-zinc-600 hover:bg-zinc-50 hover:text-zinc-900'
            }`}
          >
            <div className="flex items-center gap-2">
              <span className="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse" />
              <span>In Roadmap</span>
            </div>
            <span className="text-[10px] font-mono text-amber-600 font-semibold">{totalRoadmapCount}</span>
          </button>
        </div>
      </div>

      {/* ── 5. STACK REPLACEMENT HIGHLIGHT CARD ── */}
      <div className="p-4 rounded-2xl bg-zinc-50 border border-zinc-200/80 text-zinc-900">
        <div className="flex items-center gap-2 mb-2 text-xs font-bold text-zinc-950">
          <Sparkles className="w-3.5 h-3.5 text-zinc-700" />
          <span>Save ₹35,000+ / mo</span>
        </div>
        <p className="text-[11px] text-zinc-500 leading-relaxed mb-3">
          Cora replaces 8+ separate tools: DocuSign, HubSpot, Calendly, Zoho, Typeform, and Asana into 1 subscription.
        </p>
        <Link
          href="/pricing"
          className="text-xs font-bold text-zinc-950 hover:text-zinc-600 inline-flex items-center gap-1 group"
        >
          <span>Calculate ROI</span>
          <ArrowRight className="w-3 h-3 group-hover:translate-x-0.5 transition-transform" />
        </Link>
      </div>

    </aside>
  );
}
