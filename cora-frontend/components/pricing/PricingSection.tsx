'use client';

import React, { useState, useEffect } from 'react';
import { Check, ArrowRight, Sparkles, Rocket, TrendingUp, Leaf, Gift, Receipt } from 'lucide-react';
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
        <div className="text-center max-w-[760px] mx-auto mb-10 sm:mb-12">
          <div className="inline-flex items-center gap-1.5 px-3.5 py-1 bg-zinc-100 rounded-full text-zinc-800 text-xs font-semibold uppercase tracking-wider mb-4 border border-zinc-200/80">
            <Sparkles className="w-3.5 h-3.5 text-zinc-950" />
            <span>HONEST 4-TIER PRICING</span>
          </div>

          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl font-bold leading-[1.18] tracking-[-0.03em] bg-gradient-to-r from-zinc-950 via-zinc-700 to-zinc-400 bg-clip-text text-transparent inline-block pb-2 mb-2">
            Transparent pricing. Exclusive of taxes.
          </h2>

          <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[600px] mx-auto">
            Get started for free today. Upgrade when your business grows with 2 months free on annual plans.
          </p>
        </div>

        {/* Billing Switcher (Monthly / Annual) */}
        <div className="flex items-center justify-center mb-12 sm:mb-16">
          <div className="inline-flex items-center bg-zinc-100 p-1.5 rounded-2xl border border-zinc-200/80 shadow-2xs">
            <button
              type="button"
              onClick={() => setBilling('monthly')}
              className={`px-5 py-2 text-xs sm:text-sm font-semibold rounded-xl transition-all cursor-pointer ${
                !isAnnual ? 'bg-zinc-950 text-white shadow-2xs' : 'text-zinc-600 hover:text-zinc-950'
              }`}
            >
              Monthly
            </button>
            <button
              type="button"
              onClick={() => setBilling('annual')}
              className={`px-5 py-2 text-xs sm:text-sm font-semibold rounded-xl transition-all flex items-center gap-1.5 cursor-pointer ${
                isAnnual ? 'bg-zinc-950 text-white shadow-2xs' : 'text-zinc-600 hover:text-zinc-950'
              }`}
            >
              <span>Annual</span>
              <span className="px-2 py-0.5 text-[10px] font-bold bg-emerald-500 text-white rounded-full">
                2 Mo. Free
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
                <span className="px-2.5 py-1 bg-zinc-100 text-zinc-700 rounded-full text-[11px] font-bold">
                  ₹0
                </span>
              </div>

              <p className="text-xs text-zinc-500 leading-relaxed mb-6">
                For solo operators, freelancers &amp; solopreneurs starting out.
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
                  <span>1,000 AI Runs / mo</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span>1 User workspace</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span>Website on heycora.in subdomain</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span>Standard Kanban CRM &amp; leads</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Check className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                  <span>18% GST invoicing &amp; receipts</span>
                </li>
              </ul>
            </div>

            <button
              type="button"
              onClick={() => handlePlanSelect('free')}
              className="w-full py-3 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 rounded-xl text-xs sm:text-sm font-semibold transition-colors shadow-2xs cursor-pointer text-center block"
            >
              Start free — no card needed
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
                <span className="px-2.5 py-1 bg-emerald-50 text-emerald-800 rounded-full text-[11px] font-bold">
                  ₹{isAnnual ? '833' : '999'}/mo
                </span>
              </div>

              <p className="text-xs text-zinc-500 leading-relaxed mb-6">
                For independent operators establishing their brand with custom domains.
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
                  <span><strong>{isAnnual ? '6,000' : '5,000'}</strong> AI Runs / mo</span>
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
              Get started with Starter
            </button>
          </div>

          {/* ── CARD 3: PROFESSIONAL (COLUMN 3 - RECOMMENDED) ── */}
          <div className={`text-white rounded-3xl p-6 sm:p-7 shadow-lg flex flex-col justify-between relative transform xl:-translate-y-2 transition-all ${
            isPartner100Coupon(appliedCoupon)
              ? 'bg-zinc-950 border-2 border-emerald-500 shadow-2xl ring-4 ring-emerald-400/20'
              : 'bg-zinc-950 border-2 border-zinc-950'
          }`}>
            <div className={`absolute -top-3.5 left-1/2 -translate-x-1/2 text-[11px] font-bold px-3.5 py-1 rounded-full uppercase tracking-wider shadow-2xs ${
              isPartner100Coupon(appliedCoupon)
                ? 'bg-emerald-400 text-zinc-950 font-extrabold animate-pulse'
                : 'bg-emerald-500 text-zinc-950'
            }`}>
              {isPartner100Coupon(appliedCoupon) ? '🎉 100% OFF PARTNER PASS' : 'Recommended'}
            </div>

            <div>
              <div className="flex items-center justify-between mb-4 pt-1">
                <div className="flex items-center gap-2">
                  <div className="w-8 h-8 rounded-xl bg-zinc-800 text-pink-400 flex items-center justify-center border border-zinc-700">
                    <Sparkles className="w-4 h-4" />
                  </div>
                  <h3 className="font-display text-lg font-bold text-white">Professional</h3>
                </div>
                <span className="px-2.5 py-1 bg-zinc-800 text-emerald-400 rounded-full text-[11px] font-bold">
                  {isPartner100Coupon(appliedCoupon) ? '₹0' : (isAnnual ? '₹2,083' : '₹2,499')}/mo
                </span>
              </div>

              <p className="text-xs text-zinc-400 leading-relaxed mb-6">
                Autonomous backbone with advanced AI, official WhatsApp dispatch &amp; UPI QR.
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
                  <span><strong>15,000</strong> AI Runs / mo</span>
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
              className={`w-full py-3 rounded-xl text-xs sm:text-sm font-semibold transition-colors shadow-2xs cursor-pointer text-center block ${
                isPartner100Coupon(appliedCoupon)
                  ? 'bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-bold'
                  : 'bg-white hover:bg-zinc-100 text-zinc-950'
              }`}
            >
              {isPartner100Coupon(appliedCoupon)
                ? 'Claim 100% Free Professional Plan →'
                : 'Get Started with Professional'}
            </button>
          </div>

          {/* ── CARD 4: SCALE (COLUMN 4) ── */}
          <div className="bg-white rounded-3xl p-6 sm:p-7 border border-zinc-200/90 shadow-2xs flex flex-col justify-between hover:shadow-md transition-all duration-300">
            <div>
              <div className="flex items-center justify-between mb-4">
                <div className="flex items-center gap-2">
                  <div className="w-8 h-8 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center border border-purple-200">
                    <TrendingUp className="w-4 h-4" />
                  </div>
                  <h3 className="font-display text-lg font-bold text-zinc-950">Scale</h3>
                </div>
                <span className="px-2.5 py-1 bg-purple-50 text-purple-800 rounded-full text-[11px] font-bold">
                  ₹{isAnnual ? '3,749' : '4,499'}/mo
                </span>
              </div>

              <p className="text-xs text-zinc-500 leading-relaxed mb-6">
                High-throughput infrastructure for agencies &amp; multi-member teams.
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
                <li className="flex items-start gap-2.5 p-1.5 rounded-lg bg-purple-50 border border-purple-200 text-purple-950 font-semibold">
                  <Gift className="w-4 h-4 text-purple-700 shrink-0 mt-0.5" />
                  <span>Free Website Dev or Migration</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Sparkles className="w-4 h-4 text-purple-600 shrink-0 mt-0.5" />
                  <span>All Frontier AI Engines (GPT-4o, Claude 3.5)</span>
                </li>
                <li className="flex items-start gap-2.5">
                  <Sparkles className="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
                  <span><strong>25,000</strong> AI Runs / mo</span>
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
              Get Started with Scale
            </button>
          </div>

        </div>

        {/* Tax Note */}
        <div className="mt-8 text-center text-xs text-zinc-500 flex items-center justify-center gap-1.5 font-mono">
          <Receipt className="w-3.5 h-3.5 text-zinc-600" />
          <span>* All prices are exclusive of 18% GST. Fast 1-step checkout via UPI &amp; Cards with instant GST tax invoices.</span>
        </div>

      </div>
    </section>
  );
}
