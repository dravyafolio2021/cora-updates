import React from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { ArrowRight, Clock, Calendar } from 'lucide-react';
import type { BlogArticle } from '@/lib/blog-data';

interface BlogArticleCardProps {
  article: BlogArticle;
}

export function BlogArticleCard({ article }: BlogArticleCardProps) {
  return (
    <article className="group rounded-3xl border border-zinc-200 dark:border-zinc-800 bg-[#FBFaf7] dark:bg-zinc-900/60 p-5 sm:p-6 shadow-sm hover:border-zinc-400 dark:hover:border-zinc-600 transition-all flex flex-col justify-between overflow-hidden">
      <div>
        <div className="relative aspect-[16/9] w-full rounded-2xl overflow-hidden border border-zinc-200/80 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-950 mb-4">
          <Image
            src={article.coverImage}
            alt={article.coverAlt}
            fill
            className="object-cover group-hover:scale-105 transition-transform duration-500"
            sizes="(max-width: 768px) 100vw, 400px"
          />
        </div>

        <div className="flex items-center justify-between gap-2 text-[10px] font-mono mb-2">
          <span className="font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">
            {article.qualityLabel} &bull; {article.category.replace('-', ' ')}
          </span>
          <span className="text-zinc-500 dark:text-zinc-400 flex items-center gap-1">
            <Clock className="w-3 h-3" />
            <span>{article.readTime}</span>
          </span>
        </div>

        <h3 className="font-display text-lg font-bold tracking-tight text-zinc-950 dark:text-white group-hover:text-zinc-700 dark:group-hover:text-zinc-300 transition-colors leading-snug">
          <Link href={`/blog/${article.slug}/`}>
            {article.title}
          </Link>
        </h3>

        <p className="mt-2 text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 line-clamp-2 leading-relaxed">
          {article.excerpt}
        </p>
      </div>

      <div className="mt-5 pt-4 border-t border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between text-xs">
        <div className="text-[11px] font-mono text-zinc-600 dark:text-zinc-400 font-medium">
          {article.publishedAt}
        </div>

        <Link
          href={`/blog/${article.slug}/`}
          className="inline-flex items-center gap-1 font-bold text-zinc-950 dark:text-white group-hover:translate-x-0.5 transition-transform"
        >
          <span>Read</span>
          <ArrowRight className="w-3.5 h-3.5" />
        </Link>
      </div>
    </article>
  );
}
