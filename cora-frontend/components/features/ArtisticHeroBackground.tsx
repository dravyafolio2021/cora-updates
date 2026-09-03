import React from 'react';
import Image from 'next/image';

interface ArtisticHeroBackgroundProps {
  className?: string;
  imageSrc?: string;
  tone?: 'neutral' | 'studio' | 'warm';
}

export function ArtisticHeroBackground({ 
  className = '', 
  imageSrc,
  tone = 'neutral'
}: ArtisticHeroBackgroundProps) {
  const isStudio = tone === 'studio' || tone === 'warm';
  const resolvedImageSrc = imageSrc || (isStudio ? '/images/cora_pricing_hero_studio.jpg' : '/images/cora_pricing_pure_sky.jpg');

  return (
    <div 
      className={`absolute inset-0 pointer-events-none overflow-hidden select-none z-0 ${className}`}
      aria-hidden="true"
    >
      {/* Base Tint Fallback */}
      <div 
        className={`absolute inset-0 ${
          isStudio 
            ? 'bg-gradient-to-b from-[#FAF8F5] via-[#F5F2EC] to-white' 
            : 'bg-gradient-to-b from-[#56a2e8] via-[#cae4fc] to-white'
        }`} 
      />

      {/* Signature Atmospheric Background Artwork */}
      <Image
        src={resolvedImageSrc}
        alt="Cora Atmospheric Background"
        fill
        priority
        className={`object-cover ${isStudio ? 'object-center opacity-80' : 'object-top'}`}
        sizes="100vw"
      />

      {/* Radiant Multi-Stop Overlay */}
      <div
        className="absolute inset-0 pointer-events-none"
        style={{
          background: isStudio
            ? 'linear-gradient(180deg, rgba(250, 248, 245, 0.45) 0%, rgba(250, 248, 245, 0.55) 40%, rgba(255, 255, 255, 0.90) 80%, #ffffff 100%)'
            : 'linear-gradient(180deg, rgba(86, 162, 232, 0.14) 0%, rgba(255, 255, 255, 0.20) 35%, rgba(255, 255, 255, 0.88) 75%, #ffffff 100%)',
        }}
      />

      {/* Silky Smooth Bottom Fade Veil (Melt naturally into page) */}
      <div className="absolute inset-x-0 bottom-0 h-32 sm:h-44 bg-gradient-to-t from-white via-white/95 to-transparent pointer-events-none z-[2]" />
    </div>
  );
}
