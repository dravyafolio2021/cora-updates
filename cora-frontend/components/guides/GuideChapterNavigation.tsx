'use client';

import React, { useEffect, useState } from 'react';
import { ChevronDown, BookOpen, Layers } from 'lucide-react';
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
      { rootMargin: '0px 0px -60% 0px', threshold: 0 }
    );

    chapters.forEach((ch) => {
      const el = document.getElementById(ch.slug);
      if (el) observer.observe(el);
    });

    return () => observer.disconnect();
  }, [chapters]);

  if (!chapters || chapters.length === 0) return null;

  const activeChapter = chapters.find((c) => c.slug === activeSlug) || chapters[0];

  return (
    <>
      {/* Mobile Expandable Chapter Dropdown */}
      {(mode === 'mobile' || mode === 'all') && (
        <div className={`my-6 rounded-2xl border border-zinc-200 bg-[#FBFaf7] p-4 ${mode === 'all' ? 'lg:hidden' : ''}`}>
          <button
            onClick={() => setIsOpenMobile(!isOpenMobile)}
            className="w-full flex items-center justify-between text-xs font-bold text-zinc-900 uppercase tracking-wider font-mono cursor-pointer"
          >
            <div className="flex items-center gap-2">
              <Layers className="w-4 h-4 text-zinc-600" />
              <span>Chapters ({chapters.length})</span>
            </div>
            <div className="flex items-center gap-2">
              <span className="text-[11px] text-zinc-600 truncate max-w-[150px] font-sans">
                {activeChapter ? `${activeChapter.number}. ${activeChapter.title}` : ''}
              </span>
              <ChevronDown
                className={`w-4 h-4 text-zinc-500 transition-transform ${isOpenMobile ? 'rotate-180' : ''}`}
              />
            </div>
          </button>

          {isOpenMobile && (
            <nav className="mt-3 pt-3 border-t border-zinc-200/80 space-y-1.5">
              {chapters.map((ch) => {
                const isActive = activeSlug === ch.slug;
                return (
                  <a
                    key={ch.slug}
                    href={`#${ch.slug}`}
                    onClick={() => setIsOpenMobile(false)}
                    className={`block text-xs leading-relaxed py-1.5 px-2 rounded-lg transition-colors ${
                      isActive
                        ? 'font-bold bg-zinc-200/70 text-zinc-950'
                        : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100'
                    }`}
                  >
                    <span className="font-mono text-[10px] text-zinc-500 mr-2">{ch.number}</span>
                    <span>{ch.title}</span>
                  </a>
                );
              })}
            </nav>
          )}
        </div>
      )}

      {/* Desktop Sticky Chapter Navigation Rail */}
      {(mode === 'desktop' || mode === 'all') && (
        <div className={`space-y-3 ${mode === 'all' ? 'hidden lg:block' : ''}`}>
          <div className="text-[11px] font-mono font-bold uppercase tracking-widest text-zinc-400 flex items-center gap-2">
            <BookOpen className="w-3.5 h-3.5" />
            <span>PLAYBOOK CHAPTERS</span>
          </div>

          <nav className="space-y-1 text-xs border-l border-zinc-200 pl-3">
            {chapters.map((ch) => {
              const isActive = activeSlug === ch.slug;
              return (
                <a
                  key={ch.slug}
                  href={`#${ch.slug}`}
                  className={`group flex items-start gap-2 py-2 transition-all leading-snug ${
                    isActive
                      ? 'font-bold text-zinc-950 -ml-[13px] border-l-2 border-zinc-950 pl-3'
                      : 'text-zinc-500 hover:text-zinc-900'
                  }`}
                >
                  <span
                    className={`font-mono text-[10px] shrink-0 mt-0.5 ${
                      isActive ? 'text-zinc-950 font-bold' : 'text-zinc-400 group-hover:text-zinc-600'
                    }`}
                  >
                    {ch.number}
                  </span>
                  <span className="line-clamp-2">{ch.title}</span>
                </a>
              );
            })}
          </nav>
        </div>
      )}
    </>
  );
}
