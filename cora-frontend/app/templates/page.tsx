'use client';

import React, { useState, useMemo } from 'react';
import Link from 'next/link';
import { 
  Sparkles, 
  ArrowRight, 
  CheckCircle2, 
  Plus, 
  Minus, 
  RotateCcw, 
  Search, 
  Layers, 
  Code2, 
  Bot, 
  LayoutTemplate,
  Copy,
  Check 
} from 'lucide-react';
import { 
  TEMPLATES_DATA, 
  TEMPLATE_CATEGORIES, 
  TemplateItem 
} from '@/lib/templates-data';
import { TemplateHero } from '@/components/templates/TemplateHero';
import { TemplateCard } from '@/components/templates/TemplateCard';
import { TemplatePreviewModal } from '@/components/templates/TemplatePreviewModal';

const TEMPLATES_FAQS = [
  {
    question: 'What types of Frontend Assets are included in this library?',
    answer: 'Our library contains 4 main formats: (1) Native Tailwind CSS & Next.js full landing pages, (2) Master AI section generation prompts engineered for Claude 3.5 Sonnet, Cursor AI, and v0, (3) Importable Elementor Pro Flexbox container JSON kits, and (4) 1-click forkable Lovable.dev fullstack React projects.'
  },
  {
    question: 'How do I use the AI Section Prompts in Cursor or Claude?',
    answer: 'Click "Copy Prompt" on any section (Hero, Pricing Matrix, Bento Grid, Form Drawer), open Cursor Composer (⌘I) or Claude 3.5 Sonnet, and paste. The prompts are pre-engineered with layout constraints, accessibility classes, and typography tokens to ensure zero AI hallucinations.'
  },
  {
    question: 'How do I import Elementor Template Kits?',
    answer: 'In your WordPress dashboard with Elementor installed, go to Elementor > Tools > Import / Export Kit. Upload the downloaded JSON template blueprint or copy the container structure directly into Elementor.'
  },
  {
    question: 'Are all these frontend assets and prompts free?',
    answer: 'Yes, 100% free and open-source under the MIT license. You can use them for commercial client projects, SaaS landing pages, agency websites, and portfolio builds without any attribution or licensing fees.'
  }
];

export default function TemplatesHubPage() {
  const [activeCategory, setActiveCategory] = useState<string>('all');
  const [searchQuery, setSearchQuery] = useState<string>('');
  const [previewTemplate, setPreviewTemplate] = useState<TemplateItem | null>(null);
  const [isPreviewModalOpen, setIsPreviewModalOpen] = useState(false);
  const [openFaq, setOpenFaq] = useState<number | null>(0);

  const filteredTemplates = useMemo(() => {
    return TEMPLATES_DATA.filter((tmpl) => {
      const matchesCategory = activeCategory === 'all' || tmpl.category === activeCategory;
      const q = searchQuery.toLowerCase().trim();
      if (!q) return matchesCategory;

      const matchesSearch =
        tmpl.title.toLowerCase().includes(q) ||
        tmpl.shortTitle.toLowerCase().includes(q) ||
        tmpl.tagline.toLowerCase().includes(q) ||
        tmpl.heroDescription.toLowerCase().includes(q) ||
        tmpl.categoryLabel.toLowerCase().includes(q) ||
        tmpl.formatBadge.toLowerCase().includes(q) ||
        tmpl.compatibleBuilders.some(b => b.toLowerCase().includes(q)) ||
        tmpl.features.some(f => f.toLowerCase().includes(q)) ||
        tmpl.sectionsIncluded.some(s => s.name.toLowerCase().includes(q));

      return matchesCategory && matchesSearch;
    });
  }, [activeCategory, searchQuery]);

  const handleOpenPreview = (tmpl: TemplateItem) => {
    setPreviewTemplate(tmpl);
    setIsPreviewModalOpen(true);
  };

  const handleClosePreview = () => {
    setIsPreviewModalOpen(false);
    setPreviewTemplate(null);
  };

  const handleResetFilters = () => {
    setActiveCategory('all');
    setSearchQuery('');
  };

  const toggleFaq = (idx: number) => {
    setOpenFaq(openFaq === idx ? null : idx);
  };

  return (
    <div className="w-full bg-white text-zinc-900 selection:bg-zinc-900 selection:text-white">
      
      {/* ── 1. HERO ── */}
      <TemplateHero />

      {/* ── 2. DIRECTORY & FILTER MATRIX ── */}
      <section id="templates-directory" className="w-full py-8 sm:py-12 max-w-[1240px] mx-auto px-4 sm:px-6">
        
        {/* Sleek Search & Category Filter Toolbar */}
        <div className="flex flex-col gap-5 mb-8 pb-6 border-b border-zinc-200/80">
          
          <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
              <h2 className="font-display text-xl sm:text-2xl lg:text-3xl font-bold text-zinc-950 tracking-tight">
                Discover Design Prompts &amp; Templates
              </h2>
              <p className="text-xs sm:text-sm text-zinc-500 mt-1">
                Showing {filteredTemplates.length} of {TEMPLATES_DATA.length} production-ready standalone HTML templates and prompts
              </p>
            </div>

            {/* Direct Search Input (Positioned right at the library) */}
            <div className="relative w-full md:w-80 shrink-0">
              <div className="relative flex items-center bg-zinc-50 hover:bg-zinc-100/80 focus-within:bg-white focus-within:ring-2 focus-within:ring-zinc-950/10 border border-zinc-200/90 rounded-2xl px-3.5 py-2.5 transition-all shadow-2xs">
                <Search className="w-4 h-4 text-zinc-400 mr-2.5 shrink-0" />
                <input
                  type="text"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  placeholder="Search prompts e.g. Hero, Dock, Bento..."
                  className="w-full bg-transparent text-xs sm:text-sm text-zinc-950 placeholder:text-zinc-400 focus:outline-none font-sans"
                />
                {searchQuery && (
                  <button
                    type="button"
                    onClick={() => setSearchQuery('')}
                    className="text-xs font-mono text-zinc-400 hover:text-zinc-900 px-1.5 py-0.5 rounded cursor-pointer"
                  >
                    ✕
                  </button>
                )}
              </div>
            </div>
          </div>

          {/* Category Filter Pills Bar */}
          <div className="flex items-center gap-2 overflow-x-auto whitespace-nowrap scrollbar-none py-1">
            {TEMPLATE_CATEGORIES.map((cat) => {
              const isActive = activeCategory === cat.id;
              return (
                <button
                  key={cat.id}
                  onClick={() => setActiveCategory(cat.id)}
                  className={`inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold transition-all cursor-pointer ${
                    isActive
                      ? 'bg-zinc-950 text-white shadow-xs'
                      : 'bg-white text-zinc-700 border border-zinc-200/90 hover:bg-zinc-100 hover:text-zinc-950'
                  }`}
                >
                  <span>{cat.label}</span>
                  <span className={`text-[10px] font-mono rounded-full px-2 py-0.5 ${isActive ? 'bg-zinc-800 text-zinc-300' : 'bg-zinc-100 text-zinc-600'}`}>
                    {cat.count}
                  </span>
                </button>
              );
            })}
          </div>

        </div>

        {/* Templates Cards Grid */}
        {filteredTemplates.length === 0 ? (
          <div className="text-center py-16 px-4 bg-zinc-50 rounded-3xl border border-zinc-200">
            <Search className="w-8 h-8 text-zinc-400 mx-auto mb-3" />
            <h3 className="text-base font-bold text-zinc-950 mb-1">
              No matching frontend assets found
            </h3>
            <p className="text-xs text-zinc-500 max-w-sm mx-auto mb-4">
              We couldn&apos;t find any assets matching &ldquo;{searchQuery}&rdquo;. Try another term or reset filters.
            </p>
            <button
              onClick={handleResetFilters}
              className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-zinc-950 text-white text-xs font-semibold hover:bg-zinc-800 transition-all cursor-pointer"
            >
              <RotateCcw className="w-3.5 h-3.5" />
              <span>Reset All Filters</span>
            </button>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
            {filteredTemplates.map((template) => (
              <TemplateCard
                key={template.id}
                template={template}
                onQuickPreview={handleOpenPreview}
              />
            ))}
          </div>
        )}

      </section>

      {/* ── 4. "CORA FRONTEND ASSETS VS BLOATED THEMES" COMPARISON ── */}
      <section className="w-full py-14 sm:py-20 bg-zinc-50/80 border-t border-zinc-200/80 mt-12 sm:mt-16">
        <div className="max-w-[1100px] mx-auto px-4 sm:px-6">
          
          <div className="text-center max-w-2xl mx-auto mb-10 sm:mb-12">
            <span className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-500 bg-zinc-100 px-3 py-1 rounded-full border border-zinc-200 mb-3 inline-block">
              Builder Architecture
            </span>
            <h2 className="font-display text-2xl sm:text-4xl font-bold text-zinc-950 tracking-tight mb-3">
              Why Cora Frontend Assets Build Faster
            </h2>
            <p className="text-xs sm:text-base text-zinc-600 font-normal leading-relaxed">
              Traditional marketplace themes lock you into bloated code. Cora provides clean Tailwind, ready AI section prompts, Elementor kits, and Lovable repositories.
            </p>
          </div>

          <div className="rounded-2xl sm:rounded-3xl bg-white border border-zinc-200/90 overflow-hidden shadow-sm">
            <div className="overflow-x-auto">
              <table className="w-full text-left text-xs sm:text-sm">
                <thead className="bg-zinc-100/80 text-zinc-700 font-mono text-[11px] uppercase tracking-wider border-b border-zinc-200">
                  <tr>
                    <th className="p-4 sm:p-5">Asset Feature</th>
                    <th className="p-4 sm:p-5 text-zinc-500 font-normal">Generic Marketplace Themes</th>
                    <th className="p-4 sm:p-5 text-zinc-950 font-bold bg-purple-50/50">Cora Frontend Assets</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-zinc-100">
                  <tr className="hover:bg-zinc-50/50">
                    <td className="p-4 sm:p-5 font-bold text-zinc-900">AI Prompt Engineering</td>
                    <td className="p-4 sm:p-5 text-zinc-600">No prompts; manual customization only</td>
                    <td className="p-4 sm:p-5 font-semibold text-purple-950 bg-purple-50/30">Engineered prompts for Claude 3.5 Sonnet, Cursor &amp; v0</td>
                  </tr>
                  <tr className="hover:bg-zinc-50/50">
                    <td className="p-4 sm:p-5 font-bold text-zinc-900">Elementor Compatibility</td>
                    <td className="p-4 sm:p-5 text-zinc-600">Requires 10+ paid third-party add-on plugins</td>
                    <td className="p-4 sm:p-5 font-semibold text-purple-950 bg-purple-50/30">100% Flexbox container JSON kits with zero bloat</td>
                  </tr>
                  <tr className="hover:bg-zinc-50/50">
                    <td className="p-4 sm:p-5 font-bold text-zinc-900">Lovable &amp; v0 Ready</td>
                    <td className="p-4 sm:p-5 text-zinc-600">Non-existent or incompatible legacy PHP files</td>
                    <td className="p-4 sm:p-5 font-semibold text-purple-950 bg-purple-50/30">1-Click forkable React &amp; Lovable.dev web apps</td>
                  </tr>
                  <tr className="hover:bg-zinc-50/50">
                    <td className="p-4 sm:p-5 font-bold text-zinc-900">Performance &amp; Speed</td>
                    <td className="p-4 sm:p-5 text-zinc-600">Slow TTFB with bulky JS bundles (2MB+)</td>
                    <td className="p-4 sm:p-5 font-semibold text-purple-950 bg-purple-50/30">Sub-50ms static speed, 0.00 CLS &amp; pure Tailwind</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </section>

      {/* ── 5. FAQ ACCORDION ── */}
      <section className="w-full py-12 sm:py-20 max-w-[860px] mx-auto px-4 sm:px-6">
        <div className="text-center mb-8 sm:mb-10">
          <span className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-500 bg-zinc-100 px-3 py-1 rounded-full border border-zinc-200 mb-2.5 sm:mb-3 inline-block">
            Frequently Asked Questions
          </span>
          <h2 className="font-display text-xl sm:text-3xl font-bold text-zinc-950 tracking-tight">
            Common questions about frontend assets &amp; templates
          </h2>
        </div>

        <div className="space-y-2.5 sm:space-y-3">
          {TEMPLATES_FAQS.map((faq, idx) => {
            const isOpen = openFaq === idx;
            return (
              <div
                key={idx}
                className="rounded-2xl bg-white border border-zinc-200/90 overflow-hidden shadow-2xs"
              >
                <button
                  onClick={() => toggleFaq(idx)}
                  className="w-full p-4 sm:p-5 flex items-center justify-between text-left gap-4 hover:bg-zinc-50 transition-colors cursor-pointer"
                >
                  <span className="text-xs sm:text-sm font-bold text-zinc-950">
                    {faq.question}
                  </span>
                  <span className="p-1 rounded-lg bg-zinc-100 text-zinc-500 shrink-0">
                    {isOpen ? <Minus className="w-3.5 h-3.5" /> : <Plus className="w-3.5 h-3.5" />}
                  </span>
                </button>

                {isOpen && (
                  <div className="px-4 sm:px-5 pb-4 sm:pb-5 text-xs sm:text-sm text-zinc-600 leading-relaxed border-t border-zinc-100 pt-3">
                    {faq.answer}
                  </div>
                )}
              </div>
            );
          })}
        </div>
      </section>

      {/* ── 6. QUICK PREVIEW MODAL ── */}
      <TemplatePreviewModal
        template={previewTemplate}
        isOpen={isPreviewModalOpen}
        onClose={handleClosePreview}
      />

    </div>
  );
}
