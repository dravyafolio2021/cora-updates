'use client';

import React, { useEffect, useRef, useState } from 'react';

interface Milestone {
  phase: string;
  year: string;
  title: string;
  story: string;
}

const MILESTONES: Milestone[] = [
  {
    phase: '01',
    year: '2024',
    title: 'The Genesis',
    story: 'Unifying chaotic WhatsApp chats, rate cards, and manual proposals into one calm operating system.'
  },
  {
    phase: '02',
    year: '2025',
    title: 'Autonomous Rails',
    story: 'Automated 18% GST calculation, instant UPI soundbox receipts, and legally binding digital signatures.'
  },
  {
    phase: '03',
    year: '2026',
    title: 'The AI Co-Founder',
    story: 'Frontier AI agents handling 24/7 client discovery, budget vetting, and autonomous shoot bookings.'
  },
  {
    phase: '04',
    year: 'BEYOND',
    title: 'Universal Autonomy',
    story: 'Empowering independent studios and production houses globally to run high-leverage creative empires.'
  }
];

export function ScrollDrivenTimeline() {
  const containerRef = useRef<HTMLDivElement>(null);
  const [scrollProgress, setScrollProgress] = useState(0);
  const [activeMilestone, setActiveMilestone] = useState(0);

  useEffect(() => {
    const handleScroll = () => {
      if (!containerRef.current) return;
      const rect = containerRef.current.getBoundingClientRect();
      const windowHeight = window.innerHeight;
      
      const totalHeight = rect.height;
      const currentScroll = windowHeight * 0.5 - rect.top;
      const progress = Math.min(Math.max(currentScroll / totalHeight, 0), 1);
      setScrollProgress(progress);

      const milestoneIndex = Math.min(
        Math.floor(progress * MILESTONES.length),
        MILESTONES.length - 1
      );
      setActiveMilestone(Math.max(0, milestoneIndex));
    };

    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  return (
    <div ref={containerRef} className="w-full space-y-10 sm:space-y-14">
      
      {/* Section Header */}
      <div className="text-center space-y-2.5">
        <span className="text-[11px] sm:text-xs font-mono font-semibold uppercase tracking-widest text-zinc-500 block">
          OUR EVOLUTION
        </span>
        <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl font-bold text-zinc-950 tracking-tight">
          Our journey.
        </h2>
      </div>

      {/* Scroll-Driven Connected Timeline */}
      <div className="relative max-w-[620px] mx-auto pl-6 sm:pl-8 space-y-10 sm:space-y-12">
        
        {/* Background Track Line */}
        <div className="absolute left-[7px] sm:left-[9px] top-2 bottom-4 w-0.5 bg-zinc-200 pointer-events-none" />

        {/* Dynamic Scroll-Fill Beam */}
        <div 
          className="absolute left-[7px] sm:left-[9px] top-2 w-0.5 bg-zinc-950 pointer-events-none transition-all duration-150"
          style={{ height: `${Math.min(scrollProgress * 100, 100)}%` }}
        />

        {MILESTONES.map((item, idx) => {
          const isPassed = scrollProgress >= (idx / MILESTONES.length);

          return (
            <div 
              key={idx} 
              className={`relative transition-all duration-300 ${
                isPassed ? 'opacity-100' : 'opacity-40'
              }`}
            >
              {/* Timeline Indicator Dot */}
              <div 
                className={`absolute -left-[24px] sm:-left-[32px] top-1.5 w-3 h-3 sm:w-3.5 sm:h-3.5 rounded-full border-2 transition-all duration-300 ${
                  isPassed
                    ? 'bg-zinc-950 border-zinc-950 ring-4 ring-zinc-100 scale-110'
                    : 'bg-white border-zinc-300 ring-2 ring-transparent'
                }`}
              />

              {/* Milestone Content */}
              <div className="space-y-1.5">
                
                {/* Phase & Year */}
                <div className="flex items-center gap-2 text-xs font-mono font-bold text-zinc-400 uppercase tracking-wider">
                  <span>{item.phase}</span>
                  <span>/</span>
                  <span className="text-zinc-950">{item.year}</span>
                </div>

                {/* Main Headline */}
                <h3 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
                  {item.title}
                </h3>

                {/* Concise 1-Line Story */}
                <p className="text-sm text-zinc-600 leading-relaxed font-normal">
                  {item.story}
                </p>

              </div>
            </div>
          );
        })}

      </div>

    </div>
  );
}
