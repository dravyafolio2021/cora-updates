'use client';

import React from 'react';

const brands = [
  { name: 'Hudson Media', icon: 'M12 2L2 7l10 5 10-5-10-5z' },
  { name: 'Babylon Studios', icon: 'M4 4h16v16H4z' },
  { name: 'NONME Agency', icon: 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z' },
  { name: 'Phoenix Estates', icon: 'M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z' },
  { name: 'Nexter Creative', icon: 'M13 2L3 14h9l-1 8 10-12h-9l1-8z' },
  { name: 'Theo Studio', icon: 'M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6' },
  { name: 'London Production', icon: 'M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z' },
  { name: 'Vogue Motion', icon: 'M23 7l-7 5 7 5V7z' },
];

export function BrandTicker() {
  return (
    <section className="py-10 sm:py-12 bg-white relative z-10 overflow-hidden border-b border-zinc-100">
      
      {/* ── Centered Badge on Divider Line (Matching Reference Screenshot) ── */}
      <div className="w-full max-w-[1140px] mx-auto px-4 sm:px-6 mb-8">
        <div className="relative flex justify-center">
          <div className="absolute inset-0 flex items-center" aria-hidden="true">
            <div className="w-full border-t border-zinc-200/70" />
          </div>
          <div className="relative px-4 bg-white">
            <span className="inline-flex items-center gap-1.5 px-4 py-1.5 bg-zinc-100 rounded-full text-zinc-600 text-xs font-medium border border-zinc-200/60 shadow-2xs">
              <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" />
              <span>Trusted by founders and creative business teams</span>
            </span>
          </div>
        </div>
      </div>

      {/* ── Infinite Marquee Brand Ticker ── */}
      <div className="flex w-full overflow-hidden select-none [mask-image:linear-gradient(to_right,transparent,black_15%,black_85%,transparent)]">
        <div className="flex min-w-full shrink-0 items-center justify-around gap-10 sm:gap-16 animate-marquee py-2">
          {brands.concat(brands).map((brand, idx) => (
            <div
              key={idx}
              className="flex items-center gap-2.5 text-zinc-800 font-display font-bold text-sm sm:text-base tracking-tight whitespace-nowrap opacity-60 hover:opacity-100 transition-opacity cursor-default"
            >
              <div className="w-5 h-5 rounded-md bg-zinc-950 text-white flex items-center justify-center p-1 shrink-0">
                <svg
                  className="w-3 h-3 text-white fill-none stroke-current"
                  viewBox="0 0 24 24"
                  strokeWidth="2.2"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                >
                  <path d={brand.icon} />
                </svg>
              </div>
              <span className="font-semibold tracking-[-0.02em]">{brand.name}</span>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
