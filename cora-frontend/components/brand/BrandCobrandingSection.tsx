'use client';

import React from 'react';

const PARTNERS = [
  {
    name: 'FRAMER',
    role: 'Design System & Landing Pages',
    theme: 'light'
  },
  {
    name: 'STRIPE',
    role: 'Global Retainer Merchant & Invoicing',
    theme: 'dark'
  },
  {
    name: 'WEBFLOW',
    role: 'Agency Client Portals & CMS',
    theme: 'light'
  },
  {
    name: 'OPENAI',
    role: 'Frontier AI Reasoning Partner',
    theme: 'dark'
  }
];

export function BrandCobrandingSection() {
  return (
    <section className="py-14 sm:py-20 border-b border-zinc-100 bg-white">
      <div className="w-full max-w-[1100px] mx-auto px-4 sm:px-6 space-y-10 sm:space-y-12">
        
        {/* Section Header */}
        <div className="space-y-2">
          <div className="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-zinc-100 border border-zinc-200 text-[10.5px] font-mono font-medium text-zinc-700 uppercase">
            <span>PARTNERSHIPS &amp; INTEGRATIONS</span>
          </div>
          <h2 className="font-display text-3xl sm:text-4xl font-bold text-zinc-950 tracking-tight">
            Co-Branding &amp; Partnerships
          </h2>
          <p className="text-sm sm:text-base text-zinc-600 font-normal">
            Approved lockups for integrated agency partners, developer platforms, and technology ecosystems.
          </p>
        </div>

        {/* Lockup Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">
          {PARTNERS.map((partner, idx) => (
            <div
              key={idx}
              className={`rounded-3xl p-8 sm:p-10 flex flex-col items-center justify-center space-y-3 transition-all ${
                partner.theme === 'dark'
                  ? 'bg-[#09090B] text-white border border-zinc-800 shadow-xl'
                  : 'bg-white text-zinc-950 border border-zinc-200/90 shadow-2xs'
              }`}
            >
              <div className="flex items-center gap-4 sm:gap-5 select-none">
                <span className={`font-display text-2xl sm:text-3xl font-extrabold ${partner.theme === 'dark' ? 'text-white' : 'text-zinc-950'}`}>
                  Cora
                </span>
                <span className={`text-lg sm:text-xl font-mono ${partner.theme === 'dark' ? 'text-zinc-600' : 'text-zinc-300'}`}>
                  &times;
                </span>
                <span className={`font-display text-lg sm:text-xl font-bold tracking-wider uppercase ${partner.theme === 'dark' ? 'text-zinc-300' : 'text-zinc-800'}`}>
                  {partner.name}
                </span>
              </div>
              <span className={`text-[11px] font-mono ${partner.theme === 'dark' ? 'text-zinc-500' : 'text-zinc-400'}`}>
                {partner.role}
              </span>
            </div>
          ))}
        </div>

      </div>
    </section>
  );
}
