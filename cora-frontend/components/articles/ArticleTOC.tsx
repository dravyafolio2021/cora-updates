'use client';

import React, { useEffect, useState } from 'react';
import { ListOrdered } from 'lucide-react';

interface ArticleTOCProps {
  toc: { id: string; title: string }[];
}

export function ArticleTOC({ toc }: ArticleTOCProps) {
  const [activeId, setActiveId] = useState<string>(toc[0]?.id || '');

  useEffect(() => {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            setActiveId(entry.target.id);
          }
        });
      },
      { rootMargin: '-80px 0px -60% 0px' }
    );

    toc.forEach((item) => {
      const el = document.getElementById(item.id);
      if (el) observer.observe(el);
    });

    return () => observer.disconnect();
  }, [toc]);

  if (!toc || toc.length === 0) return null;

  return (
    <nav className="sticky top-28 space-y-3 p-5 rounded-2xl bg-zinc-50/80 border border-zinc-200/80 backdrop-blur-xs">
      <div className="flex items-center gap-2 pb-2.5 border-b border-zinc-200/60 text-xs font-mono font-bold text-zinc-900 uppercase tracking-wider">
        <ListOrdered className="w-3.5 h-3.5 text-zinc-500" />
        <span>Table of Contents</span>
      </div>

      <ul className="space-y-1.5 text-xs">
        {toc.map((item) => {
          const isActive = activeId === item.id;
          return (
            <li key={item.id}>
              <a
                href={`#${item.id}`}
                onClick={(e) => {
                  e.preventDefault();
                  const target = document.getElementById(item.id);
                  if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                  }
                }}
                className={`block py-1 px-2.5 rounded-lg transition-all line-clamp-1 ${
                  isActive
                    ? 'bg-zinc-950 text-white font-semibold shadow-2xs'
                    : 'text-zinc-600 hover:text-zinc-950 hover:bg-zinc-100'
                }`}
              >
                {item.title}
              </a>
            </li>
          );
        })}
      </ul>
    </nav>
  );
}
