'use client';

import React from 'react';
import {
  Code,
  Scale,
  Receipt,
  Briefcase,
  BarChart2,
  Sparkles,
  ShieldCheck,
  Building,
  Layers,
  Camera,
  TrendingUp,
  ArrowRight,
} from 'lucide-react';

interface IndustryItem {
  name: string;
  desc: string;
  icon: React.ComponentType<{ className?: string }>;
  iconBg: string;
  link: string;
}

const industries: IndustryItem[] = [
  {
    name: 'Software & Tech Agencies',
    desc: 'Sprint retainers, milestone staging, code vaults & client approval gates.',
    icon: Code,
    iconBg: 'bg-blue-600',
    link: '/use-cases#software-agencies',
  },
  {
    name: 'Law Firms & Legal Practices',
    desc: 'Client retainers, SHA-256 e-contracts, NDAs & secure document vaults.',
    icon: Scale,
    iconBg: 'bg-slate-700',
    link: '/use-cases#legal-practices',
  },
  {
    name: 'Tax & Accounting (CA Firms)',
    desc: '18% GST filing, SAC tracking, audit vaults & automated ledger sync.',
    icon: Receipt,
    iconBg: 'bg-emerald-600',
    link: '/use-cases#tax-accounting',
  },
  {
    name: 'Management & Strategy Consulting',
    desc: 'Diagnostic audits, advisory retainers, fixed scopes & board decks.',
    icon: Briefcase,
    iconBg: 'bg-indigo-600',
    link: '/use-cases#consulting',
  },
  {
    name: 'Digital Marketing & SEO Agencies',
    desc: 'Monthly retainer billing, SEO audits, client portals & ROAS tracking.',
    icon: BarChart2,
    iconBg: 'bg-sky-600',
    link: '/use-cases#marketing-agencies',
  },
  {
    name: 'Brand & UI/UX Design Studios',
    desc: 'Design sprints, Figma handoff portals, asset vaults & revision sign-offs.',
    icon: Sparkles,
    iconBg: 'bg-violet-600',
    link: '/use-cases#design-studios',
  },
  {
    name: 'Financial & Wealth Advisory',
    desc: 'Advisory agreements, compliance tracking, wealth decks & wire/UPI billing.',
    icon: TrendingUp,
    iconBg: 'bg-teal-600',
    link: '/use-cases#financial-advisory',
  },
  {
    name: 'IT & Managed Services (MSPs)',
    desc: 'Monthly recurring retainers, uptime SLAs, ticket escalation & reports.',
    icon: ShieldCheck,
    iconBg: 'bg-emerald-700',
    link: '/use-cases#it-services',
  },
  {
    name: 'Architecture & Engineering Firms',
    desc: '3D CAD stage milestones, contractor draws, blueprint sign-offs & GST splits.',
    icon: Layers,
    iconBg: 'bg-orange-500',
    link: '/use-cases#architecture',
  },
  {
    name: 'Clinics & Healthcare Practices',
    desc: 'Patient intake booking, consent e-signs, records privacy & consultations.',
    icon: ShieldCheck,
    iconBg: 'bg-teal-500',
    link: '/use-cases#healthcare-clinics',
  },
  {
    name: 'Commercial Real Estate & Advisory',
    desc: 'Transaction contracts, property media, MLS AI copy & broker payouts.',
    icon: Building,
    iconBg: 'bg-emerald-600',
    link: '/use-cases#real-estate',
  },
  {
    name: 'Media & Production Studios',
    desc: 'Commercial photo/video scopes, 4K proofing vaults & crew call-sheets.',
    icon: Camera,
    iconBg: 'bg-rose-500',
    link: '/use-cases#commercial-studios',
  },
];

export function IndustryGridSection() {
  return (
    <section className="py-20 sm:py-28 bg-[#FFFFFF] relative z-10 overflow-hidden border-b border-zinc-100">
      <div className="w-full max-w-[1280px] mx-auto px-4 sm:px-6">
        
        {/* ── Section Header ── */}
        <div className="max-w-[800px] mx-auto text-center mb-12 sm:mb-16">
          <div className="inline-flex items-center gap-1.5 text-indigo-600 text-xs font-bold uppercase tracking-widest mb-3">
            <span>FIND YOUR INDUSTRY</span>
          </div>

          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl lg:text-[48px] font-bold text-zinc-950 leading-[1.1] tracking-[-0.03em] mb-6">
            Every industry, one platform
          </h2>

          <div className="flex justify-center">
            <a
              href="https://app.heycora.in/workspace/login"
              className="inline-flex items-center gap-2 bg-zinc-950 text-white hover:bg-zinc-800 px-6 py-3 rounded-xl text-xs sm:text-sm font-semibold shadow-sm transition-all hover:-translate-y-0.5"
            >
              <span>Get started</span>
              <ArrowRight className="w-4 h-4" />
            </a>
          </div>
        </div>

        {/* ── 12-Industry 3x4 Grid (Matching Reference 1:1) ── */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 max-w-[1200px] mx-auto">
          {industries.map((item, idx) => {
            const Icon = item.icon;
            return (
              <a
                key={idx}
                href={item.link}
                className="flex items-start gap-4 p-5 rounded-2xl bg-white hover:bg-zinc-50/80 border border-transparent hover:border-zinc-200/80 transition-all duration-200 group"
              >
                <div className={`w-11 h-11 rounded-2xl ${item.iconBg} text-white flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform`}>
                  <Icon className="w-5 h-5" />
                </div>
                <div className="space-y-1 min-w-0">
                  <h3 className="font-bold text-zinc-950 text-sm sm:text-base tracking-tight group-hover:text-indigo-600 transition-colors">
                    {item.name}
                  </h3>
                  <p className="text-zinc-600 text-xs leading-relaxed line-clamp-2">
                    {item.desc}
                  </p>
                </div>
              </a>
            );
          })}
        </div>

      </div>
    </section>
  );
}
