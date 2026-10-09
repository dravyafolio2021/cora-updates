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
  phase: string;
  year: string;
  badge: string;
  icon: React.ElementType;
  title: string;
  story: string;
  tags: string[];
}

const MILESTONES: Milestone[] = [
  {
    phase: 'PHASE 01',
    year: '2024',
    badge: 'GENESIS',
    icon: Sparkles,
    title: 'Killing the 7-Tool Agency Sprawl',
    story: 'We eliminated scattered client briefs, lost WhatsApp feedback, and disconnected Google Sheets by unifying lead discovery and project scopes into one calm agency command center.',
    tags: ['Lead Intake CRM', 'Scope Briefs', 'Client Portal']
  },
  {
    phase: 'PHASE 02',
    year: '2025',
    badge: 'PROTOCOLS',
    icon: FileCheck,
    title: 'Automating Retainers, GST & E-Sign',
    story: 'We engineered automated 18% GST billing, milestone-based retainer collections, and legally binding SHA-256 e-sign contracts to protect agency margins and eliminate late payments.',
    tags: ['18% GST Engine', 'SHA-256 Contracts', 'Auto-Invoicing']
  },
  {
    phase: 'PHASE 03',
    year: '2026',
    badge: 'AI CO-FOUNDER',
    icon: Bot,
    title: 'Autonomous AI Lead Triage & Scoping',
    story: 'We deployed frontier AI co-founders that qualify high-intent inbound leads 24/7, draft technical estimates, and generate commercial proposals before your first discovery call.',
    tags: ['24/7 AI Lead Triage', 'Scope Estimator', 'Auto-Proposals']
  },
  {
    phase: 'PHASE 04',
    year: '2027 & BEYOND',
    badge: 'GLOBAL SCALE',
    icon: TrendingUp,
    title: 'Scaling High-Margin Agencies Worldwide',
    story: 'We empower performance marketing agencies, UI/UX design firms, and dev consultancies globally to 10x output and scale recurring retainers with zero administrative overhead.',
    tags: ['Multi-Currency', 'Global Workspace', 'Zero Admin Fatigue']
  }
];

export function ScrollDrivenTimeline() {
  const [hoveredIdx, setHoveredIdx] = useState<number | null>(null);

  return (
    <div className="w-full space-y-8 sm:space-y-14">
      
      {/* ── Section Header ── */}
      <div className="text-center space-y-3 sm:space-y-4 max-w-[760px] mx-auto px-4">
        <div className="inline-flex items-center gap-2 px-3.5 py-1 bg-white/95 backdrop-blur-md rounded-full text-[11px] font-mono font-bold tracking-[0.16em] uppercase text-zinc-700 border border-zinc-200/90 shadow-2xs">
          <span>OUR EVOLUTION</span>
        </div>
        
        <div>
          <h2 className="font-display text-3xl xs:text-4xl sm:text-6xl lg:text-[70px] font-bold tracking-[-0.03em] leading-[1.08] bg-gradient-to-r from-zinc-950 via-zinc-700 to-zinc-400 bg-clip-text text-transparent inline-block">
            Our journey.
          </h2>
        </div>
        
        <p className="text-zinc-600 text-xs xs:text-sm sm:text-base lg:text-lg leading-relaxed max-w-[620px] mx-auto font-normal">
          From solving our own agency bottlenecks to empowering hundreds of high-growth creative and development firms worldwide.
        </p>
      </div>

      {/* ── Modern Connected Milestone Journey Cards ── */}
      <div className="max-w-[780px] mx-auto px-4 sm:px-6">
        <div className="relative space-y-4 sm:space-y-6">
          
          {/* Subtle Vertical Connector (Desktop) */}
          <div className="hidden sm:block absolute left-6 top-8 bottom-8 w-px bg-zinc-200 pointer-events-none" />

          {MILESTONES.map((item, idx) => {
            const Icon = item.icon;
            const isHovered = hoveredIdx === idx;

            return (
              <div 
                key={idx} 
                onMouseEnter={() => setHoveredIdx(idx)}
                onMouseLeave={() => setHoveredIdx(null)}
                className={`relative group rounded-2xl sm:rounded-3xl bg-white border p-5 sm:p-7 transition-all duration-300 ${
                  isHovered
                    ? 'border-zinc-400 shadow-[0_12px_36px_-8px_rgba(0,0,0,0.08)] -translate-y-0.5'
                    : 'border-zinc-200/90 shadow-[0_2px_12px_-2px_rgba(0,0,0,0.03)] hover:border-zinc-300'
                }`}
              >
                {/* Top Bar: Icon Tile + Phase/Year + Category Badge */}
                <div className="flex items-center justify-between gap-3 mb-4">
                  <div className="flex items-center gap-3">
                    {/* Icon Badge */}
                    <div className={`w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center transition-colors duration-200 ${
                      isHovered
                        ? 'bg-zinc-950 text-white shadow-xs'
                        : 'bg-zinc-100 text-zinc-900 group-hover:bg-zinc-950 group-hover:text-white'
                    }`}>
                      <Icon className="w-4 h-4 sm:w-4.5 sm:h-4.5 stroke-[2]" />
                    </div>

                    {/* Phase & Year */}
                    <div className="flex flex-col sm:flex-row sm:items-center sm:gap-2">
                      <span className="text-xs sm:text-[13px] font-mono font-bold text-zinc-950 tracking-tight">
                        {item.phase}
                      </span>
                      <span className="text-[11px] sm:text-xs font-mono text-zinc-400 font-medium">
                        • {item.year}
                      </span>
                    </div>
                  </div>

                  {/* Status/Category Tag */}
                  <span className="text-[10px] sm:text-[11px] font-mono font-bold tracking-wider text-zinc-500 bg-zinc-50 border border-zinc-200/80 px-2.5 py-1 rounded-full uppercase">
                    {item.badge}
                  </span>
                </div>

                {/* Headline */}
                <h3 className="font-display text-lg sm:text-2xl font-bold text-zinc-950 tracking-tight mb-2 leading-snug">
                  {item.title}
                </h3>

                {/* Story Copy */}
                <p className="text-xs sm:text-sm text-zinc-600 leading-relaxed font-normal mb-4">
                  {item.story}
                </p>

                {/* Capability Tags */}
                <div className="flex items-center flex-wrap gap-1.5 sm:gap-2 pt-3 border-t border-zinc-100">
                  {item.tags.map((tag, tIdx) => (
                    <span
                      key={tIdx}
                      className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-zinc-50 border border-zinc-200/70 text-[10px] sm:text-[11px] font-mono font-medium text-zinc-700"
                    >
                      <Check className="w-3 h-3 text-zinc-900 stroke-[2.5]" />
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
