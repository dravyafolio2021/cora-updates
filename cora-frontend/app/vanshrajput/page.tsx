'use client';

import React, { useState, useEffect } from 'react';
import Image from 'next/image';
import Link from 'next/link';
import { 
  Check, 
  ArrowRight, 
  Sparkles, 
  TrendingUp, 
  Rocket, 
  Target, 
  Zap, 
  Video, 
  Layers, 
  GraduationCap, 
  ShieldCheck, 
  Receipt, 
  Lock, 
  Globe, 
  Users, 
  Calendar, 
  PhoneCall, 
  MessageSquare, 
  BarChart3, 
  ChevronDown, 
  ChevronUp, 
  Star, 
  Gift, 
  Award, 
  CheckCircle2, 
  Activity, 
  Play, 
  HelpCircle,
  Clock,
  ExternalLink,
  Flame,
  BadgeCheck
} from 'lucide-react';
import { trackEvent } from '@/components/analytics/Analytics';
import { triggerRazorpayCheckout, useRazorpayPreload, PlanKey } from '@/components/pricing/FastRazorpayModal';

// Vector WhatsApp Icon
const WhatsAppIcon = ({ className = "w-4 h-4" }: { className?: string }) => (
  <svg viewBox="0 0 24 24" className={className} fill="currentColor">
    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.746.953 3.71 1.456 5.711 1.457h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
  </svg>
);

const CoachFeature = ({ 
  icon: Icon = Check, 
  iconClass = "text-emerald-600 stroke-[2.2]", 
  children,
  highlight = false,
  textColor
}: { 
  icon?: React.ElementType; 
  iconClass?: string; 
  children: React.ReactNode; 
  highlight?: boolean;
  textColor?: string;
}) => (
  <li className={`flex items-start gap-2.5 min-h-[20px] text-[12px] sm:text-xs ${
    textColor ? textColor : (highlight ? 'font-semibold text-zinc-950' : 'text-zinc-700')
  }`}>
    <Icon className={`w-3.5 h-3.5 ${iconClass} shrink-0 mt-0.5`} />
    <span className="leading-snug">{children}</span>
  </li>
);

const FAQS = [
  {
    q: 'How does the 3-Day Trial for ₹999 on the Full Coaching Accelerator work?',
    a: 'When you test drive the Full Coaching Accelerator package, you start with a 3-Day full access trial for just ₹999 instead of paying the ₹49,999 annual fee upfront. You get immediate access to your custom landing page templates, WhatsApp booking sequences, CRM, and an onboarding strategy walkthrough with VNX Digital.'
  },
  {
    q: 'What is included in the 1-on-1 Training with VNX Digital?',
    a: 'Vansh Rajput and the VNX Digital technical team personally guide you through your offer positioning, landing page copy, Meta Ads server-side CAPI pixel setup, and high-ticket discovery call conversion scripts.'
  },
  {
    q: 'Why is Server-Side Meta & Google Ads Tracking so important?',
    a: 'Standard browser pixels miss 30-40% of conversion events due to iOS privacy and ad blockers. Our server-side Meta Conversions API (CAPI) and Google Ads enhanced tracking ensure 100% of booked discovery calls are reported back to ad networks, drastically improving ad ROAS and reducing Cost Per Lead.'
  },
  {
    q: 'Can I host my courses and video modules directly on the platform?',
    a: 'Yes! On the Course Authority (₹29,999) and VIP (₹49,999) packages, you get a full video LMS membership portal with DRM piracy protection, module drip releases, student login credentials, and completion certificates.'
  },
  {
    q: 'Are all prices inclusive of 18% GST?',
    a: 'Yes, 100% inclusive of 18% GST. No extra taxes or hidden fees are added at checkout. Official GST tax invoices eligible for 100% Input Tax Credit (ITC) are automatically generated upon payment.'
  }
];

export default function VanshRajputPartnerPage() {
  const [billingCycle, setBillingCycle] = useState<'monthly' | 'annual'>('annual');
  const [openFaq, setOpenFaq] = useState<number | null>(null);

  // Preload Razorpay Checkout
  useRazorpayPreload();

  // Remove any legacy partner lock to ensure normal navigation
  useEffect(() => {
    if (typeof window !== 'undefined') {
      try {
        localStorage.removeItem('cora_partner_lock');
      } catch (e) {}
    }
  }, []);

  const handleCheckout = (planKey: PlanKey) => {
    trackEvent('vnx_plan_checkout_clicked', { planKey, billingCycle });
    triggerRazorpayCheckout({
      planKey,
      billingCycle: planKey === 'vnx_trial' ? 'monthly' : billingCycle,
    });
  };

  const scrollToPricing = () => {
    document.getElementById('coaches-pricing')?.scrollIntoView({ behavior: 'smooth' });
  };

  return (
    <div className="min-h-screen bg-white text-zinc-950 font-sans selection:bg-zinc-950 selection:text-white pb-20 sm:pb-12 antialiased">
      
      {/* ══════════════════════════════════════════════════════════════════════
          PURE CUSTOM VNX DIGITAL PARTNER HEADER (NO CORA, NO GENERAL MENU)
      ══════════════════════════════════════════════════════════════════════ */}
      <header className="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-zinc-200/90 px-3.5 sm:px-8 py-3 transition-all">
        <div className="max-w-6xl mx-auto flex items-center justify-between gap-3">
          
          {/* Left: Pure VNX Digital Partner Logo */}
          <div className="flex items-center gap-2 sm:gap-3">
            <Image 
              src="/partners/vnx-digital.png" 
              alt="VNX Digital Logo" 
              width={130} 
              height={38}
              className="h-8 sm:h-9 w-auto object-contain"
              priority
            />
            <span className="hidden sm:inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-zinc-100 text-zinc-800 text-[10px] font-mono font-bold tracking-tight border border-zinc-200">
              COACHES ACCELERATOR
            </span>
          </div>

          {/* Right: Instant Mobile-First Action Button */}
          <div className="flex items-center gap-2">
            <button
              type="button"
              onClick={() => handleCheckout('vnx_trial')}
              className="inline-flex items-center gap-1.5 px-3 sm:px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-300 text-zinc-950 text-[11px] sm:text-xs font-extrabold transition-all shadow-xs cursor-pointer border border-amber-500/40"
            >
              <Sparkles className="w-3.5 h-3.5 text-zinc-950 shrink-0" />
              <span>3-Day Trial (₹999)</span>
            </button>

            <button
              type="button"
              onClick={scrollToPricing}
              className="hidden sm:inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold transition-all shadow-xs cursor-pointer"
            >
              <span>View Plans</span>
              <ArrowRight className="w-3.5 h-3.5" />
            </button>
          </div>

        </div>
      </header>

      {/* ══════════════════════════════════════════════════════════════════════
          HERO SECTION: Mobile-First High-Impact Coaching Engine
      ══════════════════════════════════════════════════════════════════════ */}
      <section className="pt-6 sm:pt-14 pb-10 sm:pb-16 px-4 sm:px-6 relative overflow-hidden bg-gradient-to-b from-zinc-50 via-white to-white border-b border-zinc-100">
        
        <div className="max-w-4xl mx-auto text-center flex flex-col items-center">
          
          {/* Top Pill Badge */}
          <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-zinc-900 text-white text-[10px] sm:text-[11px] font-mono font-bold uppercase tracking-wider mb-4 shadow-xs">
            <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
            <span>VNX Digital Coaching Accelerator</span>
          </div>

          {/* Primary Mobile-First Headline */}
          <h1 className="text-2xl sm:text-4xl md:text-5xl lg:text-6xl font-display font-extrabold text-zinc-950 tracking-tight leading-[1.18] sm:leading-[1.12] mb-4 sm:mb-6 max-w-3xl">
            Scale Your Coaching Business to <span className="underline decoration-indigo-500 decoration-2 underline-offset-4 sm:underline-offset-8">₹10L+/Month</span> With Automated Funnels &amp; Ads Tracking
          </h1>

          {/* Subtitle */}
          <p className="text-xs sm:text-base text-zinc-600 max-w-xl mx-auto leading-relaxed mb-6 sm:mb-8 font-normal">
            Done-with-you high-ticket landing pages, Meta CAPI conversion tracking, automated WhatsApp discovery call reminders, course LMS portals, and 1-on-1 strategy coaching by <strong className="text-zinc-900 font-semibold">Vansh Rajput &amp; VNX Digital</strong>.
          </p>

          {/* Mobile-First Dual CTA Buttons */}
          <div className="flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-2.5 sm:gap-3.5 w-full max-w-md mx-auto mb-6 sm:mb-8">
            <button
              type="button"
              onClick={() => handleCheckout('vnx_trial')}
              className="w-full sm:w-auto flex-1 inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-zinc-950 text-xs sm:text-sm font-extrabold transition-all shadow-md active:scale-[0.98] cursor-pointer border border-amber-500/50"
            >
              <Sparkles className="w-4 h-4 text-zinc-950" />
              <span>Start 3-Day Trial for ₹999</span>
            </button>

            <button
              type="button"
              onClick={scrollToPricing}
              className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white text-xs sm:text-sm font-bold transition-all shadow-xs active:scale-[0.98] cursor-pointer"
            >
              <span>Explore All Tiers</span>
              <ArrowRight className="w-4 h-4 text-zinc-400" />
            </button>
          </div>

          {/* Mobile Trust Badges Row */}
          <div className="grid grid-cols-2 sm:flex sm:flex-wrap items-center justify-center gap-2 sm:gap-6 text-[11px] text-zinc-600 font-medium pt-4 border-t border-zinc-200/80 w-full">
            <div className="flex items-center gap-1.5 justify-center">
              <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
              <span>100% Incl. 18% GST</span>
            </div>
            <div className="flex items-center gap-1.5 justify-center">
              <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
              <span>1-on-1 VNX Training</span>
            </div>
            <div className="flex items-center gap-1.5 justify-center">
              <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
              <span>70%+ Less No-Shows</span>
            </div>
            <div className="flex items-center gap-1.5 justify-center">
              <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
              <span>1-Click Razorpay UPI</span>
            </div>
          </div>

        </div>
      </section>

      {/* ══════════════════════════════════════════════════════════════════════
          3-DAY TRIAL EXCLUSIVE PROMO BANNER (FOR FULL COACHING ACCELERATOR)
      ══════════════════════════════════════════════════════════════════════ */}
      <section className="max-w-5xl mx-auto px-3.5 sm:px-6 my-6 sm:my-10">
        <div className="rounded-2xl sm:rounded-3xl bg-gradient-to-r from-zinc-950 via-zinc-900 to-zinc-950 text-white p-5 sm:p-7 border border-zinc-800 shadow-xl relative overflow-hidden flex flex-col sm:flex-row items-center justify-between gap-5">
          
          <div className="space-y-1.5 text-left w-full sm:w-auto">
            <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-400 text-zinc-950 text-[9px] sm:text-[10px] font-mono font-extrabold uppercase tracking-wide">
              ⚡️ FULL COACHING ACCELERATOR TRIAL
            </div>
            <h2 className="text-lg sm:text-2xl font-display font-extrabold text-white tracking-tight">
              Test Drive the Full Coaching Accelerator for 3 Days for Just ₹999
            </h2>
            <p className="text-[11px] sm:text-xs text-zinc-400 leading-relaxed max-w-lg">
              Unlock your landing page templates, WhatsApp reminder automations, LMS portal, and an onboarding strategy walkthrough from VNX Digital before committing to the ₹49,999 annual plan.
            </p>
          </div>

          <div className="flex flex-col sm:items-end shrink-0 w-full sm:w-auto">
            <button
              type="button"
              onClick={() => handleCheckout('vnx_trial')}
              className="w-full sm:w-auto px-5 py-3 rounded-xl bg-amber-400 hover:bg-amber-300 text-zinc-950 text-xs sm:text-sm font-extrabold transition-all shadow-md active:scale-[0.98] cursor-pointer flex items-center justify-center gap-2"
            >
              <span>Activate ₹999 Trial</span>
              <ArrowRight className="w-4 h-4" />
            </button>
            <span className="text-[10px] text-zinc-400 font-mono mt-1 text-center sm:text-right">
              3-Day Test Drive for Full Coaching Accelerator &bull; 100% Incl. GST
            </span>
          </div>

        </div>
      </section>

      {/* ══════════════════════════════════════════════════════════════════════
          THE 6 ESSENTIAL GROWTH PILLARS FOR COACHES
      ══════════════════════════════════════════════════════════════════════ */}
      <section className="py-8 sm:py-14 max-w-5xl mx-auto px-3.5 sm:px-6">
        <div className="text-center max-w-xl mx-auto mb-8 sm:mb-12">
          <span className="text-[10px] sm:text-[11px] font-mono font-bold uppercase tracking-wider text-indigo-800 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-200">
            6 COACHING GROWTH PILLARS
          </span>
          <h2 className="text-xl sm:text-3xl font-display font-extrabold text-zinc-950 tracking-tight mt-2.5 mb-2">
            Everything Required to Scale Your Coaching Funnel
          </h2>
          <p className="text-xs sm:text-sm text-zinc-600">
            VNX Digital provides the complete end-to-end client acquisition setup.
          </p>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
          
          {/* Card 1 */}
          <div className="p-4 sm:p-5 rounded-2xl bg-zinc-50 border border-zinc-200/80 hover:border-zinc-300 transition-all">
            <div className="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center mb-3 font-bold">
              <Globe className="w-4 h-4" />
            </div>
            <h3 className="font-display font-bold text-sm sm:text-base text-zinc-950 mb-1.5">
              High-Converting Funnels
            </h3>
            <p className="text-[11px] sm:text-xs text-zinc-600 leading-relaxed">
              Mobile-first VSL pages, discovery call application forms, and webinar registration funnels loading in under 50ms with custom domains.
            </p>
          </div>

          {/* Card 2 */}
          <div className="p-4 sm:p-5 rounded-2xl bg-zinc-50 border border-zinc-200/80 hover:border-zinc-300 transition-all">
            <div className="w-9 h-9 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center mb-3 font-bold">
              <BarChart3 className="w-4 h-4" />
            </div>
            <h3 className="font-display font-bold text-sm sm:text-base text-zinc-950 mb-1.5">
              Meta &amp; Google Ads Tracking
            </h3>
            <p className="text-[11px] sm:text-xs text-zinc-600 leading-relaxed">
              Server-side Meta CAPI, Google Enhanced Conversions, and UTM attribution so you know your exact Cost Per Booked Call and ROAS.
            </p>
          </div>

          {/* Card 3 */}
          <div className="p-4 sm:p-5 rounded-2xl bg-zinc-50 border border-zinc-200/80 hover:border-zinc-300 transition-all">
            <div className="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center mb-3 font-bold">
              <WhatsAppIcon className="w-4 h-4 text-emerald-600" />
            </div>
            <h3 className="font-display font-bold text-sm sm:text-base text-zinc-950 mb-1.5">
              WhatsApp Reminder Sequences
            </h3>
            <p className="text-[11px] sm:text-xs text-zinc-600 leading-relaxed">
              Automated booking confirmations, calendar invites, and 1-hour call alerts on WhatsApp—slashing discovery call no-shows by 70%+.
            </p>
          </div>

          {/* Card 4 */}
          <div className="p-4 sm:p-5 rounded-2xl bg-zinc-50 border border-zinc-200/80 hover:border-zinc-300 transition-all">
            <div className="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center mb-3 font-bold">
              <GraduationCap className="w-4 h-4" />
            </div>
            <h3 className="font-display font-bold text-sm sm:text-base text-zinc-950 mb-1.5">
              1-on-1 Coaching &amp; Training
            </h3>
            <p className="text-[11px] sm:text-xs text-zinc-600 leading-relaxed">
              Direct personal sessions with Vansh Rajput &amp; the VNX Digital team covering offer pricing, ad copy frameworks, and funnel optimization.
            </p>
          </div>

          {/* Card 5 */}
          <div className="p-4 sm:p-5 rounded-2xl bg-zinc-50 border border-zinc-200/80 hover:border-zinc-300 transition-all">
            <div className="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center mb-3 font-bold">
              <Video className="w-4 h-4" />
            </div>
            <h3 className="font-display font-bold text-sm sm:text-base text-zinc-950 mb-1.5">
              Course &amp; Membership LMS
            </h3>
            <p className="text-[11px] sm:text-xs text-zinc-600 leading-relaxed">
              Host your video courses, masterclasses, and student communities with built-in DRM protection, progress tracking, and certificates.
            </p>
          </div>

          {/* Card 6 */}
          <div className="p-4 sm:p-5 rounded-2xl bg-zinc-50 border border-zinc-200/80 hover:border-zinc-300 transition-all">
            <div className="w-9 h-9 rounded-xl bg-pink-100 text-pink-700 flex items-center justify-center mb-3 font-bold">
              <Receipt className="w-4 h-4" />
            </div>
            <h3 className="font-display font-bold text-sm sm:text-base text-zinc-950 mb-1.5">
              UPI QR &amp; 18% GST Invoicing
            </h3>
            <p className="text-[11px] sm:text-xs text-zinc-600 leading-relaxed">
              Collect fees via UPI (0% merchant charges) or credit cards, and automatically generate compliant Indian GST invoices for your clients.
            </p>
          </div>

        </div>
      </section>

      {/* ══════════════════════════════════════════════════════════════════════
          PRIMARY COACHES PRICING TIERS (4 TIERS)
      ══════════════════════════════════════════════════════════════════════ */}
      <section id="coaches-pricing" className="py-12 sm:py-16 bg-zinc-50 border-y border-zinc-200/80">
        <div className="max-w-6xl mx-auto px-3.5 sm:px-6">
          
          <div className="text-center max-w-2xl mx-auto mb-8 sm:mb-10">
            <span className="text-[10px] sm:text-[11px] font-mono font-bold uppercase tracking-wider text-emerald-800 bg-emerald-100/80 px-3.5 py-1 rounded-full border border-emerald-300">
              TRANSPARENT COACHES PACKAGES
            </span>
            <h2 className="text-2xl sm:text-4xl font-display font-extrabold text-zinc-950 tracking-tight mt-2.5 mb-2">
              Choose the Growth Tier for Your Coaching Business
            </h2>
            <p className="text-xs sm:text-sm text-zinc-600">
              All plans are 100% inclusive of 18% GST. Instant 1-click Razorpay activation.
            </p>

            {/* Monthly / Annual Switcher */}
            <div className="inline-flex items-center p-1 bg-zinc-200/80 rounded-xl mt-5 border border-zinc-300/80">
              <button
                type="button"
                onClick={() => setBillingCycle('annual')}
                className={`px-3.5 py-1.5 text-xs font-bold rounded-lg transition-all flex items-center gap-1.5 cursor-pointer ${
                  billingCycle === 'annual' ? 'bg-zinc-950 text-white shadow-2xs' : 'text-zinc-600 hover:text-zinc-900'
                }`}
              >
                <span>Annual Billing</span>
                <span className="px-1.5 py-0.2 rounded bg-emerald-500 text-zinc-950 font-mono text-[9px] font-bold">
                  2 Mo. FREE
                </span>
              </button>
              <button
                type="button"
                onClick={() => setBillingCycle('monthly')}
                className={`px-3.5 py-1.5 text-xs font-bold rounded-lg transition-all cursor-pointer ${
                  billingCycle === 'monthly' ? 'bg-white text-zinc-950 shadow-2xs' : 'text-zinc-600 hover:text-zinc-900'
                }`}
              >
                Monthly Billing
              </button>
            </div>
          </div>

          {/* 4-COLUMN PRICING GRID */}
          <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6 items-stretch">
            
            {/* ── CARD 1: ₹9,999 / mo ── */}
            <div className="bg-white rounded-2xl sm:rounded-3xl border border-zinc-200/90 p-5 sm:p-6 flex flex-col justify-between shadow-xs hover:border-zinc-300 transition-all">
              <div>
                <div className="flex items-center justify-between gap-2 mb-3">
                  <span className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-600 bg-zinc-100 px-2.5 py-0.5 rounded-md">
                    Starter Funnel
                  </span>
                  {billingCycle === 'annual' && (
                    <span className="text-[10px] font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                      2 Mo. FREE
                    </span>
                  )}
                </div>

                <h3 className="font-display text-lg sm:text-xl font-bold text-zinc-950 mb-1">
                  Landing Page &amp; Training
                </h3>
                <p className="text-[11px] sm:text-xs text-zinc-500 leading-relaxed mb-4 min-h-[34px]">
                  Launch your high-converting coach landing page with personal strategy training.
                </p>

                {/* Price Display */}
                <div className="mb-4 pb-4 border-b border-zinc-100">
                  <div className="flex items-baseline gap-1">
                    <span className="text-3xl sm:text-4xl font-display font-extrabold text-zinc-950">
                      ₹{billingCycle === 'annual' ? '8,333' : '9,999'}
                    </span>
                    <span className="text-xs text-zinc-500 font-medium">/ mo</span>
                  </div>
                  <div className="text-[11px] text-zinc-500 mt-1 flex flex-col gap-0.5">
                    {billingCycle === 'annual' ? (
                      <>
                        <span className="text-emerald-700 font-semibold">Billed annually: ₹99,990 / yr (Save ₹19,998)</span>
                        <span className="text-[10px] text-zinc-400">Includes 18% GST (12 mos. for 10)</span>
                      </>
                    ) : (
                      <span className="text-zinc-500">Billed monthly &bull; Incl. 18% GST</span>
                    )}
                  </div>
                </div>

                {/* Features List */}
                <ul className="space-y-2 text-xs mb-6">
                  <CoachFeature highlight>
                    Custom Coach Landing Page &amp; Funnel
                  </CoachFeature>
                  <CoachFeature highlight>
                    1-on-1 Complete Setup &amp; Training by VNX
                  </CoachFeature>
                  <CoachFeature>
                    Automated Call Booking Engine
                  </CoachFeature>
                  <CoachFeature>
                    WhatsApp &amp; Email Lead Alerts
                  </CoachFeature>
                  <CoachFeature>
                    Discovery Call Kanban CRM
                  </CoachFeature>
                  <CoachFeature>
                    5,000 Monthly AI Runs
                  </CoachFeature>
                </ul>
              </div>

              <div>
                <button
                  type="button"
                  onClick={() => handleCheckout('vnx_starter')}
                  className="w-full py-3.5 px-4 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-bold transition-all shadow-xs cursor-pointer text-center block"
                >
                  {billingCycle === 'annual' ? 'Pay Annual (₹99,990)' : 'Get Starter Monthly'}
                </button>
              </div>
            </div>

            {/* ── CARD 2: ₹14,999 / mo (MOST POPULAR) ── */}
            <div className="bg-white rounded-2xl sm:rounded-3xl border-2 border-indigo-600 p-5 sm:p-6 flex flex-col justify-between shadow-xl relative overflow-visible hover:shadow-2xl transition-all">
              
              {/* Highlight Badge */}
              <div className="absolute -top-3 left-1/2 -translate-x-1/2 z-20 whitespace-nowrap">
                <span className="px-3 py-1 rounded-full bg-indigo-600 text-white text-[9px] sm:text-[10px] font-mono font-bold uppercase tracking-wider shadow-md">
                  ★ MOST POPULAR FOR COACHES
                </span>
              </div>

              <div>
                <div className="flex items-center justify-between gap-2 mb-3 mt-1">
                  <span className="text-xs font-mono font-bold uppercase tracking-wider text-indigo-700 bg-indigo-50 px-2.5 py-0.5 rounded-md">
                    Growth Engine
                  </span>
                  {billingCycle === 'annual' && (
                    <span className="text-[10px] font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                      2 Mo. FREE
                    </span>
                  )}
                </div>

                <h3 className="font-display text-lg sm:text-xl font-bold text-zinc-950 mb-1">
                  Website, Ads Tracking &amp; Training
                </h3>
                <p className="text-[11px] sm:text-xs text-zinc-500 leading-relaxed mb-4 min-h-[34px]">
                  Full multi-page site, deep Meta &amp; Google Ads tracking, and automated reminders.
                </p>

                {/* Price Display */}
                <div className="mb-4 pb-4 border-b border-zinc-100">
                  <div className="flex items-baseline gap-1">
                    <span className="text-3xl sm:text-4xl font-display font-extrabold text-zinc-950">
                      ₹{billingCycle === 'annual' ? '12,499' : '14,999'}
                    </span>
                    <span className="text-xs text-zinc-500 font-medium">/ mo</span>
                  </div>
                  <div className="text-[11px] text-zinc-500 mt-1 flex flex-col gap-0.5">
                    {billingCycle === 'annual' ? (
                      <>
                        <span className="text-emerald-700 font-semibold">Billed annually: ₹149,990 / yr (Save ₹29,998)</span>
                        <span className="text-[10px] text-zinc-400">Includes 18% GST (12 mos. for 10)</span>
                      </>
                    ) : (
                      <span className="text-zinc-500">Billed monthly &bull; Incl. 18% GST</span>
                    )}
                  </div>
                </div>

                {/* Features List */}
                <ul className="space-y-2 text-xs mb-6">
                  <CoachFeature highlight iconClass="text-indigo-600 stroke-[2.2]">
                    Everything in Starter, plus:
                  </CoachFeature>
                  <CoachFeature highlight>
                    Full Multi-Page Website &amp; Funnel
                  </CoachFeature>
                  <CoachFeature highlight>
                    Meta Pixel + Server-Side CAPI
                  </CoachFeature>
                  <CoachFeature highlight>
                    Google Ads Enhanced Tracking
                  </CoachFeature>
                  <CoachFeature>
                    WhatsApp Automated Call Reminders
                  </CoachFeature>
                  <CoachFeature>
                    Dynamic UPI QR &amp; GST Invoicing
                  </CoachFeature>
                  <CoachFeature>
                    15,000 Monthly AI Reasoning Runs
                  </CoachFeature>
                  <CoachFeature>
                    Advanced 1-on-1 Strategy by VNX
                  </CoachFeature>
                </ul>
              </div>

              <div>
                <button
                  type="button"
                  onClick={() => handleCheckout('vnx_growth')}
                  className="w-full py-3.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition-all shadow-md cursor-pointer text-center block"
                >
                  {billingCycle === 'annual' ? 'Get Growth Engine (₹149,990)' : 'Get Growth Monthly'}
                </button>
              </div>
            </div>

            {/* ── CARD 3: ₹29,999 / mo ── */}
            <div className="bg-white rounded-2xl sm:rounded-3xl border border-zinc-200/90 p-5 sm:p-6 flex flex-col justify-between shadow-xs hover:border-zinc-300 transition-all">
              <div>
                <div className="flex items-center justify-between gap-2 mb-3">
                  <span className="text-xs font-mono font-bold uppercase tracking-wider text-purple-700 bg-purple-50 px-2.5 py-0.5 rounded-md">
                    Course Authority
                  </span>
                  {billingCycle === 'annual' && (
                    <span className="text-[10px] font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                      2 Mo. FREE
                    </span>
                  )}
                </div>

                <h3 className="font-display text-lg sm:text-xl font-bold text-zinc-950 mb-1">
                  Site, Ads, Training &amp; Course LMS
                </h3>
                <p className="text-[11px] sm:text-xs text-zinc-500 leading-relaxed mb-4 min-h-[34px]">
                  Full course &amp; membership video hosting LMS portal with multi-tier funnels.
                </p>

                {/* Price Display */}
                <div className="mb-4 pb-4 border-b border-zinc-100">
                  <div className="flex items-baseline gap-1">
                    <span className="text-3xl sm:text-4xl font-display font-extrabold text-zinc-950">
                      ₹{billingCycle === 'annual' ? '24,999' : '29,999'}
                    </span>
                    <span className="text-xs text-zinc-500 font-medium">/ mo</span>
                  </div>
                  <div className="text-[11px] text-zinc-500 mt-1 flex flex-col gap-0.5">
                    {billingCycle === 'annual' ? (
                      <>
                        <span className="text-emerald-700 font-semibold">Billed annually: ₹299,990 / yr (Save ₹59,998)</span>
                        <span className="text-[10px] text-zinc-400">Includes 18% GST (12 mos. for 10)</span>
                      </>
                    ) : (
                      <span className="text-zinc-500">Billed monthly &bull; Incl. 18% GST</span>
                    )}
                  </div>
                </div>

                {/* Features List */}
                <ul className="space-y-2 text-xs mb-6">
                  <CoachFeature highlight iconClass="text-purple-600 stroke-[2.2]">
                    Everything in Growth, plus:
                  </CoachFeature>
                  <CoachFeature highlight>
                    Full Video Course &amp; Membership LMS
                  </CoachFeature>
                  <CoachFeature highlight>
                    DRM Video Piracy Protection
                  </CoachFeature>
                  <CoachFeature>
                    Student Login &amp; Auto Certificates
                  </CoachFeature>
                  <CoachFeature>
                    Multi-Tier Funnels (VSL + Upsells)
                  </CoachFeature>
                  <CoachFeature>
                    30,000 Monthly Frontier AI Actions
                  </CoachFeature>
                  <CoachFeature>
                    Weekly 1-on-1 Mentorship by VNX
                  </CoachFeature>
                </ul>
              </div>

              <div>
                <button
                  type="button"
                  onClick={() => handleCheckout('vnx_scale')}
                  className="w-full py-3.5 px-4 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-bold transition-all shadow-xs cursor-pointer text-center block"
                >
                  {billingCycle === 'annual' ? 'Get Scale LMS (₹299,990)' : 'Get Scale Monthly'}
                </button>
              </div>
            </div>

            {/* ── CARD 4: ₹49,999 ANNUAL VIP ACCELERATOR ── */}
            <div className="bg-gradient-to-b from-amber-500/10 via-white to-white rounded-2xl sm:rounded-3xl border-2 border-amber-400/90 p-5 sm:p-6 flex flex-col justify-between shadow-lg hover:shadow-xl transition-all">
              <div>
                <div className="flex items-center justify-between gap-2 mb-3">
                  <span className="text-xs font-mono font-bold uppercase tracking-wider text-amber-900 bg-amber-200/80 px-2.5 py-0.5 rounded-md">
                    VIP Done-For-You
                  </span>
                  <span className="text-[10px] font-mono font-bold text-amber-950 bg-amber-400 px-2 py-0.5 rounded">
                    ANNUAL PASS
                  </span>
                </div>

                <h3 className="font-display text-lg sm:text-xl font-bold text-zinc-950 mb-1">
                  Full Coaching Accelerator
                </h3>
                <p className="text-[11px] sm:text-xs text-zinc-600 leading-relaxed mb-4 min-h-[34px]">
                  Turnkey Done-For-You build with dedicated private Slack &amp; 24/7 VIP support.
                </p>

                {/* Price Display */}
                <div className="mb-4 pb-4 border-b border-amber-200/60">
                  <div className="flex items-baseline gap-1">
                    <span className="text-3xl sm:text-4xl font-display font-extrabold text-zinc-950">
                      ₹49,999
                    </span>
                    <span className="text-xs text-zinc-500 font-medium">/ year</span>
                  </div>
                  <div className="text-[11px] text-zinc-500 mt-1 flex flex-col gap-0.5">
                    <span className="text-emerald-700 font-semibold">Effective ₹4,166 / mo (12 Months Access)</span>
                    <span className="text-[10px] text-zinc-400">100% Incl. 18% GST &bull; Annual Package</span>
                  </div>
                </div>

                {/* Features List */}
                <ul className="space-y-2 text-xs mb-6">
                  <CoachFeature highlight iconClass="text-amber-600 stroke-[2.2]">
                    Complete Done-For-You Build by VNX
                  </CoachFeature>
                  <CoachFeature highlight>
                    Sales Copy &amp; VSL Script Review
                  </CoachFeature>
                  <CoachFeature>
                    Full LMS Portal + Unlimited Bandwidth
                  </CoachFeature>
                  <CoachFeature>
                    Custom Meta &amp; Google Ads Setup
                  </CoachFeature>
                  <CoachFeature>
                    Dedicated Private Slack / WhatsApp
                  </CoachFeature>
                  <CoachFeature>
                    Unlimited AI Runs &amp; Lead Enrichment
                  </CoachFeature>
                </ul>
              </div>

              <div className="space-y-2">
                <button
                  type="button"
                  onClick={() => handleCheckout('vnx_trial')}
                  className="w-full py-2.5 px-3 rounded-xl bg-amber-400 hover:bg-amber-300 text-zinc-950 text-xs font-extrabold transition-all shadow-xs cursor-pointer text-center block border border-amber-500/60"
                >
                  ⚡️ Start 3-Day Trial (₹999)
                </button>
                <button
                  type="button"
                  onClick={() => handleCheckout('vnx_vip')}
                  className="w-full py-3 px-4 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold transition-all shadow-xs cursor-pointer text-center block"
                >
                  Get VIP Annual (₹49,999)
                </button>
              </div>
            </div>

          </div>

          {/* Tax & Invoice Assurance */}
          <div className="mt-6 text-center text-xs text-zinc-500 flex items-center justify-center gap-1.5 font-mono">
            <Receipt className="w-3.5 h-3.5 text-zinc-600" />
            <span>All packages 100% inclusive of 18% GST. Instant 1-click Razorpay checkout with official tax invoice.</span>
          </div>

        </div>
      </section>

      {/* ══════════════════════════════════════════════════════════════════════
          COST SAVINGS BREAKDOWN MATRIX (₹1,00,000+/MO COMPARISON)
      ══════════════════════════════════════════════════════════════════════ */}
      <section className="py-10 sm:py-16 max-w-4xl mx-auto px-3.5 sm:px-6">
        <div className="text-center mb-8">
          <span className="text-[10px] sm:text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-700 bg-zinc-100 px-3 py-1 rounded-full">
            SAVE ₹1,00,000+/MONTH ON SOFTWARE &amp; AGENCIES
          </span>
          <h2 className="text-xl sm:text-3xl font-display font-extrabold text-zinc-950 tracking-tight mt-2.5">
            Why Stitching Fragmented Tools &amp; Agencies Costs You ₹1L+/Month
          </h2>
        </div>

        <div className="rounded-2xl border border-zinc-200 overflow-hidden shadow-xs">
          <div className="grid grid-cols-2 bg-zinc-950 text-white p-3 sm:p-4 text-xs sm:text-sm font-bold">
            <div>Fragmented Agency &amp; Software Stack</div>
            <div className="text-emerald-400">VNX Digital Coaching Engine</div>
          </div>
          
          <div className="divide-y divide-zinc-100 text-[11px] sm:text-xs text-zinc-700 bg-white">
            <div className="grid grid-cols-2 p-3 items-center">
              <div>Meta &amp; Google Ads Agency Retainer: ₹45,000/mo</div>
              <div className="text-emerald-700 font-semibold">✓ Server-Side CAPI &amp; Ads Setup Included</div>
            </div>
            <div className="grid grid-cols-2 p-3 items-center">
              <div>High-Ticket Funnel Strategy Consultant: ₹25,000/mo</div>
              <div className="text-emerald-700 font-semibold">✓ 1-on-1 Mentorship &amp; Script Reviews Included</div>
            </div>
            <div className="grid grid-cols-2 p-3 items-center">
              <div>ClickFunnels / Kajabi Funnel Builder: ₹18,000/mo</div>
              <div className="text-emerald-700 font-semibold">✓ Sub-50ms High-Converting Funnels Included</div>
            </div>
            <div className="grid grid-cols-2 p-3 items-center">
              <div>Teachable / Skool Course LMS Hosting: ₹10,000/mo</div>
              <div className="text-emerald-700 font-semibold">✓ DRM Video Hosting &amp; Student LMS Included</div>
            </div>
            <div className="grid grid-cols-2 p-3 items-center">
              <div>ManyChat / Wati WhatsApp Marketing: ₹6,500/mo</div>
              <div className="text-emerald-700 font-semibold">✓ Automated WhatsApp Sequences Included</div>
            </div>
            <div className="grid grid-cols-2 p-3 items-center">
              <div>Calendly Pro + Zapier Automation Engine: ₹5,500/mo</div>
              <div className="text-emerald-700 font-semibold">✓ Native 0-Delay Booking &amp; CRM Included</div>
            </div>
            <div className="grid grid-cols-2 p-3.5 items-center bg-zinc-50 font-bold text-zinc-950 text-xs">
              <div className="text-red-700">Total Normal Cost: ₹1,10,000+/mo</div>
              <div className="text-emerald-800">From ₹9,999/mo (Everything + Training Included)</div>
            </div>
          </div>
        </div>
      </section>

      {/* ══════════════════════════════════════════════════════════════════════
          FREQUENTLY ASKED QUESTIONS (FAQ)
      ══════════════════════════════════════════════════════════════════════ */}
      <section className="py-10 sm:py-14 max-w-3xl mx-auto px-3.5 sm:px-6">
        <div className="text-center mb-8">
          <span className="text-[10px] sm:text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-600 bg-zinc-100 px-3 py-1 rounded-full">
            CLARITY &amp; DETAILS
          </span>
          <h2 className="text-xl sm:text-2xl font-display font-extrabold text-zinc-950 tracking-tight mt-2.5">
            Frequently Asked Questions
          </h2>
        </div>

        <div className="space-y-2.5">
          {FAQS.map((faq, idx) => {
            const isOpen = openFaq === idx;
            return (
              <div 
                key={idx}
                className="rounded-2xl border border-zinc-200/80 bg-white overflow-hidden transition-all"
              >
                <button
                  type="button"
                  onClick={() => setOpenFaq(isOpen ? null : idx)}
                  className="w-full p-4 text-left flex items-center justify-between gap-3 font-semibold text-xs sm:text-sm text-zinc-900 hover:text-indigo-600 transition-colors cursor-pointer"
                >
                  <span>{faq.q}</span>
                  {isOpen ? (
                    <ChevronUp className="w-4 h-4 text-zinc-500 shrink-0" />
                  ) : (
                    <ChevronDown className="w-4 h-4 text-zinc-500 shrink-0" />
                  )}
                </button>
                {isOpen && (
                  <div className="px-4 pb-4 text-xs text-zinc-600 leading-relaxed border-t border-zinc-100 pt-2.5 animate-in fade-in duration-200">
                    {faq.a}
                  </div>
                )}
              </div>
            );
          })}
        </div>
      </section>

      {/* ══════════════════════════════════════════════════════════════════════
          CUSTOM PARTNER FOOTER (NO GENERAL CORA FOOTER)
      ══════════════════════════════════════════════════════════════════════ */}
      <footer className="mt-12 pt-8 pb-12 border-t border-zinc-200 bg-zinc-50/80 text-zinc-600 text-xs px-4 sm:px-6">
        <div className="max-w-5xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6 text-center md:text-left">
          
          <div className="space-y-2">
            <Image 
              src="/partners/vnx-digital.png" 
              alt="VNX Digital" 
              width={120} 
              height={34}
              className="h-7 w-auto object-contain mx-auto md:mx-0"
            />
            <p className="text-[11px] text-zinc-500 max-w-sm">
              The high-ticket client acquisition and conversion tracking growth engine for coaches, mentors, and consultants.
            </p>
          </div>

          <div className="flex flex-col sm:flex-row items-center gap-4 text-xs font-medium text-zinc-700">
            <a 
              href="https://wa.me/918708528105?text=Hi%20Vansh,%20I%20have%20a%20question%20about%20the%20Coaching%20Growth%20Engine" 
              target="_blank" 
              rel="noopener noreferrer"
              className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-zinc-200 text-zinc-800 hover:text-emerald-700 shadow-2xs transition-colors"
            >
              <WhatsAppIcon className="w-4 h-4 text-emerald-600" />
              <span>WhatsApp Strategy Support</span>
            </a>

            <button
              type="button"
              onClick={() => handleCheckout('vnx_trial')}
              className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-400 text-zinc-950 font-bold hover:bg-amber-300 shadow-2xs transition-colors cursor-pointer"
            >
              <Sparkles className="w-3.5 h-3.5" />
              <span>3-Day Trial (₹999)</span>
            </button>
          </div>

        </div>

        <div className="max-w-5xl mx-auto mt-8 pt-4 border-t border-zinc-200/80 flex flex-col sm:flex-row items-center justify-between gap-2 text-[10px] text-zinc-500 font-mono">
          <span>&copy; {new Date().getFullYear()} VNX Digital. All rights reserved.</span>
          <span>100% Inclusive of 18% GST &bull; Secure UPI &amp; Card Payments</span>
        </div>
      </footer>

      {/* ══════════════════════════════════════════════════════════════════════
          MOBILE-ONLY STICKY BOTTOM ACTION BAR (TOUCH OPTIMIZED)
      ══════════════════════════════════════════════════════════════════════ */}
      <div className="fixed bottom-0 left-0 right-0 p-3 bg-white/95 backdrop-blur-md border-t border-zinc-200 z-40 sm:hidden flex items-center justify-between gap-2.5 shadow-2xl">
        <div className="flex flex-col">
          <span className="text-[10px] font-mono text-zinc-500 uppercase font-bold">VNX Accelerator</span>
          <span className="text-xs font-display font-bold text-zinc-950">₹999 / 3-Day Trial</span>
        </div>
        <button
          type="button"
          onClick={() => handleCheckout('vnx_trial')}
          className="flex-1 py-2.5 px-4 rounded-xl bg-amber-400 active:bg-amber-500 text-zinc-950 text-xs font-extrabold shadow-sm flex items-center justify-center gap-1.5 cursor-pointer border border-amber-500/40"
        >
          <Sparkles className="w-3.5 h-3.5 text-zinc-950" />
          <span>Start ₹999 Trial</span>
        </button>
      </div>

    </div>
  );
}
