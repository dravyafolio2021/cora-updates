'use client';

import React, { useEffect, useRef } from 'react';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import { Users, Receipt, ShieldCheck, Sparkles, Clock, ArrowUpRight } from 'lucide-react';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

const STATS_CARDS = [
  {
    id: 'studios',
    title: 'Active studios',
    value: '1,200+',
    desc: 'Agencies and creators managing production with Cora.',
    theme: 'light',
    icon: Users,
    colSpan: 'sm:col-span-1',
  },
  {
    id: 'invoices',
    title: 'Invoices settled',
    value: '₹4.8Cr+',
    desc: 'In commercial contracts and 18% GST invoices processed.',
    theme: 'dark',
    icon: Receipt,
    colSpan: 'sm:col-span-1',
  },
  {
    id: 'ai_signals',
    title: 'AI operations executed',
    value: '1.4M+',
    desc: 'Proposals, contracts, and call-sheets delivered autonomously.',
    theme: 'dark',
    icon: Sparkles,
    colSpan: 'sm:col-span-1',
  },
  {
    id: 'reclaimed_time',
    title: 'Reclaimed founder time',
    value: '20+ Hrs',
    desc: 'Saved per creator every single week on administrative chaos.',
    theme: 'accent',
    icon: Clock,
    colSpan: 'sm:col-span-1',
  },
  {
    id: 'uptime',
    title: 'Platform uptime',
    value: '99.9%',
    desc: 'Reliable 24/7 access to your production pipeline.',
    theme: 'light',
    icon: ShieldCheck,
    colSpan: 'sm:col-span-1',
  },
];

export function PlatformStatsSection() {
  const sectionRef = useRef<HTMLElement>(null);
  const cardsRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const ctx = gsap.context(() => {
      // Entrance reveal for headers
      gsap.fromTo(
        '.stats-header-anim',
        { y: 30, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.8,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: sectionRef.current,
            start: 'top 80%',
          },
        }
      );

      // Staggered card entrance with slight floating effect
      gsap.fromTo(
        '.stat-card-item',
        { y: 40, opacity: 0, scale: 0.96 },
        {
          y: 0,
          opacity: 1,
          scale: 1,
          duration: 0.8,
          stagger: 0.12,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: cardsRef.current,
            start: 'top 82%',
          },
        }
      );

      // Subtle ambient hover / float on cards
      gsap.utils.toArray<HTMLElement>('.stat-card-item').forEach((card, i) => {
        gsap.to(card, {
          y: (i % 2 === 0 ? '-=5' : '+=5'),
          duration: 3 + i * 0.4,
          repeat: -1,
          yoyo: true,
          ease: 'sine.inOut',
          delay: i * 0.2,
        });
      });
    }, sectionRef);

    return () => ctx.revert();
  }, []);

  return (
    <section
      ref={sectionRef}
      id="stats"
      className="py-20 sm:py-28 relative z-10 bg-[#FAFAFA] border-b border-zinc-200/70 overflow-hidden"
    >
      <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        
        {/* ── Section Header ── */}
        <div className="stats-header-anim text-center max-w-[780px] mx-auto mb-16 sm:mb-20">
          <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-white rounded-xl border border-zinc-200/90 text-xs font-semibold text-zinc-800 mb-3.5 shadow-2xs">
            <span className="w-2 h-2 rounded-full bg-emerald-500" />
            <span>Platform Stats</span>
          </div>
          
          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl font-bold text-zinc-950 leading-[1.12] tracking-[-0.03em]">
            Powering smarter creative operations
          </h2>
          
          <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[620px] mx-auto mt-3">
            Real-time dispatch, automated GST math, and enterprise infrastructure working together.
          </p>
        </div>

        {/* ── 5 Stat Cards Layout ── */}
        <div
          ref={cardsRef}
          className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-5 sm:gap-6 items-stretch"
        >
          {STATS_CARDS.map((card) => {
            const isDark = card.theme === 'dark';
            const isAccent = card.theme === 'accent';

            return (
              <div
                key={card.id}
                className={`stat-card-item rounded-[28px] p-6 sm:p-7 flex flex-col justify-between min-h-[260px] sm:min-h-[290px] transition-all duration-300 hover:-translate-y-1.5 ${
                  isDark
                    ? 'bg-[#100F12] border border-zinc-800 text-white shadow-[0px_16px_40px_rgba(0,0,0,0.14)]'
                    : isAccent
                    ? 'bg-zinc-900 border border-zinc-800 text-white shadow-[0px_16px_40px_rgba(0,0,0,0.12)]'
                    : 'bg-white border border-zinc-200/90 text-zinc-950 shadow-[0px_10px_30px_rgba(0,0,0,0.04)]'
                }`}
              >
                {/* Card Top: Title & Icon Pill */}
                <div className="flex items-center justify-between gap-3">
                  <span
                    className={`text-xs font-semibold tracking-wide ${
                      isDark || isAccent ? 'text-zinc-400' : 'text-zinc-600'
                    }`}
                  >
                    {card.title}
                  </span>

                  <div
                    className={`w-9 h-9 rounded-full flex items-center justify-center shadow-2xs ${
                      isDark
                        ? 'bg-zinc-800/90 text-white border border-zinc-700'
                        : isAccent
                        ? 'bg-zinc-800 text-emerald-400 border border-zinc-700'
                        : 'bg-zinc-100 text-zinc-900 border border-zinc-200'
                    }`}
                  >
                    {React.createElement(card.icon, { className: 'w-4 h-4' })}
                  </div>
                </div>

                {/* Card Bottom: Metric & Description */}
                <div className="space-y-1.5 mt-8">
                  <div
                    className={`font-display text-3xl sm:text-[38px] font-extrabold tracking-tight leading-none ${
                      isDark || isAccent ? 'text-white' : 'text-zinc-950'
                    }`}
                  >
                    {card.value}
                  </div>

                  <p
                    className={`text-xs sm:text-[13px] font-normal leading-snug pt-1 ${
                      isDark || isAccent ? 'text-zinc-400' : 'text-zinc-600'
                    }`}
                  >
                    {card.desc}
                  </p>
                </div>
              </div>
            );
          })}
        </div>

      </div>
    </section>
  );
}
