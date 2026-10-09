'use client';

import React, { useState, useEffect } from 'react';
import { 
  X, 
  Check, 
  Lock, 
  ShieldCheck, 
  Sparkles, 
  ArrowRight, 
  Tag, 
  Receipt, 
  Gift, 
  Building2 
} from 'lucide-react';
import { trackEvent } from '@/components/analytics/Analytics';

declare global {
  interface Window {
    Razorpay: any;
    endorsely_referral?: string;
  }
}

export type PlanKey = 
  | 'free' 
  | 'starter' 
  | 'pro' 
  | 'scale' 
  | 'india_only'
  | 'vnx_starter'
  | 'vnx_growth'
  | 'vnx_scale'
  | 'vnx_vip'
  | 'vnx_trial';

export type BillingCycle = 'monthly' | 'annual';

export const RAZORPAY_KEY = 'rzp_live_TRDi55lfKpnzCM';

export interface PlanPricingConfig {
  name: string;
  monthlyBase: number;
  annualBase: number;
}

export const PLAN_PRICING: Record<PlanKey, PlanPricingConfig> = {
  free: {
    name: 'Free Forever',
    monthlyBase: 0,
    annualBase: 0
  },
  starter: {
    name: 'Starter Plan',
    monthlyBase: 999,
    annualBase: 9990
  },
  pro: {
    name: 'Professional Plan',
    monthlyBase: 2499,
    annualBase: 24990
  },
  scale: {
    name: 'Scale Plan',
    monthlyBase: 4499,
    annualBase: 44990
  },
  india_only: {
    name: 'India Edition Plan',
    monthlyBase: 499,
    annualBase: 4990
  },
  vnx_starter: {
    name: 'Coaches Starter (VNX Digital)',
    monthlyBase: 9999,
    annualBase: 99990
  },
  vnx_growth: {
    name: 'Coaches Growth & Ads Tracking (VNX Digital)',
    monthlyBase: 14999,
    annualBase: 149990
  },
  vnx_scale: {
    name: 'Coaches Scale & Course LMS (VNX Digital)',
    monthlyBase: 29999,
    annualBase: 299990
  },
  vnx_vip: {
    name: 'VIP Coaching Accelerator (Annual)',
    monthlyBase: 49999,
    annualBase: 49999
  },
  vnx_trial: {
    name: '3-Day Coaching Engine Trial (VNX Digital)',
    monthlyBase: 999,
    annualBase: 999
  }
};

/**
 * Check if the provided coupon is a 100% OFF Partner Pass (OctAffialiate / NovAffialiate).
 */
export function isPartner100Coupon(code: string | null | undefined): boolean {
  if (!code) return false;
  const clean = code.trim().toLowerCase();
  return (
    clean === 'octaffialiate' ||
    clean === 'octaffiliate' ||
    clean === 'novaffialiate' ||
    clean === 'novaffiliate'
  );
}

/**
 * Safely extract Endorsely affiliate referral ID across window, cookies, localStorage, and URL params.
 */
export function getEndorselyReferralId(): string {
  if (typeof window === 'undefined') return '';
  try {
    // 1. Direct window global set by endorsely.js
    if (window.endorsely_referral && typeof window.endorsely_referral === 'string') {
      return window.endorsely_referral.trim();
    }

    // 2. Cookie lookup
    const cookieMatch = document.cookie.match(/(?:^|;\s*)endorsely_referral=([^;]+)/);
    if (cookieMatch && cookieMatch[1]) {
      return decodeURIComponent(cookieMatch[1]).trim();
    }

    // 3. LocalStorage persistence
    const stored = localStorage.getItem('endorsely_referral');
    if (stored) {
      return stored.trim();
    }

    // 4. URL query parameters (?via=... or ?ref=... or ?endorsely_referral=...)
    const urlParams = new URLSearchParams(window.location.search);
    const queryRef = urlParams.get('endorsely_referral') || urlParams.get('via') || urlParams.get('ref');
    if (queryRef) {
      localStorage.setItem('endorsely_referral', queryRef.trim());
      return queryRef.trim();
    }
  } catch (e) {
    // Silent failover
  }
  return '';
}

/**
 * Calculate accurate discounted pricing with coupon, annual 2-months-free, and 18% inclusive GST splits.
 */
export function calculateDiscountedPrice(
  planKey: PlanKey,
  billingCycle: BillingCycle,
  couponCode?: string | null
): {
  standardFullRate: number;
  annualSavings: number;
  originalBasePrice: number;
  discountPercentage: number;
  discountAmount: number;
  finalBasePrice: number;
  taxableBase: number;
  gstTax: number;
  grandTotal: number;
  effectiveMonthlyRate: number;
  cleanCode: string;
  isAnnual: boolean;
} {
  const plan = PLAN_PRICING[planKey] || PLAN_PRICING.pro;
  const isAnnual = billingCycle === 'annual';

  let standardFullRate = plan.monthlyBase * (isAnnual ? 12 : 1);
  let originalBasePrice = isAnnual ? plan.annualBase : plan.monthlyBase;
  let annualSavings = isAnnual ? Math.max(0, standardFullRate - plan.annualBase) : 0;

  if (planKey === 'india_only') {
    standardFullRate = 499 * (isAnnual ? 12 : 1);
    originalBasePrice = isAnnual ? 4990 : 499;
    annualSavings = isAnnual ? 998 : 0;
  } else if (planKey === 'vnx_vip') {
    standardFullRate = 49999;
    originalBasePrice = 49999;
    annualSavings = 0;
  }

  let discountPercentage = 0;
  const cleanCode = (couponCode || '').trim().toUpperCase();

  if (isPartner100Coupon(couponCode)) {
    discountPercentage = 100;
  } else if (cleanCode === 'INDIA40') {
    discountPercentage = 40;
  } else if (cleanCode === 'CORA10' || cleanCode === 'SAVE10' || cleanCode === 'DISCOUNT10') {
    discountPercentage = 10;
  } else if (cleanCode === 'CORA20' || cleanCode === 'SAVE20') {
    discountPercentage = 20;
  } else if (cleanCode === 'CORA30' || cleanCode === 'SAVE30') {
    discountPercentage = 30;
  } else if (cleanCode === 'CORA50' || cleanCode === 'SAVE50') {
    discountPercentage = 50;
  } else if (cleanCode) {
    const match = cleanCode.match(/\d{1,2}/);
    if (match) {
      const num = parseInt(match[0], 10);
      if (num > 0 && num <= 100) {
        discountPercentage = num;
      }
    }
  }

  let discountAmount = 0;
  let grandTotal = originalBasePrice;

  if (planKey === 'india_only' && cleanCode === 'INDIA40') {
    grandTotal = isAnnual ? 3588 : 299; // ₹299/mo rate
    discountAmount = Math.max(0, originalBasePrice - grandTotal);
    discountPercentage = 40;
  } else if (discountPercentage > 0) {
    discountAmount = Math.round(originalBasePrice * (discountPercentage / 100));
    grandTotal = Math.max(0, originalBasePrice - discountAmount);
  }

  // Price is INCLUSIVE of 18% GST
  const taxableBase = Math.round((grandTotal / 1.18) * 100) / 100;
  const gstTax = Math.round((grandTotal - taxableBase) * 100) / 100;
  const finalBasePrice = grandTotal; // For backward compat, final price paid
  const effectiveMonthlyRate = isAnnual 
    ? Math.round(grandTotal / 12) 
    : grandTotal;

  return {
    standardFullRate,
    annualSavings,
    originalBasePrice,
    discountPercentage,
    discountAmount,
    finalBasePrice,
    taxableBase,
    gstTax,
    grandTotal,
    effectiveMonthlyRate,
    cleanCode,
    isAnnual
  };
}

/**
 * Direct 1-click Checkout launcher with affiliate & referral tracking.
 */
export function triggerRazorpayCheckout({
  planKey,
  billingCycle,
  appliedCoupon,
  gstin,
  onDismiss
}: {
  planKey: PlanKey;
  billingCycle: BillingCycle;
  appliedCoupon?: string | null;
  gstin?: string | null;
  onDismiss?: () => void;
}) {
  const endorselyReferral = getEndorselyReferralId();
  const refParam = endorselyReferral ? `&endorsely_referral=${encodeURIComponent(endorselyReferral)}` : '';

  // Free Forever -> Instant zero-friction redirect with referral persistence
  if (planKey === 'free') {
    trackEvent('free_plan_signup_clicked', { source: 'pricing_card', referral: endorselyReferral });
    window.location.href = `https://app.heycora.in/workspace/login?plan=free${refParam}`;
    return;
  }

  // 100% Partner Discount for Professional Plan (OctAffialiate / NovAffialiate)
  if (isPartner100Coupon(appliedCoupon) && planKey === 'pro') {
    trackEvent('partner_100_claimed', { 
      plan: 'pro', 
      coupon: appliedCoupon, 
      billing: billingCycle,
      referral: endorselyReferral 
    });
    window.location.href = `https://app.heycora.in/workspace/login?plan=pro&billing=${encodeURIComponent(billingCycle)}&coupon=${encodeURIComponent(appliedCoupon || '')}&discount=100&paid=1${refParam}`;
    return;
  }

  const plan = PLAN_PRICING[planKey] || PLAN_PRICING.pro;
  const {
    originalBasePrice,
    discountPercentage,
    discountAmount,
    taxableBase,
    gstTax,
    grandTotal,
    cleanCode,
    isAnnual
  } = calculateDiscountedPrice(planKey, billingCycle, appliedCoupon);

  trackEvent('payment_initiate_direct', { 
    plan: planKey, 
    billing: billingCycle, 
    originalBasePrice,
    discountPercentage,
    discountAmount,
    taxableBase, 
    gstTax, 
    grandTotal,
    coupon: cleanCode,
    gstin: gstin || '',
    referral: endorselyReferral 
  });

  const launchRzp = () => {
    const options = {
      key: RAZORPAY_KEY,
      amount: Math.round(grandTotal * 100), // in paise (INCLUSIVE of 18% GST)
      currency: 'INR',
      name: 'Cora Studio AI',
      description: isAnnual
        ? `${plan.name} Annual (12 Mos. Access • 2 Mos. Free${discountPercentage > 0 ? ` + ${discountPercentage}% OFF Coupon ${cleanCode}` : ''}) • ₹${grandTotal.toLocaleString('en-IN')} (Incl. 18% GST)`
        : `${plan.name} Monthly${discountPercentage > 0 ? ` (${discountPercentage}% OFF Coupon ${cleanCode})` : ''} • ₹${grandTotal.toLocaleString('en-IN')} (Incl. 18% GST)`,
      image: 'https://heycora.in/favicon.png',
      notes: {
        plan: planKey,
        billing: billingCycle,
        billing_mode: isAnnual ? 'Annual (12 Months Access • Pay for 10 Months)' : 'Monthly',
        original_price: String(originalBasePrice),
        discount_applied: discountPercentage > 0 ? `${discountPercentage}% (Save ₹${discountAmount})` : (isAnnual ? '2 Months Free Included' : 'None'),
        coupon: cleanCode || '',
        taxable_base_amount: String(taxableBase),
        gst_amount: String(gstTax),
        grand_total_inclusive: String(grandTotal),
        gstin: gstin || '',
        endorsely_referral: endorselyReferral || ''
      },
      theme: {
        color: '#09090b',
        backdrop_color: 'rgba(9,9,11,0.85)'
      },
      handler: function (response: any) {
        trackEvent('payment_success', { 
          plan: planKey, 
          billing: billingCycle, 
          payment_id: response?.razorpay_payment_id,
          coupon: cleanCode,
          grand_total: grandTotal,
          referral: endorselyReferral 
        });

        const paymentId = response?.razorpay_payment_id || '';
        const email = response?.razorpay_email || '';
        const emailParam = email ? `&email=${encodeURIComponent(email)}` : '';
        const couponParam = cleanCode ? `&coupon=${encodeURIComponent(cleanCode)}` : '';
        const gstinParam = gstin ? `&gstin=${encodeURIComponent(gstin)}` : '';

        const redirectUrl = `https://app.heycora.in/workspace/login?plan=${encodeURIComponent(planKey)}&billing=${encodeURIComponent(billingCycle)}&payment_id=${encodeURIComponent(paymentId)}${couponParam}${emailParam}${gstinParam}${refParam}&paid=1`;
        window.location.href = redirectUrl;
      },
      modal: {
        ondismiss: function () {
          if (onDismiss) onDismiss();
        }
      }
    };

    if (window.Razorpay) {
      const rzp = new window.Razorpay(options);
      rzp.open();
    } else {
      // Load SDK on demand and trigger
      const script = document.createElement('script');
      script.src = 'https://checkout.razorpay.com/v1/checkout.js';
      script.async = true;
      script.onload = () => {
        if (window.Razorpay) {
          const rzp = new window.Razorpay(options);
          rzp.open();
        } else {
          window.location.href = `https://app.heycora.in/workspace/login?plan=${encodeURIComponent(planKey)}&billing=${encodeURIComponent(billingCycle)}${refParam}`;
        }
      };
      document.body.appendChild(script);
    }
  };

  launchRzp();
}

/**
 * Preload Razorpay Checkout SDK & capture referral params on initial load.
 */
export function useRazorpayPreload() {
  useEffect(() => {
    if (typeof window !== 'undefined') {
      // Capture and persist referral if present in URL on arrival
      try {
        const urlParams = new URLSearchParams(window.location.search);
        const ref = urlParams.get('endorsely_referral') || urlParams.get('via') || urlParams.get('ref');
        if (ref) {
          localStorage.setItem('endorsely_referral', ref);
        }
      } catch (e) {}

      // Preload Razorpay SDK
      if (!window.Razorpay) {
        const existing = document.querySelector('script[src="https://checkout.razorpay.com/v1/checkout.js"]');
        if (!existing) {
          const script = document.createElement('script');
          script.src = 'https://checkout.razorpay.com/v1/checkout.js';
          script.async = true;
          document.body.appendChild(script);
        }
      }
    }
  }, []);
}

/**
 * Slide-in Interactive Checkout Drawer (Desktop Right Drawer / Mobile Bottom Sheet)
 * Provides 100% transparent pricing breakdown, live coupon code input, and instant Razorpay launch.
 */
export function CheckoutDrawer({
  isOpen,
  onClose,
  planKey,
  initialBillingCycle = 'annual',
  initialCoupon = null
}: {
  isOpen: boolean;
  onClose: () => void;
  planKey: PlanKey;
  initialBillingCycle?: BillingCycle;
  initialCoupon?: string | null;
}) {
  const [billingCycle, setBillingCycle] = useState<BillingCycle>(initialBillingCycle);
  const [couponInput, setCouponInput] = useState('');
  const [activeCoupon, setActiveCoupon] = useState<string | null>(initialCoupon);
  const [gstin, setGstin] = useState('');
  const [couponError, setCouponError] = useState('');
  const [isApplying, setIsApplying] = useState(false);

  useEffect(() => {
    setBillingCycle(initialBillingCycle);
  }, [initialBillingCycle]);

  useEffect(() => {
    if (initialCoupon) {
      setActiveCoupon(initialCoupon);
      setCouponInput(initialCoupon);
    }
  }, [initialCoupon]);

  if (!isOpen) return null;

  const plan = PLAN_PRICING[planKey] || PLAN_PRICING.pro;
  const {
    standardFullRate,
    annualSavings,
    discountPercentage,
    discountAmount,
    finalBasePrice,
    taxableBase,
    gstTax,
    grandTotal,
    effectiveMonthlyRate,
    cleanCode,
    isAnnual
  } = calculateDiscountedPrice(planKey, billingCycle, activeCoupon);

  const handleApplyCoupon = (e: React.FormEvent) => {
    e.preventDefault();
    const code = couponInput.trim().toUpperCase();
    if (!code) {
      setActiveCoupon(null);
      setCouponError('');
      return;
    }

    setIsApplying(true);
    setTimeout(() => {
      setIsApplying(false);
      setActiveCoupon(code);
      setCouponError('');
      trackEvent('coupon_applied_drawer', { plan: planKey, coupon: code });
    }, 200);
  };

  const handleRemoveCoupon = () => {
    setActiveCoupon(null);
    setCouponInput('');
    setCouponError('');
  };

  const handleProceed = () => {
    triggerRazorpayCheckout({
      planKey,
      billingCycle,
      appliedCoupon: activeCoupon,
      gstin: gstin.trim() || undefined,
      onDismiss: () => {}
    });
  };

  return (
    <div className="fixed inset-0 z-50 flex justify-end">
      {/* Blurred Backdrop */}
      <div 
        onClick={onClose}
        className="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity duration-300"
      />

      {/* Slide-in Sheet (Desktop Right Drawer / Mobile Bottom Sheet) */}
      <div className="relative z-10 w-full max-w-[460px] bg-white text-zinc-900 shadow-2xl flex flex-col h-full overflow-y-auto animate-in slide-in-from-right duration-300 sm:rounded-l-3xl border-l border-zinc-200/80">
        
        {/* Header */}
        <div className="p-5 sm:p-6 border-b border-zinc-100 flex items-center justify-between sticky top-0 bg-white/95 backdrop-blur-md z-20">
          <div className="flex items-center gap-2.5">
            <div className="w-8 h-8 rounded-xl bg-zinc-900 text-white flex items-center justify-center">
              <Lock className="w-4 h-4" />
            </div>
            <div>
              <h3 className="font-display font-bold text-base text-zinc-950">Secure Checkout</h3>
              <div className="flex items-center gap-1.5 text-[11px] text-zinc-500 font-medium">
                <ShieldCheck className="w-3.5 h-3.5 text-emerald-600" />
                <span>256-Bit SSL Encrypted &bull; 18% GST Compliant</span>
              </div>
            </div>
          </div>

          <button
            type="button"
            onClick={onClose}
            className="w-8 h-8 rounded-xl bg-zinc-100 hover:bg-zinc-200 flex items-center justify-center text-zinc-600 transition-colors cursor-pointer"
          >
            <X className="w-4 h-4" />
          </button>
        </div>

        {/* Content Body */}
        <div className="p-5 sm:p-6 flex-1 space-y-5">
          
          {/* Plan Selected Card */}
          <div className="p-4 rounded-2xl bg-zinc-50 border border-zinc-200/80 space-y-3">
            <div className="flex items-center justify-between">
              <div>
                <span className="text-[10px] font-mono uppercase font-bold text-zinc-500 tracking-wider">
                  Selected Plan
                </span>
                <h4 className="font-display font-extrabold text-lg text-zinc-950">{plan.name}</h4>
              </div>
              <span className="px-2.5 py-1 rounded-lg bg-zinc-900 text-white font-mono text-xs font-bold">
                {isAnnual ? 'ANNUAL' : 'MONTHLY'}
              </span>
            </div>

            {/* Cadence Switcher Inside Drawer */}
            {planKey !== 'india_only' && (
              <div className="flex items-center p-1 bg-zinc-200/70 rounded-xl">
                <button
                  type="button"
                  onClick={() => setBillingCycle('monthly')}
                  className={`flex-1 py-1.5 text-xs font-semibold rounded-lg transition-all cursor-pointer ${
                    billingCycle === 'monthly' ? 'bg-white text-zinc-950 shadow-2xs' : 'text-zinc-600 hover:text-zinc-900'
                  }`}
                >
                  Monthly
                </button>
                <button
                  type="button"
                  onClick={() => setBillingCycle('annual')}
                  className={`flex-1 py-1.5 text-xs font-semibold rounded-lg transition-all flex items-center justify-center gap-1.5 cursor-pointer ${
                    billingCycle === 'annual' ? 'bg-zinc-950 text-white shadow-2xs' : 'text-zinc-600 hover:text-zinc-900'
                  }`}
                >
                  <span>Annual</span>
                  <span className="px-1.5 py-0.2 rounded bg-emerald-500 text-zinc-950 font-mono text-[9px] font-bold">
                    2 Mo. FREE
                  </span>
                </button>
              </div>
            )}
          </div>

          {/* Promo / Coupon Code Section */}
          <div className="space-y-2">
            <label className="text-xs font-semibold text-zinc-700 flex items-center justify-between">
              <span className="flex items-center gap-1.5">
                <Tag className="w-3.5 h-3.5 text-zinc-600" />
                <span>Promo / Coupon Code</span>
              </span>
              {activeCoupon && (
                <span className="text-emerald-700 font-bold text-[11px]">
                  ✓ {discountPercentage > 0 ? `${discountPercentage}% Discount Applied` : 'Coupon Active'}
                </span>
              )}
            </label>

            <form onSubmit={handleApplyCoupon} className="flex gap-2">
              <input
                type="text"
                value={couponInput}
                onChange={(e) => setCouponInput(e.target.value)}
                placeholder="Enter coupon (e.g. INDIA40, CORA10)"
                className="flex-1 px-3.5 py-2.5 rounded-xl border border-zinc-200 text-xs font-mono font-medium placeholder:text-zinc-400 focus:outline-none focus:border-zinc-900 uppercase"
              />
              {activeCoupon ? (
                <button
                  type="button"
                  onClick={handleRemoveCoupon}
                  className="px-3.5 py-2.5 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-xs font-bold transition-all cursor-pointer"
                >
                  Remove
                </button>
              ) : (
                <button
                  type="submit"
                  disabled={isApplying || !couponInput.trim()}
                  className="px-4 py-2.5 rounded-xl bg-zinc-900 hover:bg-zinc-800 disabled:bg-zinc-200 text-white disabled:text-zinc-400 text-xs font-bold transition-all cursor-pointer"
                >
                  {isApplying ? 'Applying...' : 'Apply'}
                </button>
              )}
            </form>
            {couponError && <p className="text-[11px] text-red-600">{couponError}</p>}
          </div>

          {/* Optional Business GSTIN Input */}
          <div className="space-y-1.5">
            <label className="text-xs font-semibold text-zinc-700 flex items-center justify-between">
              <span className="flex items-center gap-1.5">
                <Building2 className="w-3.5 h-3.5 text-zinc-600" />
                <span>GSTIN / Business Tax ID (Optional)</span>
              </span>
              <span className="text-[10px] text-zinc-500">100% ITC Claimable</span>
            </label>
            <input
              type="text"
              value={gstin}
              onChange={(e) => setGstin(e.target.value.toUpperCase())}
              placeholder="e.g. 29ABCDE1234F1Z5"
              maxLength={15}
              className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 text-xs font-mono placeholder:text-zinc-400 focus:outline-none focus:border-zinc-900 uppercase"
            />
          </div>

          {/* Line-Item Transparent Price Calculation */}
          <div className="p-4 rounded-2xl bg-zinc-50 border border-zinc-200/80 space-y-2.5 text-xs">
            <div className="text-[11px] font-mono uppercase font-bold text-zinc-500 tracking-wider mb-1 flex items-center justify-between">
              <div className="flex items-center gap-1.5">
                <Receipt className="w-3.5 h-3.5 text-zinc-600" />
                <span>Order Breakdown</span>
              </div>
              <span className="text-[10px] text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                18% GST Included
              </span>
            </div>

            {/* Standard 12 Months full price */}
            {isAnnual && (
              <div className="flex items-center justify-between text-zinc-600">
                <span>Standard 12 Months Access</span>
                <span className="font-mono line-through text-zinc-400">
                  ₹{standardFullRate.toLocaleString('en-IN')}
                </span>
              </div>
            )}

            {/* Annual Savings (2 Months Free) */}
            {isAnnual && annualSavings > 0 && (
              <div className="flex items-center justify-between text-emerald-700 font-semibold">
                <span className="flex items-center gap-1">
                  <Gift className="w-3.5 h-3.5 text-emerald-600" />
                  <span>Annual Deal (2 Months Free)</span>
                </span>
                <span className="font-mono">-₹{annualSavings.toLocaleString('en-IN')}</span>
              </div>
            )}

            {/* Coupon Discount */}
            {discountAmount > 0 && (
              <div className="flex items-center justify-between text-emerald-700 font-bold bg-emerald-100/60 p-2 rounded-xl border border-emerald-200/80">
                <span className="flex items-center gap-1">
                  <Sparkles className="w-3.5 h-3.5 text-emerald-600" />
                  <span>Promo Code {cleanCode} ({discountPercentage}% OFF)</span>
                </span>
                <span className="font-mono">-₹{discountAmount.toLocaleString('en-IN')}</span>
              </div>
            )}

            {/* Taxable Base & GST Breakdown */}
            <div className="pt-2 border-t border-zinc-200/80 space-y-1 text-zinc-500 text-[11px]">
              <div className="flex items-center justify-between">
                <span>Taxable Base Value:</span>
                <span className="font-mono">₹{taxableBase.toLocaleString('en-IN')}</span>
              </div>
              <div className="flex items-center justify-between">
                <span>Integrated GST (18% included):</span>
                <span className="font-mono">₹{gstTax.toLocaleString('en-IN')}</span>
              </div>
            </div>

            {/* Grand Total */}
            <div className="flex items-baseline justify-between text-zinc-950 pt-2.5 border-t-2 border-zinc-900">
              <div>
                <span className="font-display font-extrabold text-sm block">Total Due Today</span>
                <span className="text-[10px] text-zinc-500 font-normal">
                  {isAnnual ? `Effective ₹${effectiveMonthlyRate.toLocaleString('en-IN')} / mo (12 mos. access)` : 'Billed monthly'} &bull; <span className="text-emerald-700 font-semibold">All taxes included</span>
                </span>
              </div>
              <div className="text-right">
                <span className="font-display font-extrabold text-2xl text-zinc-950">
                  ₹{grandTotal.toLocaleString('en-IN')}
                </span>
              </div>
            </div>

          </div>

        </div>

        {/* Sticky Drawer Footer Action Button */}
        <div className="p-5 sm:p-6 border-t border-zinc-100 bg-white sticky bottom-0 z-20 space-y-3">
          <button
            type="button"
            onClick={handleProceed}
            className="w-full py-3.5 px-5 rounded-2xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs sm:text-sm transition-all shadow-md flex items-center justify-center gap-2 cursor-pointer group"
          >
            {isPartner100Coupon(activeCoupon) && planKey === 'pro' ? (
              <>
                <span>Activate Free Professional Access</span>
                <ArrowRight className="w-4 h-4 group-hover:translate-x-0.5 transition-transform" />
              </>
            ) : (
              <>
                <span>Proceed to Pay ₹{grandTotal.toLocaleString('en-IN')}</span>
                <ArrowRight className="w-4 h-4 group-hover:translate-x-0.5 transition-transform" />
              </>
            )}
          </button>

          <p className="text-[10px] text-zinc-500 text-center leading-relaxed">
            Instant activation &bull; 100% inclusive of 18% GST &bull; Tax invoice generated
          </p>
        </div>

      </div>
    </div>
  );
}

/**
 * Legacy compatibility export for any dependent imports (no-op modal).
 */
export function FastRazorpayModal(_props: any) {
  return null;
}
