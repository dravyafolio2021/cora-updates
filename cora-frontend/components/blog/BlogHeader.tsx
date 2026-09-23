import React from 'react';
import { Sparkles, BookOpen } from 'lucide-react';

interface BlogHeaderProps {
  title?: string;
  description?: string;
  badge?: string;
}

export function BlogHeader({
  title = 'Ideas for running a better business.',
  description = 'Practical systems, workflows, research and operating lessons for agencies and service businesses.',
  badge = 'CORA EDITORIAL PUBLICATION',
}: BlogHeaderProps) {
  return (
    <section className="pt-28 sm:pt-36 pb-12 sm:pb-16 border-b border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-950">
      <div className="mx-auto max-w-[1240px] px-4 sm:px-6">
        <div className="max-w-3xl">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-zinc-200 dark:border-zinc-800 bg-[#FBFaf7] dark:bg-zinc-900 text-[11px] font-mono font-semibold text-zinc-800 dark:text-zinc-200 mb-5 shadow-xs">
            <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" />
            <span>{badge}</span>
          </div>

          <h1 className="font-display text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-zinc-950 dark:text-white leading-[1.08]">
            {title}
          </h1>

          <p className="mt-4 text-base sm:text-lg text-zinc-600 dark:text-zinc-400 leading-relaxed max-w-2xl font-normal">
            {description}
          </p>
        </div>
      </div>
    </section>
  );
}
