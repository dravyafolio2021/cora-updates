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
    title: 'Actionable dashboards',
    desc: 'Click into any card to reassign shoot dates, update rates, or close inquiries. Your dashboard is a live operating command center, not a static report.',
    icon: LayoutDashboard,
  },
  {
    title: 'Crew & shoot overview',
    desc: 'See every crew member’s call-times, equipment assignments, and shoot status on one clean screen. No more frantic WhatsApp group calling.',
    icon: Users,
  },
  {
    title: 'Pre-built Indian templates',
    desc: 'Start immediately with legal templates for commercial agreements, 18% GST calculation breakdowns, model NDAs, and advance policies.',
    icon: FileCheck2,
  },
  {
    title: 'Advanced hold filtering',
    desc: 'Slice your pipeline by client name, shoot hold date, 50% advance status, or city. Instantly build the exact operational view your team needs.',
    icon: Filter,
  },
  {
    title: 'Shareable client portals',
    desc: 'Replace messy PDF attachments with clean live links. Clients view shoot schedules, e-sign agreements, and pay UPI advances with complete privacy.',
    icon: ShieldCheck,
  },
  {
    title: 'Real-time live data',
    desc: 'Always live. Always current. Founders and studio owners see real numbers for collected revenue, pending balances, and GSTR-1 tax liability.',
    icon: Clock,
  },
];

export function ExecutiveConfidenceSection() {
  return (
    <section className="py-20 sm:py-28 bg-[#FFFFFF] relative z-10 overflow-hidden border-b border-zinc-100">
      <div className="w-full max-w-[1280px] mx-auto px-4 sm:px-6">
        
        {/* ── Section Header (Matching Reference 1:1) ── */}
        <div className="max-w-[860px] mx-auto text-center mb-16 sm:mb-20">
          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl lg:text-[48px] font-bold text-zinc-950 leading-[1.1] tracking-[-0.03em]">
            Plus, everything you need <br />
            to lead with confidence
          </h2>
        </div>

        {/* ── 6-Feature 3x2 Grid ── */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 sm:gap-10 lg:gap-12 max-w-[1180px] mx-auto">
          {executiveFeatures.map((feat, idx) => {
            const Icon = feat.icon;
            return (
              <div key={idx} className="flex items-start gap-4 space-y-1">
                <div className="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center shrink-0 mt-1 shadow-2xs">
                  <Icon className="w-5 h-5" />
                </div>
                <div className="space-y-1.5">
                  <h3 className="font-bold text-zinc-950 text-base sm:text-lg tracking-tight">
                    {feat.title}
                  </h3>
                  <p className="text-zinc-600 text-xs sm:text-[13px] leading-relaxed">
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
