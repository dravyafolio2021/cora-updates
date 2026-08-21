'use client';

import React from 'react';
import { MessageSquare, CreditCard, Sheet, Mail, Receipt, FileSpreadsheet, Zap } from 'lucide-react';

const ecosystemTools = [
  { name: 'WhatsApp Business', icon: MessageSquare },
  { name: 'Razorpay', icon: CreditCard },
  { name: 'Instant UPI', icon: Zap },
  { name: 'Google Sheets', icon: Sheet },
  { name: 'Gmail', icon: Mail },
  { name: 'Tally Prime', icon: Receipt },
  { name: 'Microsoft Excel', icon: FileSpreadsheet },
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
            <span className="inline-flex items-center gap-1.5 px-3.5 py-1 bg-zinc-100 rounded-full text-zinc-600 text-xs font-medium border border-zinc-200/60 shadow-2xs">
              <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" />
              <span>Works seamlessly with the tools you already use</span>
            </span>
          </div>
        </div>
      </div>

      {/* ── Infinite Marquee Ticker ── */}
      <div className="flex w-full overflow-hidden select-none [mask-image:linear-gradient(to_right,transparent,black_15%,black_85%,transparent)]">
        <div className="flex min-w-full shrink-0 items-center justify-around gap-8 sm:gap-14 animate-marquee py-2">
          {ecosystemTools.concat(ecosystemTools).map((tool, idx) => {
            const IconComp = tool.icon;
            return (
              <div
                key={idx}
                className="flex items-center gap-2 text-zinc-700 font-sans font-medium text-xs sm:text-sm tracking-tight whitespace-nowrap opacity-75 hover:opacity-100 transition-opacity cursor-default"
              >
                <div className="w-5 h-5 rounded-md bg-zinc-100 text-zinc-900 flex items-center justify-center p-1 shrink-0 border border-zinc-200/80">
                  <IconComp className="w-3 h-3 text-zinc-800" />
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
