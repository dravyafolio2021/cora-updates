import React from 'react';

interface ArtisticHeroBackgroundProps {
  className?: string;
  tone?: 'blue' | 'emerald' | 'purple' | 'zinc' | 'amber';
}

export function ArtisticHeroBackground({ 
  className = '', 
  tone = 'blue' 
}: ArtisticHeroBackgroundProps) {
  // Tone palette maps
  const toneGradients = {
    blue: {
      bg: 'from-[#E3ECFC] via-[#EEF4FE] to-[#F8FAFC]',
      wave1: '#C7DAF8',
      wave2: '#D6E4FA',
      wave3: '#B9D1F5',
      glow: 'rgba(59, 130, 246, 0.12)',
    },
    emerald: {
      bg: 'from-[#E6F4EA] via-[#F0FAF2] to-[#F8FAFC]',
      wave1: '#C1E7CD',
      wave2: '#D8F1E1',
      wave3: '#A8DEB8',
      glow: 'rgba(16, 185, 129, 0.12)',
    },
    purple: {
      bg: 'from-[#EFEAFC] via-[#F6F2FD] to-[#F8FAFC]',
      wave1: '#D8CAF8',
      wave2: '#E5DCFA',
      wave3: '#C6B2F5',
      glow: 'rgba(147, 51, 234, 0.12)',
    },
    zinc: {
      bg: 'from-[#EAECEF] via-[#F3F4F6] to-[#F8FAFC]',
      wave1: '#D1D5DB',
      wave2: '#E5E7EB',
      wave3: '#9CA3AF',
      glow: 'rgba(100, 116, 139, 0.12)',
    },
    amber: {
      bg: 'from-[#FEF3E2] via-[#FFF9F0] to-[#F8FAFC]',
      wave1: '#FDE1B8',
      wave2: '#FEEFD6',
      wave3: '#FCCE90',
      glow: 'rgba(245, 158, 11, 0.12)',
    },
  };

  const current = toneGradients[tone] || toneGradients.blue;

  return (
    <div 
      className={`absolute inset-0 pointer-events-none overflow-hidden select-none ${className}`}
      aria-hidden="true"
    >
      {/* Base Gradient Canvas */}
      <div className={`absolute inset-0 bg-gradient-to-b ${current.bg}`} />

      {/* Ambient Radial Lighting Glow */}
      <div 
        className="absolute -top-[20%] left-1/2 -translate-x-1/2 w-[900px] h-[500px] rounded-full blur-3xl opacity-70"
        style={{ background: current.glow }}
      />

      {/* Artistic Organic Fluid Waves (Vector SVG Paths) */}
      <svg
        className="absolute inset-0 w-full h-full object-cover opacity-65"
        viewBox="0 0 1440 600"
        fill="none"
        preserveAspectRatio="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        {/* Soft Organic Back Wave */}
        <path
          d="M-100 240 C 240 120, 480 340, 880 180 C 1180 60, 1380 220, 1600 140 L 1600 600 L -100 600 Z"
          fill={current.wave1}
          opacity="0.35"
        />

        {/* Mid Soft Curve */}
        <path
          d="M-80 320 C 180 200, 520 420, 920 260 C 1220 140, 1420 310, 1620 230 L 1620 600 L -80 600 Z"
          fill={current.wave2}
          opacity="0.5"
        />

        {/* Foreground Gentle Sweep */}
        <path
          d="M-50 420 C 260 310, 600 480, 1000 360 C 1300 270, 1480 410, 1650 350 L 1650 600 L -50 600 Z"
          fill={current.wave3}
          opacity="0.25"
        />
      </svg>

      {/* Decorative Tactile Geometry Accents (Subtle Floating Silhouettes on Left & Right Edges) */}
      <div className="hidden lg:block absolute left-6 top-1/3 -translate-y-1/2 opacity-20 transform -rotate-12">
        <svg width="90" height="90" viewBox="0 0 90 90" fill="none">
          <rect x="15" y="15" width="60" height="60" rx="20" fill="currentColor" className="text-zinc-500" />
          <circle cx="45" cy="45" r="18" fill="white" fillOpacity="0.4" />
        </svg>
      </div>

      <div className="hidden lg:block absolute right-6 top-1/4 opacity-20 transform rotate-12">
        <svg width="100" height="100" viewBox="0 0 100 100" fill="none">
          <circle cx="50" cy="50" r="35" fill="currentColor" className="text-zinc-500" />
          <polygon points="50,20 76,65 24,65" fill="white" fillOpacity="0.5" />
        </svg>
      </div>

      {/* ── SMOOTH BOTTOM BLENDING VEIL (Eliminates Abrupt Clipping) ── */}
      <div className="absolute bottom-0 left-0 right-0 h-44 sm:h-64 bg-gradient-to-b from-transparent via-white/80 to-white" />
    </div>
  );
}
