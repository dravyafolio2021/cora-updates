'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { Clock, Share2, Check, ArrowLeft, Bookmark } from 'lucide-react';
import type { Article } from '@/lib/articles-data';

interface ArticleHeroProps {
  article: Article;
}

export function ArticleHero({ article }: ArticleHeroProps) {
  const [copied, setCopied] = useState(false);

  const handleShare = () => {
    if (typeof window !== 'undefined') {
      navigator.clipboard.writeText(window.location.href);
      setCopied(true);
      const customWin = window as unknown as { coraShowToast?: (msg: string) => void };
      if (customWin.coraShowToast) {
        customWin.coraShowToast('Article link copied to clipboard!');
      }
      setTimeout(() => setCopied(false), 2000);
    }
  };

  return (
    <header className="space-y-6 pb-8 border-b border-zinc-200">
      {/* Top Navigation & Breadcrumbs */}
      <div className="flex flex-wrap items-center justify-between gap-4">
        <Link
          href="/articles"
          className="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-500 hover:text-zinc-950 transition-colors group"
        >
          <ArrowLeft className="w-3.5 h-3.5 group-hover:-translate-x-0.5 transition-transform" />
          <span>All Articles &amp; Resources</span>
        </Link>

        <div className="flex items-center gap-2">
          <Link
            href={`/articles/${article.category}`}
            className="px-2.5 py-1 rounded-md bg-zinc-100 text-[11px] font-mono font-bold text-zinc-700 hover:bg-zinc-200 transition-colors uppercase tracking-wider"
          >
            {article.categoryLabel}
          </Link>
          <button
            onClick={handleShare}
            className="inline-flex items-center gap-1.5 px-3 py-1 rounded-md border border-zinc-200 bg-white hover:bg-zinc-50 text-xs font-medium text-zinc-700 transition-all shadow-2xs"
            title="Share article"
          >
            {copied ? <Check className="w-3.5 h-3.5 text-emerald-600" /> : <Share2 className="w-3.5 h-3.5 text-zinc-500" />}
            <span>{copied ? 'Copied' : 'Share'}</span>
          </button>
        </div>
      </div>

      {/* Main Titles */}
      <div className="space-y-3">
        <div className="flex items-center gap-2">
          <span className="text-xs font-mono font-bold text-zinc-500 uppercase tracking-widest">
            {article.eyebrow}
          </span>
          <span className="text-zinc-300">•</span>
          <span className="text-xs font-mono text-zinc-500">
            {article.difficulty} Level
          </span>
        </div>

        <h1 className="font-display text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-zinc-950 leading-[1.15]">
          {article.title}
        </h1>

        <p className="text-base sm:text-lg text-zinc-600 leading-relaxed font-normal max-w-3xl">
          {article.description}
        </p>
      </div>

      {/* Author & Timestamp Bar */}
      <div className="flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-zinc-100 text-xs text-zinc-500">
        <div className="flex items-center gap-3">
          <div className="w-8 h-8 rounded-full bg-zinc-900 text-white font-bold flex items-center justify-center text-xs">
            C
          </div>
          <div>
            <div className="font-semibold text-zinc-900">{article.author.name}</div>
            <div className="text-[11px] text-zinc-500">{article.author.role}</div>
          </div>
        </div>

        <div className="flex items-center gap-4 text-xs font-mono">
          <div className="flex items-center gap-1.5">
            <Clock className="w-3.5 h-3.5 text-zinc-400" />
            <span>{article.readTime}</span>
          </div>
          <span>•</span>
          <span>Published {article.publishedAt}</span>
        </div>
      </div>
    </header>
  );
}
