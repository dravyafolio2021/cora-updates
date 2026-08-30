'use client';

import React from 'react';
import { ArticleCard } from './ArticleCard';
import { ARTICLES_DATA, type Article } from '@/lib/articles-data';

interface RelatedArticlesProps {
  currentSlug: string;
  relatedSlugs: string[];
  category: string;
}

export function RelatedArticles({ currentSlug, relatedSlugs, category }: RelatedArticlesProps) {
  let matchedArticles = ARTICLES_DATA.filter(
    (a) => relatedSlugs.includes(a.slug) && a.slug !== currentSlug
  );

  if (matchedArticles.length === 0) {
    matchedArticles = ARTICLES_DATA.filter(
      (a) => a.category === category && a.slug !== currentSlug
    ).slice(0, 3);
  }

  if (matchedArticles.length === 0) return null;

  return (
    <section className="space-y-6 pt-12 border-t border-zinc-200">
      <div className="flex items-center justify-between">
        <h3 className="font-display text-2xl font-bold tracking-tight text-zinc-950">
          Related Educational Guides &amp; Resources
        </h3>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        {matchedArticles.map((article) => (
          <ArticleCard key={article.slug} article={article} />
        ))}
      </div>
    </section>
  );
}
