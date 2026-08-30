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
    <nav className="sticky top-28 space-y-3 border-t-2 border-zinc-950 py-5">
      <div className="flex items-center gap-2 pb-2.5 border-b border-zinc-300 text-[10px] font-mono font-bold text-zinc-900 uppercase tracking-[0.18em]">
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
                className={`block border-l-2 py-1.5 pl-3 transition-all line-clamp-1 ${
                  isActive
                    ? 'border-[#B6422B] text-zinc-950 font-semibold'
                    : 'border-transparent text-zinc-600 hover:border-zinc-400 hover:text-zinc-950'
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
