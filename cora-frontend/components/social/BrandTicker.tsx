'use client';

import React from 'react';
import {
  SiWhatsapp,
  SiRazorpay,
  SiPhonepe,
  SiNotion,
  SiQuickbooks,
  SiShopify,
  SiZoho,
} from 'react-icons/si';

// ── Official Multi-Color / Authentic Brand SVG Components ──

// Google Sheets Official Logo
const GoogleSheetsLogo = () => (
  <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
    <path d="M14.5 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V7.5L14.5 2z" fill="#0F9D58" />
    <path d="M14 2v6h6" fill="#87CEAB" />
    <path d="M7.5 12h9M7.5 15.5h9M7.5 8.5h4" stroke="#FFF" strokeWidth="1.8" strokeLinecap="round" />
  </svg>
);

// Gmail Official 4-Color Logo
const GmailLogo = () => (
  <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
    <path d="M2.5 7v10.5a2 2 0 002 2h2.5V11L2.5 7z" fill="#4285F4" />
    <path d="M21.5 7v10.5a2 2 0 01-2 2H17V11l4.5-4z" fill="#34A853" />
    <path d="M17 19.5h2.5a2 2 0 002-2V7l-4.5 3.5v9z" fill="#FBBC05" />
    <path d="M2.5 7l9.5 7.5L21.5 7v-.5a1.5 1.5 0 00-2.4-1.2L12 10.5 4.9 5.3A1.5 1.5 0 002.5 6.5V7z" fill="#EA4335" />
  </svg>
);

// Google Drive Official 3-Color Triangle Logo
const GoogleDriveLogo = () => (
  <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
    <path d="M8.2 14.5L3.5 22h9.5l4.7-7.5H8.2z" fill="#0066DA" />
    <path d="M13 2.5L8.2 10.5 13 18.5h9.5L17.8 10.5 13 2.5z" fill="#00AC47" />
    <path d="M8.2 2.5L3.5 10.5l4.7 7.5L13 10.5 8.2 2.5z" fill="#FFBA00" />
  </svg>
);

// Microsoft Excel Official Green Workbook Logo
const MicrosoftExcelLogo = () => (
  <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
    <rect x="2" y="3" width="20" height="18" rx="3" fill="#107C41" />
    <path d="M14 6h6v12h-6V6z" fill="#185A37" fillOpacity="0.4" />
    <path d="M8 8.5l2.2 3.5L8 15.5h1.8l1.3-2.3 1.3 2.3h1.8L12 12l2.2-3.5h-1.8L11.1 10 9.8 8.5H8z" fill="#FFF" />
  </svg>
);

// Slack Official 4-Color Octothorpe Logo
const SlackLogo = () => (
  <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
    <path d="M5.042 15.165a2.528 2.528 0 0 1-2.52 2.523A2.528 2.528 0 0 1 0 15.165a2.527 2.527 0 0 1 2.522-2.52h2.52v2.52zM6.313 15.165a2.527 2.527 0 0 1 2.521-2.52 2.527 2.527 0 0 1 2.521 2.52v6.313A2.528 2.528 0 0 1 8.834 24a2.528 2.528 0 0 1-2.521-2.522v-6.313z" fill="#E01E5A"/>
    <path d="M8.834 5.042a2.528 2.528 0 0 1-2.521-2.52A2.528 2.528 0 0 1 8.834 0a2.528 2.528 0 0 1 2.521 2.522v2.52H8.834zM8.834 6.313a2.528 2.528 0 0 1 2.521 2.521 2.528 2.528 0 0 1-2.521 2.521H2.522A2.528 2.528 0 0 1 0 8.834a2.528 2.528 0 0 1 2.522-2.521h6.312z" fill="#36C5F0"/>
    <path d="M18.956 8.834a2.528 2.528 0 0 1 2.522-2.521A2.528 2.528 0 0 1 24 8.834a2.528 2.528 0 0 1-2.522 2.521h-2.522V8.834zM17.688 8.834a2.528 2.528 0 0 1-2.523 2.521 2.527 2.527 0 0 1-2.52-2.521V2.522A2.527 2.527 0 0 1 15.165 0a2.528 2.528 0 0 1 2.523 2.522v6.312z" fill="#2EB67D"/>
    <path d="M15.165 18.956a2.528 2.528 0 0 1 2.523 2.522A2.528 2.528 0 0 1 15.165 24a2.527 2.527 0 0 1-2.52-2.522v-2.522h2.52zM15.165 17.688a2.527 2.527 0 0 1-2.52-2.523 2.527 2.527 0 0 1 2.52-2.52h6.313A2.527 2.527 0 0 1 24 15.165a2.528 2.528 0 0 1-2.522 2.523h-6.313z" fill="#ECB22E"/>
  </svg>
);

// NPCI UPI Official 2-Tone Logo
const UpiLogo = () => (
  <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
    <path d="M14.8 3.5L8.5 12h5.2l-1.8 8.5 7.6-9.5h-5.6l1.1-7.5z" fill="#009F4D" />
    <path d="M6.8 3.5L2.5 12h4.2l-1.5 8.5 6-9.5H7.2l1-7.5z" fill="#F15A24" />
  </svg>
);

// Tally Prime Official Logo
const TallyPrimeLogo = () => (
  <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
    <rect width="24" height="24" rx="5" fill="#F4B41A" />
    <path d="M5.5 7.5h13V11H13.5V17.5h-3V11H5.5V7.5z" fill="#005A9C" />
    <circle cx="16.5" cy="16.5" r="1.5" fill="#E31B23" />
  </svg>
);

// Canva Official Cyan Logo
const CanvaLogo = () => (
  <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
    <circle cx="12" cy="12" r="10" fill="#00C4CC" />
    <path d="M14.5 9c-.5-1-1.5-1.5-2.7-1.5-2.5 0-4.3 2-4.3 4.5 0 2.4 1.8 4.5 4.3 4.5 1.5 0 2.5-.7 3-1.6l-1.4-.9c-.4.6-1 1-1.6 1-1.4 0-2.4-1.2-2.4-3s1-3 2.4-3c.8 0 1.3.4 1.7.9l1-1z" fill="#FFF" />
  </svg>
);

// Instagram Official Gradient Logo
const InstagramLogo = () => (
  <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
    <rect width="24" height="24" rx="6" fill="url(#ig-grad)" />
    <rect x="5.5" y="5.5" width="13" height="13" rx="3.5" stroke="#FFF" strokeWidth="1.8" />
    <circle cx="12" cy="12" r="3.2" stroke="#FFF" strokeWidth="1.8" />
    <circle cx="15.8" cy="8.2" r="0.9" fill="#FFF" />
    <defs>
      <linearGradient id="ig-grad" x1="2" y1="22" x2="22" y2="2" gradientUnits="userSpaceOnUse">
        <stop stopColor="#FFDC80" />
        <stop offset="0.5" stopColor="#FD1D1D" />
        <stop offset="1" stopColor="#C13584" />
      </linearGradient>
    </defs>
  </svg>
);

const ecosystemTools = [
  { name: 'WhatsApp Business', icon: SiWhatsapp, color: '#25D366' },
  { name: 'PhonePe', icon: SiPhonepe, color: '#5F259F' },
  { name: 'Razorpay', icon: SiRazorpay, color: '#0C2340' },
  { name: 'Instant UPI', custom: UpiLogo },
  { name: 'Google Sheets', custom: GoogleSheetsLogo },
  { name: 'Gmail', custom: GmailLogo },
  { name: 'Microsoft Excel', custom: MicrosoftExcelLogo },
  { name: 'Google Drive', custom: GoogleDriveLogo },
  { name: 'Slack', custom: SlackLogo },
  { name: 'Notion', icon: SiNotion, color: '#000000' },
  { name: 'Tally Prime', custom: TallyPrimeLogo },
  { name: 'QuickBooks', icon: SiQuickbooks, color: '#2CA01C' },
  { name: 'Shopify', icon: SiShopify, color: '#7AB55C' },
  { name: 'Zoho Books', icon: SiZoho, color: '#E53935' },
  { name: 'Canva', custom: CanvaLogo },
  { name: 'Instagram', custom: InstagramLogo },
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
      <div className="flex w-full overflow-hidden select-none [mask-image:linear-gradient(to_right,transparent,black_12%,black_88%,transparent)]">
        <div className="flex min-w-full shrink-0 items-center justify-around gap-6 sm:gap-8 animate-marquee py-2">
          {ecosystemTools.concat(ecosystemTools).map((tool, idx) => {
            const IconComp = tool.icon;
            const CustomComp = tool.custom;
            return (
              <div
                key={idx}
                className="flex items-center gap-2.5 px-3.5 py-2 rounded-2xl bg-white border border-zinc-200/70 text-zinc-800 font-sans font-medium text-xs sm:text-sm tracking-tight whitespace-nowrap shadow-[0px_2px_8px_rgba(0,0,0,0.03)] hover:border-zinc-300 hover:shadow-xs transition-all duration-200 cursor-default"
              >
                <div className="flex items-center justify-center shrink-0 w-4.5 h-4.5">
                  {CustomComp ? (
                    <CustomComp />
                  ) : IconComp ? (
                    <IconComp className="w-4 h-4" style={{ color: tool.color }} />
                  ) : null}
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
