import React from 'react';
import Image from 'next/image';

interface ArtisticHeroBackgroundProps {
  className?: string;
  imageSrc?: string;
  tone?: string;
}

export function ArtisticHeroBackground({ 
  className = '', 
  imageSrc = '/images/cora_pricing_pure_sky.jpg',
  tone = 'neutral'
}: ArtisticHeroBackgroundProps) {
  return (
    <div 
      className={`absolute inset-0 pointer-events-none overflow-hidden select-none z-0 ${className}`}
      aria-hidden="true"
    >
      {/* Base Sky Tint Fallback */}
      <div className="absolute inset-0 bg-gradient-to-b from-[#56a2e8] via-[#cae4fc] to-white" />

      {/* Signature Ethereal Pure Sky Artwork */}
      <Image
        src={imageSrc}
        alt="Cora Atmospheric Background"
        fill
        priority
        className="object-cover object-top"
        sizes="100vw"
      />

      {/* Radiant Multi-Stop Overlay */}
      <div
        className="absolute inset-0 pointer-events-none"
        style={{
          background: 'linear-gradient(180deg, rgba(86, 162, 232, 0.14) 0%, rgba(255, 255, 255, 0.20) 35%, rgba(255, 255, 255, 0.88) 75%, #ffffff 100%)',
        }}
      />

      {/* Silky Smooth Bottom Fade Veil (Melt naturally into page) */}
      <div className="absolute inset-x-0 bottom-0 h-28 sm:h-40 bg-gradient-to-t from-white via-white/90 to-transparent pointer-events-none z-[2]" />
    </div>
  );
}
