'use client';

import React, { useState } from 'react';
import { 
  Sparkles, 
  FileCheck, 
  Bot, 
  TrendingUp,
  Check
} from 'lucide-react';

interface Milestone {
  num: string;
  year: string;
  badge: string;
  icon: React.ElementType;
  title: string;
  story: string;
  tags: string[];
}

const MILESTONES: Milestone[] = [
  {
    num: '01',
    year: '2024',
    badge: 'Genesis',
    icon: Sparkles,
    title: 'Killing the 7-Tool Sprawl',
    story: 'Unified lead discovery, client briefs, and scoping into one calm command center.',
    tags: ['Lead CRM', 'Scope Briefs', 'Client Portal']
  },
  {
    num: '02',
    year: '2025',
    badge: 'Protocols',
    icon: FileCheck,
    title: 'Retainers, GST & E-Sign',
    story: 'Automated 18% GST billing, recurring retainer schedules, and SHA-256 e-sign agreements.',
    tags: ['18% GST Engine', 'E-Sign Vault', 'Auto-Billing']
  },
  {
    num: '03',
    year: '2026',
    badge: 'AI Co-Founder',
    icon: Bot,
    title: 'Autonomous AI Triage',
    story: 'Deployed 24/7 AI agents that qualify inbound briefs and draft project proposals instantly.',
    tags: ['AI Lead Triage', 'Scope Estimator', 'Instant Proposals']
  },
  {
    num: '04',
    year: '2027+',
    badge: 'Global Scale',
    icon: TrendingUp,
    title: 'Scaling High-Margin Agencies',
    story: 'Empowering design, dev, and marketing firms worldwide to 10x output with zero admin drag.',
    tags: ['Multi-Currency', 'Global Retainers', 'Zero Overhead']
  }
];

export function ScrollDrivenTimeline() {
  const [activeIdx, setActiveIdx] = useState<number | null>(null);

  return (
    <div className="w-full space-y-8 sm:space-y-12">
      
      {/* ── Section Header ── */}
      <div className="text-center space-y-2.5 sm:space-y-3.5 max-w-[720px] mx-auto px-4">
        <div className="inline-flex items-center gap-2 px-3 py-0.5 bg-white/95 backdrop-blur-md rounded-full text-[10.5px] font-mono font-bold tracking-[0.16em] uppercase text-zinc-700 border border-zinc-200/90 shadow-2xs">
          <span>OUR EVOLUTION</span>
        </div>
        
        <div>
          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl lg:text-[62px] font-bold tracking-[-0.03em] leading-[1.08] bg-gradient-to-r from-zinc-950 via-zinc-700 to-zinc-400 bg-clip-text text-transparent inline-block">
            Our journey.
          </h2>
        </div>
        
        <p className="text-zinc-600 text-xs sm:text-sm lg:text-base leading-relaxed max-w-[560px] mx-auto font-normal">
          The roadmap behind building the modern operating system for agencies.
        </p>
      </div>

      {/* ── Desktop: Horizontal Connected Timeline Track (4 Columns) ── */}
      <div className="hidden lg:block max-w-[1240px] mx-auto px-6">
        
        {/* Unified 4-Column Timeline & Card Assembly */}
        <div className="grid grid-cols-4 gap-5 items-stretch">
          {MILESTONES.map((item, idx) => {
            const Icon = item.icon;
            const isHovered = activeIdx === idx;

            return (
              <div
                key={idx}
                onMouseEnter={() => setActiveIdx(idx)}
                onMouseLeave={() => setActiveIdx(null)}
                className="flex flex-col group cursor-pointer"
              >
                {/* ── Timeline Rail Header ── */}
                <div className="relative flex items-center justify-center mb-4 py-1">
                  
                  {/* Connecting Rail: Left Segment (hidden on 1st item) */}
                  {idx > 0 && (
                    <div className={`absolute left-0 right-1/2 top-1/2 -translate-y-1/2 h-[1.5px] transition-colors duration-200 ${
                      isHovered ? 'bg-zinc-400' : 'bg-zinc-200'
                    }`} />
                  )}

                  {/* Connecting Rail: Right Segment (hidden on last item) */}
                  {idx < MILESTONES.length - 1 && (
                    <div className={`absolute left-1/2 right-0 top-1/2 -translate-y-1/2 h-[1.5px] transition-colors duration-200 ${
                      isHovered ? 'bg-zinc-400' : 'bg-zinc-200'
                    }`} />
                  )}

                  {/* Year Pill Node (Centered on Rail) */}
                  <div className={`relative z-10 px-3.5 py-1 rounded-full text-xs font-mono font-bold transition-all duration-300 flex items-center gap-1.5 shadow-2xs ${
                    isHovered 
                      ? 'bg-zinc-950 text-white scale-105 shadow-md' 
                      : 'bg-white text-zinc-800 border border-zinc-200/90 group-hover:border-zinc-950'
                  }`}>
                    <span className={`w-1.5 h-1.5 rounded-full transition-colors ${
                      isHovered ? 'bg-emerald-400' : 'bg-emerald-500'
                    }`} />
                    <span>{item.year}</span>
                  </div>

                </div>

                {/* ── Milestone Card ── */}
                <div className={`flex-1 rounded-2xl bg-white border p-5 flex flex-col justify-between transition-all duration-300 ${
                  isHovered
                    ? 'border-zinc-950 shadow-[0_14px_36px_-6px_rgba(0,0,0,0.09)] -translate-y-1'
                    : 'border-zinc-200/90 shadow-[0_2px_10px_-2px_rgba(0,0,0,0.03)] hover:border-zinc-300'
                }`}>
                  <div>
                    {/* Top Bar: Icon + Phase + Category Badge */}
                    <div className="flex items-center justify-between gap-2 mb-3.5">
                      <div className="flex items-center gap-2">
                        <div className={`w-7 h-7 rounded-lg flex items-center justify-center transition-colors ${
                          isHovered ? 'bg-zinc-950 text-white' : 'bg-zinc-100 text-zinc-800'
                        }`}>
                          <Icon className="w-3.5 h-3.5 stroke-[2]" />
                        </div>
                        <span className="text-[11px] font-mono font-bold text-zinc-900">
                          Phase {item.num}
                        </span>
                      </div>

                      {/* Refined Category Badge */}
                      <span className="text-[9.5px] font-mono font-bold tracking-wider text-zinc-700 bg-zinc-100/90 border border-zinc-200/70 px-2 py-0.5 rounded-md uppercase">
                        {item.badge}
                      </span>
                    </div>

                    {/* Title (Aligned with min-height) */}
                    <h3 className="font-display text-[15px] font-bold text-zinc-950 tracking-tight leading-snug mb-2 min-h-[42px] flex items-center">
                      {item.title}
                    </h3>

                    {/* Narrative (Aligned with min-height) */}
                    <p className="text-xs text-zinc-600 leading-relaxed font-normal mb-4 min-h-[52px]">
                      {item.story}
                    </p>
                  </div>

                  {/* Feature Tags */}
                  <div className="flex items-center flex-wrap gap-1.5 pt-3 border-t border-zinc-100">
                    {item.tags.map((tag, tIdx) => (
                      <span
                        key={tIdx}
                        className="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-zinc-50 border border-zinc-200/60 text-[9.5px] font-mono text-zinc-600 font-medium"
                      >
                        <Check className="w-2.5 h-2.5 text-zinc-900 stroke-[2.5]" />
                        <span>{tag}</span>
                      </span>
                    ))}
                  </div>
                </div>

              </div>
            );
          })}
        </div>

      </div>

      {/* ── Mobile / Tablet: Clean Connected Vertical Timeline Thread ── */}
      <div className="block lg:hidden max-w-[620px] mx-auto px-4 sm:px-6">
        <div className="relative pl-6 sm:pl-8 border-l-2 border-zinc-200/90 ml-3 sm:ml-4 space-y-5 sm:space-y-6">
          {MILESTONES.map((item, idx) => {
            const Icon = item.icon;

            return (
              <div key={idx} className="relative group">
                
                {/* Timeline Spine Node */}
                <div className="absolute -left-[31px] sm:-left-[39px] top-4 w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-white border-2 border-zinc-900 flex items-center justify-center text-zinc-900 shadow-2xs z-10">
                  <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" />
                </div>

                {/* Timeline Milestone Content */}
                <div className="rounded-2xl bg-white border border-zinc-200/80 p-4 sm:p-5 shadow-[0_2px_12px_-2px_rgba(0,0,0,0.03)] hover:border-zinc-300 transition-all">
                  
                  {/* Top Bar: Icon + Phase + Year + Badge */}
                  <div className="flex items-center justify-between gap-2 mb-2.5">
                    <div className="flex items-center gap-2">
                      <div className="w-7 h-7 rounded-lg bg-zinc-100 text-zinc-900 flex items-center justify-center shrink-0">
                        <Icon className="w-3.5 h-3.5 stroke-[2]" />
                      </div>
                      <div className="flex items-center gap-1.5 text-xs font-mono">
                        <span className="font-bold text-zinc-950">Phase {item.num}</span>
                        <span className="text-zinc-400">·</span>
                        <span className="font-bold text-zinc-800 bg-zinc-100 px-2 py-0.5 rounded text-[11px]">{item.year}</span>
                      </div>
                    </div>

                    <span className="text-[9.5px] font-mono font-bold tracking-wider text-zinc-700 bg-zinc-100/90 border border-zinc-200/70 px-2 py-0.5 rounded-md uppercase">
                      {item.badge}
                    </span>
                  </div>

                  {/* Title */}
                  <h3 className="font-display text-base font-bold text-zinc-950 tracking-tight leading-snug mb-1.5">
                    {item.title}
                  </h3>

                  {/* Story */}
                  <p className="text-xs text-zinc-600 leading-relaxed font-normal mb-3">
                    {item.story}
                  </p>

                  {/* Tags */}
                  <div className="flex items-center flex-wrap gap-1.5 pt-2.5 border-t border-zinc-100">
                    {item.tags.map((tag, tIdx) => (
                      <span
                        key={tIdx}
                        className="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-zinc-50 border border-zinc-200/60 text-[10px] font-mono text-zinc-600"
                      >
                        <Check className="w-2.5 h-2.5 text-zinc-900 stroke-[2.5]" />
                        <span>{tag}</span>
                      </span>
                    ))}
                  </div>

                </div>

              </div>
            );
          })}
        </div>
      </div>

    </div>
  );
}
