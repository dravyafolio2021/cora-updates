'use client';

import React, { useState, useRef } from 'react';
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
  const [activeIndex, setActiveIndex] = useState(0);
  const carouselRef = useRef<HTMLDivElement>(null);

  const handleScroll = () => {
    if (!carouselRef.current) return;
    const { scrollLeft, clientWidth } = carouselRef.current;
    const cardWidth = clientWidth * 0.85;
    const index = Math.round(scrollLeft / cardWidth);
    setActiveIndex(Math.min(Math.max(index, 0), VALUES.length - 1));
  };

  const scrollToIndex = (index: number) => {
    if (!carouselRef.current) return;
    const { clientWidth } = carouselRef.current;
    const cardWidth = clientWidth * 0.85;
    carouselRef.current.scrollTo({
      left: index * cardWidth,
      behavior: 'smooth'
    });
    setActiveIndex(index);
  };

  return (
    <div className="w-full space-y-8 sm:space-y-12">
      
      {/* Header */}
      <div className="text-center space-y-2.5">
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
        Mobile: Native Snap-Scroll Carousel (No vertical space bloat, clean snap cards)
        Desktop: Unboxed 3-column grid
      */}
      <div className="relative w-full">
        
        {/* Mobile Swipe Strip */}
        <div
          ref={carouselRef}
          onScroll={handleScroll}
          className="flex md:hidden overflow-x-auto snap-x snap-mandatory scrollbar-none gap-4 px-4 -mx-4 pb-2"
          style={{ WebkitOverflowScrolling: 'touch' }}
        >
          {VALUES.map((val, idx) => (
            <div
              key={idx}
              className="w-[85vw] max-w-[340px] shrink-0 snap-start border-t border-zinc-200 pt-5 space-y-2.5 select-none"
            >
              <span className="text-xs font-mono font-bold text-zinc-400 uppercase tracking-wider block">
                {val.num}
              </span>
              <h3 className="font-display text-xl font-bold text-zinc-950 tracking-tight">
                {val.title}
              </h3>
              <p className="text-sm text-zinc-600 leading-relaxed font-normal">
                {val.desc}
              </p>
            </div>
          ))}
        </div>

        {/* Desktop 3-Column Grid */}
        <div className="hidden md:grid md:grid-cols-3 gap-x-12 gap-y-16">
          {VALUES.map((val, idx) => (
            <div
              key={idx}
              className="border-t border-zinc-200 pt-6 space-y-3"
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

      </div>

      {/* Mobile Indicator & Nav Controls */}
      <div className="flex md:hidden items-center justify-between px-1">
        <span className="text-xs font-mono font-medium text-zinc-400">
          0{activeIndex + 1} / 0{VALUES.length}
        </span>

        {/* Minimal Progress Dots */}
        <div className="flex items-center gap-1.5">
          {VALUES.map((_, i) => (
            <button
              key={i}
              type="button"
              onClick={() => scrollToIndex(i)}
              aria-label={`Go to principle ${i + 1}`}
              className={`h-1.5 rounded-full transition-all duration-200 ${
                activeIndex === i ? 'w-5 bg-zinc-950' : 'w-1.5 bg-zinc-300'
              }`}
            />
          ))}
        </div>

        {/* Arrows */}
        <div className="flex items-center gap-1">
          <button
            type="button"
            onClick={() => scrollToIndex(Math.max(0, activeIndex - 1))}
            disabled={activeIndex === 0}
            className="w-7 h-7 rounded-full border border-zinc-200 flex items-center justify-center text-zinc-600 disabled:opacity-30 transition-opacity"
            aria-label="Previous"
          >
            <ChevronLeft className="w-3.5 h-3.5" />
          </button>
          <button
            type="button"
            onClick={() => scrollToIndex(Math.min(VALUES.length - 1, activeIndex + 1))}
            disabled={activeIndex === VALUES.length - 1}
            className="w-7 h-7 rounded-full border border-zinc-200 flex items-center justify-center text-zinc-600 disabled:opacity-30 transition-opacity"
            aria-label="Next"
          >
            <ChevronRight className="w-3.5 h-3.5" />
          </button>
        </div>
      </div>

    </div>
  );
}
