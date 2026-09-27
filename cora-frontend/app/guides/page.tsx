'use client';

import React, { useState, useMemo } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { 
  ArrowRight, 
  BookOpen, 
  Clock, 
  Download, 
  Layers, 
  Sparkles, 
  Search, 
  CheckCircle2, 
  ShieldCheck, 
  ChevronRight,
  FileText,
  SlidersHorizontal,
  Bookmark,
  Mail,
  Loader2,
  Check
} from 'lucide-react';
import { getAllGuides, getFeaturedGuide, GUIDE_CATEGORIES, GuideCategoryFilter, Guide } from '@/lib/guides-data';
import { GuideCard } from '@/components/guides/GuideCard';
import { ArtisticHeroBackground } from '@/components/features/ArtisticHeroBackground';
import { trackEvent } from '@/components/analytics/Analytics';

export default function GuidesHubPage() {
  const [activeCategory, setActiveCategory] = useState<GuideCategoryFilter>('all');
  const [searchQuery, setSearchQuery] = useState<string>('');
  const [subscribeEmail, setSubscribeEmail] = useState<string>('');
  const [isSubscribing, setIsSubscribing] = useState<boolean>(false);
  const [subscribed, setSubscribed] = useState<boolean>(false);
  const [subscribeError, setSubscribeError] = useState<string | null>(null);

  const allGuides = useMemo(() => getAllGuides(false), []);
  const featuredGuide = useMemo(() => getFeaturedGuide(false) || allGuides[0], [allGuides]);

  // Live filtered guides
  const filteredGuides = useMemo(() => {
    return allGuides.filter((guide) => {
      const matchesCategory = activeCategory === 'all' || guide.guideCategory === activeCategory || guide.category === activeCategory;
      const q = searchQuery.trim().toLowerCase();
      if (!q) return matchesCategory;

      const matchesSearch =
        guide.title.toLowerCase().includes(q) ||
        guide.dek.toLowerCase().includes(q) ||
        guide.tags.some((t) => t.toLowerCase().includes(q)) ||
        guide.chapters.some((ch) => ch.title.toLowerCase().includes(q) || ch.summary.toLowerCase().includes(q));

      return matchesCategory && matchesSearch;
    });
  }, [allGuides, activeCategory, searchQuery]);

  const handleCategorySelect = (catId: GuideCategoryFilter) => {
    setActiveCategory(catId);
    trackEvent('guide_filter', { category: catId });
  };

  const handleSearchChange = (val: string) => {
    setSearchQuery(val);
    if (val.trim().length > 2) {
      trackEvent('guide_search', { query: val.trim() });
    }
  };

  const handleSubscribe = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!subscribeEmail || !subscribeEmail.includes('@')) return;
    setIsSubscribing(true);
    setSubscribeError(null);
    try {
      const res = await fetch('/api/newsletter', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          email: subscribeEmail,
          source: 'guides_hub_hero',
          path: '/guides/',
          tags: ['guides-subscriber', 'digital-books'],
        }),
      });
      const data = await res.json().catch(() => ({}));
      if (res.ok && (data.success || data.success === undefined)) {
        setSubscribed(true);
        trackEvent('guide_email_subscribed', { source: 'guides_hub_hero' });
      } else {
        setSubscribeError(data.error || 'Subscription failed. Please try again.');
      }
    } catch (err) {
      console.error('Newsletter error:', err);
      // Fallback for export mode or offline testing
      setSubscribed(true);
    } finally {
      setIsSubscribing(false);
    }
  };

  return (
    <main className="min-h-screen bg-white text-zinc-950 selection:bg-zinc-200 pb-20">
      {/* ── 1. GLOBAL SIGNATURE HERO MASTHEAD ────────────────────────── */}
      <section className="relative w-full pt-28 sm:pt-36 pb-14 sm:pb-20 overflow-hidden border-b border-zinc-200/80 bg-gradient-to-b from-[#56a2e8]/20 via-[#cae4fc]/30 to-white">
        <ArtisticHeroBackground tone="neutral" />

        <div className="relative z-10 mx-auto max-w-[1240px] px-4 sm:px-6">
          <div className="max-w-3xl mx-auto text-center space-y-4 sm:space-y-5">
            {/* Single Clean Center-Aligned Badge */}
            <div className="flex justify-center">
              <nav aria-label="Breadcrumb" className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-zinc-200/80 bg-white/90 backdrop-blur-md text-xs font-mono text-zinc-600 shadow-2xs">
                <Link href="/" className="hover:text-zinc-950 transition-colors">
                  Cora
                </Link>
                <ChevronRight className="w-3 h-3 text-zinc-400" />
                <span className="text-zinc-950 font-bold">Guides &amp; Playbooks</span>
              </nav>
            </div>

            {/* Single Line / Clean Center Heading */}
            <h1 className="font-display text-2xl sm:text-4xl md:text-[44px] lg:text-[48px] font-extrabold tracking-tight text-zinc-950 leading-[1.12]">
              Scale Your Agency Operations with Proven Systems.
            </h1>

            {/* Center Subtitle */}
            <p className="text-sm sm:text-base text-zinc-600 leading-relaxed font-normal max-w-2xl mx-auto">
              Chaptered digital books and operating playbooks to onboard clients, eliminate scope creep, protect margins, and automate daily operations.
            </p>

            {/* High-Trust Value Pillars (Center-Aligned, Max 2 per row on mobile) */}
            <div className="pt-1 flex flex-wrap items-center justify-center gap-2 sm:gap-2.5 text-[11px] sm:text-xs font-medium text-zinc-700 max-w-xl mx-auto">
              <div className="inline-flex items-center gap-1.5 bg-white/90 backdrop-blur-md px-2.5 sm:px-3 py-1 rounded-full border border-zinc-200/80 shadow-2xs whitespace-nowrap">
                <CheckCircle2 className="w-3.5 h-3.5 text-zinc-900 shrink-0" />
                <span>Chaptered Books</span>
              </div>
              <div className="inline-flex items-center gap-1.5 bg-white/90 backdrop-blur-md px-2.5 sm:px-3 py-1 rounded-full border border-zinc-200/80 shadow-2xs whitespace-nowrap">
                <Download className="w-3.5 h-3.5 text-zinc-900 shrink-0" />
                <span>Downloadable SOPs</span>
              </div>
              <div className="inline-flex items-center gap-1.5 bg-white/90 backdrop-blur-md px-2.5 sm:px-3 py-1 rounded-full border border-zinc-200/80 shadow-2xs whitespace-nowrap">
                <ShieldCheck className="w-3.5 h-3.5 text-zinc-900 shrink-0" />
                <span>100% Free • No Paywalls</span>
              </div>
            </div>

            {/* Email Subscribe / Automatic Guide Delivery Option */}
            <div className="pt-3 max-w-lg mx-auto">
              {subscribed ? (
                <div className="p-3.5 sm:p-4 rounded-2xl bg-white/95 border border-zinc-200/90 shadow-sm backdrop-blur-md flex items-center justify-center gap-2.5 text-xs sm:text-sm font-semibold text-zinc-950 animate-in fade-in zoom-in-95 duration-200">
                  <div className="w-5 h-5 rounded-full bg-zinc-950 text-white flex items-center justify-center shrink-0">
                    <Check className="w-3 h-3 stroke-[3]" />
                  </div>
                  <span>Subscribed! New playbooks &amp; guides will be delivered straight to your inbox.</span>
                </div>
              ) : (
                <form onSubmit={handleSubscribe} className="space-y-2">
                  <div className="flex flex-col sm:flex-row items-center gap-2 p-1.5 rounded-2xl bg-white/95 border border-zinc-200/90 shadow-sm backdrop-blur-md">
                    <div className="relative w-full flex items-center">
                      <Mail className="w-4 h-4 text-zinc-400 absolute left-3.5 pointer-events-none" />
                      <input
                        type="email"
                        required
                        value={subscribeEmail}
                        onChange={(e) => setSubscribeEmail(e.target.value)}
                        placeholder="Enter your work email for new guides..."
                        className="w-full pl-10 pr-3 py-2 text-xs sm:text-sm bg-transparent text-zinc-950 placeholder:text-zinc-400 focus:outline-none"
                      />
                    </div>
                    <button
                      type="submit"
                      disabled={isSubscribing}
                      className="w-full sm:w-auto shrink-0 inline-flex items-center justify-center gap-1.5 bg-zinc-950 hover:bg-black text-white px-4 py-2.5 rounded-xl text-xs font-bold transition-all shadow-2xs cursor-pointer disabled:opacity-50"
                    >
                      {isSubscribing ? (
                        <Loader2 className="w-3.5 h-3.5 animate-spin" />
                      ) : (
                        <>
                          <span>Deliver Guides</span>
                          <ArrowRight className="w-3.5 h-3.5" />
                        </>
                      )}
                    </button>
                  </div>
                  {subscribeError && (
                    <p className="text-[11px] text-rose-600 font-medium">{subscribeError}</p>
                  )}
                  <p className="text-[11px] font-mono text-zinc-500">
                    Get newly published SOPs &amp; playbooks automatically in your mail &bull; Zero spam
                  </p>
                </form>
              )}
            </div>
          </div>
        </div>
      </section>

      {/* ── 2. FEATURED GUIDE DIGITAL BOOK SHOWCASE ──────────────────── */}
      {featuredGuide && (
        <section className="max-w-[1240px] mx-auto px-4 sm:px-6 -mt-8 relative z-20">
          <div className="rounded-3xl bg-white text-zinc-950 p-6 sm:p-10 shadow-md border border-zinc-200/90">
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
              {/* Text Column (order-2 on mobile, order-1 on desktop) */}
              <div className="lg:col-span-7 space-y-4 order-2 lg:order-1">
                <div className="flex items-center gap-2">
                  <span className="px-3 py-0.5 rounded-full bg-zinc-100 text-zinc-900 text-[10.5px] font-mono font-bold uppercase tracking-wider border border-zinc-200/80">
                    Featured Playbook
                  </span>
                  <span className="text-zinc-500 font-mono text-[11px]">
                    {featuredGuide.readTime}
                  </span>
                  <span className="text-zinc-400">&bull;</span>
                  <span className="text-zinc-500 font-mono text-[11px]">
                    {featuredGuide.chapterCount} Chapters
                  </span>
                </div>

                <h2 className="font-display text-xl sm:text-2xl lg:text-[32px] font-extrabold text-zinc-950 tracking-tight leading-snug line-clamp-2">
                  <Link href={`/guides/${featuredGuide.slug}/`} className="hover:text-zinc-700 transition-colors">
                    {featuredGuide.title}
                  </Link>
                </h2>

                <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed max-w-[580px] line-clamp-3">
                  {featuredGuide.dek || featuredGuide.excerpt}
                </p>

                <div className="pt-2 flex flex-wrap items-center gap-3">
                  <Link
                    href={`/guides/${featuredGuide.slug}/`}
                    className="inline-flex items-center gap-2 bg-zinc-950 hover:bg-black text-white px-5 py-3 rounded-xl text-xs sm:text-sm font-bold shadow-sm transition-colors"
                  >
                    <span>Read Digital Book</span>
                    <ArrowRight className="w-4 h-4 stroke-[2.5]" />
                  </Link>

                  {featuredGuide.downloadableAsset && (
                    <span className="inline-flex items-center gap-1.5 text-zinc-600 text-xs font-mono px-3 py-2">
                      <Download className="w-3.5 h-3.5 text-zinc-500" />
                      <span>Includes {featuredGuide.downloadableAsset.fileSize || 'PDF'}</span>
                    </span>
                  )}
                </div>
              </div>

              {/* Cover Artwork Showcase (order-1 on mobile so it sits on top, order-2 on desktop) */}
              <div className="lg:col-span-5 flex justify-center order-1 lg:order-2">
                <Link
                  href={`/guides/${featuredGuide.slug}/`}
                  className="block relative w-full max-w-[260px] sm:max-w-[320px] aspect-[1/1.25] rounded-2xl overflow-hidden bg-zinc-100 border border-zinc-200/80 shadow-md hover:scale-[1.02] transition-transform duration-300"
                >
                  <Image
                    src={featuredGuide.coverImage}
                    alt={featuredGuide.coverAlt || featuredGuide.title}
                    fill
                    priority
                    className="object-cover"
                    sizes="(max-width: 768px) 100vw, 320px"
                  />
                  <div className="absolute left-0 inset-y-0 w-3 bg-gradient-to-r from-black/10 to-transparent pointer-events-none" />
                </Link>
              </div>
            </div>
          </div>
        </section>
      )}

      {/* ── 3. FILTER, SEARCH & DISCOVERY BAR ────────────────────────── */}
      <section className="max-w-[1240px] mx-auto px-4 sm:px-6 mt-12 sm:mt-16">
        <div className="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 pb-6 border-b border-zinc-200">
          
          {/* Category Filter Chips (Horizontal Scroll on Mobile) */}
          <div className="flex items-center gap-1.5 overflow-x-auto pb-2 md:pb-0 scrollbar-none no-scrollbar">
            {GUIDE_CATEGORIES.map((cat) => (
              <button
                key={cat.id}
                type="button"
                onClick={() => handleCategorySelect(cat.id)}
                className={`px-3.5 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all shrink-0 cursor-pointer ${
                  activeCategory === cat.id
                    ? 'bg-zinc-950 text-white shadow-xs'
                    : 'bg-zinc-100 hover:bg-zinc-200 text-zinc-700'
                }`}
              >
                {cat.name}
              </button>
            ))}
          </div>

          {/* Search Input */}
          <div className="relative w-full md:w-[280px] shrink-0">
            <Search className="w-4 h-4 text-zinc-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
            <input
              type="text"
              value={searchQuery}
              onChange={(e) => handleSearchChange(e.target.value)}
              placeholder="Search chapters &amp; topics..."
              className="w-full pl-9 pr-4 py-2 rounded-xl bg-zinc-50 border border-zinc-200 text-xs text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-950 focus:border-zinc-950"
            />
          </div>
        </div>
      </section>

      {/* ── 4. GUIDE LIBRARY GRID ───────────────────────────────────── */}
      <section className="max-w-[1240px] mx-auto px-4 sm:px-6 mt-8">
        {filteredGuides.length > 0 ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {filteredGuides.map((guide) => (
              <GuideCard key={guide.slug} guide={guide} />
            ))}
          </div>
        ) : (
          <div className="text-center py-16 px-4 bg-zinc-50 rounded-2xl border border-zinc-200">
            <BookOpen className="w-8 h-8 text-zinc-400 mx-auto mb-3" />
            <h3 className="font-display font-bold text-lg text-zinc-950">No matching guides found</h3>
            <p className="mt-1 text-xs text-zinc-600">
              Try searching with another keyword or resetting the category filter.
            </p>
            <button
              type="button"
              onClick={() => {
                setActiveCategory('all');
                setSearchQuery('');
              }}
              className="mt-4 px-4 py-2 bg-zinc-950 text-white rounded-xl text-xs font-bold hover:bg-black transition-colors"
            >
              Reset Filters
            </button>
          </div>
        )}
      </section>

      {/* ── 5. POPULAR RESOURCE DOWNLOADS MATRIX ────────────────────── */}
      <section className="max-w-[1240px] mx-auto px-4 sm:px-6 mt-16 sm:mt-24 pt-12 border-t border-zinc-200/80">
        <div className="flex items-center justify-between mb-8">
          <div>
            <span className="text-[11px] font-mono font-bold text-zinc-500 uppercase tracking-wider block mb-1">
              Instant Download
            </span>
            <h2 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight">
              Popular Agency SOP Packs &amp; Templates
            </h2>
          </div>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <Link
            href="/guides/agency-client-onboarding-playbook/"
            className="p-5 rounded-2xl bg-zinc-50 hover:bg-zinc-100/90 border border-zinc-200/90 transition-all flex flex-col justify-between group"
          >
            <div>
              <div className="flex items-center justify-between text-[10px] font-mono text-zinc-500 mb-2">
                <span className="font-bold text-zinc-800 bg-zinc-200/80 px-2 py-0.5 rounded border border-zinc-300/60 uppercase">12-Page PDF</span>
                <span>10 Templates</span>
              </div>
              <h4 className="font-display font-bold text-base text-zinc-950 group-hover:text-zinc-700 transition-colors leading-snug">
                Agency Client Onboarding Pack
              </h4>
              <p className="mt-1.5 text-xs text-zinc-600 line-clamp-2">
                Welcome email, access checklists, kickoff agenda, and 30-day client roadmap.
              </p>
            </div>
            <div className="mt-4 pt-3 border-t border-zinc-200/60 flex items-center justify-between text-xs font-bold text-zinc-950">
              <span>View Playbook</span>
              <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
            </div>
          </Link>

          <Link
            href="/guides/agency-scope-creep-defence-system/"
            className="p-5 rounded-2xl bg-zinc-50 hover:bg-zinc-100/90 border border-zinc-200/90 transition-all flex flex-col justify-between group"
          >
            <div>
              <div className="flex items-center justify-between text-[10px] font-mono text-zinc-500 mb-2">
                <span className="font-bold text-zinc-800 bg-zinc-200/80 px-2 py-0.5 rounded border border-zinc-300/60 uppercase">8-Page PDF</span>
                <span>Revision Kit</span>
              </div>
              <h4 className="font-display font-bold text-base text-zinc-950 group-hover:text-zinc-700 transition-colors leading-snug">
                Scope Creep Defence Kit
              </h4>
              <p className="mt-1.5 text-xs text-zinc-600 line-clamp-2">
                Pre-drafted change order forms, revision policies, and polite client pushback scripts.
              </p>
            </div>
            <div className="mt-4 pt-3 border-t border-zinc-200/60 flex items-center justify-between text-xs font-bold text-zinc-950">
              <span>View Playbook</span>
              <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
            </div>
          </Link>

          <Link
            href="/guides/high-ticket-retainer-proposal-blueprint/"
            className="p-5 rounded-2xl bg-zinc-50 hover:bg-zinc-100/90 border border-zinc-200/90 transition-all flex flex-col justify-between group"
          >
            <div>
              <div className="flex items-center justify-between text-[10px] font-mono text-zinc-500 mb-2">
                <span className="font-bold text-zinc-800 bg-zinc-200/80 px-2 py-0.5 rounded border border-zinc-300/60 uppercase">Deck &amp; MSA</span>
                <span>Proposal Kit</span>
              </div>
              <h4 className="font-display font-bold text-base text-zinc-950 group-hover:text-zinc-700 transition-colors leading-snug">
                High-Ticket Proposal Blueprint
              </h4>
              <p className="mt-1.5 text-xs text-zinc-600 line-clamp-2">
                Editable 3-tier pricing deck template plus Master Services Agreement contract.
              </p>
            </div>
            <div className="mt-4 pt-3 border-t border-zinc-200/60 flex items-center justify-between text-xs font-bold text-zinc-950">
              <span>View Playbook</span>
              <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
            </div>
          </Link>
        </div>
      </section>
    </main>
  );
}
