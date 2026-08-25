'use client';

import React, { useState, useEffect } from 'react';
import Link from 'next/link';
import { 
  Check, 
  ArrowRight, 
  Sparkles, 
  Zap, 
  ShieldCheck, 
  HelpCircle, 
  ChevronDown, 
  ChevronUp, 
  Building2, 
  Layers, 
  CheckCircle2, 
  Minus,
  Info,
  Flame,
  Globe,
  Gift
} from 'lucide-react';
import { trackEvent } from '@/components/analytics/Analytics';

const TESTIMONIAL_LOGOS = [
  { name: 'Apex Digital Agency', quote: 'Cora automated our entire proposal-to-GST invoicing pipeline in week one.' },
  { name: 'Vanguard Advisory', quote: 'The SHA-256 E-Sign registry gives our corporate clients total confidence.' },
  { name: 'Horizon Commercial', quote: 'Saved over 15 hours every week on client scheduling and WhatsApp dispatch.' },
  { name: 'Luminary Legal Group', quote: 'Full Indian DPDP & IT Act compliance out of the box. Truly unmatched.' },
  { name: 'Matrix Real Estate', quote: 'Lead scoring and automated follow-ups doubled our consultation conversion.' },
];

const FAQS = [
  {
    q: 'Is the Free Forever plan really free?',
    a: 'Yes, 100% free forever. It includes 1,000 complimentary AI agent runs every month, unlimited tamper-evident SHA-256 e-signatures, automated GST invoicing, and Kanban CRM access with zero credit card required.'
  },
  {
    q: 'How does the Free Domain on annual plans work?',
    a: 'When you subscribe to any of our paid annual plans ($9/mo, $19/mo, $29/mo, or the India ₹499/mo plan), you receive a complimentary custom domain registration (.com, .in, or .co) with automatic SSL and DNS configuration.'
  },
  {
    q: 'Why is the India Only plan strictly annual?',
    a: 'The India MSME Edition is heavily subsidised at ₹499/month specifically to support registered Indian businesses and founders with long-term operational infrastructure. It is only available as an annual commitment (₹5,988/year).'
  },
  {
    q: 'Can I upgrade, downgrade, or cancel anytime?',
    a: 'Yes. You can manage your tier anytime directly in your workspace settings. If you downgrade, your historical contracts, invoices, and clients remain fully accessible and protected.'
  },
  {
    q: 'Is our financial and client data secure?',
    a: 'All data is protected by AES-256 encryption at rest and TLS 1.3 in transit. All e-signatures are SHA-256 cryptographically sealed and legally compliant under the Indian IT Act 2000 and DPDP Act 2023.'
  }
];

export default function PricingPage() {
  const [billingCycle, setBillingCycle] = useState<'monthly' | 'annual'>('annual');
  const [currency, setCurrency] = useState<'INR' | 'USD'>('INR');
  const [isIndia, setIsIndia] = useState(true);
  const [showTable, setShowTable] = useState(false);
  const [openFaq, setOpenFaq] = useState<number | null>(null);

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

  const toggleFaq = (index: number) => {
    setOpenFaq(openFaq === index ? null : index);
  };

  return (
    <main className="w-full relative pt-32 sm:pt-40 pb-24 overflow-hidden bg-white text-zinc-900">
      
      {/* ── Top Hero Section ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 text-center mb-12 sm:mb-16">
        <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-zinc-100 rounded-full border border-zinc-200/90 text-xs font-semibold text-zinc-900 mb-4 shadow-2xs">
          <span className="w-1.5 h-1.5 rounded-full bg-zinc-900" />
          <span>TRANSPARENT PRICING &amp; PLANS</span>
        </div>

        <h1 className="font-display text-4xl xs:text-5xl sm:text-6xl font-bold text-zinc-950 leading-[1.1] tracking-[-0.035em] max-w-[860px] mx-auto mb-4">
          Flexible pricing that fits your business
        </h1>

        <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[640px] mx-auto mb-8">
          Start with our free forever plan, or choose an annual tier with a complimentary custom domain and high-throughput AI reasoning.
        </p>

        {/* ── Cadence Selector (Monthly / Annual) with Free Domain Badge ── */}
        <div className="flex flex-col items-center justify-center gap-2 mb-10">
          <div className="inline-flex items-center p-1 bg-zinc-100 rounded-xl border border-zinc-200 shadow-2xs">
            <button
              type="button"
              onClick={() => {
                setBillingCycle('monthly');
                trackEvent('pricing_cycle_change', { cycle: 'monthly' });
              }}
              className={`px-5 py-2 rounded-lg text-xs font-semibold transition-all ${
                billingCycle === 'monthly'
                  ? 'bg-zinc-950 text-white shadow-xs'
                  : 'text-zinc-600 hover:text-zinc-950'
              }`}
            >
              Monthly
            </button>
            <button
              type="button"
              onClick={() => {
                setBillingCycle('annual');
                trackEvent('pricing_cycle_change', { cycle: 'annual' });
              }}
              className={`px-5 py-2 rounded-lg text-xs font-semibold transition-all flex items-center gap-2 ${
                billingCycle === 'annual'
                  ? 'bg-zinc-950 text-white shadow-xs'
                  : 'text-zinc-600 hover:text-zinc-950'
              }`}
            >
              <span>Annual</span>
              <span className="text-[10px] font-mono px-1.5 py-0.5 rounded bg-zinc-200 text-zinc-900 font-bold">
                FREE DOMAIN + SAVE 20%
              </span>
            </button>
          </div>
          
          {billingCycle === 'annual' && (
            <div className="inline-flex items-center gap-1.5 text-xs text-zinc-500 font-mono animate-in fade-in duration-200">
              <Gift className="w-3.5 h-3.5 text-zinc-900" />
              <span>Includes 1 year free custom domain (.com / .in) on all annual tiers</span>
            </div>
          )}
        </div>

        {/* ── Marquee / Client Quotes Strip ── */}
        <div className="w-full overflow-hidden py-3 border-y border-zinc-100">
          <div className="flex items-center justify-center flex-wrap gap-x-8 gap-y-2 text-xs text-zinc-500 font-medium">
            <span className="text-zinc-400 font-mono text-[11px] uppercase tracking-wider">TRUSTED BY 2,400+ FOUNDERS:</span>
            {TESTIMONIAL_LOGOS.map((item, i) => (
              <span key={i} className="inline-flex items-center gap-1.5 text-zinc-700 hover:text-zinc-950 transition-colors cursor-default" title={item.quote}>
                <span className="w-1.5 h-1.5 rounded-full bg-zinc-400" />
                <span className="font-semibold">{item.name}</span>
              </span>
            ))}
          </div>
        </div>

      </section>

      {/* ══════════════════════════════════════════════════════════════════════
          ROW 1: FREE FOREVER PLAN (HERO BANNER CARD)
      ══════════════════════════════════════════════════════════════════════ */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-12">
        <div className="bg-white border border-zinc-200 rounded-[28px] p-6 sm:p-8 shadow-[0_12px_36px_rgba(0,0,0,0.03)] hover:border-zinc-300 transition-all">
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
            
            <div className="lg:col-span-4 space-y-2">
              <div className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-zinc-100 text-[10px] font-mono font-bold text-zinc-700 uppercase tracking-wider">
                <span>TIER 01</span>
                <span>&bull;</span>
                <span>NO CREDIT CARD REQUIRED</span>
              </div>
              <h2 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950">
                Free Forever Plan
              </h2>
              <p className="text-xs sm:text-sm text-zinc-500 leading-relaxed">
                Everything you need to launch, manage leads, send proposals, and execute legally binding e-contracts for your business.
              </p>
              <div className="pt-1 flex items-baseline gap-1.5">
                <span className="text-3xl sm:text-4xl font-display font-bold text-zinc-950">$0 / ₹0</span>
                <span className="text-xs text-zinc-400 font-mono">forever free</span>
              </div>
            </div>

            <div className="lg:col-span-5 grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs text-zinc-700">
              <div className="flex items-start gap-2">
                <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                <span><strong>1,000</strong> AI agent runs/month</span>
              </div>
              <div className="flex items-start gap-2">
                <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                <span>Google Gemini 2.5 Flash LLM</span>
              </div>
              <div className="flex items-start gap-2">
                <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                <span>Unlimited SHA-256 E-Sign Vault</span>
              </div>
              <div className="flex items-start gap-2">
                <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                <span>Automated 18% GST Invoicing</span>
              </div>
              <div className="flex items-start gap-2">
                <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                <span>Kanban CRM &amp; Lead Registry</span>
              </div>
              <div className="flex items-start gap-2">
                <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                <span>1 Workspace &amp; Community Support</span>
              </div>
            </div>

            <div className="lg:col-span-3 flex lg:justify-end">
              <a
                href="https://app.heycora.in/workspace/login?plan=free"
                className="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-zinc-950 hover:bg-zinc-800 text-white px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold transition-colors shadow-sm"
              >
                <span>Start Free — No Card Needed</span>
                <ArrowRight className="w-4 h-4 text-zinc-400" />
              </a>
            </div>

          </div>
        </div>
      </section>

      {/* ══════════════════════════════════════════════════════════════════════
          ROW 2: 3-TIER GLOBAL SAAS PRICING ($9, $19, $29)
      ══════════════════════════════════════════════════════════════════════ */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-12">
        <div className="mb-6 flex flex-col sm:flex-row sm:items-end justify-between gap-2">
          <div>
            <div className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-400">
              GROWTH PLANS
            </div>
            <h2 className="font-display text-2xl font-bold text-zinc-950">
              High-Throughput Operating Plans
            </h2>
          </div>
          <div className="text-xs text-zinc-500 font-mono">
            {billingCycle === 'annual' ? '🎉 Free custom domain included with all annual plans' : 'Switch to annual for a free custom domain'}
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">
          
          {/* TIER A: $9 / mo */}
          <div className="bg-white border border-zinc-200/90 rounded-[28px] p-6 sm:p-7 flex flex-col justify-between shadow-2xs hover:border-zinc-300 transition-all">
            <div className="space-y-4">
              <div>
                <span className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-500">
                  STARTER
                </span>
                <h3 className="font-display text-2xl font-bold text-zinc-950 mt-1">
                  Starter Plan
                </h3>
                <p className="text-xs text-zinc-500 mt-1 leading-relaxed">
                  For solo consultants and small boutique agencies automating their client ops.
                </p>
              </div>

              <div className="py-2 border-y border-zinc-100">
                <div className="flex items-baseline gap-1">
                  <span className="text-3xl sm:text-4xl font-display font-bold text-zinc-950">
                    {currency === 'INR' ? (billingCycle === 'annual' ? '₹699' : '₹799') : (billingCycle === 'annual' ? '$7' : '$9')}
                  </span>
                  <span className="text-xs text-zinc-400 font-mono">/month</span>
                </div>
                <div className="text-[11px] text-zinc-500 font-mono mt-0.5">
                  {billingCycle === 'annual' ? 'Billed annually + Free Domain' : 'Billed monthly'} &bull; 5K AI runs/mo
                </div>
              </div>

              <div className="space-y-2 text-xs text-zinc-700">
                <div className="font-semibold text-zinc-900 text-[11px] uppercase tracking-wider font-mono">
                  Features included:
                </div>
                <ul className="space-y-2">
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span><strong>5,000</strong> AI agent runs/month</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span>Free Custom Domain on Annual Billing</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span>Automated GST/Tax Invoices with QR</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span>SHA-256 E-Sign Vault with Audit Logs</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span>Meta WhatsApp notification templates</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span>Up to 2 Team Seats included</span>
                  </li>
                </ul>
              </div>
            </div>

            <div className="pt-6 mt-6 border-t border-zinc-100">
              <a
                href="https://app.heycora.in/workspace/login?plan=starter"
                className="w-full inline-flex items-center justify-center gap-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-950 px-4 py-2.5 rounded-xl text-xs font-semibold transition-colors"
              >
                <span>Start 14-Day Trial</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-500" />
              </a>
            </div>
          </div>

          {/* TIER B: $19 / mo (MOST POPULAR) */}
          <div className="bg-[#0E1115] text-white border-2 border-zinc-800 rounded-[28px] p-6 sm:p-7 flex flex-col justify-between shadow-xl relative overflow-hidden">
            <div className="absolute top-3 right-3">
              <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-zinc-800 border border-zinc-700 text-[10px] font-mono font-bold text-white uppercase tracking-wider">
                <Flame className="w-3 h-3 text-zinc-300" />
                <span>MOST POPULAR</span>
              </span>
            </div>

            <div className="space-y-4">
              <div>
                <span className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-400">
                  STUDIO &amp; AGENCY
                </span>
                <h3 className="font-display text-2xl font-bold text-white mt-1">
                  Professional Plan
                </h3>
                <p className="text-xs text-zinc-400 mt-1 leading-relaxed">
                  The complete autonomous operating backbone for growing commercial practices.
                </p>
              </div>

              <div className="py-2 border-y border-zinc-800">
                <div className="flex items-baseline gap-1">
                  <span className="text-3xl sm:text-4xl font-display font-bold text-white">
                    {currency === 'INR' ? (billingCycle === 'annual' ? '₹1,399' : '₹1,599') : (billingCycle === 'annual' ? '$15' : '$19')}
                  </span>
                  <span className="text-xs text-zinc-400 font-mono">/month</span>
                </div>
                <div className="text-[11px] text-zinc-400 font-mono mt-0.5">
                  {billingCycle === 'annual' ? 'Billed annually + Free Domain' : 'Billed monthly'} &bull; 20K AI runs/mo
                </div>
              </div>

              <div className="space-y-2 text-xs text-zinc-300">
                <div className="font-semibold text-zinc-200 text-[11px] uppercase tracking-wider font-mono">
                  Everything in Starter, plus:
                </div>
                <ul className="space-y-2">
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-white shrink-0 mt-0.5" />
                    <span><strong>20,000</strong> AI agent runs/month</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-white shrink-0 mt-0.5" />
                    <span>Claude 3.5 Sonnet &amp; GPT-4o mini</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-white shrink-0 mt-0.5" />
                    <span>Free Custom Domain on Annual Billing</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-white shrink-0 mt-0.5" />
                    <span>Meta WhatsApp Cloud automated dispatch</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-white shrink-0 mt-0.5" />
                    <span>Full GSTIN splits &amp; Dynamic UPI QR</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-white shrink-0 mt-0.5" />
                    <span>Up to 5 Team Seats &amp; Priority Support</span>
                  </li>
                </ul>
              </div>
            </div>

            <div className="pt-6 mt-6 border-t border-zinc-800">
              <a
                href="https://app.heycora.in/workspace/login?plan=pro"
                className="w-full inline-flex items-center justify-center gap-2 bg-white hover:bg-zinc-100 text-zinc-950 px-4 py-2.5 rounded-xl text-xs font-bold transition-colors shadow-sm"
              >
                <span>Start 14-Day Free Trial</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-950" />
              </a>
            </div>
          </div>

          {/* TIER C: $29 / mo */}
          <div className="bg-white border border-zinc-200/90 rounded-[28px] p-6 sm:p-7 flex flex-col justify-between shadow-2xs hover:border-zinc-300 transition-all">
            <div className="space-y-4">
              <div>
                <span className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-500">
                  SCALE &amp; SPEED
                </span>
                <h3 className="font-display text-2xl font-bold text-zinc-950 mt-1">
                  Scale Plan
                </h3>
                <p className="text-xs text-zinc-500 mt-1 leading-relaxed">
                  High-throughput infrastructure for multi-member teams and enterprise workflows.
                </p>
              </div>

              <div className="py-2 border-y border-zinc-100">
                <div className="flex items-baseline gap-1">
                  <span className="text-3xl sm:text-4xl font-display font-bold text-zinc-950">
                    {currency === 'INR' ? (billingCycle === 'annual' ? '₹2,199' : '₹2,499') : (billingCycle === 'annual' ? '$23' : '$29')}
                  </span>
                  <span className="text-xs text-zinc-400 font-mono">/month</span>
                </div>
                <div className="text-[11px] text-zinc-500 font-mono mt-0.5">
                  {billingCycle === 'annual' ? 'Billed annually + Free Domain' : 'Billed monthly'} &bull; 60K AI runs/mo
                </div>
              </div>

              <div className="space-y-2 text-xs text-zinc-700">
                <div className="font-semibold text-zinc-900 text-[11px] uppercase tracking-wider font-mono">
                  Everything in Pro, plus:
                </div>
                <ul className="space-y-2">
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span><strong>60,000</strong> AI reasoning runs/month</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span>All Frontier Models (Claude 3.5 &amp; GPT-4o)</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span>Free Custom Domain on Annual Billing</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span>Autonomous AI Proposal &amp; Research Agent</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span>Unlimited team seats &amp; role permissions</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span>Custom Webhook Automations &amp; API</span>
                  </li>
                </ul>
              </div>
            </div>

            <div className="pt-6 mt-6 border-t border-zinc-100">
              <a
                href="https://app.heycora.in/workspace/login?plan=scale"
                className="w-full inline-flex items-center justify-center gap-2 bg-zinc-950 hover:bg-zinc-800 text-white px-4 py-2.5 rounded-xl text-xs font-semibold transition-colors shadow-xs"
              >
                <span>Start 14-Day Free Trial</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
              </a>
            </div>
          </div>

        </div>
      </section>

      {/* ══════════════════════════════════════════════════════════════════════
          ROW 3: INDIA ONLY PLAN (BHARAT EDITION - RS 499/MO ANNUAL ONLY)
      ══════════════════════════════════════════════════════════════════════ */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-20">
        <div className="bg-gradient-to-b from-zinc-900 to-zinc-950 text-white border-2 border-zinc-800 rounded-[32px] p-6 sm:p-10 shadow-2xl relative overflow-hidden">
          
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            <div className="lg:col-span-5 space-y-3">
              <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-zinc-800 border border-zinc-700 text-[10px] font-mono font-bold text-zinc-200 uppercase tracking-wider">
                <Flame className="w-3.5 h-3.5 text-zinc-300" />
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
                  Billed annually at ₹5,988/year &bull; <em>Strictly annual commitment only (no monthly option)</em>
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
                <span>Claim India MSME Plan</span>
                <ArrowRight className="w-4 h-4 text-zinc-950" />
              </a>
              <p className="text-[11px] text-zinc-500 text-center font-mono">
                UDYAM / GSTIN verification supported &bull; Instant activation
              </p>
            </div>

          </div>

        </div>
      </section>

      {/* ── Full Feature Comparison Matrix (Clay Style Accordion) ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-24">
        <div className="text-center mb-8">
          <button
            type="button"
            onClick={() => setShowTable(!showTable)}
            className="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-zinc-100 hover:bg-zinc-200 text-xs font-bold text-zinc-900 transition-colors"
          >
            <span>{showTable ? 'Hide detailed plan comparison' : 'See full plan comparison matrix'}</span>
            {showTable ? <ChevronUp className="w-4 h-4" /> : <ChevronDown className="w-4 h-4" />}
          </button>
        </div>

        {showTable && (
          <div className="border border-zinc-200 rounded-3xl overflow-hidden shadow-sm bg-white animate-in fade-in duration-200">
            <div className="overflow-x-auto">
              <table className="w-full text-left border-collapse text-xs">
                <thead>
                  <tr className="bg-zinc-50 border-b border-zinc-200 text-zinc-950 font-bold">
                    <th className="p-4 sm:p-5 w-2/6">Capabilities &amp; Modules</th>
                    <th className="p-4 sm:p-5 w-1/6 text-center">Free Forever</th>
                    <th className="p-4 sm:p-5 w-1/6 text-center bg-zinc-100/70 font-bold">India Edition (₹499/mo)</th>
                    <th className="p-4 sm:p-5 w-1/6 text-center">Starter ($9/mo)</th>
                    <th className="p-4 sm:p-5 w-1/6 text-center">Professional ($19/mo)</th>
                    <th className="p-4 sm:p-5 w-1/6 text-center">Scale ($29/mo)</th>
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
                    <td className="p-4 text-center font-mono bg-zinc-50 font-bold">10,000</td>
                    <td className="p-4 text-center font-mono">5,000</td>
                    <td className="p-4 text-center font-mono">20,000</td>
                    <td className="p-4 text-center font-mono">60,000</td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">Free Custom Domain (Annual)</td>
                    <td className="p-4 text-center text-zinc-400"><Minus className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900 bg-zinc-50 font-bold"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">Supported LLM Models</td>
                    <td className="p-4 text-center text-zinc-600">Gemini 2.5 Flash</td>
                    <td className="p-4 text-center text-zinc-900 font-medium bg-zinc-50">Claude 3.5 + GPT-4o mini</td>
                    <td className="p-4 text-center text-zinc-600">Gemini 2.5 Flash</td>
                    <td className="p-4 text-center text-zinc-900 font-medium">Claude 3.5 + GPT-4o mini</td>
                    <td className="p-4 text-center text-zinc-900 font-medium">All Frontier Models</td>
                  </tr>

                  {/* Category 2: Invoicing & Taxes */}
                  <tr className="bg-zinc-100/50">
                    <td colSpan={6} className="p-3.5 font-mono font-bold uppercase tracking-wider text-[11px] text-zinc-600">
                      2. Invoicing, Payments &amp; Indian GST Math
                    </td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">18% GST Calculation &amp; Invoices</td>
                    <td className="p-4 text-center text-zinc-900">Basic</td>
                    <td className="p-4 text-center text-zinc-900 bg-zinc-50 font-bold">Auto CGST/SGST/IGST</td>
                    <td className="p-4 text-center text-zinc-900">Included</td>
                    <td className="p-4 text-center text-zinc-900">Automated Splits</td>
                    <td className="p-4 text-center text-zinc-900">Custom Formats</td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">Dynamic UPI QR Code Payment</td>
                    <td className="p-4 text-center text-zinc-400"><Minus className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900 bg-zinc-50 font-bold"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">WhatsApp Automated Dispatch</td>
                    <td className="p-4 text-center text-zinc-400">Manual templates</td>
                    <td className="p-4 text-center text-zinc-900 bg-zinc-50 font-bold">Meta Cloud API</td>
                    <td className="p-4 text-center text-zinc-900">Templates</td>
                    <td className="p-4 text-center text-zinc-900 font-medium">Meta Cloud API</td>
                    <td className="p-4 text-center text-zinc-900 font-medium">Meta Cloud API</td>
                  </tr>

                  {/* Category 3: Legal & Contracts */}
                  <tr className="bg-zinc-100/50">
                    <td colSpan={6} className="p-3.5 font-mono font-bold uppercase tracking-wider text-[11px] text-zinc-600">
                      3. Legal, E-Sign &amp; Governance
                    </td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">SHA-256 E-Sign Vault</td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900 bg-zinc-50 font-bold"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">Team Seats</td>
                    <td className="p-4 text-center font-mono">1 seat</td>
                    <td className="p-4 text-center font-mono bg-zinc-50 font-bold">5 seats</td>
                    <td className="p-4 text-center font-mono">2 seats</td>
                    <td className="p-4 text-center font-mono">5 seats</td>
                    <td className="p-4 text-center font-mono">Unlimited</td>
                  </tr>

                </tbody>
              </table>
            </div>
          </div>
        )}
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

        {/* Bottom Help Note */}
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
