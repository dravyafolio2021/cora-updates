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
  Layers,
  ChevronRight
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

const ROTATING_WORDS = [
  'Operations',
  'Marketing',
  'Finance',
  'Sales',
  'Productivity',
  'Contracts',
  'Invoicing',
];

const TOOLS_FAQS = [
  {
    question: 'Are these tools completely free to use?',
    answer: 'Yes, 100% free forever. There are no daily usage limits, hidden paywalls, or credit card requirements. All financial calculations and AI copy generators run instantly in your browser.'
  },
  {
    question: 'Do these tools store my proprietary business or client data?',
    answer: 'No. All calculations (such as GST splits, retainer formulas, UPI intent links, and legal clauses) are executed entirely client-side inside your browser. No financial numbers or client entities are logged or stored.'
  },
  {
    question: 'How do these micro-tools connect to the full Cora Workspace?',
    answer: 'These standalone micro-tools provide instant utility for ad-hoc tasks. Inside the Cora Workspace OS, all these capabilities are unified into an autonomous engine that automatically drafts contracts, sends invoices with dynamic UPI QR codes, and tracks milestones without manual calculation.'
  },
  {
    question: 'Are the contracts compliant with the Indian IT Act 2000?',
    answer: 'Yes. All generated legal clauses conform to Section 10A of the Information Technology Act 2000 regarding the validity of contracts formed through electronic means, complete with SHA-256 integrity provisions.'
  }
];

export default function ToolsIndexPage() {
  const [searchQuery, setSearchQuery] = useState<string>('');
  const [activeCategory, setActiveCategory] = useState<string>('all');
  const [openFaq, setOpenFaq] = useState<number | null>(0);
  const [wordIndex, setWordIndex] = useState<number>(0);
  const [isFlipping, setIsFlipping] = useState<boolean>(false);

  // Auto-rotating highlighted word effect
  useEffect(() => {
    const interval = setInterval(() => {
      setIsFlipping(true);
      setTimeout(() => {
        setWordIndex((prev) => (prev + 1) % ROTATING_WORDS.length);
        setIsFlipping(false);
      }, 250);
    }, 2400);

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

  return (
    <div className="w-full bg-white text-zinc-900 selection:bg-zinc-900 selection:text-white">
      
      {/* ── 1. DEDICATED LUMINOUS ATMOSPHERIC HERO SECTION ── */}
      <section className="relative w-full pt-16 sm:pt-24 pb-12 sm:pb-16 overflow-hidden border-b border-zinc-100">
        
        {/* Dedicated Soft Luminous Gradient Aura (Unique for Tools, not duplicating sky image) */}
        <div 
          className="absolute inset-0 pointer-events-none"
          style={{
            background: 'radial-gradient(ellipse 90% 60% at 50% -10%, rgba(99, 102, 241, 0.12) 0%, rgba(236, 72, 153, 0.05) 45%, rgba(255, 255, 255, 0) 80%), #ffffff',
          }}
        />

        {/* Delicate Micro-Dot Grid */}
        <div 
          className="absolute inset-0 opacity-[0.03] pointer-events-none"
          style={{
            backgroundImage: `radial-gradient(circle at 1px 1px, #000 1px, transparent 0)`,
            backgroundSize: '24px 24px'
          }}
        />

        <div className="relative z-10 w-full max-w-[1240px] mx-auto px-4 sm:px-6 text-center">
          
          {/* Breadcrumbs */}
          <nav className="inline-flex items-center gap-1.5 text-xs text-zinc-500 font-medium mb-4">
            <Link href="/" className="hover:text-zinc-950 transition-colors">
              Home
            </Link>
            <ChevronRight className="w-3.5 h-3.5 text-zinc-400 shrink-0" />
            <span className="text-zinc-950 font-semibold">
              Free Micro-Tools
            </span>
          </nav>

          {/* Glassmorphic Status Pill */}
          <div className="mb-4">
            <span className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/90 backdrop-blur-md text-zinc-900 border border-zinc-200/80 text-xs font-semibold shadow-2xs">
              <Sparkles className="w-3.5 h-3.5 text-amber-500" />
              <span>6 Free Turnkey Utilities</span>
              <span className="text-zinc-300">•</span>
              <span className="text-emerald-700 font-mono text-[11px] font-bold">Zero Login Required</span>
            </span>
          </div>

          {/* Dynamic Headline with Animated Rotating Word */}
          <h1 className="font-display text-3xl sm:text-5xl md:text-6xl font-bold text-zinc-950 tracking-[-0.035em] leading-[1.18] mb-4 max-w-[880px] mx-auto">
            Free Tools to Make{' '}
            <span 
              className={`inline-flex items-center justify-center px-4 py-0.5 rounded-2xl bg-white/90 backdrop-blur-md border border-zinc-200/90 shadow-xs text-zinc-950 font-bold mx-1 transition-all duration-300 min-w-[140px] sm:min-w-[180px] ${
                isFlipping ? 'opacity-0 scale-95 -translate-y-1' : 'opacity-100 scale-100 translate-y-0'
              }`}
            >
              {ROTATING_WORDS[wordIndex]}
            </span>{' '}
            Simple
          </h1>

          {/* Subtitle */}
          <p className="text-zinc-600 text-xs sm:text-base md:text-lg font-normal leading-relaxed max-w-[660px] mx-auto mb-8">
            Instant browser utilities for Indian GST calculations, client retainer modeling, legal agreements, and developer embeds.
          </p>

          {/* ── Central AI Copilot Input Capsule ── */}
          <div className="mb-10 sm:mb-14">
            <ToolsHeroAIInput
              searchQuery={searchQuery}
              onSearchChange={setSearchQuery}
            />
          </div>

          {/* ── 2. HORIZONTAL ROW OF LUMINOUS CATEGORY CARDS ── */}
          <div className="w-full overflow-x-auto pb-4 pt-1 scrollbar-none">
            <div className="flex lg:grid lg:grid-cols-6 gap-3 sm:gap-4 min-w-max lg:min-w-0 justify-start lg:justify-between px-1">
              {TOOL_CATEGORY_BLOCKS.map((block) => (
                <ToolCategoryHeroCard key={block.id} block={block} />
              ))}
            </div>
          </div>

        </div>
      </section>

      {/* ── 3. METRICS RIBBON (AIRY & REFINED) ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 -mt-3 mb-12 sm:mb-16">
        <div className="rounded-3xl bg-gradient-to-r from-blue-50/70 via-indigo-50/50 to-purple-50/70 border border-blue-100/80 p-6 sm:p-8 shadow-xs">
          <div className="grid grid-cols-2 md:grid-cols-4 gap-6 sm:gap-8 items-center divide-y sm:divide-y-0 sm:divide-x divide-blue-200/50">
            
            {/* Metric 1 */}
            <div className="flex items-center gap-3 pt-3 sm:pt-0 sm:px-4">
              <div className="font-display text-2xl sm:text-3xl font-bold text-blue-600 tracking-tight">
                100k+
              </div>
              <div className="text-left">
                <span className="block text-xs font-bold text-zinc-900 leading-tight">Active</span>
                <span className="block text-[11px] text-zinc-500 leading-tight">Tools Executed</span>
              </div>
            </div>

            {/* Metric 2 */}
            <div className="flex items-center gap-3 pt-3 sm:pt-0 sm:px-4">
              <div className="font-display text-2xl sm:text-3xl font-bold text-emerald-600 tracking-tight">
                ₹50Cr+
              </div>
              <div className="text-left">
                <span className="block text-xs font-bold text-zinc-900 leading-tight">Invoices</span>
                <span className="block text-[11px] text-zinc-500 leading-tight">Calculated</span>
              </div>
            </div>

            {/* Metric 3 */}
            <div className="flex items-center gap-3 pt-3 sm:pt-0 sm:px-4">
              <div className="font-display text-2xl sm:text-3xl font-bold text-purple-600 tracking-tight">
                20+
              </div>
              <div className="text-left">
                <span className="block text-xs font-bold text-zinc-900 leading-tight">Online</span>
                <span className="block text-[11px] text-zinc-500 leading-tight">Free Utilities</span>
              </div>
            </div>

            {/* Metric 4 */}
            <div className="flex items-center gap-3 pt-3 sm:pt-0 sm:px-4">
              <div className="font-display text-2xl sm:text-3xl font-bold text-amber-600 tracking-tight">
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
      <section id="tools-directory" className="w-full py-10 max-w-[1240px] mx-auto px-4 sm:px-6">
        
        {/* Category Tabs & Header */}
        <div className="flex flex-col sm:flex-row items-center justify-between gap-4 mb-8 pb-4 border-b border-zinc-100">
          <div>
            <h2 className="font-display text-2xl font-bold text-zinc-950 tracking-tight">
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

        {/* Tools Cards Grid */}
        {filteredTools.length === 0 ? (
          <div className="text-center py-16 bg-zinc-50 rounded-3xl border border-zinc-200">
            <span className="text-sm font-bold text-zinc-900 block mb-1">No tools match your search</span>
            <span className="text-xs text-zinc-500">Try clearing filters or search terms.</span>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {filteredTools.map((tool) => (
              <ToolCard key={tool.slug} tool={tool} />
            ))}
          </div>
        )}

      </section>

      {/* ── 5. "MICRO-TOOLS VS CORA OS" TRANSFORMATION CARD ── */}
      <section className="w-full py-16 bg-zinc-50/80 border-t border-zinc-200/80">
        <div className="max-w-[1100px] mx-auto px-4 sm:px-6">
          <div className="text-center max-w-2xl mx-auto mb-10">
            <span className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-500 bg-zinc-100 px-3 py-1 rounded-full border border-zinc-200 mb-3 inline-block">
              Operational Upgrade
            </span>
            <h2 className="font-display text-2xl sm:text-3xl md:text-4xl font-bold text-zinc-950 tracking-tight mb-3">
              From Ad-Hoc Calculators to an Autonomous Workspace
            </h2>
            <p className="text-xs sm:text-sm md:text-base text-zinc-600 font-normal leading-relaxed">
              Standalone tools are great for quick tasks. Cora Workspace OS unifies everything into a continuous autopilot pipeline.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {/* Column 1: Fragmented Tools */}
            <div className="rounded-3xl bg-white border border-zinc-200/90 p-6 space-y-3">
              <div className="w-8 h-1 bg-zinc-300 rounded-full mb-4" />
              <h3 className="text-base font-bold text-zinc-950">
                Fragmented Tool Sprawl
              </h3>
              <p className="text-xs text-zinc-600 leading-relaxed font-normal">
                Manual copy-pasting between calculators, spreadsheets, and word docs reduces billing efficiency by up to <strong>35%</strong>.
              </p>
            </div>

            {/* Column 2: Central Cora Intelligence */}
            <div className="rounded-3xl bg-white border border-purple-200/80 p-6 space-y-3 shadow-sm">
              <div className="w-8 h-1 bg-purple-500 rounded-full mb-4" />
              <h3 className="text-base font-bold text-purple-950 flex items-center gap-1.5">
                <Sparkles className="w-4 h-4 text-purple-600" />
                <span>Central Intelligence</span>
              </h3>
              <p className="text-xs text-zinc-600 leading-relaxed font-normal">
                Proposals, 18% GST invoices, and SHA-256 e-sign agreements are automatically generated directly from client conversations.
              </p>
            </div>

            {/* Column 3: Zero Question Chaos */}
            <div className="rounded-3xl bg-white border border-emerald-200/80 p-6 space-y-3 shadow-sm">
              <div className="w-8 h-1 bg-emerald-500 rounded-full mb-4" />
              <h3 className="text-base font-bold text-emerald-950 flex items-center gap-1.5">
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
      <section className="w-full py-16 sm:py-20 max-w-[860px] mx-auto px-4 sm:px-6">
        <div className="text-center mb-10">
          <span className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-500 bg-zinc-100 px-3 py-1 rounded-full border border-zinc-200 mb-3 inline-block">
            Frequently Asked Questions
          </span>
          <h2 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight">
            Common questions about Cora micro-tools
          </h2>
        </div>

        <div className="space-y-3">
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
                  <div className="px-4 sm:px-5 pb-5 text-xs sm:text-sm text-zinc-600 leading-relaxed border-t border-zinc-100 pt-3">
                    {faq.answer}
                  </div>
                )}
              </div>
            );
          })}
        </div>
      </section>

      {/* ── 7. MONOCHROMATIC FOOTER CTA BANNER ── */}
      <section className="w-full py-16 sm:py-20 bg-zinc-950 text-white relative overflow-hidden">
        <div className="relative z-10 max-w-[980px] mx-auto px-4 sm:px-6 text-center">
          <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-zinc-900 border border-zinc-800 text-[11px] font-mono font-semibold text-zinc-300 mb-4 shadow-sm">
            <Sparkles className="w-3.5 h-3.5 text-amber-400" />
            <span>Turnkey Cora Workspace</span>
          </div>

          <h2 className="font-display text-2xl sm:text-3xl md:text-4xl font-bold tracking-tight mb-4 max-w-[720px] mx-auto leading-tight">
            Need all these micro-tools unified in your client portal?
          </h2>

          <p className="text-xs sm:text-sm md:text-base text-zinc-400 font-normal leading-relaxed max-w-[580px] mx-auto mb-8">
            Launch your free workspace in 3 minutes. Automated 18% GST tax invoices, SHA-256 legal contracts, and client portals pre-seeded.
          </p>

          <div className="flex flex-col sm:flex-row items-center justify-center gap-3.5 mb-8">
            <a
              href="https://app.heycora.in/workspace/login?source=tools_footer_cta"
              className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl bg-white text-zinc-950 text-xs sm:text-sm font-semibold hover:bg-zinc-100 transition-all shadow-sm group"
            >
              <span>Get Started Free Forever</span>
              <ArrowRight className="w-4 h-4 text-zinc-600 group-hover:translate-x-0.5 transition-transform" />
            </a>

            <Link
              href="/demo"
              className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white border border-zinc-800 text-xs sm:text-sm font-semibold transition-all"
            >
              <span>Explore Interactive Architecture</span>
            </Link>
          </div>

          <div className="flex flex-wrap items-center justify-center gap-4 sm:gap-8 text-[11.5px] font-mono text-zinc-400">
            <div className="flex items-center gap-1.5">
              <CheckCircle2 className="w-3.5 h-3.5 text-emerald-400" />
              <span>Zero Credit Card Required</span>
            </div>
            <div className="flex items-center gap-1.5">
              <CheckCircle2 className="w-3.5 h-3.5 text-emerald-400" />
              <span>Pre-Seeded 18% SAC Codes</span>
            </div>
            <div className="flex items-center gap-1.5">
              <CheckCircle2 className="w-3.5 h-3.5 text-emerald-400" />
              <span>100% IT Act 2000 Compliant</span>
            </div>
          </div>

        </div>
      </section>

    </div>
  );
}
