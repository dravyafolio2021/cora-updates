import React from 'react';
import type { Metadata } from 'next';
import Link from 'next/link';
import { Search, Compass, Layers, Receipt, Bot, ShieldCheck, BarChart2, Sparkles, ArrowRight } from 'lucide-react';
import { ARTICLES_DATA, ARTICLE_CATEGORIES, getFeaturedArticles } from '@/lib/articles-data';
import { ArticleCard } from '@/components/articles/ArticleCard';
import { ArticlesClientHub } from './ArticlesClientHub';

export const metadata: Metadata = {
  title: 'Articles & Compare — Cora Knowledge Hub',
  description: 'Educational how-to guides, Indian GST playbooks, operational benchmarks, and side-by-side competitor comparisons.',
  openGraph: {
    title: 'Articles & Compare — Cora Knowledge Hub',
    description: 'Educational how-to guides, Indian GST playbooks, operational benchmarks, and side-by-side competitor comparisons.',
    url: 'https://heycora.in/articles',
    type: 'website',
    siteName: 'Cora',
  },
  alternates: {
    canonical: 'https://heycora.in/articles',
  }
};

export default function ArticlesPage() {
  const featuredArticles = getFeaturedArticles();
  const mainFeatured = featuredArticles[0] || ARTICLES_DATA[0];

  return (
    <div className="min-h-screen bg-[#FBFaf7] text-zinc-900 selection:bg-zinc-950 selection:text-white pt-24 pb-20">

      {/* ── Top Header Hero ── */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-12">
        <div className="max-w-3xl space-y-4">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-zinc-100 border border-zinc-200/80 text-xs font-mono font-semibold text-zinc-800">
            <Sparkles className="w-3.5 h-3.5 text-zinc-600" />
            <span>KNOWLEDGE HUB &amp; COMPARISONS</span>
          </div>

          <h1 className="font-display text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight text-zinc-950 leading-[1.1]">
            Articles, Guides &amp; Benchmarks
          </h1>

          <p className="text-base sm:text-lg text-zinc-600 font-normal leading-relaxed">
            In-depth guides, operational playbooks, 18% GST tax workflows, and side-by-side software benchmarks engineered for high-growth creative studios and media agencies.
          </p>
        </div>

        {/* ── Category Quick-Nav Tickers ── */}
        <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-3 pt-10">
          {ARTICLE_CATEGORIES.map((cat) => (
            <Link
              key={cat.id}
              href={`/articles/${cat.id}`}
              className="p-3.5 rounded-2xl bg-white border border-zinc-200/80 hover:border-zinc-400 hover:shadow-xs transition-all flex flex-col justify-between group"
            >
              <div className="space-y-1">
                <span className="text-[10px] font-mono font-bold text-zinc-600 uppercase tracking-wider block">
                  {cat.badge}
                </span>
                <span className="text-xs font-bold text-zinc-900 group-hover:text-black block line-clamp-1">
                  {cat.label}
                </span>
              </div>
              <div className="pt-2 flex items-center justify-between text-[11px] text-zinc-500 font-mono">
                <span>View</span>
                <ArrowRight className="w-3 h-3 text-zinc-400 group-hover:translate-x-0.5 group-hover:text-black transition-transform" />
              </div>
            </Link>
          ))}
        </div>
      </section>

      {/* ── Featured Spotlight & Design-Reference Card ── */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-14">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">

          {/* Main Featured Article (8 Cols) */}
          <div className="lg:col-span-8">
            <ArticleCard article={mainFeatured} featured={true} />
          </div>

          {/* Design Reference Highlight Card: Articles & Compare (4 Cols) */}
          <div className="lg:col-span-4">
            <Link
              href="/compare"
              className="h-full group relative flex flex-col justify-between p-7 rounded-3xl bg-emerald-50 text-zinc-950 border border-emerald-200/80 hover:border-emerald-300 transition-all shadow-[0px_10px_35px_rgba(16,185,129,0.06)] overflow-hidden"
            >
              <div className="space-y-3">
                <div className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-600/10 text-[10px] font-mono font-bold text-emerald-800 border border-emerald-600/20">
                  BENCHMARKS
                </div>

                <h3 className="font-display text-2xl sm:text-3xl font-extrabold tracking-tight text-zinc-950 leading-snug">
                  Articles &amp; Compare
                </h3>

                <p className="text-sm text-zinc-700 font-normal leading-relaxed">
                  Side-by-side benchmarks vs. legacy market stacks (HoneyBook, Studio Ninja, Dubsado, HubSpot, Pixieset).
                </p>
              </div>

              <div className="pt-6 mt-4 border-t border-emerald-200/60 flex items-center justify-between">
                <span className="text-xs font-bold text-emerald-950 group-hover:underline underline-offset-4">
                  Browse all 8 comparisons →
                </span>
                <div className="w-8 h-8 rounded-full bg-emerald-950 text-white flex items-center justify-center group-hover:scale-105 transition-transform shadow-2xs">
                  <ArrowRight className="w-4 h-4" />
                </div>
              </div>
            </Link>
          </div>

        </div>
      </section>

      {/* ── Client Interactive Hub (Search & Dynamic Category Filtering) ── */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <ArticlesClientHub initialArticles={ARTICLES_DATA} />
      </section>

    </div>
  );
}
