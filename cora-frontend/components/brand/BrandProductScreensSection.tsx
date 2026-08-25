'use client';

import React from 'react';
import Image from 'next/image';
import { ExternalLink, Download } from 'lucide-react';

const SCREENS = [
  {
    title: 'AI STUDIO AGENTS',
    image: '/images/bento_ai_seo.jpg',
    category: 'INTELLIGENCE'
  },
  {
    title: 'COMMERCIAL PIPELINE',
    image: '/images/usecase_production_house.jpg',
    category: 'WORKFLOWS'
  },
  {
    title: '18% GST BILLING',
    image: '/images/bento_gst_upi.jpg',
    category: 'FINANCE & UPI'
  },
  {
    title: 'CALL-TIME SCHEDULE',
    image: '/images/homepage_card_calendar.jpg',
    category: 'STUDIO BAYS'
  },
  {
    title: 'E-SIGN LEGAL VAULT',
    image: '/images/bento_esign_seal.jpg',
    category: 'CONTRACTS'
  },
  {
    title: 'CREW & GEAR ROSTER',
    image: '/images/bento_crew_camera.jpg',
    category: 'PRODUCTION'
  }
];

export function BrandProductScreensSection() {
  return (
    <section className="py-14 sm:py-20 bg-white">
      <div className="w-full max-w-[1100px] mx-auto px-4 sm:px-6 space-y-10 sm:space-y-12">
        
        {/* Section Header */}
        <div className="space-y-2">
          <h2 className="font-display text-3xl sm:text-4xl font-bold text-zinc-950 tracking-tight">
            Product Screens
          </h2>
          <p className="text-sm sm:text-base text-zinc-600 font-normal">
            High-definition production captures of the Cora platform across major modules.
          </p>
        </div>

        {/* Screens Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
          {SCREENS.map((screen, idx) => (
            <div
              key={idx}
              className="group rounded-3xl border border-zinc-200 p-3 bg-zinc-50/50 hover:bg-white hover:border-zinc-300 transition-all duration-300 shadow-2xs hover:shadow-lg flex flex-col justify-between space-y-3"
            >
              <div className="relative w-full h-[200px] rounded-2xl overflow-hidden bg-zinc-200 border border-zinc-200/80">
                <Image
                  src={screen.image}
                  alt={screen.title}
                  fill
                  sizes="(max-width: 768px) 100vw, 340px"
                  className="object-cover group-hover:scale-103 transition-transform duration-500"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-zinc-950/40 via-transparent to-transparent pointer-events-none" />
                <div className="absolute top-2.5 left-2.5">
                  <span className="px-2 py-0.5 rounded-md bg-zinc-950/80 backdrop-blur-xs text-white text-[9px] font-mono font-semibold uppercase tracking-wider">
                    {screen.category}
                  </span>
                </div>
              </div>

              <div className="px-1 pb-1 flex items-center justify-between">
                <span className="text-xs font-mono font-bold text-zinc-900 tracking-wider">
                  {screen.title}
                </span>
                <a
                  href={screen.image}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="w-7 h-7 rounded-lg border border-zinc-200 bg-white flex items-center justify-center text-zinc-500 hover:text-zinc-950 hover:bg-zinc-100 transition-colors shadow-2xs"
                  aria-label={`View ${screen.title}`}
                >
                  <ExternalLink className="w-3.5 h-3.5" />
                </a>
              </div>
            </div>
          ))}
        </div>

      </div>
    </section>
  );
}
