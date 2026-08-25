'use client';

import React, { useState } from 'react';
import { Copy, Check, Sparkles } from 'lucide-react';

export function BrandLogosSection() {
  const [copiedId, setCopiedId] = useState<string | null>(null);

  const handleCopySvg = (id: string, svgString: string) => {
    navigator.clipboard.writeText(svgString);
    setCopiedId(id);
    setTimeout(() => setCopiedId(null), 2000);
  };

  const FULL_LOCKUP_LIGHT_SVG = `<svg width="180" height="44" viewBox="0 0 180 44" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M26 12C23.5 9.5 20 8 16 8C8.268 8 2 14.268 2 22C2 29.732 8.268 36 16 36C20 36 23.5 34.5 26 32" stroke="#09090B" stroke-width="4.5" stroke-linecap="round"/>
  <circle cx="26" cy="22" r="3" fill="#09090B"/>
  <text x="42" y="32" font-family="Inter, sans-serif" font-size="28" font-weight="800" fill="#09090B" letter-spacing="-0.04em">Cora</text>
</svg>`;

  const FULL_LOCKUP_DARK_SVG = `<svg width="180" height="44" viewBox="0 0 180 44" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M26 12C23.5 9.5 20 8 16 8C8.268 8 2 14.268 2 22C2 29.732 8.268 36 16 36C20 36 23.5 34.5 26 32" stroke="#FFFFFF" stroke-width="4.5" stroke-linecap="round"/>
  <circle cx="26" cy="22" r="3" fill="#FFFFFF"/>
  <text x="42" y="32" font-family="Inter, sans-serif" font-size="28" font-weight="800" fill="#FFFFFF" letter-spacing="-0.04em">Cora</text>
</svg>`;

  const SYMBOL_SVG_LIGHT = `<svg width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M30 14C27.5 11.5 24 10 20 10C12.268 10 6 16.268 6 24C6 31.732 12.268 38 20 38C24 38 27.5 36.5 30 34" stroke="#09090B" stroke-width="4" stroke-linecap="round"/>
  <circle cx="30" cy="24" r="2.5" fill="#09090B"/>
</svg>`;

  const SYMBOL_SVG_DARK = `<svg width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M30 14C27.5 11.5 24 10 20 10C12.268 10 6 16.268 6 24C6 31.732 12.268 38 20 38C24 38 27.5 36.5 30 34" stroke="#FFFFFF" stroke-width="4" stroke-linecap="round"/>
  <circle cx="30" cy="24" r="2.5" fill="#FFFFFF"/>
</svg>`;

  const AI_LOCKUP_LIGHT = `<svg width="220" height="44" viewBox="0 0 220 44" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M26 12C23.5 9.5 20 8 16 8C8.268 8 2 14.268 2 22C2 29.732 8.268 36 16 36C20 36 23.5 34.5 26 32" stroke="#09090B" stroke-width="4.5" stroke-linecap="round"/>
  <circle cx="26" cy="22" r="3" fill="#09090B"/>
  <text x="42" y="32" font-family="Inter, sans-serif" font-size="28" font-weight="800" fill="#09090B" letter-spacing="-0.04em">Cora</text>
  <rect x="115" y="11" width="36" height="22" rx="6" fill="#09090B"/>
  <text x="123" y="27" font-family="Inter, sans-serif" font-size="12" font-weight="700" fill="#FFFFFF">AI</text>
</svg>`;

  const AI_LOCKUP_DARK = `<svg width="220" height="44" viewBox="0 0 220 44" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M26 12C23.5 9.5 20 8 16 8C8.268 8 2 14.268 2 22C2 29.732 8.268 36 16 36C20 36 23.5 34.5 26 32" stroke="#FFFFFF" stroke-width="4.5" stroke-linecap="round"/>
  <circle cx="26" cy="22" r="3" fill="#FFFFFF"/>
  <text x="42" y="32" font-family="Inter, sans-serif" font-size="28" font-weight="800" fill="#FFFFFF" letter-spacing="-0.04em">Cora</text>
  <rect x="115" y="11" width="36" height="22" rx="6" fill="#FFFFFF"/>
  <text x="123" y="27" font-family="Inter, sans-serif" font-size="12" font-weight="700" fill="#09090B">AI</text>
</svg>`;

  return (
    <section className="py-14 sm:py-20 border-b border-zinc-100 bg-white">
      <div className="w-full max-w-[1100px] mx-auto px-4 sm:px-6 space-y-12 sm:space-y-16">
        
        {/* Section Title */}
        <div className="space-y-2">
          <h2 className="font-display text-3xl sm:text-4xl font-bold text-zinc-950 tracking-tight">
            Logos
          </h2>
          <p className="text-sm sm:text-base text-zinc-600 font-normal">
            The core visual identity and emblems of the Cora platform.
          </p>
        </div>

        {/* ── Subsection 1: Full Lockup ── */}
        <div className="space-y-4">
          <h3 className="font-display text-lg font-bold text-zinc-950">
            Full Lockup
          </h3>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
            {/* Light Box */}
            <div className="relative rounded-2xl bg-white border border-zinc-200 p-10 sm:p-14 flex items-center justify-center shadow-2xs group min-h-[160px]">
              <div className="flex items-center gap-3">
                <div className="w-8 h-8 rounded-full border-2 border-zinc-950 flex items-center justify-center">
                  <span className="w-2 h-2 rounded-full bg-zinc-950" />
                </div>
                <span className="font-display text-3xl font-extrabold tracking-tight text-zinc-950">
                  Cora
                </span>
              </div>

              <button
                type="button"
                onClick={() => handleCopySvg('lockup-light', FULL_LOCKUP_LIGHT_SVG)}
                className="absolute top-3.5 right-3.5 px-3 py-1.5 rounded-lg border border-zinc-200 bg-white text-xs font-mono text-zinc-700 hover:bg-zinc-50 flex items-center gap-1.5 transition-all shadow-2xs"
              >
                {copiedId === 'lockup-light' ? (
                  <>
                    <Check className="w-3.5 h-3.5 text-emerald-600" />
                    <span>Copied SVG</span>
                  </>
                ) : (
                  <>
                    <Copy className="w-3.5 h-3.5 text-zinc-400" />
                    <span>Copy SVG</span>
                  </>
                )}
              </button>
            </div>

            {/* Dark Box */}
            <div className="relative rounded-2xl bg-[#0A0D10] border border-zinc-800 p-10 sm:p-14 flex items-center justify-center shadow-xl group min-h-[160px]">
              <div className="flex items-center gap-3">
                <div className="w-8 h-8 rounded-full border-2 border-white flex items-center justify-center">
                  <span className="w-2 h-2 rounded-full bg-white" />
                </div>
                <span className="font-display text-3xl font-extrabold tracking-tight text-white">
                  Cora
                </span>
              </div>

              <button
                type="button"
                onClick={() => handleCopySvg('lockup-dark', FULL_LOCKUP_DARK_SVG)}
                className="absolute top-3.5 right-3.5 px-3 py-1.5 rounded-lg border border-zinc-700 bg-zinc-900 text-xs font-mono text-zinc-200 hover:bg-zinc-800 flex items-center gap-1.5 transition-all shadow-2xs"
              >
                {copiedId === 'lockup-dark' ? (
                  <>
                    <Check className="w-3.5 h-3.5 text-emerald-400" />
                    <span>Copied SVG</span>
                  </>
                ) : (
                  <>
                    <Copy className="w-3.5 h-3.5 text-zinc-400" />
                    <span>Copy SVG</span>
                  </>
                )}
              </button>
            </div>
          </div>
        </div>

        {/* ── Subsection 2: Symbol ── */}
        <div className="space-y-4">
          <h3 className="font-display text-lg font-bold text-zinc-950">
            Symbol &amp; App Icon
          </h3>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
            {/* Light Symbol */}
            <div className="relative rounded-2xl bg-white border border-zinc-200 p-10 flex items-center justify-center shadow-2xs min-h-[140px]">
              <div className="w-12 h-12 rounded-2xl bg-zinc-100 border border-zinc-200 flex items-center justify-center">
                <div className="w-6 h-6 rounded-full border-2 border-zinc-950 flex items-center justify-center">
                  <span className="w-1.5 h-1.5 rounded-full bg-zinc-950" />
                </div>
              </div>

              <button
                type="button"
                onClick={() => handleCopySvg('symbol-light', SYMBOL_SVG_LIGHT)}
                className="absolute top-3.5 right-3.5 px-3 py-1.5 rounded-lg border border-zinc-200 bg-white text-xs font-mono text-zinc-700 hover:bg-zinc-50 flex items-center gap-1.5 transition-all shadow-2xs"
              >
                {copiedId === 'symbol-light' ? (
                  <>
                    <Check className="w-3.5 h-3.5 text-emerald-600" />
                    <span>Copied SVG</span>
                  </>
                ) : (
                  <>
                    <Copy className="w-3.5 h-3.5 text-zinc-400" />
                    <span>Copy SVG</span>
                  </>
                )}
              </button>
            </div>

            {/* Dark Symbol */}
            <div className="relative rounded-2xl bg-[#0A0D10] border border-zinc-800 p-10 flex items-center justify-center shadow-xl min-h-[140px]">
              <div className="w-12 h-12 rounded-2xl bg-zinc-900 border border-zinc-700 flex items-center justify-center">
                <div className="w-6 h-6 rounded-full border-2 border-white flex items-center justify-center">
                  <span className="w-1.5 h-1.5 rounded-full bg-white" />
                </div>
              </div>

              <button
                type="button"
                onClick={() => handleCopySvg('symbol-dark', SYMBOL_SVG_DARK)}
                className="absolute top-3.5 right-3.5 px-3 py-1.5 rounded-lg border border-zinc-700 bg-zinc-900 text-xs font-mono text-zinc-200 hover:bg-zinc-800 flex items-center gap-1.5 transition-all shadow-2xs"
              >
                {copiedId === 'symbol-dark' ? (
                  <>
                    <Check className="w-3.5 h-3.5 text-emerald-400" />
                    <span>Copied SVG</span>
                  </>
                ) : (
                  <>
                    <Copy className="w-3.5 h-3.5 text-zinc-400" />
                    <span>Copy SVG</span>
                  </>
                )}
              </button>
            </div>
          </div>
        </div>

        {/* ── Subsection 3: Cora AI Intelligence Layer ── */}
        <div className="space-y-4">
          <div className="space-y-1">
            <h3 className="font-display text-lg font-bold text-zinc-950">
              Cora AI (Intelligence Layer)
            </h3>
            <p className="text-xs text-zinc-500 font-mono">
              The autonomous co-founder and reasoning intelligence layer identity.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
            {/* AI Light */}
            <div className="relative rounded-2xl bg-white border border-zinc-200 p-10 flex items-center justify-center shadow-2xs min-h-[150px]">
              <div className="flex items-center gap-3">
                <div className="w-7 h-7 rounded-full border-2 border-zinc-950 flex items-center justify-center">
                  <span className="w-1.5 h-1.5 rounded-full bg-zinc-950" />
                </div>
                <span className="font-display text-2xl font-extrabold tracking-tight text-zinc-950">
                  Cora
                </span>
                <span className="px-2 py-0.5 rounded-md bg-zinc-950 text-white text-[11px] font-mono font-bold tracking-wider">
                  AI
                </span>
              </div>

              <button
                type="button"
                onClick={() => handleCopySvg('ai-light', AI_LOCKUP_LIGHT)}
                className="absolute top-3.5 right-3.5 px-3 py-1.5 rounded-lg border border-zinc-200 bg-white text-xs font-mono text-zinc-700 hover:bg-zinc-50 flex items-center gap-1.5 transition-all shadow-2xs"
              >
                {copiedId === 'ai-light' ? (
                  <>
                    <Check className="w-3.5 h-3.5 text-emerald-600" />
                    <span>Copied SVG</span>
                  </>
                ) : (
                  <>
                    <Copy className="w-3.5 h-3.5 text-zinc-400" />
                    <span>Copy SVG</span>
                  </>
                )}
              </button>
            </div>

            {/* AI Dark */}
            <div className="relative rounded-2xl bg-[#0A0D10] border border-zinc-800 p-10 flex items-center justify-center shadow-xl min-h-[150px]">
              <div className="flex items-center gap-3">
                <div className="w-7 h-7 rounded-full border-2 border-white flex items-center justify-center">
                  <span className="w-1.5 h-1.5 rounded-full bg-white" />
                </div>
                <span className="font-display text-2xl font-extrabold tracking-tight text-white">
                  Cora
                </span>
                <span className="px-2 py-0.5 rounded-md bg-white text-zinc-950 text-[11px] font-mono font-bold tracking-wider">
                  AI
                </span>
              </div>

              <button
                type="button"
                onClick={() => handleCopySvg('ai-dark', AI_LOCKUP_DARK)}
                className="absolute top-3.5 right-3.5 px-3 py-1.5 rounded-lg border border-zinc-700 bg-zinc-900 text-xs font-mono text-zinc-200 hover:bg-zinc-800 flex items-center gap-1.5 transition-all shadow-2xs"
              >
                {copiedId === 'ai-dark' ? (
                  <>
                    <Check className="w-3.5 h-3.5 text-emerald-400" />
                    <span>Copied SVG</span>
                  </>
                ) : (
                  <>
                    <Copy className="w-3.5 h-3.5 text-zinc-400" />
                    <span>Copy SVG</span>
                  </>
                )}
              </button>
            </div>
          </div>
        </div>

      </div>
    </section>
  );
}
