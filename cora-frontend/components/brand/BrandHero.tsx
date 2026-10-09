'use client';

import React, { useState } from 'react';
import { Download, Copy, Check, Sparkles, ShieldCheck, Zap } from 'lucide-react';

interface BrandHeroProps {
  onCopyAll: () => void;
}

export function BrandHero({ onCopyAll }: BrandHeroProps) {
  const [downloading, setDownloading] = useState(false);

  const handleDownloadZip = () => {
    setDownloading(true);
    const svgBundle = `<?xml version="1.0" encoding="UTF-8"?>
<!-- Cora Official Brand Asset Bundle (v4.2) -->
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="800" height="600" fill="none">
  <!-- Background -->
  <rect width="800" height="600" fill="#FFFFFF"/>
  
  <!-- 1. Primary Wordmark Light -->
  <g transform="translate(60, 60)">
    <path d="M26 12C23.5 9.5 20 8 16 8C8.268 8 2 14.268 2 22C2 29.732 8.268 36 16 36C20 36 23.5 34.5 26 32" stroke="#09090B" stroke-width="4.5" stroke-linecap="round"/>
    <circle cx="26" cy="22" r="3" fill="#09090B"/>
    <text x="44" y="32" font-family="Inter, -apple-system, sans-serif" font-size="28" font-weight="800" fill="#09090B" letter-spacing="-0.03em">Cora</text>
  </g>

  <!-- 2. Primary Wordmark Dark -->
  <g transform="translate(420, 40)">
    <rect width="320" height="80" rx="16" fill="#09090B"/>
    <g transform="translate(30, 20)">
      <path d="M26 12C23.5 9.5 20 8 16 8C8.268 8 2 14.268 2 22C2 29.732 8.268 36 16 36C20 36 23.5 34.5 26 32" stroke="#FFFFFF" stroke-width="4.5" stroke-linecap="round"/>
      <circle cx="26" cy="22" r="3" fill="#FFFFFF"/>
      <text x="44" y="32" font-family="Inter, -apple-system, sans-serif" font-size="28" font-weight="800" fill="#FFFFFF" letter-spacing="-0.03em">Cora</text>
    </g>
  </g>

  <!-- 3. AI Co-Founder Lockup -->
  <g transform="translate(60, 200)">
    <path d="M26 12C23.5 9.5 20 8 16 8C8.268 8 2 14.268 2 22C2 29.732 8.268 36 16 36C20 36 23.5 34.5 26 32" stroke="#09090B" stroke-width="4.5" stroke-linecap="round"/>
    <circle cx="26" cy="22" r="3" fill="#09090B"/>
    <text x="44" y="32" font-family="Inter, -apple-system, sans-serif" font-size="28" font-weight="800" fill="#09090B" letter-spacing="-0.03em">Cora</text>
    <rect x="125" y="12" width="36" height="22" rx="6" fill="#09090B"/>
    <text x="134" y="27" font-family="Inter, -apple-system, sans-serif" font-size="11" font-weight="700" fill="#FFFFFF">AI</text>
  </g>

  <!-- 4. Squircle App Icon -->
  <g transform="translate(420, 180)">
    <rect width="90" height="90" rx="24" fill="#09090B"/>
    <path d="M54 32C50.5 28.5 45.5 26 40 26C28.954 26 20 34.954 20 46C20 57.046 28.954 66 40 66C45.5 66 50.5 63.5 54 60" stroke="#FFFFFF" stroke-width="6" stroke-linecap="round"/>
    <circle cx="54" cy="46" r="4.5" fill="#FFFFFF"/>
  </g>
</svg>`;

    const blob = new Blob([svgBundle], { type: 'image/svg+xml' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'cora-brand-assets-v4.svg';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    setTimeout(() => setDownloading(false), 1000);
  };

  return (
    <section className="relative py-14 sm:py-20 overflow-hidden bg-white border-b border-zinc-100">
      
      {/* Background Subtle Radial Matrix */}
      <div 
        className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[680px] h-[360px] rounded-full blur-3xl opacity-30 bg-zinc-200 pointer-events-none -z-10"
      />

      <div className="w-full max-w-[1100px] mx-auto px-4 sm:px-6 text-center space-y-6 sm:space-y-7">
        
        {/* Official Cora Emblem */}
        <div className="flex justify-center">
          <div className="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-zinc-950 text-white flex items-center justify-center shadow-xl border border-zinc-800">
            <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path 
                d="M34 14C31.5 11.5 28 10 24 10C16.268 10 10 16.268 10 24C10 31.732 16.268 38 24 38C28 38 31.5 36.5 34 34" 
                stroke="currentColor" 
                strokeWidth="4.5" 
                strokeLinecap="round"
              />
              <circle cx="34" cy="24" r="3" fill="currentColor" />
            </svg>
          </div>
        </div>

        {/* Text */}
        <div className="space-y-3 max-w-[680px] mx-auto">
          <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-zinc-100 border border-zinc-200 text-[11px] font-mono font-medium text-zinc-700">
            <Sparkles className="w-3.5 h-3.5 text-zinc-950" />
            <span className="uppercase tracking-wider">BRAND ASSET REGISTRY • v4.2</span>
          </div>

          <h1 className="font-display text-4xl xs:text-5xl sm:text-6xl font-bold text-zinc-950 tracking-tight leading-[1.08]">
            Cora Brand Assets
          </h1>

          <p className="text-sm sm:text-base lg:text-lg text-zinc-600 font-normal leading-relaxed">
            Vector logos, design tokens, typography rules, and product captures that define the visual identity of Cora OS for modern agencies.
          </p>
        </div>

        {/* Action Buttons */}
        <div className="flex items-center justify-center flex-wrap gap-3 pt-2">
          <button
            type="button"
            onClick={handleDownloadZip}
            disabled={downloading}
            className="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-zinc-950 text-white text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-sm disabled:opacity-75 cursor-pointer"
          >
            <Download className="w-4 h-4" />
            <span>{downloading ? 'Preparing Kit...' : 'Download Brand Assets (.SVG)'}</span>
          </button>

          <button
            type="button"
            onClick={onCopyAll}
            className="inline-flex items-center gap-2 px-5 py-3.5 rounded-xl border border-zinc-200 bg-white text-zinc-900 text-xs sm:text-sm font-semibold hover:bg-zinc-50 hover:border-zinc-300 transition-all shadow-2xs cursor-pointer"
          >
            <Copy className="w-4 h-4 text-zinc-500" />
            <span>Copy All SVG Vectors</span>
          </button>
        </div>

        {/* Metric Badges */}
        <div className="flex items-center justify-center flex-wrap gap-2 sm:gap-4 pt-3 text-[11px] font-mono text-zinc-500">
          <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-zinc-50 border border-zinc-200/60">
            <Check className="w-3 h-3 text-emerald-600" />
            <span>100% Vector SVG</span>
          </span>
          <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-zinc-50 border border-zinc-200/60">
            <ShieldCheck className="w-3 h-3 text-zinc-700" />
            <span>WCAG AAA Compliant</span>
          </span>
          <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-zinc-50 border border-zinc-200/60">
            <Zap className="w-3 h-3 text-amber-600" />
            <span>Figma &amp; Web Ready</span>
          </span>
        </div>

      </div>
    </section>
  );
}
