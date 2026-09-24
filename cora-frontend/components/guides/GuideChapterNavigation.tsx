'use client';

import React, { useEffect, useState } from 'react';
import { ChevronDown, BookOpen, Layers, CheckCircle2, ListFilter } from 'lucide-react';
import { GuideChapter } from '@/lib/guides-data';

interface GuideChapterNavigationProps {
  chapters: GuideChapter[];
  mode?: 'mobile' | 'desktop' | 'all';
}

export function GuideChapterNavigation({
  chapters,
  mode = 'all',
}: GuideChapterNavigationProps) {
  const [activeSlug, setActiveSlug] = useState<string>(chapters[0]?.slug || '');
  const [isOpenMobile, setIsOpenMobile] = useState(false);

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
      { rootMargin: '0px 0px -55% 0px', threshold: 0 }
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
  const activeChapter = chapters[currentIndex] || chapters[0];
  const progressPercent = Math.round(((currentIndex + 1) / chapters.length) * 100);

  return (
    <>
      {/* Mobile Sticky / Expandable Chapter Bar */}
      {(mode === 'mobile' || mode === 'all') && (
        <div className={`my-6 rounded-2xl border border-zinc-200/80 bg-white/95 backdrop-blur-md p-4 shadow-sm ${mode === 'all' ? 'lg:hidden' : ''}`}>
          <button
            onClick={() => setIsOpenMobile(!isOpenMobile)}
            className="w-full flex items-center justify-between text-xs font-bold text-zinc-900 cursor-pointer"
          >
            <div className="flex items-center gap-2">
              <span className="w-6 h-6 rounded-lg bg-zinc-950 text-white text-[11px] font-mono flex items-center justify-center font-bold">
                {activeChapter?.number || '01'}
              </span>
              <div className="text-left">
                <div className="text-[10px] font-mono text-zinc-400 uppercase tracking-wider">
                  Chapter {currentIndex + 1} of {chapters.length}
                </div>
                <div className="text-xs text-zinc-900 font-bold truncate max-w-[200px] sm:max-w-[300px]">
                  {activeChapter?.title}
                </div>
              </div>
            </div>
            <div className="flex items-center gap-2">
              <span className="text-[11px] font-mono font-semibold text-zinc-500">{progressPercent}%</span>
              <ChevronDown
                className={`w-4 h-4 text-zinc-500 transition-transform duration-200 ${isOpenMobile ? 'rotate-180' : ''}`}
              />
            </div>
          </button>

          {isOpenMobile && (
            <nav className="mt-4 pt-3 border-t border-zinc-100 space-y-1 max-h-80 overflow-y-auto">
              {chapters.map((ch, idx) => {
                const isActive = activeSlug === ch.slug;
                const isPassed = idx < currentIndex;
                return (
                  <a
                    key={ch.slug}
                    href={`#${ch.slug}`}
                    onClick={() => setIsOpenMobile(false)}
                    className={`flex items-center justify-between text-xs py-2 px-2.5 rounded-xl transition-all ${
                      isActive
                        ? 'font-bold bg-zinc-950 text-white shadow-xs'
                        : isPassed
                        ? 'text-zinc-700 hover:bg-zinc-100'
                        : 'text-zinc-500 hover:text-zinc-900 hover:bg-zinc-100'
                    }`}
                  >
                    <div className="flex items-center gap-2.5 truncate">
                      <span className={`font-mono text-[10px] ${isActive ? 'text-zinc-300' : 'text-zinc-400'}`}>
                        {ch.number}
                      </span>
                      <span className="truncate">{ch.title}</span>
                    </div>
                    {isPassed && !isActive && (
                      <CheckCircle2 className="w-3.5 h-3.5 text-zinc-400 shrink-0 ml-2" />
                    )}
                  </a>
                );
              })}
            </nav>
          )}
        </div>
      )}

      {/* Desktop Floating Glassmorphic Rail */}
      {(mode === 'desktop' || mode === 'all') && (
        <div className={`rounded-3xl border border-zinc-200/80 bg-white/90 backdrop-blur-md p-5 shadow-[0_8px_30px_rgb(0,0,0,0.03)] space-y-4 ${mode === 'all' ? 'hidden lg:block' : ''}`}>
          {/* Header with Reading Progress */}
          <div>
            <div className="flex items-center justify-between text-[11px] font-mono font-bold text-zinc-500 mb-2">
              <span className="uppercase tracking-wider flex items-center gap-1.5">
                <BookOpen className="w-3.5 h-3.5 text-zinc-700" />
                <span>Playbook Chapters</span>
              </span>
              <span className="text-zinc-700">{progressPercent}%</span>
            </div>

            {/* Continuous progress track */}
            <div className="w-full h-1.5 rounded-full bg-zinc-100 overflow-hidden">
              <div
                className="h-full bg-zinc-950 rounded-full transition-all duration-300"
                style={{ width: `${progressPercent}%` }}
              />
            </div>
          </div>

          {/* Chapter Links List */}
          <nav className="space-y-1 text-xs">
            {chapters.map((ch, idx) => {
              const isActive = activeSlug === ch.slug;
              const isPassed = idx < currentIndex;

              return (
                <a
                  key={ch.slug}
                  href={`#${ch.slug}`}
                  className={`group flex items-start gap-2.5 py-2 px-3 rounded-xl transition-all leading-snug ${
                    isActive
                      ? 'font-bold bg-zinc-950 text-white shadow-xs'
                      : isPassed
                      ? 'text-zinc-700 hover:bg-zinc-100/80'
                      : 'text-zinc-500 hover:text-zinc-900 hover:bg-zinc-100/80'
                  }`}
                >
                  <span
                    className={`font-mono text-[10px] shrink-0 mt-0.5 ${
                      isActive ? 'text-zinc-300 font-bold' : 'text-zinc-400 group-hover:text-zinc-700'
                    }`}
                  >
                    {ch.number}
                  </span>
                  <span className="line-clamp-2 flex-1">{ch.title}</span>
                </a>
              );
            })}
          </nav>
        </div>
      )}
    </>
  );
}
