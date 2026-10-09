'use client';

import React, { useEffect, useRef } from 'react';
import Image from 'next/image';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import { Sparkles, Inbox, FolderKanban, CreditCard } from 'lucide-react';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

export function UnifiedHubSprawlSection() {
  const sectionRef = useRef<HTMLElement>(null);

  useEffect(() => {
    const ctx = gsap.context(() => {
      gsap.fromTo(
        '.hub-col',
        { y: 20, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.5,
          stagger: 0.08,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: sectionRef.current,
            start: 'top 85%',
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
      className="py-16 sm:py-[100px] bg-[#FFFFFF] relative z-10 overflow-hidden"
    >
      <div className="w-full max-w-[1200px] mx-auto px-4 sm:px-6">
        
        {/* ── 1. Compact Header ── */}
        <div className="max-w-[760px] mx-auto text-center mb-6 sm:mb-8 flex flex-col items-center">
          <div className="inline-flex items-center gap-1.5 px-3 py-1 bg-zinc-100 rounded-full text-zinc-900 text-[11px] font-semibold uppercase tracking-wider mb-3 border border-zinc-200/80 shadow-2xs">
            <Sparkles className="w-3.5 h-3.5 text-zinc-900 stroke-[1.8]" />
            <span>Unified Platform</span>
          </div>

          <h2 className="w-full font-display text-2xl xs:text-3xl sm:text-4xl lg:text-[40px] font-bold leading-[1.18] tracking-[-0.03em] text-zinc-950 mb-2.5">
            Run your business in one place.
          </h2>
          <p className="text-zinc-600 text-xs sm:text-sm font-normal leading-relaxed max-w-[580px] mx-auto">
            Keep your website, clients, work, and payments together.
          </p>
        </div>

        {/* ── 2. Visual Centerpiece: Ultra-Tight Spacing ── */}
        <div className="relative w-full max-w-[960px] mx-auto mb-6 sm:mb-8">
          <div className="relative w-full aspect-[1024/450] max-h-[340px]">
            <Image
              src="/images/cora_unified_hub_sprawl.png"
              alt="Cora Central Intelligence Hub Unifying Work Sprawl"
              fill
              className="object-contain object-center"
              priority
            />
          </div>
        </div>

        {/* ── 3. Three Compact Context Columns (Mobile-First Optimized) ── */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 lg:gap-10 max-w-[960px] mx-auto text-center">
          
          {/* Column 1: Get enquiries */}
          <div className="hub-col space-y-1.5 flex flex-col items-center text-center pt-2 md:pt-0">
            <div className="flex items-center justify-center gap-2">
              <Inbox className="w-4 h-4 text-zinc-900 stroke-[1.8] shrink-0" />
              <h3 className="font-display text-base sm:text-lg font-bold text-zinc-950 tracking-tight">
                Get enquiries
              </h3>
            </div>
            <p className="text-zinc-600 text-xs sm:text-[13px] leading-relaxed text-center max-w-[280px]">
              Collect requests and follow up with clients.
            </p>
          </div>

          {/* Column 2: Manage client work */}
          <div className="hub-col space-y-1.5 flex flex-col items-center text-center pt-2 md:pt-0">
            <div className="flex items-center justify-center gap-2">
              <FolderKanban className="w-4 h-4 text-zinc-900 stroke-[1.8] shrink-0" />
              <h3 className="font-display text-base sm:text-lg font-bold text-zinc-950 tracking-tight">
                Manage client work
              </h3>
            </div>
            <p className="text-zinc-600 text-xs sm:text-[13px] leading-relaxed text-center max-w-[280px]">
              Keep files, feedback, and updates together.
            </p>
          </div>

          {/* Column 3: Track payments */}
          <div className="hub-col space-y-1.5 flex flex-col items-center text-center pt-2 md:pt-0">
            <div className="flex items-center justify-center gap-2">
              <CreditCard className="w-4 h-4 text-zinc-900 stroke-[1.8] shrink-0" />
              <h3 className="font-display text-base sm:text-lg font-bold text-zinc-950 tracking-tight">
                Track payments
              </h3>
            </div>
            <p className="text-zinc-600 text-xs sm:text-[13px] leading-relaxed text-center max-w-[280px]">
              Send invoices and see who has paid.
            </p>
          </div>

        </div>

      </div>
    </section>
  );
}
