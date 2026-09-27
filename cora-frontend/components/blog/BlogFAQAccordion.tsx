'use client';

import React, { useState } from 'react';
import { ChevronDown, HelpCircle } from 'lucide-react';

export interface FAQItem {
  question: string;
  answer: string;
}

interface BlogFAQAccordionProps {
  faqs?: FAQItem[];
  title?: string;
  description?: string;
}

export function BlogFAQAccordion({
  faqs,
  title = 'Frequently Asked Questions',
  description = 'Clear, practical answers to common operational questions regarding scope governance and agency workflows.',
}: BlogFAQAccordionProps) {
  const [openIndex, setOpenIndex] = useState<number | null>(0); // First open by default

  if (!faqs || faqs.length === 0) return null;

  const toggle = (idx: number) => {
    setOpenIndex(openIndex === idx ? null : idx);
  };

  return (
    <section 
      aria-label="Frequently Asked Questions"
      className="my-12 pt-8 border-t border-zinc-200/90"
    >
      <div className="mb-6">
        <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-zinc-100 text-zinc-800 text-[10px] font-mono font-bold uppercase tracking-wider mb-2">
          <HelpCircle className="w-3 h-3 text-zinc-600" />
          <span>EDITORIAL Q&amp;A</span>
        </div>
        <h2 className="font-display text-xl sm:text-2xl font-bold tracking-tight text-zinc-950">
          {title}
        </h2>
        {description && (
          <p className="mt-1 text-xs sm:text-sm text-zinc-600 font-normal">
            {description}
          </p>
        )}
      </div>

      <div className="space-y-3">
        {faqs.map((faq, idx) => {
          const isOpen = openIndex === idx;
          return (
            <div
              key={idx}
              className={`rounded-2xl border transition-all overflow-hidden ${
                isOpen
                  ? 'border-zinc-300 bg-[#FBFaf7] shadow-2xs'
                  : 'border-zinc-200/80 bg-white hover:border-zinc-300'
              }`}
            >
              <button
                type="button"
                onClick={() => toggle(idx)}
                aria-expanded={isOpen}
                className="w-full px-5 py-4 text-left flex items-center justify-between gap-4 cursor-pointer select-none"
              >
                <span className="font-display text-xs sm:text-sm md:text-base font-bold text-zinc-950 leading-snug">
                  {faq.question}
                </span>
                <span
                  className={`w-6 h-6 rounded-full border border-zinc-200 bg-white flex items-center justify-center shrink-0 text-zinc-500 transition-transform duration-200 ${
                    isOpen ? 'rotate-180 text-zinc-950' : ''
                  }`}
                >
                  <ChevronDown className="w-3.5 h-3.5" />
                </span>
              </button>

              {isOpen && (
                <div className="px-5 pb-4 pt-1 text-xs sm:text-sm text-zinc-600 leading-relaxed font-normal border-t border-zinc-200/50">
                  <p>{faq.answer}</p>
                </div>
              )}
            </div>
          );
        })}
      </div>
    </section>
  );
}
