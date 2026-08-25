'use client';

import React, { useState } from 'react';
import { Copy, Check } from 'lucide-react';

interface ColorSwatch {
  name: string;
  role: string;
  hex: string;
  rgb: string;
  border?: boolean;
  darkText?: boolean;
}

const NEUTRAL_COLORS: ColorSwatch[] = [
  { name: 'Pure White', role: 'Background Canvas', hex: '#FFFFFF', rgb: '255 255 255', border: true, darkText: true },
  { name: 'Claude Cream', role: 'B-Roll & Video Canvas', hex: '#FBFaf7', rgb: '251 250 247', border: true, darkText: true },
  { name: 'Zinc 50', role: 'Surface Secondary', hex: '#FAFAFA', rgb: '250 250 250', border: true, darkText: true },
  { name: 'Zinc 200', role: 'Border Stroke / Divider', hex: '#E4E4E7', rgb: '228 228 231', darkText: true },
  { name: 'Zinc 500', role: 'Muted Typography', hex: '#71717A', rgb: '113 113 122' },
  { name: 'Zinc 700', role: 'Secondary Body', hex: '#3F3F46', rgb: '63 63 70' },
  { name: 'Zinc 900', role: 'Dark Surface Panel', hex: '#18181B', rgb: '24 24 27' },
  { name: 'Zinc 950', role: 'Primary Dark / Contrast', hex: '#09090B', rgb: '9 9 11' },
  { name: 'Pure Black', role: 'True Pitch Black', hex: '#000000', rgb: '0 0 0' }
];

const ACCENT_COLORS: ColorSwatch[] = [
  { name: 'Active Emerald', role: 'Online / Verified State', hex: '#10B981', rgb: '16 185 129' },
  { name: 'Pending Amber', role: 'Awaiting Action / Sign', hex: '#F59E0B', rgb: '245 158 11' },
  { name: 'Critical Crimson', role: 'Destructive / Overdue', hex: '#EF4444', rgb: '239 68 68' }
];

export function BrandColorsSection() {
  const [copiedCode, setCopiedCode] = useState<string | null>(null);

  const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text);
    setCopiedCode(text);
    setTimeout(() => setCopiedCode(null), 1800);
  };

  return (
    <section className="py-14 sm:py-20 border-b border-zinc-100 bg-white">
      <div className="w-full max-w-[1100px] mx-auto px-4 sm:px-6 space-y-12 sm:space-y-16">
        
        {/* Section Header */}
        <div className="space-y-2">
          <h2 className="font-display text-3xl sm:text-4xl font-bold text-zinc-950 tracking-tight">
            Core Colors
          </h2>
          <p className="text-sm sm:text-base text-zinc-600 font-normal">
            The strict monochromatic and functional neutral ramp that governs all Cora applications and media.
          </p>
        </div>

        {/* ── Neutral Foundation Ramp ── */}
        <div className="space-y-4">
          <div className="flex items-center justify-between">
            <h3 className="font-display text-lg font-bold text-zinc-950">
              Monochromatic Neutral Ramp
            </h3>
            <span className="text-xs font-mono text-zinc-400">Click swatch to copy HEX</span>
          </div>

          <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3.5 sm:gap-4">
            {NEUTRAL_COLORS.map((color, idx) => {
              const isCopied = copiedCode === color.hex;

              return (
                <div
                  key={idx}
                  onClick={() => copyToClipboard(color.hex)}
                  className="rounded-2xl border border-zinc-200/80 p-3 bg-white space-y-3 cursor-pointer hover:border-zinc-400 transition-all shadow-2xs group"
                >
                  <div
                    className={`w-full h-16 sm:h-20 rounded-xl relative flex items-center justify-center ${
                      color.border ? 'border border-zinc-200' : ''
                    }`}
                    style={{ backgroundColor: color.hex }}
                  >
                    <span
                      className={`text-[11px] font-mono font-bold opacity-0 group-hover:opacity-100 transition-opacity px-2 py-1 rounded-md bg-zinc-950/80 text-white backdrop-blur-xs`}
                    >
                      {isCopied ? 'COPIED' : 'COPY'}
                    </span>
                  </div>

                  <div className="space-y-1 text-left">
                    <div className="flex items-center justify-between">
                      <span className="font-display text-xs font-bold text-zinc-950">
                        {color.name}
                      </span>
                      {isCopied && <Check className="w-3 h-3 text-emerald-600" />}
                    </div>
                    <div className="text-[10px] text-zinc-400 font-normal truncate">
                      {color.role}
                    </div>
                    <div className="flex items-center justify-between text-[11px] font-mono text-zinc-600 pt-1 border-t border-zinc-100">
                      <span>{color.hex}</span>
                    </div>
                  </div>
                </div>
              );
            })}
          </div>
        </div>

        {/* ── Functional State Accents ── */}
        <div className="space-y-4">
          <div className="space-y-1">
            <h3 className="font-display text-lg font-bold text-zinc-950">
              Functional State Accents
            </h3>
            <p className="text-xs text-zinc-500 font-mono">
              Restricted exclusively for connection telemetry, cryptographic seals, and alert indicators.
            </p>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
            {ACCENT_COLORS.map((color, idx) => {
              const isCopied = copiedCode === color.hex;

              return (
                <div
                  key={idx}
                  onClick={() => copyToClipboard(color.hex)}
                  className="rounded-2xl border border-zinc-200/80 p-4 bg-white space-y-3 cursor-pointer hover:border-zinc-400 transition-all shadow-2xs group flex items-center gap-4"
                >
                  <div
                    className="w-14 h-14 rounded-xl shrink-0 flex items-center justify-center shadow-xs"
                    style={{ backgroundColor: color.hex }}
                  >
                    <span className="text-[10px] font-mono font-bold text-white opacity-0 group-hover:opacity-100 transition-opacity">
                      {isCopied ? '✓' : 'COPY'}
                    </span>
                  </div>

                  <div className="space-y-0.5 flex-1">
                    <div className="flex items-center justify-between">
                      <span className="font-display text-sm font-bold text-zinc-950">
                        {color.name}
                      </span>
                      {isCopied && <Check className="w-3.5 h-3.5 text-emerald-600" />}
                    </div>
                    <div className="text-xs text-zinc-500 font-normal">
                      {color.role}
                    </div>
                    <div className="text-xs font-mono text-zinc-700 font-semibold pt-1">
                      {color.hex} <span className="text-zinc-400 font-normal text-[11px]">({color.rgb})</span>
                    </div>
                  </div>
                </div>
              );
            })}
          </div>
        </div>

      </div>
    </section>
  );
}
