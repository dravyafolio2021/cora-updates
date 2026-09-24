'use client';

import React, { useEffect, useState } from 'react';
import { BookOpen, CheckCircle2 } from 'lucide-react';
import { GuideChapter } from '@/lib/guides-data';

interface GuideChapterNavigationProps {
  chapters: GuideChapter[];
  onSelectChapter?: (slug: string) => void;
}

export function GuideChapterNavigation({ chapters, onSelectChapter }: GuideChapterNavigationProps) {
  const [activeSlug, setActiveSlug] = useState<string>(chapters[0]?.slug || '');

  useEffect(() => {
    if (!chapters.length) return;

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            setActiveSlug(entry.target.id);
          }
        });
      },
      { rootMargin: '0px 0px -60% 0px', threshold: 0 }
    );

    chapters.forEach((ch) => {
      const el = document.getElementById(ch.slug);
      if (el) observer.observe(el);
    });

    return () => observer.disconnect();
  }, [chapters]);

  if (!chapters || chapters.length === 0) return null;

  const activeIndex = chapters.findIndex((c) => c.slug === activeSlug);
  const currentIndex = activeIndex >= 0 ? activeIndex : 0;
  const progressPercent = Math.round(((currentIndex + 1) / chapters.length) * 100);

  return (
    <nav className="space-y-3" aria-label="Table of contents">
      {/* Reading Progress Header */}
      <div className="flex items-center justify-between text-[11px] font-mono text-zinc-500 pb-2 border-b border-zinc-100">
        <span className="font-bold uppercase tracking-wider text-zinc-600 flex items-center gap-1.5">
          <BookOpen className="w-3.5 h-3.5 text-zinc-800" />
          <span>Chapters</span>
        </span>
        <span className="font-bold text-zinc-800">{progressPercent}%</span>
      </div>

      {/* Chapter List */}
      <div className="space-y-0.5">
        {chapters.map((ch, idx) => {
          const isActive = activeSlug === ch.slug;
          const isPassed = idx < currentIndex;

          return (
            <a
              key={ch.slug}
              href={`#${ch.slug}`}
              onClick={() => {
                if (onSelectChapter) {
                  onSelectChapter(ch.slug);
                }
              }}
              className={`group flex items-start gap-2.5 py-1.5 px-2 rounded-lg text-xs leading-snug transition-all ${
                isActive
                  ? 'font-bold text-zinc-950 bg-zinc-100/90'
                  : isPassed
                  ? 'text-zinc-600 hover:text-zinc-950 hover:bg-zinc-50'
                  : 'text-zinc-400 hover:text-zinc-900 hover:bg-zinc-50'
              }`}
            >
              <span
                className={`font-mono text-[10px] shrink-0 mt-0.5 ${
                  isActive ? 'text-zinc-950 font-bold' : 'text-zinc-400 group-hover:text-zinc-600'
                }`}
              >
                {ch.number}
              </span>
              <span className="line-clamp-2 flex-1">{ch.title}</span>
              {isPassed && !isActive && (
                <CheckCircle2 className="w-3 h-3 text-zinc-300 shrink-0 mt-0.5" />
              )}
            </a>
          );
        })}
      </div>
    </nav>
  );
}
