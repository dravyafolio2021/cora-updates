import React from 'react';

interface ArtisticHeroBackgroundProps {
  className?: string;
  tone?: 'blue' | 'emerald' | 'purple' | 'zinc' | 'amber';
}

export function ArtisticHeroBackground({ 
  className = '', 
  tone = 'blue' 
}: ArtisticHeroBackgroundProps) {
  const toneGradients = {
    blue: {
      bg: 'from-[#EBF2FC] via-[#F2F7FE] to-white',
      wave1: '#D6E4FA',
      wave2: '#E4EDFB',
      glow: 'rgba(59, 130, 246, 0.08)',
    },
    emerald: {
      bg: 'from-[#EAF6EE] via-[#F3FAF5] to-white',
      wave1: '#CEECD7',
      wave2: '#E1F5E8',
      glow: 'rgba(16, 185, 129, 0.08)',
    },
    purple: {
      bg: 'from-[#F3EEFD] via-[#F8F5FE] to-white',
      wave1: '#E2D7FA',
      wave2: '#EDE6FC',
      glow: 'rgba(147, 51, 234, 0.08)',
    },
    zinc: {
      bg: 'from-[#ECEFF2] via-[#F4F6F8] to-white',
      wave1: '#D8DEE4',
      wave2: '#E7ECF0',
      glow: 'rgba(100, 116, 139, 0.08)',
    },
    amber: {
      bg: 'from-[#FEF6E8] via-[#FFFBF4] to-white',
      wave1: '#FCE6C4',
      wave2: '#FDF1DE',
      glow: 'rgba(245, 158, 11, 0.08)',
    },
  };

  const current = toneGradients[tone] || toneGradients.blue;

  return (
    <div 
      className={`absolute inset-0 pointer-events-none overflow-hidden select-none ${className}`}
      aria-hidden="true"
    >
      {/* Base Gradient */}
      <div className={`absolute inset-0 bg-gradient-to-b ${current.bg}`} />

      {/* Ambient Overhead Light */}
      <div 
        className="absolute -top-1/4 left-1/2 -translate-x-1/2 w-[800px] h-[350px] rounded-full blur-3xl opacity-60"
        style={{ background: current.glow }}
      />

      {/* Soft Painterly Organic Waves (Light & Non-Intrusive) */}
      <svg
        className="absolute inset-0 w-full h-full object-cover opacity-45"
        viewBox="0 0 1440 380"
        fill="none"
        preserveAspectRatio="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          d="M-50 140 C 220 70, 520 220, 880 110 C 1180 30, 1380 140, 1550 90 L 1550 380 L -50 380 Z"
          fill={current.wave1}
          opacity="0.4"
        />
        <path
          d="M-50 200 C 180 130, 500 270, 920 170 C 1220 90, 1420 200, 1550 150 L 1550 380 L -50 380 Z"
          fill={current.wave2}
          opacity="0.6"
        />
      </svg>

      {/* Smooth Bottom Gradient Fade (Seamless merge into page) */}
      <div className="absolute bottom-0 left-0 right-0 h-28 sm:h-36 bg-gradient-to-b from-transparent via-white/80 to-white" />
    </div>
  );
}
