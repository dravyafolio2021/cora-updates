'use client';

import React from 'react';

const TYPE_SCALE = [
  { level: 'Display Hero', size: '64px / 4rem', weight: 'Bold 700 / Extrabold 800', tracking: '-0.035em', usage: 'Main Hero and Landing Headlines' },
  { level: 'Section Title (H2)', size: '36px / 2.25rem', weight: 'Bold 700', tracking: '-0.025em', usage: 'Major Module Deck and Feature Headers' },
  { level: 'Card Title (H3)', size: '20px / 1.25rem', weight: 'Semibold 600 / Bold 700', tracking: '-0.015em', usage: 'Bento Tiles, Data Tables & Lead Cards' },
  { level: 'Body UI', size: '15px / 0.9375rem', weight: 'Regular 400 / Medium 500', tracking: 'Normal', usage: 'Paragraphs, Scopes, Notes & Descriptions' },
  { level: 'Telemetry & Code', size: '12px / 0.75rem', weight: 'Medium 500 / Bold 700', tracking: '0.05em (Mono)', usage: 'GST Invoices, SHA-256 Hashes, API Tokens' },
  { level: 'Micro Badges', size: '10.5px / 0.656rem', weight: 'Bold 700 (Mono)', tracking: '0.14em (Uppercase)', usage: 'Eyebrows, Category Status & Retainer Tags' },
];

export function BrandTypographySection() {
  return (
    <section className="py-14 sm:py-20 border-b border-zinc-100 bg-white">
      <div className="w-full max-w-[1100px] mx-auto px-4 sm:px-6 space-y-12 sm:space-y-16">
        
        {/* Section Header */}
        <div className="space-y-2">
          <div className="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-zinc-100 border border-zinc-200 text-[10.5px] font-mono font-medium text-zinc-700 uppercase">
            <span>TYPOGRAPHIC SYSTEM</span>
          </div>
          <h2 className="font-display text-3xl sm:text-4xl font-bold text-zinc-950 tracking-tight">
            Typography Hierarchy
          </h2>
          <p className="text-sm sm:text-base text-zinc-600 font-normal">
            The precise typographic pairing engineered for maximum clarity across high-growth agency workspaces.
          </p>
        </div>

        {/* Font Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
          
          {/* Display & UI Sans: Inter */}
          <div className="rounded-3xl border border-zinc-200/90 p-6 sm:p-8 bg-zinc-50/60 space-y-6 flex flex-col justify-between shadow-2xs">
            <div className="space-y-2">
              <div className="flex items-center justify-between">
                <h3 className="font-display text-2xl font-bold text-zinc-950">
                  Inter / Display Sans
                </h3>
                <span className="text-xs font-mono font-semibold px-2.5 py-1 rounded-lg bg-zinc-200/80 text-zinc-900">
                  Primary UI &amp; Headings
                </span>
              </div>
              <p className="text-xs text-zinc-500 font-normal leading-relaxed">
                Applied across all primary headlines, buttons, navigation bars, client portals, and marketing copy. Clean, geometric, and effortless.
              </p>
            </div>

            <div className="space-y-3 pt-4 border-t border-zinc-200/80">
              <div className="text-3xl sm:text-4xl font-extrabold text-zinc-950 tracking-tight">
                Aa Bb Cc Dd Ee Ff Gg
              </div>
              <div className="text-base sm:text-lg text-zinc-700 font-medium">
                The modern operating system for digital agencies and creative consultancies.
              </div>
              <div className="text-xs font-mono text-zinc-400">
                Weights: Regular (400) • Medium (500) • Semibold (600) • Bold (700) • Extrabold (800)
              </div>
            </div>
          </div>

          {/* Monospace: JetBrains Mono */}
          <div className="rounded-3xl border border-zinc-200/90 p-6 sm:p-8 bg-zinc-50/60 space-y-6 flex flex-col justify-between shadow-2xs">
            <div className="space-y-2">
              <div className="flex items-center justify-between">
                <h3 className="font-mono text-2xl font-bold text-zinc-950">
                  JetBrains Mono
                </h3>
                <span className="text-xs font-mono font-semibold px-2.5 py-1 rounded-lg bg-zinc-200/80 text-zinc-900">
                  Code, Math &amp; Telemetry
                </span>
              </div>
              <p className="text-xs text-zinc-500 font-normal leading-relaxed">
                Applied to 18% GST tax computations, SHA-256 cryptographic e-sign seals, milestone timelines, and webhook payloads.
              </p>
            </div>

            <div className="space-y-3 pt-4 border-t border-zinc-200/80 font-mono">
              <div className="text-2xl sm:text-3xl font-bold text-zinc-950">
                0123456789 ₹ $ % @ #
              </div>
              <div className="text-xs sm:text-sm text-zinc-700 space-y-1">
                <div>SAC: 998311 • CGST: 9.00% • SGST: 9.00%</div>
                <div className="text-zinc-500">SHA256: 7f83b1657ff1fc53b92dc18148a1d65dfc2d4b1f...</div>
              </div>
              <div className="text-xs text-zinc-400">
                Weights: Regular (400) • Medium (500) • Bold (700)
              </div>
            </div>
          </div>

        </div>

        {/* Typographic Scale Table */}
        <div className="space-y-4">
          <h3 className="font-display text-lg font-bold text-zinc-950">
            Standard Typographic Scale
          </h3>

          <div className="rounded-2xl border border-zinc-200/90 bg-white overflow-hidden shadow-2xs">
            <div className="overflow-x-auto">
              <table className="w-full text-left text-xs">
                <thead className="bg-zinc-50 border-b border-zinc-200 text-zinc-500 font-mono text-[11px] uppercase">
                  <tr>
                    <th className="px-5 py-3">Level</th>
                    <th className="px-5 py-3">Size</th>
                    <th className="px-5 py-3">Weight</th>
                    <th className="px-5 py-3">Tracking</th>
                    <th className="px-5 py-3">Usage</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-zinc-100 font-sans text-zinc-700">
                  {TYPE_SCALE.map((row, idx) => (
                    <tr key={idx} className="hover:bg-zinc-50/50 transition-colors">
                      <td className="px-5 py-3.5 font-bold text-zinc-950">{row.level}</td>
                      <td className="px-5 py-3.5 font-mono text-zinc-600">{row.size}</td>
                      <td className="px-5 py-3.5 text-zinc-600">{row.weight}</td>
                      <td className="px-5 py-3.5 font-mono text-zinc-500">{row.tracking}</td>
                      <td className="px-5 py-3.5 text-zinc-600">{row.usage}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </section>
  );
}
