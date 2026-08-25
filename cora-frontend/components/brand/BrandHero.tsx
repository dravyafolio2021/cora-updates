'use client';

import React, { useState } from 'react';
import { Download, Copy, Check, Sparkles, Layers } from 'lucide-react';

interface BrandHeroProps {
  onCopyAll: () => void;
}

export function BrandHero({ onCopyAll }: BrandHeroProps) {
  const [downloading, setDownloading] = useState(false);

  const handleDownloadZip = () => {
    setDownloading(true);
    // Create an automated dynamic download bundle containing SVG assets
    const svgContent = `<svg width="200" height="50" viewBox="0 0 200 50" fill="none" xmlns="http://www.w3.org/2000/svg">
  <text x="10" y="36" font-family="Inter, sans-serif" font-size="34" font-weight="800" fill="#09090B" letter-spacing="-0.04em">Cora</text>
  <circle cx="115" cy="18" r="4.5" fill="#09090B"/>
</svg>`;
    const blob = new Blob([svgContent], { type: 'image/svg+xml' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'cora-brand-assets-2026.svg';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    setTimeout(() => setDownloading(false), 1200);
  };

  return (
    <section className="relative pt-12 sm:pt-16 pb-14 sm:pb-20 overflow-hidden bg-white border-b border-zinc-100">
      
      {/* Background Soft Radial Glow */}
      <div 
        className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[340px] rounded-full blur-3xl opacity-30 bg-zinc-200 pointer-events-none -z-10"
      />

      <div className="w-full max-w-[1100px] mx-auto px-4 sm:px-6 text-center space-y-7">
        
        {/* Emblem */}
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
        <div className="space-y-3 max-w-[640px] mx-auto">
          <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-zinc-100 border border-zinc-200 text-xs font-mono font-medium text-zinc-600">
            <Sparkles className="w-3.5 h-3.5 text-zinc-950" />
            <span className="uppercase tracking-wider">BRAND ASSET REGISTRY</span>
          </div>

          <h1 className="font-display text-4xl xs:text-5xl sm:text-6xl font-bold text-zinc-950 tracking-tight leading-[1.08]">
            Cora Brand
          </h1>

          <p className="text-base sm:text-lg text-zinc-600 font-normal leading-relaxed">
            Principles, design tokens, logo assets, and guidelines that shape the visual identity of Cora Studio OS.
          </p>
        </div>

        {/* Actions */}
        <div className="flex items-center justify-center flex-wrap gap-3.5 pt-2">
          <button
            type="button"
            onClick={handleDownloadZip}
            disabled={downloading}
            className="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-zinc-950 text-white text-xs sm:text-sm font-semibold hover:bg-zinc-850 transition-all shadow-sm disabled:opacity-75"
          >
            <Download className="w-4 h-4" />
            <span>{downloading ? 'Downloading Kit...' : 'Download Brand Assets (.SVG)'}</span>
          </button>

          <button
            type="button"
            onClick={onCopyAll}
            className="inline-flex items-center gap-2 px-5 py-3.5 rounded-xl border border-zinc-200 bg-white text-zinc-900 text-xs sm:text-sm font-semibold hover:bg-zinc-50 hover:border-zinc-300 transition-all shadow-2xs"
          >
            <Copy className="w-4 h-4 text-zinc-500" />
            <span>Copy All SVG Vectors</span>
          </button>
        </div>

      </div>
    </section>
  );
}
