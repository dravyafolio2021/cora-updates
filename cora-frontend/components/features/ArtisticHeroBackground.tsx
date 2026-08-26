import React from 'react';

interface ArtisticHeroBackgroundProps {
  className?: string;
  tone?: 'neutral' | 'blue' | 'emerald' | 'purple' | 'zinc' | 'amber';
}

export function ArtisticHeroBackground({ 
  className = '', 
  tone = 'neutral' 
}: ArtisticHeroBackgroundProps) {
  return (
    <div 
      className={`absolute inset-0 pointer-events-none overflow-hidden select-none ${className}`}
      aria-hidden="true"
    >
      {/* Subtle, High-Confidence Monochromatic Atmospheric Canvas */}
      <div className="absolute inset-0 bg-gradient-to-b from-zinc-100/70 via-zinc-50/50 to-white" />

      {/* Gentle Radial Ambient Illumination */}
      <div 
        className="absolute -top-24 left-1/2 -translate-x-1/2 w-[850px] h-[360px] rounded-full blur-3xl opacity-40"
        style={{
          background: 'radial-gradient(ellipse at center, rgba(228, 228, 231, 0.8) 0%, rgba(244, 244, 245, 0.4) 50%, rgba(255, 255, 255, 0) 80%)'
        }}
      />

      {/* Ultra-Subtle Dot Matrix Grid for Clean Architectural Feel */}
      <div 
        className="absolute inset-0 opacity-[0.035]"
        style={{
          backgroundImage: 'radial-gradient(#000000 1px, transparent 1px)',
          backgroundSize: '24px 24px'
        }}
      />

      {/* Silky Smooth Bottom Fade Veil (Buttery merge directly into page background) */}
      <div className="absolute bottom-0 left-0 right-0 h-28 sm:h-36 bg-gradient-to-b from-transparent via-white/80 to-white" />
    </div>
  );
}
