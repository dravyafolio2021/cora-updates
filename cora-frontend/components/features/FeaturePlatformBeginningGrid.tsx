'use client';

import React from 'react';
import Link from 'next/link';
import { FeatureModule } from '@/lib/features-data';

interface FeaturePlatformBeginningGridProps {
  feature: FeatureModule;
}

// ── BESPOKE 3D / DUOTONE TACTILE APP GLYPHS ──

function CrmGlyph({ className = "w-6 h-6" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <rect x="4" y="3" width="7" height="18" rx="2.5" fill="white" fillOpacity="0.9" />
      <rect x="13" y="3" width="7" height="11" rx="2.5" fill="white" />
      <circle cx="16.5" cy="18.5" r="2.5" fill="white" fillOpacity="0.75" />
    </svg>
  );
}

function EsignGlyph({ className = "w-6 h-6" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <path d="M5 3h9l5 5v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z" fill="white" />
      <path d="M14 3v5h5" fill="#D1FAE5" />
      <circle cx="12" cy="15.5" r="3.5" fill="#047857" />
      <path d="M10.5 15.5l1.2 1.2 2.3-2.3" stroke="white" strokeWidth="1.6" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}

function GstGlyph({ className = "w-6 h-6" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <path d="M4 2h16v20l-2.5-1.5L15 22l-3-1.5L9 22l-2.5-1.5L4 22V2z" fill="white" />
      <path d="M8 7h8M8 10.5h5M8 14h8" stroke="#D97706" strokeWidth="1.8" strokeLinecap="round" />
      <circle cx="15.5" cy="10.5" r="1.5" fill="#D97706" />
    </svg>
  );
}

function CalendarGlyph({ className = "w-6 h-6" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <rect x="3" y="5" width="18" height="16" rx="3.5" fill="white" />
      <path d="M3 5a3.5 3.5 0 0 1 3.5-3.5h11A3.5 3.5 0 0 1 21 5v4H3V5z" fill="#1D4ED8" fillOpacity="0.3" />
      <rect x="7" y="1.5" width="2" height="3.5" rx="1" fill="white" />
      <rect x="15" y="1.5" width="2" height="3.5" rx="1" fill="white" />
      <text x="12" y="17.5" fill="#1D4ED8" fontSize="8.5" fontWeight="900" textAnchor="middle" fontFamily="sans-serif">31</text>
    </svg>
  );
}

function DispatchGlyph({ className = "w-6 h-6" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <path d="M22 2L11 13" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
      <path d="M22 2L15 22L11 13L2 9L22 2Z" fill="white" />
      <path d="M11 13l4-9-4 9z" fill="#4338CA" fillOpacity="0.25" />
    </svg>
  );
}

function CameraGlyph({ className = "w-6 h-6" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <rect x="3" y="6" width="18" height="14" rx="3.5" fill="white" />
      <circle cx="12" cy="13" r="4.5" fill="#18181B" />
      <circle cx="12" cy="13" r="2.8" fill="#3F3F46" />
      <circle cx="12" cy="13" r="1.2" fill="#71717A" />
      <circle cx="18" cy="9.5" r="1.2" fill="#EF4444" />
      <path d="M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2" fill="white" />
    </svg>
  );
}

function VaultGlyph({ className = "w-6 h-6" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <rect x="3" y="4" width="18" height="16" rx="3.5" fill="white" />
      <circle cx="12" cy="11.5" r="4" fill="#6D28D9" />
      <circle cx="12" cy="11.5" r="2" fill="white" />
      <rect x="6" y="16.5" width="12" height="1.8" rx="0.9" fill="#6D28D9" />
    </svg>
  );
}

function VoiceGlyph({ className = "w-6 h-6" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <rect x="3" y="9.5" width="2.5" height="5" rx="1.25" fill="white" />
      <rect x="7.5" y="5.5" width="2.5" height="13" rx="1.25" fill="white" />
      <rect x="12" y="3" width="2.5" height="18" rx="1.25" fill="white" />
      <rect x="16.5" y="7" width="2.5" height="10" rx="1.25" fill="white" />
      <rect x="21" y="10" width="2.5" height="4" rx="1.25" fill="white" />
    </svg>
  );
}

function ReviewGlyph({ className = "w-6 h-6" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill="white" />
      <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77V2z" fill="#FEF3C7" fillOpacity="0.4" />
    </svg>
  );
}

function FormGlyph({ className = "w-6 h-6" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <rect x="4" y="4" width="16" height="17" rx="3.5" fill="white" />
      <path d="M8 2h8a1 1 0 0 1 1 1v2H7V3a1 1 0 0 1 1-1z" fill="white" />
      <rect x="7.5" y="9" width="3" height="3" rx="1" fill="#0284C7" />
      <path d="M13 10.5h4" stroke="#0284C7" strokeWidth="1.8" strokeLinecap="round" />
      <rect x="7.5" y="14" width="3" height="3" rx="1" fill="#0284C7" />
      <path d="M13 15.5h4" stroke="#0284C7" strokeWidth="1.8" strokeLinecap="round" />
    </svg>
  );
}

function ContentAiGlyph({ className = "w-6 h-6" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <path d="M12 2C12 7.523 7.523 12 2 12C7.523 12 12 16.477 12 22C12 16.477 16.477 12 22 12C16.477 12 12 7.523 12 2Z" fill="white" />
      <path d="M19 3c0 1.657-1.343 3-3 3 1.657 0 3 1.343 3 3 0-1.657 1.343-3 3-3-1.657 0-3-1.343-3-3z" fill="white" fillOpacity="0.8" />
    </svg>
  );
}

function RbacGlyph({ className = "w-6 h-6" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <path d="M12 2L4 5.5v5.5c0 5.5 3.8 10.6 8 11.8 4.2-1.2 8-6.3 8-11.8V5.5L12 2z" fill="white" />
      <path d="M9 11.5l2 2 4-4" stroke="#047857" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}

function TaskBoardGlyph({ className = "w-6 h-6" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <rect x="3" y="3" width="18" height="5.5" rx="2" fill="white" />
      <rect x="3" y="10" width="18" height="5.5" rx="2" fill="white" fillOpacity="0.8" />
      <rect x="3" y="17" width="18" height="4" rx="1.5" fill="white" fillOpacity="0.5" />
    </svg>
  );
}

function EmailGlyph({ className = "w-6 h-6" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <rect x="3" y="5" width="18" height="14" rx="3.5" fill="white" />
      <path d="M3 6.5l9 6 9-6" stroke="#0284C7" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}

function MobileGlyph({ className = "w-6 h-6" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <rect x="5" y="2" width="14" height="20" rx="3.5" fill="white" />
      <rect x="9.5" y="3.5" width="5" height="1.5" rx="0.75" fill="#DB2777" />
      <rect x="7.5" y="7" width="3.5" height="3.5" rx="1" fill="#DB2777" />
      <rect x="13" y="7" width="3.5" height="3.5" rx="1" fill="#DB2777" />
      <rect x="7.5" y="12" width="3.5" height="3.5" rx="1" fill="#DB2777" />
      <rect x="13" y="12" width="3.5" height="3.5" rx="1" fill="#DB2777" />
      <rect x="9.5" y="18.5" width="5" height="1" rx="0.5" fill="#DB2777" />
    </svg>
  );
}

function AllFeaturesGlyph({ className = "w-6 h-6" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <rect x="3" y="3" width="8" height="8" rx="2.5" fill="#F43F5E" />
      <rect x="13" y="3" width="8" height="8" rx="2.5" fill="#3B82F6" />
      <rect x="3" y="13" width="8" height="8" rx="2.5" fill="#F59E0B" />
      <rect x="13" y="13" width="8" height="8" rx="2.5" fill="#10B981" />
      <circle cx="12" cy="12" r="3.5" fill="white" />
      <path d="M10.5 12h3M12.5 10.5l1.5 1.5-1.5 1.5" stroke="#18181B" strokeWidth="1.4" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}

export function FeaturePlatformBeginningGrid({ feature }: FeaturePlatformBeginningGridProps) {
  const modules = [
    {
      slug: 'lead-crm',
      name: 'Lead CRM',
      desc: 'Visual Kanban pipeline & stages',
      Glyph: CrmGlyph,
      bgGradient: 'bg-gradient-to-br from-[#F43F5E] to-[#E11D48]'
    },
    {
      slug: 'esign-vault',
      name: 'E-Sign Vault',
      desc: 'IT Act 2000 digital contracts',
      Glyph: EsignGlyph,
      bgGradient: 'bg-gradient-to-br from-[#10B981] to-[#047857]'
    },
    {
      slug: 'gst-invoicing',
      name: '18% GST Invoicing',
      desc: 'SAC 998314 billing & UPI QR',
      Glyph: GstGlyph,
      bgGradient: 'bg-gradient-to-br from-[#F59E0B] to-[#D97706]'
    },
    {
      slug: 'master-calendar',
      name: 'Master Calendar',
      desc: 'Multi-location shoot holds',
      Glyph: CalendarGlyph,
      bgGradient: 'bg-gradient-to-br from-[#3B82F6] to-[#1D4ED8]'
    },
    {
      slug: 'crew-dispatch',
      name: 'Crew Dispatch',
      desc: 'Call sheets & gear allocations',
      Glyph: DispatchGlyph,
      bgGradient: 'bg-gradient-to-br from-[#6366F1] to-[#4338CA]'
    },
    {
      slug: 'asset-gear',
      name: 'Asset & Gear Hub',
      desc: 'Sony FX6 kit check-ins',
      Glyph: CameraGlyph,
      bgGradient: 'bg-gradient-to-br from-[#27272A] to-[#09090B]'
    },
    {
      slug: 'media-hub',
      name: 'Media Vault',
      desc: '8K RAW footage delivery',
      Glyph: VaultGlyph,
      bgGradient: 'bg-gradient-to-br from-[#8B5CF6] to-[#6D28D9]'
    },
    {
      slug: 'voice-to-scope',
      name: 'Voice-to-Scope',
      desc: 'Audio brief transcription',
      Glyph: VoiceGlyph,
      bgGradient: 'bg-gradient-to-br from-[#14B8A6] to-[#0F766E]'
    },
    {
      slug: 'review-portal',
      name: 'Review Portal',
      desc: '5-star Google review engine',
      Glyph: ReviewGlyph,
      bgGradient: 'bg-gradient-to-br from-[#F59E0B] to-[#B45309]'
    },
    {
      slug: 'form-builder',
      name: 'Form Builder',
      desc: 'Embeddable lead widgets',
      Glyph: FormGlyph,
      bgGradient: 'bg-gradient-to-br from-[#0EA5E9] to-[#0369A1]'
    },
    {
      slug: 'content-ai',
      name: 'Content AI',
      desc: 'Viral scripts & storyboard copy',
      Glyph: ContentAiGlyph,
      bgGradient: 'bg-gradient-to-br from-[#A855F7] to-[#7E22CE]'
    },
    {
      slug: 'rbac-system',
      name: 'RBAC Security',
      desc: 'Role permissions & audit logs',
      Glyph: RbacGlyph,
      bgGradient: 'bg-gradient-to-br from-[#10B981] to-[#065F46]'
    },
    {
      slug: 'task-board',
      name: 'Task Board',
      desc: 'Post-production milestones',
      Glyph: TaskBoardGlyph,
      bgGradient: 'bg-gradient-to-br from-[#3F3F46] to-[#18181B]'
    },
    {
      slug: 'email-smtp',
      name: 'Email & SMTP',
      desc: 'Verified custom domain inbox',
      Glyph: EmailGlyph,
      bgGradient: 'bg-gradient-to-br from-[#38BDF8] to-[#0284C7]'
    },
    {
      slug: 'pwa-push',
      name: 'Mobile App',
      desc: 'iOS & Android instant alerts',
      Glyph: MobileGlyph,
      bgGradient: 'bg-gradient-to-br from-[#F472B6] to-[#BE185D]'
    },
    {
      slug: '',
      name: 'All 20 Modules',
      desc: 'Explore complete studio OS',
      Glyph: AllFeaturesGlyph,
      bgGradient: 'bg-gradient-to-br from-[#27272A] to-[#000000]',
      isExploreAll: true
    }
  ];

  return (
    <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-24 sm:mb-32">
      
      {/* Central Headline */}
      <div className="text-center max-w-[800px] mx-auto mb-12 sm:mb-16">
        <span className="text-xs font-mono font-bold uppercase tracking-wider text-indigo-600 mb-2 block">
          THE CORA PLATFORM
        </span>
        <h2 className="font-display text-3xl sm:text-5xl font-bold tracking-tight text-zinc-950 mb-4">
          {feature.shortTitle} is just the <span className="text-zinc-400 font-semibold">beginning</span>
        </h2>
        <p className="text-zinc-600 text-sm sm:text-base leading-relaxed max-w-[680px] mx-auto">
          {feature.shortTitle} is the connective tissue of the Cora platform. E-Sign, 18% GST Invoicing, CRM, Call Sheets, and more in a single, converged workspace. Explore everything you unlock when your studio runs in one place.
        </p>
      </div>

      {/* 4x4 Grid of Platform Apps with Rich Tactile 3D Icons */}
      <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
        {modules.map((mod, idx) => {
          const Glyph = mod.Glyph;
          const href = mod.isExploreAll ? '/features' : `/features/${mod.slug}`;
          
          return (
            <Link
              key={idx}
              href={href}
              className="p-4 sm:p-5 rounded-2xl bg-white border border-zinc-200/80 hover:border-zinc-300 hover:shadow-[0_8px_20px_rgba(0,0,0,0.06)] hover:-translate-y-0.5 transition-all flex items-start gap-3.5 sm:gap-4 group cursor-pointer"
            >
              <div className={`w-11 h-11 sm:w-12 sm:h-12 rounded-[14px] ${mod.bgGradient} p-2 sm:p-2.5 flex items-center justify-center shrink-0 shadow-[0_4px_12px_rgba(0,0,0,0.08)] group-hover:scale-105 transition-transform`}>
                <Glyph className="w-full h-full" />
              </div>
              <div className="min-w-0 space-y-0.5 pt-0.5">
                <h4 className="font-display text-sm sm:text-[15px] font-bold text-zinc-950 group-hover:text-zinc-700 transition-colors truncate">
                  {mod.name}
                </h4>
                <p className="text-[11px] sm:text-xs text-zinc-500 truncate font-normal">
                  {mod.desc}
                </p>
              </div>
            </Link>
          );
        })}
      </div>

    </section>
  );
}
