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
    title: 'Eliminate Scope Creep & Chaos',
    story: 'We unified messy client WhatsApp briefs, Figma handoff threads, and custom dev scopes into one structured agency command center.'
  },
  {
    phase: '02',
    year: '2025',
    title: 'Automate Retainers & Contracts',
    story: 'We built automated 18% GST invoicing, sprint milestone billing, and legally binding e-signatures to protect agency IP and collect retainers on time.'
  },
  {
    phase: '03',
    year: '2026',
    title: 'Deploy Autonomous AI Co-Founders',
    story: 'We deployed frontier AI agents that qualify high-ticket inbound leads 24/7, draft technical estimates, and close project scopes on autopilot.'
  },
  {
    phase: '04',
    year: 'BEYOND',
    title: 'Scale High-Margin Modern Agencies',
    story: 'We empower performance marketing agencies, UI/UX studios, and dev shops worldwide to 10x output and scale revenue without adding admin overhead.'
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
      
      {/* Section Header (Strict Cora Design System Tokens) */}
      <div className="text-center space-y-3 sm:space-y-4 max-w-[700px] mx-auto px-4">
        <div className="inline-flex items-center gap-2 px-3.5 py-1 bg-white/90 backdrop-blur-md rounded-full text-[11px] font-mono font-bold tracking-[0.16em] uppercase text-zinc-700 border border-zinc-200/90 mb-1 shadow-2xs">
          <span>OUR EVOLUTION</span>
        </div>
        <div>
          <h2 className="font-display text-4xl xs:text-5xl sm:text-6xl lg:text-[72px] font-bold tracking-[-0.03em] leading-[1.08] bg-gradient-to-r from-zinc-950 via-zinc-700 to-zinc-400 bg-clip-text text-transparent inline-block">
            Our journey.
          </h2>
        </div>
        <p className="text-zinc-600 text-base sm:text-lg leading-relaxed max-w-[580px] mx-auto font-normal">
          We replace fragmented agency tool stacks with an autonomous AI co-founder—reclaiming 52 billable days every year.
        </p>
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
