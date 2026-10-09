import React from 'react';
import Image from 'next/image';

interface ArtisticHeroBackgroundProps {
  className?: string;
  imageSrc?: string;
  tone?: 'neutral' | 'studio' | 'warm' | 'tools';
}

export function ArtisticHeroBackground({ 
  className = '', 
  imageSrc,
  tone = 'neutral'
}: ArtisticHeroBackgroundProps) {
  // If tone is "tools", render a clean, high-precision SaaS tools grid canvas with ambient spotlight
  if (tone === 'tools') {
    return (
      <div 
        className={`absolute inset-0 pointer-events-none overflow-hidden select-none z-0 ${className}`}
        aria-hidden="true"
      >
        {/* Crisp Base Canvas */}
        <div className="absolute inset-0 bg-gradient-to-b from-[#FAFAF9] via-[#FDFDFD] to-white" />

        {/* Precision Engineering & Tools Grid */}
        <div 
          className="absolute inset-0 opacity-[0.65] pointer-events-none"
          style={{
            backgroundImage: `
              linear-gradient(to right, rgba(228, 228, 231, 0.7) 1px, transparent 1px),
              linear-gradient(to bottom, rgba(228, 228, 231, 0.7) 1px, transparent 1px)
            `,
            backgroundSize: '36px 36px',
            maskImage: 'radial-gradient(ellipse 75% 65% at 50% 30%, #000 35%, transparent 100%)',
            WebkitMaskImage: 'radial-gradient(ellipse 75% 65% at 50% 30%, #000 35%, transparent 100%)',
          }}
        />

        {/* Ambient Focal Glow centered on AI Search Capsule */}
        <div 
          className="absolute top-[28%] left-1/2 -translate-x-1/2 -translate-y-1/2 w-[720px] h-[360px] pointer-events-none"
          style={{
            background: 'radial-gradient(ellipse at center, rgba(99, 102, 241, 0.07) 0%, rgba(244, 244, 245, 0.0) 70%)',
            filter: 'blur(40px)',
          }}
        />

        {/* Silky Bottom Fade Veil to pure white */}
        <div className="absolute inset-x-0 bottom-0 h-32 sm:h-44 bg-gradient-to-t from-white via-white/95 to-transparent pointer-events-none z-[2]" />
      </div>
    );
  }

  const resolvedImageSrc = imageSrc || '/images/cora_pricing_pure_sky.jpg';

  return (
    <div 
      className={`absolute inset-0 pointer-events-none overflow-hidden select-none z-0 ${className}`}
      aria-hidden="true"
    >
      {/* Base Tint Fallback */}
      <div 
        className="absolute inset-0 bg-gradient-to-b from-[#56a2e8] via-[#cae4fc] to-white" 
      />

      {/* Signature Atmospheric Background Artwork */}
      <Image
        src={resolvedImageSrc}
        alt="Cora Atmospheric Background"
        fill
        priority
        className="object-cover object-top opacity-95"
        sizes="100vw"
      />

      {/* Radiant Multi-Stop Overlay */}
      <div
        className="absolute inset-0 pointer-events-none"
        style={{
          background:
            'linear-gradient(180deg, rgba(86, 162, 232, 0.10) 0%, rgba(255, 255, 255, 0.20) 30%, rgba(255, 255, 255, 0.90) 75%, #ffffff 100%)',
        }}
      />

      {/* Silky Smooth Bottom Fade Veil (Melt naturally into page) */}
      <div className="absolute inset-x-0 bottom-0 h-32 sm:h-44 bg-gradient-to-t from-white via-white/95 to-transparent pointer-events-none z-[2]" />
    </div>
  );
}
