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
  Search, 
  CheckCircle2, 
  ShieldCheck, 
  ChevronRight,
  ChevronDown,
  Trash2,
  Mail,
  Loader2,
  Check
} from 'lucide-react';
import { getAllGuides, getFeaturedGuide, GUIDE_CATEGORIES, GuideCategoryFilter, Guide } from '@/lib/guides-data';
import { GuideCard } from '@/components/guides/GuideCard';
import { ArtisticHeroBackground } from '@/components/features/ArtisticHeroBackground';
import { trackEvent } from '@/components/analytics/Analytics';

export default function GuidesHubPage() {
  const [activeTopic, setActiveTopic] = useState<string>('all');
  const [activeContentType, setActiveContentType] = useState<string>('all');
  const [activeFormat, setActiveFormat] = useState<string>('all');
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
      // 1. Topic filter
      const matchesTopic =
        activeTopic === 'all' ||
        guide.guideCategory === activeTopic ||
        guide.category === activeTopic;

      // 2. Content Type filter
      const matchesType =
        activeContentType === 'all' ||
        guide.qualityLabel.toLowerCase() === activeContentType.toLowerCase() ||
        guide.title.toLowerCase().includes(activeContentType.toLowerCase());

      // 3. Format filter
      const matchesFormat =
        activeFormat === 'all' ||
        (activeFormat === 'pdf' && guide.downloadableAsset?.fileType === 'pdf') ||
        (activeFormat === 'templates' &&
          (guide.downloadableAsset?.fileType === 'template' ||
            guide.downloadableAsset?.fileType === 'xlsx' ||
            guide.downloadableAsset?.fileType === 'docx' ||
            guide.tags.includes('Templates') ||
            guide.resourceBadges?.includes('Templates')));

      // 4. Search query
      const q = searchQuery.trim().toLowerCase();
      const matchesSearch =
        !q ||
        guide.title.toLowerCase().includes(q) ||
        guide.dek.toLowerCase().includes(q) ||
        guide.tags.some((t) => t.toLowerCase().includes(q)) ||
        guide.chapters.some(
          (ch) => ch.title.toLowerCase().includes(q) || ch.summary.toLowerCase().includes(q)
        );

      return matchesTopic && matchesType && matchesFormat && matchesSearch;
    });
  }, [allGuides, activeTopic, activeContentType, activeFormat, searchQuery]);

  const hasActiveFilters =
    activeTopic !== 'all' ||
    activeContentType !== 'all' ||
    activeFormat !== 'all' ||
    searchQuery.trim() !== '';

  const handleClearAllFilters = () => {
    setActiveTopic('all');
    setActiveContentType('all');
    setActiveFormat('all');
    setSearchQuery('');
    trackEvent('guide_clear_filters');
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
      setSubscribed(true);
    } finally {
      setIsSubscribing(false);
    }
  };

  const selectedTopicName = useMemo(() => {
    if (activeTopic === 'all') return null;
    const cat = GUIDE_CATEGORIES.find((c) => c.id === activeTopic);
    return cat ? cat.name : activeTopic;
  }, [activeTopic]);

  return (
    <main className="min-h-screen bg-white text-zinc-950 selection:bg-zinc-200 pb-20">
      {/* ── 1. GLOBAL SIGNATURE HERO MASTHEAD ────────────────────────── */}
      <section className="relative w-full pt-28 sm:pt-36 pb-14 sm:pb-20 overflow-hidden border-b border-zinc-200/80 bg-gradient-to-b from-[#56a2e8]/20 via-[#cae4fc]/30 to-white">
        <ArtisticHeroBackground tone="neutral" />

        <div className="relative z-10 mx-auto max-w-[1240px] px-4 sm:px-6">
          <div className="max-w-4xl mx-auto text-center space-y-4 sm:space-y-6">
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
            <h1 className="font-display text-[2.25rem] xs:text-[2.65rem] sm:text-[50px] md:text-[58px] lg:text-[64px] font-medium tracking-[-0.035em] text-zinc-950 leading-[1.22] xs:leading-[1.18] sm:leading-[1.16] md:leading-[1.12] max-w-4xl mx-auto">
              Scale Your Agency Operations with Proven Systems.
            </h1>

            {/* Center Subtitle */}
            <p className="text-sm sm:text-base md:text-lg text-zinc-600 leading-relaxed sm:leading-7 font-normal max-w-2xl mx-auto">
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
      <section className="max-w-[1240px] mx-auto px-4 sm:px-6 mt-14 sm:mt-20">
        <div className="bg-[#FAFAF8] rounded-2xl border border-zinc-200/90 p-4 sm:p-6 shadow-2xs">
          {/* Breadcrumb & Section Header */}
          <div className="mb-4">
            <div className="flex items-center gap-1.5 text-[11px] font-mono text-zinc-500 mb-1">
              <Link href="/" className="hover:text-zinc-950 transition-colors">
                Home
              </Link>
              <span>&gt;</span>
              <button
                type="button"
                onClick={handleClearAllFilters}
                className="hover:text-zinc-950 transition-colors cursor-pointer"
              >
                Guides
              </button>
              {selectedTopicName && (
                <>
                  <span>&gt;</span>
                  <span className="text-zinc-900 font-semibold">{selectedTopicName}</span>
                </>
              )}
              {activeFormat !== 'all' && (
                <>
                  <span>&gt;</span>
                  <span className="text-zinc-900 font-semibold uppercase">{activeFormat}</span>
                </>
              )}
            </div>
            <div className="flex items-baseline justify-between">
              <h3 className="font-display text-lg sm:text-xl font-bold text-zinc-950">
                Explore {filteredGuides.length} Guide {filteredGuides.length === 1 ? 'Resource' : 'Resources'}
              </h3>
            </div>
          </div>

          {/* 4-Field Dropdown & Search Controls */}
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            {/* 1. All Topics Dropdown */}
            <div>
              <label className="text-xs font-bold text-zinc-900 mb-1.5 block">
                All Topics
              </label>
              <div className="relative">
                <select
                  value={activeTopic}
                  onChange={(e) => setActiveTopic(e.target.value)}
                  className="w-full appearance-none bg-white border border-zinc-200 hover:border-zinc-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-zinc-900 font-medium focus:outline-none focus:ring-2 focus:ring-zinc-950 focus:border-zinc-950 shadow-2xs pr-9 cursor-pointer transition-colors"
                >
                  <option value="all">- All Topics -</option>
                  <option value="operations">Operations</option>
                  <option value="client-management">Client Management</option>
                  <option value="sales-proposals">Sales &amp; Proposals</option>
                  <option value="growth">Growth</option>
                  <option value="finance">Finance</option>
                  <option value="agency-profitability">Agency Profitability</option>
                  <option value="ai-automation">AI &amp; Automation</option>
                  <option value="research">Research</option>
                </select>
                <ChevronDown className="w-4 h-4 text-zinc-500 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
              </div>
            </div>

            {/* 2. All Content Types Dropdown */}
            <div>
              <label className="text-xs font-bold text-zinc-900 mb-1.5 block">
                All Content Types
              </label>
              <div className="relative">
                <select
                  value={activeContentType}
                  onChange={(e) => setActiveContentType(e.target.value)}
                  className="w-full appearance-none bg-white border border-zinc-200 hover:border-zinc-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-zinc-900 font-medium focus:outline-none focus:ring-2 focus:ring-zinc-950 focus:border-zinc-950 shadow-2xs pr-9 cursor-pointer transition-colors"
                >
                  <option value="all">- All Content Types -</option>
                  <option value="playbook">Playbook</option>
                  <option value="guide">Guide</option>
                  <option value="manual">Manual</option>
                  <option value="blueprint">Blueprint</option>
                </select>
                <ChevronDown className="w-4 h-4 text-zinc-500 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
              </div>
            </div>

            {/* 3. All Formats Dropdown */}
            <div>
              <label className="text-xs font-bold text-zinc-900 mb-1.5 block">
                All Formats
              </label>
              <div className="relative">
                <select
                  value={activeFormat}
                  onChange={(e) => setActiveFormat(e.target.value)}
                  className="w-full appearance-none bg-white border border-zinc-200 hover:border-zinc-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-zinc-900 font-medium focus:outline-none focus:ring-2 focus:ring-zinc-950 focus:border-zinc-950 shadow-2xs pr-9 cursor-pointer transition-colors"
                >
                  <option value="all">- All Formats -</option>
                  <option value="pdf">PDF Included</option>
                  <option value="templates">Templates &amp; Sheets</option>
                </select>
                <ChevronDown className="w-4 h-4 text-zinc-500 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
              </div>
            </div>

            {/* 4. Search Input */}
            <div>
              <label className="text-xs font-bold text-zinc-900 mb-1.5 block">
                Search all resources
              </label>
              <div className="relative">
                <Search className="w-4 h-4 text-zinc-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                <input
                  type="text"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  placeholder="Search resources..."
                  className="w-full bg-white border border-zinc-200 hover:border-zinc-300 rounded-xl pl-9 pr-3.5 py-2.5 text-xs sm:text-sm text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-950 focus:border-zinc-950 shadow-2xs transition-colors"
                />
              </div>
            </div>
          </div>

          {/* Clear All Filters Button */}
          {hasActiveFilters && (
            <div className="mt-4 pt-3.5 border-t border-zinc-200/80 flex items-center justify-between">
              <button
                type="button"
                onClick={handleClearAllFilters}
                className="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-600 hover:text-zinc-950 transition-colors cursor-pointer"
              >
                <Trash2 className="w-3.5 h-3.5 text-zinc-400" />
                <span>Clear All Filters</span>
              </button>
              <span className="text-[11px] font-mono text-zinc-500">
                Showing {filteredGuides.length} of {allGuides.length}
              </span>
            </div>
          )}
        </div>
      </section>

      {/* ── 4. GUIDE LIBRARY GRID ───────────────────────────────────── */}
      <section className="max-w-[1240px] mx-auto px-4 sm:px-6 mt-8">
        {filteredGuides.length > 0 ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {filteredGuides.map((guide, idx) => (
              <GuideCard key={guide.slug} guide={guide} index={idx} />
            ))}
          </div>
        ) : (
          <div className="text-center py-16 px-4 bg-zinc-50 rounded-2xl border border-zinc-200">
            <BookOpen className="w-8 h-8 text-zinc-400 mx-auto mb-3" />
            <h3 className="font-display font-bold text-lg text-zinc-950">No matching guides found</h3>
            <p className="mt-1 text-xs text-zinc-600">
              Try searching with another keyword or resetting the filters.
            </p>
            <button
              type="button"
              onClick={handleClearAllFilters}
              className="mt-4 px-4 py-2 bg-zinc-950 text-white rounded-xl text-xs font-bold hover:bg-black transition-colors cursor-pointer"
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
            className="p-5 rounded-2xl bg-[#ECEFFE] hover:bg-[#E2E7FC] border border-[#D7DCF5] hover:border-[#C6CEEE] transition-all flex flex-col justify-between group shadow-2xs hover:shadow-xs"
          >
            <div>
              <div className="flex items-center justify-between text-[10px] font-mono text-zinc-600 mb-2">
                <span className="font-bold text-indigo-950 bg-white/90 px-2 py-0.5 rounded border border-white/80 uppercase shadow-2xs">12-Page PDF</span>
                <span>10 Templates</span>
              </div>
              <h4 className="font-display font-bold text-base text-zinc-950 group-hover:text-indigo-950 transition-colors leading-snug">
                Agency Client Onboarding Pack
              </h4>
              <p className="mt-1.5 text-xs text-zinc-600 line-clamp-2">
                Welcome email, access checklists, kickoff agenda, and 30-day client roadmap.
              </p>
            </div>
            <div className="mt-4 pt-3 border-t border-indigo-200/60 flex items-center justify-between text-xs font-bold text-zinc-950">
              <span>View Playbook</span>
              <ArrowRight className="w-3.5 h-3.5 text-zinc-500 group-hover:translate-x-0.5 transition-transform" />
            </div>
          </Link>

          <Link
            href="/guides/agency-scope-creep-defence-system/"
            className="p-5 rounded-2xl bg-[#E2F1F8] hover:bg-[#D4EAF6] border border-[#CCE3EF] hover:border-[#B5D7E8] transition-all flex flex-col justify-between group shadow-2xs hover:shadow-xs"
          >
            <div>
              <div className="flex items-center justify-between text-[10px] font-mono text-zinc-600 mb-2">
                <span className="font-bold text-sky-950 bg-white/90 px-2 py-0.5 rounded border border-white/80 uppercase shadow-2xs">8-Page PDF</span>
                <span>Revision Kit</span>
              </div>
              <h4 className="font-display font-bold text-base text-zinc-950 group-hover:text-sky-950 transition-colors leading-snug">
                Scope Creep Defence Kit
              </h4>
              <p className="mt-1.5 text-xs text-zinc-600 line-clamp-2">
                Pre-drafted change order forms, revision policies, and polite client pushback scripts.
              </p>
            </div>
            <div className="mt-4 pt-3 border-t border-sky-200/60 flex items-center justify-between text-xs font-bold text-zinc-950">
              <span>View Playbook</span>
              <ArrowRight className="w-3.5 h-3.5 text-zinc-500 group-hover:translate-x-0.5 transition-transform" />
            </div>
          </Link>

          <Link
            href="/guides/high-ticket-retainer-proposal-blueprint/"
            className="p-5 rounded-2xl bg-[#E6F3EC] hover:bg-[#D8EDE0] border border-[#CEE5D6] hover:border-[#B9DCC4] transition-all flex flex-col justify-between group shadow-2xs hover:shadow-xs"
          >
            <div>
              <div className="flex items-center justify-between text-[10px] font-mono text-zinc-600 mb-2">
                <span className="font-bold text-emerald-950 bg-white/90 px-2 py-0.5 rounded border border-white/80 uppercase shadow-2xs">Deck &amp; MSA</span>
                <span>Proposal Kit</span>
              </div>
              <h4 className="font-display font-bold text-base text-zinc-950 group-hover:text-emerald-950 transition-colors leading-snug">
                High-Ticket Proposal Blueprint
              </h4>
              <p className="mt-1.5 text-xs text-zinc-600 line-clamp-2">
                Editable 3-tier pricing deck template plus Master Services Agreement contract.
              </p>
            </div>
            <div className="mt-4 pt-3 border-t border-emerald-200/60 flex items-center justify-between text-xs font-bold text-zinc-950">
              <span>View Playbook</span>
              <ArrowRight className="w-3.5 h-3.5 text-zinc-500 group-hover:translate-x-0.5 transition-transform" />
            </div>
          </Link>
        </div>
      </section>
    </main>
  );
}
