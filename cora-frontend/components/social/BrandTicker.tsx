'use client';

import React from 'react';

// ── Official Brand SVGs ──
const WhatsAppLogo = () => (
  <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
    <path
      d="M12.004 2C6.48 2 2 6.48 2 12c0 1.83.497 3.545 1.365 5.02L2 22l5.12-1.332A9.957 9.957 0 0012.004 22c5.523 0 10-4.48 10-10s-4.477-10-10-10z"
      fill="#25D366"
    />
    <path
      d="M17.472 14.382c-.3-.15-1.774-.874-2.05-.975-.275-.1-.475-.15-.675.15-.2.3-.775.975-.95 1.174-.175.2-.35.225-.65.075-.3-.15-1.265-.466-2.41-1.486-.89-.795-1.49-1.776-1.666-2.076-.174-.3-.018-.462.132-.612.135-.134.3-.35.45-.525.15-.175.2-.3.3-.5.1-.2.05-.375-.025-.525-.075-.15-.675-1.625-.925-2.225-.244-.585-.492-.505-.675-.515-.175-.01-.375-.01-.575-.01-.2 0-.525.075-.8.375-.275.3-1.05 1.025-1.05 2.5 0 1.475 1.075 2.9 1.225 3.1.15.2 2.115 3.23 5.124 4.53.716.31 1.275.495 1.71.634.72.228 1.375.196 1.893.118.577-.086 1.774-.725 2.024-1.425.25-.7.25-1.3.175-1.425-.075-.125-.275-.2-.575-.35z"
      fill="#FFF"
    />
  </svg>
);

const RazorpayLogo = () => (
  <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
    <path
      d="M14.07 2.18L4.62 13.97h6.29L8.46 21.82l10.92-12.78h-6.38l1.07-6.86z"
      fill="#0C2340"
    />
    <path
      d="M13.5 3L5.5 13.5h5.5l-2 7 9.5-11.5h-5.5l1-6z"
      fill="#3395FF"
    />
  </svg>
);

const UpiLogo = () => (
  <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
    <path d="M14.8 3.5L8.5 12h5.2l-1.8 8.5 7.6-9.5h-5.6l1.1-7.5z" fill="#009F4D" />
    <path d="M6.8 3.5L2.5 12h4.2l-1.5 8.5 6-9.5H7.2l1-7.5z" fill="#F15A24" />
  </svg>
);

const GoogleSheetsLogo = () => (
  <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
    <path d="M14.5 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V7.5L14.5 2z" fill="#0F9D58" />
    <path d="M14 2v6h6" fill="#87CEAB" />
    <path d="M8 12.5h8M8 16h8M8 9.5h4" stroke="#FFF" strokeWidth="1.6" strokeLinecap="round" />
  </svg>
);

const GmailLogo = () => (
  <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
    <path d="M2.5 7v10.5a2 2 0 002 2h2.5V11L2.5 7z" fill="#4285F4" />
    <path d="M21.5 7v10.5a2 2 0 01-2 2H17V11l4.5-4z" fill="#34A853" />
    <path d="M17 19.5h2.5a2 2 0 002-2V7l-4.5 3.5v9z" fill="#FBBC05" />
    <path d="M2.5 7l9.5 7.5L21.5 7v-.5a1.5 1.5 0 00-2.4-1.2L12 10.5 4.9 5.3A1.5 1.5 0 002.5 6.5V7z" fill="#EA4335" />
  </svg>
);

const TallyPrimeLogo = () => (
  <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
    <rect width="24" height="24" rx="5" fill="#F4B41A" />
    <path d="M6 7h12v3.5H13.5V18h-3V10.5H6V7z" fill="#005A9C" />
    <circle cx="17" cy="16.5" r="1.5" fill="#E31B23" />
  </svg>
);

const MicrosoftExcelLogo = () => (
  <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
    <rect x="2" y="3" width="20" height="18" rx="3" fill="#107C41" />
    <path d="M14 6h6v12h-6V6z" fill="#185A37" fillOpacity="0.4" />
    <path d="M8 8.5l2.2 3.5L8 15.5h1.8l1.3-2.3 1.3 2.3h1.8L12 12l2.2-3.5h-1.8L11.1 10 9.8 8.5H8z" fill="#FFF" />
  </svg>
);

const PhonePeLogo = () => (
  <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
    <rect width="24" height="24" rx="5" fill="#5F259F" />
    <path
      d="M14.8 6.5h-4.6c-1.2 0-2.2 1-2.2 2.2v8.6h2.8v-3.2h2.2c2.4 0 4.2-1.8 4.2-3.8s-1.8-3.8-4.2-3.8h1.8zm-1.8 4.8h-2.8V9.3h2.8c.8 0 1.4.6 1.4 1s-.6 1-1.4 1z"
      fill="#FFF"
    />
  </svg>
);

const GoogleDriveLogo = () => (
  <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
    <path d="M8.2 14.5L3.5 22h9.5l4.7-7.5H8.2z" fill="#0066DA" />
    <path d="M13 2.5L8.2 10.5 13 18.5h9.5L17.8 10.5 13 2.5z" fill="#00AC47" />
    <path d="M8.2 2.5L3.5 10.5l4.7 7.5L13 10.5 8.2 2.5z" fill="#FFBA00" />
  </svg>
);

const ZohoBooksLogo = () => (
  <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
    <rect x="2" y="2" width="9" height="9" rx="2" fill="#E53935" />
    <rect x="13" y="2" width="9" height="9" rx="2" fill="#1E88E5" />
    <rect x="2" y="13" width="9" height="9" rx="2" fill="#43A047" />
    <rect x="13" y="13" width="9" height="9" rx="2" fill="#FDD835" />
  </svg>
);

const ecosystemTools = [
  { name: 'WhatsApp Business', icon: WhatsAppLogo },
  { name: 'Razorpay', icon: RazorpayLogo },
  { name: 'Instant UPI', icon: UpiLogo },
  { name: 'Google Sheets', icon: GoogleSheetsLogo },
  { name: 'Gmail', icon: GmailLogo },
  { name: 'Tally Prime', icon: TallyPrimeLogo },
  { name: 'Microsoft Excel', icon: MicrosoftExcelLogo },
  { name: 'PhonePe', icon: PhonePeLogo },
  { name: 'Google Drive', icon: GoogleDriveLogo },
  { name: 'Zoho Books', icon: ZohoBooksLogo },
];

export function BrandTicker() {
  return (
    <section className="py-8 sm:py-10 bg-white relative z-10 overflow-hidden border-b border-zinc-100">
      
      {/* ── Centered Badge on Divider Line ── */}
      <div className="w-full max-w-[1140px] mx-auto px-4 sm:px-6 mb-6">
        <div className="relative flex justify-center">
          <div className="absolute inset-0 flex items-center" aria-hidden="true">
            <div className="w-full border-t border-zinc-200/70" />
          </div>
          <div className="relative px-4 bg-white">
            <span className="inline-flex items-center gap-1.5 px-3.5 py-1 bg-zinc-50 rounded-full text-zinc-600 text-xs font-medium border border-zinc-200/60 shadow-2xs">
              <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
              <span>Works seamlessly with the tools you already use</span>
            </span>
          </div>
        </div>
      </div>

      {/* ── Infinite Marquee Ticker with Official Brand Logos ── */}
      <div className="flex w-full overflow-hidden select-none [mask-image:linear-gradient(to_right,transparent,black_15%,black_85%,transparent)]">
        <div className="flex min-w-full shrink-0 items-center justify-around gap-8 sm:gap-12 animate-marquee py-2">
          {ecosystemTools.concat(ecosystemTools).map((tool, idx) => {
            const IconComp = tool.icon;
            return (
              <div
                key={idx}
                className="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-zinc-50/80 border border-zinc-200/60 text-zinc-800 font-sans font-medium text-xs sm:text-sm tracking-tight whitespace-nowrap shadow-2xs hover:bg-white hover:border-zinc-300 hover:shadow-xs transition-all duration-200 cursor-default"
              >
                <div className="flex items-center justify-center shrink-0">
                  <IconComp />
                </div>
                <span className="font-semibold text-zinc-900">{tool.name}</span>
              </div>
            );
          })}
        </div>
      </div>
    </section>
  );
}
