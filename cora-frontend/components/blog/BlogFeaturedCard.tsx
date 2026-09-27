'use client';

import React from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { ArrowRight, Clock, Sparkles } from 'lucide-react';
import type { BlogArticle } from '@/lib/blog-data';

interface BlogFeaturedCardProps {
  article: BlogArticle;
}

export function BlogFeaturedCard({ article }: BlogFeaturedCardProps) {
  const displayDate = article.updatedAt || article.publishedAt;
  const categoryLabel = article.category.replace(/-/g, ' ');

  return (
    <article className="group rounded-3xl border border-zinc-200/90 bg-[#FBFaf7] p-6 sm:p-8 lg:p-10 shadow-xs hover:border-zinc-300 hover:shadow-md transition-all duration-300 overflow-hidden">
      <div className="grid lg:grid-cols-[1.15fr_0.85fr] gap-8 items-center">
        <div>
          <div className="flex flex-wrap items-center gap-2 text-[11px] font-mono mb-3.5">
            <span className="px-2.5 py-0.5 rounded-full bg-zinc-950 text-white font-bold uppercase tracking-wider text-[10px]">
              FEATURED EDITORIAL
            </span>
            <span className="text-zinc-600 font-bold uppercase text-[10px]">
              &bull; {categoryLabel}
            </span>
            {article.quickAnswer && (
              <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white border border-zinc-200 text-[10px] text-zinc-900 font-bold shadow-2xs">
                <Sparkles className="w-2.5 h-2.5 text-amber-500 fill-amber-500" />
                <span>45s Summary</span>
              </span>
            )}
          </div>

          <h2 className="font-display text-xl sm:text-2xl lg:text-3xl font-bold tracking-tight text-zinc-950 group-hover:text-zinc-700 transition-colors leading-[1.2]">
            <Link href={`/blog/${article.slug}/`}>
              {article.title}
            </Link>
          </h2>

          <p className="mt-3.5 text-xs sm:text-sm md:text-base text-zinc-600 leading-relaxed line-clamp-3 font-normal">
            {article.dek || article.excerpt}
          </p>

          <div className="mt-6 pt-4 border-t border-zinc-200/80 flex flex-wrap items-center justify-between gap-4">
            <div className="flex items-center gap-2.5">
              <div className="w-7 h-7 rounded-full bg-zinc-950 text-white flex items-center justify-center font-bold text-xs font-mono">
                {article.author.name.charAt(0)}
              </div>
              <div className="text-xs">
                <div className="font-bold text-zinc-900">{article.author.name}</div>
                <div className="text-zinc-600 text-[11px] flex items-center gap-1.5 font-mono">
                  <span>{article.readTime}</span>
                  <span>&bull;</span>
                  <span>{displayDate}</span>
                </div>
              </div>
            </div>

            <Link
              href={`/blog/${article.slug}/`}
              className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-zinc-950 hover:bg-black text-white text-xs font-bold transition-all shadow-2xs cursor-pointer group/btn"
            >
              <span>Read article</span>
              <ArrowRight className="w-3.5 h-3.5 group-hover/btn:translate-x-0.5 transition-transform" />
            </Link>
          </div>
        </div>

        <Link href={`/blog/${article.slug}/`} className="block relative aspect-[16/10] w-full rounded-2xl overflow-hidden border border-zinc-200/80 bg-zinc-100">
          <Image
            src={article.coverImage}
            alt={article.coverAlt || article.title}
            fill
            className="object-cover group-hover:scale-[1.03] transition-transform duration-500"
            sizes="(max-width: 1024px) 100vw, 550px"
          />
        </Link>
      </div>
    </article>
  );
}

