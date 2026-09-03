'use client';

import React, { useState, useMemo, useEffect } from 'react';
import Link from 'next/link';
import { 
  Sparkles, 
  ArrowRight, 
  CheckCircle2, 
  ShieldCheck, 
  Plus, 
  Minus,
  Zap,
  Lock,
  Check
} from 'lucide-react';
import { 
  TOOLS_DATA, 
  TOOL_CATEGORY_BLOCKS, 
  TOOL_CATEGORIES, 
  ToolItem 
} from '@/lib/tools-data';
import { ToolCard } from '@/components/tools/ToolCard';
import { ToolCategoryHeroCard } from '@/components/tools/ToolCategoryHeroCard';
import { ToolsHeroAIInput } from '@/components/tools/ToolsHeroAIInput';
import { ArtisticHeroBackground } from '@/components/features/ArtisticHeroBackground';

const ROTATING_ITEMS = [
  { word: 'Operations', curveColor: '#2563EB' },
  { word: 'Marketing', curveColor: '#E11D48' },
  { word: 'Finance', curveColor: '#059669' },
  { word: 'Contracts', curveColor: '#4F46E5' },
  { word: 'Productivity', curveColor: '#7C3AED' },
  { word: 'Invoicing', curveColor: '#D97706' },
];

const TOOLS_FAQS = [
  {
    question: 'Are these tools really 100% free with no hidden paywalls?',
    answer: 'Yes, absolutely free forever. There are zero daily limits, zero login gates, and no credit card is ever requested. We believe essential business utilities—like 18% GST math, IT Act contracts, and UPI links—should be open and accessible to every Indian entrepreneur.'
  },
  {
    question: 'Do these tools store or log my financial numbers or client names?',
    answer: 'No. All calculations (such as GST splits, retainer formulas, UPI intent links, and legal clauses) are executed 100% client-side inside your browser. No financial numbers or client entities are transmitted or stored on any server.'
  },
  {
    question: 'How do these free micro-tools connect to the Cora Workspace platform?',
    answer: 'These standalone micro-tools solve immediate ad-hoc tasks for free. When your agency grows and you want these calculations, contracts, dynamic UPI invoices, and client portals automated on autopilot, you can launch a Cora Workspace in 3 minutes.'
  },
  {
    question: 'Are the contract clauses legally valid under Indian law?',
    answer: 'Yes. All generated legal clauses conform to Section 10A of the Information Technology Act 2000 regarding the validity of contracts formed through electronic means, complete with SHA-256 tamper-evident digital signature provisions.'
  }
];

export default function ToolsIndexPage() {
  const [searchQuery, setSearchQuery] = useState<string>('');
  const [activeCategory, setActiveCategory] = useState<string>('all');
  const [openFaq, setOpenFaq] = useState<number | null>(0);
  const [wordIndex, setWordIndex] = useState<number>(0);

  // Smooth rotating word timer
  useEffect(() => {
    const interval = setInterval(() => {
      setWordIndex((prev) => (prev + 1) % ROTATING_ITEMS.length);
    }, 2500);

    return () => clearInterval(interval);
  }, []);

  const filteredTools = useMemo(() => {
    return TOOLS_DATA.filter((tool) => {
      const matchesCategory = activeCategory === 'all' || tool.category === activeCategory;
      const q = searchQuery.toLowerCase().trim();
      if (!q) return matchesCategory;

      const matchesSearch =
        tool.title.toLowerCase().includes(q) ||
        tool.shortTitle.toLowerCase().includes(q) ||
        tool.tagline.toLowerCase().includes(q) ||
        tool.description.toLowerCase().includes(q) ||
        tool.highlights.some(h => h.toLowerCase().includes(q));

      return matchesCategory && matchesSearch;
    });
  }, [activeCategory, searchQuery]);

  const toggleFaq = (index: number) => {
    setOpenFaq(openFaq === index ? null : index);
  };

  const currentItem = ROTATING_ITEMS[wordIndex];

  return (
    <div className="w-full bg-white text-zinc-900 selection:bg-zinc-900 selection:text-white">
      
      {/* ── 1. BESPOKE ARCHITECTURAL STUDIO HERO (DISTINCT FROM HOMEPAGE SKY) ── */}
      <section className="relative w-full pt-16 sm:pt-24 pb-10 sm:pb-16 overflow-hidden">
        <ArtisticHeroBackground tone="studio" />

        <div className="relative z-10 w-full max-w-[1240px] mx-auto px-4 sm:px-6 text-center">
          
          {/* Streamlined Status Badge */}
          <div className="mb-3 sm:mb-5">
            <span className="inline-flex items-center gap-1.5 px-3 py-1 sm:px-3.5 sm:py-1.5 rounded-full bg-white/90 backdrop-blur-md text-zinc-900 border border-white/90 text-[11px] sm:text-xs font-semibold shadow-2xs">
              <Sparkles className="w-3 h-3 sm:w-3.5 sm:h-3.5 text-amber-500" />
              <span>6 Free Turnkey Micro-Tools</span>
            </span>
          </div>

          {/* Clean 2-Line Headline (Mobile Proportions Fixed) */}
          <h1 className="font-display text-3xl sm:text-5xl md:text-6xl font-extrabold text-zinc-950 tracking-[-0.035em] leading-[1.2] sm:leading-[1.14] mb-3 sm:mb-4 max-w-[960px] mx-auto">
            <span className="block">Free Tools to Make</span>
            <span className="block mt-1 sm:mt-2">
              <span className="inline-flex items-baseline justify-center gap-2 sm:gap-3">
                <span className="relative inline-block font-extrabold text-zinc-950 pb-1">
                  <span>{currentItem.word}</span>
                  <svg
                    key={currentItem.word}
                    className="absolute -bottom-0.5 sm:-bottom-1 left-0 w-full h-2.5 sm:h-3.5 overflow-visible pointer-events-none animate-in fade-in zoom-in-95 duration-200"
                    viewBox="0 0 100 12"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                    preserveAspectRatio="none"
                  >
                    <path
                      d="M2 9C30 2 70 2 98 8"
                      stroke={currentItem.curveColor}
                      strokeWidth="3.5"
                      strokeLinecap="round"
                    />
                  </svg>
                </span>
                <span>Simple</span>
              </span>
            </span>
          </h1>

          {/* Subtitle */}
          <p className="text-zinc-600 text-xs sm:text-base md:text-lg font-normal leading-relaxed max-w-[580px] mx-auto mb-6 sm:mb-8 px-2">
            Instant browser utilities for Indian GST calculations, client retainer modeling, legal agreements, and dynamic UPI QR generation.
          </p>

          {/* ── High-Trust Value Pillars (Desktop-only to keep mobile ultra-clean) ── */}
          <div className="hidden sm:flex flex-wrap items-center justify-center gap-4 sm:gap-6 mb-8 text-xs sm:text-sm font-medium text-zinc-700">
            <div className="flex items-center gap-1.5 bg-white/70 backdrop-blur-xs px-3 py-1 rounded-full border border-zinc-200/60 shadow-2xs">
              <Check className="w-3.5 h-3.5 text-emerald-600 font-bold" />
              <span>100% Free Forever</span>
            </div>
            <div className="flex items-center gap-1.5 bg-white/70 backdrop-blur-xs px-3 py-1 rounded-full border border-zinc-200/60 shadow-2xs">
              <Zap className="w-3.5 h-3.5 text-amber-500" />
              <span>Zero Login Required</span>
            </div>
            <div className="flex items-center gap-1.5 bg-white/70 backdrop-blur-xs px-3 py-1 rounded-full border border-zinc-200/60 shadow-2xs">
              <Lock className="w-3.5 h-3.5 text-blue-600" />
              <span>100% Client-Side Privacy</span>
            </div>
            <div className="flex items-center gap-1.5 bg-white/70 backdrop-blur-xs px-3 py-1 rounded-full border border-zinc-200/60 shadow-2xs">
              <ShieldCheck className="w-3.5 h-3.5 text-indigo-600" />
              <span>Indian IT Act &amp; SAC 9983</span>
            </div>
          </div>

          {/* ── Central AI Copilot Input Capsule ── */}
          <div className="mb-6 sm:mb-12">
            <ToolsHeroAIInput
              searchQuery={searchQuery}
              onSearchChange={setSearchQuery}
            />
          </div>

        </div>

        {/* ── 2. AUTONOMOUS INFINITE MARQUEE TRACK (NO MANUAL HORIZONTAL SCROLL) ── */}
        <div className="w-full overflow-hidden select-none py-1 sm:py-2 [mask-image:linear-gradient(to_right,transparent,black_6%,black_94%,transparent)]">
          <div className="flex min-w-full shrink-0 items-center justify-around gap-3 sm:gap-5 animate-marquee hover:[animation-play-state:paused]">
            {TOOL_CATEGORY_BLOCKS.concat(TOOL_CATEGORY_BLOCKS).map((block, idx) => (
              <div key={`${block.id}-${idx}`} className="shrink-0 w-[175px] sm:w-[225px]">
                <ToolCategoryHeroCard block={block} />
              </div>
            ))}
          </div>
        </div>

      </section>

      {/* ── 3. METRICS RIBBON (CLEAN MINIMAL CARD & PRECISE BORDERS) ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mt-4 sm:mt-8 mb-10 sm:mb-16 relative z-10">
        <div className="rounded-2xl sm:rounded-3xl bg-white border border-zinc-200/90 p-4 sm:p-7 shadow-[0_4px_24px_rgba(0,0,0,0.03)]">
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 items-center divide-y sm:divide-y-0 sm:divide-x divide-zinc-100">
            
            {/* Metric 1: Monthly Usage */}
            <div className="flex items-center gap-3 pt-2 sm:pt-0 sm:px-4">
              <div className="font-display text-xl sm:text-3xl font-extrabold text-zinc-950 tracking-tight">
                3,840+
              </div>
              <div className="text-left">
                <span className="block text-xs font-bold text-zinc-900 leading-tight">Monthly</span>
                <span className="block text-[11px] text-zinc-500 leading-tight">Tools Executed</span>
              </div>
            </div>

            {/* Metric 2: Productivity Speedup */}
            <div className="flex items-center gap-3 pt-2 sm:pt-0 sm:px-4">
              <div className="font-display text-xl sm:text-3xl font-extrabold text-zinc-950 tracking-tight">
                4.8x
              </div>
              <div className="text-left">
                <span className="block text-xs font-bold text-zinc-900 leading-tight">Workflow</span>
                <span className="block text-[11px] text-zinc-500 leading-tight">Speed Up</span>
              </div>
            </div>

            {/* Metric 3: Weekly Hours Saved */}
            <div className="flex items-center gap-3 pt-2 sm:pt-0 sm:px-4">
              <div className="font-display text-xl sm:text-3xl font-extrabold text-zinc-950 tracking-tight">
                12+ hrs
              </div>
              <div className="text-left">
                <span className="block text-xs font-bold text-zinc-900 leading-tight">Time Saved</span>
                <span className="block text-[11px] text-zinc-500 leading-tight">Per Studio / Wk</span>
              </div>
            </div>

            {/* Metric 4: Accessibility */}
            <div className="flex items-center gap-3 pt-2 sm:pt-0 sm:px-4">
              <div className="font-display text-xl sm:text-3xl font-extrabold text-zinc-950 tracking-tight">
                100%
              </div>
              <div className="text-left">
                <span className="block text-xs font-bold text-zinc-900 leading-tight">Free</span>
                <span className="block text-[11px] text-zinc-500 leading-tight">Zero Login</span>
              </div>
            </div>

          </div>
        </div>
      </section>

      {/* ── 4. DIRECTORY & ALL TOOLS FILTER GRID ── */}
      <section id="tools-directory" className="w-full py-8 sm:py-10 max-w-[1240px] mx-auto px-4 sm:px-6">
        
        {/* Category Tabs & Header */}
        <div className="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6 sm:mb-8 pb-4 border-b border-zinc-100">
          <div>
            <h2 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
              All Micro-Tools Directory
            </h2>
            <span className="text-xs text-zinc-500">
              Instant browser-based utilities with zero sign-up required
            </span>
          </div>

          <div className="flex items-center gap-1.5 overflow-x-auto whitespace-nowrap scrollbar-none w-full sm:w-auto py-1">
            {TOOL_CATEGORIES.map((cat) => {
              const isActive = activeCategory === cat.id;
              return (
                <button
                  key={cat.id}
                  onClick={() => setActiveCategory(cat.id)}
                  className={`inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all cursor-pointer ${
                    isActive
                      ? 'bg-zinc-950 text-white shadow-xs'
                      : 'bg-zinc-50 text-zinc-700 border border-zinc-200 hover:bg-zinc-100 hover:text-zinc-950'
                  }`}
                >
                  <span>{cat.label}</span>
                  <span className={`text-[10px] font-mono rounded-full px-1.5 py-0.2 ${isActive ? 'bg-zinc-800 text-zinc-300' : 'bg-zinc-200/70 text-zinc-600'}`}>
                    {cat.count}
                  </span>
                </button>
              );
            })}
          </div>
        </div>

        {/* Tools Cards Grid (4 Columns on Desktop, 2 Columns on Mobile) */}
        {filteredTools.length === 0 ? (
          <div className="text-center py-16 bg-zinc-50 rounded-3xl border border-zinc-200">
            <span className="text-sm font-bold text-zinc-900 block mb-1">No tools match your search</span>
            <span className="text-xs text-zinc-500">Try clearing filters or search terms.</span>
          </div>
        ) : (
          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-5">
            {filteredTools.map((tool) => (
              <ToolCard key={tool.slug} tool={tool} />
            ))}
          </div>
        )}

      </section>

      {/* ── 5. "MICRO-TOOLS VS CORA OS" TRANSFORMATION CARD ── */}
      <section className="w-full py-12 sm:py-16 bg-zinc-50/80 border-t border-zinc-200/80">
        <div className="max-w-[1100px] mx-auto px-4 sm:px-6">
          <div className="text-center max-w-2xl mx-auto mb-8 sm:mb-10">
            <span className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-500 bg-zinc-100 px-3 py-1 rounded-full border border-zinc-200 mb-2.5 sm:mb-3 inline-block">
              Operational Upgrade
            </span>
            <h2 className="font-display text-xl sm:text-3xl md:text-4xl font-bold text-zinc-950 tracking-tight mb-2.5 sm:mb-3">
              From Ad-Hoc Calculators to an Autonomous Workspace
            </h2>
            <p className="text-xs sm:text-sm md:text-base text-zinc-600 font-normal leading-relaxed">
              Standalone tools are great for quick tasks. Cora Workspace OS unifies everything into a continuous autopilot pipeline.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
            {/* Column 1: Fragmented Tools */}
            <div className="rounded-2xl sm:rounded-3xl bg-white border border-zinc-200/90 p-5 sm:p-6 space-y-2.5 sm:space-y-3">
              <div className="w-8 h-1 bg-zinc-300 rounded-full mb-3 sm:mb-4" />
              <h3 className="text-sm sm:text-base font-bold text-zinc-950">
                Fragmented Tool Sprawl
              </h3>
              <p className="text-xs text-zinc-600 leading-relaxed font-normal">
                Manual copy-pasting between calculators, spreadsheets, and word docs reduces billing efficiency by up to <strong>35%</strong>.
              </p>
            </div>

            {/* Column 2: Central Cora Intelligence */}
            <div className="rounded-2xl sm:rounded-3xl bg-white border border-purple-200/80 p-5 sm:p-6 space-y-2.5 sm:space-y-3 shadow-sm">
              <div className="w-8 h-1 bg-purple-500 rounded-full mb-3 sm:mb-4" />
              <h3 className="text-sm sm:text-base font-bold text-purple-950 flex items-center gap-1.5">
                <Sparkles className="w-4 h-4 text-purple-600" />
                <span>Central Intelligence</span>
              </h3>
              <p className="text-xs text-zinc-600 leading-relaxed font-normal">
                Proposals, 18% GST invoices, and SHA-256 e-sign agreements are automatically generated directly from client conversations.
              </p>
            </div>

            {/* Column 3: Zero Question Chaos */}
            <div className="rounded-2xl sm:rounded-3xl bg-white border border-emerald-200/80 p-5 sm:p-6 space-y-2.5 sm:space-y-3 shadow-sm">
              <div className="w-8 h-1 bg-emerald-500 rounded-full mb-3 sm:mb-4" />
              <h3 className="text-sm sm:text-base font-bold text-emerald-950 flex items-center gap-1.5">
                <CheckCircle2 className="w-4 h-4 text-emerald-600" />
                <span>Zero Scope Chaos</span>
              </h3>
              <p className="text-xs text-zinc-600 leading-relaxed font-normal">
                Save <strong>2.5 hours daily</strong> with built-in scope creep buffers, dynamic UPI QR collections, and instant milestone releases.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* ── 6. FREQUENTLY ASKED QUESTIONS ── */}
      <section className="w-full py-12 sm:py-20 max-w-[860px] mx-auto px-4 sm:px-6">
        <div className="text-center mb-8 sm:mb-10">
          <span className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-500 bg-zinc-100 px-3 py-1 rounded-full border border-zinc-200 mb-2.5 sm:mb-3 inline-block">
            Frequently Asked Questions
          </span>
          <h2 className="font-display text-xl sm:text-3xl font-bold text-zinc-950 tracking-tight">
            Common questions about Cora micro-tools
          </h2>
        </div>

        <div className="space-y-2.5 sm:space-y-3">
          {TOOLS_FAQS.map((faq, idx) => {
            const isOpen = openFaq === idx;
            return (
              <div
                key={idx}
                className="rounded-2xl bg-white border border-zinc-200/90 overflow-hidden shadow-2xs"
              >
                <button
                  onClick={() => toggleFaq(idx)}
                  className="w-full p-4 sm:p-5 flex items-center justify-between text-left gap-4 hover:bg-zinc-50 transition-colors"
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

    </div>
  );
}
