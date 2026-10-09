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
  return (
    <div className="w-full space-y-6 sm:space-y-10">
      
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

      {/* ── Structured 2x2 Bento Matrix (Effortless Grid on Desktop, Clean Stack on Mobile) ── */}
      <div className="max-w-[1080px] mx-auto px-4 sm:px-6">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-3.5 sm:gap-5">
          {MILESTONES.map((item, idx) => {
            const Icon = item.icon;

            return (
              <div 
                key={idx} 
                className="group relative rounded-2xl sm:rounded-3xl bg-white border border-zinc-200/80 p-5 sm:p-6 shadow-[0_2px_12px_-2px_rgba(0,0,0,0.03)] hover:shadow-[0_10px_30px_-6px_rgba(0,0,0,0.07)] hover:border-zinc-300 transition-all duration-300 flex flex-col justify-between"
              >
                <div>
                  {/* Top Bar: Icon Tile + Phase Number + Year + Category Badge */}
                  <div className="flex items-center justify-between gap-3 mb-3.5">
                    <div className="flex items-center gap-2.5">
                      <div className="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-zinc-100 group-hover:bg-zinc-950 group-hover:text-white text-zinc-900 flex items-center justify-center transition-colors duration-200 shrink-0">
                        <Icon className="w-4 h-4 stroke-[2]" />
                      </div>
                      <div className="flex items-center gap-1.5 font-mono text-xs">
                        <span className="font-bold text-zinc-950">Phase {item.num}</span>
                        <span className="text-zinc-400">·</span>
                        <span className="text-zinc-500">{item.year}</span>
                      </div>
                    </div>

                    <span className="text-[10px] sm:text-[10.5px] font-mono font-semibold tracking-wider text-zinc-500 bg-zinc-50 border border-zinc-200/70 px-2 py-0.5 rounded-full uppercase">
                      {item.badge}
                    </span>
                  </div>

                  {/* Headline */}
                  <h3 className="font-display text-base sm:text-xl font-bold text-zinc-950 tracking-tight mb-1.5 leading-snug">
                    {item.title}
                  </h3>

                  {/* Short 1-Line Narrative */}
                  <p className="text-xs sm:text-[13.5px] text-zinc-600 leading-relaxed font-normal mb-4">
                    {item.story}
                  </p>
                </div>

                {/* Minimalist Feature Tags */}
                <div className="flex items-center flex-wrap gap-1.5 pt-3 border-t border-zinc-100">
                  {item.tags.map((tag, tIdx) => (
                    <span
                      key={tIdx}
                      className="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-zinc-50 border border-zinc-200/60 text-[10px] sm:text-[10.5px] font-mono text-zinc-600 font-medium"
                    >
                      <Check className="w-2.5 h-2.5 text-zinc-900 stroke-[2.5]" />
                      <span>{tag}</span>
                    </span>
                  ))}
                </div>

              </div>
            );
          })}
        </div>
      </div>

    </div>
  );
}
