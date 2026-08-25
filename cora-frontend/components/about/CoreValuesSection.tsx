'use client';

import React, { useEffect, useRef, useState } from 'react';

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
  const sectionRef = useRef<HTMLDivElement>(null);
  const [scrollProgress, setScrollProgress] = useState(0);

  useEffect(() => {
    const handleScroll = () => {
      if (!sectionRef.current) return;
      const rect = sectionRef.current.getBoundingClientRect();
      const windowHeight = window.innerHeight;
      
      // Calculate how far we have scrolled through the sticky section
      const totalScrollable = rect.height - windowHeight;
      if (totalScrollable <= 0) return;

      const currentScroll = -rect.top;
      const progress = Math.min(Math.max(currentScroll / totalScrollable, 0), 1);
      
      setScrollProgress(progress);
    };

    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  // Calculate current active card index
  const activeIndex = Math.min(
    Math.floor(scrollProgress * VALUES.length),
    VALUES.length - 1
  );

  return (
    <div ref={sectionRef} className="relative w-full h-[240vh] md:h-auto">
      
      {/* 
        Sticky Container on Mobile (Pins while scrolling vertically, translating horizontally like GSAP)
        Static Grid on Desktop (Standard 3-column layout)
      */}
      <div className="sticky top-20 sm:top-28 md:static w-full space-y-8 sm:space-y-12 overflow-hidden md:overflow-visible">
        
        {/* Section Header */}
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
          Mobile: Translated track automatically driven by vertical page scroll
          Desktop: Clean 3-column grid
        */}
        <div className="relative w-full overflow-hidden md:overflow-visible">
          
          {/* Mobile Animated Horizontal Track (Translates automatically on vertical scroll) */}
          <div
            className="flex md:hidden gap-6 transition-transform duration-75 ease-out px-4"
            style={{
              transform: `translateX(calc(-${scrollProgress * (VALUES.length - 1) * 82}vw))`
            }}
          >
            {VALUES.map((val, idx) => (
              <div
                key={idx}
                className="w-[80vw] xs:w-[75vw] shrink-0 border-t border-zinc-200 pt-6 space-y-3"
              >
                <span className="text-xs font-mono font-bold text-zinc-400 uppercase tracking-wider block">
                  {val.num}
                </span>
                <h3 className="font-display text-xl xs:text-2xl font-bold text-zinc-950 tracking-tight">
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

        {/* Mobile Live Scroll Progress Tracker (Only on Mobile) */}
        <div className="flex md:hidden items-center justify-between pt-2 px-4">
          <span className="text-xs font-mono font-medium text-zinc-400">
            0{activeIndex + 1} / 0{VALUES.length}
          </span>

          <div className="flex items-center gap-1.5">
            {VALUES.map((_, i) => (
              <div
                key={i}
                className={`h-1.5 rounded-full transition-all duration-200 ${
                  activeIndex === i ? 'w-6 bg-zinc-950' : 'w-1.5 bg-zinc-300'
                }`}
              />
            ))}
          </div>

          <span className="text-[10px] font-mono text-zinc-400 uppercase tracking-wider">
            Scroll to explore ↓
          </span>
        </div>

      </div>

    </div>
  );
}
