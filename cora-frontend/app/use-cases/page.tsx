'use client';

import React, { useState, useMemo } from 'react';
import type { Metadata } from 'next';
import { 
  INDUSTRY_WORKSPACES, 
  SECTOR_CATEGORIES, 
  IndustryWorkspace 
} from '@/lib/industry-data';
import { IndustryHero } from '@/components/industry/IndustryHero';
import { IndustryFilterSidebar } from '@/components/industry/IndustryFilterSidebar';
import { IndustryCard } from '@/components/industry/IndustryCard';
import { IndustryDetailModal } from '@/components/industry/IndustryDetailModal';
import { IndustryComparisonTable } from '@/components/industry/IndustryComparisonTable';
import { IndustryCtaBanner } from '@/components/industry/IndustryCtaBanner';
import { Filter, Search, RotateCcw } from 'lucide-react';

export default function UseCasesPage() {
  const [activeSector, setActiveSector] = useState('all');
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedWorkspace, setSelectedWorkspace] = useState<IndustryWorkspace | null>(null);
  const [isDetailModalOpen, setIsDetailModalOpen] = useState(false);
  const [isMobileFilterOpen, setIsMobileFilterOpen] = useState(false);

  // Filtered industry workspaces based on active sector and search query
  const filteredWorkspaces = useMemo(() => {
    return INDUSTRY_WORKSPACES.filter((ws) => {
      const matchesSector = activeSector === 'all' || ws.sectorId === activeSector;

      const q = searchQuery.toLowerCase().trim();
      if (!q) return matchesSector;

      const matchesSearch =
        ws.title.toLowerCase().includes(q) ||
        ws.tagline.toLowerCase().includes(q) ||
        ws.heroDescription.toLowerCase().includes(q) ||
        ws.sacCode.toLowerCase().includes(q) ||
        ws.sectorLabel.toLowerCase().includes(q) ||
        ws.preSeededTemplates.some(t => t.toLowerCase().includes(q)) ||
        ws.workflowHighlights.some(w => w.toLowerCase().includes(q)) ||
        ws.recommendedModules.some(m => m.title.toLowerCase().includes(q));

      return matchesSector && matchesSearch;
    });
  }, [activeSector, searchQuery]);

  const handleOpenDetails = (ws: IndustryWorkspace) => {
    setSelectedWorkspace(ws);
    setIsDetailModalOpen(true);
  };

  const handleCloseDetails = () => {
    setIsDetailModalOpen(false);
    setSelectedWorkspace(null);
  };

  const handleResetFilters = () => {
    setActiveSector('all');
    setSearchQuery('');
  };

  return (
    <main className="w-full relative bg-white text-zinc-900 min-h-screen">
      
      {/* ── 1. HERO SECTION WITH SEARCH & QUICK SECTOR PILLS ── */}
      <IndustryHero
        searchQuery={searchQuery}
        onSearchChange={setSearchQuery}
        activeSector={activeSector}
        onSectorChange={setActiveSector}
      />

      {/* ── 2. MAIN WORKSPACE EXPLORER SECTION ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 py-12 sm:py-16">
        
        {/* Mobile Filter Trigger Bar */}
        <div className="lg:hidden flex items-center justify-between p-3.5 bg-zinc-50 rounded-2xl border border-zinc-200 mb-6">
          <div className="flex items-center gap-2 text-xs font-semibold text-zinc-800">
            <span>Showing {filteredWorkspaces.length} of {INDUSTRY_WORKSPACES.length} Workspaces</span>
          </div>
          <button
            onClick={() => setIsMobileFilterOpen(true)}
            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-zinc-200 text-xs font-semibold text-zinc-900 shadow-2xs hover:bg-zinc-100 transition-colors"
          >
            <Filter className="w-3.5 h-3.5" />
            <span>Filter Sectors</span>
          </button>
        </div>

        {/* Desktop 2-Column Layout */}
        <div className="flex flex-col lg:flex-row gap-8 xl:gap-10 items-start">
          
          {/* Left Sticky Filter Sidebar */}
          <div className="hidden lg:block sticky top-28">
            <IndustryFilterSidebar
              activeSector={activeSector}
              onSectorChange={setActiveSector}
              totalFilteredCount={filteredWorkspaces.length}
              totalWorkspacesCount={INDUSTRY_WORKSPACES.length}
              onResetFilters={handleResetFilters}
            />
          </div>

          {/* Right Main Grid */}
          <div className="flex-1 w-full min-w-0">
            
            {/* Results Count Header */}
            <div className="hidden lg:flex items-center justify-between pb-4 mb-6 border-b border-zinc-100">
              <span className="text-xs font-mono font-semibold text-zinc-500">
                Displaying {filteredWorkspaces.length} pre-configured business solutions
              </span>
              {activeSector !== 'all' && (
                <button
                  onClick={handleResetFilters}
                  className="text-xs font-semibold text-zinc-600 hover:text-zinc-950 inline-flex items-center gap-1 transition-colors"
                >
                  <RotateCcw className="w-3 h-3" />
                  <span>Show all 16 industries</span>
                </button>
              )}
            </div>

            {/* Empty State if No Results */}
            {filteredWorkspaces.length === 0 ? (
              <div className="text-center py-16 px-4 rounded-3xl bg-zinc-50 border border-zinc-200/80">
                <Search className="w-8 h-8 text-zinc-400 mx-auto mb-3" />
                <h3 className="text-base font-bold text-zinc-950 mb-1">
                  No matching industry workspaces found
                </h3>
                <p className="text-xs text-zinc-500 max-w-sm mx-auto mb-5">
                  We couldn&apos;t find any workspaces matching &ldquo;{searchQuery}&rdquo;. Try searching for another vertical or reset filters.
                </p>
                <button
                  onClick={handleResetFilters}
                  className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-zinc-950 text-white text-xs font-semibold hover:bg-zinc-800 transition-all"
                >
                  <RotateCcw className="w-3.5 h-3.5" />
                  <span>Reset All Filters</span>
                </button>
              </div>
            ) : (
              /* Grid of Industry Cards (2 columns on md/xl) */
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {filteredWorkspaces.map((ws) => (
                  <IndustryCard
                    key={ws.id}
                    workspace={ws}
                    onOpenDetails={handleOpenDetails}
                  />
                ))}
              </div>
            )}

          </div>

        </div>

      </section>

      {/* ── 3. COMPARISON BREAKDOWN TABLE ── */}
      <IndustryComparisonTable />

      {/* ── 4. CALL TO ACTION BANNER ── */}
      <IndustryCtaBanner />

      {/* ── 5. DEEP-DIVE MODAL DRAWER ── */}
      <IndustryDetailModal
        workspace={selectedWorkspace}
        isOpen={isDetailModalOpen}
        onClose={handleCloseDetails}
      />

      {/* ── 6. MOBILE FILTER BOTTOM-SHEET DRAWER ── */}
      {isMobileFilterOpen && (
        <div className="fixed inset-0 z-50 flex items-end lg:hidden">
          <div
            className="fixed inset-0 bg-black/50 backdrop-blur-sm"
            onClick={() => setIsMobileFilterOpen(false)}
          />
          <div className="relative z-10 w-full bg-white rounded-t-3xl p-6 shadow-2xl max-h-[80vh] overflow-y-auto animate-in slide-in-from-bottom duration-300">
            <div className="w-10 h-1 bg-zinc-300 rounded-full mx-auto mb-5" />
            <IndustryFilterSidebar
              activeSector={activeSector}
              onSectorChange={setActiveSector}
              totalFilteredCount={filteredWorkspaces.length}
              totalWorkspacesCount={INDUSTRY_WORKSPACES.length}
              onResetFilters={handleResetFilters}
              isMobileDrawer={true}
              onCloseMobileDrawer={() => setIsMobileFilterOpen(false)}
            />
          </div>
        </div>
      )}

    </main>
  );
}
