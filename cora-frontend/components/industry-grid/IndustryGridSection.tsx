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
    category: 'AI Assistance',
    badge: 'AI HELP',
    badgeColor: 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
    items: [
      {
        name: 'AI Co-Founder',
        desc: 'Plan daily tasks, draft replies, and run operations.',
        icon: Bot,
        iconBg: 'bg-emerald-50',
        iconBorder: 'border-emerald-100',
        iconColor: 'text-emerald-600',
      },
      {
        name: 'Voice to Text',
        desc: 'Turn client audio into clear, actionable project steps.',
        icon: AudioWaveform,
        iconBg: 'bg-purple-50',
        iconBorder: 'border-purple-100',
        iconColor: 'text-purple-600',
      },
      {
        name: 'AI SEO & Content',
        desc: 'Draft video scripts, posts, and search-ready copy.',
        icon: Sparkles,
        iconBg: 'bg-amber-50',
        iconBorder: 'border-amber-100',
        iconColor: 'text-amber-500',
      },
      {
        name: 'AI Memory Search',
        desc: 'Search client briefs, documents, and past project notes.',
        icon: Database,
        iconBg: 'bg-sky-50',
        iconBorder: 'border-sky-100',
        iconColor: 'text-sky-600',
      },
    ],
  },
  {
    category: 'Sales & Pipeline',
    badge: 'SALES',
    badgeColor: 'bg-blue-50 text-blue-700 border-blue-200/80',
    items: [
      {
        name: 'Lead CRM',
        desc: 'Track deals, log calls, and message prospects on WhatsApp.',
        icon: Kanban,
        iconBg: 'bg-blue-50',
        iconBorder: 'border-blue-100',
        iconColor: 'text-blue-600',
      },
      {
        name: 'Landing Pages',
        desc: 'Launch fast pages for your services and campaigns.',
        icon: Layers,
        iconBg: 'bg-rose-50',
        iconBorder: 'border-rose-100',
        iconColor: 'text-rose-500',
      },
      {
        name: 'Intake Forms',
        desc: 'Use forms to gather project details and book intake calls.',
        icon: FileText,
        iconBg: 'bg-teal-50',
        iconBorder: 'border-teal-100',
        iconColor: 'text-teal-600',
      },
      {
        name: '5★ Review Portal',
        desc: 'Request feedback and publish 5-star Google reviews.',
        icon: Star,
        iconBg: 'bg-amber-50',
        iconBorder: 'border-amber-100',
        iconColor: 'text-amber-500',
      },
    ],
  },
  {
    category: 'Client Operations',
    badge: 'CLIENT WORK',
    badgeColor: 'bg-purple-50 text-purple-700 border-purple-200/80',
    items: [
      {
        name: 'Digital Contracts',
        desc: 'Send digital contracts for clients to sign online legally.',
        icon: FileCheck2,
        iconBg: 'bg-emerald-50',
        iconBorder: 'border-emerald-100',
        iconColor: 'text-emerald-600',
      },
      {
        name: 'Crew Dispatch',
        desc: 'Share job briefs, call sheets, and avoid scheduling clashes.',
        icon: Navigation,
        iconBg: 'bg-indigo-50',
        iconBorder: 'border-indigo-100',
        iconColor: 'text-indigo-600',
      },
      {
        name: 'Master Calendar',
        desc: 'Keep client calls, shoots, and team deadlines together.',
        icon: Calendar,
        iconBg: 'bg-rose-50',
        iconBorder: 'border-rose-100',
        iconColor: 'text-rose-500',
      },
      {
        name: 'Task Board',
        desc: 'Assign work to team members and monitor progress in real time.',
        icon: CheckSquare,
        iconBg: 'bg-purple-50',
        iconBorder: 'border-purple-100',
        iconColor: 'text-purple-600',
      },
    ],
  },
  {
    category: 'Finance & Files',
    badge: 'FINANCE & FILES',
    badgeColor: 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
    items: [
      {
        name: 'GST Invoicing',
        desc: 'Create compliant GST invoices with instant UPI QR payments.',
        icon: Receipt,
        iconBg: 'bg-emerald-50',
        iconBorder: 'border-emerald-100',
        iconColor: 'text-emerald-600',
      },
      {
        name: 'Gear Inventory',
        desc: 'See which gear is available and who is currently using it.',
        icon: Camera,
        iconBg: 'bg-zinc-100',
        iconBorder: 'border-zinc-200',
        iconColor: 'text-zinc-700',
      },
      {
        name: 'Media Hub',
        desc: 'Organise project files, media assets, and delivery galleries.',
        icon: Film,
        iconBg: 'bg-fuchsia-50',
        iconBorder: 'border-fuchsia-100',
        iconColor: 'text-fuchsia-600',
      },
      {
        name: 'Role Permissions',
        desc: 'Choose exactly what clients and team members can view.',
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
        
        {/* ── Section Header (Strictly Centered Stack with Clean Hierarchy) ── */}
        <div className="max-w-[760px] mx-auto flex flex-col items-center text-center mb-12 sm:mb-16">
          <div className="inline-flex items-center gap-1.5 px-3 py-1 bg-zinc-100 rounded-full text-zinc-800 text-xs font-semibold uppercase tracking-wider mb-4 border border-zinc-200/80 shadow-2xs">
            <span className="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-pulse" />
            <span>AGENCY WORKSPACE</span>
          </div>

          <h2 className="w-full font-display text-3xl xs:text-4xl sm:text-5xl lg:text-[48px] font-bold leading-[1.12] tracking-[-0.03em] text-zinc-950 mb-3.5">
            Keep your agency moving.
          </h2>
          
          <p className="text-zinc-600 text-sm sm:text-base md:text-lg font-normal leading-relaxed max-w-[600px]">
            Manage leads, projects, files, and payments in one workspace.
          </p>
        </div>

        {/* ── 4-Column Directory (Feature Product Names & Max 2-Line Active Descriptions) ── */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-6 max-w-[1280px] mx-auto">
          {capabilityColumns.map((col, cIdx) => (
            <div key={cIdx} className="space-y-4">
              {/* Column Header */}
              <div className="flex items-center justify-between pb-3 border-b border-zinc-100 gap-2">
                <span className="text-xs sm:text-[13px] font-bold text-zinc-900 tracking-tight">
                  {col.category}
                </span>
                <span className={`text-[9.5px] font-mono font-bold uppercase tracking-wider px-2 py-0.5 rounded-full border shrink-0 ${col.badgeColor}`}>
                  {col.badge}
                </span>
              </div>

              {/* Column Items */}
              <div className="space-y-3.5">
                {col.items.map((item, iIdx) => {
                  const Icon = item.icon;
                  return (
                    <div
                      key={iIdx}
                      className="flex items-start gap-3 p-2 -m-2 rounded-2xl hover:bg-zinc-50/80 transition-colors duration-150 group cursor-default select-none"
                    >
                      {/* Icon */}
                      <div
                        className={`w-10 h-10 rounded-xl ${item.iconBg} ${item.iconBorder} border ${item.iconColor} flex items-center justify-center shrink-0 shadow-2xs transition-transform group-hover:scale-105 mt-0.5`}
                      >
                        <Icon className="w-4.5 h-4.5" />
                      </div>

                      {/* Text Container: Clear Feature Name & Max 2-Line Active Voice Description */}
                      <div className="space-y-0.5 min-w-0 flex-1">
                        <h3 className="font-bold text-zinc-950 text-xs sm:text-[13.5px] leading-snug group-hover:text-indigo-600 transition-colors">
                          {item.name}
                        </h3>
                        <p className="text-zinc-500 text-xs leading-snug line-clamp-2">
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
