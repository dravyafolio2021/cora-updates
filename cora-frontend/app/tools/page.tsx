'use client';

import React, { useState, useMemo } from 'react';
import Link from 'next/link';
import { 
  Search, 
  ArrowRight, 
  CheckCircle2, 
  ShieldCheck, 
  Plus, 
  Minus,
  Sparkles,
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

  const handleSearchSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    const toolsGrid = document.getElementById('tools-directory');
    if (toolsGrid) {
      toolsGrid.scrollIntoView({ behavior: 'smooth' });
    }
  };

  return (
    <div className="w-full bg-white text-zinc-900 selection:bg-zinc-900 selection:text-white">
      
      {/* ── 1. CENTERED HERO INSPIRED BY USER REFERENCE ── */}
      <section className="relative w-full pt-16 sm:pt-24 pb-12 sm:pb-16 overflow-hidden bg-gradient-to-b from-[#F8FAFC] via-white to-white">
        
        {/* Subtle Decorative Floating 3D Geometric Confetti Accents */}
        <div className="absolute top-12 left-[10%] w-3 h-3 bg-rose-400 rounded-xs rotate-45 opacity-60 pointer-events-none hidden md:block" />
        <div className="absolute top-20 right-[12%] w-4 h-4 bg-purple-500 rounded-xs rotate-12 opacity-60 pointer-events-none hidden md:block" />
        <div className="absolute top-36 left-[6%] w-3.5 h-3.5 bg-blue-500 rounded-xs -rotate-12 opacity-50 pointer-events-none hidden md:block" />
        <div className="absolute top-28 right-[8%] w-3 h-3 bg-amber-400 rounded-xs rotate-45 opacity-70 pointer-events-none hidden md:block" />

        <div className="relative z-10 w-full max-w-[1240px] mx-auto px-4 sm:px-6 text-center">
          
          {/* Main Headline with Highlighted Blue Box */}
          <h1 className="font-display text-3xl sm:text-5xl md:text-6xl font-extrabold text-zinc-950 tracking-[-0.035em] leading-[1.18] mb-4 max-w-[900px] mx-auto">
            Free Tools to Make <span className="bg-[#2563EB] text-white px-3 py-0.5 rounded-xl inline-block shadow-sm">Business</span> Simple
          </h1>

          {/* Subtitle */}
          <p className="text-zinc-600 text-xs sm:text-base md:text-lg font-normal leading-relaxed max-w-[640px] mx-auto mb-8">
            We offer GST, proposal, contract, embed and AI tools to make your agency and professional life easier
          </p>

          {/* Central Elevated Search Capsule */}
          <form 
            onSubmit={handleSearchSubmit}
            className="max-w-[560px] mx-auto bg-white rounded-full p-1.5 sm:p-2 border border-zinc-200/90 shadow-[0_8px_30px_rgba(0,0,0,0.06)] flex items-center justify-between gap-2 mb-12 sm:mb-16 focus-within:border-blue-600 focus-within:ring-4 focus-within:ring-blue-500/10 transition-all"
          >
            <div className="flex items-center gap-2.5 pl-3 flex-1">
              <Search className="w-4 h-4 text-[#2563EB] shrink-0" />
              <input
                type="text"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                placeholder="Search tools (e.g. GST calculator, retainer math, NDA)..."
                className="w-full bg-transparent text-xs sm:text-sm text-zinc-900 placeholder:text-zinc-400 focus:outline-none"
              />
            </div>
            <button
              type="submit"
              className="px-6 py-2 sm:py-2.5 rounded-full bg-[#2563EB] hover:bg-blue-700 text-white font-semibold text-xs sm:text-sm transition-all shadow-sm cursor-pointer shrink-0"
            >
              Search
            </button>
          </form>

          {/* ── 2. HORIZONTAL ROW OF VIBRANT CATEGORY CARDS ── */}
          <div className="w-full overflow-x-auto pb-4 pt-1 scrollbar-none">
            <div className="flex lg:grid lg:grid-cols-6 gap-3 sm:gap-4 min-w-max lg:min-w-0 justify-start lg:justify-between px-2">
              {TOOL_CATEGORY_BLOCKS.map((block) => (
                <ToolCategoryHeroCard key={block.id} block={block} />
              ))}
            </div>
          </div>

        </div>
      </section>

      {/* ── 3. METRICS RIBBON (DUAL OPACITY NUMBERS) ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 -mt-2 mb-12 sm:mb-16">
        <div className="rounded-3xl bg-gradient-to-r from-blue-50/70 via-indigo-50/50 to-blue-50/70 border border-blue-100/80 p-6 sm:p-8 shadow-xs">
          <div className="grid grid-cols-2 md:grid-cols-4 gap-6 sm:gap-8 items-center divide-y sm:divide-y-0 sm:divide-x divide-blue-200/50">
            
            {/* Metric 1 */}
            <div className="flex items-center gap-3 pt-3 sm:pt-0 sm:px-4">
              <div className="font-display text-3xl sm:text-4xl font-black text-[#2563EB] tracking-tight">
                100k+
              </div>
              <div className="text-left">
                <span className="block text-xs font-bold text-zinc-900 leading-tight">Active</span>
                <span className="block text-[11px] text-zinc-500 leading-tight">Users</span>
              </div>
            </div>

            {/* Metric 2 */}
            <div className="flex items-center gap-3 pt-3 sm:pt-0 sm:px-4">
              <div className="font-display text-3xl sm:text-4xl font-black text-[#2563EB] tracking-tight">
                ₹50Cr+
              </div>
              <div className="text-left">
                <span className="block text-xs font-bold text-zinc-900 leading-tight">Invoices</span>
                <span className="block text-[11px] text-zinc-500 leading-tight">Calculated</span>
              </div>
            </div>

            {/* Metric 3 */}
            <div className="flex items-center gap-3 pt-3 sm:pt-0 sm:px-4">
              <div className="font-display text-3xl sm:text-4xl font-black text-[#2563EB] tracking-tight">
                20+
              </div>
              <div className="text-left">
                <span className="block text-xs font-bold text-zinc-900 leading-tight">Online</span>
                <span className="block text-[11px] text-zinc-500 leading-tight">Tools</span>
              </div>
            </div>

            {/* Metric 4 */}
            <div className="flex items-center gap-3 pt-3 sm:pt-0 sm:px-4">
              <div className="font-display text-3xl sm:text-4xl font-black text-[#2563EB] tracking-tight">
                100%
              </div>
              <div className="text-left">
                <span className="block text-xs font-bold text-zinc-900 leading-tight">Free</span>
                <span className="block text-[11px] text-zinc-500 leading-tight">Forever</span>
              </div>
            </div>

          </div>
        </div>
      </section>

      {/* ── 4. DIRECTORY & ALL TOOLS FILTER GRID ── */}
      <section id="tools-directory" className="w-full py-12 max-w-[1240px] mx-auto px-4 sm:px-6">
        
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
                  className={`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold transition-all cursor-pointer ${
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

          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {/* Ad-Hoc Way */}
            <div className="rounded-3xl bg-white border border-zinc-200 p-6 sm:p-7 space-y-3.5">
              <span className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-500 block">
                Standalone Web Micro-Tools
              </span>
              <ul className="space-y-2.5 text-xs sm:text-sm text-zinc-600">
                <li className="flex items-start gap-2">
                  <span className="text-zinc-400 font-bold">•</span>
                  <span>Manual copy-pasting between calculator, word documents &amp; email clients</span>
                </li>
                <li className="flex items-start gap-2">
                  <span className="text-zinc-400 font-bold">•</span>
                  <span>Zero saved client history or automated follow-up reminders</span>
                </li>
                <li className="flex items-start gap-2">
                  <span className="text-zinc-400 font-bold">•</span>
                  <span>Manual generation of UPI QR codes and payment reconciliations</span>
                </li>
              </ul>
            </div>

            {/* Cora OS Autopilot */}
            <div className="rounded-3xl bg-zinc-950 text-white border border-zinc-800 p-6 sm:p-7 space-y-3.5 shadow-md">
              <span className="text-xs font-mono font-bold uppercase tracking-wider text-emerald-400 block flex items-center gap-1.5">
                <Zap className="w-3.5 h-3.5" />
                <span>Unified Cora Workspace OS</span>
              </span>
              <ul className="space-y-2.5 text-xs sm:text-sm text-zinc-300">
                <li className="flex items-start gap-2">
                  <CheckCircle2 className="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                  <span>Proposals automatically draft IT Act 2000 contracts and e-sign links</span>
                </li>
                <li className="flex items-start gap-2">
                  <CheckCircle2 className="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                  <span>Invoices split 18% CGST/SGST and generate dynamic UPI QRs on autopilot</span>
                </li>
                <li className="flex items-start gap-2">
                  <CheckCircle2 className="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                  <span>AI Co-Founder monitors sprint milestone staging releases and client sign-offs</span>
                </li>
              </ul>
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
