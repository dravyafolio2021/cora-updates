'use client';

import React, { useEffect, useRef } from 'react';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

const VALUES = [
  {
    num: '01',
    category: 'CRAFT',
    title: 'Be in the details.',
    desc: 'We obsess over typography, micro-interactions, and visual polish so every touchpoint feels effortless and intentional.'
  },
  {
    num: '02',
    category: 'SPEED',
    title: 'Move with intent.',
    desc: 'We eliminate administrative friction so studio founders and creative teams can operate at the speed of thought.'
  },
  {
    num: '03',
    category: 'CLARITY',
    title: 'Radical simplicity.',
    desc: 'No bloated menus or fragmented tools. We replace dozens of disconnected apps with one unified, calm workspace.'
  },
  {
    num: '04',
    category: 'EMPATHY',
    title: 'Built for creators.',
    desc: 'We design specifically for real-world studio sets, production houses, and the creators who run them every day.'
  },
  {
    num: '05',
    category: 'ITERATION',
    title: 'Grow 1% every day.',
    desc: 'Continuous refinement through listening closely to our community, fixing nuances quickly, and raising the standard.'
  },
  {
    num: '06',
    category: 'EXPERIENCE',
    title: 'Customer delight first.',
    desc: 'Our success is measured solely by the time, freedom, and joy our creators gain back for their actual creative work.'
  }
];

export function CoreValuesSection() {
  const containerRef = useRef<HTMLDivElement>(null);
  const trackRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    gsap.registerPlugin(ScrollTrigger);

    const ctx = gsap.context(() => {
      const track = trackRef.current;
      const container = containerRef.current;
      if (!track || !container) return;

      const getScrollAmount = () => {
        return track.scrollWidth - window.innerWidth + (window.innerWidth > 768 ? 96 : 32);
      };

      gsap.to(track, {
        x: () => -getScrollAmount(),
        ease: 'none',
        scrollTrigger: {
          trigger: container,
          pin: true,
          scrub: 0.6,
          start: 'top 12%',
          end: () => `+=${getScrollAmount()}`,
          invalidateOnRefresh: true,
          anticipatePin: 1,
        }
      });
    }, containerRef);

    return () => ctx.revert();
  }, []);

  return (
    <div ref={containerRef} className="w-full overflow-hidden">
      
      <div className="w-full space-y-10 sm:space-y-14">
        
        {/* Section Header (Strict Cora Design System Tokens with Signature Gradient) */}
        <div className="text-center space-y-3 sm:space-y-4 max-w-[760px] mx-auto px-4 sm:px-6">
          <div className="inline-flex items-center gap-2 px-3.5 py-1 bg-white/90 backdrop-blur-md rounded-full text-[11px] font-mono font-bold tracking-[0.16em] uppercase text-zinc-700 border border-zinc-200/90 mb-1 shadow-2xs">
            <span>DNA &amp; PRINCIPLES</span>
          </div>
          <div>
            <h2 className="font-display text-4xl xs:text-5xl sm:text-6xl lg:text-[72px] font-bold tracking-[-0.03em] leading-[1.08] bg-gradient-to-r from-zinc-950 via-zinc-700 to-zinc-400 bg-clip-text text-transparent inline-block">
              Our core values.
            </h2>
          </div>
          <p className="text-zinc-600 text-base sm:text-lg leading-relaxed max-w-[580px] mx-auto font-normal">
            The fundamental beliefs that guide how we build products, support creators, and grow together.
          </p>
        </div>

        {/* GSAP Full Viewport Edge-to-Edge Horizontal Track */}
        <div className="w-full">
          <div
            ref={trackRef}
            className="flex gap-6 sm:gap-10 pl-4 sm:pl-8 md:pl-16 lg:pl-24 pr-12 sm:pr-24 lg:pr-32 will-change-transform pb-2"
          >
            {VALUES.map((val, idx) => (
              <div
                key={idx}
                className="w-[80vw] max-w-[360px] sm:w-[390px] shrink-0 border-t-2 border-zinc-950 pt-6 space-y-3 select-none"
              >
                <div className="flex items-center justify-between">
                  <span className="text-xs font-mono font-bold text-zinc-400 uppercase tracking-wider">
                    {val.num} / {val.category}
                  </span>
                </div>

                <h3 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight">
                  {val.title}
                </h3>

                <p className="text-sm sm:text-base text-zinc-600 leading-relaxed font-normal">
                  {val.desc}
                </p>
              </div>
            ))}
          </div>
        </div>

      </div>

    </div>
  );
}
