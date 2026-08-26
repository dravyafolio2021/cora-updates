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
  Palette
} from 'lucide-react';
import { CATEGORIES, INDUSTRIES } from '@/lib/features-data';
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
const getIndustryIcon = (iconName: string) => {
  switch (iconName) {
    case 'Camera':
      return <Camera className="w-4 h-4" />;
    case 'Clapperboard':
      return <Clapperboard className="w-4 h-4" />;
    case 'Building':
      return <Building className="w-4 h-4" />;
    case 'Heart':
      return <Heart className="w-4 h-4" />;
    case 'Palette':
      return <Palette className="w-4 h-4" />;
    case 'Briefcase':
    default:
      return <Briefcase className="w-4 h-4" />;
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
        <label htmlFor="directory-search-input" className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-400 mb-2 px-1 block">
          Search Directory
        </label>
        <div className="relative flex items-center">
          <Search className="w-4 h-4 text-zinc-400 absolute left-3.5 pointer-events-none" aria-hidden="true" />
          <input
            id="directory-search-input"
            type="text"
            placeholder="Search all 28 modules..."
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

      {/* ── 2. BUSINESS VERTICAL CATEGORIES ── */}
      <div>
        <div className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-400 mb-2.5 px-1 flex items-center justify-between">
          <span>Business Verticals</span>
          <span className="text-[10px] text-zinc-400 font-normal">28 Modules</span>
        </div>

        <nav className="space-y-1" aria-label="Feature categories">
          {CATEGORIES.map((cat) => {
            const isActive = activeCategory === cat.id;
            return (
              <button
                key={cat.id}
                type="button"
                onClick={() => onSelectCategory(cat.id)}
                className={`w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all cursor-pointer group text-left ${
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

      {/* ── 3. TARGET INDUSTRY FILTERS ── */}
      <div className="pt-4 border-t border-zinc-100">
        <div className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-400 mb-2.5 px-1 flex items-center justify-between">
          <span>Target Industry</span>
          <span className="text-[10px] text-zinc-400 font-normal">Workflows</span>
        </div>

        <div className="space-y-1" role="group" aria-label="Industry filters">
          {INDUSTRIES.map((ind) => {
            const isSelected = selectedIndustry === ind.id;
            const count = industryCounts[ind.id] ?? 28;
            return (
              <button
                key={ind.id}
                type="button"
                onClick={() => onSelectIndustry(ind.id)}
                className={`w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all cursor-pointer group text-left ${
                  isSelected
                    ? 'bg-zinc-900 text-white shadow-xs font-bold'
                    : 'text-zinc-700 hover:bg-zinc-100 hover:text-zinc-950'
                }`}
              >
                <div className="flex items-center gap-2.5 min-w-0">
                  <span className={`${isSelected ? 'text-white' : 'text-zinc-400 group-hover:text-zinc-700'}`}>
                    {getIndustryIcon(ind.iconName)}
                  </span>
                  <span className="truncate">{ind.label}</span>
                </div>

                <span className={`text-[11px] font-mono px-2 py-0.5 rounded-md shrink-0 ${
                  isSelected 
                    ? 'bg-white/20 text-white' 
                    : 'bg-zinc-100 text-zinc-500 group-hover:bg-zinc-200/70 group-hover:text-zinc-800'
                }`}>
                  {count}
                </span>
              </button>
            );
          })}
        </div>
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
