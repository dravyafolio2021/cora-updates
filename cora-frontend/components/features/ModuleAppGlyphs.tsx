'use client';

import React from 'react';

// ─────────────────────────────────────────────────────────────────────────────
// 🎨 CLAY-STYLE VIBRANT PROFESSIONAL COLOR ICONS FOR CORA MODULES
// ─────────────────────────────────────────────────────────────────────────────

// 1. AI Co-Founder
export function AiCofounderColorIcon({ className = "w-9 h-9" }: { className?: string }) {
  return (
    <div className={`${className} rounded-xl bg-gradient-to-br from-emerald-500/10 via-emerald-500/5 to-teal-500/10 border border-emerald-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 group-hover:border-emerald-500/40 transition-all duration-150`}>
      <svg className="w-5 h-5" viewBox="0 0 24 24" fill="none">
        <rect x="3" y="6" width="18" height="14" rx="4" fill="#10B981" />
        <rect x="5" y="8" width="14" height="10" rx="2.5" fill="#047857" />
        <circle cx="8.5" cy="13" r="1.75" fill="#A7F3D0" />
        <circle cx="15.5" cy="13" r="1.75" fill="#A7F3D0" />
        <rect x="10.5" y="2.5" width="3" height="4" rx="1.5" fill="#10B981" />
        <circle cx="12" cy="2" r="1.2" fill="#34D399" />
        <path d="M10 15.5c.7.4 1.3.6 2 .6s1.3-.2 2-.6" stroke="#A7F3D0" strokeWidth="1.4" strokeLinecap="round" />
      </svg>
    </div>
  );
}

// 2. Voice-to-Scope
export function VoiceScopeColorIcon({ className = "w-9 h-9" }: { className?: string }) {
  return (
    <div className={`${className} rounded-xl bg-gradient-to-br from-purple-500/10 via-purple-500/5 to-indigo-500/10 border border-purple-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 group-hover:border-purple-500/40 transition-all duration-150`}>
      <svg className="w-5 h-5" viewBox="0 0 24 24" fill="none">
        <rect x="3" y="10" width="2.5" height="4" rx="1.25" fill="#C084FC" />
        <rect x="7" y="6" width="2.5" height="12" rx="1.25" fill="#A855F7" />
        <rect x="11" y="3" width="2.5" height="18" rx="1.25" fill="#7E22CE" />
        <rect x="15" y="7" width="2.5" height="10" rx="1.25" fill="#9333EA" />
        <rect x="19" y="10" width="2.5" height="4" rx="1.25" fill="#C084FC" />
      </svg>
    </div>
  );
}

// 3. Content AI & GEO
export function ContentAiColorIcon({ className = "w-9 h-9" }: { className?: string }) {
  return (
    <div className={`${className} rounded-xl bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-orange-500/10 border border-amber-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 group-hover:border-amber-500/40 transition-all duration-150`}>
      <svg className="w-5 h-5" viewBox="0 0 24 24" fill="none">
        <path d="M12 2L14.4 8.6L21 11L14.4 13.4L12 20L9.6 13.4L3 11L9.6 8.6L12 2Z" fill="#F59E0B" />
        <path d="M19 16L20.2 19.3L23.5 20.5L20.2 21.7L19 25L17.8 21.7L14.5 20.5L17.8 19.3L19 16Z" fill="#FBBF24" />
        <path d="M18 3l.8 2.2L21 6l-2.2.8L18 9l-.8-2.2L15 6l2.2-.8L18 3z" fill="#FDE68A" />
      </svg>
    </div>
  );
}

// 4. RAG Memory MCP
export function RagMemoryColorIcon({ className = "w-9 h-9" }: { className?: string }) {
  return (
    <div className={`${className} rounded-xl bg-gradient-to-br from-sky-500/10 via-sky-500/5 to-blue-500/10 border border-sky-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 group-hover:border-sky-500/40 transition-all duration-150`}>
      <svg className="w-5 h-5" viewBox="0 0 24 24" fill="none">
        <rect x="4" y="4" width="16" height="16" rx="4" fill="#0284C7" />
        <circle cx="12" cy="12" r="3.5" fill="#38BDF8" />
        <circle cx="12" cy="12" r="1.5" fill="white" />
        <path d="M12 4v3M12 17v3M4 12h3M17 12h3" stroke="#BAE6FD" strokeWidth="1.8" strokeLinecap="round" />
        <circle cx="6.5" cy="6.5" r="1" fill="#E0F2FE" />
        <circle cx="17.5" cy="17.5" r="1" fill="#E0F2FE" />
      </svg>
    </div>
  );
}

// 5. Kanban Lead CRM
export function LeadCrmColorIcon({ className = "w-9 h-9" }: { className?: string }) {
  return (
    <div className={`${className} rounded-xl bg-gradient-to-br from-blue-500/10 via-blue-500/5 to-indigo-500/10 border border-blue-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 group-hover:border-blue-500/40 transition-all duration-150`}>
      <svg className="w-5 h-5" viewBox="0 0 24 24" fill="none">
        <rect x="3" y="4" width="5" height="16" rx="2" fill="#3B82F6" />
        <rect x="9.5" y="4" width="5" height="11" rx="2" fill="#2563EB" />
        <rect x="16" y="4" width="5" height="7" rx="2" fill="#60A5FA" />
        <circle cx="12" cy="18" r="2" fill="#93C5FD" />
      </svg>
    </div>
  );
}

// 6. Funnel Builder
export function CanvasBuilderColorIcon({ className = "w-9 h-9" }: { className?: string }) {
  return (
    <div className={`${className} rounded-xl bg-gradient-to-br from-rose-500/10 via-rose-500/5 to-pink-500/10 border border-rose-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 group-hover:border-rose-500/40 transition-all duration-150`}>
      <svg className="w-5 h-5" viewBox="0 0 24 24" fill="none">
        <rect x="3" y="3" width="18" height="6" rx="2" fill="#F43F5E" />
        <rect x="3" y="11" width="8" height="10" rx="2" fill="#FB7185" />
        <rect x="13" y="11" width="8" height="10" rx="2" fill="#FDA4AF" />
      </svg>
    </div>
  );
}

// 7. Visual Forms
export function FormBuilderColorIcon({ className = "w-9 h-9" }: { className?: string }) {
  return (
    <div className={`${className} rounded-xl bg-gradient-to-br from-teal-500/10 via-teal-500/5 to-emerald-500/10 border border-teal-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 group-hover:border-teal-500/40 transition-all duration-150`}>
      <svg className="w-5 h-5" viewBox="0 0 24 24" fill="none">
        <rect x="4" y="3" width="16" height="18" rx="3.5" fill="#0D9488" />
        <rect x="7" y="7" width="10" height="2.5" rx="1.2" fill="#5EEAD4" />
        <rect x="7" y="11.5" width="6" height="2.5" rx="1.2" fill="#5EEAD4" />
        <circle cx="15.5" cy="12.7" r="1.5" fill="#CCFBF1" />
        <rect x="7" y="16" width="10" height="2" rx="1" fill="#2DD4BF" />
      </svg>
    </div>
  );
}

// 8. 5★ Review Portal
export function ReviewPortalColorIcon({ className = "w-9 h-9" }: { className?: string }) {
  return (
    <div className={`${className} rounded-xl bg-gradient-to-br from-amber-500/10 via-yellow-500/5 to-amber-500/10 border border-amber-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 group-hover:border-amber-500/40 transition-all duration-150`}>
      <svg className="w-5 h-5" viewBox="0 0 24 24" fill="none">
        <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" fill="#F59E0B" />
        <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77V2Z" fill="#FBBF24" />
      </svg>
    </div>
  );
}

// 9. SHA-256 E-Signs
export function EsignVaultColorIcon({ className = "w-9 h-9" }: { className?: string }) {
  return (
    <div className={`${className} rounded-xl bg-gradient-to-br from-emerald-500/10 via-teal-500/5 to-emerald-500/10 border border-emerald-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 group-hover:border-emerald-500/40 transition-all duration-150`}>
      <svg className="w-5 h-5" viewBox="0 0 24 24" fill="none">
        <path d="M4 4C4 2.9 4.9 2 6 2H14L20 8V20C20 21.1 19.1 22 18 22H6C4.9 22 4 21.1 4 20V4Z" fill="#059669" />
        <path d="M14 2V8H20" fill="#34D399" />
        <circle cx="12" cy="15" r="3.5" fill="#10B981" />
        <path d="M10.5 15l1.2 1.2 2.3-2.4" stroke="white" strokeWidth="1.6" strokeLinecap="round" strokeLinejoin="round" />
      </svg>
    </div>
  );
}

// 10. Crew Dispatch
export function CrewDispatchColorIcon({ className = "w-9 h-9" }: { className?: string }) {
  return (
    <div className={`${className} rounded-xl bg-gradient-to-br from-indigo-500/10 via-indigo-500/5 to-blue-500/10 border border-indigo-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 group-hover:border-indigo-500/40 transition-all duration-150`}>
      <svg className="w-5 h-5" viewBox="0 0 24 24" fill="none">
        <path d="M22 2L11 13" stroke="#6366F1" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
        <path d="M22 2L15 22L11 13L2 9L22 2Z" fill="#4F46E5" />
        <path d="M11 13L22 2L15 22L11 13Z" fill="#6366F1" />
        <path d="M2 9L22 2L11 13L2 9Z" fill="#818CF8" />
      </svg>
    </div>
  );
}

// 11. Master Calendar
export function MasterCalendarColorIcon({ className = "w-9 h-9" }: { className?: string }) {
  return (
    <div className={`${className} rounded-xl bg-gradient-to-br from-rose-500/10 via-rose-500/5 to-red-500/10 border border-rose-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 group-hover:border-rose-500/40 transition-all duration-150`}>
      <svg className="w-5 h-5" viewBox="0 0 24 24" fill="none">
        <rect x="3" y="5" width="18" height="16" rx="3.5" fill="#E11D48" />
        <path d="M3 5C3 3.1 4.6 1.5 6.5 1.5H17.5C19.4 1.5 21 3.1 21 5V8H3V5Z" fill="#BE123C" />
        <rect x="7" y="1" width="2" height="3" rx="1" fill="#FDA4AF" />
        <rect x="15" y="1" width="2" height="3" rx="1" fill="#FDA4AF" />
        <text x="12" y="17.5" fill="white" fontSize="8" fontWeight="bold" textAnchor="middle" fontFamily="sans-serif">31</text>
      </svg>
    </div>
  );
}

// 12. Task Board
export function TaskBoardColorIcon({ className = "w-9 h-9" }: { className?: string }) {
  return (
    <div className={`${className} rounded-xl bg-gradient-to-br from-violet-500/10 via-purple-500/5 to-violet-500/10 border border-violet-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 group-hover:border-violet-500/40 transition-all duration-150`}>
      <svg className="w-5 h-5" viewBox="0 0 24 24" fill="none">
        <rect x="3" y="3" width="18" height="5" rx="2" fill="#7C3AED" />
        <rect x="3" y="9.5" width="18" height="5" rx="2" fill="#8B5CF6" />
        <rect x="3" y="16" width="12" height="5" rx="2" fill="#A78BFA" />
      </svg>
    </div>
  );
}

// 13. 18% GST Invoicing
export function GstInvoicingColorIcon({ className = "w-9 h-9" }: { className?: string }) {
  return (
    <div className={`${className} rounded-xl bg-gradient-to-br from-emerald-500/10 via-green-500/5 to-teal-500/10 border border-emerald-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 group-hover:border-emerald-500/40 transition-all duration-150`}>
      <svg className="w-5 h-5" viewBox="0 0 24 24" fill="none">
        <path d="M4 2H20V22L16.5 20.5L13 22L9.5 20.5L6 22L4 20.5V2Z" fill="#16A34A" />
        <path d="M8 7H16M8 11H13M8 15H16" stroke="#DCFCE7" strokeWidth="1.8" strokeLinecap="round" />
        <circle cx="15.5" cy="11" r="1.5" fill="#BBF7D0" />
      </svg>
    </div>
  );
}

// 14. Gear & Inventory
export function AssetGearColorIcon({ className = "w-9 h-9" }: { className?: string }) {
  return (
    <div className={`${className} rounded-xl bg-gradient-to-br from-zinc-700/10 via-zinc-800/5 to-zinc-900/10 border border-zinc-700/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 group-hover:border-zinc-700/40 transition-all duration-150`}>
      <svg className="w-5 h-5" viewBox="0 0 24 24" fill="none">
        <rect x="3" y="6" width="18" height="14" rx="3.5" fill="#27272A" />
        <circle cx="12" cy="13" r="4.5" fill="#18181B" />
        <circle cx="12" cy="13" r="3" fill="#3F3F46" />
        <circle cx="12" cy="13" r="1.5" fill="#71717A" />
        <circle cx="18" cy="9.5" r="1.2" fill="#EF4444" />
        <path d="M8 6V4C8 3.4 8.4 3 9 3H15C15.6 3 16 3.4 16 4V6" fill="#3F3F46" />
      </svg>
    </div>
  );
}

// 15. Media Hub & RAW
export function MediaHubColorIcon({ className = "w-9 h-9" }: { className?: string }) {
  return (
    <div className={`${className} rounded-xl bg-gradient-to-br from-fuchsia-500/10 via-pink-500/5 to-purple-500/10 border border-fuchsia-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 group-hover:border-fuchsia-500/40 transition-all duration-150`}>
      <svg className="w-5 h-5" viewBox="0 0 24 24" fill="none">
        <rect x="3" y="4" width="18" height="16" rx="4" fill="#C026D3" />
        <circle cx="12" cy="12" r="4" fill="#E879F9" />
        <path d="M10.5 10L14.5 12L10.5 14V10Z" fill="#701A75" />
      </svg>
    </div>
  );
}

// 16. Multi-Tenant RBAC
export function RbacSecurityColorIcon({ className = "w-9 h-9" }: { className?: string }) {
  return (
    <div className={`${className} rounded-xl bg-gradient-to-br from-cyan-500/10 via-teal-500/5 to-cyan-500/10 border border-cyan-500/20 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 group-hover:border-cyan-500/40 transition-all duration-150`}>
      <svg className="w-5 h-5" viewBox="0 0 24 24" fill="none">
        <path d="M12 2L4 5.5V11C4 16.5 7.4 21.6 12 23C16.6 21.6 20 16.5 20 11V5.5L12 2Z" fill="#0891B2" />
        <path d="M9 12L11 14L15 10" stroke="white" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round" />
      </svg>
    </div>
  );
}

// ── Legacy Glyph Stubs for Backward Compatibility ──
export const AiCofounderGlyph = ({ className }: { className?: string }) => <AiCofounderColorIcon className={className} />;
export const ContentAiGlyph = ({ className }: { className?: string }) => <ContentAiColorIcon className={className} />;
export const RagMemoryGlyph = ({ className }: { className?: string }) => <RagMemoryColorIcon className={className} />;
export const VoiceScopeGlyph = ({ className }: { className?: string }) => <VoiceScopeColorIcon className={className} />;
export const LeadCrmGlyph = ({ className }: { className?: string }) => <LeadCrmColorIcon className={className} />;
export const CanvasBuilderGlyph = ({ className }: { className?: string }) => <CanvasBuilderColorIcon className={className} />;
export const FormBuilderGlyph = ({ className }: { className?: string }) => <FormBuilderColorIcon className={className} />;
export const ReviewPortalGlyph = ({ className }: { className?: string }) => <ReviewPortalColorIcon className={className} />;
export const EsignVaultGlyph = ({ className }: { className?: string }) => <EsignVaultColorIcon className={className} />;
export const CrewDispatchGlyph = ({ className }: { className?: string }) => <CrewDispatchColorIcon className={className} />;
export const MasterCalendarGlyph = ({ className }: { className?: string }) => <MasterCalendarColorIcon className={className} />;
export const TaskBoardGlyph = ({ className }: { className?: string }) => <TaskBoardColorIcon className={className} />;
export const GstInvoicingGlyph = ({ className }: { className?: string }) => <GstInvoicingColorIcon className={className} />;
export const AssetGearGlyph = ({ className }: { className?: string }) => <AssetGearColorIcon className={className} />;
export const MediaHubGlyph = ({ className }: { className?: string }) => <MediaHubColorIcon className={className} />;
export const RbacSecurityGlyph = ({ className }: { className?: string }) => <RbacSecurityColorIcon className={className} />;

// ── MODULE METADATA MAPPING (SLUG -> GLYPH & GRADIENT) ──
export const MODULE_GLYPH_MAP: Record<string, {
  Glyph: React.ComponentType<{ className?: string }>;
  bgGradient: string;
  shortName: string;
}> = {
  'ai-cofounder': {
    Glyph: AiCofounderColorIcon,
    bgGradient: 'bg-emerald-500',
    shortName: 'AI Co-Founder'
  },
  'content-ai': {
    Glyph: ContentAiColorIcon,
    bgGradient: 'bg-amber-500',
    shortName: 'Content AI & SEO'
  },
  'rag-mcp': {
    Glyph: RagMemoryColorIcon,
    bgGradient: 'bg-sky-500',
    shortName: 'RAG Memory MCP'
  },
  'voice-to-scope': {
    Glyph: VoiceScopeColorIcon,
    bgGradient: 'bg-purple-500',
    shortName: 'Voice-to-Scope'
  },
  'lead-crm': {
    Glyph: LeadCrmColorIcon,
    bgGradient: 'bg-blue-500',
    shortName: 'Kanban Lead CRM'
  },
  'canvas-builder': {
    Glyph: CanvasBuilderColorIcon,
    bgGradient: 'bg-rose-500',
    shortName: 'Funnel Builder'
  },
  'form-builder': {
    Glyph: FormBuilderColorIcon,
    bgGradient: 'bg-teal-500',
    shortName: 'Visual Forms'
  },
  'review-portal': {
    Glyph: ReviewPortalColorIcon,
    bgGradient: 'bg-amber-500',
    shortName: '5★ Review Portal'
  },
  'esign-vault': {
    Glyph: EsignVaultColorIcon,
    bgGradient: 'bg-emerald-600',
    shortName: 'SHA-256 E-Signs'
  },
  'crew-dispatch': {
    Glyph: CrewDispatchColorIcon,
    bgGradient: 'bg-indigo-600',
    shortName: 'Crew Dispatch'
  },
  'master-calendar': {
    Glyph: MasterCalendarColorIcon,
    bgGradient: 'bg-rose-600',
    shortName: 'Master Calendar'
  },
  'task-board': {
    Glyph: TaskBoardColorIcon,
    bgGradient: 'bg-violet-600',
    shortName: 'Task Board'
  },
  'gst-invoicing': {
    Glyph: GstInvoicingColorIcon,
    bgGradient: 'bg-emerald-600',
    shortName: '18% GST Invoicing'
  },
  'asset-gear': {
    Glyph: AssetGearColorIcon,
    bgGradient: 'bg-zinc-800',
    shortName: 'Gear & Inventory'
  },
  'media-hub': {
    Glyph: MediaHubColorIcon,
    bgGradient: 'bg-fuchsia-600',
    shortName: 'Media Hub & RAW'
  },
  'rbac-system': {
    Glyph: RbacSecurityColorIcon,
    bgGradient: 'bg-cyan-700',
    shortName: 'Multi-Tenant RBAC'
  }
};
