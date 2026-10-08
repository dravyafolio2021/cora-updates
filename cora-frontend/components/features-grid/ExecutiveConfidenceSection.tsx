'use client';

import React from 'react';
import {
  LayoutDashboard,
  Users,
  FileCheck2,
  Filter,
  ShieldCheck,
  Clock,
} from 'lucide-react';

interface FeatureItem {
  title: string;
  desc: string;
  icon: React.ComponentType<{ className?: string }>;
}

const executiveFeatures: FeatureItem[] = [
  {
    title: 'Check your business at a glance',
    desc: 'View enquiries, schedules, and payments. Make updates right here.',
    icon: LayoutDashboard,
  },
  {
    title: 'See who’s doing what',
    desc: 'Check team schedules, assignments, and work progress.',
    icon: Users,
  },
  {
    title: 'Start with ready-made templates',
    desc: 'Use agreements, NDAs, and payment terms for client work.',
    icon: FileCheck2,
  },
  {
    title: 'Find what needs attention',
    desc: 'Filter work by client, date, or payment status.',
    icon: Filter,
  },
  {
    title: 'Keep clients in the loop',
    desc: 'Share updates, collect approvals, and send payment links.',
    icon: ShieldCheck,
  },
  {
    title: 'Know what’s paid and pending',
    desc: 'Check collected payments, unpaid invoices, and GST totals.',
    icon: Clock,
  },
];

export function ExecutiveConfidenceSection() {
  return (
    <section className="py-16 sm:py-[100px] bg-[#FFFFFF] relative z-10 overflow-hidden border-b border-zinc-100">
      <div className="w-full max-w-[1280px] mx-auto px-4 sm:px-6">
        
        {/* ── Section Header (Strictly Centered Stack with Active Headline & Subtitle) ── */}
        <div className="max-w-[760px] mx-auto text-center mb-12 sm:mb-16">
          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl lg:text-[48px] font-bold text-zinc-950 leading-[1.12] tracking-[-0.03em] mb-3.5">
            Stay on top of your agency.
          </h2>
          <p className="text-zinc-600 text-sm sm:text-base md:text-lg font-normal leading-relaxed max-w-[600px] mx-auto">
            See what’s due, who’s doing it, and what’s still unpaid.
          </p>
        </div>

        {/* ── 6-Feature 3x2 Grid (Active Voice & Max 2-Line Descriptions) ── */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 sm:gap-10 lg:gap-12 max-w-[1180px] mx-auto">
          {executiveFeatures.map((feat, idx) => {
            const Icon = feat.icon;
            return (
              <div key={idx} className="flex items-start gap-4">
                <div className="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
                  <Icon className="w-5 h-5" />
                </div>
                <div className="space-y-1 min-w-0 flex-1">
                  <h3 className="font-bold text-zinc-950 text-sm sm:text-base tracking-tight truncate">
                    {feat.title}
                  </h3>
                  <p className="text-zinc-500 text-xs sm:text-[13px] leading-relaxed line-clamp-2">
                    {feat.desc}
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
