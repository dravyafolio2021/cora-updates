import React from 'react';
import Link from 'next/link';
import { ArrowRight, Clock } from 'lucide-react';
import type { BlogArticle } from '@/lib/blog-data';

interface BlogRelatedPostsProps {
  articles: BlogArticle[];
}

export function BlogRelatedPosts({ articles }: BlogRelatedPostsProps) {
  if (!articles || articles.length === 0) return null;

  return (
    <section className="my-14 pt-10 border-t border-zinc-200">
      <div className="flex items-center justify-between mb-6">
        <div>
          <div className="text-[10px] font-mono font-bold uppercase tracking-widest text-zinc-600">
            CONTINUE EXPLORING
          </div>
          <h3 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 mt-1">
            Related Operating Systems & Guides
          </h3>
        </div>
        <Link
          href="/blog"
          className="hidden sm:inline-flex items-center gap-1.5 text-xs font-bold text-zinc-900 hover:text-zinc-600"
        >
          <span>All Articles</span>
          <ArrowRight className="w-3.5 h-3.5" />
        </Link>
      </div>

      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        {articles.map((art) => (
          <Link
            key={art.slug}
            href={`/blog/${art.slug}/`}
            className="group block rounded-2xl border border-zinc-200 bg-[#FBFaf7] p-5 hover:border-zinc-400 transition-all shadow-sm flex flex-col justify-between"
          >
            <div>
              <div className="flex items-center justify-between gap-2 text-[10px] font-mono mb-2.5">
                <span className="font-bold text-zinc-500 uppercase tracking-wider">
                  {art.qualityLabel}
                </span>
                <span className="flex items-center gap-1 text-zinc-400">
                  <Clock className="w-3 h-3" />
                  <span>{art.readTime}</span>
                </span>
              </div>
              <h4 className="font-display text-base font-bold text-zinc-950 group-hover:text-zinc-700 transition-colors line-clamp-2">
                {art.title}
              </h4>
              <p className="mt-2 text-xs text-zinc-600 line-clamp-2 leading-relaxed">
                {art.excerpt}
              </p>
            </div>

            <div className="mt-4 pt-3 border-t border-zinc-200/80 flex items-center justify-between text-xs font-bold text-zinc-900">
              <span>Read article</span>
              <ArrowRight className="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" />
            </div>
          </Link>
        ))}
      </div>
    </section>
  );
}
