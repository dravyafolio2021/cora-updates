'use client';

import React, { useEffect, useState } from 'react';
import { ChevronDown, List, AlignLeft } from 'lucide-react';

interface TOCItem {
  id: string;
  text: string;
  level: number;
}

interface BlogTableOfContentsProps {
  headings: TOCItem[];
}

export function BlogTableOfContents({ headings }: BlogTableOfContentsProps) {
  const [activeId, setActiveId] = useState<string>('');
  const [isOpenMobile, setIsOpenMobile] = useState(false);

  useEffect(() => {
    if (!headings.length) return;

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            setActiveId(entry.target.id);
          }
        });
      },
      { rootMargin: '0px 0px -65% 0px', threshold: 0 }
    );

    headings.forEach((h) => {
      const el = document.getElementById(h.id);
      if (el) observer.observe(el);
    });

    return () => observer.disconnect();
  }, [headings]);

  if (!headings || headings.length === 0) return null;

  return (
    <>
      {/* Mobile Expandable TOC Dropdown */}
      <div className="lg:hidden my-6 rounded-2xl border border-zinc-200 bg-[#FBFaf7] p-4">
        <button
          onClick={() => setIsOpenMobile(!isOpenMobile)}
          className="w-full flex items-center justify-between text-xs font-bold text-zinc-900 uppercase tracking-wider font-mono cursor-pointer"
        >
          <div className="flex items-center gap-2">
            <AlignLeft className="w-4 h-4 text-zinc-600" />
            <span>On this page</span>
          </div>
          <ChevronDown
            className={`w-4 h-4 text-zinc-500 transition-transform ${isOpenMobile ? 'rotate-180' : ''}`}
          />
        </button>

        {isOpenMobile && (
          <nav className="mt-3 pt-3 border-t border-zinc-200/80 space-y-2">
            {headings.map((h) => (
              <a
                key={h.id}
                href={`#${h.id}`}
                onClick={() => setIsOpenMobile(false)}
                className={`block text-xs leading-relaxed transition-colors py-1 ${
                  h.level === 3 ? 'pl-4 text-[11px]' : ''
                } ${
                  activeId === h.id
                    ? 'font-bold text-zinc-950'
                    : 'text-zinc-600 hover:text-zinc-900'
                }`}
              >
                {h.text}
              </a>
            ))}
          </nav>
        )}
      </div>

      {/* Desktop Sticky Rail TOC */}
      <div className="hidden lg:block sticky top-28 space-y-3">
        <div className="text-[11px] font-mono font-bold uppercase tracking-widest text-zinc-400 flex items-center gap-2">
          <List className="w-3.5 h-3.5" />
          <span>ON THIS PAGE</span>
        </div>

        <nav className="space-y-1 text-xs border-l border-zinc-200 pl-3">
          {headings.map((h) => {
            const isActive = activeId === h.id;
            return (
              <a
                key={h.id}
                href={`#${h.id}`}
                className={`block py-1.5 transition-all leading-snug ${
                  h.level === 3 ? 'pl-3 text-[11px]' : ''
                } ${
                  isActive
                    ? 'font-bold text-zinc-950 -ml-[13px] border-l-2 border-zinc-950 pl-3'
                    : 'text-zinc-500 hover:text-zinc-900'
                }`}
              >
                {h.text}
              </a>
            );
          })}
        </nav>
      </div>
    </>
  );
}
