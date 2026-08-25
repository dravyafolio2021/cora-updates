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
  Globe
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
    a: 'Yes, absolutely. Our Free Forever tier includes 1,000 complimentary AI agent runs each month, unlimited tamper-evident SHA-256 e-signatures, automated GST invoicing, and full Kanban CRM access with zero credit card required.'
  },
  {
    q: 'What is the India Edition annual commitment?',
    a: 'The India Edition is specially crafted for Indian MSMEs, agencies, and consultancies. It includes full GSTIN tax split calculations, instant UPI dynamic QR codes on invoices, and Meta WhatsApp integration at ₹999/month, billed annually (₹11,988/year).'
  },
  {
    q: 'Can I change plans or cancel anytime?',
    a: 'Yes. You can upgrade, downgrade, or cancel your subscription at any time directly from your workspace billing dashboard. If you downgrade, your existing data remains fully secure and accessible.'
  },
  {
    q: 'How do AI agent runs work?',
    a: 'Each AI run powers an autonomous operation — such as drafting a detailed proposal, calculating multi-tier project scopes, extracting lead intelligence, or formatting customized GST invoices. Additional run packs can be added anytime.'
  },
  {
    q: 'Is our client and financial data secure?',
    a: 'Yes. All data is encrypted with AES-256 at rest and TLS 1.3 in transit. All e-signatures are cryptographic SHA-256 sealed under the Indian IT Act 2000 and DPDP Act 2023.'
  }
];

export default function PricingPage() {
  const [billingCycle, setBillingCycle] = useState<'monthly' | 'annual'>('annual');
  const [currency, setCurrency] = useState<'INR' | 'USD'>('INR');
  const [isIndia, setIsIndia] = useState(true);
  const [showTable, setShowTable] = useState(true);
  const [openFaq, setOpenFaq] = useState<number | null>(null);

  // Dynamic Geolocation detection
  useEffect(() => {
    try {
      // 1. Instant synchronous browser timezone check (0ms latency, zero layout shift)
      const tz = Intl.DateTimeFormat().resolvedOptions().timeZone || '';
      const isIndiaTz = tz.includes('Kolkata') || tz.includes('Calcutta') || tz.includes('India');
      if (isIndiaTz) {
        setCurrency('INR');
        setIsIndia(true);
      } else {
        setCurrency('USD');
        setIsIndia(false);
      }

      // 2. Fast background Geo-IP verification
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
      // Graceful fallback to INR
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

        <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[620px] mx-auto mb-8">
          Start for free with 1,000 complimentary AI runs and e-signatures. Upgrade as your team, client volume, and automated workflows scale.
        </p>

        {/* ── Cadence Selector (Monthly / Annual) ── */}
        <div className="flex items-center justify-center gap-3 mb-10">
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
                SAVE 20%
              </span>
            </button>
          </div>
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

      {/* ── 4 Main Pricing Cards Grid ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-20">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 items-stretch">
          
          {/* ══════════════════════════════════════════════════════════════
              PLAN 1: FREE FOREVER
          ══════════════════════════════════════════════════════════════ */}
          <div className="bg-white border border-zinc-200/90 rounded-[28px] p-6 sm:p-7 flex flex-col justify-between shadow-2xs hover:border-zinc-300 transition-all">
            <div className="space-y-4">
              <div>
                <span className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-500">
                  STARTER
                </span>
                <h3 className="font-display text-2xl font-bold text-zinc-950 mt-1">
                  Free Forever
                </h3>
                <p className="text-xs text-zinc-500 mt-1 leading-relaxed">
                  Essential operating tools for solo founders, independent consultants, and creators.
                </p>
              </div>

              {/* Price */}
              <div className="py-2 border-y border-zinc-100">
                <div className="flex items-baseline gap-1">
                  <span className="text-3xl sm:text-4xl font-display font-bold text-zinc-950">
                    {currency === 'INR' ? '₹0' : '$0'}
                  </span>
                  <span className="text-xs text-zinc-400 font-mono">/forever</span>
                </div>
                <div className="text-[11px] text-zinc-500 font-mono mt-0.5">
                  1,000 AI runs / month included
                </div>
              </div>

              {/* Feature Checklist */}
              <div className="space-y-2.5 text-xs text-zinc-700">
                <div className="font-semibold text-zinc-900 text-[11px] uppercase tracking-wider font-mono">
                  What&apos;s included:
                </div>
                <ul className="space-y-2">
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span><strong>1,000</strong> AI agent runs/month</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span>Google Gemini 2.5 Flash reasoning</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span>Unlimited SHA-256 E-Sign Vault</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span>Automated 18% GST tax calculation</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span>Kanban CRM &amp; Lead Funnel</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span>1 Workspace &amp; Community support</span>
                  </li>
                </ul>
              </div>
            </div>

            <div className="pt-6 mt-6 border-t border-zinc-100">
              <a
                href="https://app.heycora.in/workspace/login?plan=free"
                className="w-full inline-flex items-center justify-center gap-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-950 px-4 py-2.5 rounded-xl text-xs font-semibold transition-colors"
              >
                <span>Get Started Free</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-500" />
              </a>
            </div>
          </div>

          {/* ══════════════════════════════════════════════════════════════
              PLAN 2: INDIA ONLY EDITION / LOCALIZED GROWTH
          ══════════════════════════════════════════════════════════════ */}
          <div className="bg-[#0E1115] text-white border-2 border-zinc-800 rounded-[28px] p-6 sm:p-7 flex flex-col justify-between shadow-xl relative overflow-hidden">
            {/* Highlight Ribbon */}
            <div className="absolute top-3 right-3">
              <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-zinc-800 border border-zinc-700 text-[10px] font-mono font-bold text-white uppercase tracking-wider">
                <Flame className="w-3 h-3 text-zinc-300" />
                <span>{isIndia ? 'INDIA MSME' : 'POPULAR'}</span>
              </span>
            </div>

            <div className="space-y-4">
              <div>
                <span className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-400">
                  {isIndia ? 'INDIA EDITION' : 'ESSENTIALS'}
                </span>
                <h3 className="font-display text-2xl font-bold text-white mt-1">
                  {isIndia ? 'Bharat Growth' : 'Starter Growth'}
                </h3>
                <p className="text-xs text-zinc-400 mt-1 leading-relaxed">
                  {isIndia 
                    ? 'Optimized for Indian agencies, consultancies, studios & MSMEs with annual billing.'
                    : 'Complete operating suite for growing agencies and independent practices.'}
                </p>
              </div>

              {/* Price */}
              <div className="py-2 border-y border-zinc-800">
                <div className="flex items-baseline gap-1">
                  <span className="text-3xl sm:text-4xl font-display font-bold text-white">
                    {currency === 'INR'
                      ? (billingCycle === 'annual' ? '₹999' : '₹1,499')
                      : (billingCycle === 'annual' ? '$15' : '$19')}
                  </span>
                  <span className="text-xs text-zinc-400 font-mono">/month</span>
                </div>
                <div className="text-[11px] text-zinc-400 font-mono mt-0.5">
                  {billingCycle === 'annual' 
                    ? (currency === 'INR' ? 'Billed annually (₹11,988/yr)' : 'Billed annually ($180/yr)') 
                    : 'Billed monthly'} &bull; 10K runs/mo
                </div>
              </div>

              {/* Feature Checklist */}
              <div className="space-y-2.5 text-xs text-zinc-300">
                <div className="font-semibold text-zinc-200 text-[11px] uppercase tracking-wider font-mono">
                  Everything in Free, plus:
                </div>
                <ul className="space-y-2">
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-white shrink-0 mt-0.5" />
                    <span><strong>10,000</strong> AI agent runs/month</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-white shrink-0 mt-0.5" />
                    <span>Claude 3.5 Sonnet &amp; GPT-4o mini</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-white shrink-0 mt-0.5" />
                    <span>{isIndia ? 'Full GSTIN verification & tax splits' : 'Multi-currency invoicing & tax engine'}</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-white shrink-0 mt-0.5" />
                    <span>{isIndia ? 'Dynamic UPI QR code on all invoices' : 'Instant online client payment links'}</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-white shrink-0 mt-0.5" />
                    <span>WhatsApp automated client notifications</span>
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
                href={`https://app.heycora.in/workspace/login?plan=${isIndia ? 'india_annual' : 'growth'}`}
                className="w-full inline-flex items-center justify-center gap-2 bg-white hover:bg-zinc-100 text-zinc-950 px-4 py-2.5 rounded-xl text-xs font-bold transition-colors shadow-sm"
              >
                <span>Start 14-Day Free Trial</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-950" />
              </a>
            </div>
          </div>

          {/* ══════════════════════════════════════════════════════════════
              PLAN 3: PRO / GLOBAL GROWTH
          ══════════════════════════════════════════════════════════════ */}
          <div className="bg-white border border-zinc-200/90 rounded-[28px] p-6 sm:p-7 flex flex-col justify-between shadow-2xs hover:border-zinc-300 transition-all">
            <div className="space-y-4">
              <div>
                <span className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-500">
                  GROWTH
                </span>
                <h3 className="font-display text-2xl font-bold text-zinc-950 mt-1">
                  Global Pro
                </h3>
                <p className="text-xs text-zinc-500 mt-1 leading-relaxed">
                  For scaling professional services firms managing high client throughput and custom workflows.
                </p>
              </div>

              {/* Price */}
              <div className="py-2 border-y border-zinc-100">
                <div className="flex items-baseline gap-1">
                  <span className="text-3xl sm:text-4xl font-display font-bold text-zinc-950">
                    {currency === 'INR' 
                      ? (billingCycle === 'annual' ? '₹3,199' : '₹3,999')
                      : (billingCycle === 'annual' ? '$39' : '$49')}
                  </span>
                  <span className="text-xs text-zinc-400 font-mono">/month</span>
                </div>
                <div className="text-[11px] text-zinc-500 font-mono mt-0.5">
                  {billingCycle === 'annual' ? 'Billed annually (Save 20%)' : 'Billed monthly'} &bull; 50K runs/mo
                </div>
              </div>

              {/* Feature Checklist */}
              <div className="space-y-2.5 text-xs text-zinc-700">
                <div className="font-semibold text-zinc-900 text-[11px] uppercase tracking-wider font-mono">
                  Everything in {isIndia ? 'Bharat' : 'Growth'}, plus:
                </div>
                <ul className="space-y-2">
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span><strong>50,000</strong> AI reasoning runs/month</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span>All Frontier Models (Claude 3.5 &amp; GPT-4o)</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span>Autonomous Proposal &amp; Research Agent</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span>Unlimited team seats &amp; role permissions</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span>Custom Webhook Automations &amp; API</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span>24/7 Priority engineering queue</span>
                  </li>
                </ul>
              </div>
            </div>

            <div className="pt-6 mt-6 border-t border-zinc-100">
              <a
                href="https://app.heycora.in/workspace/login?plan=pro"
                className="w-full inline-flex items-center justify-center gap-2 bg-zinc-950 hover:bg-zinc-800 text-white px-4 py-2.5 rounded-xl text-xs font-semibold transition-colors shadow-xs"
              >
                <span>Start 14-Day Free Trial</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
              </a>
            </div>
          </div>

          {/* ══════════════════════════════════════════════════════════════
              PLAN 4: ENTERPRISE & SCALE
          ══════════════════════════════════════════════════════════════ */}
          <div className="bg-white border border-zinc-200/90 rounded-[28px] p-6 sm:p-7 flex flex-col justify-between shadow-2xs hover:border-zinc-300 transition-all">
            <div className="space-y-4">
              <div>
                <span className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-500">
                  ORGANIZATION
                </span>
                <h3 className="font-display text-2xl font-bold text-zinc-950 mt-1">
                  Enterprise
                </h3>
                <p className="text-xs text-zinc-500 mt-1 leading-relaxed">
                  For multi-branch organizations requiring bespoke AI quota, dedicated DPAs, and SSO.
                </p>
              </div>

              {/* Price */}
              <div className="py-2 border-y border-zinc-100">
                <div className="flex items-baseline gap-1">
                  <span className="text-3xl sm:text-4xl font-display font-bold text-zinc-950">
                    Custom
                  </span>
                </div>
                <div className="text-[11px] text-zinc-500 font-mono mt-0.5">
                  Volume actions &bull; Dedicated SLA
                </div>
              </div>

              {/* Feature Checklist */}
              <div className="space-y-2.5 text-xs text-zinc-700">
                <div className="font-semibold text-zinc-900 text-[11px] uppercase tracking-wider font-mono">
                  Everything in Pro, plus:
                </div>
                <ul className="space-y-2">
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span>Custom AI run volume &amp; fine-tuned models</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span>SAML / SSO &amp; Advanced RBAC</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span>Custom DPA &amp; DPDP Act 2023 Compliance</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span>White-label client portal &amp; custom domain</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span>Dedicated Solutions Architect</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0 mt-0.5" />
                    <span>99.98% Uptime SLA Agreement</span>
                  </li>
                </ul>
              </div>
            </div>

            <div className="pt-6 mt-6 border-t border-zinc-100">
              <Link
                href="/contact"
                className="w-full inline-flex items-center justify-center gap-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-950 px-4 py-2.5 rounded-xl text-xs font-semibold transition-colors"
              >
                <span>Talk to Solutions Team</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-500" />
              </Link>
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
                    <th className="p-4 sm:p-5 w-2/5">Capabilities &amp; Modules</th>
                    <th className="p-4 sm:p-5 w-[15%] text-center">Free Forever</th>
                    <th className="p-4 sm:p-5 w-[15%] text-center bg-zinc-100/70">{isIndia ? 'Bharat Growth' : 'Starter Growth'}</th>
                    <th className="p-4 sm:p-5 w-[15%] text-center">Global Pro</th>
                    <th className="p-4 sm:p-5 w-[15%] text-center">Enterprise</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-zinc-100">
                  
                  {/* Category 1: AI Operations */}
                  <tr className="bg-zinc-100/50">
                    <td colSpan={5} className="p-3.5 font-mono font-bold uppercase tracking-wider text-[11px] text-zinc-600">
                      1. AI Intelligence &amp; Autonomous Scoping
                    </td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">Monthly AI Runs</td>
                    <td className="p-4 text-center font-mono">1,000</td>
                    <td className="p-4 text-center font-mono bg-zinc-50 font-bold">10,000</td>
                    <td className="p-4 text-center font-mono">50,000</td>
                    <td className="p-4 text-center font-mono">Custom</td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">Supported LLM Models</td>
                    <td className="p-4 text-center text-zinc-600">Gemini 2.5 Flash</td>
                    <td className="p-4 text-center text-zinc-900 font-medium bg-zinc-50">Claude 3.5 + GPT-4o mini</td>
                    <td className="p-4 text-center text-zinc-900 font-medium">All Frontier Models</td>
                    <td className="p-4 text-center text-zinc-900 font-medium">Fine-tuned &amp; Dedicated</td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">Autonomous Proposal Builder</td>
                    <td className="p-4 text-center text-zinc-400"><Minus className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900 bg-zinc-50"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                  </tr>

                  {/* Category 2: CRM & Lead Management */}
                  <tr className="bg-zinc-100/50">
                    <td colSpan={5} className="p-3.5 font-mono font-bold uppercase tracking-wider text-[11px] text-zinc-600">
                      2. CRM, Pipelines &amp; Dispatch
                    </td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">Kanban Lead Pipeline</td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900 bg-zinc-50"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">WhatsApp Automated Dispatch</td>
                    <td className="p-4 text-center text-zinc-400">Manual templates</td>
                    <td className="p-4 text-center text-zinc-900 bg-zinc-50 font-medium">Meta Cloud API</td>
                    <td className="p-4 text-center text-zinc-900 font-medium">Meta Cloud API</td>
                    <td className="p-4 text-center text-zinc-900 font-medium">Dedicated Number Routing</td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">Team Seats Included</td>
                    <td className="p-4 text-center font-mono">1 seat</td>
                    <td className="p-4 text-center font-mono bg-zinc-50 font-bold">5 seats</td>
                    <td className="p-4 text-center font-mono">Unlimited</td>
                    <td className="p-4 text-center font-mono">Unlimited</td>
                  </tr>

                  {/* Category 3: Invoicing & Finance */}
                  <tr className="bg-zinc-100/50">
                    <td colSpan={5} className="p-3.5 font-mono font-bold uppercase tracking-wider text-[11px] text-zinc-600">
                      3. Finance &amp; Tax Engine
                    </td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">{isIndia ? '18% GST Invoicing & Math' : 'Tax & Automated Invoicing'}</td>
                    <td className="p-4 text-center text-zinc-900">Basic</td>
                    <td className="p-4 text-center text-zinc-900 bg-zinc-50 font-medium">{isIndia ? 'Auto CGST/SGST/IGST' : 'Automated Tax Engine'}</td>
                    <td className="p-4 text-center text-zinc-900 font-medium">Multi-Currency &amp; Tax</td>
                    <td className="p-4 text-center text-zinc-900 font-medium">Custom ERP Sync</td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">{isIndia ? 'Dynamic UPI QR Code Payment' : 'Online Client Payments'}</td>
                    <td className="p-4 text-center text-zinc-400"><Minus className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900 bg-zinc-50"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">{isIndia ? 'CA Tax Export (GSTR-1 Ready)' : 'Financial Accounting Export'}</td>
                    <td className="p-4 text-center text-zinc-400"><Minus className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900 bg-zinc-50"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                  </tr>

                  {/* Category 4: Contracts & Security */}
                  <tr className="bg-zinc-100/50">
                    <td colSpan={5} className="p-3.5 font-mono font-bold uppercase tracking-wider text-[11px] text-zinc-600">
                      4. Legal, E-Sign &amp; Enterprise Governance
                    </td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">SHA-256 E-Sign Vault</td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900 bg-zinc-50"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">Compliance &amp; Audit Trail</td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900 bg-zinc-50"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                    <td className="p-4 text-center text-zinc-900"><Check className="w-4 h-4 mx-auto" /></td>
                  </tr>
                  <tr>
                    <td className="p-4 font-medium text-zinc-800">Support &amp; Account SLA</td>
                    <td className="p-4 text-center text-zinc-500">Community</td>
                    <td className="p-4 text-center text-zinc-900 bg-zinc-50 font-medium">Priority ({isIndia ? 'IST < 2 hr' : '< 4 hr'})</td>
                    <td className="p-4 text-center text-zinc-900 font-medium">24/7 Priority Queue</td>
                    <td className="p-4 text-center text-zinc-900 font-medium">Dedicated Solutions Architect</td>
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
