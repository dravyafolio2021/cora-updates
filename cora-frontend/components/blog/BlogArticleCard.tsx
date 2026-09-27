'use client';

import React from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { ArrowRight, Clock, Sparkles } from 'lucide-react';
import type { BlogArticle } from '@/lib/blog-data';

interface BlogArticleCardProps {
  article: BlogArticle;
}

export function BlogArticleCard({ article }: BlogArticleCardProps) {
  const displayDate = article.updatedAt || article.publishedAt;
  const categoryLabel = article.category.replace(/-/g, ' ');

  return (
    <article className="group rounded-3xl border border-zinc-200/90 bg-[#FBFaf7] p-5 sm:p-6 shadow-xs hover:border-zinc-300 hover:shadow-md transition-all duration-300 flex flex-col justify-between overflow-hidden">
      <div>
        {/* Cover Image */}
        <Link href={`/blog/${article.slug}/`} className="block relative aspect-[16/9] w-full rounded-2xl overflow-hidden border border-zinc-200/80 bg-zinc-100 mb-4">
          <Image
            src={article.coverImage}
            alt={article.coverAlt || article.title}
            fill
            className="object-cover group-hover:scale-[1.03] transition-transform duration-500"
            sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 380px"
          />
          {article.quickAnswer && (
            <div className="absolute top-2.5 left-2.5 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white/95 backdrop-blur-xs text-[10px] font-mono font-bold text-zinc-900 border border-zinc-200/80 shadow-2xs">
              <Sparkles className="w-2.5 h-2.5 text-amber-500 fill-amber-500" />
              <span>45s Answer</span>
            </div>
          )}
        </Link>

        {/* Category & Read Time */}
        <div className="flex items-center justify-between gap-2 text-[11px] font-mono mb-2.5">
          <span className="font-bold text-zinc-600 uppercase tracking-wider text-[10px]">
            {categoryLabel}
          </span>
          <span className="text-zinc-500 flex items-center gap-1 text-[11px]">
            <Clock className="w-3 h-3 text-zinc-400" />
            <span>{article.readTime}</span>
          </span>
        </div>

        {/* Title */}
        <h3 className="font-display text-base sm:text-lg font-bold tracking-tight text-zinc-950 group-hover:text-zinc-700 transition-colors leading-snug">
          <Link href={`/blog/${article.slug}/`}>
            {article.title}
          </Link>
        </h3>

        {/* Excerpt */}
        <p className="mt-2 text-xs sm:text-sm text-zinc-600 line-clamp-2 leading-relaxed font-normal">
          {article.excerpt || article.dek}
        </p>
      </div>

      {/* Footer Meta & Action */}
      <div className="mt-5 pt-3.5 border-t border-zinc-200/80 flex items-center justify-between text-xs">
        <span className="text-[11px] font-mono text-zinc-500">
          {displayDate}
        </span>

        <Link
          href={`/blog/${article.slug}/`}
          className="inline-flex items-center gap-1 font-bold text-zinc-950 group-hover:translate-x-0.5 transition-transform"
        >
          <span>Read</span>
          <ArrowRight className="w-3.5 h-3.5 text-zinc-900" />
        </Link>
      </div>
    </article>
  );
}

