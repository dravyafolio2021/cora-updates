'use client';

import React, { useState } from 'react';
import { Check, ArrowRight, Sparkles, Zap, Shield, HelpCircle } from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

export function PricingSection() {
  const [billing, setBilling] = useState<'monthly' | 'annual'>('annual');
  const isAnnual = billing === 'annual';

  const handlePlanSelect = (planName: string, price: number) => {
    trackEvent('plan_selected', { plan: planName, billing, price });
    window.location.href = `https://app.heycora.in/workspace/login?plan=${encodeURIComponent(planName)}&billing=${encodeURIComponent(billing)}`;
  };

  return (
    <section id="pricing" className="py-20 sm:py-28 relative z-10 bg-white border-b border-zinc-200/80">
      <div className="w-full max-w-[1200px] mx-auto px-4 sm:px-6">
        
        {/* Section Header */}
        <div className="text-center max-w-[760px] mx-auto mb-10 sm:mb-12">
          <div className="inline-flex items-center gap-1.5 px-3.5 py-1 bg-zinc-100 rounded-full text-zinc-800 text-xs font-semibold uppercase tracking-wider mb-4 border border-zinc-200/80">
            <Sparkles className="w-3.5 h-3.5 text-zinc-950" />
            <span>HONEST PRICING</span>
          </div>

          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl font-bold text-zinc-950 leading-[1.12] tracking-[-0.03em] mb-3.5">
            Honest Indian pricing. No hidden fees.
          </h2>

          <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[600px] mx-auto">
            Get started for free today. Upgrade when your business grows.
          </p>
        </div>

        {/* Billing Switcher (Monthly / Annual) */}
        <div className="flex items-center justify-center mb-12 sm:mb-16">
          <div className="inline-flex items-center bg-zinc-100 p-1.5 rounded-2xl border border-zinc-200/80 shadow-2xs">
            <button
              type="button"
              onClick={() => setBilling('monthly')}
              className={`px-5 py-2 text-xs sm:text-sm font-semibold rounded-xl transition-all ${
                !isAnnual ? 'bg-zinc-950 text-white shadow-2xs' : 'text-zinc-600 hover:text-zinc-950'
              }`}
            >
              Monthly
            </button>
            <button
              type="button"
              onClick={() => setBilling('annual')}
              className={`px-5 py-2 text-xs sm:text-sm font-semibold rounded-xl transition-all flex items-center gap-1.5 ${
                isAnnual ? 'bg-zinc-950 text-white shadow-2xs' : 'text-zinc-600 hover:text-zinc-950'
              }`}
            >
              <span>Annual</span>
              <span className="px-2 py-0.5 text-[10px] font-bold bg-emerald-500 text-white rounded-full">
                Save 25%
              </span>
            </button>
          </div>
        </div>

        {/* 3 Pricing Cards */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-7 items-stretch">
          
          {/* ── CARD 1: FREE FOREVER ── */}
          <div className="bg-white rounded-3xl p-7 sm:p-8 border border-zinc-200/90 shadow-2xs flex flex-col justify-between hover:shadow-md transition-all duration-300">
            <div>
              <div className="flex items-center justify-between mb-4">
                <h3 className="font-display text-xl font-bold text-zinc-950">Free Forever</h3>
                <span className="px-2.5 py-1 bg-zinc-100 text-zinc-700 rounded-full text-[11px] font-bold">
                  ₹0
                </span>
              </div>

              <p className="text-xs text-zinc-500 leading-relaxed mb-6">
                For solo service providers and solopreneurs getting started.
              </p>

              <div className="mb-6 pb-6 border-b border-zinc-100">
                <div className="flex items-baseline gap-1">
                  <span className="font-display text-4xl sm:text-5xl font-bold text-zinc-950">₹0</span>
                  <span className="text-xs text-zinc-500 font-medium">/ month</span>
                </div>
                <span className="text-[11px] text-zinc-400 block mt-1">No credit card required</span>
              </div>

              <ul className="space-y-3 text-xs sm:text-sm text-zinc-700 mb-8">
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span>1 User workspace</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span>Core AI chat assistant</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span>Standard client &amp; lead manager</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span>Up to 15 GST invoices / month</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span>WhatsApp shareable payment links</span>
                </li>
              </ul>
            </div>

            <button
              type="button"
              onClick={() => handlePlanSelect('free', 0)}
              className="w-full py-3 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 rounded-xl text-xs sm:text-sm font-semibold transition-colors shadow-2xs"
            >
              Start free — no card needed
            </button>
          </div>

          {/* ── CARD 2: STANDARD (MOST POPULAR) ── */}
          <div className="bg-zinc-950 text-white rounded-3xl p-7 sm:p-8 border-2 border-zinc-950 shadow-lg flex flex-col justify-between relative transform lg:-translate-y-2">
            <div className="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-emerald-500 text-zinc-950 text-[11px] font-bold px-3.5 py-1 rounded-full uppercase tracking-wider shadow-2xs">
              Most Popular
            </div>

            <div>
              <div className="flex items-center justify-between mb-4 pt-1">
                <h3 className="font-display text-xl font-bold text-white">Standard</h3>
                <span className="px-2.5 py-1 bg-zinc-800 text-emerald-400 rounded-full text-[11px] font-bold">
                  ₹{isAnnual ? '299' : '399'}/mo
                </span>
              </div>

              <p className="text-xs text-zinc-400 leading-relaxed mb-6">
                The complete AI co-founder for growing service businesses.
              </p>

              <div className="mb-6 pb-6 border-b border-zinc-800">
                <div className="flex items-baseline gap-1">
                  <span className="font-display text-4xl sm:text-5xl font-bold text-white">
                    ₹{isAnnual ? '299' : '399'}
                  </span>
                  <span className="text-xs text-zinc-400 font-medium">/ month</span>
                </div>
                <span className="text-[11px] text-zinc-400 block mt-1">
                  {isAnnual ? 'Billed annually (₹3,588/yr)' : 'Billed monthly'}
                </span>
              </div>

              <ul className="space-y-3 text-xs sm:text-sm text-zinc-300 mb-8">
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                  <span>Up to 3 Team members</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                  <span>Unlimited AI chat queries</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                  <span>Unlimited GST invoices &amp; receipts</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                  <span>Full business memory &amp; client history</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                  <span>Cash flow &amp; expense tracking</span>
                </li>
              </ul>
            </div>

            <button
              type="button"
              onClick={() => handlePlanSelect('standard', isAnnual ? 299 : 399)}
              className="w-full py-3 bg-white hover:bg-zinc-100 text-zinc-950 rounded-xl text-xs sm:text-sm font-semibold transition-colors shadow-2xs"
            >
              Get Started with Standard
            </button>
          </div>

          {/* ── CARD 3: BUSINESS ── */}
          <div className="bg-white rounded-3xl p-7 sm:p-8 border border-zinc-200/90 shadow-2xs flex flex-col justify-between hover:shadow-md transition-all duration-300">
            <div>
              <div className="flex items-center justify-between mb-4">
                <h3 className="font-display text-xl font-bold text-zinc-950">Business</h3>
                <span className="px-2.5 py-1 bg-zinc-100 text-zinc-700 rounded-full text-[11px] font-bold">
                  ₹{isAnnual ? '999' : '1,299'}/mo
                </span>
              </div>

              <p className="text-xs text-zinc-500 leading-relaxed mb-6">
                For established agencies, clinics, and multi-location businesses.
              </p>

              <div className="mb-6 pb-6 border-b border-zinc-100">
                <div className="flex items-baseline gap-1">
                  <span className="font-display text-4xl sm:text-5xl font-bold text-zinc-950">
                    ₹{isAnnual ? '999' : '1,299'}
                  </span>
                  <span className="text-xs text-zinc-500 font-medium">/ month</span>
                </div>
                <span className="text-[11px] text-zinc-400 block mt-1">
                  {isAnnual ? 'Billed annually (₹11,988/yr)' : 'Billed monthly'}
                </span>
              </div>

              <ul className="space-y-3 text-xs sm:text-sm text-zinc-700 mb-8">
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span>Up to 10 Team members</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span>Multi-branch &amp; location support</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span>Priority WhatsApp customer support</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span>Custom business reporting &amp; Excel export</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span>Dedicated onboarding assistance</span>
                </li>
              </ul>
            </div>

            <button
              type="button"
              onClick={() => handlePlanSelect('business', isAnnual ? 999 : 1299)}
              className="w-full py-3 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 rounded-xl text-xs sm:text-sm font-semibold transition-colors shadow-2xs"
            >
              Get Started with Business
            </button>
          </div>

        </div>

      </div>
    </section>
  );
}
