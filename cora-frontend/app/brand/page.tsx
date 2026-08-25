'use client';

import React, { useState } from 'react';
import { BrandHero } from '@/components/brand/BrandHero';
import { BrandLogosSection } from '@/components/brand/BrandLogosSection';
import { BrandColorsSection } from '@/components/brand/BrandColorsSection';
import { BrandTypographySection } from '@/components/brand/BrandTypographySection';
import { BrandLogoGuidanceSection } from '@/components/brand/BrandLogoGuidanceSection';
import { BrandCobrandingSection } from '@/components/brand/BrandCobrandingSection';
import { BrandProductScreensSection } from '@/components/brand/BrandProductScreensSection';
import { Check } from 'lucide-react';

export default function BrandPage() {
  const [toastMessage, setToastMessage] = useState<string | null>(null);

  const triggerToast = (msg: string) => {
    setToastMessage(msg);
    setTimeout(() => setToastMessage(null), 2400);
  };

  const handleCopyAll = () => {
    const allSvgs = `<!-- Cora Primary Logo Light -->
<svg width="180" height="44" viewBox="0 0 180 44" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M26 12C23.5 9.5 20 8 16 8C8.268 8 2 14.268 2 22C2 29.732 8.268 36 16 36C20 36 23.5 34.5 26 32" stroke="#09090B" stroke-width="4.5" stroke-linecap="round"/>
  <circle cx="26" cy="22" r="3" fill="#09090B"/>
  <text x="42" y="32" font-family="Inter, sans-serif" font-size="28" font-weight="800" fill="#09090B" letter-spacing="-0.04em">Cora</text>
</svg>

<!-- Cora Symbol Dark -->
<svg width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M30 14C27.5 11.5 24 10 20 10C12.268 10 6 16.268 6 24C6 31.732 12.268 38 20 38C24 38 27.5 36.5 30 34" stroke="#FFFFFF" stroke-width="4" stroke-linecap="round"/>
  <circle cx="30" cy="24" r="2.5" fill="#FFFFFF"/>
</svg>`;

    navigator.clipboard.writeText(allSvgs);
    triggerToast('All Cora SVG vector logos copied to clipboard!');
  };

  return (
    <main className="min-h-screen bg-white text-zinc-950 flex flex-col selection:bg-zinc-950 selection:text-white">
      
      {/* Toast Notification */}
      {toastMessage && (
        <div className="fixed bottom-6 right-6 z-50 px-4 py-3 rounded-xl bg-zinc-950 text-white text-xs font-mono font-medium shadow-2xl flex items-center gap-2 border border-zinc-800 animate-in fade-in slide-in-from-bottom-2 duration-200">
          <Check className="w-4 h-4 text-emerald-400" />
          <span>{toastMessage}</span>
        </div>
      )}

      {/* Hero */}
      <BrandHero onCopyAll={handleCopyAll} />

      {/* Logos & Emblems */}
      <BrandLogosSection />

      {/* Core Colors & Design Tokens */}
      <BrandColorsSection />

      {/* Typography Hierarchy */}
      <BrandTypographySection />

      {/* Logo Guidance & Clear Space */}
      <BrandLogoGuidanceSection />

      {/* Co-Branding & Partnerships */}
      <BrandCobrandingSection />

      {/* Product Screen Captures */}
      <BrandProductScreensSection />

    </main>
  );
}
