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
  SiStripe,
  SiInstagram,
} from 'react-icons/si';

// Official SVG Logos
const GoogleSheetsLogo = () => (
  <svg className="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none">
    <path d="M14.5 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V7.5L14.5 2z" fill="#0F9D58" />
    <path d="M14 2v6h6" fill="#87CEAB" />
    <path d="M7.5 12h9M7.5 15.5h9M7.5 8.5h4" stroke="#FFF" strokeWidth="1.8" strokeLinecap="round" />
  </svg>
);

const GmailLogo = () => (
  <svg className="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none">
    <path d="M2.5 7v10.5a2 2 0 002 2h2.5V11L2.5 7z" fill="#4285F4" />
    <path d="M21.5 7v10.5a2 2 0 01-2 2H17V11l4.5-4z" fill="#34A853" />
    <path d="M17 19.5h2.5a2 2 0 002-2V7l-4.5 3.5v9z" fill="#FBBC05" />
    <path d="M2.5 7l9.5 7.5L21.5 7v-.5a1.5 1.5 0 00-2.4-1.2L12 10.5 4.9 5.3A1.5 1.5 0 002.5 6.5V7z" fill="#EA4335" />
  </svg>
);

const MicrosoftExcelLogo = () => (
  <svg className="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none">
    <rect x="2" y="3" width="20" height="18" rx="3" fill="#107C41" />
    <path d="M14 6h6v12h-6V6z" fill="#185A37" fillOpacity="0.4" />
    <path d="M8 8.5l2.2 3.5L8 15.5h1.8l1.3-2.3 1.3 2.3h1.8L12 12l2.2-3.5h-1.8L11.1 10 9.8 8.5H8z" fill="#FFF" />
  </svg>
);

const SlackLogo = () => (
  <svg className="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none">
    <path d="M5.042 15.165a2.528 2.528 0 0 1-2.52 2.523A2.528 2.528 0 0 1 0 15.165a2.527 2.527 0 0 1 2.522-2.52h2.52v2.52zM6.313 15.165a2.527 2.527 0 0 1 2.521-2.52 2.527 2.527 0 0 1 2.521 2.52v6.313A2.528 2.528 0 0 1 8.834 24a2.528 2.528 0 0 1-2.521-2.522v-6.313z" fill="#E01E5A"/>
    <path d="M8.834 5.042a2.528 2.528 0 0 1-2.521-2.52A2.528 2.528 0 0 1 8.834 0a2.528 2.528 0 0 1 2.521 2.522v2.52H8.834zM8.834 6.313a2.528 2.528 0 0 1 2.521 2.521 2.528 2.528 0 0 1-2.521 2.521H2.522A2.528 2.528 0 0 1 0 8.834a2.528 2.528 0 0 1 2.522-2.521h6.312z" fill="#36C5F0"/>
    <path d="M18.956 8.834a2.528 2.528 0 0 1 2.522-2.521A2.528 2.528 0 0 1 24 8.834a2.528 2.528 0 0 1-2.522 2.521h-2.522V8.834zM17.688 8.834a2.528 2.528 0 0 1-2.523 2.521 2.527 2.527 0 0 1-2.52-2.521V2.522A2.527 2.527 0 0 1 15.165 0a2.528 2.528 0 0 1 2.523 2.522v6.312z" fill="#2EB67D"/>
    <path d="M15.165 18.956a2.528 2.528 0 0 1 2.523 2.522A2.528 2.528 0 0 1 15.165 24a2.527 2.527 0 0 1-2.52-2.522v-2.522h2.52zM15.165 17.688a2.527 2.527 0 0 1-2.52-2.523 2.527 2.527 0 0 1 2.52-2.52h6.313A2.527 2.527 0 0 1 24 15.165a2.528 2.528 0 0 1-2.522 2.523h-6.313z" fill="#ECB22E"/>
  </svg>
);

const UpiLogo = () => (
  <svg className="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none">
    <path d="M14.8 3.5L8.5 12h5.2l-1.8 8.5 7.6-9.5h-5.6l1.1-7.5z" fill="#009F4D" />
    <path d="M6.8 3.5L2.5 12h4.2l-1.5 8.5 6-9.5H7.2l1-7.5z" fill="#F15A24" />
  </svg>
);

const TallyPrimeLogo = () => (
  <svg className="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none">
    <rect width="24" height="24" rx="5" fill="#F4B41A" />
    <path d="M5.5 7.5h13V11H13.5V17.5h-3V11H5.5V7.5z" fill="#005A9C" />
    <circle cx="16.5" cy="16.5" r="1.5" fill="#E31B23" />
  </svg>
);

const rowOne = [
  { name: 'WhatsApp', icon: SiWhatsapp, color: '#25D366' },
  { name: 'PhonePe', icon: SiPhonepe, color: '#5F259F' },
  { name: 'Razorpay', icon: SiRazorpay, color: '#0C2340' },
  { name: 'Instant UPI', custom: UpiLogo },
  { name: 'Google Sheets', custom: GoogleSheetsLogo },
  { name: 'Gmail', custom: GmailLogo },
  { name: 'Excel', custom: MicrosoftExcelLogo },
  { name: 'Notion', icon: SiNotion, color: '#000000' },
];

const rowTwo = [
  { name: 'Tally Prime', custom: TallyPrimeLogo },
  { name: 'QuickBooks', icon: SiQuickbooks, color: '#2CA01C' },
  { name: 'Shopify', icon: SiShopify, color: '#7AB55C' },
  { name: 'Zoho Books', icon: SiZoho, color: '#E53935' },
  { name: 'Stripe', icon: SiStripe, color: '#635BFF' },
  { name: 'Instagram', icon: SiInstagram, color: '#E4405F' },
  { name: 'Slack', custom: SlackLogo },
  { name: 'Google Sheets', custom: GoogleSheetsLogo },
];

export function IntegrationsWaveSection() {
  return (
    <section className="py-20 sm:py-28 bg-[#FFFFFF] relative z-10 overflow-hidden border-b border-zinc-100">
      <div className="w-full max-w-[1280px] mx-auto px-4 sm:px-6">
        
        {/* ── Section Header (Matching Reference 1:1) ── */}
        <div className="max-w-[800px] mx-auto text-center mb-16 sm:mb-20">
          <div className="inline-flex items-center gap-1.5 text-indigo-600 text-xs font-bold uppercase tracking-widest mb-3">
            <span>INTEGRATIONS</span>
          </div>

          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl lg:text-[48px] font-bold leading-[1.1] tracking-[-0.03em] bg-gradient-to-r from-zinc-950 via-zinc-700 to-zinc-400 bg-clip-text text-transparent inline-block mb-4">
            Cora makes your other tools better
          </h2>
          <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[640px] mx-auto">
            Bring your essential business tools into one place with seamless native sync. From WhatsApp Business and UPI to Tally Prime and Google Sheets.
          </p>
        </div>

        {/* ── Dual Flowing Icon Rows with Central Cora Emblem ── */}
        <div className="relative py-12 select-none">
          
          {/* Row 1 Marquee */}
          <div className="flex w-full overflow-hidden mb-5 [mask-image:linear-gradient(to_right,transparent,black_15%,black_85%,transparent)]">
            <div className="flex min-w-full shrink-0 items-center justify-around gap-4 sm:gap-6 animate-marquee">
              {rowOne.concat(rowOne).concat(rowOne).map((tool, idx) => {
                const Icon = tool.icon;
                const Custom = tool.custom;
                return (
                  <div
                    key={idx}
                    className="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-white border border-zinc-200/80 shadow-[0px_4px_16px_rgba(0,0,0,0.04)] flex items-center justify-center shrink-0 hover:scale-110 hover:shadow-md transition-all duration-200"
                  >
                    {Custom ? (
                      <Custom />
                    ) : Icon ? (
                      <Icon className="w-5 h-5 sm:w-6 sm:h-6" style={{ color: tool.color }} />
                    ) : null}
                  </div>
                );
              })}
            </div>
          </div>

          {/* Center Cora Hub Emblem Overlay */}
          <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-20">
            <div className="w-24 h-24 sm:w-28 sm:h-28 rounded-full bg-white border-4 border-white shadow-[0px_16px_40px_rgba(0,0,0,0.12)] flex items-center justify-center p-2">
              <div className="w-full h-full rounded-full bg-gradient-to-tr from-indigo-600 via-sky-500 to-emerald-400 flex items-center justify-center shadow-inner">
                <span className="font-display font-black text-2xl sm:text-3xl text-white tracking-tighter">
                  C
                </span>
              </div>
            </div>
          </div>

          {/* Row 2 Marquee (Reverse) */}
          <div className="flex w-full overflow-hidden [mask-image:linear-gradient(to_right,transparent,black_15%,black_85%,transparent)]">
            <div className="flex min-w-full shrink-0 items-center justify-around gap-4 sm:gap-6 animate-marquee-reverse">
              {rowTwo.concat(rowTwo).concat(rowTwo).map((tool, idx) => {
                const Icon = tool.icon;
                const Custom = tool.custom;
                return (
                  <div
                    key={idx}
                    className="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-white border border-zinc-200/80 shadow-[0px_4px_16px_rgba(0,0,0,0.04)] flex items-center justify-center shrink-0 hover:scale-110 hover:shadow-md transition-all duration-200"
                  >
                    {Custom ? (
                      <Custom />
                    ) : Icon ? (
                      <Icon className="w-5 h-5 sm:w-6 sm:h-6" style={{ color: tool.color }} />
                    ) : null}
                  </div>
                );
              })}
            </div>
          </div>

        </div>

      </div>
    </section>
  );
}
