'use client';

import React from 'react';

export interface BrandLogoProps {
  className?: string;
  size?: number;
  monochrome?: boolean;
}

// ── 1. ANTHROPIC CLAUDE LOGO ──
export function ClaudeLogo({ className = "w-4 h-4", size, monochrome = false }: BrandLogoProps) {
  const style = size ? { width: size, height: size } : undefined;
  return (
    <svg className={className} style={style} width={size} height={size} viewBox="0 0 24 24" fill="currentColor">
      <path
        d="m4.7144 15.9555 4.7174-2.6471.079-.2307-.079-.1275h-.2307l-.7893-.0486-2.6956-.0729-2.3375-.0971-2.2646-.1214-.5707-.1215-.5343-.7042.0546-.3522.4797-.3218.686.0608 1.5179.1032 2.2767.1578 1.6514.0972 2.4468.255h.3886l.0546-.1579-.1336-.0971-.1032-.0972L6.973 9.8356l-2.55-1.6879-1.3356-.9714-.7225-.4918-.3643-.4614-.1578-1.0078.6557-.7225.8803.0607.2246.0607.8925.686 1.9064 1.4754 2.4893 1.8336.3643.3035.1457-.1032.0182-.0728-.164-.2733-1.3539-2.4467-1.445-2.4893-.6435-1.032-.17-.6194c-.0607-.255-.1032-.4674-.1032-.7285L6.287.1335 6.6997 0l.9957.1336.419.3642.6192 1.4147 1.0018 2.2282 1.5543 3.0296.4553.8985.2429.8318.091.255h.1579v-.1457l.1275-1.706.2368-2.0947.2307-2.6957.0789-.7589.3764-.9107.7468-.4918.5828.2793.4797.686-.0668.4433-.2853 1.8517-.5586 2.9021-.3643 1.9429h.2125l.2429-.2429.9835-1.3053 1.6514-2.0643.7286-.8196.85-.9046.5464-.4311h1.0321l.759 1.1293-.34 1.1657-1.0625 1.3478-.8804 1.1414-1.2628 1.7-.7893 1.36.0729.1093.1882-.0183 2.8535-.607 1.5421-.2794 1.8396-.3157.8318.3886.091.3946-.3278.8075-1.967.4857-2.3072.4614-3.4364.8136-.0425.0304.0486.0607 1.5482.1457.6618.0364h1.621l3.0175.2247.7892.522.4736.6376-.079.4857-1.2142.6193-1.6393-.3886-3.825-.9107-1.3113-.3279h-.1822v.1093l1.0929 1.0686 2.0035 1.8092 2.5075 2.3314.1275.5768-.3218.4554-.34-.0486-2.2039-1.6575-.85-.7468-1.9246-1.621h-.1275v.17l.4432.6496 2.3436 3.5214.1214 1.0807-.17.3521-.6071.2125-.6679-.1214-1.3721-1.9246L14.38 17.959l-1.1414-1.9428-.1397.079-.674 7.2552-.3156.3703-.7286.2793-.6071-.4614-.3218-.7468.3218-1.4753.3886-1.9246.3157-1.53.2853-1.9004.17-.6314-.0121-.0425-.1397.0182-1.4328 1.9672-2.1796 2.9446-1.7243 1.8456-.4128.164-.7164-.3704.0667-.6618.4008-.5889 2.386-3.0357 1.4389-1.882.929-1.0868-.0062-.1579h-.0546l-6.3385 4.1164-1.1293.1457-.4857-.4554.0608-.7467.2307-.2429 1.9064-1.3114Z"
        fill={monochrome ? "currentColor" : "#D97706"}
      />
    </svg>
  );
}

// ── 2. GOOGLE GEMINI LOGO ──
export function GeminiLogo({ className = "w-4 h-4", size, monochrome = false }: BrandLogoProps) {
  const style = size ? { width: size, height: size } : undefined;
  return (
    <svg className={className} style={style} width={size} height={size} viewBox="0 0 24 24" fill="none">
      <defs>
        <linearGradient id="gemini-grad-brand" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stopColor="#4E82EE" />
          <stop offset="50%" stopColor="#9B72CB" />
          <stop offset="100%" stopColor="#D96570" />
        </linearGradient>
      </defs>
      <path
        d="M12 0C12 6.627 6.627 12 0 12c6.627 0 12 5.373 12 12 0-6.627 5.373-12 12-12-6.627 0-12-5.373-12-12z"
        fill={monochrome ? "currentColor" : "url(#gemini-grad-brand)"}
      />
    </svg>
  );
}

// ── 3. OPENAI / CHATGPT LOGO ──
export function OpenAILogo({ className = "w-4 h-4", size, monochrome = false }: BrandLogoProps) {
  const style = size ? { width: size, height: size } : undefined;
  return (
    <svg className={className} style={style} width={size} height={size} viewBox="0 0 24 24" fill="none">
      <path
        d="M22.2819 9.8211a5.9847 5.9847 0 0 0-.5157-4.9108 6.0462 6.0462 0 0 0-6.5098-2.9A6.0651 6.0651 0 0 0 4.9807 4.1818a5.9847 5.9847 0 0 0-3.9977 2.9 6.0462 6.0462 0 0 0 .7427 7.0966 5.98 5.98 0 0 0 .511 4.9107 6.051 6.051 0 0 0 6.5146 2.9001A5.9847 5.9847 0 0 0 13.2599 24a6.0557 6.0557 0 0 0 5.7718-4.2058 5.9894 5.9894 0 0 0 3.9977-2.9001 6.0557 6.0557 0 0 0-.7475-7.0729zm-9.022 12.6081a4.4755 4.4755 0 0 1-2.8764-1.0408l.1419-.0804 4.7783-2.7582a.7948.7948 0 0 0 .3927-.6813v-6.7369l2.02 1.1683a.071.071 0 0 1 .038.052v5.5826a4.504 4.504 0 0 1-4.4945 4.4947zm-9.6607-4.1254a4.4708 4.4708 0 0 1-.5346-3.0137l.142.0852 4.783 2.7582a.7712.7712 0 0 0 .7806 0l5.8428-3.3685v2.3324a.0804.0804 0 0 1-.0332.0615L9.74 19.9502a4.4992 4.4992 0 0 1-6.1408-1.6464zM2.3408 7.8956a4.485 4.485 0 0 1 2.3655-1.9728V11.6a.7664.7664 0 0 0 .3879.6765l5.8144 3.3543-2.0201 1.1683a.0757.0757 0 0 1-.071 0l-4.8303-2.7866A4.4992 4.4992 0 0 1 2.3408 7.872zm16.5963 3.8558L13.1038 8.364 15.1239 7.2a.0757.0757 0 0 1 .071 0l4.8303 2.7913a4.4944 4.4944 0 0 1-.6765 8.1042v-5.6772a.79.79 0 0 0-.4076-.6667zm2.0107-3.0231l-.142-.0852-4.7735-2.7818a.7759.7759 0 0 0-.7854 0L9.409 9.2297V6.8974a.0662.0662 0 0 1 .0284-.0615l4.8303-2.7866a4.4992 4.4992 0 0 1 6.6802 4.6608zm-12.6401 4.135l2.4633-1.4208 2.4633 1.4208v2.8417l-2.4633 1.4208-2.4633-1.4208zM7.65 1.761a4.4992 4.4992 0 0 1 5.371 1.055l-.142.0804-4.783 2.763a.79.79 0 0 0-.3927.6812v6.7417L5.683 11.914a.0757.0757 0 0 1-.038-.052V6.2794a4.4944 4.4944 0 0 1 2.005-4.5184z"
        fill={monochrome ? "currentColor" : "#10A37F"}
      />
    </svg>
  );
}
export const ChatGPTLogo = OpenAILogo;

// ── 3B. PERPLEXITY AI OFFICIAL LOGO ──
export function PerplexityLogo({ className = "w-4 h-4", size, monochrome = false }: BrandLogoProps) {
  const style = size ? { width: size, height: size } : undefined;
  return (
    <svg className={className} style={style} width={size} height={size} viewBox="0 0 24 24" fill="none">
      <path
        d="M22.3977 7.0896h-2.3106V.0676l-7.5094 6.3542V.1577h-1.1554v6.1966L4.4904 0v7.0896H1.6023v10.3976h2.8882V24l6.932-6.3591v6.2005h1.1554v-6.0469l6.9318 6.1807v-6.4879h2.8882V7.0896zm-3.4657-4.531v4.531h-5.355l5.355-4.531zm-13.2862.0676 4.8691 4.4634H5.6458V2.6262zM2.7576 16.332V8.245h7.8476l-6.1149 6.1147v1.9723H2.7576zm2.8882 5.0404v-3.8852h.0001v-2.6488l5.7763-5.7764v7.0111l-5.7764 5.2993zm12.7086.0248-5.7766-5.1509V9.0618l5.7766 5.7766v6.5588zm2.8882-5.0652h-1.733v-1.9723L13.3948 8.245h7.8478v8.087z"
        fill={monochrome ? "currentColor" : "#20808D"}
      />
    </svg>
  );
}

// ── 3C. YOUTUBE OFFICIAL LOGO ──
export function YouTubeLogo({ className = "w-4 h-4", size, monochrome = false }: BrandLogoProps) {
  const style = size ? { width: size, height: size } : undefined;
  return (
    <svg className={className} style={style} width={size} height={size} viewBox="0 0 24 24" fill="none">
      <path
        d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814z"
        fill={monochrome ? "currentColor" : "#FF0000"}
      />
      <path
        d="M9.545 15.568V8.432L15.818 12l-6.273 3.568z"
        fill={monochrome ? "#18181B" : "#FFFFFF"}
      />
    </svg>
  );
}

// ── 4. OFFICIAL WHATSAPP LOGO ──
export function WhatsAppLogo({ className = "w-4 h-4", size }: BrandLogoProps) {
  const style = size ? { width: size, height: size } : undefined;
  return (
    <svg className={className} style={style} width={size} height={size} viewBox="0 0 24 24" fill="currentColor">
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
