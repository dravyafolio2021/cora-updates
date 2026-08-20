'use client';

import React, { useState } from 'react';
import type { Metadata } from 'next';
import { Check, ArrowRight, Sparkles, Zap, ShieldCheck, HelpCircle } from 'lucide-react';
import { trackEvent } from '@/components/analytics/Analytics';

const PLANS_DATA = [
  {
    id: 'free',
    name: 'Free Forever',
    tagline: 'Essential autonomous tools for solo creators and freelancers.',
    priceUSD: 0,
    priceINR: 0,
    period: '/forever',
    highlight: false,
    ctaText: 'Get started for Free',
    ctaLink: 'https://app.heycora.in/workspace/login?plan=free',
    features: [
      '1,000 complimentary AI agent runs/mo',
      'Google Gemini 2.0 Flash & GPT-4o mini',
      'Unlimited digital e-sign agreements',
      'Automated 18% GST tax invoicing',
      'Basic WhatsApp call-sheet templates',
      'Community support & updates'
    ]
  },
  {
    id: 'pro',
    name: 'Studio Pro',
    tagline: 'Complete operating backbone for growing commercial studios.',
    priceUSD: 19,
    priceINR: 1499,
    period: '/month',
    highlight: true,
    badge: 'MOST POPULAR',
    ctaText: 'Start 14-Day Free Trial',
    ctaLink: 'https://app.heycora.in/workspace/login?plan=pro',
    features: [
      'Unlimited multi-model AI reasoning runs',
      'Anthropic Claude 3.5 Sonnet & GPT-4o',
      'Official Meta WhatsApp Cloud API dispatch',
      'Custom branded PDF invoices with GSTIN',
      'Multi-seat crew access (Up to 5 seats)',
      'Direct Google Drive & S3 media vault link',
      'Priority 24/7 founder & engineering support'
    ]
  },
  {
    id: 'scale',
    name: 'Agency Scale',
    tagline: 'High-throughput infrastructure for multi-bay production houses.',
    priceUSD: 49,
    priceINR: 3999,
    period: '/month',
    highlight: false,
    ctaText: 'Contact Enterprise Sales',
    ctaLink: 'mailto:dravya.bansal@heycora.in?subject=Agency%20Scale%20Plan%20Inquiry',
    features: [
      'Everything in Studio Pro with zero limits',
      'Unlimited team & crew seat permissions',
      'White-label client portal & custom domain',
      'Dedicated WhatsApp phone number routing',
      'Multi-workspace studio branch management',
      'Custom CA tax export formats (GSTR-1/3B)',
      'Dedicated account manager & SLA guarantee'
    ]
  }
];

const COMPARISON_ROWS = [
  { feature: 'Complimentary AI Agent Runs', free: '1,000 / mo', pro: 'Unlimited', scale: 'Unlimited' },
  { feature: 'Frontier AI (Claude 3.5 Sonnet)', free: '—', pro: 'Included', scale: 'Included' },
  { feature: 'Sub-400ms AI (Gemini 2.0 Flash)', free: 'Included', pro: 'Included', scale: 'Included' },
  { feature: 'Tamper-Evident E-Signatures (SHA-256)', free: 'Unlimited', pro: 'Unlimited', scale: 'Unlimited' },
  { feature: '18% GST Invoicing & Tax Calculation', free: 'Basic', pro: 'Automated Splits', scale: 'Custom Formats' },
  { feature: 'WhatsApp Call-Sheet Dispatch', free: 'Manual Templates', pro: 'Automated Cloud API', scale: 'Dedicated Phone Number' },
  { feature: 'Team Seats', free: '1 Seat', pro: '5 Seats', scale: 'Unlimited Seats' },
  { feature: 'White-Label Client Portal', free: '—', pro: 'Standard', scale: 'Custom Domain' },
  { feature: 'Indian IT Act 2000 Audit Logs', free: 'Included', pro: 'Included', scale: 'Included' },
  { feature: 'Support SLA', free: 'Community', pro: '24/7 Priority', scale: 'Dedicated Manager' },
];

export default function PricingPage() {
  const [currency, setCurrency] = useState<'INR' | 'USD'>('INR');

  return (
    <main className="w-full relative pt-32 sm:pt-40 pb-20 overflow-hidden bg-white">
      
      {/* ── Top Hero Section ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 text-center mb-16 sm:mb-20">
        <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-zinc-100 rounded-xl border border-zinc-200/90 text-xs font-semibold text-zinc-900 mb-4 shadow-2xs">
          <span className="w-2 h-2 rounded-full bg-emerald-500" />
          <span>Simple, Transparent Pricing</span>
        </div>

        <h1 className="font-display text-4xl xs:text-5xl sm:text-6xl font-bold text-zinc-950 leading-[1.1] tracking-[-0.035em] max-w-[840px] mx-auto mb-5">
          Predictable pricing that scales with your studio
        </h1>

        <p className="text-zinc-600 text-base sm:text-xl font-normal leading-relaxed max-w-[620px] mx-auto mb-8">
          Start for free with 1,000 AI runs and full e-signatures. Upgrade only when you scale your team and automated dispatch.
        </p>

        {/* Currency Switcher */}
        <div className="inline-flex items-center p-1 bg-zinc-100 rounded-xl border border-zinc-200 shadow-2xs">
          <button
            onClick={() => {
              setCurrency('INR');
              trackEvent('currency_switch', { currency: 'INR' });
            }}
            className={`px-4 py-1.5 rounded-lg text-xs font-semibold transition-all ${
              currency === 'INR'
                ? 'bg-zinc-950 text-white shadow-xs'
                : 'text-zinc-600 hover:text-zinc-950'
            }`}
          >
            INR (₹) India
          </button>
          <button
            onClick={() => {
              setCurrency('USD');
              trackEvent('currency_switch', { currency: 'USD' });
            }}
            className={`px-4 py-1.5 rounded-lg text-xs font-semibold transition-all ${
              currency === 'USD'
                ? 'bg-zinc-950 text-white shadow-xs'
                : 'text-zinc-600 hover:text-zinc-950'
            }`}
          >
            USD ($) Global
          </button>
        </div>
      </section>

      {/* ── 3 Pricing Cards Grid ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-24 sm:mb-32">
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch">
          {PLANS_DATA.map((plan) => {
            const priceDisplay = currency === 'INR' 
              ? (plan.priceINR === 0 ? '₹0' : `₹${plan.priceINR.toLocaleString('en-IN')}`)
              : (plan.priceUSD === 0 ? '$0' : `$${plan.priceUSD}`);

            return (
              <div
                key={plan.id}
                className={`rounded-[32px] p-8 sm:p-10 flex flex-col justify-between transition-all duration-300 relative ${
                  plan.highlight
                    ? 'bg-[#100F12] text-white border-2 border-zinc-800 shadow-2xl lg:-translate-y-2'
                    : 'bg-white text-zinc-950 border border-zinc-200/90 shadow-[0px_10px_30px_rgba(0,0,0,0.04)]'
                }`}
              >
                {plan.badge && (
                  <div className="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-emerald-500 text-white text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider shadow-xs">
                    {plan.badge}
                  </div>
                )}

                <div>
                  <div className="space-y-2 mb-6">
                    <h3 className={`font-display text-2xl font-bold tracking-tight ${plan.highlight ? 'text-white' : 'text-zinc-950'}`}>
                      {plan.name}
                    </h3>
                    <p className={`text-xs sm:text-sm font-normal leading-relaxed ${plan.highlight ? 'text-zinc-400' : 'text-zinc-600'}`}>
                      {plan.tagline}
                    </p>
                  </div>

                  {/* Price Tag */}
                  <div className="flex items-baseline gap-1 pb-6 mb-6 border-b border-zinc-200/20">
                    <span className={`font-display text-4xl sm:text-5xl font-black tracking-tight ${plan.highlight ? 'text-white' : 'text-zinc-950'}`}>
                      {priceDisplay}
                    </span>
                    <span className={`text-xs font-semibold ${plan.highlight ? 'text-zinc-400' : 'text-zinc-500'}`}>
                      {plan.period}
                    </span>
                  </div>

                  {/* Features List */}
                  <div className="space-y-3 mb-8">
                    {plan.features.map((feat, i) => (
                      <div key={i} className="flex items-start gap-2.5 text-xs sm:text-[13px] font-medium">
                        <Check className={`w-4 h-4 shrink-0 mt-0.5 ${plan.highlight ? 'text-emerald-400' : 'text-emerald-600'}`} />
                        <span className={plan.highlight ? 'text-zinc-300' : 'text-zinc-700'}>
                          {feat}
                        </span>
                      </div>
                    ))}
                  </div>
                </div>

                {/* Card CTA */}
                <div>
                  <a
                    href={plan.ctaLink}
                    className={`w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold transition-all shadow-sm ${
                      plan.highlight
                        ? 'bg-white text-zinc-950 hover:bg-zinc-100'
                        : 'bg-zinc-950 text-white hover:bg-zinc-800'
                    }`}
                  >
                    <span>{plan.ctaText}</span>
                    <ArrowRight className="w-3.5 h-3.5" />
                  </a>
                </div>

              </div>
            );
          })}
        </div>
      </section>

      {/* ── Feature Comparison Table ── */}
      <section className="w-full max-w-[1040px] mx-auto px-4 sm:px-6 mb-24 sm:mb-32">
        <div className="text-center mb-12">
          <h2 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight">
            Detailed plan feature matrix
          </h2>
        </div>

        <div className="bg-white border border-zinc-200/90 rounded-[28px] overflow-hidden shadow-2xs">
          <div className="overflow-x-auto">
            <table className="w-full text-left text-xs sm:text-sm border-collapse">
              <thead>
                <tr className="bg-zinc-50/80 border-b border-zinc-200">
                  <th className="p-4 sm:p-5 font-bold text-zinc-900">Feature</th>
                  <th className="p-4 sm:p-5 font-bold text-zinc-900 text-center">Free Forever</th>
                  <th className="p-4 sm:p-5 font-bold text-zinc-900 text-center bg-zinc-100/60">Studio Pro</th>
                  <th className="p-4 sm:p-5 font-bold text-zinc-900 text-center">Agency Scale</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-zinc-200/70">
                {COMPARISON_ROWS.map((row, i) => (
                  <tr key={i} className="hover:bg-zinc-50/40 transition-colors">
                    <td className="p-4 sm:p-5 font-medium text-zinc-800">{row.feature}</td>
                    <td className="p-4 sm:p-5 text-center text-zinc-600 font-medium">{row.free}</td>
                    <td className="p-4 sm:p-5 text-center text-zinc-950 font-bold bg-zinc-50/50">{row.pro}</td>
                    <td className="p-4 sm:p-5 text-center text-zinc-600 font-medium">{row.scale}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </section>

      {/* ── Bottom Section CTA ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        <div className="w-full rounded-[36px] bg-zinc-950 text-white p-8 sm:p-14 text-center relative overflow-hidden border border-zinc-800 shadow-xl">
          <div className="relative z-10 max-w-[680px] mx-auto space-y-6">
            <h2 className="font-display text-3xl sm:text-4xl font-bold tracking-tight">
              Start your 14-day free studio trial today
            </h2>
            <p className="text-zinc-400 text-sm sm:text-base leading-relaxed font-normal">
              Zero setup fees. Zero credit card needed. Begin generating branded scopes, digital e-signs, and 18% GST invoices in under 2 minutes.
            </p>

            <div className="flex items-center justify-center flex-wrap gap-3.5 pt-2">
              <a
                href="https://app.heycora.in/workspace/login?source=pricing_bottom"
                className="inline-flex items-center gap-2 bg-white text-zinc-950 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-100 transition-all shadow-sm group"
              >
                <span>Get started for Free</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-600 group-hover:translate-x-0.5 transition-transform" />
              </a>

              <a
                href="mailto:dravya.bansal@heycora.in?subject=Pricing%20Inquiry%20from%20Cora"
                className="inline-flex items-center gap-2 bg-zinc-900 text-white border border-zinc-700 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-2xs"
              >
                <span>Chat with Founder</span>
              </a>
            </div>
          </div>
        </div>
      </section>

    </main>
  );
}
