'use client';

import React, { useState } from 'react';
import { BookOpen, Download, X, Check, Clock, ChevronUp } from 'lucide-react';
import { GuideChapter, DownloadableAsset } from '@/lib/guides-data';

interface GuideMobileBarProps {
  chapters: GuideChapter[];
  activeChapterIndex: number;
  onSelectChapter: (index: number) => void;
  onOpenDownloadModal: () => void;
  asset?: DownloadableAsset;
  completedChapters?: Set<number>;
}

export function GuideMobileBar({
  chapters,
  activeChapterIndex,
  onSelectChapter,
  onOpenDownloadModal,
  asset,
  completedChapters = new Set(),
}: GuideMobileBarProps) {
  const [isDrawerOpen, setIsDrawerOpen] = useState(false);
  const activeChapter = chapters[activeChapterIndex] || chapters[0];

  const handleChapterClick = (index: number) => {
    onSelectChapter(index);
    setIsDrawerOpen(false);
  };

  return (
    <>
      {/* 1. Mobile Bottom Sticky Navigation Island Bar */}
      <div className="lg:hidden fixed bottom-0 inset-x-0 z-40 p-3 bg-white/95 backdrop-blur-md border-t border-zinc-200/90 shadow-[0_-4px_20px_rgba(0,0,0,0.06)]">
        <div className="flex items-center gap-2 max-w-[540px] mx-auto">
          {/* Contents Drawer Trigger Button */}
          <button
            type="button"
            onClick={() => setIsDrawerOpen(true)}
            className="flex-1 flex items-center justify-between gap-2 px-4 py-2.5 rounded-xl bg-zinc-100 hover:bg-zinc-200/80 text-zinc-900 text-xs font-bold transition-colors"
          >
            <div className="flex items-center gap-2 min-w-0">
              <BookOpen className="w-3.5 h-3.5 text-zinc-700 shrink-0" />
              <span className="truncate">
                Ch. {activeChapter?.number}: {activeChapter?.title}
              </span>
            </div>
            <ChevronUp className="w-3.5 h-3.5 text-zinc-500 shrink-0" />
          </button>

          {/* Download Lead Magnet CTA Button */}
          {asset && (
            <button
              type="button"
              onClick={onOpenDownloadModal}
              className="flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-zinc-950 hover:bg-black text-white text-xs font-bold shadow-sm shrink-0 transition-colors"
            >
              <Download className="w-3.5 h-3.5" />
              <span>Download</span>
            </button>
          )}
        </div>
      </div>

      {/* 2. Mobile Table of Contents Bottom Sheet Drawer */}
      {isDrawerOpen && (
        <div className="lg:hidden fixed inset-0 z-50 flex items-end bg-zinc-950/60 backdrop-blur-sm animate-in fade-in duration-200">
          {/* Backdrop Click Dismiss */}
          <div className="absolute inset-0" onClick={() => setIsDrawerOpen(false)} />

          {/* Drawer Sheet Container */}
          <div
            className="relative w-full max-h-[85vh] bg-white rounded-t-3xl border-t border-zinc-200 shadow-2xl p-6 z-10 flex flex-col animate-in slide-in-from-bottom duration-250"
            onClick={(e) => e.stopPropagation()}
          >
            {/* Drag Handle */}
            <div className="w-12 h-1 bg-zinc-300 rounded-full mx-auto mb-4" />

            {/* Header */}
            <div className="flex items-center justify-between pb-3 border-b border-zinc-200">
              <div>
                <span className="font-display font-bold text-base text-zinc-950 block">
                  Table of Contents
                </span>
                <span className="text-[11px] font-mono text-zinc-500">
                  {chapters.length} Chapters • Digital Book Index
                </span>
              </div>
              <button
                type="button"
                onClick={() => setIsDrawerOpen(false)}
                className="p-2 rounded-full text-zinc-400 hover:text-zinc-950 hover:bg-zinc-100 transition-colors"
                aria-label="Close table of contents"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            {/* Scrollable Chapter List */}
            <ol className="overflow-y-auto py-3 space-y-1.5 flex-1">
              {chapters.map((chapter, index) => {
                const isActive = index === activeChapterIndex;
                const isCompleted = completedChapters.has(index);

                return (
                  <li key={chapter.id || chapter.slug}>
                    <button
                      type="button"
                      onClick={() => handleChapterClick(index)}
                      className={`w-full text-left flex items-start gap-3 p-3 rounded-xl transition-colors text-xs ${
                        isActive
                          ? 'bg-zinc-950 text-white font-bold shadow-sm'
                          : 'text-zinc-700 hover:bg-zinc-100'
                      }`}
                    >
                      <div
                        className={`w-6 h-6 rounded-lg flex items-center justify-center shrink-0 font-mono text-[10.5px] font-bold ${
                          isActive
                            ? 'bg-white text-zinc-950'
                            : isCompleted
                            ? 'bg-zinc-200 text-zinc-900'
                            : 'bg-zinc-100 text-zinc-600'
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
                        <div
                          className={`flex items-center gap-1.5 mt-1 text-[10px] font-mono ${
                            isActive ? 'text-zinc-300' : 'text-zinc-400'
                          }`}
                        >
                          <Clock className="w-2.5 h-2.5" />
                          <span>{chapter.readTime}</span>
                        </div>
                      </div>
                    </button>
                  </li>
                );
              })}
            </ol>

            {/* Bottom Quick Download Trigger in Drawer */}
            {asset && (
              <div className="pt-3 border-t border-zinc-200">
                <button
                  type="button"
                  onClick={() => {
                    setIsDrawerOpen(false);
                    onOpenDownloadModal();
                  }}
                  className="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-zinc-950 text-white font-bold text-xs hover:bg-black transition-colors"
                >
                  <Download className="w-3.5 h-3.5" />
                  <span>Download Complete Playbook (PDF)</span>
                </button>
              </div>
            )}
          </div>
        </div>
      )}
    </>
  );
}
