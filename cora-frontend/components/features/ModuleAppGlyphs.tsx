'use client';

import React from 'react';

// ─────────────────────────────────────────────────────────────────────────────
// 🎨 CLAY-STYLE VIBRANT PROFESSIONAL COLOR ICONS FOR CORA MODULES
// ─────────────────────────────────────────────────────────────────────────────

// 1. AI Co-Founder
export function AiCofounderColorIcon({ className = "w-10 h-10" }: { className?: string }) {
  return (
    <div className={`${className} rounded-2xl bg-gradient-to-br from-emerald-500/15 via-teal-500/10 to-emerald-500/20 border border-emerald-500/30 flex items-center justify-center shrink-0 shadow-[0_2px_10px_rgba(16,185,129,0.12)] group-hover:scale-105 group-hover:border-emerald-500/50 transition-all duration-200`}>
      <svg className="w-6 h-6" viewBox="0 0 32 32" fill="none">
        <defs>
          <linearGradient id="ai_bot_grad" x1="4" y1="8" x2="28" y2="28" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stopColor="#34D399" />
            <stop offset="50%" stopColor="#10B981" />
            <stop offset="100%" stopColor="#059669" />
          </linearGradient>
          <linearGradient id="ai_screen_grad" x1="8" y1="12" x2="24" y2="24" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stopColor="#064E3B" />
            <stop offset="100%" stopColor="#022C22" />
          </linearGradient>
        </defs>
        <rect x="5" y="9" width="22" height="17" rx="5" fill="url(#ai_bot_grad)" />
        <rect x="8" y="12" width="16" height="11" rx="3" fill="url(#ai_screen_grad)" />
        <circle cx="12" cy="17.5" r="2.2" fill="#6EE7B7" />
        <circle cx="20" cy="17.5" r="2.2" fill="#6EE7B7" />
        <circle cx="12.7" cy="16.8" r="0.8" fill="white" />
        <circle cx="20.7" cy="16.8" r="0.8" fill="white" />
        <path d="M16 4v5" stroke="#34D399" strokeWidth="2" strokeLinecap="round" />
        <circle cx="16" cy="4" r="2" fill="#FBBF24" />
      </svg>
    </div>
  );
}

// 2. Voice-to-Scope
export function VoiceScopeColorIcon({ className = "w-10 h-10" }: { className?: string }) {
  return (
    <div className={`${className} rounded-2xl bg-gradient-to-br from-purple-500/15 via-violet-500/10 to-indigo-500/20 border border-purple-500/30 flex items-center justify-center shrink-0 shadow-[0_2px_10px_rgba(168,85,247,0.12)] group-hover:scale-105 group-hover:border-purple-500/50 transition-all duration-200`}>
      <svg className="w-6 h-6" viewBox="0 0 32 32" fill="none">
        <defs>
          <linearGradient id="v_bar1" x1="0" y1="0" x2="0" y2="100%">
            <stop offset="0%" stopColor="#C084FC" />
            <stop offset="100%" stopColor="#9333EA" />
          </linearGradient>
          <linearGradient id="v_bar2" x1="0" y1="0" x2="0" y2="100%">
            <stop offset="0%" stopColor="#E879F9" />
            <stop offset="100%" stopColor="#7E22CE" />
          </linearGradient>
        </defs>
        <rect x="5" y="13" width="3.5" height="6" rx="1.75" fill="url(#v_bar1)" />
        <rect x="10" y="8" width="3.5" height="16" rx="1.75" fill="url(#v_bar2)" />
        <rect x="15" y="4" width="3.5" height="24" rx="1.75" fill="url(#v_bar1)" />
        <rect x="20" y="9" width="3.5" height="14" rx="1.75" fill="url(#v_bar2)" />
        <rect x="25" y="13" width="3.5" height="6" rx="1.75" fill="url(#v_bar1)" />
      </svg>
    </div>
  );
}

// 3. Content AI & GEO
export function ContentAiColorIcon({ className = "w-10 h-10" }: { className?: string }) {
  return (
    <div className={`${className} rounded-2xl bg-gradient-to-br from-amber-500/15 via-orange-500/10 to-yellow-500/20 border border-amber-500/30 flex items-center justify-center shrink-0 shadow-[0_2px_10px_rgba(245,158,11,0.12)] group-hover:scale-105 group-hover:border-amber-500/50 transition-all duration-200`}>
      <svg className="w-6 h-6" viewBox="0 0 32 32" fill="none">
        <defs>
          <linearGradient id="ai_star_main" x1="16" y1="3" x2="16" y2="27" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stopColor="#FDE047" />
            <stop offset="50%" stopColor="#F59E0B" />
            <stop offset="100%" stopColor="#D97706" />
          </linearGradient>
          <linearGradient id="ai_star_sub" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stopColor="#FEF08A" />
            <stop offset="100%" stopColor="#F59E0B" />
          </linearGradient>
        </defs>
        <path d="M16 3L19.2 12.8L29 16L19.2 19.2L16 29L12.8 19.2L3 16L12.8 12.8L16 3Z" fill="url(#ai_star_main)" />
        <path d="M25 4L26.2 7.8L30 9L26.2 10.2L25 14L23.8 10.2L20 9L23.8 7.8L25 4Z" fill="url(#ai_star_sub)" />
        <circle cx="8" cy="7" r="1.5" fill="#FDE047" />
      </svg>
    </div>
  );
}

// 4. RAG Memory MCP
export function RagMemoryColorIcon({ className = "w-10 h-10" }: { className?: string }) {
  return (
    <div className={`${className} rounded-2xl bg-gradient-to-br from-sky-500/15 via-blue-500/10 to-cyan-500/20 border border-sky-500/30 flex items-center justify-center shrink-0 shadow-[0_2px_10px_rgba(2,132,199,0.12)] group-hover:scale-105 group-hover:border-sky-500/50 transition-all duration-200`}>
      <svg className="w-6 h-6" viewBox="0 0 32 32" fill="none">
        <defs>
          <linearGradient id="rag_cube" x1="4" y1="4" x2="28" y2="28" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stopColor="#38BDF8" />
            <stop offset="100%" stopColor="#0284C7" />
          </linearGradient>
        </defs>
        <rect x="5" y="5" width="22" height="22" rx="6" fill="url(#rag_cube)" />
        <circle cx="16" cy="16" r="4.5" fill="#0369A1" />
        <circle cx="16" cy="16" r="2.5" fill="#BAE6FD" />
        <circle cx="16" cy="16" r="1.2" fill="white" />
        <path d="M16 5v6M16 21v6M5 16h6M21 16h6" stroke="#BAE6FD" strokeWidth="2" strokeLinecap="round" />
        <circle cx="9" cy="9" r="1.5" fill="#E0F2FE" />
        <circle cx="23" cy="23" r="1.5" fill="#E0F2FE" />
      </svg>
    </div>
  );
}

// 5. Kanban Lead CRM
export function LeadCrmColorIcon({ className = "w-10 h-10" }: { className?: string }) {
  return (
    <div className={`${className} rounded-2xl bg-gradient-to-br from-blue-500/15 via-indigo-500/10 to-blue-600/20 border border-blue-500/30 flex items-center justify-center shrink-0 shadow-[0_2px_10px_rgba(59,130,246,0.12)] group-hover:scale-105 group-hover:border-blue-500/50 transition-all duration-200`}>
      <svg className="w-6 h-6" viewBox="0 0 32 32" fill="none">
        <defs>
          <linearGradient id="crm_bar_1" x1="0" y1="0" x2="0" y2="100%">
            <stop offset="0%" stopColor="#60A5FA" />
            <stop offset="100%" stopColor="#2563EB" />
          </linearGradient>
          <linearGradient id="crm_bar_2" x1="0" y1="0" x2="0" y2="100%">
            <stop offset="0%" stopColor="#3B82F6" />
            <stop offset="100%" stopColor="#1D4ED8" />
          </linearGradient>
          <linearGradient id="crm_bar_3" x1="0" y1="0" x2="0" y2="100%">
            <stop offset="0%" stopColor="#93C5FD" />
            <stop offset="100%" stopColor="#3B82F6" />
          </linearGradient>
        </defs>
        <rect x="5" y="6" width="6" height="20" rx="3" fill="url(#crm_bar_1)" />
        <rect x="13" y="11" width="6" height="15" rx="3" fill="url(#crm_bar_2)" />
        <rect x="21" y="16" width="6" height="10" rx="3" fill="url(#crm_bar_3)" />
      </svg>
    </div>
  );
}

// 6. Funnel Builder
export function CanvasBuilderColorIcon({ className = "w-10 h-10" }: { className?: string }) {
  return (
    <div className={`${className} rounded-2xl bg-gradient-to-br from-rose-500/15 via-pink-500/10 to-rose-600/20 border border-rose-500/30 flex items-center justify-center shrink-0 shadow-[0_2px_10px_rgba(244,63,94,0.12)] group-hover:scale-105 group-hover:border-rose-500/50 transition-all duration-200`}>
      <svg className="w-6 h-6" viewBox="0 0 32 32" fill="none">
        <defs>
          <linearGradient id="canvas_top" x1="4" y1="4" x2="28" y2="12" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stopColor="#FB7185" />
            <stop offset="100%" stopColor="#E11D48" />
          </linearGradient>
          <linearGradient id="canvas_bottom" x1="0" y1="0" x2="0" y2="100%">
            <stop offset="0%" stopColor="#FDA4AF" />
            <stop offset="100%" stopColor="#FB7185" />
          </linearGradient>
        </defs>
        <rect x="4" y="5" width="24" height="8" rx="3" fill="url(#canvas_top)" />
        <rect x="4" y="16" width="10.5" height="11" rx="3" fill="url(#canvas_bottom)" />
        <rect x="17.5" y="16" width="10.5" height="11" rx="3" fill="url(#canvas_bottom)" />
        <circle cx="8" cy="9" r="1.5" fill="white" />
      </svg>
    </div>
  );
}

// 7. Visual Forms
export function FormBuilderColorIcon({ className = "w-10 h-10" }: { className?: string }) {
  return (
    <div className={`${className} rounded-2xl bg-gradient-to-br from-teal-500/15 via-emerald-500/10 to-teal-600/20 border border-teal-500/30 flex items-center justify-center shrink-0 shadow-[0_2px_10px_rgba(13,148,136,0.12)] group-hover:scale-105 group-hover:border-teal-500/50 transition-all duration-200`}>
      <svg className="w-6 h-6" viewBox="0 0 32 32" fill="none">
        <defs>
          <linearGradient id="form_sheet" x1="5" y1="4" x2="27" y2="28" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stopColor="#2DD4BF" />
            <stop offset="100%" stopColor="#0F766E" />
          </linearGradient>
        </defs>
        <rect x="5" y="4" width="22" height="24" rx="5" fill="url(#form_sheet)" />
        <rect x="9" y="9" width="14" height="3" rx="1.5" fill="#CCFBF1" />
        <rect x="9" y="15" width="9" height="3" rx="1.5" fill="#CCFBF1" />
        <circle cx="21" cy="16.5" r="2" fill="#FDE047" />
        <rect x="9" y="21" width="14" height="2.5" rx="1.25" fill="#5EEAD4" />
      </svg>
    </div>
  );
}

// 8. 5★ Review Portal
export function ReviewPortalColorIcon({ className = "w-10 h-10" }: { className?: string }) {
  return (
    <div className={`${className} rounded-2xl bg-gradient-to-br from-amber-500/15 via-yellow-500/10 to-amber-600/20 border border-amber-500/30 flex items-center justify-center shrink-0 shadow-[0_2px_10px_rgba(245,158,11,0.12)] group-hover:scale-105 group-hover:border-amber-500/50 transition-all duration-200`}>
      <svg className="w-6 h-6" viewBox="0 0 32 32" fill="none">
        <defs>
          <linearGradient id="rev_star" x1="16" y1="3" x2="16" y2="28" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stopColor="#FDE047" />
            <stop offset="50%" stopColor="#F59E0B" />
            <stop offset="100%" stopColor="#D97706" />
          </linearGradient>
        </defs>
        <path d="M16 3L19.9 11.2L29 12.5L22.5 18.8L24 28L16 23.6L8 28L9.5 18.8L3 12.5L12.1 11.2L16 3Z" fill="url(#rev_star)" />
        <path d="M16 3L19.9 11.2L29 12.5L22.5 18.8L24 28L16 23.6V3Z" fill="#FDE68A" fillOpacity="0.4" />
      </svg>
    </div>
  );
}

// 9. SHA-256 E-Signs
export function EsignVaultColorIcon({ className = "w-10 h-10" }: { className?: string }) {
  return (
    <div className={`${className} rounded-2xl bg-gradient-to-br from-emerald-500/15 via-teal-500/10 to-emerald-600/20 border border-emerald-500/30 flex items-center justify-center shrink-0 shadow-[0_2px_10px_rgba(16,185,129,0.12)] group-hover:scale-105 group-hover:border-emerald-500/50 transition-all duration-200`}>
      <svg className="w-6 h-6" viewBox="0 0 32 32" fill="none">
        <defs>
          <linearGradient id="doc_grad" x1="5" y1="3" x2="27" y2="29" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stopColor="#34D399" />
            <stop offset="100%" stopColor="#059669" />
          </linearGradient>
        </defs>
        <path d="M6 5C6 3.9 6.9 3 8 3H19L26 10V27C26 28.1 25.1 29 24 29H8C6.9 29 6 28.1 6 27V5Z" fill="url(#doc_grad)" />
        <path d="M19 3V10H26" fill="#A7F3D0" />
        <circle cx="16" cy="19.5" r="4.5" fill="#047857" />
        <path d="M14 19.5L15.5 21L18.5 18" stroke="#A7F3D0" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
      </svg>
    </div>
  );
}

// 10. Crew Dispatch
export function CrewDispatchColorIcon({ className = "w-10 h-10" }: { className?: string }) {
  return (
    <div className={`${className} rounded-2xl bg-gradient-to-br from-indigo-500/15 via-blue-500/10 to-indigo-600/20 border border-indigo-500/30 flex items-center justify-center shrink-0 shadow-[0_2px_10px_rgba(99,102,241,0.12)] group-hover:scale-105 group-hover:border-indigo-500/50 transition-all duration-200`}>
      <svg className="w-6 h-6" viewBox="0 0 32 32" fill="none">
        <defs>
          <linearGradient id="plane_grad1" x1="3" y1="3" x2="29" y2="29" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stopColor="#818CF8" />
            <stop offset="100%" stopColor="#4F46E5" />
          </linearGradient>
          <linearGradient id="plane_grad2" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stopColor="#A5B4FC" />
            <stop offset="100%" stopColor="#6366F1" />
          </linearGradient>
        </defs>
        <path d="M29 3L15 17" stroke="#C7D2FE" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" />
        <path d="M29 3L20 29L15 17L3 12L29 3Z" fill="url(#plane_grad1)" />
        <path d="M15 17L29 3L20 29L15 17Z" fill="url(#plane_grad2)" />
      </svg>
    </div>
  );
}

// 11. Master Calendar
export function MasterCalendarColorIcon({ className = "w-10 h-10" }: { className?: string }) {
  return (
    <div className={`${className} rounded-2xl bg-gradient-to-br from-rose-500/15 via-red-500/10 to-rose-600/20 border border-rose-500/30 flex items-center justify-center shrink-0 shadow-[0_2px_10px_rgba(225,29,72,0.12)] group-hover:scale-105 group-hover:border-rose-500/50 transition-all duration-200`}>
      <svg className="w-6 h-6" viewBox="0 0 32 32" fill="none">
        <defs>
          <linearGradient id="cal_body" x1="4" y1="6" x2="28" y2="28" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stopColor="#FB7185" />
            <stop offset="100%" stopColor="#E11D48" />
          </linearGradient>
        </defs>
        <rect x="4" y="6" width="24" height="22" rx="5" fill="url(#cal_body)" />
        <path d="M4 6C4 3.8 5.8 2 8 2H24C26.2 2 28 3.8 28 6V11H4V6Z" fill="#BE123C" />
        <rect x="9" y="1" width="3" height="4.5" rx="1.5" fill="#FECDD3" />
        <rect x="20" y="1" width="3" height="4.5" rx="1.5" fill="#FECDD3" />
        <text x="16" y="23" fill="white" fontSize="10.5" fontWeight="900" textAnchor="middle" fontFamily="sans-serif">31</text>
      </svg>
    </div>
  );
}

// 12. Task Board
export function TaskBoardColorIcon({ className = "w-10 h-10" }: { className?: string }) {
  return (
    <div className={`${className} rounded-2xl bg-gradient-to-br from-violet-500/15 via-purple-500/10 to-violet-600/20 border border-violet-500/30 flex items-center justify-center shrink-0 shadow-[0_2px_10px_rgba(139,92,246,0.12)] group-hover:scale-105 group-hover:border-violet-500/50 transition-all duration-200`}>
      <svg className="w-6 h-6" viewBox="0 0 32 32" fill="none">
        <defs>
          <linearGradient id="task_row1" x1="0" y1="0" x2="100%" y2="0">
            <stop offset="0%" stopColor="#A78BFA" />
            <stop offset="100%" stopColor="#7C3AED" />
          </linearGradient>
          <linearGradient id="task_row2" x1="0" y1="0" x2="100%" y2="0">
            <stop offset="0%" stopColor="#C4B5FD" />
            <stop offset="100%" stopColor="#8B5CF6" />
          </linearGradient>
          <linearGradient id="task_row3" x1="0" y1="0" x2="100%" y2="0">
            <stop offset="0%" stopColor="#DDD6FE" />
            <stop offset="100%" stopColor="#A78BFA" />
          </linearGradient>
        </defs>
        <rect x="4" y="5" width="24" height="6.5" rx="3" fill="url(#task_row1)" />
        <rect x="4" y="13" width="24" height="6.5" rx="3" fill="url(#task_row2)" />
        <rect x="4" y="21" width="16" height="6.5" rx="3" fill="url(#task_row3)" />
      </svg>
    </div>
  );
}

// 13. 18% GST Invoicing
export function GstInvoicingColorIcon({ className = "w-10 h-10" }: { className?: string }) {
  return (
    <div className={`${className} rounded-2xl bg-gradient-to-br from-emerald-500/15 via-green-500/10 to-teal-600/20 border border-emerald-500/30 flex items-center justify-center shrink-0 shadow-[0_2px_10px_rgba(16,185,129,0.12)] group-hover:scale-105 group-hover:border-emerald-500/50 transition-all duration-200`}>
      <svg className="w-6 h-6" viewBox="0 0 32 32" fill="none">
        <defs>
          <linearGradient id="gst_bill" x1="5" y1="3" x2="27" y2="29" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stopColor="#34D399" />
            <stop offset="100%" stopColor="#059669" />
          </linearGradient>
        </defs>
        <path d="M5 3H27V29L22.5 27L18 29L13.5 27L9 29L5 27V3Z" fill="url(#gst_bill)" />
        <path d="M10 9H22M10 14H17M10 19H22" stroke="#DCFCE7" strokeWidth="2.2" strokeLinecap="round" />
        <circle cx="21" cy="14" r="2" fill="#FEF08A" />
      </svg>
    </div>
  );
}

// 14. Gear & Inventory
export function AssetGearColorIcon({ className = "w-10 h-10" }: { className?: string }) {
  return (
    <div className={`${className} rounded-2xl bg-gradient-to-br from-zinc-700/15 via-zinc-800/10 to-zinc-900/20 border border-zinc-700/30 flex items-center justify-center shrink-0 shadow-[0_2px_10px_rgba(39,39,42,0.12)] group-hover:scale-105 group-hover:border-zinc-700/50 transition-all duration-200`}>
      <svg className="w-6 h-6" viewBox="0 0 32 32" fill="none">
        <defs>
          <linearGradient id="cam_body" x1="4" y1="8" x2="28" y2="28" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stopColor="#52525B" />
            <stop offset="50%" stopColor="#27272A" />
            <stop offset="100%" stopColor="#18181B" />
          </linearGradient>
          <linearGradient id="lens_ring" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stopColor="#A1A1AA" />
            <stop offset="100%" stopColor="#3F3F46" />
          </linearGradient>
        </defs>
        <rect x="4" y="8" width="24" height="18" rx="4.5" fill="url(#cam_body)" />
        <path d="M10 8V5C10 4.2 10.6 3.5 11.5 3.5H20.5C21.4 3.5 22 4.2 22 5V8" fill="#3F3F46" />
        <circle cx="16" cy="17" r="6" fill="url(#lens_ring)" />
        <circle cx="16" cy="17" r="4.2" fill="#09090B" />
        <circle cx="16" cy="17" r="2.2" fill="#38BDF8" />
        <circle cx="17.2" cy="15.8" r="0.8" fill="white" />
        <circle cx="24" cy="12" r="1.5" fill="#EF4444" />
      </svg>
    </div>
  );
}

// 15. Media Hub & RAW
export function MediaHubColorIcon({ className = "w-10 h-10" }: { className?: string }) {
  return (
    <div className={`${className} rounded-2xl bg-gradient-to-br from-fuchsia-500/15 via-pink-500/10 to-purple-600/20 border border-fuchsia-500/30 flex items-center justify-center shrink-0 shadow-[0_2px_10px_rgba(217,70,239,0.12)] group-hover:scale-105 group-hover:border-fuchsia-500/50 transition-all duration-200`}>
      <svg className="w-6 h-6" viewBox="0 0 32 32" fill="none">
        <defs>
          <linearGradient id="media_vault" x1="4" y1="5" x2="28" y2="27" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stopColor="#E879F9" />
            <stop offset="50%" stopColor="#C026D3" />
            <stop offset="100%" stopColor="#701A75" />
          </linearGradient>
        </defs>
        <rect x="4" y="5" width="24" height="22" rx="5.5" fill="url(#media_vault)" />
        <circle cx="16" cy="16" r="5.5" fill="#FDF4FF" fillOpacity="0.9" />
        <path d="M14 13L19.5 16L14 19V13Z" fill="#A21CAF" />
      </svg>
    </div>
  );
}

// 16. Multi-Tenant RBAC
export function RbacSecurityColorIcon({ className = "w-10 h-10" }: { className?: string }) {
  return (
    <div className={`${className} rounded-2xl bg-gradient-to-br from-cyan-500/15 via-teal-500/10 to-cyan-600/20 border border-cyan-500/30 flex items-center justify-center shrink-0 shadow-[0_2px_10px_rgba(8,145,178,0.12)] group-hover:scale-105 group-hover:border-cyan-500/50 transition-all duration-200`}>
      <svg className="w-6 h-6" viewBox="0 0 32 32" fill="none">
        <defs>
          <linearGradient id="shield_grad" x1="16" y1="3" x2="16" y2="29" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stopColor="#22D3EE" />
            <stop offset="50%" stopColor="#0891B2" />
            <stop offset="100%" stopColor="#0E7490" />
          </linearGradient>
        </defs>
        <path d="M16 3L5 7.5V14.5C5 21.8 9.7 28.5 16 30C22.3 28.5 27 21.8 27 14.5V7.5L16 3Z" fill="url(#shield_grad)" />
        <path d="M12 16L15 19L20.5 13.5" stroke="white" strokeWidth="2.8" strokeLinecap="round" strokeLinejoin="round" />
      </svg>
    </div>
  );
}

// 17. Email & SMTP
export function EmailSmtpColorIcon({ className = "w-10 h-10" }: { className?: string }) {
  return (
    <div className={`${className} rounded-2xl bg-gradient-to-br from-sky-500/15 via-blue-500/10 to-cyan-600/20 border border-sky-500/30 flex items-center justify-center shrink-0 shadow-[0_2px_10px_rgba(14,165,233,0.12)] group-hover:scale-105 group-hover:border-sky-500/50 transition-all duration-200`}>
      <svg className="w-6 h-6" viewBox="0 0 32 32" fill="none">
        <defs>
          <linearGradient id="mail_body" x1="4" y1="6" x2="28" y2="26" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stopColor="#38BDF8" />
            <stop offset="100%" stopColor="#0284C7" />
          </linearGradient>
        </defs>
        <rect x="4" y="6" width="24" height="20" rx="4.5" fill="url(#mail_body)" />
        <path d="M4 8.5L16 17.5L28 8.5" stroke="#E0F2FE" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" />
        <path d="M4 25L12 17M28 25L20 17" stroke="#BAE6FD" strokeWidth="1.8" strokeLinecap="round" />
      </svg>
    </div>
  );
}

// 18. Mobile App / PWA Push
export function PwaPushColorIcon({ className = "w-10 h-10" }: { className?: string }) {
  return (
    <div className={`${className} rounded-2xl bg-gradient-to-br from-pink-500/15 via-rose-500/10 to-pink-600/20 border border-pink-500/30 flex items-center justify-center shrink-0 shadow-[0_2px_10px_rgba(236,72,153,0.12)] group-hover:scale-105 group-hover:border-pink-500/50 transition-all duration-200`}>
      <svg className="w-6 h-6" viewBox="0 0 32 32" fill="none">
        <defs>
          <linearGradient id="phone_body" x1="7" y1="3" x2="25" y2="29" gradientUnits="userSpaceOnUse">
            <stop offset="0%" stopColor="#F472B6" />
            <stop offset="50%" stopColor="#EC4899" />
            <stop offset="100%" stopColor="#BE185D" />
          </linearGradient>
        </defs>
        <rect x="7" y="3" width="18" height="26" rx="4.5" fill="url(#phone_body)" />
        <rect x="12" y="5.5" width="8" height="2" rx="1" fill="#FCE7F3" />
        <circle cx="16" cy="24.5" r="2" fill="#FCE7F3" />
      </svg>
    </div>
  );
}

// 19. All 20 Modules
export function AllModulesColorIcon({ className = "w-10 h-10" }: { className?: string }) {
  return (
    <div className={`${className} rounded-2xl bg-gradient-to-br from-zinc-700/15 via-zinc-800/10 to-zinc-900/20 border border-zinc-700/30 flex items-center justify-center shrink-0 shadow-[0_2px_10px_rgba(24,24,27,0.12)] group-hover:scale-105 group-hover:border-zinc-700/50 transition-all duration-200`}>
      <svg className="w-6 h-6" viewBox="0 0 32 32" fill="none">
        <rect x="4" y="4" width="10" height="10" rx="3" fill="#18181B" />
        <rect x="18" y="4" width="10" height="10" rx="3" fill="#52525B" />
        <rect x="4" y="18" width="10" height="10" rx="3" fill="#71717A" />
        <rect x="18" y="18" width="10" height="10" rx="3" fill="#A1A1AA" />
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
