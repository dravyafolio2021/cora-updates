'use client';

import React from 'react';
import { MessageSquare, Mail, CreditCard, Sparkles, Database, HardDrive, CheckCircle } from 'lucide-react';

const integrations = [
  {
    name: 'WhatsApp Cloud API',
    category: 'Messaging',
    description: 'Official Meta Cloud API integration for automated call-sheet alerts & client booking confirmations.',
    badge: 'Native Dispatch',
    active: true,
  },
  {
    name: 'Gmail & Google Calendar',
    category: 'Productivity',
    description: 'Two-way schedule synchronization for photoshoot call times and automated client follow-up emails.',
    badge: '2-Way Sync',
    active: true,
  },
  {
    name: 'Razorpay UPI & RuPay',
    category: 'Payments',
    description: 'Instant Indian payment settlement with GPay, PhonePe, Paytm, CRED, NetBanking, and corporate cards.',
    badge: 'Instant UPI',
    active: true,
  },
  {
    name: 'Anthropic Claude 3.5',
    category: 'Frontier AI',
    description: 'Deep reasoning model for detailed real estate contracts, production call sheets, and creative scripts.',
    badge: 'Frontier AI',
    active: true,
  },
  {
    name: 'OpenAI GPT-4o',
    category: 'Frontier AI',
    description: 'High-speed JSON extraction, client proposal generation, and multilingual listing descriptions.',
    badge: 'Frontier AI',
    active: true,
  },
  {
    name: 'Google Gemini 2.0 Flash',
    category: 'Frontier AI',
    description: 'Ultra-low-latency responses and high-volume data analysis for high-throughput automated funnels.',
    badge: 'Sub-400ms',
    active: true,
  },
  {
    name: 'Stripe International',
    category: 'Payments',
    description: 'Global USD/EUR multi-currency card processing with automated tax compliance for overseas clients.',
    badge: 'Global Rails',
    active: true,
  },
  {
    name: 'Google Drive Storage',
    category: 'Asset Vault',
    description: 'Direct high-res RAW photo and 4K video deliverable links attached directly to client invoices.',
    badge: 'Cloud Vault',
    active: true,
  },
];

export function IntegrationsGrid() {
  return (
    <section id="ecosystem" className="py-16 md:py-24 relative z-10 bg-white border-t border-zinc-100">
      <div className="w-full max-w-[1140px] mx-auto px-4 sm:px-6">
        
        {/* Section Header */}
        <div className="text-center max-w-[780px] mx-auto mb-14">
          <div className="inline-flex items-center gap-1.5 font-sans text-[0.8125rem] font-medium text-zinc-600 px-3.5 py-1 bg-white border border-zinc-200 rounded-full mb-3.5 shadow-sm">
            <span>Seamless Connectivity</span>
          </div>
          <h2 className="font-display text-[clamp(1.85rem,3.8vw,2.75rem)] font-[550] tracking-[-0.035em] text-zinc-950 leading-[1.18] mb-3">
            Plug Cora into the tools you already rely on.
          </h2>
          <p className="font-sans text-[clamp(0.85rem,1.1vw,1rem)] text-zinc-600 leading-[1.55]">
            Zero messy API tokens. Official enterprise connectors built right into the platform.
          </p>
        </div>

        {/* 4x2 Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          {integrations.map((item, idx) => (
            <div
              key={idx}
              className="bg-zinc-50/70 border border-zinc-200/90 rounded-xl p-5 flex flex-col justify-between hover:bg-white hover:border-zinc-300 hover:shadow-md transition-all duration-200 group"
            >
              <div>
                <div className="flex items-center justify-between mb-3">
                  <span className="text-[0.625rem] font-bold uppercase tracking-wider text-zinc-400">
                    {item.category}
                  </span>
                  <span className="text-[0.625rem] font-semibold px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full">
                    {item.badge}
                  </span>
                </div>
                <h3 className="font-display text-sm font-bold text-zinc-950 mb-1.5 group-hover:text-zinc-900 transition-colors">
                  {item.name}
                </h3>
                <p className="text-xs text-zinc-500 leading-relaxed">
                  {item.description}
                </p>
              </div>

              <div className="mt-4 pt-3 border-t border-zinc-200/60 flex items-center gap-1.5 text-[0.6875rem] font-semibold text-emerald-600">
                <CheckCircle className="w-3 h-3" />
                <span>Active Integration</span>
              </div>
            </div>
          ))}
        </div>

      </div>
    </section>
  );
}
