'use client';

import React from 'react';
import Link from 'next/link';
import { ArrowRight, BookOpen, FileText, Layers, Clock } from 'lucide-react';
import { Guide, getGuideBySlug, getAllGuides } from '@/lib/guides-data';
import { getArticleBySlug, getAllBlogArticles } from '@/lib/blog-data';
import { GuideCard } from './GuideCard';
import { trackEvent } from '@/components/analytics/Analytics';

interface GuideRelatedContentProps {
  currentGuide: Guide;
}

export function GuideRelatedContent({ currentGuide }: GuideRelatedContentProps) {
  // 1. Resolve related guides (up to 2)
  let relatedGuides: Guide[] = [];
  if (currentGuide.relatedGuides && currentGuide.relatedGuides.length > 0) {
    relatedGuides = currentGuide.relatedGuides
      .map((slug) => getGuideBySlug(slug))
      .filter((g): g is Guide => g !== undefined && g.slug !== currentGuide.slug);
  }
  if (relatedGuides.length === 0) {
    relatedGuides = getAllGuides()
      .filter((g) => g.slug !== currentGuide.slug)
      .slice(0, 2);
  }

  // 2. Resolve related articles
  let relatedArticles: any[] = [];
  if (currentGuide.relatedArticles && currentGuide.relatedArticles.length > 0) {
    relatedArticles = currentGuide.relatedArticles
      .map((slug) => getArticleBySlug(slug))
      .filter((a) => a !== undefined);
  }
  if (relatedArticles.length === 0) {
    relatedArticles = getAllBlogArticles().slice(0, 2);
  }

  return (
    <section className="mt-16 sm:mt-24 pt-12 border-t border-zinc-200/80">
      <div className="flex items-center justify-between mb-8">
        <div>
          <span className="text-[11px] font-mono font-bold text-zinc-500 uppercase tracking-wider block mb-1">
            Resource Library
          </span>
          <h2 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight">
            Continue Learning
          </h2>
        </div>
        <Link
          href="/guides/"
          className="hidden sm:inline-flex items-center gap-1.5 text-xs font-bold text-zinc-950 hover:text-zinc-600 transition-colors"
        >
          <span>Explore All Guides</span>
          <ArrowRight className="w-3.5 h-3.5" />
        </Link>
      </div>

      {/* Related Guides Grid */}
      {relatedGuides.length > 0 && (
        <div className="mb-10">
          <span className="text-xs font-bold text-zinc-950 uppercase tracking-wider block mb-4 flex items-center gap-1.5">
            <BookOpen className="w-3.5 h-3.5" />
            <span>Recommended Playbooks</span>
          </span>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {relatedGuides.map((guide, idx) => (
              <GuideCard key={guide.slug} guide={guide} index={idx} />
            ))}
          </div>
        </div>
      )}

      {/* Related Operating Articles */}
      {relatedArticles.length > 0 && (
        <div>
          <span className="text-xs font-bold text-zinc-950 uppercase tracking-wider block mb-4 flex items-center gap-1.5">
            <FileText className="w-3.5 h-3.5" />
            <span>Related In-Depth Articles</span>
          </span>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {relatedArticles.map((article) => (
              <Link
                key={article.slug}
                href={`/blog/${article.slug}/`}
                onClick={() =>
                  trackEvent('related_article_click', {
                    guide_slug: currentGuide.slug,
                    article_slug: article.slug,
                  })
                }
                className="group p-5 rounded-2xl bg-zinc-50 hover:bg-zinc-100/90 border border-zinc-200/90 transition-all flex flex-col justify-between"
              >
                <div>
                  <div className="flex items-center justify-between text-[11px] font-mono text-zinc-500 mb-2">
                    <span className="font-semibold uppercase text-zinc-700">{article.category}</span>
                    <span>{article.readTime}</span>
                  </div>
                  <h4 className="font-display font-bold text-sm sm:text-base text-zinc-950 group-hover:text-zinc-700 transition-colors line-clamp-2 leading-snug">
                    {article.title}
                  </h4>
                  <p className="mt-1.5 text-xs text-zinc-600 line-clamp-2 leading-relaxed">
                    {article.dek || article.excerpt}
                  </p>
                </div>
                <div className="mt-4 pt-3 border-t border-zinc-200/60 flex items-center justify-between text-xs font-bold text-zinc-950">
                  <span>Read Article</span>
                  <ArrowRight className="w-3 h-3 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
                </div>
              </Link>
            ))}
          </div>
        </div>
      )}

      <div className="mt-8 text-center sm:hidden">
        <Link
          href="/guides/"
          className="inline-flex items-center gap-1.5 text-xs font-bold text-zinc-950 py-2.5 px-4 rounded-xl bg-zinc-100 hover:bg-zinc-200 transition-colors"
        >
          <span>Explore All Guides &amp; Playbooks</span>
          <ArrowRight className="w-3.5 h-3.5" />
        </Link>
      </div>
    </section>
  );
}
