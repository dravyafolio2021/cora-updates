'use client';

import React from 'react';

export function BrandCobrandingSection() {
  return (
    <section className="py-14 sm:py-20 border-b border-zinc-100 bg-white">
      <div className="w-full max-w-[1100px] mx-auto px-4 sm:px-6 space-y-10 sm:space-y-12">
        
        {/* Section Header */}
        <div className="space-y-2">
          <h2 className="font-display text-3xl sm:text-4xl font-bold text-zinc-950 tracking-tight">
            Co-Branding &amp; Partnerships
          </h2>
          <p className="text-sm sm:text-base text-zinc-600 font-normal">
            Approved lockups for integrated studio partners, hardware vendors, and enterprise integrations.
          </p>
        </div>

        {/* Lockup Row */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          
          {/* Light Lockup */}
          <div className="rounded-3xl border border-zinc-200 p-8 sm:p-12 bg-white flex flex-col items-center justify-center space-y-4 shadow-2xs">
            <div className="flex items-center gap-4 sm:gap-6">
              <span className="font-display text-2xl sm:text-3xl font-extrabold text-zinc-950">
                Cora
              </span>
              <span className="text-zinc-300 text-lg sm:text-xl font-mono select-none">
                &times;
              </span>
              <span className="font-display text-lg sm:text-xl font-bold text-zinc-700 tracking-wider uppercase">
                SONY PRO CINEMA
              </span>
            </div>
            <span className="text-[11px] font-mono text-zinc-400">
              Approved Light Partner Lockup
            </span>
          </div>

          {/* Dark Lockup */}
          <div className="rounded-3xl border border-zinc-800 p-8 sm:p-12 bg-[#0A0D10] text-white flex flex-col items-center justify-center space-y-4 shadow-xl">
            <div className="flex items-center gap-4 sm:gap-6">
              <span className="font-display text-2xl sm:text-3xl font-extrabold text-white">
                Cora
              </span>
              <span className="text-zinc-600 text-lg sm:text-xl font-mono select-none">
                &times;
              </span>
              <span className="font-display text-lg sm:text-xl font-bold text-zinc-300 tracking-wider uppercase">
                RED CHILLIES VFX
              </span>
            </div>
            <span className="text-[11px] font-mono text-zinc-500">
              Approved Dark Partner Lockup
            </span>
          </div>

        </div>

      </div>
    </section>
  );
}
