'use client';

import React, { useState, useMemo } from 'react';
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
  Laptop
} from 'lucide-react';
import { BUILT_MODULES, UPCOMING_MODULES, CATEGORIES, FeatureModule } from '@/lib/features-data';
import { FeatureIcon } from '@/components/features/FeatureIcon';
import { ArtisticHeroBackground } from '@/components/features/ArtisticHeroBackground';
import { trackEvent } from '@/components/analytics/Analytics';

export default function FeaturesPage() {
  const [activeCategory, setActiveCategory] = useState('all');
  const [searchQuery, setSearchQuery] = useState('');

  const filteredBuiltModules = useMemo(() => {
    return BUILT_MODULES.filter((mod) => {
      const matchesCategory = activeCategory === 'all' 
        ? true 
        : activeCategory === 'roadmap'
          ? false
          : mod.category === activeCategory;

      const q = searchQuery.toLowerCase().trim();
      const matchesSearch = !q || (
        mod.title.toLowerCase().includes(q) ||
        mod.shortTitle.toLowerCase().includes(q) ||
        mod.tagline?.toLowerCase().includes(q) ||
        mod.heroDescription?.toLowerCase().includes(q) ||
        mod.tags.some(t => t.toLowerCase().includes(q)) ||
        mod.categoryLabel.toLowerCase().includes(q)
      );

      return matchesCategory && matchesSearch;
    });
  }, [activeCategory, searchQuery]);

  const filteredRoadmapModules = useMemo(() => {
    if (activeCategory !== 'all' && activeCategory !== 'roadmap') {
      return [];
    }
    const q = searchQuery.toLowerCase().trim();
    if (!q) return UPCOMING_MODULES;
    return UPCOMING_MODULES.filter((m) => 
      m.title.toLowerCase().includes(q) ||
      m.desc.toLowerCase().includes(q) ||
      m.categoryLabel.toLowerCase().includes(q)
    );
  }, [activeCategory, searchQuery]);

  return (
    <main className="w-full relative pb-24 overflow-hidden bg-white text-zinc-900">
      
      {/* ── ARTISTIC BLENDED HERO & INTRO SECTION ── */}
      <section className="relative w-full pt-32 sm:pt-40 pb-20 sm:pb-28 overflow-hidden">
        {/* Soft Organic Background & Gradient Veil that melts down into the page */}
        <ArtisticHeroBackground tone="blue" />

        <div className="relative z-10 w-full max-w-[1240px] mx-auto px-4 sm:px-6 text-center">
          
          {/* Status Badge */}
          <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-white/80 backdrop-blur-md rounded-full border border-blue-200/80 text-xs font-semibold text-zinc-900 mb-6 shadow-2xs">
            <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
            <span>20 Built Modules Live • 8 In Active Development</span>
          </div>

          {/* Clean Headline */}
          <h1 className="font-display text-4xl xs:text-5xl sm:text-6xl font-extrabold text-zinc-950 leading-[1.12] tracking-[-0.035em] max-w-[920px] mx-auto mb-5">
            One Operating System, Every Creative Workflow
          </h1>

          {/* Tagline */}
          <p className="text-zinc-600 text-base sm:text-xl font-normal leading-relaxed max-w-[720px] mx-auto mb-9">
            An autonomous, unified workspace replacing 8+ disconnected tools with native AI reasoning, Kanban CRM, SHA-256 contracts, and 18% GST invoicing.
          </p>

          {/* ── FLOATING ARTISTIC PILL BAR (Inspired by Media Reference) ── */}
          <div className="max-w-[920px] mx-auto mb-8">
            <div className="inline-flex items-center justify-center p-1.5 bg-white/75 backdrop-blur-md border border-zinc-200/80 rounded-full shadow-sm max-w-full overflow-x-auto scrollbar-none gap-1">
              {CATEGORIES.map((cat) => {
                const isActive = activeCategory === cat.id;
                return (
                  <button
                    key={cat.id}
                    onClick={() => {
                      setActiveCategory(cat.id);
                      trackEvent('features_category_filter', { category: cat.id });
                    }}
                    className={`px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition-all duration-200 cursor-pointer flex items-center gap-1.5 ${
                      isActive
                        ? 'bg-white text-zinc-950 shadow-md border border-zinc-200/90 font-bold scale-[1.02]'
                        : 'text-zinc-600 hover:text-zinc-950 hover:bg-white/40'
                    }`}
                  >
                    <span>{cat.label}</span>
                  </button>
                );
              })}
            </div>
          </div>

          {/* Search Bar sitting harmoniously in the hero */}
          <div className="max-w-[460px] mx-auto mb-6">
            <div className="relative flex items-center">
              <Search className="w-4 h-4 text-zinc-400 absolute left-3.5 pointer-events-none" />
              <input
                type="text"
                placeholder="Search capabilities (e.g. GST, E-Signs, CRM, Calendar, MCP)..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="w-full pl-10 pr-4 py-2.5 bg-white/90 focus:bg-white border border-zinc-200/90 focus:border-zinc-950 rounded-2xl text-xs sm:text-sm font-medium text-zinc-900 placeholder-zinc-400 outline-none transition-all shadow-sm"
              />
              {searchQuery && (
                <button
                  type="button"
                  onClick={() => setSearchQuery('')}
                  className="absolute right-3 text-xs text-zinc-400 hover:text-zinc-700 font-mono"
                >
                  Clear
                </button>
              )}
            </div>
          </div>

          {/* Quick CTAs */}
          <div className="flex items-center justify-center flex-wrap gap-3 pt-2">
            <a
              href="https://app.heycora.in/workspace/login?source=features_hero"
              onClick={() => trackEvent('features_page_cta_clicked')}
              className="inline-flex items-center gap-2 bg-zinc-950 text-white px-5 py-3 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-sm border border-zinc-800 group cursor-pointer"
            >
              <span>Get started for Free</span>
              <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
            </a>

            <Link
              href="/pricing"
              className="inline-flex items-center gap-2 bg-white text-zinc-950 border border-zinc-200/90 hover:border-zinc-300 px-5 py-3 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-50 transition-all shadow-2xs"
            >
              <span>View Pricing &amp; ROI</span>
            </Link>
          </div>

        </div>
      </section>

      {/* ── 20 BUILT MODULES GRID (LIVE IN PRODUCT) ── */}
      {filteredBuiltModules.length > 0 && (
        <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-24">
          <div className="flex items-center justify-between mb-8">
            <div className="flex items-center gap-2.5">
              <span className="w-2.5 h-2.5 rounded-full bg-emerald-500" />
              <h2 className="font-display text-xl sm:text-2xl font-bold text-zinc-950">
                Built Modules (Live in Product)
              </h2>
            </div>
            <span className="text-xs font-mono font-semibold text-emerald-600 bg-emerald-50 border border-emerald-200/80 px-2.5 py-1 rounded-lg">
              {filteredBuiltModules.length} Modules Available
            </span>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {filteredBuiltModules.map((mod) => (
              <div
                key={mod.slug}
                className="bg-white rounded-[24px] border border-zinc-200/90 p-6 flex flex-col justify-between hover:shadow-[0_12px_30px_rgba(0,0,0,0.06)] hover:border-zinc-300 transition-all group"
              >
                <div className="space-y-4">
                  
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
                    <h3 className="font-display text-base sm:text-lg font-bold text-zinc-950 leading-snug group-hover:text-zinc-800 transition-colors">
                      {mod.title}
                    </h3>
                  </div>

                  {/* Description */}
                  <p className="text-zinc-600 text-xs sm:text-[13px] leading-relaxed line-clamp-3">
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
                <div className="pt-5 border-t border-zinc-100 mt-5 flex items-center justify-between gap-3">
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
        </section>
      )}

      {/* ── 8 UPCOMING ROADMAP MODULES ── */}
      {filteredRoadmapModules.length > 0 && (
        <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-28">
          <div className="bg-zinc-950 rounded-[36px] p-8 sm:p-12 md:p-14 text-white border border-zinc-800 shadow-2xl relative overflow-hidden">
            
            <div className="max-w-[720px] mb-12">
              <div className="inline-flex items-center gap-2 px-3 py-1 bg-amber-500/10 rounded-xl border border-amber-500/30 text-xs font-semibold text-amber-300 mb-4">
                <span className="w-2 h-2 rounded-full bg-amber-400 animate-pulse" />
                <span>Active Product Roadmap</span>
              </div>
              <h2 className="font-display text-2xl sm:text-4xl font-bold tracking-tight mb-3">
                Upcoming Modules &amp; Enterprise Features
              </h2>
              <p className="text-zinc-400 text-xs sm:text-sm leading-relaxed">
                Currently undergoing private alpha testing with high-volume photography and film studio partners across Mumbai, Delhi, and Bangalore.
              </p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
              {filteredRoadmapModules.map((item) => (
                <div
                  key={item.id}
                  className="bg-zinc-900/80 rounded-2xl p-5 border border-zinc-800 flex flex-col justify-between hover:border-zinc-700 transition-all group"
                >
                  <div className="space-y-3">
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

                    <p className="text-zinc-400 text-xs leading-relaxed">
                      {item.desc}
                    </p>
                  </div>

                  <div className="pt-4 mt-3 border-t border-zinc-800/80 flex items-center justify-between text-[11px] text-zinc-500">
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
        </section>
      )}

      {/* ── WHY CORA REPLACES 8+ SUBSCRIPTIONS ── */}
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

      {/* ── BOTTOM CALL TO ACTION ── */}
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
