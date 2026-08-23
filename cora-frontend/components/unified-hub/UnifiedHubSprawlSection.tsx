'use client';

import React, { useEffect, useRef } from 'react';
import Image from 'next/image';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import { Sparkles, Layers, Cpu, CheckCircle2 } from 'lucide-react';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

export function UnifiedHubSprawlSection() {
  const sectionRef = useRef<HTMLElement>(null);

  useEffect(() => {
    const ctx = gsap.context(() => {
      gsap.fromTo(
        '.hub-col',
        { y: 25, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.6,
          stagger: 0.1,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: sectionRef.current,
            start: 'top 78%',
          },
        }
      );
    }, sectionRef);

    return () => ctx.revert();
  }, []);

  return (
    <section
      id="unified-hub"
      ref={sectionRef}
      className="py-12 sm:py-16 bg-[#FFFFFF] relative z-10 overflow-hidden border-b border-zinc-100"
    >
      <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        
        {/* ── 1. Section Header (Compact) ── */}
        <div className="max-w-[800px] mx-auto text-center mb-6 sm:mb-8">
          <div className="inline-flex items-center gap-1.5 px-3 py-0.5 bg-zinc-100 rounded-full text-zinc-800 text-[11px] font-semibold uppercase tracking-wider mb-2.5 border border-zinc-200/80 shadow-2xs">
            <Sparkles className="w-3 h-3 text-zinc-950" />
            <span>UNIFIED AI CO-FOUNDER ARCHITECTURE</span>
          </div>

          <h2 className="font-display text-2xl xs:text-3xl sm:text-4xl lg:text-[42px] font-bold text-zinc-950 leading-[1.12] tracking-[-0.03em] mb-2.5">
            60% of work is lost in context &mdash; Cora unifies it all
          </h2>
          <p className="text-zinc-600 text-sm sm:text-base font-normal leading-relaxed max-w-[580px] mx-auto">
            Work sprawl fragments your creative studio across 10+ tools. Cora acts as your central intelligence hub, connecting files, client chats, and team workflows in one shared brain.
          </p>
        </div>

        {/* ── 2. Visual Centerpiece: Borderless, Tight & Clean ── */}
        <div className="relative w-full max-w-[1080px] mx-auto mb-6 sm:mb-8">
          <div className="relative w-full aspect-[1024/460] max-h-[380px]">
            <Image
              src="/images/cora_unified_hub_sprawl.png"
              alt="Cora Central Intelligence Hub Unifying Work Sprawl"
              fill
              className="object-contain object-center"
              priority
            />
          </div>
        </div>

        {/* ── 3. Three Compact Context Columns ── */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-10 max-w-[1080px] mx-auto">
          
          {/* Column 1: Fragmented SaaS Sprawl */}
          <div className="hub-col space-y-2 text-left border-t border-zinc-100 pt-4 md:border-t-0 md:pt-0">
            <div className="w-6 h-0.5 bg-zinc-300 mb-2.5 hidden md:block" />
            <div className="flex items-center gap-1.5">
              <Layers className="w-4 h-4 text-zinc-500" />
              <h3 className="font-display text-lg sm:text-xl font-bold text-zinc-950 tracking-tight">
                Fragmented SaaS Sprawl
              </h3>
            </div>
            <p className="text-zinc-600 text-xs sm:text-[13px] leading-relaxed">
              Digital fatigue from juggling Slack, Zoom, Google Drive, Figma, and Teams reduces team performance by up to <strong className="text-zinc-950 font-bold">32%</strong>.
            </p>
          </div>

          {/* Column 2: Central Cora Intelligence */}
          <div className="hub-col space-y-2 text-left border-t border-zinc-100 pt-4 md:border-t-0 md:pt-0">
            <div className="w-6 h-0.5 bg-indigo-600 mb-2.5 hidden md:block" />
            <div className="flex items-center gap-1.5">
              <Cpu className="w-4 h-4 text-indigo-600" />
              <h3 className="font-display text-lg sm:text-xl font-bold text-zinc-950 tracking-tight">
                Central Cora Intelligence
              </h3>
            </div>
            <p className="text-zinc-600 text-xs sm:text-[13px] leading-relaxed">
              Frontier AI models routed dynamically and grounded in your studio&apos;s live rate cards, client history, 18% GST rules, and legal NDAs.
            </p>
          </div>

          {/* Column 3: Zero Question Chaos */}
          <div className="hub-col space-y-2 text-left border-t border-zinc-100 pt-4 md:border-t-0 md:pt-0">
            <div className="w-6 h-0.5 bg-emerald-600 mb-2.5 hidden md:block" />
            <div className="flex items-center gap-1.5">
              <CheckCircle2 className="w-4 h-4 text-emerald-600" />
              <h3 className="font-display text-lg sm:text-xl font-bold text-zinc-950 tracking-tight">
                Zero Question Chaos
              </h3>
            </div>
            <p className="text-zinc-600 text-xs sm:text-[13px] leading-relaxed">
              Save <strong className="text-zinc-950 font-bold">2.5 hours daily</strong>. No more frantic searches for briefs, unpaid UPI invoices, or deliverable status across disconnected tools.
            </p>
          </div>

        </div>

      </div>
    </section>
  );
}
