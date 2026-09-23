import React from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { ArrowRight, Clock, Calendar, Sparkles } from 'lucide-react';
import type { BlogArticle } from '@/lib/blog-data';

interface BlogFeaturedCardProps {
  article: BlogArticle;
}

export function BlogFeaturedCard({ article }: BlogFeaturedCardProps) {
  return (
    <article className="group my-8 rounded-3xl border border-zinc-200 bg-[#FBFaf7] p-6 sm:p-8 lg:p-10 shadow-sm hover:border-zinc-400 transition-all overflow-hidden">
      <div className="grid lg:grid-cols-[1.1fr_0.9fr] gap-8 items-center">
        <div>
          <div className="flex flex-wrap items-center gap-2.5 text-[11px] font-mono mb-4">
            <span className="px-2.5 py-0.5 rounded-full bg-zinc-950 text-white font-bold uppercase tracking-wider">
              FEATURED STORY
            </span>
            <span className="text-zinc-400">&bull;</span>
            <span className="font-bold text-zinc-600 uppercase">
              {article.qualityLabel} &bull; {article.category.replace('-', ' ')}
            </span>
          </div>

          <h2 className="font-display text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-zinc-950 group-hover:text-zinc-700 transition-colors leading-tight">
            <Link href={`/blog/${article.slug}/`}>
              {article.title}
            </Link>
          </h2>

          <p className="mt-4 text-sm sm:text-base text-zinc-600 leading-relaxed line-clamp-3 font-normal">
            {article.dek || article.excerpt}
          </p>

          <div className="mt-6 pt-5 border-t border-zinc-200/80 flex flex-wrap items-center justify-between gap-4">
            <div className="flex items-center gap-3">
              <div className="w-8 h-8 rounded-full bg-zinc-950 text-white flex items-center justify-center font-bold text-xs font-mono">
                {article.author.name.charAt(0)}
              </div>
              <div className="text-xs">
                <div className="font-bold text-zinc-900">{article.author.name}</div>
                <div className="text-zinc-600 text-[11px] flex items-center gap-1.5">
                  <span>{article.readTime}</span>
                  <span>&bull;</span>
                  <span>{article.publishedAt}</span>
                </div>
              </div>
            </div>

            <Link
              href={`/blog/${article.slug}/`}
              className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold transition-all shadow-xs"
            >
              <span>Read story</span>
              <ArrowRight className="w-3.5 h-3.5" />
            </Link>
          </div>
        </div>

        <div className="relative aspect-[16/10] w-full rounded-2xl overflow-hidden border border-zinc-200 bg-zinc-100">
          <Image
            src={article.coverImage}
            alt={article.coverAlt}
            fill
            className="object-cover group-hover:scale-105 transition-transform duration-500"
            sizes="(max-width: 1024px) 100vw, 550px"
          />
        </div>
      </div>
    </article>
  );
}
