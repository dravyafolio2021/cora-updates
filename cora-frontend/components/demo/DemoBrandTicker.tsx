'use client';

import React from 'react';

const BRANDS = [
  { name: 'Red Chillies VFX', label: 'RED CHILLIES' },
  { name: 'Dharma 2.0', label: 'DHARMA 2.0' },
  { name: 'YRF Studios', label: 'YASH RAJ FILMS' },
  { name: 'Condé Nast Studio', label: 'CONDÉ NAST' },
  { name: 'Ogilvy Production', label: 'OGILVY STUDIO' },
  { name: 'Viacom18 Studios', label: 'VIACOM18' },
  { name: 'Netflix Creative Hub', label: 'NETFLIX CREATIVE' }
];

export function DemoBrandTicker() {
  return (
    <section className="w-full py-8 border-y border-zinc-100 bg-white">
      <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        <div className="flex flex-col md:flex-row items-center justify-between gap-6">
          
          <span className="text-[11px] font-mono font-semibold uppercase tracking-widest text-zinc-400 shrink-0">
            TRUSTED BY THE BEST
          </span>

          <div className="flex items-center flex-wrap justify-center md:justify-end gap-6 sm:gap-10 text-zinc-400">
            {BRANDS.map((brand, idx) => (
              <span
                key={idx}
                className="font-display font-bold text-xs sm:text-sm tracking-wider uppercase text-zinc-400 hover:text-zinc-950 transition-colors select-none"
              >
                {brand.label}
              </span>
            ))}
          </div>

        </div>
      </div>
    </section>
  );
}
