import React from 'react';
import { BookOpen, ExternalLink } from 'lucide-react';
import type { ArticleSource } from '@/lib/blog-data';

interface BlogSourcesProps {
  sources?: ArticleSource[];
}

export function BlogSources({ sources }: BlogSourcesProps) {
  if (!sources || sources.length === 0) return null;

  return (
    <section className="my-10 rounded-2xl border border-zinc-200 bg-zinc-50 p-5 sm:p-6">
      <div className="flex items-center gap-2 text-[10px] font-mono font-bold uppercase tracking-widest text-zinc-600 mb-3">
        <BookOpen className="w-3.5 h-3.5" />
        <span>SOURCES & RESEARCH REFERENCES</span>
      </div>

      <ol className="space-y-2 text-xs divide-y divide-zinc-200/60">
        {sources.map((s, idx) => (
          <li key={idx} className="pt-2 first:pt-0 flex items-start justify-between gap-3">
            <div>
              <span className="font-semibold text-zinc-900">{s.title}</span>
              <div className="text-[11px] text-zinc-600 mt-0.5">
                {s.publisher} {s.publishDate && <span>&bull; Published {s.publishDate}</span>}
              </div>
            </div>
            <a
              href={s.url}
              target="_blank"
              rel="noopener noreferrer"
              className="inline-flex items-center gap-1 text-[11px] font-mono text-zinc-600 hover:text-zinc-950 shrink-0 font-medium"
            >
              <span>View</span>
              <ExternalLink className="w-3 h-3" />
            </a>
          </li>
        ))}
      </ol>
    </section>
  );
}
