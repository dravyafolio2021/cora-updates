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
  SiGithub,
  SiWebflow,
  SiLinear,
  SiFramer,
  SiLoom,
  SiAsana,
  SiWordpress,
  SiHubspot,
} from 'react-icons/si';

// ── Official Multi-Color / Custom Brand SVGs ──────────────────────────────────

// 1. Figma Official 5-Color SVG
const FigmaLogo = () => (
  <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
    <path d="M8 24C10.2091 24 12 22.2091 12 20V16H8C5.79086 16 4 17.7909 4 20C4 22.2091 5.79086 24 8 24Z" fill="#0ACF83"/>
    <path d="M4 12C4 9.79086 5.79086 8 8 8H12V16H8C5.79086 16 4 14.2091 4 12Z" fill="#A259FF"/>
    <path d="M4 4C4 1.79086 5.79086 0 8 0H12V8H8C5.79086 8 4 6.20914 4 4Z" fill="#F24E1E"/>
    <path d="M12 0H16C18.2091 0 20 1.79086 20 4C20 6.20914 18.2091 8 16 8H12V0Z" fill="#FF7262"/>
    <path d="M20 12C20 14.2091 18.2091 16 16 16C13.7909 16 12 14.2091 12 12C12 9.79086 13.7909 8 16 8C18.2091 8 20 9.79086 20 12Z" fill="#1ABCFE"/>
  </svg>
);

// 2. Slack Official 4-Color Hash Logo
const SlackLogo = () => (
  <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
    <path d="M5.042 15.165a2.528 2.528 0 0 1-2.52-2.523A2.528 2.528 0 0 1 0 15.165a2.527 2.527 0 0 1 2.522-2.52h2.52v2.52zM6.313 15.165a2.527 2.527 0 0 1 2.521-2.52 2.527 2.527 0 0 1 2.521 2.52v6.313A2.528 2.528 0 0 1 8.834 24a2.528 2.528 0 0 1-2.521-2.522v-6.313z" fill="#E01E5A"/>
    <path d="M8.834 5.042a2.528 2.528 0 0 1-2.521-2.52A2.528 2.528 0 0 1 8.834 0a2.528 2.528 0 0 1 2.521 2.522v2.52H8.834zM8.834 6.313a2.528 2.528 0 0 1 2.521 2.521 2.528 2.528 0 0 1-2.521 2.521H2.522A2.528 2.528 0 0 1 0 8.834a2.528 2.528 0 0 1 2.522-2.521h6.312z" fill="#36C5F0"/>
    <path d="M18.956 8.834a2.528 2.528 0 0 1 2.522-2.521A2.528 2.528 0 0 1 24 8.834a2.528 2.528 0 0 1-2.522 2.521h-2.522V8.834zM17.688 8.834a2.528 2.528 0 0 1-2.523 2.521 2.527 2.527 0 0 1-2.52-2.521V2.522A2.527 2.527 0 0 1 15.165 0a2.528 2.528 0 0 1 2.523 2.522v6.312z" fill="#2EB67D"/>
    <path d="M15.165 18.956a2.528 2.528 0 0 1 2.523 2.522A2.528 2.528 0 0 1 15.165 24a2.527 2.527 0 0 1-2.52-2.522v-2.522h2.52zM15.165 17.688a2.527 2.527 0 0 1-2.52-2.523 2.527 2.527 0 0 1 2.52-2.52h6.313A2.527 2.527 0 0 1 24 15.165a2.528 2.528 0 0 1-2.522 2.523h-6.313z" fill="#ECB22E"/>
  </svg>
);

// 3. Google Drive Official Triangle Logo
const GoogleDriveLogo = () => (
  <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
    <path d="M8.2 3.5L2 14.2l3.8 6.5L12 10 8.2 3.5z" fill="#0066DA" />
    <path d="M15.8 3.5H8.2L12 10l7.6 13.2h7.6L15.8 3.5z" fill="#00AC47" />
    <path d="M2 14.2l3.8 6.5h15.4L17.4 14.2H2z" fill="#FFBA00" />
    <path d="M8.2 3.5l3.8 6.5H27l-3.8-6.5H8.2z" fill="#EA4335" />
  </svg>
);

// 4. Gmail Official 4-Color M Logo
const GmailLogo = () => (
  <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
    <path d="M2.5 7v10.5a2 2 0 002 2h2.5V11L2.5 7z" fill="#4285F4" />
    <path d="M21.5 7v10.5a2 2 0 01-2 2H17V11l4.5-4z" fill="#34A853" />
    <path d="M17 19.5h2.5a2 2 0 002-2V7l-4.5 3.5v9z" fill="#FBBC05" />
    <path d="M2.5 7l9.5 7.5L21.5 7v-.5a1.5 1.5 0 00-2.4-1.2L12 10.5 4.9 5.3A1.5 1.5 0 002.5 6.5V7z" fill="#EA4335" />
  </svg>
);

// 5. Google Sheets Official SVG Logo
const GoogleSheetsLogo = () => (
  <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
    <path d="M14.5 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V7.5L14.5 2z" fill="#0F9D58" />
    <path d="M14 2v6h6" fill="#87CEAB" />
    <path d="M7.5 12h9M7.5 15.5h9M7.5 8.5h4" stroke="#FFF" strokeWidth="1.8" strokeLinecap="round" />
  </svg>
);

// 6. Microsoft Excel Official Green Logo
const MicrosoftExcelLogo = () => (
  <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
    <rect x="2" y="3" width="20" height="18" rx="3" fill="#107C41" />
    <path d="M14 6h6v12h-6V6z" fill="#185A37" fillOpacity="0.4" />
    <path d="M8 8.5l2.2 3.5L8 15.5h1.8l1.3-2.3 1.3 2.3h1.8L12 12l2.2-3.5h-1.8L11.1 10 9.8 8.5H8z" fill="#FFF" />
  </svg>
);

// 7. Instant UPI Official 2-Tone Logo
const UpiLogo = () => (
  <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
    <path d="M14.8 3.5L8.5 12h5.2l-1.8 8.5 7.6-9.5h-5.6l1.1-7.5z" fill="#009F4D" />
    <path d="M6.8 3.5L2.5 12h4.2l-1.5 8.5 6-9.5H7.2l1-7.5z" fill="#F15A24" />
  </svg>
);

// 8. Tally Prime Official Gold/Navy Logo
const TallyPrimeLogo = () => (
  <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
    <rect width="24" height="24" rx="5" fill="#F4B41A" />
    <path d="M5.5 7.5h13V11H13.5V17.5h-3V11H5.5V7.5z" fill="#005A9C" />
    <circle cx="16.5" cy="16.5" r="1.5" fill="#E31B23" />
  </svg>
);

// 9. Canva Official Cyan Logo
const CanvaLogo = () => (
  <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
    <circle cx="12" cy="12" r="10" fill="#00C4CC" />
    <path d="M14.5 9c-.5-1-1.5-1.5-2.7-1.5-2.5 0-4.3 2-4.3 4.5 0 2.4 1.8 4.5 4.3 4.5 1.5 0 2.5-.7 3-1.6l-1.4-.9c-.4.6-1 1-1.6 1-1.4 0-2.4-1.2-2.4-3s1-3 2.4-3c.8 0 1.3.4 1.7.9l1-1z" fill="#FFF" />
  </svg>
);

// ── Daily Routine Agency Tools (Row 1: Design, Dev, Operations & PM) ──────────
const rowOneTools = [
  { name: 'Figma', custom: FigmaLogo },
  { name: 'Slack', custom: SlackLogo },
  { name: 'Notion', icon: SiNotion, color: '#000000' },
  { name: 'Google Drive', custom: GoogleDriveLogo },
  { name: 'GitHub', icon: SiGithub, color: '#181717' },
  { name: 'Webflow', icon: SiWebflow, color: '#146EF5' },
  { name: 'Linear', icon: SiLinear, color: '#5E6AD2' },
  { name: 'Framer', icon: SiFramer, color: '#0055FF' },
  { name: 'Loom', icon: SiLoom, color: '#625DF5' },
  { name: 'Asana', icon: SiAsana, color: '#F06A6A' },
  { name: 'WordPress', icon: SiWordpress, color: '#21759B' },
  { name: 'Gmail', custom: GmailLogo },
];

// ── Daily Routine Agency Tools (Row 2: Finance, Billing, Marketing & Clients) ─
const rowTwoTools = [
  { name: 'Stripe', icon: SiStripe, color: '#635BFF' },
  { name: 'Razorpay', icon: SiRazorpay, color: '#0C2340' },
  { name: 'WhatsApp Business', icon: SiWhatsapp, color: '#25D366' },
  { name: 'QuickBooks', icon: SiQuickbooks, color: '#2CA01C' },
  { name: 'Zoho Books', icon: SiZoho, color: '#E53935' },
  { name: 'HubSpot', icon: SiHubspot, color: '#FF7A59' },
  { name: 'Shopify', icon: SiShopify, color: '#7AB55C' },
  { name: 'Tally Prime', custom: TallyPrimeLogo },
  { name: 'Google Sheets', custom: GoogleSheetsLogo },
  { name: 'Instant UPI', custom: UpiLogo },
  { name: 'Microsoft Excel', custom: MicrosoftExcelLogo },
  { name: 'Canva', custom: CanvaLogo },
  { name: 'PhonePe', icon: SiPhonepe, color: '#5F259F' },
];

export function BrandTicker() {
  return (
    <section className="pt-2 pb-6 sm:pt-3 sm:pb-8 bg-white relative z-10 overflow-hidden space-y-2.5 sm:space-y-3">
      
      {/* ── Track 1: Moving Left (Design, Dev & Operations Stack) ── */}
      <div className="flex w-full overflow-hidden select-none [mask-image:linear-gradient(to_right,transparent,black_8%,black_92%,transparent)]">
        <div className="flex min-w-full shrink-0 items-center justify-around gap-3 sm:gap-4.5 animate-marquee py-0.5">
          {rowOneTools.concat(rowOneTools).map((tool, idx) => {
            const IconComp = tool.icon;
            const CustomComp = tool.custom;
            return (
              <div
                key={`row1-${idx}`}
                className="flex items-center gap-2 sm:gap-2.5 px-3 sm:px-3.5 py-1.5 sm:py-2 rounded-xl sm:rounded-2xl bg-white border border-zinc-200/70 text-zinc-800 font-sans font-medium text-xs sm:text-sm tracking-tight whitespace-nowrap shadow-[0px_2px_8px_rgba(0,0,0,0.02)] hover:border-zinc-300 hover:shadow-xs transition-all duration-200 cursor-default"
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

      {/* ── Track 2: Moving Right in Opposite Direction (Finance, Billing & Communication) ── */}
      <div className="flex w-full overflow-hidden select-none [mask-image:linear-gradient(to_right,transparent,black_8%,black_92%,transparent)]">
        <div className="flex min-w-full shrink-0 items-center justify-around gap-3 sm:gap-4.5 animate-marquee-reverse py-0.5">
          {rowTwoTools.concat(rowTwoTools).map((tool, idx) => {
            const IconComp = tool.icon;
            const CustomComp = tool.custom;
            return (
              <div
                key={`row2-${idx}`}
                className="flex items-center gap-2 sm:gap-2.5 px-3 sm:px-3.5 py-1.5 sm:py-2 rounded-xl sm:rounded-2xl bg-white border border-zinc-200/70 text-zinc-800 font-sans font-medium text-xs sm:text-sm tracking-tight whitespace-nowrap shadow-[0px_2px_8px_rgba(0,0,0,0.02)] hover:border-zinc-300 hover:shadow-xs transition-all duration-200 cursor-default"
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
