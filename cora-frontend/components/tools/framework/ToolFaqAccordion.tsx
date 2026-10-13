'use client';

import React, { useState } from 'react';
import { ChevronDown, HelpCircle } from 'lucide-react';

export interface FaqItem {
  question: string;
  answer: string;
}

export interface ToolFaqAccordionProps {
  title?: string;
  items: FaqItem[];
}

export function ToolFaqAccordion({
  title = 'Frequently Asked Questions',
  items,
}: ToolFaqAccordionProps) {
  const [openIndex, setOpenIndex] = useState<number | null>(0);

  const toggle = (idx: number) => {
    setOpenIndex((current) => (current === idx ? null : idx));
  };

  return (
    <div className="rounded-2xl border border-zinc-200/90 bg-white p-6 sm:p-8 shadow-xs space-y-4">
      <div className="flex items-center gap-2 border-b border-zinc-200/70 pb-4 mb-2">
        <HelpCircle className="w-5 h-5 text-zinc-700" />
        <h3 className="font-display text-lg sm:text-xl font-bold text-zinc-950 tracking-tight">
          {title}
        </h3>
      </div>

      <div className="divide-y divide-zinc-200/70">
        {items.map((item, idx) => {
          const isOpen = openIndex === idx;
          return (
            <div key={idx} className="py-3.5 first:pt-0 last:pb-0">
              <button
                type="button"
                onClick={() => toggle(idx)}
                className="w-full flex items-center justify-between gap-4 text-left group focus:outline-hidden"
                aria-expanded={isOpen}
              >
                <span className="text-sm sm:text-base font-semibold text-zinc-900 group-hover:text-zinc-950 transition-colors">
                  {item.question}
                </span>
                <ChevronDown
                  className={`w-4 h-4 text-zinc-400 shrink-0 transition-transform duration-200 ${
                    isOpen ? 'rotate-180 text-zinc-800' : ''
                  }`}
                />
              </button>

              {isOpen && (
                <div className="mt-2 text-xs sm:text-sm text-zinc-600 leading-relaxed pr-6">
                  <p>{item.answer}</p>
                </div>
              )}
            </div>
          );
        })}
      </div>
    </div>
  );
}
