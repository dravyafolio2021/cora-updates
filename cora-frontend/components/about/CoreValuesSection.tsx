'use client';

import React from 'react';
import { 
  Sparkles, 
  Zap, 
  CheckCircle2, 
  Heart, 
  TrendingUp, 
  Award,
  ArrowUpRight
} from 'lucide-react';

const VALUES = [
  {
    num: '01',
    category: 'CRAFT',
    tag: 'Design Polish',
    icon: Sparkles,
    title: 'Be in the details.',
    desc: 'Obsessing over typography and micro-interactions so every client touchpoint feels world-class.'
  },
  {
    num: '02',
    category: 'SPEED',
    tag: 'Zero Friction',
    icon: Zap,
    title: 'Move with intent.',
    desc: 'Eliminating administrative friction so client-facing teams operate at the speed of thought.'
  },
  {
    num: '03',
    category: 'CLARITY',
    tag: 'Uncluttered UI',
    icon: CheckCircle2,
    title: 'Radical simplicity.',
    desc: 'Replacing dozens of disconnected tools with one calm, high-margin agency command center.'
  },
  {
    num: '04',
    category: 'EMPATHY',
    tag: 'Agency First',
    icon: Heart,
    title: 'Built for agencies.',
    desc: 'Designed specifically for discovery intake, retainer billing, and fast-paced client delivery.'
  },
  {
    num: '05',
    category: 'ITERATION',
    tag: 'Daily Velocity',
    icon: TrendingUp,
    title: 'Grow 1% every day.',
    desc: 'Listening closely to top operators, shipping improvements fast, and setting a higher standard.'
  },
  {
    num: '06',
    category: 'EXPERIENCE',
    tag: 'Proven ROI',
    icon: Award,
    title: 'Client delight first.',
    desc: 'Measured by the billable hours and creative headspace agency founders win back.'
  }
];

export function CoreValuesSection() {
  return (
    <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
      
      {/* ── Section Header ── */}
      <div className="text-center space-y-2.5 sm:space-y-3.5 max-w-[760px] mx-auto mb-6 sm:mb-12">
        <div className="inline-flex items-center gap-2 px-3 py-0.5 bg-white/95 backdrop-blur-md rounded-full text-[10.5px] font-mono font-bold tracking-[0.16em] uppercase text-zinc-700 border border-zinc-200/90 shadow-2xs">
          <span>DNA &amp; PRINCIPLES</span>
        </div>
        
        <div>
          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl lg:text-[62px] font-bold tracking-[-0.03em] leading-[1.08] bg-gradient-to-r from-zinc-950 via-zinc-700 to-zinc-400 bg-clip-text text-transparent inline-block">
            Our core values.
          </h2>
        </div>
        
        <p className="text-zinc-600 text-xs sm:text-sm lg:text-base leading-relaxed max-w-[560px] mx-auto font-normal">
          The fundamental beliefs that guide how we build products and support modern agencies.
        </p>
      </div>

      {/* ── 6 High-Polish Bento Value Cards (3 Cols Desktop, 2 Cols Tablet, 1 Col Mobile) ── */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-6">
        {VALUES.map((val, idx) => {
          const Icon = val.icon;

          return (
            <div
              key={idx}
              className="group relative p-4 xs:p-5 sm:p-7 rounded-xl xs:rounded-2xl sm:rounded-3xl bg-white border border-zinc-200/80 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.03)] hover:shadow-[0_12px_30px_-6px_rgba(0,0,0,0.08)] hover:border-zinc-300 hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between overflow-hidden"
            >
              {/* Subtle top accent gradient line on hover */}
              <div className="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-transparent via-zinc-900 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" />

              {/* Card Top: Number, Category & Icon Tile */}
              <div className="flex items-center justify-between mb-3.5 sm:mb-5">
                <div className="inline-flex items-center gap-1.5 sm:gap-2 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full bg-zinc-100/80 border border-zinc-200/70 text-[10px] sm:text-[11px] font-mono font-semibold text-zinc-600">
                  <span className="text-zinc-900 font-bold">{val.num}</span>
                  <span className="text-zinc-400">/</span>
                  <span className="uppercase tracking-wider text-[9px] sm:text-[10px]">{val.category}</span>
                </div>

                <div className="w-8 h-8 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-zinc-50 border border-zinc-200/80 flex items-center justify-center text-zinc-800 group-hover:bg-zinc-950 group-hover:text-white group-hover:border-zinc-950 transition-all duration-300 shadow-2xs">
                  <Icon className="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-[2]" />
                </div>
              </div>

              {/* Card Middle: Title */}
              <div className="mb-2 sm:mb-3">
                <h3 className="font-display text-lg sm:text-2xl font-bold text-zinc-950 tracking-tight group-hover:text-black transition-colors">
                  {val.title}
                </h3>
              </div>

              {/* Card Bottom: Description & Tag */}
              <div className="space-y-3 sm:space-y-4 pt-1">
                <p className="text-xs sm:text-sm text-zinc-600 leading-relaxed font-normal">
                  {val.desc}
                </p>

                <div className="flex items-center justify-between pt-2.5 sm:pt-3 border-t border-zinc-100">
                  <span className="text-[9.5px] sm:text-[10.5px] font-mono font-medium text-zinc-400 uppercase tracking-wider">
                    {val.tag}
                  </span>
                  <ArrowUpRight className="w-3.5 h-3.5 text-zinc-300 group-hover:text-zinc-900 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all" />
                </div>
              </div>

            </div>
          );
        })}
      </div>

    </div>
  );
}
