'use client';

import React from 'react';

// ── 1. ANTHROPIC CLAUDE LOGO ──
export function ClaudeLogo({ className = "w-4 h-4" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <path
        d="M17.472 2a.75.75 0 0 1 .71.512l1.642 5.053a.75.75 0 0 1-.22.788l-4.088 3.395 3.916 3.593a.75.75 0 0 1 .184.8l-1.854 4.978a.75.75 0 0 1-1.345.105l-3.415-4.07-3.415 4.07a.75.75 0 0 1-1.345-.105l-1.854-4.978a.75.75 0 0 1 .184-.8l3.916-3.593-4.088-3.395a.75.75 0 0 1-.22-.788L7.3 2.512A.75.75 0 0 1 8.01 2h9.462z"
        fill="#D97706"
      />
    </svg>
  );
}

// ── 2. GOOGLE GEMINI LOGO ──
export function GeminiLogo({ className = "w-4 h-4" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <path
        d="M12 0C12 6.627 6.627 12 0 12C6.627 12 12 17.373 12 24C12 17.373 17.373 12 24 12C17.373 12 12 6.627 12 0Z"
        fill="url(#gemini-grad-brand)"
      />
      <defs>
        <linearGradient id="gemini-grad-brand" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stopColor="#4E82EE" />
          <stop offset="50%" stopColor="#9B72CB" />
          <stop offset="100%" stopColor="#D96570" />
        </linearGradient>
      </defs>
    </svg>
  );
}

// ── 3. OPENAI LOGO ──
export function OpenAILogo({ className = "w-4 h-4" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="currentColor">
      <path
        d="M22.2819 9.8211a5.9847 5.9847 0 0 0-.5157-4.9108 6.0462 6.0462 0 0 0-6.5098-2.9A6.0651 6.0651 0 0 0 4.9807 4.1818a5.9847 5.9847 0 0 0-3.9977 2.9 6.0462 6.0462 0 0 0 .7427 7.0966 5.98 5.98 0 0 0 .511 4.9107 6.051 6.051 0 0 0 6.5146 2.9001A5.9847 5.9847 0 0 0 13.259 24a6.0557 6.0557 0 0 0 5.7718-4.2058 5.9894 5.9894 0 0 0 3.9977-2.9001 6.0557 6.0557 0 0 0-.7466-7.0729z"
        fill="#10A37F"
      />
    </svg>
  );
}

// ── 4. OFFICIAL WHATSAPP LOGO ──
export function WhatsAppLogo({ className = "w-4 h-4" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="currentColor">
      <path
        d="M12.031 0C5.395 0 0 5.395 0 12.031c0 2.12.553 4.188 1.604 6.01L.062 24l6.143-1.61A12.03 12.03 0 0 0 12.03 24c6.637 0 12.031-5.395 12.031-12.031S18.668 0 12.031 0z"
        fill="#25D366"
      />
      <path
        d="M17.5 14.3c-.3-.15-1.78-.88-2.06-.98-.28-.1-.48-.15-.68.15-.2.3-.78.98-.95 1.18-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.68-1.64-.93-2.25-.24-.59-.49-.51-.68-.52h-.58c-.2 0-.52.07-.8.37-.27.3-1.05 1.03-1.05 2.51s1.08 2.91 1.23 3.11c.15.2 2.12 3.24 5.14 4.54.72.31 1.28.5 1.72.64.72.23 1.38.2 1.9.12.58-.09 1.78-.73 2.03-1.43.25-.7.25-1.3.18-1.43-.08-.13-.28-.2-.58-.35z"
        fill="#FFFFFF"
      />
    </svg>
  );
}

// ── 5. NPCI UPI LOGO ──
export function UPILogo({ className = "w-4 h-4" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <path d="M14.6 2L8.2 22H4.4L10.8 2h3.8z" fill="#097939" />
      <path d="M19.6 2l-6.4 20h-3.8L15.8 2h3.8z" fill="#F58220" />
    </svg>
  );
}

// ── 6. GOOGLE CALENDAR LOGO ──
export function GoogleCalendarLogo({ className = "w-4 h-4" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <rect x="3" y="4" width="18" height="17" rx="3" fill="#FFFFFF" stroke="#4285F4" strokeWidth="2" />
      <path d="M3 8h18" stroke="#4285F4" strokeWidth="2" />
      <rect x="7" y="11" width="3" height="3" fill="#EA4335" rx="0.5" />
      <rect x="11" y="11" width="3" height="3" fill="#FBBC04" rx="0.5" />
      <rect x="15" y="11" width="3" height="3" fill="#34A853" rx="0.5" />
      <rect x="7" y="15" width="3" height="3" fill="#4285F4" rx="0.5" />
      <rect x="11" y="15" width="3" height="3" fill="#34A853" rx="0.5" />
      <path d="M7 2v3M17 2v3" stroke="#4285F4" strokeWidth="2" strokeLinecap="round" />
    </svg>
  );
}

// ── 7. SONY CINEMA / FX6 BADGE ──
export function SonyCinemaLogo({ className = "w-4 h-4" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="currentColor">
      <rect x="2" y="5" width="20" height="14" rx="3" fill="#18181B" />
      <circle cx="12" cy="12" r="4.5" fill="#3F3F46" stroke="#FA5252" strokeWidth="1.2" />
      <circle cx="12" cy="12" r="2" fill="#18181B" />
      <circle cx="18" cy="8" r="1" fill="#FA5252" />
    </svg>
  );
}

// ── 8. BLACKMAGIC DAVINCI RESOLVE LOGO ──
export function DaVinciResolveLogo({ className = "w-4 h-4" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <circle cx="12" cy="12" r="10" fill="#18181B" />
      <path d="M12 4a8 8 0 0 1 8 8h-8V4z" fill="#E11D48" />
      <path d="M20 12a8 8 0 0 1-8 8v-8h8z" fill="#3B82F6" />
      <path d="M12 20a8 8 0 0 1-8-8h8v8z" fill="#F59E0B" />
      <path d="M4 12a8 8 0 0 1 8-8v8H4z" fill="#10B981" />
    </svg>
  );
}

// ── 9. SECTION 10A IT ACT 2000 / E-SIGN COURT SEAL ──
export function ITActSealLogo({ className = "w-4 h-4" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <path d="M12 2L4 5v6.5c0 5 3.4 9.7 8 10.5 4.6-.8 8-5.5 8-10.5V5l-8-3z" fill="#047857" />
      <path d="M8.5 12l2.5 2.5 5-5" stroke="#FFFFFF" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}

// ── 10. TALLY PRIME & ZOHO BOOKS SYNC ──
export function TallyZohoLogo({ className = "w-4 h-4" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <rect x="3" y="3" width="18" height="18" rx="4" fill="#0284C7" />
      <path d="M7 8h10M12 8v9M9 17h6" stroke="#FFFFFF" strokeWidth="2.2" strokeLinecap="round" />
    </svg>
  );
}

// ── 11. SLACK OFFICIAL LOGO ──
export function SlackLogo({ className = "w-4 h-4" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <path d="M5.042 15.165a2.528 2.528 0 0 1-2.52-2.523 2.52 2.52 0 0 1 2.52-2.52h2.52v2.52a2.52 2.52 0 0 1-2.52 2.523z" fill="#E01E5A" />
      <path d="M8.833 15.165a2.528 2.528 0 0 1 2.521-2.523 2.52 2.52 0 0 1 2.52 2.52v6.313a2.52 2.52 0 0 1-2.52 2.521 2.528 2.528 0 0 1-2.521-2.521v-6.313z" fill="#E01E5A" />
      <path d="M8.833 5.059a2.528 2.528 0 0 1 2.521-2.52 2.52 2.52 0 0 1 2.52 2.52v2.52h-2.52a2.528 2.528 0 0 1-2.521-2.52z" fill="#36C5F0" />
      <path d="M8.833 8.85a2.528 2.528 0 0 1 2.521 2.521 2.52 2.52 0 0 1-2.52 2.52H2.521A2.52 2.52 0 0 1 0 11.371a2.528 2.528 0 0 1 2.521-2.52h6.312z" fill="#36C5F0" />
      <path d="M18.956 8.85a2.528 2.528 0 0 1 2.522 2.521 2.52 2.52 0 0 1-2.522 2.52h-2.52v-2.52a2.52 2.52 0 0 1 2.52-2.521z" fill="#2EB67D" />
      <path d="M15.165 8.85a2.528 2.528 0 0 1-2.52-2.521 2.52 2.52 0 0 1 2.52-2.52h6.313a2.52 2.52 0 0 1 2.521 2.52 2.528 2.528 0 0 1-2.521 2.521h-6.313z" fill="#2EB67D" />
      <path d="M15.165 18.956a2.528 2.528 0 0 1-2.52 2.522 2.52 2.52 0 0 1-2.52-2.522v-2.52h2.52a2.52 2.52 0 0 1 2.52 2.52z" fill="#ECB22E" />
      <path d="M15.165 15.165a2.528 2.528 0 0 1-2.52-2.52 2.52 2.52 0 0 1 2.52-2.523h6.313a2.52 2.52 0 0 1 2.521 2.523 2.528 2.528 0 0 1-2.521 2.52h-6.313z" fill="#ECB22E" />
    </svg>
  );
}

// ── 12. STRIPE OFFICIAL LOGO ──
export function StripeLogo({ className = "w-4 h-4" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <rect x="2" y="4" width="20" height="16" rx="3.5" fill="#635BFF" />
      <path d="M13.5 10.8c-.8-.4-1.2-.7-1.2-1.1 0-.4.4-.7 1.1-.7.8 0 1.6.3 2.2.7l.6-1.5c-.7-.4-1.7-.7-2.8-.7-2.1 0-3.5 1.1-3.5 2.8 0 2.2 3 1.8 3 2.8 0 .5-.5.8-1.3.8-1 0-2-.5-2.6-.9l-.6 1.6c.8.5 2 .9 3.2.9 2.2 0 3.7-1.1 3.7-2.9 0-2.3-3.2-1.9-3.2-2.9z" fill="#FFFFFF" />
    </svg>
  );
}

// ── 13. CLOUDFLARE LOGO ──
export function CloudflareLogo({ className = "w-4 h-4" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <path d="M18.5 10.5a4.5 4.5 0 0 0-8.6-1.6 3.5 3.5 0 0 0-4.4 4.3A3.5 3.5 0 0 0 6.5 19h12a3.5 3.5 0 0 0 0-7c0-.5-.2-1-.5-1.5z" fill="#F38020" />
      <path d="M14.5 19h4a3.5 3.5 0 0 0 0-7c-.4 0-.8.1-1.1.2A4.5 4.5 0 0 0 14.5 19z" fill="#FAAD3F" />
    </svg>
  );
}

// ── 14. APPLE PWA / IOS LOGO ──
export function AppleLogo({ className = "w-4 h-4" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="currentColor">
      <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.87c.65-.79 1.1-1.9 0.98-3.01-1 .04-2.19.67-2.88 1.48-.61.71-1.14 1.84-.99 2.93 1.12.09 2.24-.61 2.89-1.4z" fill="#18181B" />
    </svg>
  );
}

// ── 15. META ADS / INSTAGRAM LOGO ──
export function MetaLogo({ className = "w-4 h-4" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <path d="M12 7.5c-2.4 0-4.4 1.6-5.4 3.7C5.6 9.1 3.9 8 2 8 0.9 8 0 8.9 0 10v4c0 1.1.9 2 2 2 1.9 0 3.6-1.1 4.6-3.2 1 2.1 3 3.7 5.4 3.7s4.4-1.6 5.4-3.7c1 2.1 2.7 3.2 4.6 3.2 1.1 0 2-.9 2-2v-4c0-1.1-.9-2-2-2-1.9 0-3.6 1.1-4.6 3.2-1-2.1-3-3.7-5.4-3.7z" fill="#0668E1" />
    </svg>
  );
}

// ── 16. GOOGLE REVIEWS STAR LOGO ──
export function GoogleReviewsLogo({ className = "w-4 h-4" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <rect x="2" y="2" width="20" height="20" rx="5" fill="#4285F4" />
      <path d="M12 6.5l1.6 3.3 3.6.5-2.6 2.5.6 3.6-3.2-1.7-3.2 1.7.6-3.6-2.6-2.5 3.6-.5L12 6.5z" fill="#FBBC04" />
    </svg>
  );
}

// ── 17. GST COUNCIL OFFICIAL INDIA BADGE ──
export function GSTCouncilLogo({ className = "w-4 h-4" }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none">
      <circle cx="12" cy="12" r="10" fill="#0F766E" />
      <path d="M6 12h12M12 6v12" stroke="#FFFFFF" strokeWidth="2.5" strokeLinecap="round" />
      <circle cx="12" cy="12" r="4" fill="#134E4A" stroke="#FFFFFF" strokeWidth="1" />
    </svg>
  );
}
