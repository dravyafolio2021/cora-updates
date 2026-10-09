'use client';

import React, { useState, useEffect } from 'react';
import { Check, ArrowRight, Sparkles, Rocket, TrendingUp, Leaf, Gift, Receipt, HardDrive, Globe, Info } from 'lucide-react';
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

              <div className="mb-3">
                <span className="inline-block px-2.5 py-0.5 rounded-md bg-zinc-100 text-zinc-800 text-[10px] font-mono font-bold uppercase tracking-wider">
                  Core Features Included
                </span>
              </div>
              <ul className="space-y-2.5 text-xs text-zinc-700 mb-8">
                <li className="flex items-center justify-between gap-2 min-h-[20px] group/item">
                  <div className="flex items-center gap-2.5 min-w-0">
                    <Sparkles className="w-3.5 h-3.5 text-amber-500 shrink-0" />
                    <span className="truncate"><strong>1,000</strong> AI runs / mo</span>
                  </div>
                  <span className="text-zinc-400 group-hover/item:text-zinc-600 transition-colors shrink-0 cursor-help" title="Standard AI engine for proposals, briefs and copy generation">
                    <Info className="w-3 h-3" />
                  </span>
                </li>
                <li className="flex items-center justify-between gap-2 min-h-[20px] group/item">
                  <div className="flex items-center gap-2.5 min-w-0">
                    <Globe className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                    <span className="truncate">Website on heycora.in subdomain</span>
                  </div>
                  <span className="text-zinc-400 group-hover/item:text-zinc-600 transition-colors shrink-0 cursor-help" title="Live SSL-secured studio website on heycora.in">
                    <Info className="w-3 h-3" />
                  </span>
                </li>
                <li className="flex items-center justify-between gap-2 min-h-[20px] group/item">
                  <div className="flex items-center gap-2.5 min-w-0">
                    <HardDrive className="w-3.5 h-3.5 text-zinc-700 shrink-0" />
                    <span className="truncate"><strong>5 GB</strong> bandwidth &bull; <strong>1 GB</strong> storage</span>
                  </div>
                  <span className="text-zinc-400 group-hover/item:text-zinc-600 transition-colors shrink-0 cursor-help" title="High-speed CDN bandwidth and cloud media storage">
                    <Info className="w-3 h-3" />
                  </span>
                </li>
                <li className="flex items-center justify-between gap-2 min-h-[20px] group/item">
                  <div className="flex items-center gap-2.5 min-w-0">
                    <Check className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                    <span className="truncate">Standard Kanban CRM &amp; leads</span>
                  </div>
                  <span className="text-zinc-400 group-hover/item:text-zinc-600 transition-colors shrink-0 cursor-help" title="Visual pipeline and lead funnel management">
                    <Info className="w-3 h-3" />
                  </span>
                </li>
                <li className="flex items-center justify-between gap-2 min-h-[20px] group/item">
                  <div className="flex items-center gap-2.5 min-w-0">
                    <Check className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                    <span className="truncate">18% GST invoicing &amp; receipts</span>
                  </div>
                  <span className="text-zinc-400 group-hover/item:text-zinc-600 transition-colors shrink-0 cursor-help" title="Automated GST tax calculation and instant PDF receipts">
                    <Info className="w-3 h-3" />
                  </span>
                </li>
                <li className="flex items-center justify-between gap-2 min-h-[20px] group/item">
                  <div className="flex items-center gap-2.5 min-w-0">
                    <Check className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                    <span className="truncate">1 User workspace</span>
                  </div>
                  <span className="text-zinc-400 group-hover/item:text-zinc-600 transition-colors shrink-0 cursor-help" title="Single seat license for solopreneurs & freelance operators">
                    <Info className="w-3 h-3" />
                  </span>
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

              <div className="mb-3">
                <span className="inline-block px-2.5 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200/60 text-[10px] font-mono font-bold uppercase tracking-wider">
                  Everything in Free, plus:
                </span>
              </div>
              <ul className="space-y-2.5 text-xs text-zinc-700 mb-8">
                <li className="flex items-center justify-between gap-2 min-h-[20px] group/item">
                  <div className="flex items-center gap-2.5 min-w-0">
                    <Sparkles className="w-3.5 h-3.5 text-amber-500 shrink-0" />
                    <span className="truncate"><strong>{isAnnual ? '6,000' : '5,000'}</strong> AI runs / mo</span>
                  </div>
                  <span className="text-zinc-400 group-hover/item:text-zinc-600 transition-colors shrink-0 cursor-help" title="AI quota for proposals, pitch decks and content creation">
                    <Info className="w-3 h-3" />
                  </span>
                </li>
                <li className="flex items-center justify-between gap-2 min-h-[20px] group/item">
                  <div className="flex items-center gap-2.5 min-w-0">
                    <HardDrive className="w-3.5 h-3.5 text-zinc-700 shrink-0" />
                    <span className="truncate"><strong>25 GB</strong> bandwidth &bull; <strong>5 GB</strong> storage</span>
                  </div>
                  <span className="text-zinc-400 group-hover/item:text-zinc-600 transition-colors shrink-0 cursor-help" title="Generous bandwidth & storage for client media delivery">
                    <Info className="w-3 h-3" />
                  </span>
                </li>
                <li className="flex items-center justify-between gap-2 min-h-[20px] group/item">
                  <div className="flex items-center gap-2.5 min-w-0">
                    <Check className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                    <span className="truncate">Connect custom domain (.com/.in)</span>
                  </div>
                  <span className="text-zinc-400 group-hover/item:text-zinc-600 transition-colors shrink-0 cursor-help" title="Full custom branded domain with automated SSL certs">
                    <Info className="w-3 h-3" />
                  </span>
                </li>
                <li className="flex items-center justify-between gap-2 min-h-[20px] group/item">
                  <div className="flex items-center gap-2.5 min-w-0">
                    <Check className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                    <span className="truncate">Custom business email sending</span>
                  </div>
                  <span className="text-zinc-400 group-hover/item:text-zinc-600 transition-colors shrink-0 cursor-help" title="Send system emails directly from your domain address">
                    <Info className="w-3 h-3" />
                  </span>
                </li>
                <li className="flex items-center justify-between gap-2 min-h-[20px] group/item">
                  <div className="flex items-center gap-2.5 min-w-0">
                    <Check className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                    <span className="truncate">Up to 2 team seats</span>
                  </div>
                  <span className="text-zinc-400 group-hover/item:text-zinc-600 transition-colors shrink-0 cursor-help" title="Add a co-pilot or team partner with dedicated access">
                    <Info className="w-3 h-3" />
                  </span>
                </li>
                <li className="flex items-center justify-between gap-2 min-h-[20px] group/item">
                  <div className="flex items-center gap-2.5 min-w-0">
                    <Check className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                    <span className="truncate">100% white-label client view</span>
                  </div>
                  <span className="text-zinc-400 group-hover/item:text-zinc-600 transition-colors shrink-0 cursor-help" title="No Cora badge or branding in your client-facing portal">
                    <Info className="w-3 h-3" />
                  </span>
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

              <div className="mb-3">
                <span className="inline-block px-2.5 py-0.5 rounded-md bg-pink-950/80 text-pink-300 border border-pink-700/60 text-[10px] font-mono font-bold uppercase tracking-wider">
                  Everything in Starter, plus:
                </span>
              </div>
              <ul className="space-y-2.5 text-xs text-zinc-300 mb-8">
                <li className="flex items-center justify-between gap-2 min-h-[20px] group/item font-medium text-white">
                  <div className="flex items-center gap-2.5 min-w-0">
                    <Sparkles className="w-3.5 h-3.5 text-blue-400 shrink-0" />
                    <span className="truncate"><strong>Advanced AI Reasoning Engine</strong></span>
                  </div>
                  <span className="text-zinc-500 group-hover/item:text-zinc-300 transition-colors shrink-0 cursor-help" title="Autonomous reasoning for complex proposals and research">
                    <Info className="w-3 h-3" />
                  </span>
                </li>
                <li className="flex items-center justify-between gap-2 min-h-[20px] group/item">
                  <div className="flex items-center gap-2.5 min-w-0">
                    <Sparkles className="w-3.5 h-3.5 text-emerald-400 shrink-0" />
                    <span className="truncate"><strong>15,000</strong> AI runs / mo</span>
                  </div>
                  <span className="text-zinc-500 group-hover/item:text-zinc-300 transition-colors shrink-0 cursor-help" title="High volume automation quota for growing studios">
                    <Info className="w-3 h-3" />
                  </span>
                </li>
                <li className="flex items-center justify-between gap-2 min-h-[20px] group/item">
                  <div className="flex items-center gap-2.5 min-w-0">
                    <HardDrive className="w-3.5 h-3.5 text-pink-400 shrink-0" />
                    <span className="truncate"><strong>50 GB</strong> bandwidth &bull; <strong>15 GB</strong> storage</span>
                  </div>
                  <span className="text-zinc-500 group-hover/item:text-zinc-300 transition-colors shrink-0 cursor-help" title="High-capacity media storage & fast streaming bandwidth">
                    <Info className="w-3 h-3" />
                  </span>
                </li>
                <li className="flex items-center justify-between gap-2 min-h-[20px] group/item">
                  <div className="flex items-center gap-2.5 min-w-0">
                    <Check className="w-3.5 h-3.5 text-emerald-400 shrink-0" />
                    <span className="truncate">Official WhatsApp automated dispatch</span>
                  </div>
                  <span className="text-zinc-500 group-hover/item:text-zinc-300 transition-colors shrink-0 cursor-help" title="Automated quotes, payment reminders & project updates via WhatsApp">
                    <Info className="w-3 h-3" />
                  </span>
                </li>
                <li className="flex items-center justify-between gap-2 min-h-[20px] group/item">
                  <div className="flex items-center gap-2.5 min-w-0">
                    <Check className="w-3.5 h-3.5 text-emerald-400 shrink-0" />
                    <span className="truncate">Dynamic UPI QR code on invoices</span>
                  </div>
                  <span className="text-zinc-500 group-hover/item:text-zinc-300 transition-colors shrink-0 cursor-help" title="Scan & pay via PhonePe, GPay, Paytm directly on PDFs">
                    <Info className="w-3 h-3" />
                  </span>
                </li>
                <li className="flex items-center justify-between gap-2 min-h-[20px] group/item">
                  <div className="flex items-center gap-2.5 min-w-0">
                    <Check className="w-3.5 h-3.5 text-emerald-400 shrink-0" />
                    <span className="truncate">Up to 5 team seats &amp; roles</span>
                  </div>
                  <span className="text-zinc-500 group-hover/item:text-zinc-300 transition-colors shrink-0 cursor-help" title="Role-based permissions for managers, creatives and accountants">
                    <Info className="w-3 h-3" />
                  </span>
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

              <div className="mb-3">
                <span className="inline-block px-2.5 py-0.5 rounded-md bg-purple-50 text-purple-900 border border-purple-200/60 text-[10px] font-mono font-bold uppercase tracking-wider">
                  Everything in Professional, plus:
                </span>
              </div>
              <ul className="space-y-2.5 text-xs text-zinc-700 mb-8">
                <li className="flex items-center justify-between gap-2 min-h-[20px] group/item font-semibold text-purple-950">
                  <div className="flex items-center gap-2.5 min-w-0">
                    <Gift className="w-3.5 h-3.5 text-purple-700 shrink-0" />
                    <span className="truncate"><strong>Free Website Dev &amp; Migration</strong></span>
                  </div>
                  <span className="text-purple-400 group-hover/item:text-purple-700 transition-colors shrink-0 cursor-help" title="Custom website design and full site migration engineered by Cora experts">
                    <Info className="w-3 h-3" />
                  </span>
                </li>
                <li className="flex items-center justify-between gap-2 min-h-[20px] group/item">
                  <div className="flex items-center gap-2.5 min-w-0">
                    <Sparkles className="w-3.5 h-3.5 text-purple-600 shrink-0" />
                    <span className="truncate"><strong>Frontier AI Models (GPT-4o, Claude 3.5)</strong></span>
                  </div>
                  <span className="text-zinc-400 group-hover/item:text-zinc-600 transition-colors shrink-0 cursor-help" title="Direct access to top-tier reasoning and coding frontier models">
                    <Info className="w-3 h-3" />
                  </span>
                </li>
                <li className="flex items-center justify-between gap-2 min-h-[20px] group/item">
                  <div className="flex items-center gap-2.5 min-w-0">
                    <Sparkles className="w-3.5 h-3.5 text-amber-500 shrink-0" />
                    <span className="truncate"><strong>25,000</strong> AI runs / mo</span>
                  </div>
                  <span className="text-zinc-400 group-hover/item:text-zinc-600 transition-colors shrink-0 cursor-help" title="Enterprise automation quota for large agency operations">
                    <Info className="w-3 h-3" />
                  </span>
                </li>
                <li className="flex items-center justify-between gap-2 min-h-[20px] group/item">
                  <div className="flex items-center gap-2.5 min-w-0">
                    <HardDrive className="w-3.5 h-3.5 text-zinc-700 shrink-0" />
                    <span className="truncate"><strong>100 GB</strong> bandwidth &bull; <strong>30 GB</strong> storage</span>
                  </div>
                  <span className="text-zinc-400 group-hover/item:text-zinc-600 transition-colors shrink-0 cursor-help" title="Enterprise-grade CDN bandwidth and high-volume media storage">
                    <Info className="w-3 h-3" />
                  </span>
                </li>
                <li className="flex items-center justify-between gap-2 min-h-[20px] group/item">
                  <div className="flex items-center gap-2.5 min-w-0">
                    <Check className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                    <span className="truncate">Unlimited* team seats</span>
                  </div>
                  <span className="text-zinc-400 group-hover/item:text-zinc-600 transition-colors shrink-0 cursor-help" title="Scale your entire team without per-seat upgrade fees">
                    <Info className="w-3 h-3" />
                  </span>
                </li>
                <li className="flex items-center justify-between gap-2 min-h-[20px] group/item">
                  <div className="flex items-center gap-2.5 min-w-0">
                    <Check className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                    <span className="truncate">Custom Webhooks &amp; REST APIs</span>
                  </div>
                  <span className="text-zinc-400 group-hover/item:text-zinc-600 transition-colors shrink-0 cursor-help" title="Integrate with custom internal tools, Zapier and external databases">
                    <Info className="w-3 h-3" />
                  </span>
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
