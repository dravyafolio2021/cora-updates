'use client';

import React from 'react';
import {
  Bot,
  AudioWaveform,
  Sparkles,
  Database,
  Kanban,
  Layers,
  FileText,
  Star,
  FileCheck2,
  Navigation,
  Calendar,
  CheckSquare,
  Receipt,
  Camera,
  Film,
  ShieldCheck,
} from 'lucide-react';

interface CapabilityItem {
  name: string;
  desc: string;
  icon: React.ComponentType<{ className?: string }>;
  iconBg: string;
  iconBorder: string;
  iconColor: string;
}

interface CapabilityColumn {
  category: string;
  badge: string;
  badgeColor: string;
  items: CapabilityItem[];
}

const capabilityColumns: CapabilityColumn[] = [
  {
    category: 'INTELLIGENCE & AI',
    badge: 'FLAGSHIP',
    badgeColor: 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
    items: [
      {
        name: 'AI Co-Founder',
        desc: 'Autonomous operations triage',
        icon: Bot,
        iconBg: 'bg-emerald-50',
        iconBorder: 'border-emerald-100',
        iconColor: 'text-emerald-600',
      },
      {
        name: 'Voice-to-Scope',
        desc: 'Audio briefs to structured milestones',
        icon: AudioWaveform,
        iconBg: 'bg-purple-50',
        iconBorder: 'border-purple-100',
        iconColor: 'text-purple-600',
      },
      {
        name: 'Content AI & GEO',
        desc: '3-Act viral scripts & answer engines',
        icon: Sparkles,
        iconBg: 'bg-amber-50',
        iconBorder: 'border-amber-100',
        iconColor: 'text-amber-500',
      },
      {
        name: 'RAG Memory MCP',
        desc: 'Semantic memory & IDE bridge',
        icon: Database,
        iconBg: 'bg-sky-50',
        iconBorder: 'border-sky-100',
        iconColor: 'text-sky-600',
      },
    ],
  },
  {
    category: 'GROWTH & PIPELINE',
    badge: 'GROWTH',
    badgeColor: 'bg-blue-50 text-blue-700 border-blue-200/80',
    items: [
      {
        name: 'Kanban Lead CRM',
        desc: 'Deal stages & WhatsApp follow-up',
        icon: Kanban,
        iconBg: 'bg-blue-50',
        iconBorder: 'border-blue-100',
        iconColor: 'text-blue-600',
      },
      {
        name: 'Funnel Builder',
        desc: 'High-converting pages & reels',
        icon: Layers,
        iconBg: 'bg-rose-50',
        iconBorder: 'border-rose-100',
        iconColor: 'text-rose-500',
      },
      {
        name: 'Visual Forms',
        desc: 'Dynamic briefs & call-time booking',
        icon: FileText,
        iconBg: 'bg-teal-50',
        iconBorder: 'border-teal-100',
        iconColor: 'text-teal-600',
      },
      {
        name: '5★ Review Portal',
        desc: 'Smart sentiment routing & Google reviews',
        icon: Star,
        iconBg: 'bg-amber-50',
        iconBorder: 'border-amber-100',
        iconColor: 'text-amber-500',
      },
    ],
  },
  {
    category: 'OPERATIONS & LEGAL',
    badge: 'LEGAL TECH',
    badgeColor: 'bg-purple-50 text-purple-700 border-purple-200/80',
    items: [
      {
        name: 'SHA-256 E-Signs',
        desc: '5-Step legally binding digital contracts',
        icon: FileCheck2,
        iconBg: 'bg-emerald-50',
        iconBorder: 'border-emerald-100',
        iconColor: 'text-emerald-600',
      },
      {
        name: 'Crew Dispatch',
        desc: 'Automated call sheets & conflict alerts',
        icon: Navigation,
        iconBg: 'bg-indigo-50',
        iconBorder: 'border-indigo-100',
        iconColor: 'text-indigo-600',
      },
      {
        name: 'Master Calendar',
        desc: 'Multi-location scheduling & iCal sync',
        icon: Calendar,
        iconBg: 'bg-rose-50',
        iconBorder: 'border-rose-100',
        iconColor: 'text-rose-500',
      },
      {
        name: 'Task Board',
        desc: 'Post-production sprints & proofing',
        icon: CheckSquare,
        iconBg: 'bg-purple-50',
        iconBorder: 'border-purple-100',
        iconColor: 'text-purple-600',
      },
    ],
  },
  {
    category: 'FINANCE & ASSETS',
    badge: 'INDIA GST',
    badgeColor: 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
    items: [
      {
        name: '18% GST Invoicing',
        desc: 'Automated CGST/SGST splitting & SAC codes',
        icon: Receipt,
        iconBg: 'bg-emerald-50',
        iconBorder: 'border-emerald-100',
        iconColor: 'text-emerald-600',
      },
      {
        name: 'Gear & Inventory',
        desc: 'Equipment registry & checkouts',
        icon: Camera,
        iconBg: 'bg-zinc-100',
        iconBorder: 'border-zinc-200',
        iconColor: 'text-zinc-700',
      },
      {
        name: 'Media Hub & RAW',
        desc: 'Workspace library & 4K galleries',
        icon: Film,
        iconBg: 'bg-fuchsia-50',
        iconBorder: 'border-fuchsia-100',
        iconColor: 'text-fuchsia-600',
      },
      {
        name: 'Multi-Tenant RBAC',
        desc: '30-Point security & capability roles',
        icon: ShieldCheck,
        iconBg: 'bg-cyan-50',
        iconBorder: 'border-cyan-100',
        iconColor: 'text-cyan-600',
      },
    ],
  },
];

export function IndustryGridSection() {
  return (
    <section className="pt-10 pb-16 sm:pt-14 sm:pb-24 bg-[#FFFFFF] relative z-10 overflow-hidden">
      <div className="w-full max-w-[1280px] mx-auto px-4 sm:px-6">
        
        {/* ── Section Header (Properly Centered with Dual Tone Gradient) ── */}
        <div className="max-w-[760px] mx-auto text-center mb-12 sm:mb-16">
          <div className="inline-flex items-center gap-1.5 px-3 py-1 bg-zinc-100 rounded-full text-zinc-800 text-xs font-semibold uppercase tracking-wider mb-3.5 border border-zinc-200/80">
            <span className="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-pulse" />
            <span>UNIFIED AGENCY ECOSYSTEM</span>
          </div>

          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl lg:text-[46px] font-bold leading-[1.2] tracking-[-0.03em] bg-gradient-to-r from-zinc-950 via-zinc-700 to-zinc-400 bg-clip-text text-transparent inline-block pb-2 mb-3">
            Powering every agency capability
          </h2>
          <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[640px] mx-auto">
            From autonomous AI brief triage to legally binding SHA-256 contracts and 18% GST billing — every module works as one unified engine.
          </p>
        </div>

        {/* ── 4-Column Directory (Clean, Simple, High-Readability) ── */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-6 max-w-[1280px] mx-auto">
          {capabilityColumns.map((col, cIdx) => (
            <div key={cIdx} className="space-y-5">
              {/* Column Header */}
              <div className="flex items-center justify-between pb-3 border-b border-zinc-100">
                <span className="text-[11px] font-mono font-bold tracking-widest text-zinc-400 uppercase">
                  {col.category}
                </span>
                <span className={`text-[10px] font-mono font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full border ${col.badgeColor}`}>
                  {col.badge}
                </span>
              </div>

              {/* Column Items */}
              <div className="space-y-4">
                {col.items.map((item, iIdx) => {
                  const Icon = item.icon;
                  return (
                    <div
                      key={iIdx}
                      className="flex items-start gap-3.5 p-2 -m-2 rounded-2xl hover:bg-zinc-50/80 transition-colors duration-150 group cursor-default select-none"
                    >
                      {/* Icon */}
                      <div
                        className={`w-11 h-11 rounded-2xl ${item.iconBg} ${item.iconBorder} border ${item.iconColor} flex items-center justify-center shrink-0 shadow-2xs transition-transform group-hover:scale-105 mt-0.5`}
                      >
                        <Icon className="w-5 h-5" />
                      </div>

                      {/* Text */}
                      <div className="space-y-0.5 min-w-0 flex-1">
                        <h3 className="font-bold text-zinc-950 text-[14.5px] leading-snug group-hover:text-indigo-600 transition-colors">
                          {item.name}
                        </h3>
                        <p className="text-zinc-500 text-xs sm:text-[12.5px] leading-tight">
                          {item.desc}
                        </p>
                      </div>
                    </div>
                  );
                })}
              </div>
            </div>
          ))}
        </div>

      </div>
    </section>
  );
}



