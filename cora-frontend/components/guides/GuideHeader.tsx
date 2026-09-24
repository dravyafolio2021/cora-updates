import React from 'react';
import Link from 'next/link';
import { Home, ChevronRight, BookOpen } from 'lucide-react';

export function GuideHeader() {
  return (
    <header className="border-b border-zinc-200 bg-[#FBFaf7] pt-20 sm:pt-24">
      <div className="mx-auto max-w-[1240px] px-4 sm:px-6 py-3 sm:py-3.5 flex items-center justify-between">
        <div className="flex items-center gap-2 text-xs font-mono text-zinc-500">
          <Link href="/" className="hover:text-zinc-900 transition-colors flex items-center gap-1">
            <Home className="w-3.5 h-3.5" />
            <span>Cora</span>
          </Link>
          <ChevronRight className="w-3 h-3 text-zinc-400" />
          <Link href="/guides" className="text-zinc-900 font-semibold transition-colors flex items-center gap-1">
            <BookOpen className="w-3.5 h-3.5" />
            <span>Guides &amp; Playbooks</span>
          </Link>
        </div>

        <div className="flex items-center gap-4 text-xs font-mono">
          <Link
            href="/blog"
            className="text-zinc-600 hover:text-zinc-950 font-bold transition-colors"
          >
            Blog Articles
          </Link>
          <Link
            href="/tools"
            className="text-zinc-600 hover:text-zinc-950 font-bold transition-colors"
          >
            Tools
          </Link>
        </div>
      </div>
    </header>
  );
}
