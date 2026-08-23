'use client';

import React from 'react';
import {
  Camera,
  Film,
  Building,
  Briefcase,
  Layers,
  Palette,
  Sparkles,
  Calendar,
  ShieldCheck,
  Package,
  ShoppingBag,
  FileSpreadsheet,
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
    name: 'Commercial Photography',
    desc: 'Automate client brief intake, rate cards, model release NDAs, and 18% GST invoices.',
    icon: Camera,
    iconBg: 'bg-amber-500',
    link: '/use-cases?industry=photography_studio',
  },
  {
    name: 'Film & Ad Production',
    desc: 'Dispatch 50+ crew call-sheets, manage location permits, and automate vendor payouts.',
    icon: Film,
    iconBg: 'bg-indigo-600',
    link: '/use-cases?industry=film_production',
  },
  {
    name: 'Creative & Ad Agencies',
    desc: 'Scale monthly retainer billing, milestone approvals, and stop out-of-scope creep.',
    icon: Palette,
    iconBg: 'bg-rose-500',
    link: '/use-cases?industry=creative_agency',
  },
  {
    name: 'Real Estate Brokerages',
    desc: 'Coordinate buyer site visits, builder agreements, and track channel partner payouts.',
    icon: Building,
    iconBg: 'bg-emerald-600',
    link: '/use-cases?industry=real_estate',
  },
  {
    name: 'Architecture & Interior Design',
    desc: 'Manage 3D render approvals, contractor draws, advance terms, and GST tax splits.',
    icon: Layers,
    iconBg: 'bg-sky-600',
    link: '/use-cases?industry=architecture',
  },
  {
    name: 'Event & Wedding Planning',
    desc: 'Coordinate venue hold dates, vendor advances, and automated WhatsApp guest schedules.',
    icon: Calendar,
    iconBg: 'bg-purple-600',
    link: '/use-cases?industry=wedding_events',
  },
  {
    name: 'Consulting & Strategy',
    desc: 'Book high-ticket client advisory calls, send SHA-256 agreements, and collect wire/UPI.',
    icon: Briefcase,
    iconBg: 'bg-blue-600',
    link: '/use-cases?industry=consulting',
  },
  {
    name: 'Equipment Rental Houses',
    desc: 'Track camera gear holds, security deposits, damage waivers, and dynamic rental billing.',
    icon: Package,
    iconBg: 'bg-orange-500',
    link: '/use-cases?industry=rental_houses',
  },
  {
    name: 'E-Commerce Product Studios',
    desc: 'High-volume batch SKU shooting, deliverable tracking, and automated corporate GSTIN bills.',
    icon: ShoppingBag,
    iconBg: 'bg-pink-600',
    link: '/use-cases?industry=ecommerce_studios',
  },
  {
    name: 'Talent & Model Agencies',
    desc: 'Manage talent portfolios, client usage rights, advance escrow, and commission math.',
    icon: Sparkles,
    iconBg: 'bg-yellow-500',
    link: '/use-cases?industry=talent_agencies',
  },
  {
    name: 'Legal & CA Advisory',
    desc: 'Standardize client compliance filings, tax consultations, and automated retainer billing.',
    icon: ShieldCheck,
    iconBg: 'bg-teal-600',
    link: '/use-cases?industry=legal_advisory',
  },
  {
    name: 'Solo Freelancers & Editors',
    desc: 'Run a one-person production studio with free forever invoicing and 24/7 AI quoting.',
    icon: FileSpreadsheet,
    iconBg: 'bg-zinc-800',
    link: '/use-cases?industry=solo_creators',
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
