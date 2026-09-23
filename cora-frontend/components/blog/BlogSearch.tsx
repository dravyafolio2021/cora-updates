'use client';

import React, { useState, useMemo } from 'react';
import { Search, X, Clock, ArrowRight } from 'lucide-react';
import Link from 'next/link';
import type { BlogArticle } from '@/lib/blog-data';

interface BlogSearchProps {
  articles: BlogArticle[];
}

export function BlogSearch({ articles }: BlogSearchProps) {
  const [query, setQuery] = useState('');

  const filtered = useMemo(() => {
    const q = query.trim().toLowerCase();
    if (!q) return [];

    return articles.filter((art) => {
      const titleMatch = art.title.toLowerCase().includes(q);
      const dekMatch = art.dek.toLowerCase().includes(q);
      const excerptMatch = art.excerpt.toLowerCase().includes(q);
      const tagMatch = art.tags.some((t) => t.toLowerCase().includes(q));
      const catMatch = art.category.toLowerCase().includes(q);
      return titleMatch || dekMatch || excerptMatch || tagMatch || catMatch;
    });
  }, [query, articles]);

  return (
    <div className="relative my-6">
      <div className="relative max-w-md">
        <Search className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400" />
        <input
          type="text"
          value={query}
          onChange={(e) => setQuery(e.target.value)}
          placeholder="Search operating systems, workflows & articles..."
          className="w-full pl-10 pr-9 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-[#FBFaf7] dark:bg-zinc-900 text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 outline-none focus:border-zinc-400 dark:focus:border-zinc-600 transition-all shadow-xs"
        />
        {query && (
          <button
            onClick={() => setQuery('')}
            className="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 p-0.5"
          >
            <X className="w-3.5 h-3.5" />
          </button>
        )}
      </div>

      {query.trim() && (
        <div className="absolute top-full left-0 right-0 max-w-lg mt-2 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 p-3 shadow-2xl z-30 max-h-80 overflow-y-auto">
          <div className="text-[10px] font-mono text-zinc-600 dark:text-zinc-400 uppercase font-semibold px-2 py-1">
            {filtered.length} {filtered.length === 1 ? 'RESULT' : 'RESULTS'} FOUND
          </div>
          {filtered.length === 0 ? (
            <div className="p-4 text-center text-xs text-zinc-500">
              No matching articles found for &ldquo;{query}&rdquo;.
            </div>
          ) : (
            <div className="divide-y divide-zinc-100 dark:divide-zinc-900">
              {filtered.map((art) => (
                <Link
                  key={art.slug}
                  href={`/blog/${art.slug}/`}
                  onClick={() => setQuery('')}
                  className="p-2.5 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-900 block transition-colors"
                >
                  <div className="flex items-center justify-between text-[10px] font-mono text-zinc-600 dark:text-zinc-400 mb-1">
                    <span>{art.qualityLabel}</span>
                    <span>{art.readTime}</span>
                  </div>
                  <div className="font-bold text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 line-clamp-1">
                    {art.title}
                  </div>
                  <p className="text-[11px] text-zinc-500 line-clamp-1 mt-0.5">
                    {art.excerpt}
                  </p>
                </Link>
              ))}
            </div>
          )}
        </div>
      )}
    </div>
  );
}
