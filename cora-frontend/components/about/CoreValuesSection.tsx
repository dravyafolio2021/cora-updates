'use client';

import React, { useState, useRef, useEffect } from 'react';
import { ChevronLeft, ChevronRight } from 'lucide-react';

const VALUES = [
  {
    num: '01 / CRAFT',
    title: 'Be in the details.',
    desc: 'We obsess over typography, micro-interactions, and visual polish so every touchpoint feels effortless and intentional.'
  },
  {
    num: '02 / SPEED',
    title: 'Move with intent.',
    desc: 'We eliminate administrative friction so studio founders and creative teams can operate at the speed of thought.'
  },
  {
    num: '03 / CLARITY',
    title: 'Radical simplicity.',
    desc: 'No bloated menus or fragmented tools. We replace dozens of disconnected apps with one unified, calm workspace.'
  },
  {
    num: '04 / EMPATHY',
    title: 'Built for creators.',
    desc: 'We design specifically for real-world studio sets, production houses, and the creators who run them every day.'
  },
  {
    num: '05 / ITERATION',
    title: 'Grow 1% every day.',
    desc: 'Continuous refinement through listening closely to our community, fixing nuances quickly, and raising the standard.'
  },
  {
    num: '06 / EXPERIENCE',
    title: 'Customer delight first.',
    desc: 'Our success is measured solely by the time, freedom, and joy our creators gain back for their actual creative work.'
  }
];

export function CoreValuesSection() {
  const scrollRef = useRef<HTMLDivElement>(null);
  const [activeIndex, setActiveIndex] = useState(0);

  const handleScroll = () => {
    if (!scrollRef.current) return;
    const { scrollLeft, offsetWidth } = scrollRef.current;
    const newIndex = Math.round(scrollLeft / (offsetWidth * 0.78));
    setActiveIndex(Math.min(Math.max(newIndex, 0), VALUES.length - 1));
  };

  const scrollTo = (index: number) => {
    if (!scrollRef.current) return;
    const { offsetWidth } = scrollRef.current;
    scrollRef.current.scrollTo({
      left: index * (offsetWidth * 0.78),
      behavior: 'smooth'
    });
    setActiveIndex(index);
  };

  return (
    <div className="w-full space-y-10 sm:space-y-14">
      
      {/* Section Header */}
      <div className="text-center space-y-3">
        <span className="text-[11px] sm:text-xs font-mono font-semibold uppercase tracking-widest text-zinc-500 block">
          DNA &amp; PRINCIPLES
        </span>
        <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl font-bold text-zinc-950 tracking-tight">
          Our core values.
        </h2>
        <p className="text-sm sm:text-base text-zinc-600 max-w-[520px] mx-auto font-normal">
          The fundamental beliefs that guide how we build products, support creators, and grow together.
        </p>
      </div>

      {/* 
        Responsive Strip:
        - Mobile: Horizontal snap strip (fits on 1 screen, user swipes through cards effortlessly)
        - Desktop: 3-Column minimal editorial grid with top hairline borders
      */}
      <div
        ref={scrollRef}
        onScroll={handleScroll}
        className="w-full flex md:grid md:grid-cols-3 gap-6 md:gap-x-12 md:gap-y-16 overflow-x-auto md:overflow-visible snap-x snap-mandatory scrollbar-none px-4 md:px-0 -mx-4 md:mx-0 pb-2 md:pb-0"
        style={{ WebkitOverflowScrolling: 'touch' }}
      >
        {VALUES.map((val, idx) => (
          <div
            key={idx}
            className="w-[82vw] xs:w-[75vw] sm:w-[60vw] md:w-auto shrink-0 snap-start border-t border-zinc-200 pt-6 space-y-3 select-none transition-opacity"
          >
            <span className="text-xs font-mono font-bold text-zinc-400 uppercase tracking-wider block">
              {val.num}
            </span>
            <h3 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
              {val.title}
            </h3>
            <p className="text-sm text-zinc-600 leading-relaxed font-normal">
              {val.desc}
            </p>
          </div>
        ))}
      </div>

      {/* Mobile Swipe Navigation & Progress Tracker (Hidden on Desktop) */}
      <div className="flex md:hidden items-center justify-between pt-2 px-2">
        
        {/* Step Indicator */}
        <span className="text-xs font-mono font-medium text-zinc-400">
          0{activeIndex + 1} / 0{VALUES.length}
        </span>

        {/* Minimal Progress Dots */}
        <div className="flex items-center gap-1.5">
          {VALUES.map((_, i) => (
            <button
              key={i}
              type="button"
              onClick={() => scrollTo(i)}
              aria-label={`Go to principle ${i + 1}`}
              className={`h-1.5 rounded-full transition-all duration-300 ${
                activeIndex === i ? 'w-6 bg-zinc-950' : 'w-1.5 bg-zinc-300'
              }`}
            />
          ))}
        </div>

        {/* Minimal Nav Arrow Controls */}
        <div className="flex items-center gap-1">
          <button
            type="button"
            onClick={() => scrollTo(Math.max(0, activeIndex - 1))}
            disabled={activeIndex === 0}
            className="w-7 h-7 rounded-full border border-zinc-200 flex items-center justify-center text-zinc-600 disabled:opacity-30 transition-opacity"
            aria-label="Previous principle"
          >
            <ChevronLeft className="w-3.5 h-3.5" />
          </button>
          <button
            type="button"
            onClick={() => scrollTo(Math.min(VALUES.length - 1, activeIndex + 1))}
            disabled={activeIndex === VALUES.length - 1}
            className="w-7 h-7 rounded-full border border-zinc-200 flex items-center justify-center text-zinc-600 disabled:opacity-30 transition-opacity"
            aria-label="Next principle"
          >
            <ChevronRight className="w-3.5 h-3.5" />
          </button>
        </div>

      </div>

    </div>
  );
}
