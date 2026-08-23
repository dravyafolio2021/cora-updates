'use client';

import React, { useEffect, useRef } from 'react';
import Image from 'next/image';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

export function FounderValueShowcase() {
  const sectionRef = useRef<HTMLElement>(null);

  useEffect(() => {
    const ctx = gsap.context(() => {
      gsap.fromTo(
        '.sprawl-col',
        { y: 30, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.7,
          stagger: 0.15,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: sectionRef.current,
            start: 'top 75%',
          },
        }
      );
    }, sectionRef);

    return () => ctx.revert();
  }, []);

  return (
    <section
      id="context-sprawl"
      ref={sectionRef}
      className="py-20 sm:py-28 bg-white relative z-10 overflow-hidden border-b border-zinc-100"
    >
      <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        
        {/* ── 1. Main Display Headline & Subtitle ── */}
        <div className="max-w-[920px] mx-auto text-center mb-12 sm:mb-16">
          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl lg:text-[46px] font-bold text-zinc-950 leading-[1.12] tracking-[-0.03em] mb-4">
            60% of work is lost in context <br className="hidden sm:inline" />
            &mdash; and AI is lost without it
          </h2>
          <p className="text-zinc-500 text-base sm:text-lg font-normal leading-relaxed max-w-[620px] mx-auto">
            Work Sprawl is killing context and destroying productivity for modern service businesses.
          </p>
        </div>

        {/* ── 2. Tangled Ribbon Illustration (Matching Reference 1:1) ── */}
        <div className="relative w-full max-w-[1100px] mx-auto mb-8 sm:mb-12">
          <div className="relative w-full aspect-[1024/292] max-h-[280px]">
            <Image
              src="/images/tangled_context_sprawl.png"
              alt="Work Sprawl and Tangled Context Switching"
              fill
              className="object-contain object-center"
              priority
            />
          </div>
        </div>

        {/* ── 3. Three Context Columns with Guide Lines ── */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12 relative max-w-[1100px] mx-auto">
          
          {/* Column 1: Context Switching */}
          <div className="sprawl-col space-y-2.5 text-left border-t border-zinc-100 pt-6 md:border-t-0 md:pt-0">
            <div className="w-8 h-0.5 bg-zinc-200 mb-4 hidden md:block" />
            <h3 className="font-display text-2xl font-bold text-zinc-950 tracking-tight">
              Context Switching
            </h3>
            <p className="text-zinc-600 text-sm sm:text-[15px] leading-relaxed">
              Digital fatigue from juggling 10+ disconnected tabs reduces employee and studio performance by up to{' '}
              <strong className="text-zinc-950 font-bold">32%</strong>.
            </p>
          </div>

          {/* Column 2: Context Missing */}
          <div className="sprawl-col space-y-2.5 text-left border-t border-zinc-100 pt-6 md:border-t-0 md:pt-0">
            <div className="w-8 h-0.5 bg-zinc-200 mb-4 hidden md:block" />
            <h3 className="font-display text-2xl font-bold text-zinc-950 tracking-tight">
              Context Missing
            </h3>
            <p className="text-zinc-600 text-sm sm:text-[15px] leading-relaxed">
              <strong className="text-zinc-950 font-bold">96% of companies fail</strong> in AI value &amp; adoption because standalone chatbots lack live memory of your rate cards, client history, and invoices.
            </p>
          </div>

          {/* Column 3: Context Stitching */}
          <div className="sprawl-col space-y-2.5 text-left border-t border-zinc-100 pt-6 md:border-t-0 md:pt-0">
            <div className="w-8 h-0.5 bg-zinc-200 mb-4 hidden md:block" />
            <h3 className="font-display text-2xl font-bold text-zinc-950 tracking-tight">
              Context Stitching
            </h3>
            <p className="text-zinc-600 text-sm sm:text-[15px] leading-relaxed">
              <strong className="text-zinc-950 font-bold">2.5 hours daily</strong> wasted searching WhatsApp briefs, verifying GST splits, and stitching agreements across fragmented tools.
            </p>
          </div>

        </div>

      </div>
    </section>
  );
}
