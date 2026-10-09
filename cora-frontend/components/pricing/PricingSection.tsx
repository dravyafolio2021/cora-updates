'use client';

import React, { useState, useEffect } from 'react';
import { Check, ArrowRight, Sparkles, Rocket, TrendingUp, Leaf, Gift, Receipt, HardDrive, Globe } from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';
import { triggerRazorpayCheckout, useRazorpayPreload, isPartner100Coupon, PlanKey } from './FastRazorpayModal';

export function PricingSection() {
  const [billing, setBilling] = useState<'monthly' | 'annual'>('annual');
  const [appliedCoupon, setAppliedCoupon] = useState<string | null>(null);
  const isAnnual = billing === 'annual';

  // Preload Razorpay checkout script
  useRazorpayPreload();

  useEffect(() => {
    if (typeof window !== 'undefined') {
      const params = new URLSearchParams(window.location.search);
      const coupon = params.get('coupon') || params.get('code') || params.get('discount');
      if (isPartner100Coupon(coupon)) {
        setAppliedCoupon(coupon!.trim());
      }
    }
  }, []);

  const handlePlanSelect = (plan: PlanKey) => {
    trackEvent('plan_selected', { plan, billing, coupon: appliedCoupon });
    triggerRazorpayCheckout({
      planKey: plan,
      billingCycle: billing,
      appliedCoupon
    });
  };

  return (
    <section id="pricing" className="py-16 sm:py-[100px] relative z-10 bg-white">
      <div className="w-full max-w-[1360px] mx-auto px-4 sm:px-6">
        
        {/* Section Header */}
        <div className="text-center max-w-[760px] mx-auto mb-10 sm:mb-12 flex flex-col items-center">
          <div className="inline-flex items-center gap-1.5 px-3.5 py-1 bg-zinc-100 rounded-full text-zinc-800 text-xs font-semibold uppercase tracking-wider mb-4 border border-zinc-200/80 shadow-2xs">
            <Sparkles className="w-3.5 h-3.5 text-zinc-950" />
            <span>TRANSPARENT PRICING</span>
          </div>

          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl font-bold leading-[1.15] tracking-[-0.03em] text-zinc-950 mb-3 text-center">
            Simple, honest pricing.
          </h2>

          <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[600px] text-center">
            Start free. Upgrade anytime with 2 months free on annual plans.
          </p>
        </div>

        {/* Billing Switcher (Monthly / Annual) */}
        <div className="flex items-center justify-center mb-12 sm:mb-16 select-none">
          <div className="inline-flex items-center bg-zinc-100 p-1.5 rounded-2xl border border-zinc-200/80 shadow-2xs">
            <button
              type="button"
              onClick={() => setBilling('monthly')}
              className={`px-5 py-2 text-xs sm:text-sm font-semibold rounded-xl transition-all cursor-pointer outline-none focus:outline-none focus-visible:outline-none focus:ring-0 select-none ${
                !isAnnual ? 'bg-zinc-950 text-white shadow-2xs' : 'text-zinc-600 hover:text-zinc-950'
              }`}
            >
              Monthly
            </button>
            <button
              type="button"
              onClick={() => setBilling('annual')}
              className={`px-5 py-2 text-xs sm:text-sm font-semibold rounded-xl transition-all flex items-center gap-1.5 cursor-pointer outline-none focus:outline-none focus-visible:outline-none focus:ring-0 select-none ${
                isAnnual ? 'bg-zinc-950 text-white shadow-2xs' : 'text-zinc-600 hover:text-zinc-950'
              }`}
            >
              <span>Annual</span>
              <span className="px-2 py-0.5 text-[9.5px] font-mono font-bold bg-white text-zinc-900 border border-zinc-200/80 rounded-full shadow-2xs">
                Save 20%
              </span>
            </button>
          </div>
        </div>

        {/* 4 Pricing Cards Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 items-stretch">
          
          {/* ── CARD 1: FREE FOREVER (COLUMN 1) ── */}
          <div className="bg-white rounded-3xl p-6 sm:p-7 border border-zinc-200/90 shadow-2xs flex flex-col justify-between hover:shadow-md transition-all duration-300">
            <div>
              <div className="flex items-center justify-between mb-4">
                <div className="flex items-center gap-2">
                  <div className="w-8 h-8 rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-900 border border-zinc-200">
                    <Leaf className="w-4 h-4 text-emerald-600" />
                  </div>
                  <h3 className="font-display text-lg font-bold text-zinc-950">Free Forever</h3>
                </div>
                <span className="px-2.5 py-0.5 bg-zinc-100 text-zinc-700 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider border border-zinc-200/60">
                  FREE
                </span>
              </div>

              <p className="text-xs text-zinc-500 leading-relaxed mb-6">
                Run your CRM, create invoices, and test AI agents.
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
                  <Sparkles className="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
                  <span>1,000 AI runs / mo</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Globe className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span>Website on heycora.in subdomain</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <HardDrive className="w-4 h-4 text-zinc-700 shrink-0 mt-0.5" />
                  <span><strong>5 GB</strong> bandwidth &bull; <strong>1 GB</strong> storage</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span>Standard Kanban CRM &amp; leads</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span>18% GST invoicing &amp; receipts</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span>1 User workspace</span>
                </li>
              </ul>
            </div>

            <button
              type="button"
              onClick={() => handlePlanSelect('free')}
              className="w-full py-3 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 rounded-xl text-xs sm:text-sm font-semibold transition-colors shadow-2xs cursor-pointer text-center block"
            >
              Start Free
            </button>
          </div>

          {/* ── CARD 2: STARTER (COLUMN 2) ── */}
          <div className="bg-white rounded-3xl p-6 sm:p-7 border border-zinc-200/90 shadow-2xs flex flex-col justify-between hover:shadow-md transition-all duration-300">
            <div>
              <div className="flex items-center justify-between mb-4">
                <div className="flex items-center gap-2">
                  <div className="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center border border-emerald-200">
                    <Rocket className="w-4 h-4" />
                  </div>
                  <h3 className="font-display text-lg font-bold text-zinc-950">Starter</h3>
                </div>
                <span className="px-2.5 py-0.5 bg-emerald-50 text-emerald-800 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider border border-emerald-200">
                  POPULAR
                </span>
              </div>

              <p className="text-xs text-zinc-500 leading-relaxed mb-6">
                Connect custom domains, send branded emails, and invite 2 teammates.
              </p>

              <div className="mb-6 pb-6 border-b border-zinc-100">
                <div className="flex items-baseline gap-1">
                  <span className="font-display text-4xl sm:text-5xl font-bold text-zinc-950">
                    ₹{isAnnual ? '833' : '999'}
                  </span>
                  <span className="text-xs text-zinc-500 font-medium">/ month</span>
                </div>
                <span className="text-[11px] text-zinc-400 block mt-1">
                  {isAnnual ? 'Billed annually (₹9,990/yr • Save ₹1,998)' : 'Billed monthly • + 18% GST'}
                </span>
              </div>

              <ul className="space-y-3 text-xs sm:text-sm text-zinc-700 mb-8">
                <li className="flex items-start gap-2.5">
                  <Sparkles className="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
                  <span><strong>{isAnnual ? '6,000' : '5,000'}</strong> AI runs / mo</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <HardDrive className="w-4 h-4 text-zinc-700 shrink-0 mt-0.5" />
                  <span><strong>25 GB</strong> bandwidth &bull; <strong>5 GB</strong> storage</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span>Connect custom domain (.com/.in)</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span>Custom business email sending</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span>Up to 2 team seats</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span>100% white-label client view</span>
                </li>
              </ul>
            </div>

            <button
              type="button"
              onClick={() => handlePlanSelect('starter')}
              className="w-full py-3 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 rounded-xl text-xs sm:text-sm font-semibold transition-colors shadow-2xs cursor-pointer text-center block"
            >
              Get Started
            </button>
          </div>

          {/* ── CARD 3: PROFESSIONAL (COLUMN 3 - RECOMMENDED) ── */}
          <div className="bg-zinc-950 text-white rounded-3xl p-6 sm:p-7 shadow-xl flex flex-col justify-between relative transition-all border-2 border-[#be006b] overflow-visible">
            {/* Floating Highlighted SaaS Badge - Positioned OUTSIDE the frame at the top */}
            <div className="absolute -top-3.5 left-1/2 -translate-x-1/2 z-30 whitespace-nowrap select-none pointer-events-none">
              <div className={`inline-flex items-center gap-1.5 px-4 py-1 rounded-full text-[11px] font-mono font-bold tracking-wider uppercase shadow-md border ${
                isPartner100Coupon(appliedCoupon)
                  ? 'bg-emerald-500 text-zinc-950 border-emerald-300 ring-2 ring-emerald-400/30 animate-pulse'
                  : 'bg-gradient-to-r from-[#9d0057] via-[#be006b] to-[#9d0057] text-white border-pink-300/50 ring-2 ring-pink-500/20 shadow-[0_4px_14px_rgba(190,0,107,0.35)]'
              }`}>
                <Sparkles className="w-3.5 h-3.5 text-pink-200 fill-pink-200/60 shrink-0" />
                <span>{isPartner100Coupon(appliedCoupon) ? '100% OFF PASS' : 'RECOMMENDED'}</span>
              </div>
            </div>

            <div className="flex-1 flex flex-col justify-between">
              <div>
                <div className="flex items-center justify-between mb-4">
                  <div className="flex items-center gap-2">
                    <div className="w-8 h-8 rounded-xl bg-zinc-800 text-pink-400 flex items-center justify-center border border-zinc-700">
                      <Sparkles className="w-4 h-4" />
                    </div>
                    <h3 className="font-display text-lg font-bold text-white">Professional</h3>
                  </div>
                </div>

                <p className="text-xs text-zinc-400 leading-relaxed mb-6">
                  Automate WhatsApp updates, collect payments with UPI QR, and deploy reasoning AI.
                </p>

              <div className="mb-6 pb-6 border-b border-zinc-800">
                <div className="flex items-baseline gap-1">
                  <span className="font-display text-4xl sm:text-5xl font-bold text-white">
                    {isPartner100Coupon(appliedCoupon) ? '₹0' : (isAnnual ? '₹2,083' : '₹2,499')}
                  </span>
                  <span className="text-xs text-zinc-400 font-medium">/ month</span>
                  {isPartner100Coupon(appliedCoupon) && (
                    <span className="text-xs text-zinc-500 line-through ml-2 font-mono">
                      ₹{isAnnual ? '2,083' : '2,499'}
                    </span>
                  )}
                </div>
                <span className="text-[11px] text-zinc-400 block mt-1">
                  {isPartner100Coupon(appliedCoupon)
                    ? '100% Partner Discount Applied • ₹0 Forever'
                    : (isAnnual ? 'Billed annually (₹24,990/yr • Save ₹4,998)' : 'Billed monthly • + 18% GST')}
                </span>
              </div>

              <ul className="space-y-3 text-xs sm:text-sm text-zinc-300 mb-8">
                <li className="flex items-start gap-2.5">
                  <Sparkles className="w-4 h-4 text-blue-400 shrink-0 mt-0.5" />
                  <span><strong>Advanced AI Reasoning Engine</strong></span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Sparkles className="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                  <span><strong>15,000</strong> AI runs / mo</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <HardDrive className="w-4 h-4 text-pink-400 shrink-0 mt-0.5" />
                  <span><strong>50 GB</strong> bandwidth &bull; <strong>15 GB</strong> storage</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                  <span>Official WhatsApp automated dispatch</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                  <span>Dynamic UPI QR code on invoices</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                  <span>Up to 5 team seats &amp; roles</span>
                </li>
              </ul>
            </div>

            <button
              type="button"
              onClick={() => handlePlanSelect('pro')}
              className="w-full py-3 rounded-xl text-xs sm:text-sm font-semibold transition-colors shadow-2xs cursor-pointer text-center block bg-white hover:bg-zinc-100 text-zinc-950 font-bold"
            >
              {isPartner100Coupon(appliedCoupon)
                ? 'Claim Free Plan'
                : 'Get Started'}
            </button>
            </div>
          </div>

          {/* ── CARD 4: SCALE (COLUMN 4) ── */}
          <div className="bg-white rounded-3xl p-6 sm:p-7 border border-zinc-200/90 shadow-2xs flex flex-col justify-between hover:shadow-md transition-all duration-300 relative overflow-visible">
            {/* Floating Highlighted SaaS Badge - Positioned OUTSIDE the frame at the top */}
            <div className="absolute -top-3.5 left-1/2 -translate-x-1/2 z-30 whitespace-nowrap select-none pointer-events-none">
              <div className="inline-flex items-center gap-1.5 px-4 py-1 rounded-full text-[11px] font-mono font-bold tracking-wider uppercase shadow-md border bg-gradient-to-r from-[#432bb3] via-[#5438dc] to-[#432bb3] text-white border-purple-300/50 ring-2 ring-purple-500/20 shadow-[0_4px_14px_rgba(84,56,220,0.35)]">
                <Gift className="w-3.5 h-3.5 text-purple-200 shrink-0" />
                <span>FREE SITE MIGRATION</span>
              </div>
            </div>

            <div>
              <div className="flex items-center justify-between mb-4">
                <div className="flex items-center gap-2">
                  <div className="w-8 h-8 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center border border-purple-200">
                    <TrendingUp className="w-4 h-4" />
                  </div>
                  <h3 className="font-display text-lg font-bold text-zinc-950">Scale</h3>
                </div>
                <span className="px-2.5 py-0.5 bg-purple-50 text-purple-800 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider border border-purple-200">
                  SCALE
                </span>
              </div>

              <p className="text-xs text-zinc-500 leading-relaxed mb-6">
                Scale your agency with unlimited seats, custom APIs, and free site migration.
              </p>

              <div className="mb-6 pb-6 border-b border-zinc-100">
                <div className="flex items-baseline gap-1">
                  <span className="font-display text-4xl sm:text-5xl font-bold text-zinc-950">
                    ₹{isAnnual ? '3,749' : '4,499'}
                  </span>
                  <span className="text-xs text-zinc-500 font-medium">/ month</span>
                </div>
                <span className="text-[11px] text-zinc-400 block mt-1">
                  {isAnnual ? 'Billed annually (₹44,990/yr • Save ₹8,998)' : 'Billed monthly • + 18% GST'}
                </span>
              </div>

              <ul className="space-y-3 text-xs sm:text-sm text-zinc-700 mb-8">
                <li className="flex items-start gap-2.5">
                  <Gift className="w-4 h-4 text-purple-700 shrink-0 mt-0.5" />
                  <span><strong>Free Website Dev &amp; Migration</strong></span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Sparkles className="w-4 h-4 text-purple-600 shrink-0 mt-0.5" />
                  <span>Frontier AI Models (GPT-4o, Claude 3.5)</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Sparkles className="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
                  <span><strong>25,000</strong> AI runs / mo</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <HardDrive className="w-4 h-4 text-zinc-700 shrink-0 mt-0.5" />
                  <span><strong>100 GB</strong> bandwidth &bull; <strong>30 GB</strong> storage</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span>Unlimited* team seats</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span>Custom Webhooks &amp; REST APIs</span>
                </li>
              </ul>
            </div>

            <button
              type="button"
              onClick={() => handlePlanSelect('scale')}
              className="w-full py-3 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 rounded-xl text-xs sm:text-sm font-semibold transition-colors shadow-2xs cursor-pointer text-center block"
            >
              Get Started
            </button>
          </div>

        </div>

        {/* Tax Note */}
        <div className="mt-8 text-center text-xs text-zinc-500 flex items-center justify-center gap-1.5 font-mono">
          <Receipt className="w-3.5 h-3.5 text-zinc-600" />
          <span>All prices exclude 18% GST. Instant UPI &amp; card checkout with compliant tax invoices.</span>
        </div>

      </div>
    </section>
  );
}
