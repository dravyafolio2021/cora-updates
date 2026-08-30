'use client';

import React from 'react';
import Link from 'next/link';
import { ArrowRight, Clock, Sparkles, Compass, Layers, Receipt, Bot, ShieldCheck, BarChart2 } from 'lucide-react';
import type { Article } from '@/lib/articles-data';

interface ArticleCardProps {
  article: Article;
  featured?: boolean;
}

const CategoryIconMap: Record<string, React.ReactNode> = {
  'product-guides': <Compass className="w-3.5 h-3.5" />,
  'studio-operations': <Layers className="w-3.5 h-3.5" />,
  'finance-gst': <Receipt className="w-3.5 h-3.5" />,
  'ai-automation': <Bot className="w-3.5 h-3.5" />,
  'legal-contracts': <ShieldCheck className="w-3.5 h-3.5" />,
  'comparisons': <BarChart2 className="w-3.5 h-3.5" />,
};

export function ArticleCard({ article, featured = false }: ArticleCardProps) {
  if (featured) {
    return (
      <Link
        href={`/articles/${article.category}/${article.slug}`}
        className="group relative block p-7 sm:p-9 rounded-3xl bg-zinc-950 text-white border border-zinc-800 hover:border-zinc-700 transition-all shadow-[0px_10px_35px_rgba(0,0,0,0.25)] overflow-hidden"
      >
        {/* Subtle Background Glow */}
        <div className="absolute top-0 right-0 -mr-24 -mt-24 w-80 h-80 rounded-full bg-zinc-800/40 blur-3xl pointer-events-none group-hover:bg-zinc-700/40 transition-all" />

        <div className="relative z-10 flex flex-col justify-between h-full space-y-6">
          <div className="space-y-4">
            <div className="flex flex-wrap items-center gap-2.5">
              <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-[11px] font-mono font-semibold text-zinc-300 border border-white/10">
                {CategoryIconMap[article.category] || <Sparkles className="w-3.5 h-3.5" />}
                <span>{article.categoryLabel}</span>
              </span>
              <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-[10px] font-mono font-bold text-emerald-400 border border-emerald-500/20">
                FEATURED SPOTLIGHT
              </span>
              <span className="inline-flex items-center gap-1 text-[11px] font-mono text-zinc-400">
                <Clock className="w-3 h-3" />
                <span>{article.readTime}</span>
              </span>
            </div>

            <h3 className="font-display text-2xl sm:text-3xl font-bold tracking-tight text-white group-hover:text-zinc-100 leading-snug">
              {article.title}
            </h3>

            <p className="text-sm text-zinc-400 line-clamp-3 leading-relaxed font-normal">
              {article.description}
            </p>
          </div>

          <div className="pt-2 border-t border-zinc-800/80 flex items-center justify-between">
            <div className="flex items-center gap-2 text-xs text-zinc-400">
              <span className="font-medium text-zinc-300">{article.author.name}</span>
              <span>•</span>
              <span className="text-[11px] font-mono text-zinc-500">{article.publishedAt}</span>
            </div>
            <span className="inline-flex items-center gap-1.5 text-xs font-semibold text-white group-hover:translate-x-1 transition-transform">
              <span>Read Full Guide</span>
              <ArrowRight className="w-4 h-4 text-zinc-400 group-hover:text-white" />
            </span>
          </div>
        </div>
      </Link>
    );
  }

  return (
    <Link
      href={`/articles/${article.category}/${article.slug}`}
      className="group relative flex flex-col justify-between p-6 rounded-2xl bg-white border border-zinc-200/90 hover:border-zinc-400/80 transition-all hover:shadow-[0px_8px_30px_rgba(0,0,0,0.06)] hover:-translate-y-0.5"
    >
      <div className="space-y-3.5">
        <div className="flex items-center justify-between gap-2">
          <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-zinc-100 text-[10px] font-mono font-bold text-zinc-700 uppercase tracking-wider">
            {CategoryIconMap[article.category] || <Sparkles className="w-3 h-3" />}
            <span>{article.categoryLabel}</span>
          </span>
          <span className="flex items-center gap-1 text-[11px] font-mono text-zinc-400">
            <Clock className="w-3 h-3" />
            <span>{article.readTime}</span>
          </span>
        </div>

        <h4 className="font-display text-lg font-bold text-zinc-950 group-hover:text-black leading-snug tracking-tight">
          {article.title}
        </h4>

        <p className="text-xs text-zinc-600 line-clamp-2 leading-relaxed font-normal">
          {article.description}
        </p>
      </div>

      <div className="pt-4 mt-4 border-t border-zinc-100 flex items-center justify-between">
        <span className="text-[11px] font-mono text-zinc-400">
          {article.difficulty}
        </span>
        <span className="inline-flex items-center gap-1 text-xs font-semibold text-zinc-900 group-hover:text-black group-hover:translate-x-0.5 transition-all">
          <span>Read guide</span>
          <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:text-black" />
        </span>
      </div>
    </Link>
  );
}
