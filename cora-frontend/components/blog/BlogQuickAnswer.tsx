'use client';

import React from 'react';
import { Sparkles, CheckCircle2, ArrowDown, Share2 } from 'lucide-react';
import type { QuickAnswer } from '@/lib/blog-data';

interface BlogQuickAnswerProps {
  quickAnswer: QuickAnswer;
  onShare?: () => void;
}

export function BlogQuickAnswer({ quickAnswer, onShare }: BlogQuickAnswerProps) {
  if (!quickAnswer) return null;

  return (
    <section 
      aria-label="Quick Answer Summary"
      className="my-8 sm:my-10 rounded-3xl border border-zinc-200/90 bg-[#FBFaf7] p-6 sm:p-8 shadow-xs relative overflow-hidden"
    >
      {/* Subtle top indicator bar */}
      <div className="flex flex-wrap items-center justify-between gap-3 mb-4 pb-3.5 border-b border-zinc-200/80">
        <div className="inline-flex items-center gap-2">
          <div className="w-5 h-5 rounded-full bg-zinc-950 text-white flex items-center justify-center shrink-0">
            <Sparkles className="w-3 h-3 text-amber-300 fill-amber-300" />
          </div>
          <span className="text-[11px] font-mono font-bold tracking-wider text-zinc-900 uppercase">
            Quick Answer &bull; 45-Second Read
          </span>
        </div>

        {onShare && (
          <button
            type="button"
            onClick={onShare}
            className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border border-zinc-200/80 bg-white text-[11px] font-mono text-zinc-600 hover:text-zinc-950 hover:border-zinc-400 transition-colors cursor-pointer shadow-2xs"
            title="Share this quick answer"
          >
            <Share2 className="w-3 h-3 text-zinc-500" />
            <span>Share Insight</span>
          </button>
        )}
      </div>

      {/* Core Bold Summary */}
      <h2 className="font-display text-base sm:text-lg md:text-xl font-bold tracking-tight text-zinc-950 leading-snug">
        {quickAnswer.summary}
      </h2>

      {/* Direct Concise Answer */}
      <p className="mt-3 text-xs sm:text-sm text-zinc-600 leading-relaxed font-normal">
        {quickAnswer.directResponse}
      </p>

      {/* Key Actionable Bullet Highlights */}
      {quickAnswer.bulletHighlights && quickAnswer.bulletHighlights.length > 0 && (
        <div className="mt-5 pt-4 border-t border-zinc-200/70 space-y-2.5">
          <div className="text-[10px] font-mono font-bold uppercase tracking-widest text-zinc-600">
            KEY SYSTEM PRINCIPLES:
          </div>
          <ul className="space-y-2 text-xs sm:text-sm text-zinc-700">
            {quickAnswer.bulletHighlights.map((bullet, idx) => (
              <li key={idx} className="flex items-start gap-2.5">
                <CheckCircle2 className="w-4 h-4 text-zinc-900 shrink-0 mt-0.5 stroke-[2.2]" />
                <span className="leading-snug">{bullet}</span>
              </li>
            ))}
          </ul>
        </div>
      )}

      {/* Bottom Hint */}
      <div className="mt-5 pt-3 flex items-center justify-between text-[11px] font-mono text-zinc-600">
        <span className="flex items-center gap-1">
          <ArrowDown className="w-3 h-3 text-zinc-600" />
          <span>Detailed frameworks, scripts, and contract clauses below</span>
        </span>
      </div>
    </section>
  );
}
