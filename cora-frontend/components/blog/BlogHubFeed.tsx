'use client';

import React, { useState, useMemo } from 'react';
import Link from 'next/link';
import { Search, X, Layers, TrendingUp, Sparkles } from 'lucide-react';
import type { BlogArticle, BlogCategory } from '@/lib/blog-data';
import { BlogArticleCard } from './BlogArticleCard';
import { BlogFeaturedCard } from './BlogFeaturedCard';

interface BlogHubFeedProps {
  articles: BlogArticle[];
  categories: BlogCategory[];
  featuredArticle?: BlogArticle;
}

export function BlogHubFeed({ articles, categories, featuredArticle }: BlogHubFeedProps) {
  const [selectedCategory, setSelectedCategory] = useState<string>('all');
  const [searchQuery, setSearchQuery] = useState<string>('');

  const filteredArticles = useMemo(() => {
    const q = searchQuery.trim().toLowerCase();

    return articles.filter((art) => {
      // 1. Category check
      const matchesCategory =
        selectedCategory === 'all' ||
        art.category === selectedCategory;

      // 2. Search query check
      const matchesQuery =
        !q ||
        art.title.toLowerCase().includes(q) ||
        art.dek.toLowerCase().includes(q) ||
        art.excerpt.toLowerCase().includes(q) ||
        art.tags.some((t) => t.toLowerCase().includes(q)) ||
        art.primaryKeyword?.toLowerCase().includes(q);

      return matchesCategory && matchesQuery;
    });
  }, [articles, selectedCategory, searchQuery]);

  const isFiltering = selectedCategory !== 'all' || searchQuery.trim() !== '';

  const popularArticles = useMemo(() => {
    return articles.slice(0, 4);
  }, [articles]);

  return (
    <div className="space-y-10">
      {/* ── Search & Category Filter Bar (Surface directly below hero) ── */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 p-2 sm:p-2.5 rounded-2xl bg-[#FBFaf7] border border-zinc-200/90 shadow-2xs">
        {/* Category Pills (Horizontal Scroll on Mobile) */}
        <div className="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 scrollbar-none max-w-full">
          <button
            type="button"
            onClick={() => setSelectedCategory('all')}
            className={`px-3.5 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all cursor-pointer ${
              selectedCategory === 'all'
                ? 'bg-zinc-950 text-white shadow-2xs'
                : 'bg-white text-zinc-600 hover:text-zinc-950 hover:bg-zinc-100 border border-zinc-200/70'
            }`}
          >
            All Topics
          </button>

          {categories.map((cat) => {
            const isActive = selectedCategory === cat.id;
            return (
              <button
                key={cat.id}
                type="button"
                onClick={() => setSelectedCategory(cat.id)}
                className={`px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all cursor-pointer ${
                  isActive
                    ? 'bg-zinc-950 text-white shadow-2xs'
                    : 'bg-white text-zinc-600 hover:text-zinc-950 hover:bg-zinc-100 border border-zinc-200/70'
                }`}
              >
                {cat.shortName || cat.name}
              </button>
            );
          })}
        </div>

        {/* Instant Search Input */}
        <div className="relative min-w-[240px] sm:min-w-[280px]">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-zinc-400" />
          <input
            type="text"
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            placeholder="Search articles & systems..."
            className="w-full pl-9 pr-8 py-1.5 text-xs bg-white text-zinc-950 placeholder:text-zinc-400 rounded-xl border border-zinc-200/80 focus:outline-none focus:border-zinc-400 shadow-2xs transition-colors"
          />
          {searchQuery && (
            <button
              type="button"
              onClick={() => setSearchQuery('')}
              className="absolute right-2.5 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-950 p-0.5 cursor-pointer"
            >
              <X className="w-3.5 h-3.5" />
            </button>
          )}
        </div>
      </div>

      {/* If not filtering and featured exists, show featured hero story */}
      {!isFiltering && featuredArticle && (
        <section aria-label="Featured Story">
          <BlogFeaturedCard article={featuredArticle} />
        </section>
      )}

      {/* Main Articles Grid */}
      <section aria-label="Article Grid">
        <div className="flex items-center justify-between gap-4 mb-6 border-b border-zinc-200/80 pb-3">
          <div>
            <div className="text-[10px] font-mono font-bold uppercase tracking-widest text-zinc-600">
              {isFiltering ? 'FILTERED RESULTS' : 'LATEST PLAYBOOKS & ARTICLES'}
            </div>
            <h2 className="font-display text-xl sm:text-2xl font-bold tracking-tight text-zinc-950 mt-0.5">
              {isFiltering
                ? `Showing ${filteredArticles.length} matching ${
                    filteredArticles.length === 1 ? 'article' : 'articles'
                  }`
                : 'Systems, Frameworks & Operations'}
            </h2>
          </div>
          <div className="text-xs font-mono text-zinc-600">
            {filteredArticles.length} {filteredArticles.length === 1 ? 'entry' : 'entries'}
          </div>
        </div>

        {filteredArticles.length === 0 ? (
          <div className="p-12 text-center rounded-3xl border border-zinc-200 bg-[#FBFaf7]">
            <h3 className="font-display text-base font-bold text-zinc-950">
              No matching editorial articles found.
            </h3>
            <p className="mt-1.5 text-xs text-zinc-600 max-w-sm mx-auto">
              Try adjusting your search terms or browse all topics above.
            </p>
            <button
              type="button"
              onClick={() => {
                setSelectedCategory('all');
                setSearchQuery('');
              }}
              className="mt-4 px-4 py-2 rounded-xl bg-zinc-950 text-white text-xs font-bold cursor-pointer hover:bg-black transition-colors"
            >
              Reset Filters
            </button>
          </div>
        ) : (
          <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {filteredArticles.map((art) => (
              <BlogArticleCard key={art.slug} article={art} />
            ))}
          </div>
        )}
      </section>

      {/* Popular & Trending Reading Strip (When not actively filtering) */}
      {!isFiltering && (
        <section aria-label="Popular Articles" className="my-12 pt-8 border-t border-zinc-200/80">
          <div className="flex items-center gap-2 text-[10px] font-mono font-bold uppercase tracking-widest text-zinc-600 mb-4">
            <TrendingUp className="w-3.5 h-3.5 text-zinc-900" />
            <span>POPULAR OPERATING SYSTEMS</span>
          </div>

          <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {popularArticles.map((art, idx) => (
              <Link
                key={art.slug}
                href={`/blog/${art.slug}/`}
                className="group p-4 rounded-2xl border border-zinc-200/80 bg-[#FBFaf7] hover:border-zinc-300 hover:shadow-xs transition-all flex flex-col justify-between"
              >
                <div>
                  <div className="text-[10px] font-mono text-zinc-600 mb-1">
                    0{idx + 1} &bull; {art.readTime}
                  </div>
                  <h4 className="font-display text-xs sm:text-sm font-bold text-zinc-950 group-hover:text-zinc-700 transition-colors line-clamp-2 leading-snug">
                    {art.title}
                  </h4>
                </div>
                <div className="mt-3 pt-2 border-t border-zinc-200/60 text-[11px] font-mono text-zinc-600 group-hover:text-zinc-950">
                  Read system →
                </div>
              </Link>
            ))}
          </div>
        </section>
      )}
    </div>
  );
}
