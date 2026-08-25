'use client';

import React, { useEffect, useRef, useState } from 'react';

interface Milestone {
  phase: string;
  year: string;
  title: string;
  subtitle: string;
  story: string;
  highlight: string;
}

const MILESTONES: Milestone[] = [
  {
    phase: 'PHASE 01',
    year: '2024',
    title: 'Solving Studio Fragmentation',
    subtitle: 'From chaos to unified clarity',
    story: 'Creative founders were losing up to 20 hours every week juggling fragmented tools—WhatsApp for client negotiations, Excel for rate calculations, PDFs for proposals, and manual invoices. We set out to replace the chaos with a single, calm operating system.',
    highlight: 'Unified Briefs & Bookings'
  },
  {
    phase: 'PHASE 02',
    year: '2024 – 2025',
    title: 'Native Commercial Infrastructure',
    subtitle: 'Automating the operational core',
    story: 'We built India-first financial and legal rails natively into the studio engine—instant 18% GST rate-card computation, automated dynamic proposal generators, UPI payment webhooks, and SHA-256 digital signature contracts.',
    highlight: 'Zero Manual Billing'
  },
  {
    phase: 'PHASE 03',
    year: '2025 – 2026',
    title: 'The AI Co-Founder Emerges',
    subtitle: 'Software that thinks and acts with you',
    story: 'Cora evolved from a management tool into an autonomous AI co-founder. Bridging 24/7 customer-facing concierge agents with admin workspaces—handling client discovery, vetting budgets, and scheduling production call sheets automatically.',
    highlight: 'Autonomous Studio Agent'
  },
  {
    phase: 'PHASE 04',
    year: '2026 & Beyond',
    title: 'Universal Creative Autonomy',
    subtitle: 'Empowering the next generation of creative empires',
    story: 'Expanding multi-tenant infrastructure across photography studios, production sets, and agencies globally. Giving ambitious creators the leverage of a full enterprise operations team with effortless simplicity.',
    highlight: 'Global Studio OS'
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
      
      // Calculate progress through container
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
    <div ref={containerRef} className="w-full space-y-12 sm:space-y-16">
      
      {/* Section Header */}
      <div className="text-center space-y-3">
        <span className="text-[11px] sm:text-xs font-mono font-semibold uppercase tracking-widest text-zinc-500 block">
          OUR EVOLUTION
        </span>
        <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl font-bold text-zinc-950 tracking-tight">
          Our journey.
        </h2>
        <p className="text-sm sm:text-base text-zinc-600 max-w-[540px] mx-auto font-normal">
          How we evolved from solving fragmented studio chaos to building the autonomous AI co-founder for modern creators.
        </p>
      </div>

      {/* 
        Scroll-Driven Connected Timeline:
        A vertical line that fills dynamically as you scroll down the page.
      */}
      <div className="relative max-w-[760px] mx-auto pl-6 sm:pl-10 space-y-12 sm:space-y-16">
        
        {/* Background Track Line */}
        <div className="absolute left-[7px] sm:left-[11px] top-2 bottom-4 w-0.5 bg-zinc-200 pointer-events-none" />

        {/* Dynamic Scroll-Fill Beam */}
        <div 
          className="absolute left-[7px] sm:left-[11px] top-2 w-0.5 bg-zinc-950 pointer-events-none transition-all duration-150"
          style={{ height: `${Math.min(scrollProgress * 100, 100)}%` }}
        />

        {MILESTONES.map((item, idx) => {
          const isPassed = scrollProgress >= (idx / MILESTONES.length);
          const isCurrent = activeMilestone === idx;

          return (
            <div 
              key={idx} 
              className={`relative transition-all duration-300 ${
                isPassed ? 'opacity-100' : 'opacity-40'
              }`}
            >
              {/* Timeline Indicator Dot */}
              <div 
                className={`absolute -left-[24px] sm:-left-[40px] top-1.5 w-3.5 h-3.5 sm:w-4 sm:h-4 rounded-full border-2 transition-all duration-300 ${
                  isPassed
                    ? 'bg-zinc-950 border-zinc-950 ring-4 ring-zinc-100 scale-110'
                    : 'bg-white border-zinc-300 ring-2 ring-transparent'
                }`}
              />

              {/* Milestone Content */}
              <div className="space-y-2.5">
                
                {/* Phase & Year Header */}
                <div className="flex items-center gap-3">
                  <span className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-950">
                    {item.phase}
                  </span>
                  <span className="text-zinc-300">•</span>
                  <span className="text-[11px] font-mono text-zinc-500 font-medium">
                    {item.year}
                  </span>
                  <span className="text-zinc-300">•</span>
                  <span className="text-[10px] font-mono px-2 py-0.5 rounded-full bg-zinc-100 text-zinc-700 font-semibold uppercase tracking-tight">
                    {item.highlight}
                  </span>
                </div>

                {/* Main Headline */}
                <h3 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
                  {item.title}
                </h3>

                {/* Subtitle / Context */}
                <p className="text-xs font-mono text-zinc-500 uppercase tracking-wide">
                  {item.subtitle}
                </p>

                {/* Story Paragraph */}
                <p className="text-sm sm:text-base text-zinc-600 leading-relaxed font-normal pt-1">
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
