'use client';

import React, { useState, useEffect, useRef } from 'react';
import Image from 'next/image';
import Link from 'next/link';
import { 
  Check, 
  ArrowRight, 
  ChevronDown, 
  ChevronUp, 
  Minus,
  Sparkles,
  Gift,
  Layout,
  ShieldCheck,
  FileCheck,
  FileSignature,
  FolderUp,
  Calendar,
  Receipt,
  Globe,
  Star,
  QrCode,
  Building2,
  Users,
  User,
  Send,
  LifeBuoy,
  Leaf,
  Rocket,
  TrendingUp,
  Shield,
  Clock,
  Flame,
  HardDrive,
  Info,
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
  Mail
} from 'lucide-react';
import { trackEvent } from '@/components/analytics/Analytics';
import { 
  triggerRazorpayCheckout, 
  useRazorpayPreload, 
  isPartner100Coupon, 
  calculateDiscountedPrice,
  PlanKey 
} from '@/components/pricing/FastRazorpayModal';
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

const FeatureInfoTooltip = ({ text }: { text: string }) => {
  return (
    <span className="relative inline-flex items-center group/tooltip ml-1.5 align-middle">
      <button
        type="button"
        aria-label="Feature explanation"
        className="text-zinc-400 hover:text-zinc-800 transition-colors cursor-help p-0.5 rounded-full hover:bg-zinc-100 focus:outline-hidden inline-flex items-center justify-center"
      >
        <Info className="w-3.5 h-3.5 stroke-[1.8]" />
      </button>
      <span className="invisible opacity-0 group-hover/tooltip:visible group-hover/tooltip:opacity-100 group-focus-within/tooltip:visible group-focus-within/tooltip:opacity-100 transition-all duration-150 absolute bottom-full left-0 mb-2 w-56 sm:w-64 p-2.5 rounded-xl bg-zinc-900 text-white text-[11px] leading-relaxed shadow-xl border border-zinc-700 z-50 pointer-events-none backdrop-blur-md normal-case font-normal font-sans text-left">
        {text}
        <span className="absolute top-full left-3 -mt-1 border-4 border-transparent border-t-zinc-900" />
      </span>
    </span>
  );
};

const FAQS = [
  {
    q: 'Is the Free Forever plan really free?',
    a: 'Yes, absolutely free forever with no credit card required. You get 500 complimentary AI agent runs every month, a live portfolio website builder on your free heycora.in subdomain, Kanban CRM, unlimited* SHA-256 e-signatures, and automated GST invoicing. You only upgrade to a paid Growth plan when you want to connect a custom domain or send from your own business email.'
  },
  {
    q: 'How does the 2 Months Free on Annual plans work?',
    a: 'Choose annual billing on Starter (₹9,990/yr), Professional (₹24,990/yr), or Scale (₹44,990/yr) to pay for 10 months and get 12 full months of access—saving up to ₹8,998 immediately. We also credit bonus AI reasoning runs directly to your workspace all year long.'
  },
  {
    q: 'What is included in the Scale Plan Free Website Development & Migration?',
    a: 'When you subscribe to the Scale plan (₹4,499/mo or ₹44,990/yr), our technical engineering and design team builds or migrates your full studio/agency website for free. We handle responsive design, custom CMS, lead form integrations, and domain mapping with zero downtime.'
  },
  {
    q: 'Are the displayed prices inclusive or exclusive of taxes?',
    a: 'All displayed subscription prices are 100% inclusive of 18% GST. No extra taxes or hidden fees are added at checkout. Official GST invoices with 100% Input Tax Credit (ITC) eligibility are generated automatically upon purchase.'
  },
  {
    q: 'What is the difference between Starter, Professional, and Scale?',
    a: 'Starter (₹999/mo) powers up to 2 team seats with custom domain connection, custom email dispatch, and 5,000 AI runs. Professional (₹2,499/mo) adds Advanced Reasoning AI (14,000 runs), official WhatsApp dispatch, dynamic UPI QR codes, and 5 seats. Scale (₹4,499/mo) unlocks all Frontier AI models (20,000 runs), unlimited* seats, custom REST APIs & webhooks, dedicated SLA, and free custom website development/migration.'
  },
  {
    q: 'Can I upgrade, downgrade, or cancel anytime?',
    a: 'Yes, you control your subscription directly from workspace settings anytime. When you upgrade, downgrade, or pause, Cora keeps all your signed contracts, GST invoices, and client records securely accessible with zero data loss.'
  }
];

export default function PricingPage() {
  const [billingCycle, setBillingCycle] = useState<'monthly' | 'annual'>('annual');
  const [currency, setCurrency] = useState<'INR' | 'USD'>('INR');
  const [isIndia, setIsIndia] = useState(true);
  const [showComparison, setShowComparison] = useState(false);
  const [showFloatingToggle, setShowFloatingToggle] = useState(false);
  const [openFaq, setOpenFaq] = useState<number | null>(null);
  const [appliedCoupon, setAppliedCoupon] = useState<string | null>(null);
  const [activeReferral, setActiveReferral] = useState<string>('');
  const [flashSecondsLeft, setFlashSecondsLeft] = useState(600);

  // Preload Razorpay checkout script
  useRazorpayPreload();

  const topToggleRef = useRef<HTMLDivElement>(null);
  const cardsContainerRef = useRef<HTMLDivElement>(null);

  const handleInitiatePlan = (plan: PlanKey) => {
    if (plan === 'india_only' && !isIndia) {
      if (typeof window !== 'undefined' && (window as any).coraShowToast) {
        (window as any).coraShowToast('India Startup Credits are strictly restricted to Indian residents & billing addresses.', 'error');
      }
      return;
    }
    trackEvent('plan_checkout_clicked', { plan, billing: billingCycle, referral: activeReferral });
    triggerRazorpayCheckout({
      planKey: plan,
      billingCycle,
      appliedCoupon,
      referralCode: activeReferral
    });
  };

  // Dynamic Countdown Timer for 40% Flash Discount
  useEffect(() => {
    if (appliedCoupon === 'INDIA40') {
      const timer = setInterval(() => {
        setFlashSecondsLeft((prev) => (prev > 0 ? prev - 1 : 600));
      }, 1000);
      return () => clearInterval(timer);
    }
  }, [appliedCoupon]);

  const formatFlashTimer = (totalSeconds: number) => {
    const mins = Math.floor(totalSeconds / 60);
    const secs = totalSeconds % 60;
    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
  };

  // Clear any legacy partner lock to prevent unwanted redirects
  useEffect(() => {
    if (typeof window !== 'undefined') {
      try {
        localStorage.removeItem('cora_partner_lock');
      } catch (e) {}
    }
  }, []);

  // Universal Referral Tracking, Dynamic Coupon & URL Parameter Detection + Confetti Blast
  useEffect(() => {
    if (typeof window !== 'undefined') {
      // 1. Detect and track referral
      const tracking = initReferralTracking();
      if (tracking.code) {
        setActiveReferral(tracking.code);
        if (tracking.isTracked) {
          setTimeout(async () => {
            try {
              const confetti = (await import('canvas-confetti')).default;
              confetti({
                particleCount: 110,
                spread: 75,
                origin: { y: 0.58 },
                colors: ['#18181b', '#27272a', '#10b981', '#ffffff']
              });
            } catch (e) {}
          }, 300);
        }
      } else {
        const ref = getActiveReferralCode();
        if (ref) setActiveReferral(ref);
      }

      // 2. Detect coupons
      const params = new URLSearchParams(window.location.search);
      const coupon = params.get('coupon') || params.get('code') || params.get('discount');
      const plan = params.get('plan');

      if (isPartner100Coupon(coupon)) {
        setAppliedCoupon(coupon!.trim());
        setCurrency('INR');
        setIsIndia(true);

        // Trigger celebratory confetti blast for 100% partner pass
        setTimeout(async () => {
          try {
            const confetti = (await import('canvas-confetti')).default;
            confetti({
              particleCount: 160,
              spread: 100,
              origin: { y: 0.55 },
              colors: ['#10b981', '#059669', '#34d399', '#000000', '#ffffff', '#fbbf24']
            });
          } catch (e) {}
        }, 300);

        setTimeout(() => {
          const el = document.getElementById('plan-card-pro');
          if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
        }, 400);
      } else if (coupon?.toUpperCase() === 'INDIA40' || plan === 'india_only') {
        setAppliedCoupon('INDIA40');
        setCurrency('INR');
        setIsIndia(true);

        // Trigger celebratory confetti paper blast
        setTimeout(async () => {
          try {
            const confetti = (await import('canvas-confetti')).default;
            confetti({
              particleCount: 140,
              spread: 90,
              origin: { y: 0.6 },
              colors: ['#f59e0b', '#10b981', '#6366f1', '#ec4899', '#000000', '#fbbf24']
            });
          } catch (e) {}
        }, 300);

        if (plan === 'india_only') {
          setTimeout(() => {
            const el = document.getElementById('india-only-plan');
            if (el) {
              el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
          }, 350);
        }
      } else if (coupon) {
        setAppliedCoupon(coupon.trim().toUpperCase());
        setCurrency('INR');
        setIsIndia(true);
        setTimeout(async () => {
          try {
            const confetti = (await import('canvas-confetti')).default;
            confetti({
              particleCount: 120,
              spread: 80,
              origin: { y: 0.6 },
              colors: ['#10b981', '#6366f1', '#f59e0b', '#000000', '#ffffff']
            });
          } catch (e) {}
        }, 300);
      }
    }
  }, []);

  // Auto-center the middle Professional card on mobile screens
  useEffect(() => {
    if (typeof window !== 'undefined' && window.innerWidth < 768 && cardsContainerRef.current) {
      const timer = setTimeout(() => {
        const container = cardsContainerRef.current;
        if (container && container.children[2]) {
          const proCard = container.children[2] as HTMLElement;
          const scrollPos = proCard.offsetLeft - (window.innerWidth - proCard.offsetWidth) / 2;
          container.scrollTo({ left: Math.max(0, scrollPos), behavior: 'smooth' });
        }
      }, 150);
      return () => clearTimeout(timer);
    }
  }, []);

  // Dynamic Geolocation detection
  useEffect(() => {
    try {
      const tz = Intl.DateTimeFormat().resolvedOptions().timeZone || '';
      const isIndiaTz = tz.includes('Kolkata') || tz.includes('Calcutta') || tz.includes('India');
      if (isIndiaTz) {
        setCurrency('INR');
        setIsIndia(true);
      } else {
        setCurrency('USD');
        setIsIndia(false);
      }

      fetch('https://api.country.is')
        .then(res => res.json())
        .then(data => {
          if (data?.country === 'IN') {
            setCurrency('INR');
            setIsIndia(true);
          } else if (data?.country) {
            setCurrency('USD');
            setIsIndia(false);
          }
        })
        .catch(() => {});
    } catch (e) {
      setCurrency('INR');
      setIsIndia(true);
    }
  }, []);

  // Strict single toggle visibility: ONLY visible within the pricing cards & comparison table
  useEffect(() => {
    const handleScroll = () => {
      if (!topToggleRef.current) return;
      const topRect = topToggleRef.current.getBoundingClientRect();
      
      // Has scrolled past top header toggle
      const scrolledPastTop = topRect.bottom < 0;

      if (!scrolledPastTop) {
        setShowFloatingToggle(false);
        return;
      }

      // Check end boundary (Comparison Table if open, or Cards Grid if closed)
      const compTable = document.getElementById('pricing-comparison-table');
      if (compTable) {
        const compRect = compTable.getBoundingClientRect();
        // Visible while comparison table is in view, hides as soon as scrolled past table
        setShowFloatingToggle(compRect.bottom > 120);
        return;
      }

      if (cardsContainerRef.current) {
        const cardsRect = cardsContainerRef.current.getBoundingClientRect();
        // Visible while pricing cards grid is in view, hides as soon as scrolled past cards
        setShowFloatingToggle(cardsRect.bottom > 120);
        return;
      }

      setShowFloatingToggle(false);
    };

    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();
    return () => window.removeEventListener('scroll', handleScroll);
  }, [showComparison]);

  const toggleFaq = (index: number) => {
    setOpenFaq(openFaq === index ? null : index);
  };

  const handleToggleComparison = (expandOnly = false) => {
    if (expandOnly) {
      setShowComparison(true);
      trackEvent('pricing_comparison_toggle', { open: true });
      setTimeout(() => {
        document.getElementById('pricing-comparison-table')?.scrollIntoView({ behavior: 'smooth' });
      }, 60);
      return;
    }
    const nextState = !showComparison;
    setShowComparison(nextState);
    trackEvent('pricing_comparison_toggle', { open: nextState });
    if (nextState) {
      setTimeout(() => {
        document.getElementById('pricing-comparison-table')?.scrollIntoView({ behavior: 'smooth' });
      }, 60);
    }
  };

  const starterPricing = calculateDiscountedPrice('starter', billingCycle, appliedCoupon, activeReferral);
  const proPricing = calculateDiscountedPrice('pro', billingCycle, appliedCoupon, activeReferral);
  const scalePricing = calculateDiscountedPrice('scale', billingCycle, appliedCoupon, activeReferral);
  const indiaPricing = calculateDiscountedPrice('india_only', 'annual', appliedCoupon, activeReferral);

  return (
    <main className="w-full relative pb-16 sm:pb-24 bg-white text-zinc-900">
      
      {/* ── Ethereal Pure Cloud Sky Hero Section ── */}
      <section className="relative w-full overflow-hidden bg-gradient-to-b from-[#56a2e8] via-[#cae4fc] to-white pt-24 sm:pt-36 pb-10 sm:pb-20">
        
        {/* Background Pure Sky Artwork */}
        <div className="absolute inset-0 z-0 pointer-events-none select-none">
          <Image
            src="/images/cora_pricing_pure_sky.jpg"
            alt="Cora Pure Cloud Sky Background"
            fill
            priority
            className="object-cover object-top"
            sizes="100vw"
          />
          <div
            className="absolute inset-0 pointer-events-none"
            style={{
              background: 'linear-gradient(180deg, rgba(86, 162, 232, 0.12) 0%, rgba(255, 255, 255, 0.20) 40%, rgba(255, 255, 255, 0.88) 80%, #ffffff 100%)',
            }}
          />
          <div className="absolute inset-x-0 bottom-0 h-32 sm:h-52 bg-gradient-to-t from-white via-white/90 to-transparent pointer-events-none" />
        </div>

        {/* Hero Content */}
        <div className="relative z-10 w-full max-w-[1280px] mx-auto px-4 sm:px-6 flex flex-col items-center text-center">
          
          {/* Trust Badges & Action Buttons */}
          <div className="flex flex-wrap items-center justify-center gap-2.5 mb-5">
            <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-white/95 backdrop-blur-md rounded-full text-xs font-mono font-medium text-zinc-800 border border-zinc-200/90 shadow-2xs">
              <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
              <span className="font-semibold text-zinc-950">CORA CLOUD</span>
            </div>

            <button
              type="button"
              onClick={() => {
                const el = document.getElementById('india-only-plan');
                if (el) {
                  el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
              }}
              className="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-gradient-to-r from-amber-50 via-white to-orange-50 hover:from-amber-100 hover:to-orange-100 text-amber-950 rounded-full text-xs font-medium border border-amber-300/80 shadow-2xs hover:shadow-xs transition-all cursor-pointer group select-none backdrop-blur-md"
            >
              <span className="text-xs">🇮🇳</span>
              <span className="font-semibold">Claim Indian Startup Benefits &amp; Credits</span>
              <ArrowRight className="w-3 h-3 text-amber-700 group-hover:translate-x-0.5 transition-transform" />
            </button>
          </div>

          {/* Heading */}
          <div>
            <h1 className="font-display text-4xl sm:text-5xl md:text-6xl font-bold leading-[1.08] tracking-[-0.035em] max-w-[760px] mx-auto mb-2 sm:mb-3 bg-gradient-to-r from-zinc-950 via-zinc-700 to-zinc-400 bg-clip-text text-transparent inline-block">
              Plans that fit your business.
            </h1>
          </div>

          {/* Body */}
          <p className="text-zinc-600 text-sm sm:text-base font-normal leading-relaxed max-w-[580px] mx-auto">
            Start free forever with 500 monthly AI runs. Upgrade with direct 1-step checkout via UPI, Cards, and instant GST invoices.
          </p>

          {/* Primary Cadence Switcher with 2 Months Free badge */}
          <div ref={topToggleRef} className="flex flex-col items-center gap-2 mt-6 shrink-0 select-none">
            <div className="w-fit inline-flex items-center p-1 bg-white/90 backdrop-blur-md rounded-full border border-zinc-200/90 shadow-2xs">
              <button
                type="button"
                onClick={() => {
                  setBillingCycle('monthly');
                  trackEvent('pricing_cycle_change', { cycle: 'monthly' });
                }}
                className={`px-4 sm:px-5 py-2 rounded-full text-xs font-semibold transition-all cursor-pointer outline-none focus:outline-none focus-visible:outline-none focus:ring-0 select-none ${
                  billingCycle === 'monthly'
                    ? 'bg-zinc-950 text-white shadow-xs'
                    : 'text-zinc-600 hover:text-zinc-950'
                }`}
              >
                Monthly Billing
              </button>
              <button
                type="button"
                onClick={() => {
                  setBillingCycle('annual');
                  trackEvent('pricing_cycle_change', { cycle: 'annual' });
                }}
                className={`px-4 sm:px-5 py-2 rounded-full text-xs font-semibold transition-all flex items-center gap-1.5 cursor-pointer outline-none focus:outline-none focus-visible:outline-none focus:ring-0 select-none ${
                  billingCycle === 'annual'
                    ? 'bg-zinc-950 text-white shadow-xs'
                    : 'text-zinc-600 hover:text-zinc-950'
                }`}
              >
                <span>Annual Billing</span>
                <span className="bg-zinc-100 text-zinc-900 border border-zinc-200/80 font-mono text-[9px] sm:text-[10px] font-bold px-2 py-0.5 rounded-full shadow-2xs">
                  Save 20%
                </span>
              </button>
            </div>

            <div className="text-[11px] sm:text-xs text-zinc-500 text-center min-h-[20px] flex items-center justify-center">
              {billingCycle === 'annual' ? (
                <div className="inline-flex items-center gap-1.5 text-emerald-800 font-medium animate-in fade-in duration-150">
                  <Gift className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                  <span>Pay for 10 months, get 12 (Save 2 full months on annual plans)</span>
                </div>
              ) : (
                <div className="text-zinc-500 font-normal animate-in fade-in duration-150">
                  Switch to annual billing to unlock 2 full months free
                </div>
              )}
            </div>
          </div>

        </div>
      </section>

      {/* ── 100% PARTNER PASS CELEBRATION TOP BANNER (OctAffialiate / NovAffialiate) ── */}
      {isPartner100Coupon(appliedCoupon) && (
        <div className="w-full max-w-[1240px] mx-auto px-3.5 sm:px-6 mb-8 -mt-6 relative z-20">
          <div className="rounded-2xl sm:rounded-3xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 text-white p-4 sm:p-5 shadow-2xl border-2 border-emerald-300/60 flex flex-col md:flex-row items-center justify-between gap-4 animate-in fade-in slide-in-from-top-4 duration-300">
            <div className="flex items-center gap-3 text-left">
              <span className="text-3xl sm:text-4xl shrink-0">🎁</span>
              <div>
                <div className="text-sm sm:text-base font-extrabold tracking-tight flex items-center gap-2 flex-wrap">
                  <span>100% EXCLUSIVE PARTNER PASS UNLOCKED!</span>
                  <span className="px-2 py-0.5 rounded-lg bg-black/40 font-mono text-xs border border-white/20 uppercase">
                    CODE: {appliedCoupon}
                  </span>
                </div>
                <div className="text-xs sm:text-sm text-emerald-100 font-medium mt-0.5">
                  You unlocked the full <strong className="text-white font-extrabold underline decoration-white decoration-2">Professional Plan for ₹0 / month</strong> (100% Free Access) with Advanced AI Reasoning &amp; WhatsApp automation.
                </div>
              </div>
            </div>

            <div className="flex items-center gap-2.5 shrink-0 w-full md:w-auto justify-between md:justify-end">
              <button
                type="button"
                onClick={() => handleInitiatePlan('pro')}
                className="px-5 py-2.5 rounded-xl bg-white text-emerald-950 font-extrabold text-xs hover:bg-emerald-50 transition-all shadow-md cursor-pointer shrink-0 flex items-center gap-1.5"
              >
                <span>Claim Professional Free</span>
                <ArrowRight className="w-3.5 h-3.5" />
              </button>
            </div>
          </div>
        </div>
      )}

      {/* ── 40% FLASH DISCOUNT CELEBRATION TOP BANNER ── */}
      {appliedCoupon === 'INDIA40' && (
        <div className="w-full max-w-[1240px] mx-auto px-3.5 sm:px-6 mb-8 -mt-6 relative z-20">
          <div className="rounded-2xl sm:rounded-3xl bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 text-white p-4 sm:p-5 shadow-2xl border-2 border-amber-300/60 flex flex-col md:flex-row items-center justify-between gap-4 animate-in fade-in slide-in-from-top-4 duration-300">
            <div className="flex items-center gap-3 text-left">
              <span className="text-3xl sm:text-4xl shrink-0">🎉</span>
              <div>
                <div className="text-sm sm:text-base font-extrabold tracking-tight flex items-center gap-2 flex-wrap">
                  <span>40% FLASH DISCOUNT UNLOCKED!</span>
                  <span className="px-2 py-0.5 rounded-lg bg-black/40 font-mono text-xs border border-white/20">
                    CODE: INDIA40
                  </span>
                </div>
                <div className="text-xs sm:text-sm text-amber-100 font-medium mt-0.5">
                  You unlocked the India Only Plan for <strong className="text-white font-extrabold underline decoration-white decoration-2">₹299/mo</strong> (Reg. ₹499) &bull; Save ₹2,400/yr
                </div>
              </div>
            </div>

            <div className="flex items-center gap-2.5 shrink-0 w-full md:w-auto justify-between md:justify-end">
              <div className="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-black/50 backdrop-blur-md border border-white/20 font-mono text-xs font-bold text-amber-200">
                <Clock className="w-3.5 h-3.5 text-amber-300" />
                <span>Expires in: {formatFlashTimer(flashSecondsLeft)}</span>
              </div>
              <button
                type="button"
                onClick={() => handleInitiatePlan('india_only')}
                className="px-4 py-2 rounded-xl bg-white text-zinc-950 font-extrabold text-xs hover:bg-amber-50 transition-all shadow-md cursor-pointer shrink-0"
              >
                Claim Deal ↓
              </button>
            </div>
          </div>
        </div>
      )}

      {/* ── ACTIVE REFERRAL DISCOUNT CELEBRATION TOP BANNER ── */}
      {activeReferral && !isPartner100Coupon(appliedCoupon) && appliedCoupon !== 'INDIA40' && (
        <div className="w-full max-w-[1240px] mx-auto px-3.5 sm:px-6 mb-8 -mt-6 relative z-20">
          <div className="rounded-2xl sm:rounded-3xl bg-zinc-950 text-white p-4 sm:p-5 shadow-2xl border border-zinc-800 flex flex-col md:flex-row items-center justify-between gap-4 animate-in fade-in slide-in-from-top-4 duration-300">
            <div className="flex items-center gap-3 text-left">
              <span className="text-3xl sm:text-4xl shrink-0">⚡</span>
              <div>
                <div className="text-sm sm:text-base font-extrabold tracking-tight flex items-center gap-2 flex-wrap">
                  <span>10% REFERRAL DISCOUNT ACTIVE!</span>
                  <span className="px-2.5 py-0.5 rounded-lg bg-zinc-800 font-mono text-xs border border-zinc-700 uppercase text-zinc-200">
                    PARTNER: @{activeReferral}
                  </span>
                </div>
                <div className="text-xs sm:text-sm text-zinc-300 font-medium mt-0.5">
                  10% Referral Discount pre-applied across all plans at checkout on top of annual savings!
                </div>
              </div>
            </div>

            <div className="flex items-center gap-2.5 shrink-0 w-full md:w-auto justify-between md:justify-end">
              <span className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-zinc-900 border border-zinc-700 font-mono text-xs font-bold text-emerald-400">
                <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" />
                <span>10% Off Pre-Applied</span>
              </span>
            </div>
          </div>
        </div>
      )}

      {/* ── CUSTOM PROMO COUPON CELEBRATION TOP BANNER ── */}
      {appliedCoupon && !isPartner100Coupon(appliedCoupon) && appliedCoupon !== 'INDIA40' && (
        <div className="w-full max-w-[1240px] mx-auto px-3.5 sm:px-6 mb-8 -mt-6 relative z-20">
          <div className="rounded-2xl sm:rounded-3xl bg-gradient-to-r from-emerald-600 via-teal-600 to-indigo-700 text-white p-4 sm:p-5 shadow-2xl border-2 border-emerald-300/60 flex flex-col md:flex-row items-center justify-between gap-4 animate-in fade-in slide-in-from-top-4 duration-300">
            <div className="flex items-center gap-3 text-left">
              <span className="text-3xl sm:text-4xl shrink-0">🎁</span>
              <div>
                <div className="text-sm sm:text-base font-extrabold tracking-tight flex items-center gap-2 flex-wrap">
                  <span>DISCOUNT COUPON ACTIVE!</span>
                  <span className="px-2 py-0.5 rounded-lg bg-black/40 font-mono text-xs border border-white/20 uppercase">
                    CODE: {appliedCoupon}
                  </span>
                </div>
                <div className="text-xs sm:text-sm text-emerald-100 font-medium mt-0.5">
                  Coupon discount is auto-applied at checkout on all plans on top of annual savings.
                </div>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* ══════════════════════════════════════════════════════════════════════
          PRIMARY 4-COLUMN PRICING MATRIX WITH 2 MONTHS FREE ON ANNUAL
      ══════════════════════════════════════════════════════════════════════ */}
      <section className="w-full max-w-[1380px] mx-auto px-4 sm:px-6 mb-12 sm:mb-16 -mt-2 sm:-mt-4 relative z-10">

        {/* ══════════════════════════════════════════════════════════════════════
            4-COLUMN PRICING GRID: FREE -> STARTER -> PROFESSIONAL -> SCALE
        ══════════════════════════════════════════════════════════════════════ */}
        <div 
          ref={cardsContainerRef}
          className="w-full grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 items-stretch gap-6 py-6 sm:py-8"
        >
          
          {/* ── CARD 1: FREE FOREVER (COLUMN 1) ── */}
          <div className="w-full bg-white border border-zinc-200/90 rounded-2xl sm:rounded-[22px] overflow-hidden flex flex-col h-full shadow-[0_2px_10px_rgba(0,0,0,0.03)] hover:shadow-lg hover:border-zinc-300 transition-all">
            {/* Top Header Banner in Dark Charcoal */}
            <div className="bg-[#18181b] h-[160px] p-5 text-white flex flex-col justify-between relative shrink-0">
              <div className="flex items-center justify-between gap-2 h-9">
                <div className="w-9 h-9 rounded-xl bg-zinc-800 text-zinc-100 shadow-xs flex items-center justify-center shrink-0 border border-zinc-700">
                  <Leaf className="w-4 h-4 text-emerald-400" />
                </div>
                <span className="inline-flex items-center px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-mono font-bold tracking-wide border border-emerald-500/30">
                  FREE
                </span>
              </div>
              <div className="space-y-1">
                <h3 className="font-display text-xl font-bold text-white tracking-tight leading-tight">
                  Free Forever
                </h3>
                <p className="text-xs text-zinc-300 leading-relaxed line-clamp-2 h-[36px] flex items-start">
                  Run your CRM, create invoices, and test AI agents.
                </p>
              </div>
            </div>

            {/* Card Body */}
            <div className="p-5 sm:p-5.5 flex-1 flex flex-col justify-between">
              <div>
                {/* Price Block */}
                <div className="h-[68px] mb-4 flex flex-col justify-center shrink-0">
                  <div className="h-8 flex items-baseline gap-1.5">
                    <span className="text-3xl sm:text-[34px] font-display font-bold text-zinc-950 tracking-tight leading-none">
                      ₹0
                    </span>
                    <span className="text-xs text-zinc-500 font-medium">/ mo</span>
                  </div>
                  <div className="text-[11px] text-zinc-500 mt-1.5 flex items-center gap-1.5 h-4">
                    <span className="text-emerald-700 font-semibold">Free forever &bull; No card needed</span>
                  </div>
                </div>

                {/* Direct 1-Step Fast Checkout Trigger */}
                <button
                  type="button"
                  onClick={() => handleInitiatePlan('free')}
                  className="h-11 w-full inline-flex items-center justify-center gap-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 px-4 rounded-xl text-xs font-bold transition-all mb-5 cursor-pointer shrink-0 shadow-2xs group"
                >
                  <span>Start Free Forever</span>
                  <ArrowRight className="w-3.5 h-3.5 text-zinc-500 group-hover:translate-x-0.5 transition-transform" />
                </button>

                {/* Features List */}
                <div className="pt-3 border-t border-zinc-100">
                  <div className="mb-3">
                    <span className="inline-block px-2.5 py-0.5 rounded-md bg-zinc-100 text-zinc-800 text-[10px] font-mono font-bold uppercase tracking-wider">
                      Core Features Included
                    </span>
                  </div>
                  <ul className="space-y-2.5 text-xs">
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
              </div>

              {/* In-Card Compare Toggle */}
              <div className="mt-6 pt-4 border-t border-zinc-100">
                <button
                  type="button"
                  onClick={() => handleToggleComparison()}
                  className="w-full py-2.5 px-3 rounded-xl bg-zinc-50 hover:bg-zinc-100 border border-zinc-200/80 text-xs font-semibold text-zinc-800 flex items-center justify-center gap-1.5 transition-all group/cmp cursor-pointer"
                >
                  <span>{showComparison ? 'Hide Comparison' : 'Compare All Features'}</span>
                  {showComparison ? (
                    <ChevronUp className="w-3.5 h-3.5 text-zinc-500 group-hover/cmp:text-zinc-900 transition-transform" />
                  ) : (
                    <ChevronDown className="w-3.5 h-3.5 text-zinc-500 group-hover/cmp:text-zinc-900 transition-transform" />
                  )}
                </button>
              </div>
            </div>
          </div>

          {/* ── CARD 2: STARTER (COLUMN 2) ── */}
          <div className="w-full bg-white border border-zinc-200/90 rounded-2xl sm:rounded-[22px] overflow-hidden flex flex-col h-full shadow-[0_2px_10px_rgba(0,0,0,0.03)] hover:shadow-lg hover:border-zinc-300 transition-all">
            {/* Top Header Banner in Vibrant Emerald Green */}
            <div className="bg-[#0b7a4d] h-[160px] p-5 text-white flex flex-col justify-between relative shrink-0">
              <div className="flex items-center justify-between gap-2 h-9">
                <div className="w-9 h-9 rounded-xl bg-[#a7f3d0] text-[#065f46] shadow-xs flex items-center justify-center shrink-0">
                  <Rocket className="w-4 h-4" />
                </div>
              </div>
              <div className="space-y-1">
                <div className="flex items-center gap-2 flex-wrap">
                  <h3 className="font-display text-xl font-bold text-white tracking-tight leading-tight">
                    Starter
                  </h3>
                  {billingCycle === 'annual' && (
                    <span className="inline-flex items-center px-2 py-0.5 rounded-full bg-white/20 text-white text-[10px] font-mono font-bold tracking-tight border border-white/30 backdrop-blur-xs">
                      2 MONTHS FREE
                    </span>
                  )}
                  {starterPricing.isReferralDiscount && (
                    <span className="inline-flex items-center px-2 py-0.5 rounded-full bg-white text-emerald-950 text-[9.5px] font-mono font-bold tracking-tight">
                      10% REF OFF
                    </span>
                  )}
                </div>
                <p className="text-xs text-emerald-50/90 leading-relaxed line-clamp-2 h-[36px] flex items-start">
                  Connect custom domains, send branded emails, and invite 2 teammates.
                </p>
              </div>
            </div>

            {/* Card Body */}
            <div className="p-5 sm:p-5.5 flex-1 flex flex-col justify-between">
              <div>
                {/* Price Block */}
                <div className="mb-4 flex flex-col justify-center shrink-0">
                  <div className="h-8 flex items-baseline gap-1.5 flex-wrap">
                    <span className="text-3xl sm:text-[34px] font-display font-bold text-zinc-950 tracking-tight leading-none">
                      {currency === 'INR'
                        ? `₹${starterPricing.effectiveMonthlyRate.toLocaleString('en-IN')}`
                        : (billingCycle === 'annual' ? '$7.50' : '$9')}
                    </span>
                    <span className="text-xs text-zinc-500 font-medium">/ mo</span>
                    {(starterPricing.discountPercentage > 0 || billingCycle === 'annual') && (
                      <span className="text-xs text-zinc-400 line-through ml-1 font-mono">
                        {currency === 'INR' ? (billingCycle === 'annual' ? '₹833' : '₹999') : '$9'}
                      </span>
                    )}
                  </div>
                  <div className="text-[11px] text-zinc-500 mt-1.5 flex flex-col gap-0.5">
                    {billingCycle === 'annual' ? (
                      <>
                        <div className="flex items-center gap-1.5 font-medium text-emerald-700">
                          <span>Billed annually: {currency === 'INR' ? `₹${starterPricing.grandTotal.toLocaleString('en-IN')} / yr` : '$90 / yr'}</span>
                          <span className="text-zinc-300">&bull;</span>
                          <span className="text-emerald-800 font-bold">
                            {currency === 'INR' 
                              ? `Save ₹${(starterPricing.standardFullRate - starterPricing.grandTotal).toLocaleString('en-IN')}` 
                              : 'Save $18'}
                          </span>
                        </div>
                        <div className="text-[10px] text-zinc-400">
                          {currency === 'INR' ? 'Includes 18% GST (12 mos. for the price of 10)' : '12 months access for price of 10'}
                        </div>
                      </>
                    ) : (
                      <div className="text-zinc-500 h-4 flex items-center">Billed monthly &bull; Incl. 18% GST</div>
                    )}
                  </div>
                </div>

                {/* Direct 1-Step Fast Checkout Trigger */}
                <button
                  type="button"
                  onClick={() => handleInitiatePlan('starter')}
                  className="h-11 w-full inline-flex items-center justify-center gap-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 px-4 rounded-xl text-xs font-bold transition-all mb-5 cursor-pointer shrink-0 group"
                >
                  <span>{billingCycle === 'annual' ? 'Get Started (Annual)' : 'Get Started'}</span>
                  <ArrowRight className="w-3.5 h-3.5 text-zinc-500 group-hover:translate-x-0.5 transition-transform" />
                </button>

                {/* Features List */}
                <div className="pt-3 border-t border-zinc-100">
                  <div className="mb-3">
                    <span className="inline-block px-2.5 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200/60 text-[10px] font-mono font-bold uppercase tracking-wider">
                      Everything in Free, plus:
                    </span>
                  </div>
                  <ul className="space-y-2.5 text-xs">
                    <PricingFeature 
                      icon={Sparkles} 
                      iconClass="text-amber-500" 
                    >
                      <span className="font-semibold text-zinc-900">{billingCycle === 'annual' ? '6,000' : '5,000'}</span> CORA AI runs / mo
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
                    {billingCycle === 'annual' && (
                      <PricingFeature 
                        icon={Gift} 
                        iconClass="text-emerald-600" 
                        textColor="text-emerald-800 font-medium"
                      >
                        2 Months Free Included
                      </PricingFeature>
                    )}
                  </ul>
                </div>
              </div>

              {/* In-Card Compare Toggle */}
              <div className="mt-6 pt-4 border-t border-zinc-100">
                <button
                  type="button"
                  onClick={() => handleToggleComparison()}
                  className="w-full py-2.5 px-3 rounded-xl bg-zinc-50 hover:bg-zinc-100 border border-zinc-200/80 text-xs font-semibold text-zinc-800 flex items-center justify-center gap-1.5 transition-all group/cmp cursor-pointer"
                >
                  <span>{showComparison ? 'Hide Comparison' : 'Compare All Features'}</span>
                  {showComparison ? (
                    <ChevronUp className="w-3.5 h-3.5 text-zinc-500 group-hover/cmp:text-zinc-900 transition-transform" />
                  ) : (
                    <ChevronDown className="w-3.5 h-3.5 text-zinc-500 group-hover/cmp:text-zinc-900 transition-transform" />
                  )}
                </button>
              </div>
            </div>
          </div>

          {/* ── CARD 3: PROFESSIONAL (RECOMMENDED - COLUMN 3) ── */}
          <div 
            id="plan-card-pro"
            className={`w-full bg-white rounded-2xl sm:rounded-[22px] flex flex-col h-full transition-all relative z-20 overflow-visible ${
              isPartner100Coupon(appliedCoupon)
                ? 'border-2 border-emerald-500 shadow-2xl ring-4 ring-emerald-400/20'
                : 'border-2 border-[#be006b] shadow-[0_12px_36px_rgba(190,0,107,0.18)] hover:shadow-2xl'
            }`}
          >
            {/* Floating Highlighted SaaS Badge - Positioned OUTSIDE the frame at the top */}
            <div className="absolute -top-3.5 left-1/2 -translate-x-1/2 z-30 whitespace-nowrap select-none pointer-events-none">
              <div className={`inline-flex items-center gap-1.5 px-4 py-1 rounded-full text-[11px] font-mono font-bold tracking-wider uppercase shadow-md border ${
                isPartner100Coupon(appliedCoupon)
                  ? 'bg-emerald-500 text-zinc-950 border-emerald-300 ring-2 ring-emerald-400/30 animate-pulse'
                  : 'bg-gradient-to-r from-[#9d0057] via-[#be006b] to-[#9d0057] text-white border-pink-300/50 ring-2 ring-pink-500/20 shadow-[0_4px_14px_rgba(190,0,107,0.35)]'
              }`}>
                <Sparkles className="w-3.5 h-3.5 text-pink-200 fill-pink-200/60 shrink-0" />
                <span>{isPartner100Coupon(appliedCoupon) ? '100% OFF PARTNER PASS' : 'RECOMMENDED'}</span>
              </div>
            </div>

            {/* Top Header Banner in Vibrant Magenta / Fuchsia or Emerald for 100% partner pass */}
            <div 
              className={`h-[160px] p-5 text-white flex flex-col justify-between relative shrink-0 rounded-t-[14px] sm:rounded-t-[20px] overflow-hidden ${
                isPartner100Coupon(appliedCoupon) ? 'bg-gradient-to-br from-emerald-700 via-teal-700 to-emerald-800' : 'bg-[#be006b]'
              }`}
            >
              <div className="flex items-center justify-between gap-2 h-9">
                <div className={`w-9 h-9 rounded-xl shadow-xs flex items-center justify-center shrink-0 ${
                  isPartner100Coupon(appliedCoupon) ? 'bg-emerald-300 text-emerald-950 font-bold' : 'bg-[#fbcfe8] text-[#9d174d]'
                }`}>
                  <Sparkles className="w-4 h-4" />
                </div>
              </div>
              <div className="space-y-1">
                <div className="flex items-center gap-2 flex-wrap">
                  <h3 className="font-display text-xl font-bold text-white tracking-tight leading-tight">
                    Professional
                  </h3>
                  {!isPartner100Coupon(appliedCoupon) && billingCycle === 'annual' && (
                    <span className="inline-flex items-center px-2 py-0.5 rounded-full bg-white/20 text-white text-[10px] font-mono font-bold tracking-tight border border-white/30 backdrop-blur-xs">
                      2 MONTHS FREE
                    </span>
                  )}
                  {proPricing.isReferralDiscount && (
                    <span className="inline-flex items-center px-2 py-0.5 rounded-full bg-white text-pink-950 text-[9.5px] font-mono font-bold tracking-tight">
                      10% REF OFF
                    </span>
                  )}
                </div>
                <p className="text-xs text-pink-50/90 leading-relaxed line-clamp-2 h-[36px] flex items-start">
                  Automate WhatsApp updates, collect payments with UPI QR, and deploy reasoning AI.
                </p>
              </div>
            </div>

            {/* Card Body */}
            <div className="p-5 sm:p-5.5 flex-1 flex flex-col justify-between">
              <div>
                {/* Price Block */}
                <div className="mb-4 flex flex-col justify-center shrink-0">
                  <div className="h-8 flex items-baseline gap-1.5 flex-wrap">
                    <span className="text-3xl sm:text-[34px] font-display font-bold text-zinc-950 tracking-tight leading-none">
                      {isPartner100Coupon(appliedCoupon)
                        ? '₹0'
                        : (currency === 'INR'
                            ? `₹${proPricing.effectiveMonthlyRate.toLocaleString('en-IN')}`
                            : (billingCycle === 'annual' ? '$20' : '$24'))}
                    </span>
                    <span className="text-xs text-zinc-500 font-medium">/ mo</span>
                    {(isPartner100Coupon(appliedCoupon) || proPricing.discountPercentage > 0 || billingCycle === 'annual') && (
                      <span className="text-xs text-zinc-400 line-through ml-1 font-mono">
                        {isPartner100Coupon(appliedCoupon)
                          ? (currency === 'INR' ? '₹2,499' : '$24')
                          : (currency === 'INR' ? (billingCycle === 'annual' ? '₹2,083' : '₹2,499') : '$24')}
                      </span>
                    )}
                  </div>
                  <div className="text-[11px] text-zinc-500 mt-1.5 flex flex-col gap-0.5">
                    {isPartner100Coupon(appliedCoupon) ? (
                      <span className="text-emerald-700 font-bold">100% Partner Discount Applied • ₹0 Forever</span>
                    ) : (billingCycle === 'annual' ? (
                      <>
                        <div className="flex items-center gap-1.5 font-medium text-emerald-700">
                          <span>Billed annually: {currency === 'INR' ? `₹${proPricing.grandTotal.toLocaleString('en-IN')} / yr` : '$240 / yr'}</span>
                          <span className="text-zinc-300">&bull;</span>
                          <span className="text-emerald-800 font-bold">
                            {currency === 'INR' 
                              ? `Save ₹${(proPricing.standardFullRate - proPricing.grandTotal).toLocaleString('en-IN')}` 
                              : 'Save $48'}
                          </span>
                        </div>
                        <div className="text-[10px] text-zinc-400">
                          {currency === 'INR' ? 'Includes 18% GST (12 mos. for the price of 10)' : '12 months access for price of 10'}
                        </div>
                      </>
                    ) : (
                      <div className="text-zinc-500 h-4 flex items-center">Billed monthly &bull; Incl. 18% GST</div>
                    ))}
                  </div>
                </div>

                {/* Direct 1-Step Fast Checkout Trigger */}
                <button
                  type="button"
                  onClick={() => handleInitiatePlan('pro')}
                  className={`h-11 w-full inline-flex items-center justify-center gap-2 px-4 rounded-xl text-xs font-bold transition-all shadow-sm hover:shadow-md mb-5 cursor-pointer shrink-0 group ${
                    isPartner100Coupon(appliedCoupon)
                      ? 'bg-emerald-600 hover:bg-emerald-500 text-white ring-2 ring-emerald-400/50'
                      : 'bg-zinc-950 hover:bg-zinc-800 text-white'
                  }`}
                >
                  <span>
                    {isPartner100Coupon(appliedCoupon)
                      ? 'Claim 100% Free Professional Plan'
                      : (billingCycle === 'annual' ? 'Get Started (Annual)' : 'Get Started')}
                  </span>
                  <ArrowRight className="w-3.5 h-3.5 text-zinc-300 group-hover:translate-x-0.5 group-hover:text-white transition-transform" />
                </button>

                {/* Features List */}
                <div className="pt-3 border-t border-zinc-100">
                  <div className="mb-3">
                    <span className="inline-block px-2.5 py-0.5 rounded-md bg-pink-50 text-pink-900 border border-pink-200/60 text-[10px] font-mono font-bold uppercase tracking-wider">
                      Everything in Starter, plus:
                    </span>
                  </div>
                  <ul className="space-y-2.5 text-xs">
                    <PricingFeature 
                      icon={Sparkles} 
                      iconClass="text-blue-600 stroke-[2.2]" 
                      highlight
                      textColor="text-zinc-900 font-medium"
                    >
                      <span className="font-semibold text-zinc-900">Advanced AI Reasoning Engine (CORA AI)</span>
                    </PricingFeature>
                    <PricingFeature 
                      icon={Sparkles} 
                      iconClass="text-amber-500" 
                    >
                      <span className="font-semibold text-zinc-900">14,000</span> CORA AI runs / mo
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
                      <span className="font-semibold text-zinc-900">50 GB</span> bandwidth &bull; <span className="font-semibold text-zinc-900">15 GB</span> storage
                    </PricingFeature>
                    <PricingFeature 
                      icon={Split} 
                      iconClass="text-pink-600 stroke-[2.2]" 
                      highlight
                      textColor="text-zinc-900 font-medium"
                    >
                      A/B testing &amp; optimization
                    </PricingFeature>
                    <PricingFeature 
                      icon={Search} 
                      iconClass="text-pink-600 stroke-[2.2]" 
                      highlight
                      textColor="text-zinc-900 font-medium"
                    >
                      AI site search &amp; vector indexing
                    </PricingFeature>
                    <PricingFeature 
                      icon={UploadCloud} 
                      iconClass="text-zinc-900" 
                    >
                      Form file &amp; media uploads
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
                      icon={WhatsAppIcon} 
                      iconClass="text-[#25D366]" 
                      highlight
                    >
                      Official WhatsApp automated dispatch*
                    </PricingFeature>
                    <PricingFeature 
                      icon={Headphones} 
                      iconClass="text-pink-600 stroke-[2.2]" 
                      highlight
                      textColor="text-zinc-900 font-medium"
                    >
                      Priority support desk
                    </PricingFeature>
                    <PricingFeature 
                      icon={Users} 
                      iconClass="text-zinc-900" 
                    >
                      Up to 5 team workspace seats &amp; roles
                    </PricingFeature>
                    {billingCycle === 'annual' && (
                      <PricingFeature 
                        icon={Gift} 
                        iconClass="text-emerald-600" 
                        textColor="text-emerald-800 font-medium"
                      >
                        2 Months Free Included
                      </PricingFeature>
                    )}
                  </ul>
                </div>
              </div>

              {/* In-Card Compare Toggle */}
              <div className="mt-6 pt-4 border-t border-zinc-100">
                <button
                  type="button"
                  onClick={() => handleToggleComparison()}
                  className="w-full py-2.5 px-3 rounded-xl bg-pink-50/70 hover:bg-pink-100/80 border border-pink-200/80 text-xs font-bold text-[#be006b] flex items-center justify-center gap-1.5 transition-all group/cmp cursor-pointer shadow-2xs"
                >
                  <span>{showComparison ? 'Hide Comparison' : 'Compare All Features'}</span>
                  {showComparison ? (
                    <ChevronUp className="w-3.5 h-3.5 text-[#be006b] group-hover/cmp:scale-110 transition-transform" />
                  ) : (
                    <ChevronDown className="w-3.5 h-3.5 text-[#be006b] group-hover/cmp:scale-110 transition-transform" />
                  )}
                </button>
              </div>
            </div>
          </div>

          {/* ── CARD 4: SCALE (COLUMN 4) ── */}
          <div className="w-full bg-white border border-zinc-200/90 rounded-2xl sm:rounded-[22px] flex flex-col h-full shadow-[0_2px_10px_rgba(0,0,0,0.03)] hover:shadow-lg hover:border-zinc-300 transition-all relative z-20 overflow-visible">
            {/* Floating Highlighted SaaS Badge - Positioned OUTSIDE the frame at the top */}
            <div className="absolute -top-3.5 left-1/2 -translate-x-1/2 z-30 whitespace-nowrap select-none pointer-events-none">
              <div className="inline-flex items-center gap-1.5 px-4 py-1 rounded-full text-[11px] font-mono font-bold tracking-wider uppercase shadow-md border bg-gradient-to-r from-[#432bb3] via-[#5438dc] to-[#432bb3] text-white border-purple-300/50 ring-2 ring-purple-500/20 shadow-[0_4px_14px_rgba(84,56,220,0.35)]">
                <Gift className="w-3.5 h-3.5 text-purple-200 shrink-0" />
                <span>FREE SITE MIGRATION</span>
              </div>
            </div>

            {/* Top Header Banner in Vibrant Royal Violet / Indigo */}
            <div className="bg-[#5438dc] h-[160px] p-5 text-white flex flex-col justify-between relative shrink-0 rounded-t-[14px] sm:rounded-t-[20px] overflow-hidden">
              <div className="flex items-center justify-between gap-2 h-9">
                <div className="w-9 h-9 rounded-xl bg-[#ddd6fe] text-[#4c1d95] shadow-xs flex items-center justify-center shrink-0">
                  <TrendingUp className="w-4 h-4" />
                </div>
              </div>
              <div className="space-y-1">
                <div className="flex items-center gap-2 flex-wrap">
                  <h3 className="font-display text-xl font-bold text-white tracking-tight leading-tight">
                    Scale
                  </h3>
                  {billingCycle === 'annual' && (
                    <span className="inline-flex items-center px-2 py-0.5 rounded-full bg-white/20 text-white text-[10px] font-mono font-bold tracking-tight border border-white/30 backdrop-blur-xs">
                      2 MONTHS FREE
                    </span>
                  )}
                  {scalePricing.isReferralDiscount && (
                    <span className="inline-flex items-center px-2 py-0.5 rounded-full bg-white text-purple-950 text-[9.5px] font-mono font-bold tracking-tight">
                      10% REF OFF
                    </span>
                  )}
                </div>
                <p className="text-xs text-indigo-50/90 leading-relaxed line-clamp-2 h-[36px] flex items-start">
                  Scale your agency with unlimited seats, custom APIs, and free site migration.
                </p>
              </div>
            </div>

            {/* Card Body */}
            <div className="p-5 sm:p-5.5 flex-1 flex flex-col justify-between">
              <div>
                {/* Price Block */}
                <div className="mb-4 flex flex-col justify-center shrink-0">
                  <div className="h-8 flex items-baseline gap-1.5 flex-wrap">
                    <span className="text-3xl sm:text-[34px] font-display font-bold text-zinc-950 tracking-tight leading-none">
                      {currency === 'INR'
                        ? `₹${scalePricing.effectiveMonthlyRate.toLocaleString('en-IN')}`
                        : (billingCycle === 'annual' ? '$37.50' : '$45')}
                    </span>
                    <span className="text-xs text-zinc-500 font-medium">/ mo</span>
                    {(scalePricing.discountPercentage > 0 || billingCycle === 'annual') && (
                      <span className="text-xs text-zinc-400 line-through ml-1 font-mono">
                        {currency === 'INR' ? (billingCycle === 'annual' ? '₹3,749' : '₹4,499') : '$45'}
                      </span>
                    )}
                  </div>
                  <div className="text-[11px] text-zinc-500 mt-1.5 flex flex-col gap-0.5">
                    {billingCycle === 'annual' ? (
                      <>
                        <div className="flex items-center gap-1.5 font-medium text-emerald-700">
                          <span>Billed annually: {currency === 'INR' ? `₹${scalePricing.grandTotal.toLocaleString('en-IN')} / yr` : '$450 / yr'}</span>
                          <span className="text-zinc-300">&bull;</span>
                          <span className="text-emerald-800 font-bold">
                            {currency === 'INR' 
                              ? `Save ₹${(scalePricing.standardFullRate - scalePricing.grandTotal).toLocaleString('en-IN')}` 
                              : 'Save $90'}
                          </span>
                        </div>
                        <div className="text-[10px] text-zinc-400">
                          {currency === 'INR' ? 'Includes 18% GST (12 mos. for the price of 10)' : '12 months access for price of 10'}
                        </div>
                      </>
                    ) : (
                      <div className="text-zinc-500 h-4 flex items-center">Billed monthly &bull; Incl. 18% GST</div>
                    )}
                  </div>
                </div>

                {/* Direct 1-Step Fast Checkout Trigger */}
                <button
                  type="button"
                  onClick={() => handleInitiatePlan('scale')}
                  className="h-11 w-full inline-flex items-center justify-center gap-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 px-4 rounded-xl text-xs font-bold transition-all mb-5 cursor-pointer shrink-0 group"
                >
                  <span>{billingCycle === 'annual' ? 'Get Started (Annual)' : 'Get Started'}</span>
                  <ArrowRight className="w-3.5 h-3.5 text-zinc-500 group-hover:translate-x-0.5 transition-transform" />
                </button>

                {/* Features List */}
                <div className="pt-3 border-t border-zinc-100">
                  <div className="mb-3">
                    <span className="inline-block px-2.5 py-0.5 rounded-md bg-purple-50 text-purple-900 border border-purple-200/60 text-[10px] font-mono font-bold uppercase tracking-wider">
                      Everything in Professional, plus:
                    </span>
                  </div>
                  <ul className="space-y-2.5 text-xs">
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
                    {billingCycle === 'annual' && (
                      <PricingFeature 
                        icon={Gift} 
                        iconClass="text-emerald-600" 
                        textColor="text-emerald-800 font-medium"
                      >
                        2 Months Free Included
                      </PricingFeature>
                    )}
                  </ul>
                </div>
              </div>

              {/* In-Card Compare Toggle */}
              <div className="mt-6 pt-4 border-t border-zinc-100">
                <button
                  type="button"
                  onClick={() => handleToggleComparison()}
                  className="w-full py-2.5 px-3 rounded-xl bg-zinc-50 hover:bg-zinc-100 border border-zinc-200/80 text-xs font-semibold text-zinc-800 flex items-center justify-center gap-1.5 transition-all group/cmp cursor-pointer"
                >
                  <span>{showComparison ? 'Hide Comparison' : 'Compare All Features'}</span>
                  {showComparison ? (
                    <ChevronUp className="w-3.5 h-3.5 text-zinc-500 group-hover/cmp:text-zinc-900 transition-transform" />
                  ) : (
                    <ChevronDown className="w-3.5 h-3.5 text-zinc-500 group-hover/cmp:text-zinc-900 transition-transform" />
                  )}
                </button>
              </div>
            </div>
          </div>

        </div>


        {/* Mobile Swipe Hint */}
        <div className="flex md:hidden items-center justify-center gap-2 mt-4 text-[11px] text-zinc-400">
          <span className="inline-block w-1.5 h-1.5 rounded-full bg-zinc-300"></span>
          <span className="inline-block w-1.5 h-1.5 rounded-full bg-zinc-300"></span>
          <span className="inline-block w-3.5 h-1.5 rounded-full bg-[#be006b]"></span>
          <span className="inline-block w-1.5 h-1.5 rounded-full bg-zinc-300"></span>
          <span className="ml-1 text-zinc-500 font-medium">Swipe to explore all 4 plans &rarr;</span>
        </div>

      </section>

      {/* ══════════════════════════════════════════════════════════════════════
          ACCORDION COMPARISON MATRIX (FULL-WIDTH EXPANDED VIEW)
      ══════════════════════════════════════════════════════════════════════ */}
      {showComparison && (
        <section id="pricing-comparison-table" className="w-full max-w-[1380px] mx-auto px-3 sm:px-6 mb-12 sm:mb-16">
          <div className="w-full bg-white rounded-2xl sm:rounded-[32px] p-2 sm:p-5 animate-in fade-in slide-in-from-top-3 duration-200">
            
            {/* Top Heading Block */}
            <div className="max-w-[760px] mx-auto text-center mb-8 sm:mb-10 pt-2 flex flex-col items-center relative z-10">
              <div className="inline-flex items-center gap-1.5 px-3 py-1 bg-zinc-100 rounded-full text-zinc-900 text-[11px] font-semibold uppercase tracking-wider mb-3 border border-zinc-200/80 shadow-2xs">
                <Sparkles className="w-3.5 h-3.5 text-zinc-900 stroke-[1.8]" />
                <span>Feature Comparison</span>
              </div>
              <h3 className="w-full font-display text-2xl xs:text-3xl sm:text-4xl lg:text-[42px] font-bold leading-[1.15] tracking-tight text-zinc-950 mb-2 sm:mb-3">
                Compare plans side by side.
              </h3>
              <p className="text-zinc-600 text-xs sm:text-sm md:text-base font-normal leading-relaxed max-w-[620px] mx-auto">
                See what&apos;s included in every tier—from AI run allocations and custom domains to client workflows and automated GST invoicing.
              </p>
            </div>

            {/* Responsive Table with ROCK-SOLID STICKY HEADER CARDS */}
            <div className="w-full relative pb-2 overflow-x-auto">
              <table className="w-full table-fixed text-left border-separate border-spacing-0 text-xs min-w-[620px] sm:min-w-[860px]">
                
                <colgroup>
                  <col className="w-[145px] sm:w-[28%]" />
                  <col className="w-[118px] sm:w-[18%]" />
                  <col className="w-[118px] sm:w-[18%]" />
                  <col className="w-[118px] sm:w-[18%]" />
                  <col className="w-[118px] sm:w-[18%]" />
                </colgroup>

                {/* ── Sticky Header with Direct Fast Checkout In-Card CTAs ── */}
                <thead className="sticky top-0 sm:top-[70px] z-30 bg-white shadow-xs">
                  <tr className="align-top">
                    
                    {/* Left title column with Inline Cadence Switcher (Sticky top-left) */}
                    <th className="sticky left-0 z-40 p-1 sm:p-2.5 align-top bg-white border-b border-zinc-200/80 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="h-[145px] sm:h-[215px] p-2 sm:p-3.5 flex flex-col justify-between bg-zinc-900 text-white rounded-xl sm:rounded-2xl shadow-xs">
                        <div>
                          <div className="inline-flex items-center gap-1 px-1.5 py-0.5 sm:px-2.5 sm:py-0.5 rounded-full bg-zinc-800 text-zinc-300 text-[8px] sm:text-[9.5px] font-mono font-bold tracking-wider uppercase mb-1 sm:mb-1.5 border border-zinc-700/60">
                            <Sparkles className="w-2.5 h-2.5 sm:w-3 sm:h-3 text-zinc-300" />
                            <span className="hidden xs:inline">FEATURE MATRIX</span>
                            <span className="xs:hidden">FEATURES</span>
                          </div>
                          <div className="font-display font-bold text-xs sm:text-base text-white leading-tight">Plans &amp; features</div>
                          <div className="hidden sm:block text-[10.5px] sm:text-[11px] text-zinc-400 font-normal mt-0.5 leading-tight line-clamp-1 sm:line-clamp-2">
                            Side-by-side comparison of all capabilities.
                          </div>
                        </div>

                        {/* Inline Cadence Switcher in Sticky Header (Monthly / Annually) */}
                        <div className="inline-flex items-center p-0.5 bg-zinc-800 border border-zinc-700 rounded-full shadow-2xs w-full select-none">
                          <button
                            type="button"
                            onClick={() => setBillingCycle('monthly')}
                            className={`flex-1 py-1 px-1 sm:px-2 rounded-full text-[9px] sm:text-[10px] font-semibold text-center transition-all cursor-pointer ${
                              billingCycle === 'monthly' ? 'bg-white text-zinc-950 font-bold shadow-xs' : 'text-zinc-400 hover:text-white'
                            }`}
                          >
                            Monthly
                          </button>
                          <button
                            type="button"
                            onClick={() => setBillingCycle('annual')}
                            className={`flex-1 py-1 px-1 sm:px-2 rounded-full text-[9px] sm:text-[10px] font-semibold text-center transition-all flex items-center justify-center gap-0.5 sm:gap-1 cursor-pointer ${
                              billingCycle === 'annual' ? 'bg-white text-zinc-950 font-bold shadow-xs' : 'text-zinc-400 hover:text-white'
                            }`}
                          >
                            <span>Annually</span>
                            <span className={`text-[7.5px] sm:text-[8.5px] font-mono font-bold ${
                              billingCycle === 'annual' ? 'text-emerald-700' : 'text-emerald-400'
                            }`}>
                              -20%
                            </span>
                          </button>
                        </div>
                      </div>
                    </th>

                    {/* Free Forever Header Card (Dark Charcoal) */}
                    <th className="p-1 sm:p-2.5 align-top bg-white border-b border-zinc-200/80">
                      <div className="h-[145px] sm:h-[215px] bg-[#18181b] rounded-xl sm:rounded-2xl p-2 sm:p-3.5 text-white flex flex-col justify-between shadow-xs">
                        <div>
                          <div className="flex items-center justify-between gap-1 mb-1 sm:mb-2">
                            <div className="w-6 h-6 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-zinc-800 text-white flex items-center justify-center border border-zinc-700 shadow-xs">
                              <Leaf className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-emerald-400 stroke-[2.2]" />
                            </div>
                            <span className="inline-flex items-center px-1.5 py-0.5 sm:px-2 rounded-full bg-emerald-950/80 text-emerald-300 text-[8px] sm:text-[9px] font-mono font-bold tracking-wide border border-emerald-800/60">
                              FREE
                            </span>
                          </div>
                          <div className="font-display font-bold text-xs sm:text-sm text-white">Free Forever</div>
                          <div className="mt-1 sm:mt-1.5 font-mono flex items-baseline gap-0.5">
                            <span className="text-xl sm:text-[30px] font-extrabold tracking-tight leading-none text-emerald-400">₹0</span>
                            <span className="text-[10px] sm:text-xs text-zinc-400 font-medium font-sans">/ Free</span>
                          </div>
                        </div>

                        <button
                          type="button"
                          onClick={() => handleInitiatePlan('free')}
                          className="w-full inline-flex items-center justify-center py-1 sm:py-2 px-1 sm:px-2 rounded-lg sm:rounded-xl bg-white hover:bg-zinc-100 text-zinc-950 text-[10px] sm:text-[11px] font-bold transition-all shadow-xs cursor-pointer"
                        >
                          Start Free
                        </button>
                      </div>
                    </th>

                    {/* Starter Header Card (Deep Forest Emerald) */}
                    <th className="p-1 sm:p-2.5 align-top bg-white border-b border-zinc-200/80">
                      <div className="h-[145px] sm:h-[215px] bg-[#0b7a4d] rounded-xl sm:rounded-2xl p-2 sm:p-3.5 text-white flex flex-col justify-between shadow-xs">
                        <div>
                          <div className="flex items-center justify-between gap-1 mb-1 sm:mb-2">
                            <div className="w-6 h-6 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-white/20 text-white flex items-center justify-center shadow-xs border border-white/30 backdrop-blur-xs">
                              <Rocket className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-white stroke-[2.2]" />
                            </div>
                            {billingCycle === 'annual' && (
                              <span className="inline-flex items-center px-1.5 py-0.5 sm:px-2 rounded-full bg-white/20 text-white text-[7.5px] sm:text-[9px] font-mono font-bold tracking-tight border border-white/30 backdrop-blur-xs">
                                2 MO FREE
                              </span>
                            )}
                          </div>
                          <div className="font-display font-bold text-xs sm:text-sm text-white">Starter</div>
                          <div className="mt-1 sm:mt-1.5 font-mono flex items-baseline gap-0.5 text-white">
                            <span className="text-xl sm:text-[30px] font-extrabold tracking-tight leading-none">
                              {billingCycle === 'annual' ? (currency === 'INR' ? '₹833' : '$7.50') : (currency === 'INR' ? '₹999' : '$9')}
                            </span>
                            <span className="text-[10px] sm:text-xs text-white/80 font-medium font-sans">/mo</span>
                          </div>
                        </div>

                        <button
                          type="button"
                          onClick={() => handleInitiatePlan('starter')}
                          className="w-full inline-flex items-center justify-center py-1 sm:py-2 px-1 sm:px-2 rounded-lg sm:rounded-xl bg-white hover:bg-emerald-50 text-[#0b7a4d] text-[10px] sm:text-[11px] font-bold transition-all shadow-xs cursor-pointer"
                        >
                          Get Starter
                        </button>
                      </div>
                    </th>

                    {/* Professional Header (Deep Rich Berry / Wine) */}
                    <th className="p-1 sm:p-2.5 align-top bg-[#fdf2f8]/50 border-b border-zinc-200/80">
                      <div className={`h-[145px] sm:h-[215px] rounded-xl sm:rounded-2xl p-2 sm:p-3.5 text-white flex flex-col justify-between shadow-xl ring-2 ring-[#be185d]/40 relative overflow-hidden ${
                        isPartner100Coupon(appliedCoupon) ? 'bg-gradient-to-br from-emerald-800 via-teal-800 to-emerald-900' : 'bg-[#9d174d]'
                      }`}>
                        <div>
                          <div className="flex items-center justify-between gap-1 mb-1 sm:mb-2">
                            <div className="w-6 h-6 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl shadow-xs flex items-center justify-center bg-white/20 text-white border border-white/30 backdrop-blur-xs">
                              <Sparkles className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-white stroke-[2.2]" />
                            </div>
                            {billingCycle === 'annual' && (
                              <span className="inline-flex items-center px-1.5 py-0.5 sm:px-2 rounded-full bg-white/20 text-white text-[7.5px] sm:text-[9px] font-mono font-bold tracking-tight border border-white/30 backdrop-blur-xs">
                                2 MO FREE
                              </span>
                            )}
                          </div>
                          <div className="font-display font-bold text-xs sm:text-sm text-white">Professional</div>
                          <div className="mt-1 sm:mt-1.5 font-mono flex items-baseline gap-0.5 text-white">
                            <span className="text-xl sm:text-[30px] font-extrabold tracking-tight leading-none">
                              {billingCycle === 'annual' ? (currency === 'INR' ? '₹2,083' : '$20') : (currency === 'INR' ? '₹2,499' : '$24')}
                            </span>
                            <span className="text-[10px] sm:text-xs text-white/80 font-medium font-sans">/mo</span>
                          </div>
                        </div>

                        <div className="space-y-0.5 sm:space-y-1.5">
                          <div className="flex items-center justify-center">
                            <span className="inline-flex items-center gap-0.5 px-1.5 sm:px-2 py-0.2 sm:py-0.5 rounded-full bg-white text-[#9d174d] text-[7.5px] sm:text-[9px] font-mono font-bold tracking-wider uppercase shadow-xs">
                              <Star className="w-2 h-2 sm:w-2.5 sm:h-2.5 fill-[#9d174d] text-[#9d174d]" />
                              <span>POPULAR</span>
                            </span>
                          </div>
                          <button
                            type="button"
                            onClick={() => handleInitiatePlan('pro')}
                            className="w-full inline-flex items-center justify-center py-1 sm:py-2 px-1 sm:px-2 rounded-lg sm:rounded-xl bg-white hover:bg-pink-50 text-[#9d174d] text-[10px] sm:text-[11px] font-bold transition-all shadow-md cursor-pointer"
                          >
                            Get Pro
                          </button>
                        </div>
                      </div>
                    </th>

                    {/* Scale Header Card (Deep Royal Indigo) */}
                    <th className="p-1 sm:p-2.5 align-top bg-white border-b border-zinc-200/80">
                      <div className="h-[145px] sm:h-[215px] bg-[#3730a3] rounded-xl sm:rounded-2xl p-2 sm:p-3.5 text-white flex flex-col justify-between shadow-xs">
                        <div>
                          <div className="flex items-center justify-between gap-1 mb-1 sm:mb-2">
                            <div className="w-6 h-6 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-white/20 text-white shadow-xs flex items-center justify-center border border-white/30 backdrop-blur-xs">
                              <TrendingUp className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-white stroke-[2.2]" />
                            </div>
                            {billingCycle === 'annual' && (
                              <span className="inline-flex items-center px-1.5 py-0.5 sm:px-2 rounded-full bg-white/20 text-white text-[7.5px] sm:text-[9px] font-mono font-bold tracking-tight border border-white/30 backdrop-blur-xs">
                                2 MO FREE
                              </span>
                            )}
                          </div>
                          <div className="font-display font-bold text-xs sm:text-sm text-white">Scale</div>
                          <div className="mt-1 sm:mt-1.5 font-mono flex items-baseline gap-0.5 text-white">
                            <span className="text-xl sm:text-[30px] font-extrabold tracking-tight leading-none">
                              {billingCycle === 'annual' ? (currency === 'INR' ? '₹3,749' : '$37.50') : (currency === 'INR' ? '₹4,499' : '$45')}
                            </span>
                            <span className="text-[10px] sm:text-xs text-white/80 font-medium font-sans">/mo</span>
                          </div>
                        </div>

                        <button
                          type="button"
                          onClick={() => handleInitiatePlan('scale')}
                          className="w-full inline-flex items-center justify-center py-1 sm:py-2 px-1 sm:px-2 rounded-lg sm:rounded-xl bg-white hover:bg-purple-50 text-[#3730a3] text-[10px] sm:text-[11px] font-bold transition-all shadow-xs cursor-pointer"
                        >
                          Get Scale
                        </button>
                      </div>
                    </th>

                  </tr>
                </thead>

                <tbody className="text-zinc-700">
                  
                  {/* ─────────────────────────────────────────────────────────────
                      CATEGORY 1: AI & AUTONOMOUS AGENTS
                  ───────────────────────────────────────────────────────────── */}
                  <tr className="border-b border-zinc-200/90">
                    <td colSpan={5} className="pt-6 sm:pt-8 pb-2.5 sm:pb-3 px-3 sm:px-4 bg-zinc-50/70 sm:bg-white border-b border-zinc-200">
                      <div className="sticky left-2.5 sm:left-4 inline-flex items-center gap-1.5 sm:gap-2 font-display text-[13.5px] sm:text-base font-bold text-zinc-950 tracking-tight">
                        <Sparkles className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-zinc-900 stroke-[2.2]" />
                        <span>AI &amp; Autonomous Agents</span>
                      </div>
                    </td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Monthly AI Runs</span>
                        <FeatureInfoTooltip text="Monthly allocation of autonomous AI reasoning cycles across copy, proposals, briefs, and tasks." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[13px] text-zinc-700 border-b border-dotted border-zinc-200">500 / mo</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[13px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200">{billingCycle === 'annual' ? '6,000' : '5,000'} / mo</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[13px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">14,000 / mo</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[13px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">20,000 / mo</td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>AI Reasoning Engine</span>
                        <FeatureInfoTooltip text="Underlying AI intelligence foundation—from standard models to frontier reasoning engines (GPT-4o & Claude 3.5 Sonnet)." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] text-zinc-600 border-b border-dotted border-zinc-200">Standard AI</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] font-medium text-zinc-900 border-b border-dotted border-zinc-200">Standard AI</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">Advanced AI Reasoning</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">Frontier (GPT-4o &amp; Claude 3.5)</td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Autonomous Scope &amp; Brief Generator</span>
                        <FeatureInfoTooltip text="AI agent that analyzes intake forms to generate complete client proposals, milestones, and deliverables." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Deep Market Research Agent</span>
                        <FeatureInfoTooltip text="Autonomous agent that audits competitors, target demographics, and market data for client projects." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200"><span className="text-zinc-300 text-sm font-medium">—</span></td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200"><span className="text-zinc-300 text-sm font-medium">—</span></td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Brand Prompt Rules &amp; Workspace Memory</span>
                        <FeatureInfoTooltip text="Persistent workspace memory and brand voice rules that train AI agents on your specific style and guidelines." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] text-zinc-600 border-b border-dotted border-zinc-200">Basic memory</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] font-medium text-zinc-900 border-b border-dotted border-zinc-200">Workspace memory</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">Full Brand Context</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">Multi-Tenant Knowledge Vault</td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>AI Content Localization &amp; Translation</span>
                        <FeatureInfoTooltip text="Translate portfolio copy, proposals, and client communications dynamically across international languages." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200"><span className="text-zinc-300 text-sm font-medium">—</span></td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-medium text-zinc-900 border-b border-dotted border-zinc-200">1 Language</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">5 Languages</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">Unlimited Languages</td>
                  </tr>

                  {/* ─────────────────────────────────────────────────────────────
                      CATEGORY 2: WEBSITES & HOSTING
                  ───────────────────────────────────────────────────────────── */}
                  <tr className="border-b border-zinc-200/90">
                    <td colSpan={5} className="pt-6 sm:pt-8 pb-2.5 sm:pb-3 px-3 sm:px-4 bg-zinc-50/70 sm:bg-white border-b border-zinc-200">
                      <div className="sticky left-2.5 sm:left-4 inline-flex items-center gap-1.5 sm:gap-2 font-display text-[13.5px] sm:text-base font-bold text-zinc-950 tracking-tight">
                        <Globe className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-zinc-900 stroke-[2.2]" />
                        <span>Websites &amp; Hosting</span>
                      </div>
                    </td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Portfolio &amp; Booking Website Builder</span>
                        <FeatureInfoTooltip text="Responsive no-code studio website builder with built-in client booking calendars and media galleries." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Website Hosting URL</span>
                        <FeatureInfoTooltip text="Website hosting options—choose between a free heycora.in subdomain or custom .com/.in domain mapping." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] text-zinc-600 border-b border-dotted border-zinc-200">heycora.in/you</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200">Custom Domain (.com/.in)</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">Custom Domain</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">Custom Domain + Edge CDN</td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Monthly Bandwidth &amp; Global CDN</span>
                        <FeatureInfoTooltip text="High-speed global edge network traffic allocation with zero-latency caching, SSL, and DDoS surge protection." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] text-zinc-600 border-b border-dotted border-zinc-200">5 GB / mo</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-medium text-zinc-900 border-b border-dotted border-zinc-200">50 GB / mo</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">250 GB / mo</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">Unlimited (Global CDN)</td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Static &amp; Dynamic Pages Limit</span>
                        <FeatureInfoTooltip text="Maximum number of custom portfolio, service, case study, and landing pages created inside the builder." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] text-zinc-600 border-b border-dotted border-zinc-200">3 Pages</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-medium text-zinc-900 border-b border-dotted border-zinc-200">20 Pages</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">100 Pages</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">Unlimited Pages</td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Automated Snapshots &amp; Version History</span>
                        <FeatureInfoTooltip text="1-click snapshot restore with immutable revision history for your website design, layouts, and CMS content." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] text-zinc-600 border-b border-dotted border-zinc-200">7 Days History</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-medium text-zinc-900 border-b border-dotted border-zinc-200">30 Days History</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">90 Days History</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">Unlimited Snapshots</td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <div>
                          <span>Free Done-For-You Website Migration</span>
                          <span className="text-[11px] text-zinc-500 font-normal block">Engineered by Cora team</span>
                        </div>
                        <FeatureInfoTooltip text="Our in-house engineering team completely rebuilds and migrates your portfolio website with zero downtime." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200"><span className="text-zinc-300 text-sm font-medium">—</span></td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200"><span className="text-zinc-300 text-sm font-medium">—</span></td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45"><span className="text-zinc-300 text-sm font-medium">—</span></td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200">
                      <span className="text-xs sm:text-[13px] font-bold text-zinc-950">✓ Custom Build &amp; Migration</span>
                    </td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Custom 301 / 302 URL Redirects</span>
                        <FeatureInfoTooltip text="Manage URL redirection rules to preserve Google search rankings and link equity when moving pages." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] text-zinc-600 border-b border-dotted border-zinc-200">Up to 5</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-medium text-zinc-900 border-b border-dotted border-zinc-200">Up to 50</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">Unlimited</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">Unlimited + Wildcard</td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Password Protection &amp; Staging</span>
                        <FeatureInfoTooltip text="Create secure, password-protected staging sandboxes for client draft previews before going live." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Custom .well-known &amp; Static Hosting</span>
                        <FeatureInfoTooltip text="Host verification files, app-ads.txt, Apple Pay merchant configs, and root-level static developer assets." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200"><span className="text-zinc-300 text-sm font-medium">—</span></td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200"><span className="text-zinc-300 text-sm font-medium">—</span></td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[12px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">✓ Custom Headers</td>
                  </tr>

                  {/* ─────────────────────────────────────────────────────────────
                      CATEGORY 3: CRM & CMS
                  ───────────────────────────────────────────────────────────── */}
                  <tr className="border-b border-zinc-200/90">
                    <td colSpan={5} className="pt-6 sm:pt-8 pb-2.5 sm:pb-3 px-3 sm:px-4 bg-zinc-50/70 sm:bg-white border-b border-zinc-200">
                      <div className="sticky left-2.5 sm:left-4 inline-flex items-center gap-1.5 sm:gap-2 font-display text-[13.5px] sm:text-base font-bold text-zinc-950 tracking-tight">
                        <Users className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-zinc-900 stroke-[2.2]" />
                        <span>CRM &amp; CMS</span>
                      </div>
                    </td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Kanban Lead Funnel &amp; Pipeline</span>
                        <FeatureInfoTooltip text="Visual Kanban workflow to track incoming client leads, discovery calls, active proposals, and won contracts." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Lead Forms &amp; File Upload Dropzone</span>
                        <FeatureInfoTooltip text="Embeddable client intake forms with direct file upload dropzones for creative briefs and project assets (up to 25MB)." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] text-zinc-600 border-b border-dotted border-zinc-200">Basic Forms (Text only)</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] font-medium text-zinc-900 border-b border-dotted border-zinc-200">Forms + 5MB Uploads</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">Forms + 15MB Uploads</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">Forms + 25MB Max Uploads</td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>CMS Collections &amp; Dynamic Records</span>
                        <FeatureInfoTooltip text="Structured dynamic databases for client case studies, portfolio projects, client testimonials, and service pricing cards." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] text-zinc-600 border-b border-dotted border-zinc-200">2 Collections (100 items)</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-medium text-zinc-900 border-b border-dotted border-zinc-200">10 Collections (1,000 items)</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">50 Collections (10,000 items)</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">Unlimited Collections</td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Scheduled &amp; Batch Publishing</span>
                        <FeatureInfoTooltip text="Schedule new portfolio project drops, case studies, and blog posts to publish automatically at specific dates and times." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200"><span className="text-zinc-300 text-sm font-medium">—</span></td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] text-zinc-900 border-b border-dotted border-zinc-200">✓ Instant Live</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">✓ Scheduled &amp; Batch</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">✓ Multi-Channel Scheduled</td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Client Meeting &amp; Call Scheduler</span>
                        <FeatureInfoTooltip text="Integrated client scheduling calendar with automated timezone detection, buffer times, and Google Calendar sync." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] text-zinc-600 border-b border-dotted border-zinc-200">1 Link</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200">Unlimited + GCal</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">Round-Robin &amp; Team</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">VIP Concierge Sync</td>
                  </tr>

                  {/* ─────────────────────────────────────────────────────────────
                      CATEGORY 4: CONTRACTS & LEGAL E-SIGN
                  ───────────────────────────────────────────────────────────── */}
                  <tr className="border-b border-zinc-200/90">
                    <td colSpan={5} className="pt-6 sm:pt-8 pb-2.5 sm:pb-3 px-3 sm:px-4 bg-zinc-50/70 sm:bg-white border-b border-zinc-200">
                      <div className="sticky left-2.5 sm:left-4 inline-flex items-center gap-1.5 sm:gap-2 font-display text-[13.5px] sm:text-base font-bold text-zinc-950 tracking-tight">
                        <FileCheck className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-zinc-900 stroke-[2.2]" />
                        <span>Contracts &amp; Legal E-Sign</span>
                      </div>
                    </td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>SHA-256 Tamper-Evident Contract Vault</span>
                        <FeatureInfoTooltip text="Cryptographically hashed digital document registry that proves contracts have not been altered after signing." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Legally Binding E-Signatures (IT Act 2000)</span>
                        <FeatureInfoTooltip text="Court-admissible electronic signatures complying with the Indian IT Act 2000 and global e-sign standards." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] text-zinc-600 border-b border-dotted border-zinc-200">3 / mo</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200">Unlimited</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">Unlimited</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">Unlimited</td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Scope Creep Intercept &amp; Paid Addendums</span>
                        <FeatureInfoTooltip text="Automatically flags requests outside the agreed scope and generates 1-click paid addendum contracts." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200"><span className="text-zinc-300 text-sm font-medium">—</span></td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200"><span className="text-zinc-300 text-sm font-medium">—</span></td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Audit Trail &amp; Signature Timestamp Log</span>
                        <FeatureInfoTooltip text="Comprehensive completion certificate with signer IP addresses, device user-agents, and ISO timestamps." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] text-zinc-600 border-b border-dotted border-zinc-200">Basic Log</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] font-medium text-zinc-900 border-b border-dotted border-zinc-200">IP &amp; Timestamp</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">Full Cryptographic Trail</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">Immutable Compliance Log</td>
                  </tr>

                  {/* ─────────────────────────────────────────────────────────────
                      CATEGORY 5: INVOICING, PAYMENTS & GST
                  ───────────────────────────────────────────────────────────── */}
                  <tr className="border-b border-zinc-200/90">
                    <td colSpan={5} className="pt-6 sm:pt-8 pb-2.5 sm:pb-3 px-3 sm:px-4 bg-zinc-50/70 sm:bg-white border-b border-zinc-200">
                      <div className="sticky left-2.5 sm:left-4 inline-flex items-center gap-1.5 sm:gap-2 font-display text-[13.5px] sm:text-base font-bold text-zinc-950 tracking-tight">
                        <Receipt className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-zinc-900 stroke-[2.2]" />
                        <span>Invoicing, Payments &amp; GST</span>
                      </div>
                    </td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>18% GST Invoicing Engine</span>
                        <FeatureInfoTooltip text="Automated CGST / SGST / IGST tax calculations with HSN/SAC codes and 100% Input Tax Credit (ITC) compliance." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] text-zinc-600 border-b border-dotted border-zinc-200">Basic Tax Math</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] font-medium text-zinc-900 border-b border-dotted border-zinc-200">Auto CGST / SGST / IGST</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">Auto CGST / SGST / IGST</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">Custom Multi-State Math &amp; ITC</td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Dynamic UPI QR Code (Zero Gateway Fees)</span>
                        <FeatureInfoTooltip text="Embeds dynamic bank UPI QR codes directly onto invoices for 0% payment gateway settlement directly to your bank." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200"><span className="text-zinc-300 text-sm font-medium">—</span></td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Razorpay &amp; International Cards</span>
                        <FeatureInfoTooltip text="Accept credit/debit cards domestically and from international clients with multi-currency conversion." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] text-zinc-600 border-b border-dotted border-zinc-200">Manual Receipts</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] font-medium text-zinc-900 border-b border-dotted border-zinc-200">✓ Domestic Cards &amp; UPI</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">✓ International + Domestic</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">✓ Multi-Currency Gateways</td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Milestone Escrow &amp; Split Payments</span>
                        <FeatureInfoTooltip text="Break down high-ticket projects into automated upfront deposit, mid-progress, and final handover invoices." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200"><span className="text-zinc-300 text-sm font-medium">—</span></td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-medium text-zinc-900 border-b border-dotted border-zinc-200">2 Milestones</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">Unlimited Milestones</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">Multi-Party Split &amp; Escrow</td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Auto Payment Receipts &amp; Reminders</span>
                        <FeatureInfoTooltip text="Automated payment confirmation receipts and scheduled email/WhatsApp reminder nudges before due dates." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] text-zinc-600 border-b border-dotted border-zinc-200">Manual Email</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] font-medium text-zinc-900 border-b border-dotted border-zinc-200">Auto Email Receipts</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">Email + WhatsApp Reminders</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">Custom Retainer Autopay</td>
                  </tr>

                  {/* ─────────────────────────────────────────────────────────────
                      CATEGORY 6: CLIENT DISPATCH & MESSAGING
                  ───────────────────────────────────────────────────────────── */}
                  <tr className="border-b border-zinc-200/90">
                    <td colSpan={5} className="pt-6 sm:pt-8 pb-2.5 sm:pb-3 px-3 sm:px-4 bg-zinc-50/70 sm:bg-white border-b border-zinc-200">
                      <div className="sticky left-2.5 sm:left-4 inline-flex items-center gap-1.5 sm:gap-2 font-display text-[13.5px] sm:text-base font-bold text-zinc-950 tracking-tight">
                        <Send className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-zinc-900 stroke-[2.2]" />
                        <span>Client Dispatch &amp; Messaging</span>
                      </div>
                    </td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>WhatsApp Business Automation*</span>
                        <FeatureInfoTooltip text="Official WhatsApp automation for sending instant booking confirmations, milestone updates, and invoice links." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200"><span className="text-zinc-300 text-sm font-medium">—</span></td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] text-zinc-700 border-b border-dotted border-zinc-200">Quick Dispatch</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Custom Domain Email Dispatch</span>
                        <FeatureInfoTooltip text="Dispatch all proposals, contracts, and notifications from your own branded domain email (e.g. hello@yourstudio.com)." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] text-zinc-600 border-b border-dotted border-zinc-200">System mail</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Free External Client Reviewers (Viewers)</span>
                        <FeatureInfoTooltip text="Invite unlimited external clients and stakeholders to review, comment, and approve designs without extra seat charges." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-medium text-zinc-900 border-b border-dotted border-zinc-200">Unlimited Free</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200">Unlimited Free</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">Unlimited Free</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">Unlimited Free</td>
                  </tr>

                  {/* ─────────────────────────────────────────────────────────────
                      CATEGORY 7: DEVELOPER TOOLS & APIS
                  ───────────────────────────────────────────────────────────── */}
                  <tr className="border-b border-zinc-200/90">
                    <td colSpan={5} className="pt-6 sm:pt-8 pb-2.5 sm:pb-3 px-3 sm:px-4 bg-zinc-50/70 sm:bg-white border-b border-zinc-200">
                      <div className="sticky left-2.5 sm:left-4 inline-flex items-center gap-1.5 sm:gap-2 font-display text-[13.5px] sm:text-base font-bold text-zinc-950 tracking-tight">
                        <Code className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-zinc-900 stroke-[2.2]" />
                        <span>Developer Tools &amp; APIs</span>
                      </div>
                    </td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Branching with Staging Sandbox</span>
                        <FeatureInfoTooltip text="Isolated development branches and staging sandboxes to safely test code and layout changes before publishing." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] text-zinc-600 border-b border-dotted border-zinc-200">1 Branch</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-medium text-zinc-900 border-b border-dotted border-zinc-200">3 Branches</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">10 Branches</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">Unlimited Staging</td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Multi-Variant A/B Testing &amp; Analytics</span>
                        <FeatureInfoTooltip text="Run split tests on landing pages and booking forms to optimize visitor-to-client conversion rates." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200"><span className="text-zinc-300 text-sm font-medium">—</span></td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200"><span className="text-zinc-300 text-sm font-medium">—</span></td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">Multi-Variant Engine</td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>AI Vector Site Search</span>
                        <FeatureInfoTooltip text="Semantic vector search engine allowing visitors to search through projects, case studies, and content instantly." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200"><span className="text-zinc-300 text-sm font-medium">—</span></td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200"><span className="text-zinc-300 text-sm font-medium">—</span></td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">Knowledge Graph Search</td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Custom HTML / CSS Code Embeds</span>
                        <FeatureInfoTooltip text="Inject custom analytics scripts, CSS styling, third-party chat widgets, and HTML components anywhere." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200"><span className="text-zinc-300 text-sm font-medium">—</span></td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-medium text-zinc-900 border-b border-dotted border-zinc-200">✓ Header &amp; Footer</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">✓ Full Code Embeds</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">✓ React &amp; Headless SDK</td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>CMS REST API &amp; Webhooks Capacity</span>
                        <FeatureInfoTooltip text="High-throughput REST API endpoints and webhooks for synchronizing CMS content, bookings, and customer records." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200"><span className="text-zinc-300 text-sm font-medium">—</span></td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200"><span className="text-zinc-300 text-sm font-medium">—</span></td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">60 RPM API</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">300 RPM API + Custom Headers</td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Custom Webhooks &amp; REST APIs</span>
                        <FeatureInfoTooltip text="Programmable webhooks and REST endpoints to integrate Cora with Zapier, Make, Slack, and internal CRMs." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200"><span className="text-zinc-300 text-sm font-medium">—</span></td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200"><span className="text-zinc-300 text-sm font-medium">—</span></td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45"><span className="text-zinc-300 text-sm font-medium">—</span></td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200">
                      <div className="w-5 h-5 rounded-full bg-[#16a34a] flex items-center justify-center mx-auto text-white shadow-2xs shrink-0">
                        <Check className="w-3.5 h-3.5 stroke-[2.5]" />
                      </div>
                    </td>
                  </tr>

                  {/* ─────────────────────────────────────────────────────────────
                      CATEGORY 8: TEAM & SECURITY
                  ───────────────────────────────────────────────────────────── */}
                  <tr className="border-b border-zinc-200/90">
                    <td colSpan={5} className="pt-6 sm:pt-8 pb-2.5 sm:pb-3 px-3 sm:px-4 bg-zinc-50/70 sm:bg-white border-b border-zinc-200">
                      <div className="sticky left-2.5 sm:left-4 inline-flex items-center gap-1.5 sm:gap-2 font-display text-[13.5px] sm:text-base font-bold text-zinc-950 tracking-tight">
                        <Shield className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-zinc-900 stroke-[2.2]" />
                        <span>Team &amp; Security</span>
                      </div>
                    </td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Team Workspace Seats</span>
                        <FeatureInfoTooltip text="Number of team members, project managers, and collaborators who have active access to the workspace." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[13px] text-zinc-700 border-b border-dotted border-zinc-200">1 seat</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[13px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200">2 seats</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[13px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">5 seats</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[13px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">Unlimited*</td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Freelancer &amp; Guest Collaborator Seats</span>
                        <FeatureInfoTooltip text="Scoped temporary collaborator access for external freelancers, copywriters, and contracted developers." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200"><span className="text-zinc-300 text-sm font-medium">—</span></td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-medium text-zinc-900 border-b border-dotted border-zinc-200">1 Guest</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">5 Guests</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-mono text-xs sm:text-[12px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">Unlimited Guests</td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Role-Based Access Control (RBAC) &amp; SSO</span>
                        <FeatureInfoTooltip text="Granular permissions for Admins, Project Managers, and Viewers plus Single Sign-On (SSO) authentication." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] text-zinc-600 border-b border-dotted border-zinc-200">Owner only</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] font-medium text-zinc-900 border-b border-dotted border-zinc-200">Admin &amp; Editor</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] font-semibold text-zinc-900 border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">Granular Permissions</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[13px] font-bold text-zinc-950 border-b border-dotted border-zinc-200">SSO &amp; Enterprise RBAC</td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <div>
                          <span>Annual Savings</span>
                          <span className="text-[11px] text-zinc-400 font-normal block">Billed annually</span>
                        </div>
                        <FeatureInfoTooltip text="Instant 2 months free discount applied automatically when subscribing on an annual billing cadence." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-xs sm:text-[12px] text-zinc-500 font-mono border-b border-dotted border-zinc-200">Free Forever</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200">
                      {billingCycle === 'annual' ? (
                        <span className="font-mono text-xs sm:text-[12px] font-semibold text-zinc-900">2 Months Free (₹1,998 saved)</span>
                      ) : (
                        <span className="font-mono text-xs sm:text-[12px] text-zinc-500">Save 2 Months</span>
                      )}
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">
                      {billingCycle === 'annual' ? (
                        <span className="font-mono text-xs sm:text-[12px] font-semibold text-zinc-900">2 Months Free (₹4,998 saved)</span>
                      ) : (
                        <span className="font-mono text-xs sm:text-[12px] text-zinc-500">Save 2 Months</span>
                      )}
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center border-b border-dotted border-zinc-200">
                      {billingCycle === 'annual' ? (
                        <span className="font-mono text-xs sm:text-[12px] font-bold text-zinc-950">2 Months Free (₹8,998 saved)</span>
                      ) : (
                        <span className="font-mono text-xs sm:text-[12px] text-zinc-500">Save 2 Months</span>
                      )}
                    </td>
                  </tr>

                  <tr className="hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 sm:py-4 px-3 sm:px-4 font-medium text-xs sm:text-[13.5px] text-zinc-900 sticky left-0 z-20 bg-white border-r border-zinc-200/90 border-b border-dotted border-zinc-200 shadow-[2px_0_6px_-2px_rgba(0,0,0,0.06)]">
                      <div className="inline-flex items-center">
                        <span>Customer Support SLA</span>
                        <FeatureInfoTooltip text="Guaranteed response times and channels for technical support, including dedicated WhatsApp and Slack channels." />
                      </div>
                    </td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-zinc-600 font-mono text-xs sm:text-[12px] border-b border-dotted border-zinc-200">Community</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center text-zinc-900 font-mono text-xs sm:text-[12px] border-b border-dotted border-zinc-200">Standard Email</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-semibold text-zinc-900 font-mono text-xs sm:text-[12px] border-b border-dotted border-zinc-200 bg-[#fdf2f8]/45">Priority Support Desk</td>
                    <td className="py-3.5 sm:py-4 px-3.5 sm:px-4 text-center font-bold text-zinc-950 font-mono text-xs sm:text-[12px] border-b border-dotted border-zinc-200">Priority 24/7 Slack &amp; WhatsApp</td>
                  </tr>

                </tbody>
              </table>
            </div>

            {/* Table Bottom Assurance Footer Bar */}
            <div className="mt-4 sm:mt-6 rounded-xl sm:rounded-2xl bg-zinc-50 border border-zinc-200/80 p-3.5 sm:p-4 flex flex-col sm:flex-row items-center justify-between gap-2.5 sm:gap-3 text-[11px] sm:text-xs text-zinc-700 shadow-2xs">
              <div className="flex items-center gap-2">
                <Sparkles className="w-4 h-4 text-zinc-800 shrink-0" />
                <span>All plans include core platform modules, regular updates, and 24/7 support.</span>
              </div>
              <div className="flex items-center gap-2 text-zinc-600 shrink-0 font-medium">
                <Shield className="w-4 h-4 text-zinc-700 shrink-0" />
                <span>Secure. Compliant. Built for modern businesses.</span>
              </div>
            </div>

          </div>
        </section>
      )}

      {/* ══════════════════════════════════════════════════════════════════════
          INDIA STARTUP CREDITS (GEOLOCATION GUARDED - INDIA ONLY)
      ══════════════════════════════════════════════════════════════════════ */}
      {isIndia && (
        <section id="india-only-plan" className="w-full max-w-[1280px] mx-auto px-3.5 sm:px-6 mb-14 sm:mb-20">
          <div className={`border rounded-2xl sm:rounded-[28px] p-4 sm:p-8 shadow-[0_4px_24px_rgba(0,0,0,0.03)] transition-all relative overflow-hidden ${
            appliedCoupon === 'INDIA40'
              ? 'bg-gradient-to-br from-amber-50/80 via-white to-orange-50/30 border-amber-400 ring-4 ring-amber-400/20'
              : 'bg-gradient-to-br from-zinc-50/90 via-white to-amber-50/20 border-zinc-200/90 hover:border-zinc-300'
          }`}>
            
            <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-4 sm:gap-6 pb-4 sm:pb-6 border-b border-zinc-200/80">
              
              <div className="space-y-1.5 sm:space-y-2 max-w-[660px]">
                <div className="flex flex-wrap items-center gap-2">
                  <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full bg-amber-100/80 border border-amber-200/80 text-[10px] sm:text-xs font-bold text-amber-900">
                    <Building2 className="w-3 h-3 sm:w-3.5 sm:h-3.5 text-amber-700 shrink-0" />
                    <span>EXCLUSIVE FOR INDIA &bull; STARTUP GRANT</span>
                  </div>
                  {appliedCoupon === 'INDIA40' && (
                    <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-100 border border-emerald-300 text-emerald-900 text-[10px] sm:text-xs font-mono font-bold animate-pulse">
                      <Sparkles className="w-3 h-3 text-emerald-700" />
                      <span>40% FLASH DISCOUNT UNLOCKED</span>
                    </span>
                  )}
                </div>

                {appliedCoupon === 'INDIA40' && (
                  <div className="p-2.5 sm:p-3 rounded-xl sm:rounded-2xl bg-amber-400/20 border border-amber-400/50 flex flex-wrap items-center justify-between gap-2 text-xs">
                    <div className="flex items-center gap-1.5 font-bold text-amber-950 text-[11px] sm:text-xs">
                      <Flame className="w-3.5 h-3.5 text-amber-600 fill-amber-600 animate-bounce" />
                      <span>FLASH TIER ACTIVE: 4 SPOTS LEFT TODAY</span>
                    </div>
                    <div className="flex items-center gap-1 font-mono text-[11px] sm:text-xs font-bold text-amber-950 bg-white/90 px-2 py-0.5 rounded-lg border border-amber-300/80 shadow-2xs">
                      <Clock className="w-3 h-3 text-amber-700" />
                      <span>Ends in: {formatFlashTimer(flashSecondsLeft)}</span>
                    </div>
                  </div>
                )}

                <div>
                  <h2 className="font-display text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight text-zinc-950">
                    India Startup Credits
                  </h2>
                </div>

                <p className="text-xs sm:text-sm text-zinc-600 leading-relaxed max-w-[560px]">
                  Dedicated infrastructure grant for Indian studios and solopreneurs with ₹5,000+ credits, free domain, and 0% UPI settlements.
                </p>
              </div>

              {/* Price & Action */}
              <div className="flex flex-col sm:items-end justify-center gap-2 sm:gap-2.5 shrink-0 pt-1 sm:pt-0">
                <div className="flex items-center gap-2 sm:gap-2.5">
                  <div className="flex items-baseline gap-1">
                    <span className="text-3xl sm:text-4xl font-display font-extrabold text-zinc-950">
                      {appliedCoupon === 'INDIA40' 
                        ? '₹299' 
                        : `₹${indiaPricing.effectiveMonthlyRate.toLocaleString('en-IN')}`}
                    </span>
                    <span className="text-xs text-zinc-500 font-medium">/ mo</span>
                    {(appliedCoupon === 'INDIA40' || indiaPricing.isReferralDiscount) && (
                      <span className="text-base text-zinc-400 line-through ml-1 font-mono font-bold">
                        ₹499
                      </span>
                    )}
                  </div>
                  <span className="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 text-[10px] sm:text-[11px] font-bold tracking-wide">
                    Annual Grant
                  </span>
                  {indiaPricing.isReferralDiscount && appliedCoupon !== 'INDIA40' && (
                    <span className="inline-flex items-center px-2 py-0.5 rounded-full bg-zinc-950 text-white text-[9.5px] font-mono font-bold tracking-tight">
                      10% REF OFF
                    </span>
                  )}
                </div>

                <div className="text-[10.5px] sm:text-xs text-zinc-500 font-mono text-left sm:text-right">
                  {appliedCoupon === 'INDIA40' ? (
                    <>
                      Billed as <span className="text-emerald-700 font-bold">₹3,588/yr</span> &bull; <span className="text-emerald-600 font-semibold">Save ₹2,400 with Flash Subsidy</span>
                    </>
                  ) : indiaPricing.isReferralDiscount ? (
                    <>
                      Billed as <span className="text-zinc-950 font-semibold">₹{indiaPricing.grandTotal.toLocaleString('en-IN')}/yr</span> &bull; <span className="text-emerald-700 font-bold">10% Ref Off (Save ₹599)</span>
                    </>
                  ) : (
                    <>
                      Billed annually at <span className="text-zinc-950 font-semibold">₹5,988/yr</span> &bull; Free domain included
                    </>
                  )}
                </div>

                <button
                  type="button"
                  onClick={() => handleInitiatePlan('india_only')}
                  className="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-zinc-950 hover:bg-zinc-800 text-white px-5 py-2.5 sm:px-6 sm:py-3 rounded-xl text-xs sm:text-sm font-bold transition-all shadow-sm hover:shadow-md cursor-pointer group"
                >
                  <span>
                    {appliedCoupon === 'INDIA40' 
                      ? 'Claim 40% Flash Startup Credits' 
                      : (indiaPricing.isReferralDiscount ? `Claim with 10% Referral Discount` : 'Claim India Startup Credits')}
                  </span>
                  <ArrowRight className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-zinc-400 group-hover:translate-x-0.5 group-hover:text-white transition-all" />
                </button>
              </div>

            </div>

            {/* Bottom Capabilities Grid (Uncluttered, Crisp Mobile Layout) */}
            <div className="pt-3.5 sm:pt-5">
              <div className="text-[10px] sm:text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-400 mb-2 sm:mb-3">
                INCLUDED IN STARTUP CREDITS:
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-3">
                
                {/* 1. Free Domain */}
                <div className="flex items-center gap-2.5 sm:gap-3 p-2.5 sm:p-3 rounded-xl bg-white/90 border border-zinc-200/80 shadow-2xs hover:border-zinc-300 transition-all">
                  <div className="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-amber-50 border border-amber-200/60 flex items-center justify-center shrink-0 text-amber-800">
                    <Globe className="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                  </div>
                  <div className="min-w-0">
                    <div className="font-semibold text-zinc-950 text-xs">Free Custom Domain</div>
                    <div className="text-[10.5px] sm:text-[11px] text-zinc-500 truncate">
                      .in or .com for 1st year with auto SSL
                    </div>
                  </div>
                </div>

                {/* 2. Zero-Fee UPI */}
                <div className="flex items-center gap-2.5 sm:gap-3 p-2.5 sm:p-3 rounded-xl bg-white/90 border border-zinc-200/80 shadow-2xs hover:border-zinc-300 transition-all">
                  <div className="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-emerald-50 border border-emerald-200/60 flex items-center justify-center shrink-0 text-emerald-800">
                    <QrCode className="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                  </div>
                  <div className="min-w-0">
                    <div className="font-semibold text-zinc-950 text-xs">Zero-Fee UPI QR</div>
                    <div className="text-[10.5px] sm:text-[11px] text-zinc-500 truncate">
                      0% gateway fee bank settlements
                    </div>
                  </div>
                </div>

                {/* 3. WhatsApp Dispatch */}
                <div className="flex items-center gap-2.5 sm:gap-3 p-2.5 sm:p-3 rounded-xl bg-white/90 border border-zinc-200/80 shadow-2xs hover:border-zinc-300 transition-all">
                  <div className="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-green-50 border border-green-200/60 flex items-center justify-center shrink-0 text-[#25D366]">
                    <WhatsAppIcon className="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                  </div>
                  <div className="min-w-0">
                    <div className="font-semibold text-zinc-950 text-xs">WhatsApp Client Dispatch</div>
                    <div className="text-[10.5px] sm:text-[11px] text-zinc-500 truncate">
                      Auto-send proposals, contracts &amp; receipts
                    </div>
                  </div>
                </div>

                {/* 4. GST Invoicing */}
                <div className="flex items-center gap-2.5 sm:gap-3 p-2.5 sm:p-3 rounded-xl bg-white/90 border border-zinc-200/80 shadow-2xs hover:border-zinc-300 transition-all">
                  <div className="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-blue-50 border border-blue-200/60 flex items-center justify-center shrink-0 text-blue-800">
                    <Receipt className="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                  </div>
                  <div className="min-w-0">
                    <div className="font-semibold text-zinc-950 text-xs">GSTIN-Compliant Invoicing</div>
                    <div className="text-[10.5px] sm:text-[11px] text-zinc-500 truncate">
                      Auto CGST/SGST/IGST tax calculation
                    </div>
                  </div>
                </div>

                {/* 5. AI Startup Credits */}
                <div className="flex items-center gap-2.5 sm:gap-3 p-2.5 sm:p-3 rounded-xl bg-white/90 border border-zinc-200/80 shadow-2xs hover:border-zinc-300 transition-all">
                  <div className="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-purple-50 border border-purple-200/60 flex items-center justify-center shrink-0 text-purple-800">
                    <Sparkles className="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                  </div>
                  <div className="min-w-0">
                    <div className="font-semibold text-zinc-950 text-xs">3,500 AI Startup Credits/mo</div>
                    <div className="text-[10.5px] sm:text-[11px] text-zinc-500 truncate">
                      Briefs, pitches &amp; contract assistance
                    </div>
                  </div>
                </div>

                {/* 6. Workspace Seat & IST Support */}
                <div className="flex items-center gap-2.5 sm:gap-3 p-2.5 sm:p-3 rounded-xl bg-white/90 border border-zinc-200/80 shadow-2xs hover:border-zinc-300 transition-all">
                  <div className="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-zinc-100 border border-zinc-200 flex items-center justify-center shrink-0 text-zinc-900">
                    <ShieldCheck className="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                  </div>
                  <div className="min-w-0">
                    <div className="font-semibold text-zinc-950 text-xs">1 Seat + Dedicated IST Support</div>
                    <div className="text-[10.5px] sm:text-[11px] text-zinc-500 truncate">
                      Priority assistance during IST hours
                    </div>
                  </div>
                </div>

              </div>
            </div>

          </div>
        </section>
      )}

      {/* ── Frequently Asked Questions ── */}
      <section className="w-full max-w-[860px] mx-auto px-3.5 sm:px-6">
        <div className="text-center mb-8 sm:mb-10">
          <div className="inline-flex items-center gap-2 px-3 py-1 bg-white rounded-full text-[10px] sm:text-[11px] font-mono font-bold tracking-[0.16em] uppercase text-zinc-700 border border-zinc-200/90 mb-3 shadow-2xs">
            <span>FREQUENTLY ASKED QUESTIONS</span>
          </div>
          <div>
            <h2 className="font-display text-2xl sm:text-4xl font-bold tracking-tight bg-gradient-to-r from-zinc-950 via-zinc-700 to-zinc-400 bg-clip-text text-transparent inline-block mb-1.5 sm:mb-2">
              Frequently asked questions.
            </h2>
          </div>
          <p className="text-xs sm:text-sm text-zinc-500 font-normal max-w-md mx-auto">
            Get quick, direct answers on plans, billing cycles, custom domains, and autonomous AI quotas.
          </p>
        </div>

        <div className="space-y-2.5 sm:space-y-3">
          {FAQS.map((faq, idx) => {
            const isOpen = openFaq === idx;
            return (
              <div
                key={idx}
                className="border border-zinc-200 rounded-xl sm:rounded-2xl overflow-hidden bg-white shadow-2xs transition-all"
              >
                <button
                  type="button"
                  onClick={() => toggleFaq(idx)}
                  className="w-full p-3.5 sm:p-5 text-left flex items-center justify-between gap-3 font-semibold text-xs sm:text-sm text-zinc-950 hover:bg-zinc-50/70 transition-colors cursor-pointer"
                >
                  <span>{faq.q}</span>
                  {isOpen ? (
                    <ChevronUp className="w-4 h-4 text-zinc-400 shrink-0" />
                  ) : (
                    <ChevronDown className="w-4 h-4 text-zinc-400 shrink-0" />
                  )}
                </button>
                {isOpen && (
                  <div className="px-3.5 sm:px-5 pb-3.5 sm:pb-5 text-xs text-zinc-600 leading-relaxed border-t border-zinc-100 pt-2.5 sm:pt-3 animate-in fade-in duration-150">
                    {faq.a}
                  </div>
                )}
              </div>
            );
          })}
        </div>

        {/* Disclaimer Note */}
        <div className="mt-6 sm:mt-8 text-center text-[10px] sm:text-[11px] text-zinc-400 font-mono">
          * Fair usage policy applies to unlimited capabilities. All prices exclude applicable local taxes (18% GST added at checkout).
        </div>

        <div className="mt-4 sm:mt-6 text-center text-[11px] sm:text-xs text-zinc-500 font-mono">
          <span>Need custom contract terms or have specific compliance questions? </span>
          <Link href="/contact" className="text-zinc-950 font-bold underline underline-offset-2 hover:text-zinc-700">
            Contact our Mumbai solutions desk &rarr;
          </Link>
        </div>
      </section>

      {/* ══════════════════════════════════════════════════════════════════════
          FLOATING STICKY BILLING CADENCE TOGGLE
      ══════════════════════════════════════════════════════════════════════ */}
      {showFloatingToggle && (
        <div className="fixed bottom-4 sm:bottom-6 inset-x-0 mx-auto w-fit z-50 animate-in fade-in slide-in-from-bottom-4 duration-300">
          <div className="inline-flex items-center p-1 sm:p-1.5 bg-white/95 backdrop-blur-xl rounded-full border border-zinc-200/90 shadow-[0_12px_40px_rgba(0,0,0,0.12)] select-none">
            <button
              type="button"
              onClick={() => {
                setBillingCycle('monthly');
                trackEvent('pricing_cycle_change_floating', { cycle: 'monthly' });
              }}
              className={`px-4 sm:px-5 py-1.5 sm:py-2 rounded-full text-[11px] sm:text-xs font-bold transition-all cursor-pointer outline-none focus:outline-none focus-visible:outline-none focus:ring-0 select-none ${
                billingCycle === 'monthly'
                  ? 'bg-zinc-950 text-white shadow-xs'
                  : 'text-zinc-600 hover:text-zinc-950'
              }`}
            >
              Monthly Billing
            </button>
            <button
              type="button"
              onClick={() => {
                setBillingCycle('annual');
                trackEvent('pricing_cycle_change_floating', { cycle: 'annual' });
              }}
              className={`px-4 sm:px-5 py-1.5 sm:py-2 rounded-full text-[11px] sm:text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer outline-none focus:outline-none focus-visible:outline-none focus:ring-0 select-none ${
                billingCycle === 'annual'
                  ? 'bg-zinc-950 text-white shadow-xs'
                  : 'text-zinc-600 hover:text-zinc-950'
              }`}
            >
              <span>Annual Billing</span>
              <span className="bg-zinc-100 text-zinc-900 border border-zinc-200/80 font-mono text-[9px] sm:text-[10px] font-extrabold px-1.5 sm:px-2 py-0.5 rounded-full shadow-2xs">
                Save 20%
              </span>
            </button>
          </div>
        </div>
      )}

    </main>
  );
}
