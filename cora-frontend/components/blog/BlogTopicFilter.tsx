import React from 'react';
import Link from 'next/link';
import { BLOG_CATEGORIES } from '@/lib/blog-data';

interface BlogTopicFilterProps {
  activeCategory?: string;
}

export function BlogTopicFilter({ activeCategory }: BlogTopicFilterProps) {
  return (
    <div className="my-8">
      <div className="flex items-center justify-between gap-4 mb-3">
        <div className="text-[11px] font-mono font-bold uppercase tracking-widest text-zinc-400">
          EXPLORE BY TOPIC
        </div>
      </div>

      <div className="flex flex-wrap items-center gap-2">
        <Link
          href="/blog"
          className={`px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all ${
            !activeCategory
              ? 'bg-zinc-950 text-white shadow-xs'
              : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200'
          }`}
        >
          All Topics
        </Link>

        {BLOG_CATEGORIES.map((cat) => {
          const isActive = activeCategory === cat.id || activeCategory === cat.slug;
          return (
            <Link
              key={cat.id}
              href={`/blog/${cat.slug}/`}
              className={`px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all ${
                isActive
                  ? 'bg-zinc-950 text-white shadow-xs'
                  : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200'
              }`}
            >
              {cat.name}
            </Link>
          );
        })}
      </div>
    </div>
  );
}
