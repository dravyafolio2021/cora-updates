'use client';

import React from 'react';
import Link from 'next/link';

export function MadeInCoraBadge() {
  return (
    <aside aria-label="Made in Cora brand badge" className="fixed bottom-4 right-4 sm:bottom-5 sm:right-5 z-30 hidden sm:inline-flex">
      <Link
        href="https://heycora.in"
        target="_blank"
        rel="noopener noreferrer"
        className="inline-flex items-center gap-2 bg-white/95 backdrop-blur-md px-3.5 py-1.5 sm:px-4 sm:py-2 rounded-full shadow-[0px_4px_20px_rgba(0,0,0,0.10)] border border-zinc-200/90 text-xs font-bold text-zinc-950 hover:bg-white hover:shadow-[0px_6px_25px_rgba(0,0,0,0.15)] hover:border-zinc-300 transition-all hover:-translate-y-0.5 active:translate-y-0 select-none group"
      >
        {/* Layer Stack Icon Matching Screenshot */}
        <svg
          className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-zinc-950 transition-transform group-hover:scale-105"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="2.2"
          strokeLinecap="round"
          strokeLinejoin="round"
        >
          <path d="M12 2L2 7l10 5 10-5-10-5z" />
          <path d="M2 17l10 5 10-5" />
          <path d="M2 12l10 5 10-5" />
        </svg>
        <span className="font-display tracking-tight">Made in Cora</span>
      </Link>
    </aside>
  );
}
