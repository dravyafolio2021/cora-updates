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
  const containerRef = useRef<HTMLDivElement>(null);
  const trackRef = useRef<HTMLDivElement>(null);
  const [scrollProgress, setScrollProgress] = useState(0);
  const [activeIndex, setActiveIndex] = useState(0);

  useEffect(() => {
    let animationFrameId: number;

    const handleScroll = () => {
      if (!containerRef.current || !trackRef.current) return;
      const rect = containerRef.current.getBoundingClientRect();
      const windowHeight = window.innerHeight;
      const totalScrollDistance = rect.height - windowHeight;

      if (totalScrollDistance <= 0) return;

      const scrolled = -rect.top;
      const progress = Math.max(0, Math.min(1, scrolled / totalScrollDistance));

      setScrollProgress(progress);

      const parentWidth = trackRef.current.parentElement?.clientWidth || window.innerWidth;
      const trackWidth = trackRef.current.scrollWidth;
      const maxTranslate = Math.max(0, trackWidth - parentWidth);

      trackRef.current.style.transform = `translateX(-${progress * maxTranslate}px)`;

      const currentIndex = Math.min(
        Math.round(progress * (VALUES.length - 1)),
        VALUES.length - 1
      );
      setActiveIndex(currentIndex);
    };

    const onScroll = () => {
      cancelAnimationFrame(animationFrameId);
      animationFrameId = requestAnimationFrame(handleScroll);
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', handleScroll);
    handleScroll();

    return () => {
      window.removeEventListener('scroll', onScroll);
      window.removeEventListener('resize', handleScroll);
      cancelAnimationFrame(animationFrameId);
    };
  }, []);

  return (
    <div ref={containerRef} className="relative w-full h-[320vh]">
      
      {/* Pinned Viewport Container (GSAP Parallax Style) */}
      <div className="sticky top-0 h-screen flex flex-col justify-center overflow-hidden py-12">
        <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 space-y-10 sm:space-y-14">
          
          {/* Section Header */}
          <div className="text-center space-y-2.5 max-w-[620px] mx-auto">
            <span className="text-[11px] sm:text-xs font-mono font-semibold uppercase tracking-widest text-zinc-500 block">
              DNA &amp; PRINCIPLES
            </span>
            <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl font-bold text-zinc-950 tracking-tight">
              Our core values.
            </h2>
            <p className="text-sm sm:text-base text-zinc-600 font-normal">
              The fundamental beliefs that guide how we build products, support creators, and grow together.
            </p>
          </div>

          {/* Horizontal Translating Track */}
          <div className="relative w-full overflow-hidden">
            <div
              ref={trackRef}
              className="flex gap-8 sm:gap-12 will-change-transform transition-transform duration-75 ease-out pr-12"
            >
              {VALUES.map((val, idx) => {
                const isActive = activeIndex === idx;

                return (
                  <div
                    key={idx}
                    className={`w-[82vw] sm:w-[420px] shrink-0 border-t-2 pt-6 space-y-3 transition-opacity duration-300 ${
                      isActive ? 'border-zinc-950 opacity-100' : 'border-zinc-200 opacity-60'
                    }`}
                  >
                    <div className="flex items-center justify-between">
                      <span className="text-xs font-mono font-bold text-zinc-400 uppercase tracking-wider">
                        {val.num}
                      </span>
                      {isActive && (
                        <span className="w-1.5 h-1.5 rounded-full bg-zinc-950 animate-pulse" />
                      )}
                    </div>

                    <h3 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight">
                      {val.title}
                    </h3>

                    <p className="text-sm sm:text-base text-zinc-600 leading-relaxed font-normal">
                      {val.desc}
                    </p>
                  </div>
                );
              })}
            </div>
          </div>

          {/* Progress Bar & Stage Indicator */}
          <div className="flex items-center justify-between max-w-[420px] mx-auto w-full pt-2">
            <span className="text-xs font-mono font-bold text-zinc-950">
              0{activeIndex + 1} / 0{VALUES.length}
            </span>

            {/* Continuous Fill Bar */}
            <div className="flex-1 mx-6 h-1 bg-zinc-200 rounded-full overflow-hidden">
              <div
                className="h-full bg-zinc-950 transition-all duration-100 rounded-full"
                style={{ width: `${Math.max(8, scrollProgress * 100)}%` }}
              />
            </div>

            <span className="text-[11px] font-mono text-zinc-400 uppercase tracking-wider">
              Scroll ↓
            </span>
          </div>

        </div>
      </div>

    </div>
  );
}
