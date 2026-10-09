'use client';

import React from 'react';
import Image from 'next/image';
import { ExternalLink, Download } from 'lucide-react';

const SCREENS = [
  {
    title: 'AGENCY COMMAND CENTER',
    image: '/images/about_story_full_bg_desktop.jpg',
    category: 'OVERVIEW',
    desc: 'Unified client intake, active milestones, and high-margin ops.'
  },
  {
    title: 'LEAD INTAKE & DISCOVERY CRM',
    image: '/images/homepage_card_crm.jpg',
    category: 'GROWTH',
    desc: 'Lead scoring, automated client triage, and scope scoping.'
  },
  {
    title: '18% AUTOMATED GST BILLING',
    image: '/images/homepage_card_gst.jpg',
    category: 'FINANCE & UPI',
    desc: 'SAC-compliant tax invoicing, instant UPI QR, and reconciliation.'
  },
  {
    title: 'SHA-256 E-SIGN CONTRACT VAULT',
    image: '/images/homepage_card_legal.jpg',
    category: 'LEGAL & COMPLIANCE',
    desc: 'Legally binding retainer agreements and scope sign-offs.'
  },
  {
    title: 'RETAINER & MILESTONE TIMELINES',
    image: '/images/homepage_card_calendar.jpg',
    category: 'DELIVERY',
    desc: 'Automated milestone progress and billing triggers.'
  },
  {
    title: 'WHATSAPP CLIENT DISPATCH',
    image: '/images/homepage_card_whatsapp.jpg',
    category: 'COMMUNICATION',
    desc: 'Real-time client briefs, approvals, and payment nudges.'
  },
  {
    title: 'FINANCIAL COMMAND CENTER',
    image: '/images/homepage_card_finance.jpg',
    category: 'MARGINS & CASHFLOW',
    desc: 'Profitability tracking and recurring retainer metrics.'
  },
  {
    title: 'AI CO-FOUNDERS MATRIX',
    image: '/images/cora_agent_sales.jpg',
    category: 'FRONTIER AI',
    desc: 'Autonomous inbound qualification and instant proposal drafts.'
  }
];

export function BrandProductScreensSection() {
  return (
    <section className="py-14 sm:py-20 bg-white">
      <div className="w-full max-w-[1100px] mx-auto px-4 sm:px-6 space-y-10 sm:space-y-12">
        
        {/* Section Header */}
        <div className="space-y-2">
          <div className="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-zinc-100 border border-zinc-200 text-[10.5px] font-mono font-medium text-zinc-700 uppercase">
            <span>OFFICIAL PRODUCT CAPTURES</span>
          </div>
          <h2 className="font-display text-3xl sm:text-4xl font-bold text-zinc-950 tracking-tight">
            Production Interface Screens
          </h2>
          <p className="text-sm sm:text-base text-zinc-600 font-normal">
            High-definition production captures of the Cora platform across major agency operating modules.
          </p>
        </div>

        {/* Screens Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
          {SCREENS.map((screen, idx) => (
            <div
              key={idx}
              className="group rounded-2xl border border-zinc-200/90 p-3 bg-zinc-50/50 hover:bg-white hover:border-zinc-300 transition-all duration-300 shadow-2xs hover:shadow-lg flex flex-col justify-between space-y-3"
            >
              <div className="relative w-full h-[180px] rounded-xl overflow-hidden bg-zinc-100 border border-zinc-200/80">
                <Image
                  src={screen.image}
                  alt={screen.title}
                  fill
                  sizes="(max-width: 768px) 100vw, 280px"
                  className="object-cover object-top group-hover:scale-103 transition-transform duration-500"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-zinc-950/60 via-transparent to-transparent pointer-events-none" />
                <div className="absolute top-2 left-2">
                  <span className="px-2 py-0.5 rounded-md bg-zinc-950/80 backdrop-blur-xs text-white text-[9px] font-mono font-semibold uppercase tracking-wider">
                    {screen.category}
                  </span>
                </div>
              </div>

              <div className="px-1 pb-1 space-y-1">
                <div className="flex items-center justify-between gap-1">
                  <span className="text-[11.5px] font-mono font-bold text-zinc-900 tracking-tight truncate">
                    {screen.title}
                  </span>
                  <a
                    href={screen.image}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="w-6 h-6 rounded-md border border-zinc-200 bg-white flex items-center justify-center text-zinc-500 hover:text-zinc-950 hover:bg-zinc-100 transition-colors shadow-2xs shrink-0 cursor-pointer"
                    aria-label={`View ${screen.title}`}
                    title="Open Full Resolution"
                  >
                    <ExternalLink className="w-3 h-3" />
                  </a>
                </div>
                <p className="text-[11px] text-zinc-500 font-normal leading-snug line-clamp-2">
                  {screen.desc}
                </p>
              </div>
            </div>
          ))}
        </div>

      </div>
    </section>
  );
}
