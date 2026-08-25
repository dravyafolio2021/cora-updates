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
  Receipt,
  Globe,
  Zap,
  Lock
} from 'lucide-react';
import { trackEvent } from '@/components/analytics/Analytics';

const FAQS = [
  {
    q: 'Is the Free Forever plan really free?',
    a: 'Yes, 100% free forever. It includes 1,000 complimentary AI agent runs every month, website builder (on heycora.in/your-name subdomain), Kanban CRM, unlimited tamper-evident SHA-256 e-signatures, and automated GST invoicing with zero credit card required. Custom domain and custom email connection require an upgrade to a paid Growth plan.'
  },
  {
    q: 'What is the main difference between Starter and Professional?',
    a: 'Starter gives you custom domain connection (yourbrand.com), custom email dispatch, and 5,000 monthly AI runs. Professional upgrades your AI intelligence to Claude 3.5 Sonnet & GPT-4o mini, increases runs to 20,000/mo, adds Meta WhatsApp automated client dispatch, Dynamic UPI QR codes on invoices, and expands team seats to 5.'
  },
  {
    q: 'What perks come with an Annual plan?',
    a: 'When you choose an Annual plan on any paid tier, you receive a complimentary 1-year custom domain registration (.com, .in, or .co) with automated SSL, plus 12,000 additional AI agent runs distributed evenly (+1,000 bonus runs every month) across the year.'
  },
  {
    q: 'Why is the India Only plan strictly annual?',
    a: 'The India MSME Edition is heavily subsidised at ₹499/month specifically to support registered Indian businesses and founders with long-term operational infrastructure. It is only available as an annual commitment (₹5,988/year) and cannot be billed monthly.'
  },
  {
    q: 'Can I upgrade, downgrade, or cancel anytime?',
    a: 'Yes. You can manage your tier anytime directly in your workspace settings. If you change plans, your historical contracts, invoices, and clients remain fully accessible and protected.'
  },
  {
    q: 'Is our financial and client data secure?',
    a: 'All data is protected by AES-256 encryption at rest and TLS 1.3 in transit. All e-signatures are SHA-256 cryptographically sealed and legally compliant under the Indian IT Act 2000 and DPDP Act 2023.'
  }
];

export default function PricingPage() {
  const [billingCycle, setBillingCycle] = useState<'monthly' | 'annual'>('annual');
  const [currency, setCurrency] = useState<'INR' | 'USD'>('USD');
  const [isIndia, setIsIndia] = useState(false);
  const [showTable, setShowTable] = useState(false);
  const [openFaq, setOpenFaq] = useState<number | null>(null);
  const comparisonRef = useRef<HTMLDivElement>(null);

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
      setCurrency('USD');
      setIsIndia(false);
    }
  }, []);

  const toggleFaq = (index: number) => {
    setOpenFaq(openFaq === index ? null : index);
  };

  const handleToggleTable = () => {
    const nextState = !showTable;
    setShowTable(nextState);
    if (nextState) {
      setTimeout(() => {
        comparisonRef.current?.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }, 100);
    }
  };

  return (
    <main className="w-full relative pb-24 overflow-hidden bg-white text-zinc-900">
      
      {/* ── Ethereal Pure Cloud Sky Hero Section ── */}
      <section className="relative w-full overflow-hidden bg-gradient-to-b from-[#56a2e8] via-[#cae4fc] to-white pt-28 sm:pt-36 pb-14 sm:pb-20">
        
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
          <div className="absolute inset-x-0 bottom-0 h-40 sm:h-52 bg-gradient-to-t from-white via-white/90 to-transparent pointer-events-none" />
        </div>

        {/* Hero Content */}
        <div className="relative z-10 w-full max-w-[1240px] mx-auto px-4 sm:px-6 flex flex-col items-center text-center">
          
          {/* Announcement Pill */}
          <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-white/95 backdrop-blur-md rounded-full shadow-[0px_2px_8px_rgba(0,0,0,0.05)] border border-white/80 text-xs font-medium text-zinc-800 mb-4">
            <span className="flex h-2 w-2 relative">
              <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-600 opacity-75"></span>
              <span className="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
            </span>
            <span>Simple, transparent plans &bull; No hidden fees</span>
          </div>

          {/* Heading */}
          <h1 className="font-display text-4xl xs:text-5xl sm:text-6xl font-bold text-zinc-950 leading-[1.1] tracking-[-0.035em] max-w-[760px] mx-auto mb-3">
            Plans that fit your business
          </h1>

          {/* Body */}
          <p className="text-zinc-700 text-base sm:text-lg font-normal leading-relaxed max-w-[540px] mx-auto">
            Start free forever with 1,000 monthly AI runs. Upgrade anytime as you grow.
          </p>

        </div>
      </section>

      {/* ══════════════════════════════════════════════════════════════════════
          ROW 1: FREE FOREVER USP SHOWCASE
      ══════════════════════════════════════════════════════════════════════ */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-16 -mt-6 sm:-mt-10 relative z-10">
        <div className="bg-white border border-zinc-200 rounded-[28px] p-6 sm:p-8 shadow-[0_4px_24px_rgba(0,0,0,0.03)] hover:shadow-[0_8px_32px_rgba(0,0,0,0.05)] transition-all">
          
          <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-zinc-100">
            <div className="space-y-2 max-w-[700px]">
              <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50/80 border border-emerald-200/60 text-xs font-semibold text-emerald-800">
                <span className="flex h-1.5 w-1.5 rounded-full bg-emerald-500" />
                <span>100% Free Forever</span>
                <span className="text-emerald-300">&bull;</span>
                <span className="font-normal text-emerald-700">No credit card required</span>
              </div>

              <h2 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight">
                Free Forever Plan
              </h2>

              <p className="text-xs sm:text-sm text-zinc-500 leading-relaxed">
                Everything you need to launch and operate your business. Build your website, manage client pipeline, send proposals, and execute legally binding contracts with zero upfront cost.
              </p>
            </div>

            <div className="flex flex-col sm:items-end justify-center gap-2.5 shrink-0">
              <a
                href="https://app.heycora.in/workspace/login?plan=free"
                className="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-zinc-950 hover:bg-zinc-800 text-white px-6 py-3 rounded-xl text-xs sm:text-sm font-semibold transition-all shadow-sm hover:shadow-md"
              >
                <span>Get Started for Free</span>
                <ArrowRight className="w-4 h-4 text-zinc-400" />
              </a>
              <div className="flex items-center gap-3 text-[11px] font-mono text-zinc-400">
                <span>⚡ 30-Sec Setup</span>
                <span>&bull;</span>
                <span>🔒 Full Data Ownership</span>
              </div>
            </div>
          </div>

          <div className="pt-6">
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
              
              <div className="flex items-start gap-3 p-3.5 rounded-xl bg-zinc-50/60 border border-zinc-100 hover:border-zinc-200/80 hover:bg-zinc-50 transition-all">
                <div className="w-7 h-7 rounded-lg bg-white border border-zinc-200/80 flex items-center justify-center shrink-0 mt-0.5 text-zinc-800 shadow-2xs">
                  <Layout className="w-3.5 h-3.5" />
                </div>
                <div className="min-w-0">
                  <div className="font-semibold text-zinc-900 text-xs">Website Builder</div>
                  <div className="text-[11px] text-zinc-500 mt-0.5 leading-snug">
                    Portfolio &amp; booking pages live on <span className="font-mono text-zinc-700">heycora.in/your-name</span>
                  </div>
                </div>
              </div>

              <div className="flex items-start gap-3 p-3.5 rounded-xl bg-zinc-50/60 border border-zinc-100 hover:border-zinc-200/80 hover:bg-zinc-50 transition-all">
                <div className="w-7 h-7 rounded-lg bg-white border border-zinc-200/80 flex items-center justify-center shrink-0 mt-0.5 text-zinc-800 shadow-2xs">
                  <ShieldCheck className="w-3.5 h-3.5" />
                </div>
                <div className="min-w-0">
                  <div className="font-semibold text-zinc-900 text-xs">Kanban Client CRM</div>
                  <div className="text-[11px] text-zinc-500 mt-0.5 leading-snug">
                    Track leads, inquiry stages &amp; shoot schedules
                  </div>
                </div>
              </div>

              <div className="flex items-start gap-3 p-3.5 rounded-xl bg-zinc-50/60 border border-zinc-100 hover:border-zinc-200/80 hover:bg-zinc-50 transition-all">
                <div className="w-7 h-7 rounded-lg bg-white border border-zinc-200/80 flex items-center justify-center shrink-0 mt-0.5 text-amber-600 shadow-2xs">
                  <Sparkles className="w-3.5 h-3.5" />
                </div>
                <div className="min-w-0">
                  <div className="font-semibold text-zinc-900 text-xs">1,000 AI Credits / month</div>
                  <div className="text-[11px] text-zinc-500 mt-0.5 leading-snug">
                    Google Gemini 2.5 Flash for proposals &amp; client briefs
                  </div>
                </div>
              </div>

              <div className="flex items-start gap-3 p-3.5 rounded-xl bg-zinc-50/60 border border-zinc-100 hover:border-zinc-200/80 hover:bg-zinc-50 transition-all">
                <div className="w-7 h-7 rounded-lg bg-white border border-zinc-200/80 flex items-center justify-center shrink-0 mt-0.5 text-zinc-800 shadow-2xs">
                  <FileCheck className="w-3.5 h-3.5" />
                </div>
                <div className="min-w-0">
                  <div className="font-semibold text-zinc-900 text-xs">SHA-256 E-Sign Vault</div>
                  <div className="text-[11px] text-zinc-500 mt-0.5 leading-snug">
                    Unlimited legally binding contracts with audit trail
                  </div>
                </div>
              </div>

              <div className="flex items-start gap-3 p-3.5 rounded-xl bg-zinc-50/60 border border-zinc-100 hover:border-zinc-200/80 hover:bg-zinc-50 transition-all">
                <div className="w-7 h-7 rounded-lg bg-white border border-zinc-200/80 flex items-center justify-center shrink-0 mt-0.5 text-zinc-800 shadow-2xs">
                  <Receipt className="w-3.5 h-3.5" />
                </div>
                <div className="min-w-0">
                  <div className="font-semibold text-zinc-900 text-xs">18% GST Invoicing</div>
                  <div className="text-[11px] text-zinc-500 mt-0.5 leading-snug">
                    Automated tax calculation &amp; professional PDF invoices
                  </div>
                </div>
              </div>

              <div className="flex items-start gap-3 p-3.5 rounded-xl bg-zinc-50/60 border border-zinc-100 hover:border-zinc-200/80 hover:bg-zinc-50 transition-all">
                <div className="w-7 h-7 rounded-lg bg-white border border-zinc-200/80 flex items-center justify-center shrink-0 mt-0.5 text-zinc-500 shadow-2xs">
                  <Globe className="w-3.5 h-3.5" />
                </div>
                <div className="min-w-0">
                  <div className="font-semibold text-zinc-900 text-xs flex items-center gap-1">
                    <span>Branded Cora Subdomain</span>
                  </div>
                  <div className="text-[11px] text-zinc-500 mt-0.5 leading-snug">
                    <span className="text-zinc-600 font-medium">No custom domain/email</span> &bull; Upgrade anytime
                  </div>
                </div>
              </div>

            </div>
          </div>

        </div>
      </section>

      {/* ══════════════════════════════════════════════════════════════════════
          ROW 2: CLEAN, SCANNABLE PRICING CARDS (ARCADE STYLE CRAFT)
      ══════════════════════════════════════════════════════════════════════ */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-16">
        
        {/* Section Header & Cadence Switcher */}
        <div className="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6 pb-4 border-b border-zinc-100">
          <div>
            <div className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-400">
              GROWTH PLANS
            </div>
            <h2 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 mt-1">
              High-Throughput Operating Plans
            </h2>
            <p className="text-xs sm:text-sm text-zinc-500 mt-1">
              Connect your custom domain, automate client communications, and supercharge operations with frontier AI.
            </p>
          </div>

          <div className="flex flex-col sm:items-end gap-2 shrink-0">
            <div className="inline-flex items-center p-1 bg-zinc-100/90 rounded-2xl border border-zinc-200/80 shadow-2xs">
              <button
                type="button"
                onClick={() => {
                  setBillingCycle('monthly');
                  trackEvent('pricing_cycle_change', { cycle: 'monthly' });
                }}
                className={`px-5 py-2 rounded-xl text-xs font-semibold transition-all ${
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
                className={`px-5 py-2 rounded-xl text-xs font-semibold transition-all ${
                  billingCycle === 'annual'
                    ? 'bg-zinc-950 text-white shadow-xs'
                    : 'text-zinc-600 hover:text-zinc-950'
                }`}
              >
                Annual Billing
              </button>
            </div>

            <div className="h-6 flex items-center text-xs">
              {billingCycle === 'annual' ? (
                <div className="inline-flex items-center gap-1.5 text-zinc-900 font-medium text-[11px] animate-in fade-in duration-150">
                  <Gift className="w-3.5 h-3.5 text-zinc-900" />
                  <span className="font-semibold">Annual Perks:</span>
                  <span className="bg-zinc-100 border border-zinc-200 px-2 py-0.5 rounded text-zinc-800 font-mono text-[10px]">Free Custom Domain</span>
                  <span className="text-zinc-300">&bull;</span>
                  <span className="bg-zinc-100 border border-zinc-200 px-2 py-0.5 rounded text-zinc-800 font-mono text-[10px]">+12K Bonus Runs</span>
                </div>
              ) : (
                <div className="text-[11px] text-zinc-400 font-mono animate-in fade-in duration-150">
                  Switch to annual for a free custom domain &bull; Cancel anytime
                </div>
              )}
            </div>
          </div>
        </div>

        {/* 3-Tier SaaS Cards: Arcade-Style Clean Hierarchy */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">
          
          {/* CARD 1: STARTER */}
          <div className="bg-white border border-zinc-200 rounded-[28px] p-6 sm:p-7 flex flex-col justify-between shadow-[0_2px_12px_rgba(0,0,0,0.03)] hover:border-zinc-300 hover:shadow-md transition-all">
            <div>
              {/* Header */}
              <div className="flex items-center justify-between gap-2 mb-2">
                <h3 className="font-display text-2xl font-bold text-zinc-950">
                  Starter
                </h3>
              </div>
              <p className="text-xs text-zinc-500 leading-relaxed mb-5 min-h-[34px]">
                Establish your independent brand with custom domains, email, and 5x AI capacity.
              </p>

              {/* Price */}
              <div className="mb-5">
                <div className="flex items-baseline gap-1">
                  <span className="text-4xl font-display font-extrabold text-zinc-950">
                    {currency === 'INR' ? '₹999' : '$9'}
                  </span>
                  <span className="text-xs text-zinc-500 font-medium">/ month</span>
                </div>
                <div className="text-[11px] text-zinc-400 mt-1 font-mono">
                  {billingCycle === 'annual'
                    ? (currency === 'INR' ? 'Billed annually (₹11,988/yr)' : 'Billed annually ($108/yr)')
                    : 'Billed monthly'}
                </div>
              </div>

              {/* Action CTA */}
              <a
                href="https://app.heycora.in/workspace/login?plan=starter"
                className="w-full inline-flex items-center justify-center gap-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-950 py-3 px-4 rounded-xl text-xs font-bold transition-all mb-6"
              >
                <span>Get started</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-600" />
              </a>

              {/* Clean Feature Bullets */}
              <div className="space-y-3 pt-2 border-t border-zinc-100">
                <div className="text-xs font-semibold text-zinc-900">
                  Includes:
                </div>
                <ul className="space-y-2.5 text-xs text-zinc-700">
                  <li className="flex items-center gap-2.5">
                    <Sparkles className="w-4 h-4 text-amber-500 shrink-0" />
                    <span><strong>{billingCycle === 'annual' ? '6,000' : '5,000'}</strong> AI Runs / month</span>
                  </li>
                  <li className="flex items-center gap-2.5">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0" />
                    <span>Connect custom domain</span>
                  </li>
                  <li className="flex items-center gap-2.5">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0" />
                    <span>Custom email sending</span>
                  </li>
                  <li className="flex items-center gap-2.5">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0" />
                    <span>Up to 2 team seats</span>
                  </li>
                  <li className="flex items-center gap-2.5">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0" />
                    <span>100% white-label client view</span>
                  </li>
                  {billingCycle === 'annual' && (
                    <li className="flex items-center gap-2.5 text-emerald-800 font-medium">
                      <Gift className="w-4 h-4 text-emerald-600 shrink-0" />
                      <span>Free 1-Yr Custom Domain</span>
                    </li>
                  )}
                </ul>
              </div>
            </div>
          </div>

          {/* CARD 2: PROFESSIONAL (POPULAR) */}
          <div className="bg-white border-2 border-zinc-950 rounded-[28px] p-6 sm:p-7 flex flex-col justify-between shadow-[0_8px_30px_rgba(0,0,0,0.08)] relative">
            <div>
              {/* Header with Popular Pill */}
              <div className="flex items-center justify-between gap-2 mb-2">
                <h3 className="font-display text-2xl font-bold text-zinc-950">
                  Professional
                </h3>
                <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-zinc-950 text-white text-[10px] font-bold uppercase tracking-wider">
                  Popular
                </span>
              </div>
              <p className="text-xs text-zinc-500 leading-relaxed mb-5 min-h-[34px]">
                Autonomous operating backbone for high-growth commercial studios.
              </p>

              {/* Price */}
              <div className="mb-5">
                <div className="flex items-baseline gap-1">
                  <span className="text-4xl font-display font-extrabold text-zinc-950">
                    {currency === 'INR' ? '₹1,999' : '$19'}
                  </span>
                  <span className="text-xs text-zinc-500 font-medium">/ month</span>
                </div>
                <div className="text-[11px] text-zinc-400 mt-1 font-mono">
                  {billingCycle === 'annual'
                    ? (currency === 'INR' ? 'Billed annually (₹23,988/yr)' : 'Billed annually ($228/yr)')
                    : 'Billed monthly'}
                </div>
              </div>

              {/* Action CTA (Solid Black) */}
              <a
                href="https://app.heycora.in/workspace/login?plan=pro"
                className="w-full inline-flex items-center justify-center gap-2 bg-zinc-950 hover:bg-zinc-800 text-white py-3 px-4 rounded-xl text-xs font-bold transition-all shadow-sm hover:shadow-md mb-6"
              >
                <span>Get started</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
              </a>

              {/* Clean Feature Bullets */}
              <div className="space-y-3 pt-2 border-t border-zinc-100">
                <div className="text-xs font-semibold text-zinc-900">
                  Everything in Starter, plus:
                </div>
                <ul className="space-y-2.5 text-xs text-zinc-700">
                  <li className="flex items-center gap-2.5">
                    <Sparkles className="w-4 h-4 text-blue-600 shrink-0" />
                    <span><strong>Claude 3.5 Sonnet &amp; GPT-4o</strong></span>
                  </li>
                  <li className="flex items-center gap-2.5">
                    <Sparkles className="w-4 h-4 text-amber-500 shrink-0" />
                    <span><strong>{billingCycle === 'annual' ? '21,000' : '20,000'}</strong> AI Runs / month</span>
                  </li>
                  <li className="flex items-center gap-2.5">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0" />
                    <span>Meta WhatsApp automated dispatch</span>
                  </li>
                  <li className="flex items-center gap-2.5">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0" />
                    <span>Dynamic UPI QR code on invoices</span>
                  </li>
                  <li className="flex items-center gap-2.5">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0" />
                    <span>Automated GST tax splits (CGST/SGST/IGST)</span>
                  </li>
                  <li className="flex items-center gap-2.5">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0" />
                    <span>Up to 5 team seats &amp; roles</span>
                  </li>
                  {billingCycle === 'annual' && (
                    <li className="flex items-center gap-2.5 text-emerald-800 font-medium">
                      <Gift className="w-4 h-4 text-emerald-600 shrink-0" />
                      <span>Free 1-Yr Custom Domain</span>
                    </li>
                  )}
                </ul>
              </div>
            </div>
          </div>

          {/* CARD 3: SCALE */}
          <div className="bg-white border border-zinc-200 rounded-[28px] p-6 sm:p-7 flex flex-col justify-between shadow-[0_2px_12px_rgba(0,0,0,0.03)] hover:border-zinc-300 hover:shadow-md transition-all">
            <div>
              {/* Header */}
              <div className="flex items-center justify-between gap-2 mb-2">
                <h3 className="font-display text-2xl font-bold text-zinc-950">
                  Scale
                </h3>
              </div>
              <p className="text-xs text-zinc-500 leading-relaxed mb-5 min-h-[34px]">
                High-throughput infrastructure for agencies &amp; multi-member teams.
              </p>

              {/* Price */}
              <div className="mb-5">
                <div className="flex items-baseline gap-1">
                  <span className="text-4xl font-display font-extrabold text-zinc-950">
                    {currency === 'INR' ? '₹2,999' : '$29'}
                  </span>
                  <span className="text-xs text-zinc-500 font-medium">/ month</span>
                </div>
                <div className="text-[11px] text-zinc-400 mt-1 font-mono">
                  {billingCycle === 'annual'
                    ? (currency === 'INR' ? 'Billed annually (₹35,988/yr)' : 'Billed annually ($348/yr)')
                    : 'Billed monthly'}
                </div>
              </div>

              {/* Action CTA */}
              <a
                href="https://app.heycora.in/workspace/login?plan=scale"
                className="w-full inline-flex items-center justify-center gap-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-950 py-3 px-4 rounded-xl text-xs font-bold transition-all mb-6"
              >
                <span>Get started</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-600" />
              </a>

              {/* Clean Feature Bullets */}
              <div className="space-y-3 pt-2 border-t border-zinc-100">
                <div className="text-xs font-semibold text-zinc-900">
                  Everything in Professional, plus:
                </div>
                <ul className="space-y-2.5 text-xs text-zinc-700">
                  <li className="flex items-center gap-2.5">
                    <Sparkles className="w-4 h-4 text-purple-600 shrink-0" />
                    <span><strong>All Frontier LLM Models</strong></span>
                  </li>
                  <li className="flex items-center gap-2.5">
                    <Sparkles className="w-4 h-4 text-amber-500 shrink-0" />
                    <span><strong>{billingCycle === 'annual' ? '61,000' : '60,000'}</strong> AI Runs / month</span>
                  </li>
                  <li className="flex items-center gap-2.5">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0" />
                    <span>Autonomous AI Research Agent</span>
                  </li>
                  <li className="flex items-center gap-2.5">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0" />
                    <span>Unlimited team seats</span>
                  </li>
                  <li className="flex items-center gap-2.5">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0" />
                    <span>Custom Webhooks &amp; API access</span>
                  </li>
                  <li className="flex items-center gap-2.5">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0" />
                    <span>Dedicated account manager &amp; SLA</span>
                  </li>
                  {billingCycle === 'annual' && (
                    <li className="flex items-center gap-2.5 text-emerald-800 font-medium">
                      <Gift className="w-4 h-4 text-emerald-600 shrink-0" />
                      <span>Free 1-Yr Custom Domain</span>
                    </li>
                  )}
                </ul>
              </div>
            </div>
          </div>

        </div>

        {/* ── Compare All Features CTA Button ── */}
        <div className="mt-10 text-center">
          <button
            type="button"
            onClick={handleToggleTable}
            className="inline-flex items-center gap-2.5 px-6 py-3 rounded-full bg-zinc-100 hover:bg-zinc-200 text-xs font-bold text-zinc-900 transition-all border border-zinc-200/80 shadow-2xs hover:shadow-xs"
          >
            <span>{showTable ? 'Hide full feature comparison' : 'Compare all features in detail'}</span>
            {showTable ? (
              <ChevronUp className="w-4 h-4 text-zinc-600" />
            ) : (
              <ChevronDown className="w-4 h-4 text-zinc-600" />
            )}
          </button>
        </div>

      </section>

      {/* ══════════════════════════════════════════════════════════════════════
          DETAILED COMPARISON MATRIX (ACCORDION TABLE)
      ══════════════════════════════════════════════════════════════════════ */}
      {showTable && (
        <section ref={comparisonRef} className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-20 animate-in fade-in slide-in-from-top-4 duration-300">
          <div className="text-center mb-8">
            <h3 className="font-display text-2xl font-bold text-zinc-950">
              Complete Feature Comparison Matrix
            </h3>
            <p className="text-xs text-zinc-500 mt-1">
              Detailed breakdown of quotas, models, branding, and integrations across all Cora tiers.
            </p>
          </div>

          <div className="border border-zinc-200 rounded-3xl overflow-hidden shadow-sm bg-white">
            <div className="overflow-x-auto">
              <table className="w-full text-left border-collapse text-xs">
                <thead>
                  <tr className="bg-zinc-50 border-b border-zinc-200 text-zinc-950 font-bold">
                    <th className="p-4 sm:p-5 w-2/6">Capabilities &amp; Modules</th>
                    <th className="p-4 sm:p-5 w-1/6 text-center">Free Forever</th>
                    <th className="p-4 sm:p-5 w-1/6 text-center">Starter ({currency === 'INR' ? '₹999' : '$9'})</th>
                    <th className="p-4 sm:p-5 w-1/6 text-center bg-zinc-100/70 font-bold">Professional ({currency === 'INR' ? '₹1,999' : '$19'})</th>
                    <th className="p-4 sm:p-5 w-1/6 text-center">Scale ({currency === 'INR' ? '₹2,999' : '$29'})</th>
                    <th className="p-4 sm:p-5 w-1/6 text-center font-bold">Bharat (₹499/mo)</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-zinc-100">
                  
                  {/* Category 1: AI Operations */}
                  <tr className="bg-zinc-100/50">
                    <td colSpan={6} className="p-3.5 font-mono font-bold uppercase tracking-wider text-[11px] text-zinc-600">
                      1. AI Intelligence &amp; Autonomous Scoping
                    </td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">Monthly AI Runs</td>
                    <td className="p-4 text-center font-mono">1,000</td>
                    <td className="p-4 text-center font-mono">{billingCycle === 'annual' ? '6,000' : '5,000'}</td>
                    <td className="p-4 text-center font-mono bg-zinc-50 font-bold">{billingCycle === 'annual' ? '21,000' : '20,000'}</td>
                    <td className="p-4 text-center font-mono">{billingCycle === 'annual' ? '61,000' : '60,000'}</td>
                    <td className="p-4 text-center font-mono font-bold">10,000</td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">Supported LLM Engines</td>
                    <td className="p-4 text-center text-zinc-600">Gemini 2.5 Flash</td>
                    <td className="p-4 text-center text-zinc-600">Gemini 2.5 Flash</td>
                    <td className="p-4 text-center text-zinc-900 font-bold bg-zinc-50">Claude 3.5 + GPT-4o mini</td>
                    <td className="p-4 text-center text-zinc-900 font-bold">All Frontier Models</td>
                    <td className="p-4 text-center text-zinc-900 font-medium">Claude 3.5 + GPT-4o mini</td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">Autonomous Proposal Research Agent</td>
                    <td className="p-4 text-center text-zinc-400"><Minus className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-400"><Minus className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900 bg-zinc-50 font-medium"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900 font-bold"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-400"><Minus className="w-4 h-4 mx-auto" /></td>
                  </tr>

                  {/* Category 2: Branding & Web Presence */}
                  <tr className="bg-zinc-100/50">
                    <td colSpan={6} className="p-3.5 font-mono font-bold uppercase tracking-wider text-[11px] text-zinc-600">
                      2. Branding, Custom Domains &amp; Web Presence
                    </td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">Portfolio Website Builder</td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900 bg-zinc-50 font-bold"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">Custom Domain Connection</td>
                    <td className="p-4 text-center text-zinc-400 font-mono text-[11px]">Subdomain only</td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900 bg-zinc-50 font-bold"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">Free 1-Yr Domain on Annual</td>
                    <td className="p-4 text-center text-zinc-400"><Minus className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900 bg-zinc-50 font-bold"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900 font-bold"><Check className="w-4 h-4 mx-auto" /></td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">White-label &amp; Cora Badge Removal</td>
                    <td className="p-4 text-center text-zinc-400"><Minus className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900 bg-zinc-50 font-bold"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                  </tr>

                  {/* Category 3: Client Operations & Legal */}
                  <tr className="bg-zinc-100/50">
                    <td colSpan={6} className="p-3.5 font-mono font-bold uppercase tracking-wider text-[11px] text-zinc-600">
                      3. Client CRM, Proposals &amp; Legal E-Sign
                    </td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">SHA-256 E-Sign Vault</td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900 bg-zinc-50 font-bold"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">Team Seats</td>
                    <td className="p-4 text-center font-mono">1 seat</td>
                    <td className="p-4 text-center font-mono">2 seats</td>
                    <td className="p-4 text-center font-mono bg-zinc-50 font-bold">5 seats</td>
                    <td className="p-4 text-center font-mono font-bold">Unlimited</td>
                    <td className="p-4 text-center font-mono">5 seats</td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">Meta WhatsApp Cloud Automated Dispatch</td>
                    <td className="p-4 text-center text-zinc-400"><Minus className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-400 font-mono text-[11px]">Templates</td>
                    <td className="p-4 text-center text-zinc-900 bg-zinc-50 font-bold"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900 font-bold"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900 font-bold"><Check className="w-4 h-4 mx-auto" /></td>
                  </tr>

                  {/* Category 4: Invoicing & Indian GST */}
                  <tr className="bg-zinc-100/50">
                    <td colSpan={6} className="p-3.5 font-mono font-bold uppercase tracking-wider text-[11px] text-zinc-600">
                      4. Invoicing, Payments &amp; Indian GST Math
                    </td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">18% GST Invoicing Engine</td>
                    <td className="p-4 text-center text-zinc-900">Basic</td>
                    <td className="p-4 text-center text-zinc-900">Included</td>
                    <td className="p-4 text-center text-zinc-900 bg-zinc-50 font-bold">Auto Splits</td>
                    <td className="p-4 text-center text-zinc-900">Custom Math</td>
                    <td className="p-4 text-center text-zinc-900 font-bold">Auto CGST/SGST/IGST</td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">Dynamic UPI QR Code on Invoices</td>
                    <td className="p-4 text-center text-zinc-400"><Minus className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900 bg-zinc-50 font-bold"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900 font-bold"><Check className="w-4 h-4 mx-auto" /></td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">Custom Webhooks &amp; API Integration</td>
                    <td className="p-4 text-center text-zinc-400"><Minus className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-400"><Minus className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-400 bg-zinc-50"><Minus className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900 font-bold"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-400"><Minus className="w-4 h-4 mx-auto" /></td>
                  </tr>

                </tbody>
              </table>
            </div>
          </div>
        </section>
      )}

      {/* ══════════════════════════════════════════════════════════════════════
          ROW 3: INDIA ONLY PLAN (BHARAT EDITION)
      ══════════════════════════════════════════════════════════════════════ */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-20">
        <div className="bg-[#0A0D10] text-white border-2 border-zinc-800 rounded-[32px] p-6 sm:p-10 shadow-2xl relative overflow-hidden">
          
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            <div className="lg:col-span-5 space-y-3">
              <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-zinc-800 border border-zinc-700 text-[10px] font-mono font-bold text-zinc-200 uppercase tracking-wider">
                <span>INDIA MSME EDITION &bull; ANNUAL COMMITMENT ONLY</span>
              </div>

              <h2 className="font-display text-2xl sm:text-3xl font-bold text-white">
                Bharat Growth Plan
              </h2>

              <p className="text-xs sm:text-sm text-zinc-400 leading-relaxed">
                A heavily subsidized operating system built exclusively for Indian founders, MSMEs, agencies, and consultancies. Includes complete GST tax math, instant UPI QR payments, and WhatsApp dispatch.
              </p>

              <div className="pt-2">
                <div className="flex items-baseline gap-2">
                  <span className="text-3xl sm:text-4xl font-display font-bold text-white">₹499</span>
                  <span className="text-xs text-zinc-400 font-mono">/month</span>
                </div>
                <div className="text-[11px] text-zinc-400 font-mono mt-1">
                  Billed annually at ₹5,988/year &bull; <em>Strictly annual commitment only (Monthly not available)</em>
                </div>
              </div>
            </div>

            <div className="lg:col-span-4 space-y-2.5 text-xs text-zinc-300">
              <div className="font-semibold text-zinc-200 text-[11px] uppercase tracking-wider font-mono">
                India Edition Features:
              </div>
              <ul className="space-y-2">
                <li className="flex items-start gap-2">
                  <Check className="w-4 h-4 text-white shrink-0 mt-0.5" />
                  <span><strong>10,000</strong> AI agent reasoning runs/month</span>
                </li>
                <li className="flex items-start gap-2">
                  <Check className="w-4 h-4 text-white shrink-0 mt-0.5" />
                  <span><strong>Free Custom Domain</strong> (.in / .com) with SSL</span>
                </li>
                <li className="flex items-start gap-2">
                  <Check className="w-4 h-4 text-white shrink-0 mt-0.5" />
                  <span>Automated 18% CGST/SGST/IGST tax splits</span>
                </li>
                <li className="flex items-start gap-2">
                  <Check className="w-4 h-4 text-white shrink-0 mt-0.5" />
                  <span>Dynamic UPI QR code on all invoices</span>
                </li>
                <li className="flex items-start gap-2">
                  <Check className="w-4 h-4 text-white shrink-0 mt-0.5" />
                  <span>Meta WhatsApp automated client notifications</span>
                </li>
                <li className="flex items-start gap-2">
                  <Check className="w-4 h-4 text-white shrink-0 mt-0.5" />
                  <span>Up to 5 Team Seats &amp; Priority IST Support</span>
                </li>
              </ul>
            </div>

            <div className="lg:col-span-3 flex flex-col justify-center space-y-3">
              <a
                href="https://app.heycora.in/workspace/login?plan=india_annual_499"
                className="w-full inline-flex items-center justify-center gap-2 bg-white hover:bg-zinc-100 text-zinc-950 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-bold transition-colors shadow-sm"
              >
                <span>Get Started</span>
                <ArrowRight className="w-4 h-4 text-zinc-950" />
              </a>
              <p className="text-[11px] text-zinc-500 text-center font-mono">
                UDYAM / GSTIN verification supported &bull; Instant activation
              </p>
            </div>

          </div>

        </div>
      </section>

      {/* ── Frequently Asked Questions ── */}
      <section className="w-full max-w-[860px] mx-auto px-4 sm:px-6">
        <div className="text-center mb-10">
          <h2 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 mb-2">
            Frequently asked questions
          </h2>
          <p className="text-xs sm:text-sm text-zinc-500 font-normal">
            Everything you need to know about plans, billing, and autonomous quotas.
          </p>
        </div>

        <div className="space-y-3">
          {FAQS.map((faq, idx) => {
            const isOpen = openFaq === idx;
            return (
              <div
                key={idx}
                className="border border-zinc-200 rounded-2xl overflow-hidden bg-white shadow-2xs transition-all"
              >
                <button
                  type="button"
                  onClick={() => toggleFaq(idx)}
                  className="w-full p-4 sm:p-5 text-left flex items-center justify-between gap-4 font-semibold text-xs sm:text-sm text-zinc-950 hover:bg-zinc-50/70 transition-colors"
                >
                  <span>{faq.q}</span>
                  {isOpen ? (
                    <ChevronUp className="w-4 h-4 text-zinc-400 shrink-0" />
                  ) : (
                    <ChevronDown className="w-4 h-4 text-zinc-400 shrink-0" />
                  )}
                </button>
                {isOpen && (
                  <div className="px-4 sm:px-5 pb-4 sm:pb-5 text-xs text-zinc-600 leading-relaxed border-t border-zinc-100 pt-3 animate-in fade-in duration-150">
                    {faq.a}
                  </div>
                )}
              </div>
            );
          })}
        </div>

        <div className="mt-12 text-center text-xs text-zinc-500 font-mono">
          <span>Need custom contract terms or have specific compliance questions? </span>
          <Link href="/contact" className="text-zinc-950 font-bold underline underline-offset-2 hover:text-zinc-700">
            Contact our Mumbai solutions desk &rarr;
          </Link>
        </div>
      </section>

    </main>
  );
}
