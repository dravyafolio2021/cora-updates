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
  Check,
  Sparkles,
  Zap,
  FileText
} from 'lucide-react';
import { BLOG_CATEGORIES, BlogArticle, BlogCategory, normalizeBlogCategory } from '@/lib/blog-data';
import { ArtisticHeroBackground } from '@/components/features/ArtisticHeroBackground';
import { trackEvent } from '@/components/analytics/Analytics';

interface BlogHubClientViewProps {
  initialArticles: BlogArticle[];
  initialFeaturedArticle?: BlogArticle;
}

const THEME_STYLES: Record<string, { bg: string; hoverBg: string; border: string; hoverBorder: string; accent: string }> = {
  lavender: {
    bg: 'bg-[#ECEFFE]',
    hoverBg: 'hover:bg-[#E2E7FC]',
    border: 'border-[#D7DCF5]',
    hoverBorder: 'hover:border-[#C6CEEE]',
    accent: 'text-indigo-950',
  },
  sky: {
    bg: 'bg-[#E2F1F8]',
    hoverBg: 'hover:bg-[#D4EAF6]',
    border: 'border-[#CCE3EF]',
    hoverBorder: 'hover:border-[#B5D7E8]',
    accent: 'text-sky-950',
  },
  sage: {
    bg: 'bg-[#E6F3EC]',
    hoverBg: 'hover:bg-[#D8EDE0]',
    border: 'border-[#CEE5D6]',
    hoverBorder: 'hover:border-[#B9DCC4]',
    accent: 'text-emerald-950',
  },
  amber: {
    bg: 'bg-[#FAF3E7]',
    hoverBg: 'hover:bg-[#F5EBDA]',
    border: 'border-[#EFE1CC]',
    hoverBorder: 'hover:border-[#E4D2B6]',
    accent: 'text-amber-950',
  },
  rose: {
    bg: 'bg-[#FDF0F3]',
    hoverBg: 'hover:bg-[#FCE4EC]',
    border: 'border-[#F7D8E1]',
    hoverBorder: 'hover:border-[#EFC4D1]',
    accent: 'text-rose-950',
  },
};

const THEME_KEYS = ['lavender', 'sky', 'sage', 'amber', 'rose'];

export function BlogHubClientView({ initialArticles, initialFeaturedArticle }: BlogHubClientViewProps) {
  const [activeTopic, setActiveTopic] = useState<string>('all');
  const [activeContentType, setActiveContentType] = useState<string>('all');
  const [activeFormat, setActiveFormat] = useState<string>('all');
  const [searchQuery, setSearchQuery] = useState<string>('');

  const [subscribeEmail, setSubscribeEmail] = useState<string>('');
  const [isSubscribing, setIsSubscribing] = useState<boolean>(false);
  const [subscribed, setSubscribed] = useState<boolean>(false);
  const [subscribeError, setSubscribeError] = useState<string | null>(null);

  const allArticles = initialArticles;
  const featuredArticle = initialFeaturedArticle || allArticles[0];

  // Live filtered articles
  const filteredArticles = useMemo(() => {
    return allArticles.filter((article) => {
      // 1. Topic filter
      const normArticleTopic = normalizeBlogCategory(article.category);
      const matchesTopic =
        activeTopic === 'all' ||
        normArticleTopic === activeTopic ||
        article.category === activeTopic;

      // 2. Content Type filter
      const matchesType =
        activeContentType === 'all' ||
        (article.qualityLabel && article.qualityLabel.toLowerCase().includes(activeContentType.toLowerCase())) ||
        article.title.toLowerCase().includes(activeContentType.toLowerCase());

      // 3. Format / Reading Time filter
      const matchesFormat =
        activeFormat === 'all' ||
        (activeFormat === 'quick_answer' && Boolean(article.quickAnswer)) ||
        (activeFormat === 'deep_dive' && (article.readTime.includes('8') || article.readTime.includes('9') || article.readTime.includes('10') || article.readTime.includes('12') || article.readTime.includes('15'))) ||
        (activeFormat === 'tool' && (Boolean(article.relatedTool) || Boolean((article as any).relationships?.some((r: any) => r.type === 'related_tool' || r.type === 'tool'))));

      // 4. Search query
      const q = searchQuery.trim().toLowerCase();
      const matchesSearch =
        !q ||
        article.title.toLowerCase().includes(q) ||
        (article.dek && article.dek.toLowerCase().includes(q)) ||
        (article.excerpt && article.excerpt.toLowerCase().includes(q)) ||
        (article.tags && article.tags.some((t) => t.toLowerCase().includes(q))) ||
        (article.primaryKeyword && article.primaryKeyword.toLowerCase().includes(q));

      return matchesTopic && matchesType && matchesFormat && matchesSearch;
    });
  }, [allArticles, activeTopic, activeContentType, activeFormat, searchQuery]);

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
    trackEvent('blog_clear_filters');
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
          source: 'blog_hub_hero',
          path: '/blog/',
          tags: ['blog-subscriber', 'editorial-systems'],
        }),
      });
      const data = await res.json().catch(() => ({}));
      if (res.ok && (data.success || data.success === undefined)) {
        setSubscribed(true);
        trackEvent('blog_email_subscribed', { source: 'blog_hub_hero' });
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
    const cat = BLOG_CATEGORIES.find((c) => c.id === activeTopic || c.slug === activeTopic);
    return cat ? cat.name : activeTopic;
  }, [activeTopic]);

  return (
    <main className="min-h-screen bg-white text-zinc-950 selection:bg-zinc-200 pb-20">
      {/* ── 1. GLOBAL SIGNATURE HERO MASTHEAD ────────────────────────── */}
      <section className="relative w-full pt-20 sm:pt-24 pb-8 sm:pb-12 overflow-hidden border-b border-zinc-200/80 bg-gradient-to-b from-[#56a2e8]/20 via-[#cae4fc]/30 to-white">
        <ArtisticHeroBackground tone="neutral" />

        <div className="relative z-10 mx-auto max-w-[1240px] px-4 sm:px-6">
          <div className="max-w-4xl mx-auto text-center space-y-3 sm:space-y-4">
            {/* Single Clean Center-Aligned Badge */}
            <div className="flex justify-center">
              <nav aria-label="Breadcrumb" className="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-zinc-200/80 bg-white/90 backdrop-blur-md text-[11px] font-mono text-zinc-600 shadow-2xs">
                <Link href="/" className="hover:text-zinc-950 transition-colors">
                  Cora
                </Link>
                <ChevronRight className="w-3 h-3 text-zinc-400" />
                <span className="text-zinc-950 font-bold">Editorial Articles</span>
              </nav>
            </div>

            {/* Clean Center Heading */}
            <h1 className="font-display text-3xl xs:text-4xl sm:text-5xl md:text-6xl font-medium tracking-tight text-zinc-950 leading-tight max-w-3xl mx-auto">
              Cora Blogs
            </h1>

            {/* Center Subtitle */}
            <p className="text-xs sm:text-sm md:text-base text-zinc-600 leading-relaxed font-normal max-w-2xl mx-auto">
              Focused editorial answers to client management, scope creep defence, margin protection, and autonomous workflows.
            </p>

            {/* High-Trust Value Pillars (Center-Aligned) */}
            <div className="pt-0.5 flex flex-wrap items-center justify-center gap-2 text-[11px] font-medium text-zinc-700 max-w-xl mx-auto">
              <div className="inline-flex items-center gap-1.5 bg-white/90 backdrop-blur-md px-2.5 py-0.5 rounded-full border border-zinc-200/80 shadow-2xs whitespace-nowrap">
                <Sparkles className="w-3.5 h-3.5 text-amber-500 fill-amber-500 shrink-0" />
                <span>45s Direct Answers</span>
              </div>
              <div className="inline-flex items-center gap-1.5 bg-white/90 backdrop-blur-md px-2.5 py-0.5 rounded-full border border-zinc-200/80 shadow-2xs whitespace-nowrap">
                <Zap className="w-3.5 h-3.5 text-zinc-900 shrink-0" />
                <span>Zero Fluff • No Paywall</span>
              </div>
              <div className="inline-flex items-center gap-1.5 bg-white/90 backdrop-blur-md px-2.5 py-0.5 rounded-full border border-zinc-200/80 shadow-2xs whitespace-nowrap">
                <ShieldCheck className="w-3.5 h-3.5 text-zinc-900 shrink-0" />
                <span>Free Tools &amp; SOPs</span>
              </div>
            </div>

            {/* Email Subscribe / Automatic Article Delivery */}
            <div className="pt-2 max-w-lg mx-auto">
              {subscribed ? (
                <div className="p-3.5 sm:p-4 rounded-2xl bg-white/95 border border-zinc-200/90 shadow-sm backdrop-blur-md flex items-center justify-center gap-2.5 text-xs sm:text-sm font-semibold text-zinc-950 animate-in fade-in zoom-in-95 duration-200">
                  <div className="w-5 h-5 rounded-full bg-zinc-950 text-white flex items-center justify-center shrink-0">
                    <Check className="w-3 h-3 stroke-[3]" />
                  </div>
                  <span>Subscribed! New operational systems will be delivered straight to your inbox.</span>
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
                        placeholder="Enter your work email for new articles..."
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
                          <span>Deliver Articles</span>
                          <ArrowRight className="w-3.5 h-3.5" />
                        </>
                      )}
                    </button>
                  </div>
                  {subscribeError && (
                    <p className="text-[11px] text-rose-600 font-medium">{subscribeError}</p>
                  )}
                  <p className="text-[11px] font-mono text-zinc-500">
                    Get newly published SOPs &amp; editorial answers automatically in your mail &bull; Zero spam
                  </p>
                </form>
              )}
            </div>
          </div>
        </div>
      </section>

      {/* ── 2. FEATURED ARTICLE DIGITAL SHOWCASE ──────────────────────── */}
      {featuredArticle && (
        <section className="max-w-[1240px] mx-auto px-4 sm:px-6 -mt-8 relative z-20">
          <div className="rounded-3xl bg-white text-zinc-950 p-6 sm:p-10 shadow-md border border-zinc-200/90">
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
              {/* Text Column (order-2 on mobile, order-1 on desktop) */}
              <div className="lg:col-span-7 space-y-4 order-2 lg:order-1">
                <div className="flex items-center gap-2 flex-wrap">
                  <span className="px-3 py-0.5 rounded-full bg-zinc-100 text-zinc-900 text-[10.5px] font-mono font-bold uppercase tracking-wider border border-zinc-200/80">
                    Featured Editorial
                  </span>
                  <span className="text-zinc-500 font-mono text-[11px]">
                    {featuredArticle.readTime}
                  </span>
                  <span className="text-zinc-400">&bull;</span>
                  <span className="text-zinc-500 font-mono text-[11px] uppercase">
                    {featuredArticle.category.replace(/-/g, ' ')}
                  </span>
                  {featuredArticle.quickAnswer && (
                    <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-900 border border-amber-200/70 text-[10px] font-mono font-bold">
                      <Sparkles className="w-2.5 h-2.5 text-amber-600 fill-amber-600" />
                      <span>45s Answer</span>
                    </span>
                  )}
                </div>

                <h2 className="font-display text-xl sm:text-2xl lg:text-[32px] font-extrabold text-zinc-950 tracking-tight leading-snug line-clamp-2">
                  <Link href={`/blog/${featuredArticle.slug}/`} className="hover:text-zinc-700 transition-colors">
                    {featuredArticle.title}
                  </Link>
                </h2>

                <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed max-w-[580px] line-clamp-3">
                  {featuredArticle.dek || featuredArticle.excerpt}
                </p>

                <div className="pt-2 flex flex-wrap items-center gap-3">
                  <Link
                    href={`/blog/${featuredArticle.slug}/`}
                    className="inline-flex items-center gap-2 bg-zinc-950 hover:bg-black text-white px-5 py-3 rounded-xl text-xs sm:text-sm font-bold shadow-sm transition-colors"
                  >
                    <span>Read Article</span>
                    <ArrowRight className="w-4 h-4 stroke-[2.5]" />
                  </Link>

                  {featuredArticle.parentGuide && (
                    <Link
                      href={`/guides/${featuredArticle.parentGuide.slug}/`}
                      className="inline-flex items-center gap-1.5 text-zinc-600 hover:text-zinc-950 text-xs font-mono px-3 py-2 border border-zinc-200/80 rounded-xl bg-zinc-50 transition-colors"
                    >
                      <BookOpen className="w-3.5 h-3.5 text-zinc-500" />
                      <span>Parent Guide: {featuredArticle.parentGuide.title}</span>
                    </Link>
                  )}
                </div>
              </div>

              {/* Cover Artwork Showcase (order-1 on mobile, order-2 on desktop) */}
              <div className="lg:col-span-5 flex justify-center order-1 lg:order-2">
                <Link
                  href={`/blog/${featuredArticle.slug}/`}
                  className="block relative w-full max-w-[340px] sm:max-w-[420px] aspect-[16/10] rounded-2xl overflow-hidden bg-zinc-100 border border-zinc-200/80 shadow-md hover:scale-[1.02] transition-transform duration-300"
                >
                  <Image
                    src={featuredArticle.coverImage}
                    alt={featuredArticle.coverAlt || featuredArticle.title}
                    fill
                    priority
                    className="object-cover"
                    sizes="(max-width: 768px) 100vw, 420px"
                  />
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
                Blog
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
                  <span className="text-zinc-900 font-semibold uppercase">{activeFormat.replace(/_/g, ' ')}</span>
                </>
              )}
            </div>
            <div className="flex items-baseline justify-between">
              <h3 className="font-display text-lg sm:text-xl font-bold text-zinc-950">
                Explore {filteredArticles.length} Editorial {filteredArticles.length === 1 ? 'Article' : 'Articles'}
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
                  <option value="ai-automation">AI &amp; Automation</option>
                  <option value="finance">Finance &amp; GST</option>
                  <option value="agency-profitability">Agency Profitability</option>
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
                  <option value="sop">Standard Operating Procedure</option>
                  <option value="deep_dive">Deep Dive &amp; Framework</option>
                  <option value="case_study">Case Study &amp; Data</option>
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
                  <option value="quick_answer">45s Direct Answer</option>
                  <option value="deep_dive">In-Depth Guide (8+ min)</option>
                  <option value="tool">Free Tool Included</option>
                </select>
                <ChevronDown className="w-4 h-4 text-zinc-500 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
              </div>
            </div>

            {/* 4. Search Input */}
            <div>
              <label className="text-xs font-bold text-zinc-900 mb-1.5 block">
                Search all articles
              </label>
              <div className="relative">
                <Search className="w-4 h-4 text-zinc-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                <input
                  type="text"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  placeholder="Search articles &amp; systems..."
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
                Showing {filteredArticles.length} of {allArticles.length}
              </span>
            </div>
          )}
        </div>
      </section>

      {/* ── 4. ARTICLES LIBRARY GRID ─────────────────────────────────── */}
      <section className="max-w-[1240px] mx-auto px-4 sm:px-6 mt-8">
        {filteredArticles.length > 0 ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {filteredArticles.map((article, idx) => {
              const themeKey = THEME_KEYS[idx % THEME_KEYS.length];
              const theme = THEME_STYLES[themeKey] || THEME_STYLES.lavender;
              const displayDate = article.updatedAt || article.publishedAt;
              const categoryLabel = article.category.replace(/-/g, ' ');

              return (
                <Link
                  key={article.slug}
                  href={`/blog/${article.slug}/`}
                  className={`group relative flex flex-col justify-between rounded-2xl ${theme.bg} ${theme.hoverBg} border ${theme.border} ${theme.hoverBorder} shadow-2xs hover:shadow-md transition-all duration-300 overflow-hidden p-2 sm:p-2.5 cursor-pointer block`}
                >
                  <div>
                    {/* Cover Thumbnail Frame */}
                    <div className="block aspect-[16/10] w-full overflow-hidden bg-white relative rounded-xl border border-white/80 shadow-2xs mb-3">
                      {article.coverImage ? (
                        <Image
                          src={article.coverImage}
                          alt={article.coverAlt || article.title}
                          fill
                          sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw"
                          className="object-cover group-hover:scale-[1.02] transition-transform duration-500 ease-out"
                        />
                      ) : (
                        <div className="w-full h-full flex flex-col items-center justify-center bg-zinc-950 text-white p-6 text-center">
                          <BookOpen className="w-8 h-8 text-zinc-400 mb-2" />
                          <span className="font-display font-bold text-sm">{article.title}</span>
                        </div>
                      )}

                      {/* 45s Quick Answer Badge */}
                      {article.quickAnswer && (
                        <div className="absolute top-2.5 left-2.5 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white/95 backdrop-blur-xs text-[10px] font-mono font-bold text-zinc-900 border border-zinc-200/80 shadow-2xs">
                          <Sparkles className="w-2.5 h-2.5 text-amber-500 fill-amber-500" />
                          <span>45s Answer</span>
                        </div>
                      )}
                    </div>

                    {/* Metadata Strip */}
                    <div className="px-2 pt-1 flex items-center justify-between gap-2 text-[11px] font-mono text-zinc-600 mb-1.5">
                      <span className={`font-bold uppercase tracking-wider text-[10px] ${theme.accent}`}>
                        {categoryLabel}
                      </span>
                      <span className="flex items-center gap-1 text-[11px] text-zinc-500">
                        <Clock className="w-3 h-3 text-zinc-400" />
                        <span>{article.readTime}</span>
                      </span>
                    </div>

                    {/* Title */}
                    <h3 className="px-2 font-display font-bold text-base sm:text-lg text-zinc-950 group-hover:text-black leading-snug line-clamp-2">
                      {article.title}
                    </h3>

                    {/* Excerpt */}
                    <p className="px-2 mt-1.5 text-xs text-zinc-600 leading-relaxed line-clamp-2">
                      {article.excerpt || article.dek}
                    </p>
                  </div>

                  {/* Footer Action */}
                  <div className="mx-2 mt-4 pt-3 border-t border-zinc-200/60 flex items-center justify-between text-xs font-bold text-zinc-950">
                    <span className="text-[11px] font-mono text-zinc-500 font-normal">
                      {displayDate}
                    </span>
                    <div className="inline-flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                      <span>Read Article</span>
                      <ArrowRight className="w-3.5 h-3.5 text-zinc-600" />
                    </div>
                  </div>
                </Link>
              );
            })}
          </div>
        ) : (
          <div className="text-center py-16 px-4 bg-zinc-50 rounded-2xl border border-zinc-200">
            <BookOpen className="w-8 h-8 text-zinc-400 mx-auto mb-3" />
            <h3 className="font-display font-bold text-lg text-zinc-950">No matching articles found</h3>
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

      {/* ── 5. POPULAR AGENCY TOOLS & PLAYBOOKS SHELF ─────────────────── */}
      <section className="max-w-[1240px] mx-auto px-4 sm:px-6 mt-16 sm:mt-24 pt-12 border-t border-zinc-200/80">
        <div className="flex items-center justify-between mb-8">
          <div>
            <span className="text-[11px] font-mono font-bold text-zinc-500 uppercase tracking-wider block mb-1">
              Popular Operational Resources
            </span>
            <h2 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight">
              Featured Tools &amp; Operating Playbooks
            </h2>
          </div>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <Link
            href="/tools/retainer-calculator/"
            className="p-5 rounded-2xl bg-[#ECEFFE] hover:bg-[#E2E7FC] border border-[#D7DCF5] hover:border-[#C6CEEE] transition-all flex flex-col justify-between group shadow-2xs hover:shadow-xs"
          >
            <div>
              <div className="flex items-center justify-between text-[10px] font-mono text-zinc-600 mb-2">
                <span className="font-bold text-indigo-950 bg-white/90 px-2 py-0.5 rounded border border-white/80 uppercase shadow-2xs">Free Tool</span>
                <span>Margin Engine</span>
              </div>
              <h4 className="font-display font-bold text-base text-zinc-950 group-hover:text-indigo-950 transition-colors leading-snug">
                Retainer &amp; Profitability Calculator
              </h4>
              <p className="mt-1.5 text-xs text-zinc-600 line-clamp-2">
                Simulate client retainers, calculate GST, and project blended gross margins in real-time.
              </p>
            </div>
            <div className="mt-4 pt-3 border-t border-indigo-200/60 flex items-center justify-between text-xs font-bold text-zinc-950">
              <span>Open Tool</span>
              <ArrowRight className="w-3.5 h-3.5 text-zinc-500 group-hover:translate-x-0.5 transition-transform" />
            </div>
          </Link>

          <Link
            href="/tools/agency-proposal-generator/"
            className="p-5 rounded-2xl bg-[#E2F1F8] hover:bg-[#D4EAF6] border border-[#CCE3EF] hover:border-[#B5D7E8] transition-all flex flex-col justify-between group shadow-2xs hover:shadow-xs"
          >
            <div>
              <div className="flex items-center justify-between text-[10px] font-mono text-zinc-600 mb-2">
                <span className="font-bold text-sky-950 bg-white/90 px-2 py-0.5 rounded border border-white/80 uppercase shadow-2xs">Proposal Kit</span>
                <span>3-Tier Deck</span>
              </div>
              <h4 className="font-display font-bold text-base text-zinc-950 group-hover:text-sky-950 transition-colors leading-snug">
                Agency Proposal &amp; SOW Builder
              </h4>
              <p className="mt-1.5 text-xs text-zinc-600 line-clamp-2">
                Generate airtight client proposals with structured deliverables, milestones, and scope clauses.
              </p>
            </div>
            <div className="mt-4 pt-3 border-t border-sky-200/60 flex items-center justify-between text-xs font-bold text-zinc-950">
              <span>Generate SOW</span>
              <ArrowRight className="w-3.5 h-3.5 text-zinc-500 group-hover:translate-x-0.5 transition-transform" />
            </div>
          </Link>

          <Link
            href="/guides/agency-client-onboarding-playbook/"
            className="p-5 rounded-2xl bg-[#E6F3EC] hover:bg-[#D8EDE0] border border-[#CEE5D6] hover:border-[#B9DCC4] transition-all flex flex-col justify-between group shadow-2xs hover:shadow-xs"
          >
            <div>
              <div className="flex items-center justify-between text-[10px] font-mono text-zinc-600 mb-2">
                <span className="font-bold text-emerald-950 bg-white/90 px-2 py-0.5 rounded border border-white/80 uppercase shadow-2xs">Master Guide</span>
                <span>12-Page PDF</span>
              </div>
              <h4 className="font-display font-bold text-base text-zinc-950 group-hover:text-emerald-950 transition-colors leading-snug">
                Agency Client Onboarding Playbook
              </h4>
              <p className="mt-1.5 text-xs text-zinc-600 line-clamp-2">
                The full operational handbook to eliminate kickoff friction and turn new deals into retainers.
              </p>
            </div>
            <div className="mt-4 pt-3 border-t border-emerald-200/60 flex items-center justify-between text-xs font-bold text-zinc-950">
              <span>Read Playbook</span>
              <ArrowRight className="w-3.5 h-3.5 text-zinc-500 group-hover:translate-x-0.5 transition-transform" />
            </div>
          </Link>
        </div>
      </section>
    </main>
  );
}
