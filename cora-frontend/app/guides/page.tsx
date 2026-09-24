'use client';

import React, { useState, useMemo } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { ArrowRight, BookOpen, Clock, Download, Layers, Sparkles, Search, CheckCircle2, ShieldCheck, ChevronRight } from 'lucide-react';
import { getAllGuides, getFeaturedGuide, Guide } from '@/lib/guides-data';
import { BLOG_CATEGORIES } from '@/lib/blog-data';
import { GuideCard } from '@/components/guides/GuideCard';
import { ArtisticHeroBackground } from '@/components/features/ArtisticHeroBackground';
import { BlogNewsletterBlock } from '@/components/blog/BlogNewsletterBlock';

export default function GuidesHubPage() {
  const [activeCategory, setActiveCategory] = useState<string>('all');
  const [searchQuery, setSearchQuery] = useState<string>('');

  const allPublished = useMemo(() => getAllGuides(false), []);
  const featuredGuide = useMemo(() => getFeaturedGuide(false), []);
  const allGuidesIncludingReview = useMemo(() => getAllGuides(true), []);

  // Filtered list based on active category & search query
  const filteredGuides = useMemo(() => {
    return allPublished.filter((guide) => {
      const matchesCategory = activeCategory === 'all' || guide.category === activeCategory;
      const matchesSearch =
        !searchQuery.trim() ||
        guide.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
        guide.dek.toLowerCase().includes(searchQuery.toLowerCase()) ||
        guide.tags.some((t) => t.toLowerCase().includes(searchQuery.toLowerCase()));
      return matchesCategory && matchesSearch;
    });
  }, [allPublished, activeCategory, searchQuery]);

  return (
    <main className="min-h-screen bg-white text-zinc-900 selection:bg-zinc-200">
      {/* ── SECTION 1: APPLE-GRADE HERO MASTHEAD WITH AMBIENT DEPTH ── */}
      <section className="relative w-full pt-24 sm:pt-32 pb-14 sm:pb-20 overflow-hidden border-b border-zinc-200/80 bg-gradient-to-b from-[#FAFAF9] via-[#FDFDFD] to-white">
        <ArtisticHeroBackground tone="tools" />

        <div className="relative z-10 mx-auto max-w-[1240px] px-4 sm:px-6">
          {/* Breadcrumb Navigation Pill */}
          <nav className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-zinc-200/80 bg-white/80 backdrop-blur-md text-xs font-mono text-zinc-600 mb-6 shadow-xs">
            <Link href="/" className="hover:text-zinc-950 transition-colors">
              Cora
            </Link>
            <ChevronRight className="w-3 h-3 text-zinc-400" />
            <span className="text-zinc-950 font-bold">Guides &amp; Playbooks</span>
          </nav>

          <div className="max-w-3xl">
            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-zinc-200 bg-zinc-100 text-[11px] font-mono font-semibold text-zinc-800 mb-4 shadow-xs">
              <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
              <span>AGENCY OPERATING PLAYBOOKS &amp; SOP PACKS</span>
            </div>

            <h1 className="font-display text-3xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-zinc-950 leading-[1.08]">
              In-Depth Operating Playbooks for Modern Service Agencies.
            </h1>

            <p className="mt-4 text-base sm:text-lg text-zinc-600 leading-relaxed max-w-2xl font-normal">
              Step-by-step chaptered systems on client onboarding, contract architecture, scope control, and retainer governance — complete with downloadable SOP templates.
            </p>
          </div>

          {/* Category Filter Pills & Search Bar */}
          <div className="mt-8 pt-6 border-t border-zinc-200/80 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div className="flex flex-wrap items-center gap-2">
              <button
                onClick={() => setActiveCategory('all')}
                className={`px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all cursor-pointer ${
                  activeCategory === 'all'
                    ? 'bg-zinc-950 text-white shadow-xs'
                    : 'bg-white border border-zinc-200/80 text-zinc-600 hover:text-zinc-950 hover:bg-zinc-50'
                }`}
              >
                All Playbooks
              </button>
              {BLOG_CATEGORIES.map((cat) => (
                <button
                  key={cat.id}
                  onClick={() => setActiveCategory(cat.id)}
                  className={`px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all cursor-pointer ${
                    activeCategory === cat.id
                      ? 'bg-zinc-950 text-white shadow-xs'
                      : 'bg-white border border-zinc-200/80 text-zinc-600 hover:text-zinc-950 hover:bg-zinc-50'
                  }`}
                >
                  {cat.shortName}
                </button>
              ))}
            </div>

            {/* Quick Search Input */}
            <div className="relative w-full md:w-64">
              <Search className="w-3.5 h-3.5 text-zinc-400 absolute left-3 top-1/2 -translate-y-1/2" />
              <input
                type="text"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                placeholder="Search playbooks..."
                className="w-full pl-9 pr-3 py-1.5 text-xs rounded-xl bg-white border border-zinc-200/80 text-zinc-900 placeholder:text-zinc-400 outline-none focus:border-zinc-400 transition-all shadow-xs"
              />
            </div>
          </div>
        </div>
      </section>

      {/* ── SECTION 2: MAIN DIRECTORY ── */}
      <div className="mx-auto max-w-[1240px] px-4 sm:px-6 py-12 sm:py-16">
        {allPublished.length > 0 ? (
          <div className="space-y-14">
            {/* Featured Playbook Card (Shown when not filtering or when matches) */}
            {featuredGuide && activeCategory === 'all' && !searchQuery.trim() && (
              <div className="rounded-[32px] sm:rounded-[36px] bg-white border border-zinc-200/80 p-6 sm:p-10 lg:p-12 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_20px_50px_rgb(0,0,0,0.08)] transition-all duration-300 relative overflow-hidden group">
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                  <div className="lg:col-span-7 space-y-4">
                    <div className="flex flex-wrap items-center gap-2 text-[11px] font-mono">
                      <span className="px-2.5 py-0.5 rounded-full bg-zinc-950 text-white font-bold uppercase tracking-wider text-[10px]">
                        FLAGSHIP PLAYBOOK
                      </span>
                      <span className="text-zinc-400">&bull;</span>
                      <span className="text-zinc-600 flex items-center gap-1 font-semibold">
                        <Layers className="w-3.5 h-3.5" />
                        <span>{featuredGuide.chapterCount} Chapters</span>
                      </span>
                      <span className="text-zinc-400">&bull;</span>
                      <span className="text-zinc-600 flex items-center gap-1 font-semibold">
                        <Clock className="w-3.5 h-3.5" />
                        <span>{featuredGuide.readTime}</span>
                      </span>
                    </div>

                    <h2 className="font-display text-2xl sm:text-3xl lg:text-4xl font-bold text-zinc-950 tracking-tight leading-tight group-hover:text-zinc-800 transition-colors">
                      <Link href={`/guides/${featuredGuide.slug}/`}>
                        {featuredGuide.title}
                      </Link>
                    </h2>

                    <p className="text-sm sm:text-base text-zinc-600 leading-relaxed font-normal">
                      {featuredGuide.dek}
                    </p>

                    <div className="pt-2 flex flex-wrap items-center gap-3">
                      <Link
                        href={`/guides/${featuredGuide.slug}/`}
                        className="px-6 py-3 rounded-xl bg-zinc-950 text-white text-xs sm:text-sm font-bold hover:bg-zinc-800 transition-all flex items-center gap-2 shadow-sm active:scale-98"
                      >
                        <span>Read Playbook</span>
                        <ArrowRight className="w-4 h-4" />
                      </Link>

                      {featuredGuide.downloadableAsset && (
                        <div className="px-3.5 py-2.5 rounded-xl bg-zinc-50 border border-zinc-200/80 text-xs font-mono text-zinc-700 flex items-center gap-2">
                          <Download className="w-3.5 h-3.5 text-zinc-600" />
                          <span>Includes 10-Part SOP Pack</span>
                        </div>
                      )}
                    </div>
                  </div>

                  {featuredGuide.coverImage && (
                    <div className="lg:col-span-5">
                      <Link
                        href={`/guides/${featuredGuide.slug}/`}
                        className="block aspect-[16/9] w-full rounded-2xl overflow-hidden border border-zinc-200/80 relative shadow-sm bg-zinc-100"
                      >
                        <Image
                          src={featuredGuide.coverImage}
                          alt={featuredGuide.coverAlt || featuredGuide.title}
                          fill
                          sizes="(max-width: 1024px) 100vw, 500px"
                          className="object-cover group-hover:scale-[1.03] transition-transform duration-500 ease-out"
                        />
                      </Link>
                    </div>
                  )}
                </div>
              </div>
            )}

            {/* Guides Grid */}
            {filteredGuides.length > 0 ? (
              <div>
                <div className="flex items-center justify-between text-[11px] font-mono font-bold uppercase tracking-widest text-zinc-400 mb-6">
                  <span>ALL PUBLISHED PLAYBOOKS ({filteredGuides.length})</span>
                </div>
                <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                  {filteredGuides.map((guide) => (
                    <GuideCard key={guide.slug} guide={guide} />
                  ))}
                </div>
              </div>
            ) : (
              <div className="p-12 text-center rounded-3xl border border-zinc-200/80 bg-zinc-50 max-w-md mx-auto">
                <p className="text-sm font-semibold text-zinc-700">
                  No playbooks found matching your search.
                </p>
                <button
                  onClick={() => {
                    setActiveCategory('all');
                    setSearchQuery('');
                  }}
                  className="mt-3 text-xs font-bold text-zinc-950 underline cursor-pointer"
                >
                  Reset filters
                </button>
              </div>
            )}
          </div>
        ) : (
          /* Editorial Review Stage Container */
          <div className="space-y-10">
            <div className="p-8 sm:p-12 rounded-3xl border border-zinc-200/80 bg-white text-center max-w-2xl mx-auto shadow-sm">
              <div className="w-12 h-12 rounded-2xl bg-zinc-950 text-white flex items-center justify-center mx-auto mb-4 shadow-xs">
                <BookOpen className="w-6 h-6" />
              </div>

              <h2 className="font-display text-xl sm:text-2xl font-bold tracking-tight text-zinc-950">
                New Operational Playbooks In Final Editorial Review
              </h2>

              <p className="mt-3 text-xs sm:text-sm text-zinc-600 leading-relaxed">
                We are currently finalizing our flagship chaptered playbooks on agency client onboarding, contract architecture, and cash flow workflows.
              </p>

              <div className="mt-6 max-w-md mx-auto">
                <BlogNewsletterBlock placement="inline" heading="Get early access to agency playbooks" />
              </div>
            </div>

            {allGuidesIncludingReview.length > 0 && (
              <div className="pt-6">
                <div className="text-[11px] font-mono font-bold uppercase tracking-widest text-zinc-400 mb-4">
                  PLAYBOOKS CURRENTLY IN EDITORIAL REVIEW:
                </div>
                <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                  {allGuidesIncludingReview.map((guide) => (
                    <GuideCard key={guide.slug} guide={guide} />
                  ))}
                </div>
              </div>
            )}
          </div>
        )}
      </div>

      {/* ── SECTION 3: EDITORIAL NEWSLETTER CAPTURE ── */}
      <section className="border-t border-zinc-200/80 bg-zinc-50/50 py-16">
        <div className="mx-auto max-w-[1240px] px-4 sm:px-6">
          <BlogNewsletterBlock
            heading="Master agency systems and workflows."
            tagline="Get one practical operating playbook, contract framework, or workflow breakdown delivered every week."
            buttonText="Join Free"
            placement="end"
          />
        </div>
      </section>
    </main>
  );
}
