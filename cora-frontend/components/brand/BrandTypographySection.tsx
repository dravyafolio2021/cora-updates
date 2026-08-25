'use client';

import React from 'react';

export function BrandTypographySection() {
  return (
    <section className="py-14 sm:py-20 border-b border-zinc-100 bg-white">
      <div className="w-full max-w-[1100px] mx-auto px-4 sm:px-6 space-y-12 sm:space-y-16">
        
        {/* Section Header */}
        <div className="space-y-2">
          <h2 className="font-display text-3xl sm:text-4xl font-bold text-zinc-950 tracking-tight">
            Typography
          </h2>
          <p className="text-sm sm:text-base text-zinc-600 font-normal">
            The precise typographic hierarchy engineered for maximum clarity across studio screens.
          </p>
        </div>

        {/* Font Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
          
          {/* Display & UI Sans: Inter */}
          <div className="rounded-3xl border border-zinc-200 p-6 sm:p-8 bg-zinc-50/50 space-y-6 flex flex-col justify-between">
            <div className="space-y-2">
              <div className="flex items-center justify-between">
                <h3 className="font-display text-2xl font-bold text-zinc-950">
                  Inter / Display Sans
                </h3>
                <span className="text-xs font-mono font-semibold px-2.5 py-1 rounded-lg bg-zinc-200 text-zinc-800">
                  Primary UI &amp; Headings
                </span>
              </div>
              <p className="text-xs text-zinc-500 font-normal leading-relaxed">
                Applied across all primary headlines, buttons, navigation bars, and marketing copy. Clean, geometric, and effortless.
              </p>
            </div>

            <div className="space-y-3 pt-4 border-t border-zinc-200">
              <div className="text-3xl sm:text-4xl font-extrabold text-zinc-950 tracking-tight">
                Aa Bb Cc Dd Ee Ff Gg
              </div>
              <div className="text-base sm:text-lg text-zinc-700 font-medium">
                The autonomous AI co-founder for modern creative studios.
              </div>
              <div className="text-xs font-mono text-zinc-400">
                Weights: Regular (400) • Medium (500) • Semibold (600) • Bold (700) • Extrabold (800)
              </div>
            </div>
          </div>

          {/* Monospace: JetBrains Mono */}
          <div className="rounded-3xl border border-zinc-200 p-6 sm:p-8 bg-zinc-50/50 space-y-6 flex flex-col justify-between">
            <div className="space-y-2">
              <div className="flex items-center justify-between">
                <h3 className="font-mono text-2xl font-bold text-zinc-950">
                  JetBrains Mono
                </h3>
                <span className="text-xs font-mono font-semibold px-2.5 py-1 rounded-lg bg-zinc-200 text-zinc-800">
                  Code, Math &amp; Telemetry
                </span>
              </div>
              <p className="text-xs text-zinc-500 font-normal leading-relaxed">
                Applied to 18% GST tax computations, cryptographic contract seals, timestamps, and call-sheet timings.
              </p>
            </div>

            <div className="space-y-3 pt-4 border-t border-zinc-200 font-mono">
              <div className="text-2xl sm:text-3xl font-bold text-zinc-950">
                0123456789 ₹ $ % @ #
              </div>
              <div className="text-xs sm:text-sm text-zinc-700 space-y-1">
                <div>SAC: 998311 • CGST: 9.00% • SGST: 9.00%</div>
                <div className="text-zinc-500">SHA256: e3b0c44298fc1c149afbf4c8996fb92427ae41...</div>
              </div>
              <div className="text-xs text-zinc-400">
                Weights: Regular (400) • Medium (500) • Bold (700)
              </div>
            </div>
          </div>

        </div>

      </div>
    </section>
  );
}
