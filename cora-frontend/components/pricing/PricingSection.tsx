'use client';

import React, { useState, useEffect } from 'react';
import { 
  Check, 
  ArrowRight, 
  Sparkles, 
  Rocket, 
  TrendingUp, 
  Leaf, 
  Gift, 
  Receipt, 
  HardDrive, 
  Globe, 
  Layout,
  GitBranch,
  ArrowRightLeft,
  Lock,
  Languages,
  UploadCloud,
  Split,
  Code,
  Search,
  Bot,
  Headphones,
  Webhook,
  User,
  Users,
  Mail,
  Tag
} from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';
import { 
  triggerRazorpayCheckout, 
  useRazorpayPreload, 
  isPartner100Coupon, 
  calculateDiscountedPrice,
  PlanKey 
} from './FastRazorpayModal';
import { getActiveReferralCode, initReferralTracking } from '@/lib/referral';

// Official Vector WhatsApp SVG Icon
const WhatsAppIcon = ({ className = "w-4 h-4" }: { className?: string }) => (
  <svg viewBox="0 0 24 24" className={className} fill="currentColor">
    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.746.953 3.71 1.456 5.711 1.457h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
  </svg>
);

// Clean high-contrast feature row with perfect typography alignment
const PricingFeature = ({
  icon: Icon = Check,
  iconClass = "text-zinc-900 stroke-[2]",
  children,
  highlight = false,
  textColor,
}: {
  icon?: React.ElementType;
  iconClass?: string;
  children: React.ReactNode;
  tooltip?: string;
  highlight?: boolean;
  textColor?: string;
}) => {
  return (
    <li className={`flex items-start gap-2.5 min-h-[22px] text-xs ${
      textColor ? textColor : (highlight ? 'font-medium text-zinc-900' : 'text-zinc-600')
    }`}>
      <Icon className={`w-3.5 h-3.5 ${iconClass} shrink-0 mt-0.5`} />
      <span className="leading-snug">{children}</span>
    </li>
  );
};

export function PricingSection() {
  const [billing, setBilling] = useState<'monthly' | 'annual'>('annual');
  const [appliedCoupon, setAppliedCoupon] = useState<string | null>(null);
  const [activeReferral, setActiveReferral] = useState<string>('');
  const isAnnual = billing === 'annual';

  // Preload Razorpay checkout script & referral tracking
  useRazorpayPreload();

  useEffect(() => {
    if (typeof window !== 'undefined') {
      const tracking = initReferralTracking();
      if (tracking.code) {
        setActiveReferral(tracking.code);
      } else {
        const ref = getActiveReferralCode();
        if (ref) setActiveReferral(ref);
      }

      const params = new URLSearchParams(window.location.search);
      const coupon = params.get('coupon') || params.get('code') || params.get('discount');
      if (coupon) {
        setAppliedCoupon(coupon.trim().toUpperCase());
      }
    }
  }, []);

  const handlePlanSelect = (plan: PlanKey) => {
    trackEvent('plan_selected', { plan, billing, coupon: appliedCoupon, referral: activeReferral });
    triggerRazorpayCheckout({
      planKey: plan,
      billingCycle: billing,
      appliedCoupon,
      referralCode: activeReferral
    });
  };

  // Pricing math calculations
  const starterPricing = calculateDiscountedPrice('starter', billing, appliedCoupon, activeReferral);
  const proPricing = calculateDiscountedPrice('pro', billing, appliedCoupon, activeReferral);
  const scalePricing = calculateDiscountedPrice('scale', billing, appliedCoupon, activeReferral);

  return (
    <section id="pricing" className="py-16 sm:py-[100px] relative z-10 bg-white">
      <div className="w-full max-w-[1360px] mx-auto px-4 sm:px-6">
        
        {/* Section Header */}
        <div className="text-center max-w-[760px] mx-auto mb-10 sm:mb-12 flex flex-col items-center">
          <div className="flex flex-wrap items-center justify-center gap-2.5 mb-4">
            <div className="inline-flex items-center gap-1.5 px-3.5 py-1 bg-zinc-100 rounded-full text-zinc-800 text-xs font-semibold uppercase tracking-wider border border-zinc-200/80 shadow-2xs">
              <Sparkles className="w-3.5 h-3.5 text-zinc-950" />
              <span>TRANSPARENT PRICING</span>
            </div>
            <a
              href="/pricing#india-only-plan"
              className="inline-flex items-center gap-1.5 px-3.5 py-1 bg-gradient-to-r from-amber-50 via-white to-orange-50 hover:from-amber-100 hover:to-orange-100 text-amber-950 rounded-full text-xs font-medium border border-amber-300/80 shadow-2xs transition-all cursor-pointer group"
            >
              <span className="text-xs">🇮🇳</span>
              <span className="font-semibold">Claim Indian Startup Benefits &amp; Credits</span>
              <ArrowRight className="w-3 h-3 text-amber-700 group-hover:translate-x-0.5 transition-transform" />
            </a>
          </div>

          {/* Active Referral Announcement Pill */}
          {activeReferral && (
            <div className="inline-flex items-center gap-2 px-4 py-1.5 bg-zinc-950 text-white rounded-full text-xs font-mono font-medium shadow-md mb-4 border border-zinc-800 animate-in fade-in duration-200">
              <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" />
              <span>⚡ 10% Referral Discount Applied via Partner @{activeReferral} — All plans discounted at checkout!</span>
            </div>
          )}

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
              <ul className="space-y-2.5 text-xs mb-8">
                <PricingFeature 
                  icon={Sparkles} 
                  iconClass="text-amber-500" 
                >
                  <span className="font-semibold text-zinc-900">500</span> CORA AI runs / mo
                </PricingFeature>
                <PricingFeature 
                  icon={Globe} 
                  iconClass="text-zinc-900" 
                >
                  Free <span className="font-semibold text-zinc-900">heycora.in</span> subdomain
                </PricingFeature>
                <PricingFeature 
                  icon={Layout} 
                  iconClass="text-zinc-900" 
                >
                  <span className="font-semibold text-zinc-900">1 CMS collection</span> (Portfolio &amp; Services)
                </PricingFeature>
                <PricingFeature 
                  icon={HardDrive} 
                  iconClass="text-zinc-900" 
                >
                  <span className="font-semibold text-zinc-900">5 GB</span> bandwidth &bull; <span className="font-semibold text-zinc-900">1 GB</span> storage
                </PricingFeature>
                <PricingFeature 
                  icon={Check} 
                  iconClass="text-zinc-900" 
                >
                  Unlimited form submissions
                </PricingFeature>
                <PricingFeature 
                  icon={GitBranch} 
                  iconClass="text-zinc-900" 
                >
                  Branching with previews
                </PricingFeature>
                <PricingFeature 
                  icon={ArrowRightLeft} 
                  iconClass="text-zinc-900" 
                >
                  Site redirects (301/302)
                </PricingFeature>
                <PricingFeature 
                  icon={Lock} 
                  iconClass="text-zinc-900" 
                >
                  Password protection
                </PricingFeature>
                <PricingFeature 
                  icon={Languages} 
                  iconClass="text-zinc-900" 
                >
                  Localization &amp; geo routing
                </PricingFeature>
                <PricingFeature 
                  icon={Sparkles} 
                  iconClass="text-amber-500" 
                >
                  Built-in MCP server &amp; AI tools
                </PricingFeature>
                <PricingFeature 
                  icon={User} 
                  iconClass="text-zinc-900" 
                >
                  1 Free workspace seat
                </PricingFeature>
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
                <div className="flex items-center gap-2 flex-wrap">
                  <div className="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center border border-emerald-200">
                    <Rocket className="w-4 h-4" />
                  </div>
                  <h3 className="font-display text-lg font-bold text-zinc-950">Starter</h3>
                  {isAnnual && (
                    <span className="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-mono font-bold tracking-tight border border-emerald-300/70">
                      2 MO. FREE
                    </span>
                  )}
                  {starterPricing.isReferralDiscount && (
                    <span className="inline-flex items-center px-2 py-0.5 rounded-full bg-zinc-950 text-white text-[9.5px] font-mono font-bold tracking-tight">
                      10% REF OFF
                    </span>
                  )}
                </div>
              </div>

              <p className="text-xs text-zinc-500 leading-relaxed mb-6">
                Connect custom domains, send branded emails, and invite 2 teammates.
              </p>

              <div className="mb-6 pb-6 border-b border-zinc-100">
                <div className="flex items-baseline gap-1.5 flex-wrap">
                  <span className="font-display text-4xl sm:text-5xl font-bold text-zinc-950">
                    ₹{starterPricing.effectiveMonthlyRate.toLocaleString('en-IN')}
                  </span>
                  <span className="text-xs text-zinc-500 font-medium">/ month</span>
                  {starterPricing.discountPercentage > 0 && (
                    <span className="text-xs text-zinc-400 line-through ml-1 font-mono">
                      ₹{isAnnual ? '833' : '999'}
                    </span>
                  )}
                </div>
                <div className="text-[11px] text-zinc-500 mt-2 flex flex-col gap-0.5">
                  {isAnnual ? (
                    <>
                      <div className="flex items-center gap-1.5 font-medium text-emerald-700">
                        <span>Billed annually: ₹{starterPricing.grandTotal.toLocaleString('en-IN')}/yr</span>
                        <span className="text-zinc-300">&bull;</span>
                        <span className="text-emerald-800 font-bold">
                          Save ₹{(starterPricing.standardFullRate - starterPricing.grandTotal).toLocaleString('en-IN')}
                        </span>
                      </div>
                      <div className="text-[10px] text-zinc-400">
                        Includes 18% GST (12 mos. access for 10)
                      </div>
                    </>
                  ) : (
                    <span className="text-zinc-400">Billed monthly &bull; Incl. 18% GST</span>
                  )}
                </div>
              </div>

              <div className="mb-3">
                <span className="inline-block px-2.5 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200/60 text-[10px] font-mono font-bold uppercase tracking-wider">
                  Everything in Free, plus:
                </span>
              </div>
              <ul className="space-y-2.5 text-xs mb-8">
                <PricingFeature 
                  icon={Sparkles} 
                  iconClass="text-amber-500" 
                >
                  <span className="font-semibold text-zinc-900">{isAnnual ? '6,000' : '5,000'}</span> CORA AI runs / mo
                </PricingFeature>
                <PricingFeature 
                  icon={Globe} 
                  iconClass="text-emerald-600 stroke-[2.2]" 
                  highlight
                  textColor="text-zinc-900 font-medium"
                >
                  <span className="font-semibold text-emerald-950">Free custom domain</span> <span className="font-mono text-[10px] text-emerald-800 bg-emerald-100/80 border border-emerald-300/70 px-1.5 py-0.5 rounded font-bold">.com / .in</span>
                </PricingFeature>
                <PricingFeature 
                  icon={Layout} 
                  iconClass="text-zinc-900" 
                >
                  <span className="font-semibold text-zinc-900">5 CMS collections</span> &amp; custom pages
                </PricingFeature>
                <PricingFeature 
                  icon={HardDrive} 
                  iconClass="text-zinc-900" 
                >
                  <span className="font-semibold text-zinc-900">25 GB</span> bandwidth &bull; <span className="font-semibold text-zinc-900">5 GB</span> storage
                </PricingFeature>
                <PricingFeature 
                  icon={UploadCloud} 
                  iconClass="text-emerald-700" 
                  highlight
                  textColor="text-zinc-900 font-medium"
                >
                  Form file &amp; media uploads
                </PricingFeature>
                <PricingFeature 
                  icon={Check} 
                  iconClass="text-zinc-900" 
                >
                  Unlimited form submissions
                </PricingFeature>
                <PricingFeature 
                  icon={GitBranch} 
                  iconClass="text-zinc-900" 
                >
                  Branching with previews
                </PricingFeature>
                <PricingFeature 
                  icon={ArrowRightLeft} 
                  iconClass="text-zinc-900" 
                >
                  Site redirects (301/302)
                </PricingFeature>
                <PricingFeature 
                  icon={Lock} 
                  iconClass="text-zinc-900" 
                >
                  Password protection &amp; client links
                </PricingFeature>
                <PricingFeature 
                  icon={Languages} 
                  iconClass="text-zinc-900" 
                >
                  Localization &amp; geo routing
                </PricingFeature>
                <PricingFeature 
                  icon={Mail} 
                  iconClass="text-zinc-900" 
                >
                  Custom business email sending
                </PricingFeature>
                <PricingFeature 
                  icon={Sparkles} 
                  iconClass="text-amber-500" 
                >
                  Built-in MCP server &amp; AI tools
                </PricingFeature>
                <PricingFeature 
                  icon={Users} 
                  iconClass="text-zinc-900" 
                >
                  Up to 2 team workspace seats
                </PricingFeature>
              </ul>
            </div>

            <button
              type="button"
              onClick={() => handlePlanSelect('starter')}
              className="w-full py-3 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 rounded-xl text-xs sm:text-sm font-semibold transition-colors shadow-2xs cursor-pointer text-center block"
            >
              {isAnnual ? 'Get Started (Annual)' : 'Get Started'}
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
                  <div className="flex items-center gap-2 flex-wrap">
                    <div className="w-8 h-8 rounded-xl bg-zinc-800 text-pink-400 flex items-center justify-center border border-zinc-700">
                      <Sparkles className="w-4 h-4" />
                    </div>
                    <h3 className="font-display text-lg font-bold text-white">Professional</h3>
                    {!isPartner100Coupon(appliedCoupon) && isAnnual && (
                      <span className="inline-flex items-center px-2 py-0.5 rounded-full bg-pink-950/80 text-pink-300 text-[10px] font-mono font-bold tracking-tight border border-pink-700/80">
                        2 MO. FREE
                      </span>
                    )}
                    {proPricing.isReferralDiscount && (
                      <span className="inline-flex items-center px-2 py-0.5 rounded-full bg-pink-500 text-white text-[9.5px] font-mono font-bold tracking-tight">
                        10% REF OFF
                      </span>
                    )}
                  </div>
                </div>

                <p className="text-xs text-zinc-400 leading-relaxed mb-6">
                  Automate WhatsApp updates, collect payments with UPI QR, and deploy reasoning AI.
                </p>

              <div className="mb-6 pb-6 border-b border-zinc-800">
                <div className="flex items-baseline gap-1.5 flex-wrap">
                  <span className="font-display text-4xl sm:text-5xl font-bold text-white">
                    {isPartner100Coupon(appliedCoupon) 
                      ? '₹0' 
                      : `₹${proPricing.effectiveMonthlyRate.toLocaleString('en-IN')}`}
                  </span>
                  <span className="text-xs text-zinc-400 font-medium">/ month</span>
                  {(isPartner100Coupon(appliedCoupon) || proPricing.discountPercentage > 0) && (
                    <span className="text-xs text-zinc-500 line-through ml-2 font-mono">
                      ₹{isAnnual ? '2,083' : '2,499'}
                    </span>
                  )}
                </div>
                <div className="text-[11px] text-zinc-400 mt-2 flex flex-col gap-0.5">
                  {isPartner100Coupon(appliedCoupon) ? (
                    <span className="text-emerald-400 font-bold">100% Partner Discount Applied • ₹0 Forever</span>
                  ) : isAnnual ? (
                    <>
                      <div className="flex items-center gap-1.5 font-medium text-emerald-400">
                        <span>Billed annually: ₹{proPricing.grandTotal.toLocaleString('en-IN')}/yr</span>
                        <span className="text-zinc-600">&bull;</span>
                        <span className="text-emerald-300 font-bold">
                          Save ₹{(proPricing.standardFullRate - proPricing.grandTotal).toLocaleString('en-IN')}
                        </span>
                      </div>
                      <div className="text-[10px] text-zinc-500">
                        Includes 18% GST (12 mos. access for 10)
                      </div>
                    </>
                  ) : (
                    <span className="text-zinc-400">Billed monthly &bull; Incl. 18% GST</span>
                  )}
                </div>
              </div>

              <div className="mb-3">
                <span className="inline-block px-2.5 py-0.5 rounded-md bg-pink-950/80 text-pink-300 border border-pink-700/60 text-[10px] font-mono font-bold uppercase tracking-wider">
                  Everything in Starter, plus:
                </span>
              </div>
              <ul className="space-y-2.5 text-xs mb-8">
                <PricingFeature 
                  icon={Sparkles} 
                  iconClass="text-blue-400 stroke-[2.2]" 
                  highlight
                  textColor="text-white font-medium"
                >
                  <span className="font-semibold text-white">Advanced AI Reasoning Engine (CORA AI)</span>
                </PricingFeature>
                <PricingFeature 
                  icon={Sparkles} 
                  iconClass="text-amber-400" 
                  textColor="text-zinc-300"
                >
                  <span className="font-semibold text-white">14,000</span> CORA AI runs / mo
                </PricingFeature>
                <PricingFeature 
                  icon={Globe} 
                  iconClass="text-emerald-400 stroke-[2.2]" 
                  highlight
                  textColor="text-emerald-300 font-medium"
                >
                  <span className="font-semibold text-emerald-300">Free custom domain</span> <span className="font-mono text-[10px] text-emerald-300 bg-emerald-950/90 border border-emerald-700/80 px-1.5 py-0.5 rounded font-bold">.com / .in</span>
                </PricingFeature>
                <PricingFeature 
                  icon={HardDrive} 
                  iconClass="text-pink-400" 
                  textColor="text-zinc-300"
                >
                  <span className="font-semibold text-zinc-200">50 GB</span> bandwidth &bull; <span className="font-semibold text-zinc-200">15 GB</span> storage
                </PricingFeature>
                <PricingFeature 
                  icon={Split} 
                  iconClass="text-pink-400 stroke-[2.2]" 
                  highlight
                  textColor="text-pink-200 font-medium"
                >
                  A/B testing &amp; optimization
                </PricingFeature>
                <PricingFeature 
                  icon={Search} 
                  iconClass="text-pink-400 stroke-[2.2]" 
                  highlight
                  textColor="text-pink-200 font-medium"
                >
                  AI site search &amp; vector indexing
                </PricingFeature>
                <PricingFeature 
                  icon={UploadCloud} 
                  iconClass="text-pink-300" 
                  textColor="text-zinc-300"
                >
                  Form file &amp; media uploads
                </PricingFeature>
                <PricingFeature 
                  icon={GitBranch} 
                  iconClass="text-pink-300" 
                  textColor="text-zinc-300"
                >
                  Branching with previews
                </PricingFeature>
                <PricingFeature 
                  icon={ArrowRightLeft} 
                  iconClass="text-pink-300" 
                  textColor="text-zinc-300"
                >
                  Site redirects (301/302)
                </PricingFeature>
                <PricingFeature 
                  icon={Lock} 
                  iconClass="text-pink-300" 
                  textColor="text-zinc-300"
                >
                  Password protection &amp; client links
                </PricingFeature>
                <PricingFeature 
                  icon={Languages} 
                  iconClass="text-pink-300" 
                  textColor="text-zinc-300"
                >
                  Localization &amp; geo routing
                </PricingFeature>
                <PricingFeature 
                  icon={WhatsAppIcon} 
                  iconClass="text-[#25D366]" 
                  highlight
                  textColor="text-white font-semibold"
                >
                  Official WhatsApp automated dispatch*
                </PricingFeature>
                <PricingFeature 
                  icon={Headphones} 
                  iconClass="text-pink-400 stroke-[2.2]" 
                  highlight
                  textColor="text-pink-200 font-medium"
                >
                  Priority support desk
                </PricingFeature>
                <PricingFeature 
                  icon={Users} 
                  iconClass="text-emerald-400" 
                  textColor="text-zinc-300" 
                >
                  Up to 5 team workspace seats &amp; roles
                </PricingFeature>
              </ul>
            </div>

            <button
              type="button"
              onClick={() => handlePlanSelect('pro')}
              className="w-full py-3 rounded-xl text-xs sm:text-sm font-semibold transition-colors shadow-2xs cursor-pointer text-center block bg-white hover:bg-zinc-100 text-zinc-950 font-bold"
            >
              {isPartner100Coupon(appliedCoupon)
                ? 'Claim Free Plan'
                : (isAnnual ? 'Get Started (Annual)' : 'Get Started')}
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
                <div className="flex items-center gap-2 flex-wrap">
                  <div className="w-8 h-8 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center border border-purple-200">
                    <TrendingUp className="w-4 h-4" />
                  </div>
                  <h3 className="font-display text-lg font-bold text-zinc-950">Scale</h3>
                  {isAnnual && (
                    <span className="inline-flex items-center px-2 py-0.5 rounded-full bg-purple-100 text-purple-800 text-[10px] font-mono font-bold tracking-tight border border-purple-300/70">
                      2 MO. FREE
                    </span>
                  )}
                  {scalePricing.isReferralDiscount && (
                    <span className="inline-flex items-center px-2 py-0.5 rounded-full bg-zinc-950 text-white text-[9.5px] font-mono font-bold tracking-tight">
                      10% REF OFF
                    </span>
                  )}
                </div>
              </div>

              <p className="text-xs text-zinc-500 leading-relaxed mb-6">
                Scale your agency with unlimited seats, custom APIs, and free site migration.
              </p>

              <div className="mb-6 pb-6 border-b border-zinc-100">
                <div className="flex items-baseline gap-1.5 flex-wrap">
                  <span className="font-display text-4xl sm:text-5xl font-bold text-zinc-950">
                    ₹{scalePricing.effectiveMonthlyRate.toLocaleString('en-IN')}
                  </span>
                  <span className="text-xs text-zinc-500 font-medium">/ month</span>
                  {scalePricing.discountPercentage > 0 && (
                    <span className="text-xs text-zinc-400 line-through ml-1 font-mono">
                      ₹{isAnnual ? '3,749' : '4,499'}
                    </span>
                  )}
                </div>
                <div className="text-[11px] text-zinc-500 mt-2 flex flex-col gap-0.5">
                  {isAnnual ? (
                    <>
                      <div className="flex items-center gap-1.5 font-medium text-emerald-700">
                        <span>Billed annually: ₹{scalePricing.grandTotal.toLocaleString('en-IN')}/yr</span>
                        <span className="text-zinc-300">&bull;</span>
                        <span className="text-emerald-800 font-bold">
                          Save ₹{(scalePricing.standardFullRate - scalePricing.grandTotal).toLocaleString('en-IN')}
                        </span>
                      </div>
                      <div className="text-[10px] text-zinc-400">
                        Includes 18% GST (12 mos. access for 10)
                      </div>
                    </>
                  ) : (
                    <span className="text-zinc-400">Billed monthly &bull; Incl. 18% GST</span>
                  )}
                </div>
              </div>

              <div className="mb-3">
                <span className="inline-block px-2.5 py-0.5 rounded-md bg-purple-50 text-purple-900 border border-purple-200/60 text-[10px] font-mono font-bold uppercase tracking-wider">
                  Everything in Professional, plus:
                </span>
              </div>
              <ul className="space-y-2.5 text-xs text-zinc-700 mb-8">
                <PricingFeature 
                  icon={Gift} 
                  iconClass="text-purple-600 stroke-[2.2]" 
                  highlight
                  textColor="text-purple-950 font-medium"
                >
                  <span className="font-semibold text-purple-950">Free Website Dev &amp; Migration</span>
                </PricingFeature>
                <PricingFeature 
                  icon={Sparkles} 
                  iconClass="text-purple-600 stroke-[2.2]" 
                >
                  <span className="font-semibold text-zinc-900">Frontier AI Models</span> (GPT-4o, Claude 3.5)
                </PricingFeature>
                <PricingFeature 
                  icon={Sparkles} 
                  iconClass="text-amber-500" 
                >
                  <span className="font-semibold text-zinc-900">20,000</span> CORA AI runs / mo
                </PricingFeature>
                <PricingFeature 
                  icon={Globe} 
                  iconClass="text-emerald-600 stroke-[2.2]" 
                  highlight
                  textColor="text-zinc-900 font-medium"
                >
                  <span className="font-semibold text-emerald-950">Free custom domain</span> <span className="font-mono text-[10px] text-emerald-800 bg-emerald-100/80 border border-emerald-300/70 px-1.5 py-0.5 rounded font-bold">.com / .in</span>
                </PricingFeature>
                <PricingFeature 
                  icon={HardDrive} 
                  iconClass="text-zinc-900" 
                >
                  <span className="font-semibold text-zinc-900">100 GB</span> bandwidth &bull; <span className="font-semibold text-zinc-900">30 GB</span> storage
                </PricingFeature>
                <PricingFeature 
                  icon={Split} 
                  iconClass="text-purple-600 stroke-[2.2]" 
                  highlight
                  textColor="text-purple-950 font-medium"
                >
                  Multi-variant A/B testing &amp; analytics
                </PricingFeature>
                <PricingFeature 
                  icon={Search} 
                  iconClass="text-purple-600 stroke-[2.2]" 
                  highlight
                  textColor="text-purple-950 font-medium"
                >
                  AI site search &amp; knowledge graph
                </PricingFeature>
                <PricingFeature 
                  icon={UploadCloud} 
                  iconClass="text-zinc-900" 
                >
                  Form file &amp; media uploads
                </PricingFeature>
                <PricingFeature 
                  icon={GitBranch} 
                  iconClass="text-emerald-600" 
                >
                  <span className="font-medium text-zinc-900">Branching with previews &amp; staging sandbox</span>
                </PricingFeature>
                <PricingFeature 
                  icon={ArrowRightLeft} 
                  iconClass="text-zinc-900" 
                >
                  Custom 301/302 URL redirects
                </PricingFeature>
                <PricingFeature 
                  icon={Lock} 
                  iconClass="text-zinc-900" 
                >
                  Password protection &amp; SSO security
                </PricingFeature>
                <PricingFeature 
                  icon={Languages} 
                  iconClass="text-zinc-900" 
                >
                  Localization &amp; multi-region routing
                </PricingFeature>
                <PricingFeature 
                  icon={Webhook} 
                  iconClass="text-zinc-900" 
                >
                  Custom Webhooks &amp; REST APIs
                </PricingFeature>
                <PricingFeature 
                  icon={Headphones} 
                  iconClass="text-purple-600 stroke-[2.2]" 
                  highlight
                  textColor="text-purple-950 font-medium"
                >
                  Priority 24/7 Slack &amp; WhatsApp support
                </PricingFeature>
                <PricingFeature 
                  icon={Users} 
                  iconClass="text-zinc-900" 
                >
                  Unlimited* team workspace seats &amp; roles
                </PricingFeature>
              </ul>
            </div>

            <button
              type="button"
              onClick={() => handlePlanSelect('scale')}
              className="w-full py-3 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 rounded-xl text-xs sm:text-sm font-semibold transition-colors shadow-2xs cursor-pointer text-center block"
            >
              {isAnnual ? 'Get Started (Annual)' : 'Get Started'}
            </button>
          </div>

        </div>

        {/* Tax Note */}
        <div className="mt-8 text-center text-xs text-zinc-500 flex items-center justify-center gap-1.5 font-mono">
          <Receipt className="w-3.5 h-3.5 text-zinc-600" />
          <span>All prices are inclusive of 18% GST. Instant UPI &amp; card checkout with compliant tax invoices.</span>
        </div>

      </div>
    </section>
  );
}
