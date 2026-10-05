'use client';

import React, { useEffect } from 'react';
import { trackEvent } from '@/components/analytics/Analytics';

declare global {
  interface Window {
    Razorpay: any;
    endorsely_referral?: string;
  }
}

export type PlanKey = 'free' | 'starter' | 'pro' | 'scale' | 'india_only';
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
    annualBase: 5988
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
 * Direct 1-click Checkout launcher with affiliate & referral tracking.
 * - Free plan -> Instant redirect to workspace onboarding
 * - 100% Partner Coupon -> Instant zero-friction redirect with full Pro plan activation
 * - Paid plans -> Opens native Razorpay checkout directly with Endorsely referral metadata
 */
export function triggerRazorpayCheckout({
  planKey,
  billingCycle,
  appliedCoupon,
  onDismiss
}: {
  planKey: PlanKey;
  billingCycle: BillingCycle;
  appliedCoupon?: string | null;
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
  const isAnnual = billingCycle === 'annual';

  let basePrice = 0;
  if (planKey === 'india_only') {
    basePrice = appliedCoupon === 'INDIA40' ? 3588 : 5988;
  } else {
    basePrice = isAnnual ? plan.annualBase : plan.monthlyBase;
  }

  const gstTax = basePrice * 0.18;
  const grandTotal = basePrice + gstTax;

  trackEvent('payment_initiate_direct', { 
    plan: planKey, 
    billing: billingCycle, 
    basePrice, 
    gstTax, 
    grandTotal,
    referral: endorselyReferral 
  });

  const launchRzp = () => {
    const options = {
      key: RAZORPAY_KEY,
      amount: Math.round(grandTotal * 100), // in paise
      currency: 'INR',
      name: 'Cora Studio AI',
      description: `${plan.name} (${isAnnual ? 'Annual • 2 Mo. Free' : 'Monthly'}) + 18% GST`,
      image: 'https://heycora.in/favicon.png',
      notes: {
        plan: planKey,
        billing: billingCycle,
        base_amount: String(basePrice),
        gst_amount: String(gstTax),
        grand_total: String(grandTotal),
        coupon: appliedCoupon || '',
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
          referral: endorselyReferral 
        });

        const paymentId = response?.razorpay_payment_id || '';
        const email = response?.razorpay_email || '';
        const emailParam = email ? `&email=${encodeURIComponent(email)}` : '';

        const redirectUrl = `https://app.heycora.in/workspace/login?plan=${encodeURIComponent(planKey)}&billing=${encodeURIComponent(billingCycle)}&payment_id=${encodeURIComponent(paymentId)}${emailParam}${refParam}&paid=1`;
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
 * Legacy compatibility export for any dependent imports (no-op modal).
 */
export function FastRazorpayModal(_props: any) {
  return null;
}
