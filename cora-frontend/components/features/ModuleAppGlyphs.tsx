'use client';

import React from 'react';

// ── 1. AI CO-FOUNDER GLYPH ──
export function AiCofounderGlyph({ className = "w-full h-full" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <rect x="4" y="6" width="16" height="13" rx="3.5" fill="white" />
      <circle cx="9" cy="12" r="1.8" fill="#059669" />
      <circle cx="15" cy="12" r="1.8" fill="#059669" />
      <rect x="10.5" y="2.5" width="3" height="3.5" rx="1.5" fill="white" />
      <path d="M9 16h6" stroke="#059669" strokeWidth="1.6" strokeLinecap="round" />
    </svg>
  );
}

// ── 2. CONTENT AI GLYPH ──
export function ContentAiGlyph({ className = "w-full h-full" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <path d="M12 2C12 7.523 7.523 12 2 12C7.523 12 12 16.477 12 22C12 16.477 16.477 12 22 12C16.477 12 12 7.523 12 2Z" fill="white" />
      <path d="M19 3c0 1.657-1.343 3-3 3 1.657 0 3 1.343 3 3 0-1.657 1.343-3 3-3-1.657 0-3-1.343-3-3z" fill="white" fillOpacity="0.8" />
    </svg>
  );
}

// ── 3. RAG MEMORY MCP GLYPH ──
export function RagMemoryGlyph({ className = "w-full h-full" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <rect x="3" y="4" width="18" height="16" rx="3.5" fill="white" />
      <circle cx="12" cy="10" r="3.5" fill="#7C3AED" />
      <circle cx="12" cy="10" r="1.5" fill="white" />
      <path d="M7 16h10M9 18h6" stroke="#7C3AED" strokeWidth="1.8" strokeLinecap="round" />
    </svg>
  );
}

// ── 4. VOICE-TO-SCOPE GLYPH ──
export function VoiceScopeGlyph({ className = "w-full h-full" }: { className?: string }) {
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

// ── 5. KANBAN LEAD CRM GLYPH ──
export function LeadCrmGlyph({ className = "w-full h-full" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <rect x="4" y="3" width="7" height="18" rx="2.5" fill="white" fillOpacity="0.9" />
      <rect x="13" y="3" width="7" height="11" rx="2.5" fill="white" />
      <circle cx="16.5" cy="18.5" r="2.5" fill="white" fillOpacity="0.75" />
    </svg>
  );
}

// ── 6. CANVAS / FUNNEL BUILDER GLYPH ──
export function CanvasBuilderGlyph({ className = "w-full h-full" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <rect x="3" y="4" width="18" height="16" rx="3.5" fill="white" />
      <rect x="6" y="7" width="12" height="4" rx="1.5" fill="#6366F1" />
      <rect x="6" y="13" width="5.5" height="4" rx="1.5" fill="#6366F1" />
      <rect x="12.5" y="13" width="5.5" height="4" rx="1.5" fill="#6366F1" fillOpacity="0.6" />
    </svg>
  );
}

// ── 7. VISUAL FORM BUILDER GLYPH ──
export function FormBuilderGlyph({ className = "w-full h-full" }: { className?: string }) {
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

// ── 8. 5-STAR REVIEW PORTAL GLYPH ──
export function ReviewPortalGlyph({ className = "w-full h-full" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill="white" />
      <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77V2z" fill="#FEF3C7" fillOpacity="0.4" />
    </svg>
  );
}

// ── 9. E-SIGN VAULT GLYPH ──
export function EsignVaultGlyph({ className = "w-full h-full" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <path d="M5 3h9l5 5v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z" fill="white" />
      <path d="M14 3v5h5" fill="#D1FAE5" />
      <circle cx="12" cy="15.5" r="3.5" fill="#047857" />
      <path d="M10.5 15.5l1.2 1.2 2.3-2.3" stroke="white" strokeWidth="1.6" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}

// ── 10. CREW DISPATCH GLYPH ──
export function CrewDispatchGlyph({ className = "w-full h-full" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <path d="M22 2L11 13" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
      <path d="M22 2L15 22L11 13L2 9L22 2Z" fill="white" />
      <path d="M11 13l4-9-4 9z" fill="#4338CA" fillOpacity="0.25" />
    </svg>
  );
}

// ── 11. MASTER CALENDAR GLYPH ──
export function MasterCalendarGlyph({ className = "w-full h-full" }: { className?: string }) {
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

// ── 12. TASK BOARD GLYPH ──
export function TaskBoardGlyph({ className = "w-full h-full" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <rect x="3" y="3" width="18" height="5.5" rx="2" fill="white" />
      <rect x="3" y="10" width="18" height="5.5" rx="2" fill="white" fillOpacity="0.8" />
      <rect x="3" y="17" width="18" height="4" rx="1.5" fill="white" fillOpacity="0.5" />
    </svg>
  );
}

// ── 13. 18% GST INVOICING GLYPH ──
export function GstInvoicingGlyph({ className = "w-full h-full" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <path d="M4 2h16v20l-2.5-1.5L15 22l-3-1.5L9 22l-2.5-1.5L4 22V2z" fill="white" />
      <path d="M8 7h8M8 10.5h5M8 14h8" stroke="#D97706" strokeWidth="1.8" strokeLinecap="round" />
      <circle cx="15.5" cy="10.5" r="1.5" fill="#D97706" />
    </svg>
  );
}

// ── 14. GEAR & ASSET INVENTORY GLYPH ──
export function AssetGearGlyph({ className = "w-full h-full" }: { className?: string }) {
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

// ── 15. MEDIA HUB & 8K RAW GLYPH ──
export function MediaHubGlyph({ className = "w-full h-full" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <rect x="3" y="4" width="18" height="16" rx="3.5" fill="white" />
      <circle cx="12" cy="11.5" r="4" fill="#6D28D9" />
      <circle cx="12" cy="11.5" r="2" fill="white" />
      <rect x="6" y="16.5" width="12" height="1.8" rx="0.9" fill="#6D28D9" />
    </svg>
  );
}

// ── 16. RBAC MULTI-TENANT SECURITY GLYPH ──
export function RbacSecurityGlyph({ className = "w-full h-full" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <path d="M12 2L4 5.5v5.5c0 5.5 3.8 10.6 8 11.8 4.2-1.2 8-6.3 8-11.8V5.5L12 2z" fill="white" />
      <path d="M9 11.5l2 2 4-4" stroke="#047857" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}

// ── 17. EMAIL & SMTP GLYPH ──
export function EmailSmtpGlyph({ className = "w-full h-full" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <rect x="3" y="5" width="18" height="14" rx="3.5" fill="white" />
      <path d="M3 6.5l9 6 9-6" stroke="#0284C7" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}

// ── 18. PWA MOBILE APP GLYPH ──
export function PwaMobileGlyph({ className = "w-full h-full" }: { className?: string }) {
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

// ── 19. ALL 20 MODULES LAUNCHER GLYPH ──
export function AllModulesLauncherGlyph({ className = "w-full h-full" }: { className?: string }) {
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

// ── MODULE METADATA MAPPING (SLUG -> GLYPH & GRADIENT) ──
export const MODULE_GLYPH_MAP: Record<string, {
  Glyph: React.ComponentType<{ className?: string }>;
  bgGradient: string;
  shortName: string;
}> = {
  'ai-cofounder': {
    Glyph: AiCofounderGlyph,
    bgGradient: 'bg-gradient-to-br from-[#10B981] to-[#047857]',
    shortName: 'AI Co-Founder'
  },
  'content-ai': {
    Glyph: ContentAiGlyph,
    bgGradient: 'bg-gradient-to-br from-[#F59E0B] to-[#D97706]',
    shortName: 'Content AI & SEO'
  },
  'rag-mcp': {
    Glyph: RagMemoryGlyph,
    bgGradient: 'bg-gradient-to-br from-[#8B5CF6] to-[#6D28D9]',
    shortName: 'RAG Memory MCP'
  },
  'voice-to-scope': {
    Glyph: VoiceScopeGlyph,
    bgGradient: 'bg-gradient-to-br from-[#0EA5E9] to-[#0284C7]',
    shortName: 'Voice-to-Scope'
  },
  'lead-crm': {
    Glyph: LeadCrmGlyph,
    bgGradient: 'bg-gradient-to-br from-[#3B82F6] to-[#1D4ED8]',
    shortName: 'Kanban Lead CRM'
  },
  'canvas-builder': {
    Glyph: CanvasBuilderGlyph,
    bgGradient: 'bg-gradient-to-br from-[#6366F1] to-[#4338CA]',
    shortName: 'Funnel Builder'
  },
  'form-builder': {
    Glyph: FormBuilderGlyph,
    bgGradient: 'bg-gradient-to-br from-[#0D9488] to-[#0F766E]',
    shortName: 'Visual Forms'
  },
  'review-portal': {
    Glyph: ReviewPortalGlyph,
    bgGradient: 'bg-gradient-to-br from-[#F59E0B] to-[#B45309]',
    shortName: '5★ Review Portal'
  },
  'esign-vault': {
    Glyph: EsignVaultGlyph,
    bgGradient: 'bg-gradient-to-br from-[#E11D48] to-[#BE123C]',
    shortName: 'SHA-256 E-Signs'
  },
  'crew-dispatch': {
    Glyph: CrewDispatchGlyph,
    bgGradient: 'bg-gradient-to-br from-[#6366F1] to-[#4338CA]',
    shortName: 'Crew Dispatch'
  },
  'master-calendar': {
    Glyph: MasterCalendarGlyph,
    bgGradient: 'bg-gradient-to-br from-[#8B5CF6] to-[#7C3AED]',
    shortName: 'Master Calendar'
  },
  'task-board': {
    Glyph: TaskBoardGlyph,
    bgGradient: 'bg-gradient-to-br from-[#10B981] to-[#059669]',
    shortName: 'Task Board'
  },
  'gst-invoicing': {
    Glyph: GstInvoicingGlyph,
    bgGradient: 'bg-gradient-to-br from-[#10B981] to-[#047857]',
    shortName: '18% GST Invoicing'
  },
  'asset-gear': {
    Glyph: AssetGearGlyph,
    bgGradient: 'bg-gradient-to-br from-[#0284C7] to-[#0369A1]',
    shortName: 'Gear & Inventory'
  },
  'media-hub': {
    Glyph: MediaHubGlyph,
    bgGradient: 'bg-gradient-to-br from-[#F97316] to-[#EA580C]',
    shortName: 'Media Hub & RAW'
  },
  'rbac-system': {
    Glyph: RbacSecurityGlyph,
    bgGradient: 'bg-gradient-to-br from-[#3F3F46] to-[#18181B]',
    shortName: 'Multi-Tenant RBAC'
  },
  'email-smtp': {
    Glyph: EmailSmtpGlyph,
    bgGradient: 'bg-gradient-to-br from-[#38BDF8] to-[#0284C7]',
    shortName: 'Email & SMTP'
  },
  'pwa-push': {
    Glyph: PwaMobileGlyph,
    bgGradient: 'bg-gradient-to-br from-[#F472B6] to-[#BE185D]',
    shortName: 'Mobile App'
  }
};
