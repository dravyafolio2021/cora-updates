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
    category: 'Get help from AI',
    badge: 'AI HELP',
    badgeColor: 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
    items: [
      {
        name: 'Work with your AI co-founder',
        desc: 'Plan your next steps and handle daily work.',
        icon: Bot,
        iconBg: 'bg-emerald-50',
        iconBorder: 'border-emerald-100',
        iconColor: 'text-emerald-600',
      },
      {
        name: 'Turn voice notes into a plan',
        desc: 'Turn client messages into clear project steps.',
        icon: AudioWaveform,
        iconBg: 'bg-purple-50',
        iconBorder: 'border-purple-100',
        iconColor: 'text-purple-600',
      },
      {
        name: 'Create content faster',
        desc: 'Draft scripts and prepare content for AI search.',
        icon: Sparkles,
        iconBg: 'bg-amber-50',
        iconBorder: 'border-amber-100',
        iconColor: 'text-amber-500',
      },
      {
        name: 'Find the answers you need',
        desc: 'Search client briefs, files, and past decisions.',
        icon: Database,
        iconBg: 'bg-sky-50',
        iconBorder: 'border-sky-100',
        iconColor: 'text-sky-600',
      },
    ],
  },
  {
    category: 'Turn enquiries into clients',
    badge: 'SALES',
    badgeColor: 'bg-blue-50 text-blue-700 border-blue-200/80',
    items: [
      {
        name: 'Follow up with every lead',
        desc: 'Track enquiries and deals in your CRM.',
        icon: Kanban,
        iconBg: 'bg-blue-50',
        iconBorder: 'border-blue-100',
        iconColor: 'text-blue-600',
      },
      {
        name: 'Build pages that bring enquiries',
        desc: 'Create landing pages for your services and campaigns.',
        icon: Layers,
        iconBg: 'bg-rose-50',
        iconBorder: 'border-rose-100',
        iconColor: 'text-rose-500',
      },
      {
        name: 'Collect clear client briefs',
        desc: 'Use forms to gather details and book calls.',
        icon: FileText,
        iconBg: 'bg-teal-50',
        iconBorder: 'border-teal-100',
        iconColor: 'text-teal-600',
      },
      {
        name: 'Ask clients for reviews',
        desc: 'Collect feedback and invite clients to leave reviews.',
        icon: Star,
        iconBg: 'bg-amber-50',
        iconBorder: 'border-amber-100',
        iconColor: 'text-amber-500',
      },
    ],
  },
  {
    category: 'Keep projects moving',
    badge: 'CLIENT WORK',
    badgeColor: 'bg-purple-50 text-purple-700 border-purple-200/80',
    items: [
      {
        name: 'Get client agreements signed',
        desc: 'Send contracts for clients to sign online.',
        icon: FileCheck2,
        iconBg: 'bg-emerald-50',
        iconBorder: 'border-emerald-100',
        iconColor: 'text-emerald-600',
      },
      {
        name: 'Keep your team informed',
        desc: 'Share job details and check for schedule clashes.',
        icon: Navigation,
        iconBg: 'bg-indigo-50',
        iconBorder: 'border-indigo-100',
        iconColor: 'text-indigo-600',
      },
      {
        name: 'Plan your week',
        desc: 'Keep client calls and team schedules together.',
        icon: Calendar,
        iconBg: 'bg-rose-50',
        iconBorder: 'border-rose-100',
        iconColor: 'text-rose-500',
      },
      {
        name: 'Stay on top of tasks',
        desc: 'Assign work and see what’s done or due.',
        icon: CheckSquare,
        iconBg: 'bg-purple-50',
        iconBorder: 'border-purple-100',
        iconColor: 'text-purple-600',
      },
    ],
  },
  {
    category: 'Manage money and files',
    badge: 'FINANCE & FILES',
    badgeColor: 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
    items: [
      {
        name: 'Send GST invoices',
        desc: 'Create invoices for your client work.',
        icon: Receipt,
        iconBg: 'bg-emerald-50',
        iconBorder: 'border-emerald-100',
        iconColor: 'text-emerald-600',
      },
      {
        name: 'Track your equipment',
        desc: 'See what’s available and who’s using it.',
        icon: Camera,
        iconBg: 'bg-zinc-100',
        iconBorder: 'border-zinc-200',
        iconColor: 'text-zinc-700',
      },
      {
        name: 'Keep client files together',
        desc: 'Organise images, videos, and project files.',
        icon: Film,
        iconBg: 'bg-fuchsia-50',
        iconBorder: 'border-fuchsia-100',
        iconColor: 'text-fuchsia-600',
      },
      {
        name: 'Control who sees what',
        desc: 'Choose what clients and teammates can access.',
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
    <section className="py-16 sm:py-[100px] bg-[#FFFFFF] relative z-10 overflow-hidden">
      <div className="w-full max-w-[1280px] mx-auto px-4 sm:px-6">
        
        {/* ── Section Header (Properly Centered with Dual Tone Gradient) ── */}
        <div className="max-w-[760px] mx-auto text-center mb-12 sm:mb-16">
          <div className="inline-flex items-center gap-1.5 px-3 py-1 bg-zinc-100 rounded-full text-zinc-800 text-xs font-semibold uppercase tracking-wider mb-3.5 border border-zinc-200/80">
            <span className="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-pulse" />
            <span>AGENCY WORKSPACE</span>
          </div>

          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl lg:text-[46px] font-bold leading-[1.2] tracking-[-0.03em] bg-gradient-to-r from-zinc-950 via-zinc-700 to-zinc-400 bg-clip-text text-transparent inline-block pb-2 mb-3">
            Keep your agency moving.
          </h2>
          <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[640px] mx-auto">
            Manage leads, projects, files, and payments in one workspace.
          </p>
        </div>

        {/* ── 4-Column Directory (Clean, Simple, High-Readability) ── */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-6 max-w-[1280px] mx-auto">
          {capabilityColumns.map((col, cIdx) => (
            <div key={cIdx} className="space-y-5">
              {/* Column Header */}
              <div className="flex items-center justify-between pb-3 border-b border-zinc-100">
                <span className="text-xs sm:text-[13px] font-bold text-zinc-900 tracking-tight">
                  {col.category}
                </span>
                <span className={`text-[9.5px] font-mono font-bold uppercase tracking-wider px-2 py-0.5 rounded-full border ${col.badgeColor}`}>
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
                        <p className="text-zinc-500 text-xs sm:text-[12.5px] leading-relaxed">
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
