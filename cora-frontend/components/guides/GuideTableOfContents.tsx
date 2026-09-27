'use client';

import React from 'react';
import { GuideChapter } from '@/lib/guides-data';
import { Check, Clock, BookOpen } from 'lucide-react';

interface GuideTableOfContentsProps {
  chapters: GuideChapter[];
  activeChapterIndex: number;
  onSelectChapter: (index: number) => void;
  completedChapters?: Set<number>;
}

export function GuideTableOfContents({
  chapters,
  activeChapterIndex,
  onSelectChapter,
  completedChapters = new Set(),
}: GuideTableOfContentsProps) {
  return (
    <nav className="p-5 rounded-2xl bg-zinc-50 border border-zinc-200/90 shadow-2xs space-y-4">
      {/* Header */}
      <div className="flex items-center justify-between pb-3 border-b border-zinc-200/80">
        <div className="flex items-center gap-2">
          <BookOpen className="w-4 h-4 text-zinc-800" />
          <span className="font-display font-bold text-xs uppercase tracking-wider text-zinc-950">
            Table of Contents
          </span>
        </div>
        <span className="text-[11px] font-mono text-zinc-500 font-semibold">
          {chapters.length} Chapters
        </span>
      </div>

      {/* Chapters Index List */}
      <ol className="space-y-1">
        {chapters.map((chapter, index) => {
          const isActive = index === activeChapterIndex;
          const isCompleted = completedChapters.has(index);

          return (
            <li key={chapter.id || chapter.slug}>
              <button
                type="button"
                onClick={() => onSelectChapter(index)}
                className={`w-full text-left flex items-start gap-3 p-2.5 rounded-xl transition-all duration-200 text-xs ${
                  isActive
                    ? 'bg-white text-zinc-950 font-bold shadow-2xs border border-zinc-200/90'
                    : 'text-zinc-600 hover:text-zinc-950 hover:bg-zinc-100/70 border border-transparent'
                }`}
              >
                {/* Number / Status Icon */}
                <div
                  className={`w-6 h-6 rounded-lg flex items-center justify-center shrink-0 font-mono text-[10.5px] font-bold transition-colors ${
                    isActive
                      ? 'bg-zinc-950 text-white'
                      : isCompleted
                      ? 'bg-zinc-200 text-zinc-900'
                      : 'bg-zinc-200/70 text-zinc-600'
                  }`}
                >
                  {isCompleted && !isActive ? (
                    <Check className="w-3 h-3 stroke-[2.5]" />
                  ) : (
                    <span>{chapter.number}</span>
                  )}
                </div>

                <div className="min-w-0 flex-1">
                  <p className="line-clamp-2 leading-snug">{chapter.title}</p>
                  <div className="flex items-center gap-1.5 mt-1 text-[10px] font-mono text-zinc-400">
                    <Clock className="w-2.5 h-2.5" />
                    <span>{chapter.readTime}</span>
                  </div>
                </div>
              </button>
            </li>
          );
        })}
      </ol>
    </nav>
  );
}
