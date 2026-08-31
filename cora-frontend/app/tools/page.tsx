'use client';

import React, { useState, useMemo } from 'react';
import Link from 'next/link';
import { 
  Calculator, 
  Sparkles, 
  Code, 
  Scale, 
  Receipt, 
  QrCode, 
  Search, 
  ArrowRight, 
  CheckCircle2, 
  ShieldCheck, 
  Plus, 
  Minus,
  ChevronRight,
  Layers,
  Zap
} from 'lucide-react';
import { TOOLS_DATA, TOOL_CATEGORIES, ToolItem } from '@/lib/tools-data';
import { ToolCard } from '@/components/tools/ToolCard';
import { ArtisticHeroBackground } from '@/components/features/ArtisticHeroBackground';

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
  const [activeCategory, setActiveCategory] = useState<string>('all');
  const [searchQuery, setSearchQuery] = useState<string>('');
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

  return (
    <div className="w-full bg-white text-zinc-900">
      
      {/* ── 1. ARTISTIC HERO SECTION (COMPACT <=40VH) ── */}
      <section className="relative w-full pt-20 sm:pt-24 pb-8 sm:pb-12 overflow-hidden border-b border-zinc-100">
        <ArtisticHeroBackground tone="neutral" />

        <div className="relative z-10 w-full max-w-[1240px] mx-auto px-4 sm:px-6">
          
          {/* Breadcrumbs */}
          <nav className="flex items-center gap-1.5 text-xs text-zinc-600 font-medium overflow-x-auto whitespace-nowrap scrollbar-none py-1 mb-4">
            <Link href="/" className="hover:text-zinc-950 transition-colors">
              Home
            </Link>
            <ChevronRight className="w-3.5 h-3.5 text-zinc-400 shrink-0" />
            <Link href="/tools" className="text-zinc-950 font-semibold">
              Free Micro-Tools
            </Link>
          </nav>

          <div className="flex flex-col items-start gap-3.5 max-w-[880px]">
            {/* Status Badge */}
            <div className="flex items-center flex-wrap gap-2">
              <span className="inline-flex items-center gap-1.5 px-3 py-1 bg-white/90 backdrop-blur-md text-zinc-900 border border-zinc-200/90 rounded-full text-xs font-semibold shadow-2xs">
                <Sparkles className="w-3.5 h-3.5 text-amber-500" />
                <span>6 Turnkey Micro-Tools</span>
              </span>
              <span className="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-500/10 text-emerald-700 border border-emerald-500/30 rounded-full text-[10px] font-mono font-bold backdrop-blur-md">
                <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
                <span>100% Free Forever • Zero Login</span>
              </span>
            </div>

            {/* Main Headline */}
            <h1 className="font-display text-2xl xs:text-3xl sm:text-4xl md:text-5xl font-semibold text-zinc-950 tracking-[-0.03em] leading-[1.24] sm:leading-[1.28]">
              Tools built for speed and precision.
            </h1>

            {/* Value Tagline */}
            <p className="text-zinc-600 text-xs sm:text-base font-normal leading-relaxed max-w-[760px]">
              Instant, browser-based financial calculators, AI prompt studios, contract generators, and developer embed builders. Pre-configured for modern professional service businesses.
            </p>

            {/* Quick 3-Stat Bar */}
            <div className="grid grid-cols-3 gap-3 sm:gap-6 w-full py-3 my-1 border-y border-zinc-200/70">
              <div className="space-y-0.5">
                <div className="font-display text-lg sm:text-2xl font-semibold text-zinc-950 tracking-tight">
                  6 Micro-Tools
                </div>
                <div className="text-[10px] sm:text-xs text-zinc-500 font-medium">
                  Instant Client-Side Utilities
                </div>
              </div>
              <div className="space-y-0.5">
                <div className="font-display text-lg sm:text-2xl font-semibold text-zinc-950 tracking-tight">
                  0% Login Required
                </div>
                <div className="text-[10px] sm:text-xs text-zinc-500 font-medium">
                  Zero Sign-up or Paywalls
                </div>
              </div>
              <div className="space-y-0.5">
                <div className="font-display text-lg sm:text-2xl font-semibold text-zinc-950 tracking-tight">
                  0% Gateway Fees
                </div>
                <div className="text-[10px] sm:text-xs text-zinc-500 font-medium">
                  Direct UPI Bank Settlement
                </div>
              </div>
            </div>

          </div>

        </div>
      </section>

      {/* ── 2. SEARCH & CATEGORY FILTER TABS ── */}
      <section className="w-full py-6 bg-zinc-50/70 border-b border-zinc-200/80 sticky top-14 z-20 backdrop-blur-md">
        <div className="max-w-[1240px] mx-auto px-4 sm:px-6">
          <div className="flex flex-col sm:flex-row items-center justify-between gap-4">
            
            {/* Category Filter Pills */}
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
                        : 'bg-white text-zinc-700 border border-zinc-200 hover:bg-zinc-100 hover:text-zinc-950'
                    }`}
                  >
                    <span>{cat.label}</span>
                    <span className={`text-[10px] font-mono rounded-full px-1.5 py-0.2 ${isActive ? 'bg-zinc-800 text-zinc-300' : 'bg-zinc-100 text-zinc-500'}`}>
                      {cat.count}
                    </span>
                  </button>
                );
              })}
            </div>

            {/* Search Input Bar */}
            <div className="relative w-full sm:w-72">
              <Search className="w-4 h-4 text-zinc-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
              <input
                type="text"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                placeholder="Search micro-tools..."
                className="w-full pl-9 pr-4 py-1.5 rounded-full bg-white border border-zinc-200 text-xs text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:border-zinc-950 shadow-2xs"
              />
            </div>

          </div>
        </div>
      </section>

      {/* ── 3. TOOLS GRID (CLEAN VISUAL CARDS) ── */}
      <section className="w-full py-12 sm:py-16 max-w-[1240px] mx-auto px-4 sm:px-6">
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

      {/* ── 4. "MICRO-TOOLS VS CORA OS" TRANSFORMATION CARD ── */}
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

      {/* ── 5. FREQUENTLY ASKED QUESTIONS ── */}
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

      {/* ── 6. MONOCHROMATIC FOOTER CTA BANNER ── */}
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
