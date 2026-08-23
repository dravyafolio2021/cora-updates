'use client';

import React, { useEffect, useRef } from 'react';
import Image from 'next/image';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import { Sparkles, ArrowRight, Layers, Cpu, HelpCircle, CheckCircle2 } from 'lucide-react';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

export function UnifiedHubSprawlSection() {
  const sectionRef = useRef<HTMLElement>(null);

  useEffect(() => {
    const ctx = gsap.context(() => {
      gsap.fromTo(
        '.hub-col',
        { y: 30, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.65,
          stagger: 0.12,
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
      id="unified-hub"
      ref={sectionRef}
      className="py-20 sm:py-28 bg-[#FFFFFF] relative z-10 overflow-hidden border-b border-zinc-100"
    >
      <div className="w-full max-w-[1280px] mx-auto px-4 sm:px-6">
        
        {/* ── 1. Section Header ── */}
        <div className="max-w-[860px] mx-auto text-center mb-12 sm:mb-16">
          <div className="inline-flex items-center gap-1.5 px-3.5 py-1 bg-zinc-100 rounded-full text-zinc-800 text-xs font-semibold uppercase tracking-wider mb-4 border border-zinc-200/80 shadow-2xs">
            <Sparkles className="w-3.5 h-3.5 text-zinc-950" />
            <span>UNIFIED AI CO-FOUNDER ARCHITECTURE</span>
          </div>

          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl lg:text-[48px] font-bold text-zinc-950 leading-[1.1] tracking-[-0.03em] mb-4">
            60% of work is lost in context <br className="hidden sm:inline" />
            &mdash; Cora unifies it all
          </h2>
          <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[640px] mx-auto">
            Work sprawl fragments your creative studio across 10+ tools. Cora acts as your central intelligence hub, connecting your files, client chats, and team workflows in one shared brain.
          </p>
        </div>

        {/* ── 2. Visual Centerpiece: The Unified Cora Hub (Matching User Upload 1:1) ── */}
        <div className="relative w-full max-w-[1140px] mx-auto mb-12 sm:mb-16">
          <div className="relative w-full aspect-[1024/512] max-h-[460px] rounded-3xl overflow-hidden bg-[#FAFAFB] border border-zinc-200/80 shadow-[0px_8px_30px_rgba(0,0,0,0.03)] p-4 sm:p-6 flex items-center justify-center">
            <Image
              src="/images/cora_unified_hub_sprawl.png"
              alt="Cora Central Intelligence Hub Unifying Work Sprawl"
              fill
              className="object-contain object-center p-2 sm:p-4"
              priority
            />
          </div>
        </div>

        {/* ── 3. Three Context Columns ── */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12 max-w-[1140px] mx-auto">
          
          {/* Column 1: Fragmented SaaS Sprawl */}
          <div className="hub-col space-y-2.5 text-left border-t border-zinc-100 pt-6 md:border-t-0 md:pt-0">
            <div className="w-8 h-0.5 bg-zinc-200 mb-4 hidden md:block" />
            <div className="flex items-center gap-2">
              <Layers className="w-4 h-4 text-zinc-500" />
              <h3 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
                Fragmented SaaS Sprawl
              </h3>
            </div>
            <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed">
              Digital fatigue from juggling Slack, Zoom, Google Drive, Figma, and Teams reduces team performance by up to <strong className="text-zinc-950 font-bold">32%</strong>.
            </p>
          </div>

          {/* Column 2: Central Cora Intelligence */}
          <div className="hub-col space-y-2.5 text-left border-t border-zinc-100 pt-6 md:border-t-0 md:pt-0">
            <div className="w-8 h-0.5 bg-indigo-600 mb-4 hidden md:block" />
            <div className="flex items-center gap-2">
              <Cpu className="w-4 h-4 text-indigo-600" />
              <h3 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
                Central Cora Intelligence
              </h3>
            </div>
            <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed">
              Frontier AI models routed dynamically and grounded in your studio&apos;s live rate cards, client history, 18% GST rules, and legal NDAs.
            </p>
          </div>

          {/* Column 3: Zero Question Chaos */}
          <div className="hub-col space-y-2.5 text-left border-t border-zinc-100 pt-6 md:border-t-0 md:pt-0">
            <div className="w-8 h-0.5 bg-emerald-600 mb-4 hidden md:block" />
            <div className="flex items-center gap-2">
              <CheckCircle2 className="w-4 h-4 text-emerald-600" />
              <h3 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
                Zero Question Chaos
              </h3>
            </div>
            <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed">
              Save <strong className="text-zinc-950 font-bold">2.5 hours daily</strong>. No more frantic searches for briefs, unpaid UPI invoices, or deliverable status across disconnected tools.
            </p>
          </div>

        </div>

      </div>
    </section>
  );
}
