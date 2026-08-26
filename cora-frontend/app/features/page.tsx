'use client';

import React, { useState, useMemo, useEffect } from 'react';
import Link from 'next/link';
import { 
  ArrowRight, 
  CheckCircle2, 
  Search, 
  Sparkles, 
  ShieldCheck, 
  Layers, 
  Filter, 
  ArrowUpRight,
  Zap,
  Laptop,
  X,
  SlidersHorizontal,
  RotateCcw,
  Briefcase,
  Camera,
  Clapperboard,
  Building,
  Heart,
  Palette
} from 'lucide-react';
import { BUILT_MODULES, UPCOMING_MODULES, CATEGORIES, INDUSTRIES, FeatureModule } from '@/lib/features-data';
import { FeatureIcon } from '@/components/features/FeatureIcon';
import { FeaturesSidebar } from '@/components/features/FeaturesSidebar';
import { ArtisticHeroBackground } from '@/components/features/ArtisticHeroBackground';
import { trackEvent } from '@/components/analytics/Analytics';

export default function FeaturesPage() {
  const [activeCategory, setActiveCategory] = useState('all');
  const [selectedIndustry, setSelectedIndustry] = useState('all');
  const [searchQuery, setSearchQuery] = useState('');
  const [statusFilter, setStatusFilter] = useState<'all' | 'live' | 'roadmap'>('all');
  const [isMobileDrawerOpen, setIsMobileDrawerOpen] = useState(false);

  // Accessibility & iOS Mobile Scroll Lock: 100% prevent background page scrolling when drawer is open
  useEffect(() => {
    if (!isMobileDrawerOpen) return;

    const scrollY = window.scrollY;
    const originalHtmlOverflow = document.documentElement.style.overflow;
    const originalBodyOverflow = document.body.style.overflow;
    const originalBodyPosition = document.body.style.position;
    const originalBodyTop = document.body.style.top;
    const originalBodyWidth = document.body.style.width;

    document.documentElement.style.overflow = 'hidden';
    document.body.style.overflow = 'hidden';
    document.body.style.position = 'fixed';
    document.body.style.top = `-${scrollY}px`;
    document.body.style.width = '100%';

    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'Escape') {
        setIsMobileDrawerOpen(false);
      }
    };

    window.addEventListener('keydown', handleKeyDown);

    return () => {
      document.documentElement.style.overflow = originalHtmlOverflow;
      document.body.style.overflow = originalBodyOverflow;
      document.body.style.position = originalBodyPosition;
      document.body.style.top = originalBodyTop;
      document.body.style.width = originalBodyWidth;
      window.scrollTo(0, scrollY);
      window.removeEventListener('keydown', handleKeyDown);
    };
  }, [isMobileDrawerOpen]);

  // Active Category & Industry Object Helpers
  const activeCategoryObject = useMemo(() => {
    return CATEGORIES.find(c => c.id === activeCategory) || CATEGORIES[0];
  }, [activeCategory]);

  const selectedIndustryObject = useMemo(() => {
    return INDUSTRIES.find(i => i.id === selectedIndustry) || INDUSTRIES[0];
  }, [selectedIndustry]);

  // Dynamic Industry Counts
  const industryCounts = useMemo(() => {
    const counts: Record<string, number> = { all: BUILT_MODULES.length + UPCOMING_MODULES.length };
    INDUSTRIES.forEach((ind) => {
      if (ind.id === 'all') return;
      const builtMatches = BUILT_MODULES.filter(
        m => !m.industries || m.industries.includes('all') || m.industries.includes(ind.id)
      ).length;
      const roadmapMatches = UPCOMING_MODULES.filter(
        m => !m.industries || m.industries.includes('all') || m.industries.includes(ind.id)
      ).length;
      counts[ind.id] = builtMatches + roadmapMatches;
    });
    return counts;
  }, []);

  // Filtered 20 Built Modules
  const filteredBuiltModules = useMemo(() => {
    if (statusFilter === 'roadmap') return [];

    return BUILT_MODULES.filter((mod) => {
      const matchesCategory = activeCategory === 'all' 
        ? true 
        : activeCategory === 'roadmap'
          ? false
          : mod.category === activeCategory;

      const matchesIndustry = selectedIndustry === 'all'
        ? true
        : !mod.industries || mod.industries.includes('all') || mod.industries.includes(selectedIndustry);

      const q = searchQuery.toLowerCase().trim();
      const matchesSearch = !q || (
        mod.title.toLowerCase().includes(q) ||
        mod.shortTitle.toLowerCase().includes(q) ||
        mod.tagline?.toLowerCase().includes(q) ||
        mod.heroDescription?.toLowerCase().includes(q) ||
        mod.tags.some(t => t.toLowerCase().includes(q)) ||
        mod.categoryLabel.toLowerCase().includes(q)
      );

      return matchesCategory && matchesIndustry && matchesSearch;
    });
  }, [activeCategory, selectedIndustry, searchQuery, statusFilter]);

  // Filtered 8 Roadmap Modules
  const filteredRoadmapModules = useMemo(() => {
    if (statusFilter === 'live') return [];

    if (activeCategory !== 'all' && activeCategory !== 'roadmap') {
      return [];
    }

    return UPCOMING_MODULES.filter((m) => {
      const matchesIndustry = selectedIndustry === 'all'
        ? true
        : !m.industries || m.industries.includes('all') || m.industries.includes(selectedIndustry);

      const q = searchQuery.toLowerCase().trim();
      const matchesSearch = !q || (
        m.title.toLowerCase().includes(q) ||
        m.desc.toLowerCase().includes(q) ||
        m.categoryLabel.toLowerCase().includes(q)
      );

      return matchesIndustry && matchesSearch;
    });
  }, [activeCategory, selectedIndustry, searchQuery, statusFilter]);

  const totalResultsCount = filteredBuiltModules.length + filteredRoadmapModules.length;

  const handleResetFilters = () => {
    setActiveCategory('all');
    setSelectedIndustry('all');
    setSearchQuery('');
    setStatusFilter('all');
    trackEvent('features_filters_reset');
  };

  return (
    <main className="w-full relative pb-24 bg-white text-zinc-900">
      
      {/* ── 1. COMPACT HERO SECTION (MAX 40VH) ── */}
      <section className="relative w-full pt-24 sm:pt-28 pb-10 sm:pb-12 overflow-hidden">
        <ArtisticHeroBackground />

        <div className="relative z-10 w-full max-w-[1000px] mx-auto px-4 sm:px-6 text-center">
          
          {/* Status Pill */}
          <div className="inline-flex items-center gap-2 px-3 py-1 bg-white/90 backdrop-blur-md rounded-full border border-zinc-200/80 text-[11px] font-semibold text-zinc-800 mb-3.5 shadow-2xs">
            <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
            <span>20 Features Live • 8 In Roadmap</span>
          </div>

          {/* Clean, Refined Heading with Breathable Line Height */}
          <h1 className="font-display text-3xl sm:text-4xl md:text-[46px] font-semibold text-zinc-950 leading-[1.26] sm:leading-[1.32] tracking-[-0.03em] max-w-[820px] mx-auto mb-4">
            Everything you need to run your business
          </h1>

          {/* Subtitle */}
          <p className="text-zinc-600 text-xs sm:text-base font-normal leading-relaxed max-w-[620px] mx-auto mb-6">
            Client inquiries, CRM, digital contracts, billing, team workflows, and AI automations — all in one simple workspace.
          </p>

          {/* Primary Action Button */}
          <div className="flex items-center justify-center gap-3">
            <a
              href="https://app.heycora.in/workspace/login?source=features_hero"
              onClick={() => trackEvent('features_page_cta_clicked')}
              className="inline-flex items-center gap-2 bg-zinc-950 text-white px-7 py-3 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-sm group cursor-pointer"
            >
              <span>Get Started Free Forever</span>
              <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
            </a>
          </div>

        </div>
      </section>

      {/* ── 2. TWO-COLUMN DIRECTORY: LEFT PERMANENT STICKY SIDEBAR + RIGHT CARDS ── */}
      <div className="w-full max-w-[1360px] mx-auto px-4 sm:px-6 mb-24 pt-4 sm:pt-6">
        
        {/* Mobile Filter Action Header (<lg only) */}
        <div className="lg:hidden flex items-center justify-between gap-3 mb-6 pb-4 border-b border-zinc-200/80">
          <button
            type="button"
            onClick={() => {
              setIsMobileDrawerOpen(true);
              trackEvent('features_mobile_drawer_opened');
            }}
            className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-zinc-950 text-white text-xs font-semibold shadow-xs hover:bg-zinc-800 active:scale-95 transition-all cursor-pointer"
            aria-haspopup="dialog"
            aria-expanded={isMobileDrawerOpen}
          >
            <Filter className="w-3.5 h-3.5" />
            <span>Filters &amp; Workspaces</span>
            {(activeCategory !== 'all' || selectedIndustry !== 'all' || statusFilter !== 'all' || searchQuery) && (
              <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" />
            )}
          </button>

          <div className="flex items-center gap-2 text-xs font-medium text-zinc-600">
            <span>{totalResultsCount} modules</span>
            {(activeCategory !== 'all' || selectedIndustry !== 'all' || statusFilter !== 'all' || searchQuery) && (
              <button
                type="button"
                onClick={handleResetFilters}
                className="text-[11px] font-semibold text-zinc-400 hover:text-zinc-900 underline"
              >
                Reset
              </button>
            )}
          </div>
        </div>

        <div className="flex items-start gap-8 lg:gap-10 relative">
          
          {/* ── LEFT PERMANENT STICKY SIDEBAR (DESKTOP) ── */}
          <div className="hidden lg:block w-72 shrink-0 sticky top-28 self-start max-h-[calc(100vh-130px)] overflow-y-auto pr-2 scrollbar-none">
            <FeaturesSidebar
              activeCategory={activeCategory}
              onSelectCategory={setActiveCategory}
              selectedIndustry={selectedIndustry}
              onSelectIndustry={setSelectedIndustry}
              searchQuery={searchQuery}
              onSearchChange={setSearchQuery}
              statusFilter={statusFilter}
              onStatusFilterChange={setStatusFilter}
              totalBuiltCount={BUILT_MODULES.length}
              totalRoadmapCount={UPCOMING_MODULES.length}
              industryCounts={industryCounts}
            />
          </div>

          {/* ── RIGHT CONTENT: MODULE CARDS DIRECTORY & ROADMAP ── */}
          <div className="flex-1 min-w-0 space-y-16">
            
            {/* Active Scope & Filter Chips Header (Desktop / Tablet) */}
            {(activeCategory !== 'all' || selectedIndustry !== 'all' || statusFilter !== 'all' || searchQuery) && (
              <div className="hidden lg:flex items-center justify-between gap-3 p-3.5 rounded-2xl bg-zinc-50 border border-zinc-200/80 mb-2">
                <div className="flex items-center gap-2 flex-wrap text-xs">
                  <span className="font-bold text-zinc-950">Active Filters:</span>
                  
                  {selectedIndustry !== 'all' && (
                    <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-white border border-zinc-200 text-zinc-900 font-medium shadow-2xs">
                      <span>{selectedIndustryObject.label}</span>
                      <button
                        type="button"
                        onClick={() => setSelectedIndustry('all')}
                        className="hover:text-zinc-600 cursor-pointer"
                        aria-label="Clear industry"
                      >
                        <X className="w-3 h-3" />
                      </button>
                    </span>
                  )}

                  {activeCategory !== 'all' && (
                    <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-white border border-zinc-200 text-zinc-900 font-medium shadow-2xs">
                      <span>{activeCategoryObject.label.replace(/\s*\(\d+\)$/, '')}</span>
                      <button
                        type="button"
                        onClick={() => setActiveCategory('all')}
                        className="hover:text-zinc-600 cursor-pointer"
                        aria-label="Clear category"
                      >
                        <X className="w-3 h-3" />
                      </button>
                    </span>
                  )}

                  {searchQuery && (
                    <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-white border border-zinc-200 text-zinc-900 font-medium shadow-2xs">
                      <span>&ldquo;{searchQuery}&rdquo;</span>
                      <button
                        type="button"
                        onClick={() => setSearchQuery('')}
                        className="hover:text-zinc-600 cursor-pointer"
                        aria-label="Clear search"
                      >
                        <X className="w-3 h-3" />
                      </button>
                    </span>
                  )}

                  {statusFilter !== 'all' && (
                    <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-white border border-zinc-200 text-zinc-900 font-medium capitalize shadow-2xs">
                      <span>{statusFilter}</span>
                      <button
                        type="button"
                        onClick={() => setStatusFilter('all')}
                        className="hover:text-zinc-600 cursor-pointer"
                        aria-label="Clear status"
                      >
                        <X className="w-3 h-3" />
                      </button>
                    </span>
                  )}
                </div>

                <div className="flex items-center gap-3 shrink-0">
                  <span className="text-xs font-mono text-zinc-500">{totalResultsCount} modules</span>
                  <button
                    type="button"
                    onClick={handleResetFilters}
                    className="text-xs font-bold text-zinc-900 hover:text-zinc-600 flex items-center gap-1 cursor-pointer"
                  >
                    <RotateCcw className="w-3 h-3" />
                    <span>Reset</span>
                  </button>
                </div>
              </div>
            )}

            {/* 0 Results Empty State */}
            {totalResultsCount === 0 && (
              <div className="bg-zinc-50 rounded-3xl border border-dashed border-zinc-300 p-12 text-center my-8">
                <div className="w-12 h-12 rounded-2xl bg-zinc-200 text-zinc-500 flex items-center justify-center mx-auto mb-4">
                  <Search className="w-6 h-6" />
                </div>
                <h3 className="font-display text-lg font-bold text-zinc-950 mb-1">
                  No modules match your query
                </h3>
                <p className="text-xs text-zinc-500 max-w-sm mx-auto mb-6">
                  We couldn&apos;t find any modules matching &ldquo;{searchQuery || selectedIndustryObject.label}&rdquo;. Try another filter or reset.
                </p>
                <button
                  type="button"
                  onClick={handleResetFilters}
                  className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-zinc-950 text-white text-xs font-semibold hover:bg-zinc-800 transition-all shadow-xs cursor-pointer"
                >
                  <RotateCcw className="w-3.5 h-3.5" />
                  <span>Reset all filters</span>
                </button>
              </div>
            )}

            {/* ── 20 BUILT MODULES CARDS GRID ── */}
            {filteredBuiltModules.length > 0 && (
              <div>
                <div className="flex items-center justify-between mb-6">
                  <div className="flex items-center gap-2">
                    <span className="w-2 h-2 rounded-full bg-emerald-500" />
                    <h2 className="font-display text-lg sm:text-xl font-bold text-zinc-950">
                      Live Modules ({filteredBuiltModules.length})
                    </h2>
                  </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 sm:gap-6">
                  {filteredBuiltModules.map((mod) => (
                    <div
                      key={mod.slug}
                      className="bg-white rounded-[22px] border border-zinc-200/90 p-5 sm:p-6 flex flex-col justify-between hover:shadow-[0_12px_28px_rgba(0,0,0,0.06)] hover:border-zinc-300 transition-all group"
                    >
                      <div className="space-y-3.5">
                        
                        {/* Top Bar: Icon + Live Badge */}
                        <div className="flex items-start justify-between gap-3">
                          <div className="w-10 h-10 rounded-xl bg-zinc-950 text-white flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform">
                            <FeatureIcon name={mod.iconName} className="w-5 h-5" />
                          </div>
                          <span className="text-[10px] font-mono font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/80 px-2 py-0.5 rounded-md flex items-center gap-1">
                            <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" />
                            LIVE
                          </span>
                        </div>

                        {/* Category & Title */}
                        <div>
                          <span className="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block mb-1">
                            {mod.categoryLabel}
                          </span>
                          <h3 className="font-display text-base font-bold text-zinc-950 leading-snug group-hover:text-zinc-800 transition-colors">
                            {mod.title}
                          </h3>
                        </div>

                        {/* Description */}
                        <p className="text-zinc-600 text-xs leading-relaxed line-clamp-3">
                          {mod.tagline || mod.heroDescription}
                        </p>

                        {/* Capability Tags */}
                        <div className="flex flex-wrap gap-1.5 pt-1">
                          {mod.tags.slice(0, 3).map((tag) => (
                            <span
                              key={tag}
                              className="text-[10px] font-medium bg-zinc-100 text-zinc-700 px-2 py-0.5 rounded-md border border-zinc-200/60"
                            >
                              {tag}
                            </span>
                          ))}
                        </div>

                      </div>

                      {/* Footer Action Links */}
                      <div className="pt-4 border-t border-zinc-100 mt-4 flex items-center justify-between gap-3">
                        <Link
                          href={`/features/${mod.slug}`}
                          className="inline-flex items-center gap-1.5 text-xs font-bold text-zinc-950 hover:text-zinc-600 transition-colors group-hover:translate-x-0.5 transition-transform"
                        >
                          <span>Explore Feature</span>
                          <ArrowRight className="w-3.5 h-3.5" />
                        </Link>

                        <a
                          href={`https://app.heycora.in/workspace/login?feature=${mod.slug}`}
                          className="text-[11px] font-medium text-zinc-400 hover:text-zinc-700 transition-colors"
                        >
                          Launch Free ↗
                        </a>
                      </div>

                    </div>
                  ))}
                </div>
              </div>
            )}

            {/* ── 8 UPCOMING ROADMAP MODULES ── */}
            {filteredRoadmapModules.length > 0 && (
              <div className="bg-zinc-950 rounded-[32px] p-6 sm:p-10 text-white border border-zinc-800 shadow-2xl relative overflow-hidden">
                
                <div className="max-w-[640px] mb-8">
                  <div className="inline-flex items-center gap-2 px-3 py-1 bg-amber-500/10 rounded-xl border border-amber-500/30 text-xs font-semibold text-amber-300 mb-3">
                    <span className="w-2 h-2 rounded-full bg-amber-400 animate-pulse" />
                    <span>Active Product Roadmap</span>
                  </div>
                  <h2 className="font-display text-xl sm:text-3xl font-bold tracking-tight mb-2">
                    Upcoming Modules &amp; Enterprise Tools
                  </h2>
                  <p className="text-zinc-400 text-xs sm:text-sm leading-relaxed">
                    Currently in private alpha with high-volume photography and film studios.
                  </p>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                  {filteredRoadmapModules.map((item) => (
                    <div
                      key={item.id}
                      className="bg-zinc-900/80 rounded-2xl p-4.5 border border-zinc-800 flex flex-col justify-between hover:border-zinc-700 transition-all group"
                    >
                      <div className="space-y-2.5">
                        <div className="flex items-center justify-between">
                          <div className="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center border border-amber-500/20">
                            <FeatureIcon name={item.iconName} className="w-4 h-4" />
                          </div>
                          <span className="text-[10px] font-mono font-bold text-amber-300 bg-amber-500/20 px-2 py-0.5 rounded-full border border-amber-500/30">
                            {item.eta}
                          </span>
                        </div>

                        <div>
                          <span className="text-[9px] font-mono font-bold text-zinc-500 uppercase tracking-wider block mb-0.5">
                            {item.categoryLabel}
                          </span>
                          <h4 className="font-display text-xs sm:text-sm font-bold text-white leading-snug">
                            {item.title}
                          </h4>
                        </div>

                        <p className="text-zinc-400 text-xs leading-relaxed line-clamp-3">
                          {item.desc}
                        </p>
                      </div>

                      <div className="pt-3 mt-3 border-t border-zinc-800/80 flex items-center justify-between text-[11px] text-zinc-500">
                        <span>Status</span>
                        <span className="text-amber-400 font-semibold flex items-center gap-1">
                          <span className="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse" />
                          {item.status}
                        </span>
                      </div>
                    </div>
                  ))}
                </div>

              </div>
            )}

          </div>

        </div>

      </div>

      {/* ── 3. MOBILE ACCESSIBLE BOTTOM SHEET DRAWER (100% BACKGROUND SCROLL LOCKED) ── */}
      {isMobileDrawerOpen && (
        <div 
          className="fixed inset-0 z-50 lg:hidden flex flex-col justify-end"
          role="dialog"
          aria-modal="true"
          aria-labelledby="mobile-filters-title"
        >
          {/* Darkened Backdrop (prevents touch propagation) */}
          <div 
            className="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity duration-300 animate-in fade-in" 
            onClick={() => setIsMobileDrawerOpen(false)}
            onTouchMove={(e) => e.preventDefault()}
            aria-hidden="true"
          />

          {/* Sliding Bottom Sheet Container */}
          <div 
            className="relative w-full max-h-[85vh] bg-white rounded-t-[28px] shadow-[0_-12px_40px_rgba(0,0,0,0.25)] border-t border-zinc-200 z-10 flex flex-col overflow-hidden animate-in slide-in-from-bottom duration-300 ease-out"
            style={{ touchAction: 'pan-y' }}
          >
            
            {/* Top Drag Indicator Handle */}
            <div className="w-full pt-3 pb-1 flex items-center justify-center cursor-grab active:cursor-grabbing shrink-0">
              <span className="w-12 h-1.5 rounded-full bg-zinc-300 shrink-0" aria-hidden="true" />
            </div>

            {/* Header: Title + Scope Count + Reset + Close Button */}
            <div className="flex items-center justify-between px-5 py-3 border-b border-zinc-100 shrink-0">
              <div className="flex items-center gap-2">
                <Filter className="w-4 h-4 text-zinc-900" />
                <h2 id="mobile-filters-title" className="font-display text-base font-bold text-zinc-950">
                  Filters &amp; Workspaces
                </h2>
                <span className="text-[11px] font-mono font-semibold text-zinc-500 bg-zinc-100 px-2 py-0.5 rounded-md">
                  {totalResultsCount}
                </span>
              </div>

              <div className="flex items-center gap-3">
                {(activeCategory !== 'all' || selectedIndustry !== 'all' || statusFilter !== 'all' || searchQuery) && (
                  <button
                    type="button"
                    onClick={handleResetFilters}
                    className="text-xs font-semibold text-zinc-500 hover:text-zinc-950 transition-colors cursor-pointer"
                  >
                    Reset all
                  </button>
                )}

                <button 
                  type="button"
                  onClick={() => setIsMobileDrawerOpen(false)}
                  className="w-8 h-8 rounded-full bg-zinc-100 hover:bg-zinc-200 text-zinc-600 hover:text-zinc-950 flex items-center justify-center transition-colors cursor-pointer"
                  aria-label="Close filters drawer"
                >
                  <X className="w-4 h-4" />
                </button>
              </div>
            </div>

            {/* Scrollable Content Body (ONLY this container scrolls) */}
            <div 
              className="flex-1 overflow-y-auto overscroll-contain px-5 py-4 space-y-6"
              style={{ WebkitOverflowScrolling: 'touch' }}
            >
              <FeaturesSidebar 
                activeCategory={activeCategory}
                onSelectCategory={setActiveCategory}
                selectedIndustry={selectedIndustry}
                onSelectIndustry={setSelectedIndustry}
                searchQuery={searchQuery}
                onSearchChange={setSearchQuery}
                statusFilter={statusFilter}
                onStatusFilterChange={setStatusFilter}
                totalBuiltCount={BUILT_MODULES.length}
                totalRoadmapCount={UPCOMING_MODULES.length}
                industryCounts={industryCounts}
                onCloseMobileDrawer={() => setIsMobileDrawerOpen(false)}
              />
            </div>

            {/* Sticky Bottom Action Bar with Safe Area */}
            <div className="p-4 bg-white/95 backdrop-blur-md border-t border-zinc-100 shrink-0 pb-6 sm:pb-4">
              <button
                type="button"
                onClick={() => setIsMobileDrawerOpen(false)}
                className="w-full py-3.5 bg-zinc-950 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md hover:bg-zinc-800 active:scale-[0.99] transition-all cursor-pointer flex items-center justify-center gap-2"
              >
                <span>View {totalResultsCount} Modules</span>
                <ArrowRight className="w-4 h-4 text-zinc-400" />
              </button>
            </div>

          </div>
        </div>
      )}

      {/* ── 4. WHY CORA REPLACES 8+ SUBSCRIPTIONS ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-28 text-center">
        <div className="max-w-[720px] mx-auto mb-14">
          <h2 className="font-display text-3xl sm:text-4xl font-bold text-zinc-950 tracking-tight mb-4">
            Replace 8+ fragmented subscriptions with one unified stack
          </h2>
          <p className="text-zinc-600 text-sm sm:text-base leading-relaxed">
            Stop paying ₹35,000+ monthly across separate CRM, invoicing, e-sign, cloud storage, scheduling, and AI tools.
          </p>
        </div>

        <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 max-w-[980px] mx-auto text-left">
          <div className="p-5 rounded-2xl bg-zinc-50 border border-zinc-200/80">
            <span className="text-xs font-mono font-bold text-rose-500 line-through block mb-1">DocuSign / PandaDoc</span>
            <Link href="/features/esign-vault" className="text-xs font-bold text-zinc-900 flex items-center gap-1.5 hover:text-zinc-600">
              <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600 shrink-0" /> Cora SHA-256 E-Signs
            </Link>
          </div>

          <div className="p-5 rounded-2xl bg-zinc-50 border border-zinc-200/80">
            <span className="text-xs font-mono font-bold text-rose-500 line-through block mb-1">HubSpot / Pipedrive</span>
            <Link href="/features/lead-crm" className="text-xs font-bold text-zinc-900 flex items-center gap-1.5 hover:text-zinc-600">
              <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600 shrink-0" /> Cora Kanban CRM
            </Link>
          </div>

          <div className="p-5 rounded-2xl bg-zinc-50 border border-zinc-200/80">
            <span className="text-xs font-mono font-bold text-rose-500 line-through block mb-1">Calendly / Acuity</span>
            <Link href="/features/master-calendar" className="text-xs font-bold text-zinc-900 flex items-center gap-1.5 hover:text-zinc-600">
              <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600 shrink-0" /> Cora Booking Calendar
            </Link>
          </div>

          <div className="p-5 rounded-2xl bg-zinc-50 border border-zinc-200/80">
            <span className="text-xs font-mono font-bold text-rose-500 line-through block mb-1">Zoho / FreshBooks</span>
            <Link href="/features/gst-invoicing" className="text-xs font-bold text-zinc-900 flex items-center gap-1.5 hover:text-zinc-600">
              <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600 shrink-0" /> Cora 18% GST Invoicing
            </Link>
          </div>
        </div>
      </section>

      {/* ── 5. BOTTOM CALL TO ACTION ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        <div className="w-full rounded-[36px] bg-gradient-to-br from-[#0F172A] via-[#1E293B] to-[#0A0D12] text-white p-8 sm:p-14 text-center relative overflow-hidden border border-zinc-800 shadow-xl">
          <div className="relative z-10 max-w-[680px] mx-auto space-y-6">
            <h2 className="font-display text-3xl sm:text-4xl font-bold tracking-tight">
              Ready to run your studio on Cora OS?
            </h2>
            <p className="text-zinc-400 text-sm sm:text-base leading-relaxed font-normal">
              Activate your workspace now with 1,000 free operations and full access to all 20 built modules. No credit card required.
            </p>

            <div className="flex items-center justify-center flex-wrap gap-3.5 pt-2">
              <a
                href="https://app.heycora.in/workspace/login?source=features_bottom"
                className="inline-flex items-center gap-2 bg-white text-zinc-950 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-100 transition-all shadow-sm group cursor-pointer"
              >
                <span>Get started for Free</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-600 group-hover:translate-x-0.5 transition-transform" />
              </a>

              <Link
                href="/pricing"
                className="inline-flex items-center gap-2 bg-zinc-900 text-white border border-zinc-700 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-2xs"
              >
                <span>Explore Plans</span>
              </Link>
            </div>
          </div>
        </div>
      </section>

    </main>
  );
}
