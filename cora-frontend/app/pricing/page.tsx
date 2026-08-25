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
  Star,
  QrCode,
  Building2,
  Users,
  Send,
  LifeBuoy,
  Leaf,
  Rocket,
  TrendingUp,
  Shield
} from 'lucide-react';
import { trackEvent } from '@/components/analytics/Analytics';

// Official Vector WhatsApp SVG Icon
const WhatsAppIcon = ({ className = "w-4 h-4" }: { className?: string }) => (
  <svg viewBox="0 0 24 24" className={className} fill="currentColor">
    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.746.953 3.71 1.456 5.711 1.457h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
  </svg>
);

const FAQS = [
  {
    q: 'Is the Free Forever plan really free?',
    a: 'Yes, 100% free forever. It includes 1,000 complimentary AI agent runs every month, website builder (on heycora.in/your-name subdomain), Kanban CRM, unlimited* tamper-evident SHA-256 e-signatures, and automated GST invoicing with zero credit card required. Custom domain and custom email connection require an upgrade to a paid Growth plan.'
  },
  {
    q: 'How does the 2 Months Free on Annual plans work?',
    a: 'When you choose Annual billing on Starter, Professional, or Scale, you only pay for 10 months instead of 12 (giving you 2 full months completely free). In addition, you receive a free 1-year custom domain with SSL and +12,000 bonus AI runs across the year.'
  },
  {
    q: 'What is the India Only Plan and how does it compare to Starter?',
    a: 'The India Only Plan is an entry-level subsidized operating system at ₹499/month built specifically for single Indian freelancers, solopreneurs, and local studios. It includes 3,500 monthly AI runs, a free 1-year .in domain, dynamic UPI QR code payments on all invoices, 18% GST tax breakdown, and WhatsApp client dispatch. Starter (₹833/mo annual) is designed for growing teams, offering 5,000-6,000 monthly AI runs, global custom domain support (.com/.in), custom business email dispatch, and 2 team seats.'
  },
  {
    q: 'What is the difference between Starter and Professional?',
    a: 'Starter gives you custom domain connection (yourbrand.com/.in), custom email dispatch, 2 team seats, and 5,000-6,000 monthly AI runs. Professional upgrades your AI reasoning capacity to 20,000-21,000 runs/mo with Advanced AI Reasoning, adds full automated WhatsApp client workflows, Dynamic UPI QR codes on invoices, and expands team seats to 5.'
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
  const [showComparison, setShowComparison] = useState(false);
  const [showFloatingToggle, setShowFloatingToggle] = useState(false);
  const [openFaq, setOpenFaq] = useState<number | null>(null);

  const topToggleRef = useRef<HTMLDivElement>(null);

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

  // Strict single toggle visibility
  useEffect(() => {
    const handleScroll = () => {
      if (topToggleRef.current) {
        const rect = topToggleRef.current.getBoundingClientRect();
        if (rect.bottom < -20) {
          setShowFloatingToggle(true);
        } else {
          setShowFloatingToggle(false);
        }
      }
    };

    window.addEventListener('scroll', handleScroll, { passive: true });
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const toggleFaq = (index: number) => {
    setOpenFaq(openFaq === index ? null : index);
  };

  const handleToggleComparison = () => {
    const nextState = !showComparison;
    setShowComparison(nextState);
    trackEvent('pricing_comparison_toggle', { open: nextState });
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
                    Automated proposal drafting &amp; project brief generator
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
                    Unlimited* legally binding contracts with audit trail
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
          ROW 2: VALUE-FOR-MONEY PRICING CARDS WITH 2 MONTHS FREE ON ANNUAL
      ══════════════════════════════════════════════════════════════════════ */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-16">
        
        {/* Section Header & Primary Cadence Switcher */}
        <div className="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6 pb-4 border-b border-zinc-100">
          <div>
            <div className="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-400">
              GROWTH PLANS
            </div>
            <h2 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 mt-1">
              High-Throughput Operating Plans
            </h2>
            <p className="text-xs sm:text-sm text-zinc-500 mt-1">
              Connect your custom domain, automate client communications, and supercharge operations with advanced AI.
            </p>
          </div>

          {/* Primary Cadence Switcher with 2 Months Free badge */}
          <div ref={topToggleRef} className="flex flex-col sm:items-end gap-2 shrink-0">
            <div className="inline-flex items-center p-1 bg-zinc-100/90 rounded-full border border-zinc-200/80 shadow-2xs">
              <button
                type="button"
                onClick={() => {
                  setBillingCycle('monthly');
                  trackEvent('pricing_cycle_change', { cycle: 'monthly' });
                }}
                className={`px-5 py-2 rounded-full text-xs font-semibold transition-all cursor-pointer ${
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
                className={`px-5 py-2 rounded-full text-xs font-semibold transition-all flex items-center gap-1.5 cursor-pointer ${
                  billingCycle === 'annual'
                    ? 'bg-zinc-950 text-white shadow-xs'
                    : 'text-zinc-600 hover:text-zinc-950'
                }`}
              >
                <span>Annual Billing</span>
                <span className="bg-emerald-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                  2 Mo. Free
                </span>
              </button>
            </div>

            <div className="h-6 flex items-center text-xs">
              {billingCycle === 'annual' ? (
                <div className="inline-flex items-center gap-1.5 text-zinc-900 font-medium text-[11px] animate-in fade-in duration-150">
                  <Gift className="w-3.5 h-3.5 text-emerald-600" />
                  <span className="font-bold text-emerald-800">2 Months Free</span>
                  <span className="text-zinc-300">&bull;</span>
                  <span className="bg-zinc-100 border border-zinc-200 px-2 py-0.5 rounded text-zinc-800 font-mono text-[10px]">Free Custom Domain</span>
                  <span className="text-zinc-300">&bull;</span>
                  <span className="bg-zinc-100 border border-zinc-200 px-2 py-0.5 rounded text-zinc-800 font-mono text-[10px]">+12K Bonus Runs</span>
                </div>
              ) : (
                <div className="text-[11px] text-zinc-400 font-mono animate-in fade-in duration-150">
                  Switch to annual for 2 months free + free custom domain
                </div>
              )}
            </div>
          </div>
        </div>

        {/* 3-Tier SaaS Cards */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch pt-3">
          
          {/* CARD 1: STARTER */}
          <div className="bg-white border border-zinc-200 rounded-[28px] p-6 sm:p-7 flex flex-col justify-between shadow-[0_2px_12px_rgba(0,0,0,0.03)] hover:border-zinc-300 hover:shadow-md transition-all">
            <div>
              <div className="flex items-center justify-between gap-2 mb-2">
                <h3 className="font-display text-2xl font-bold text-zinc-950">
                  Starter
                </h3>
                {billingCycle === 'annual' && (
                  <span className="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-[10px] font-bold text-emerald-800 uppercase tracking-wider">
                    2 Mo. Free
                  </span>
                )}
              </div>
              <p className="text-xs text-zinc-500 leading-relaxed mb-5 min-h-[34px]">
                Establish your independent brand with custom domains (.com/.in), business email, and 5x AI capacity.
              </p>

              {/* Price Block */}
              <div className="mb-5">
                <div className="flex items-baseline gap-2">
                  <span className="text-4xl font-display font-extrabold text-zinc-950">
                    {billingCycle === 'annual'
                      ? (currency === 'INR' ? '₹833' : '$7.50')
                      : (currency === 'INR' ? '₹999' : '$9')}
                  </span>
                  <span className="text-xs text-zinc-500 font-medium">/ month</span>
                  {billingCycle === 'annual' && (
                    <span className="text-xs text-zinc-400 line-through font-mono">
                      {currency === 'INR' ? '₹999' : '$9'}
                    </span>
                  )}
                </div>
                <div className="text-[11px] text-zinc-500 mt-1 font-mono">
                  {billingCycle === 'annual'
                    ? (currency === 'INR' ? 'Billed annually at ₹9,990/yr (Save ₹1,998)' : 'Billed annually at $90/yr (Save $18)')
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
                    <span>Connect custom domain (.com / .in)</span>
                  </li>
                  <li className="flex items-center gap-2.5">
                    <Check className="w-4 h-4 text-zinc-950 shrink-0" />
                    <span>Custom business email sending</span>
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

          {/* CARD 2: PROFESSIONAL */}
          <div className="bg-white border-2 border-zinc-950 rounded-[28px] p-6 sm:p-7 flex flex-col justify-between shadow-[0_12px_40px_rgba(0,0,0,0.08)] relative mt-0 md:-mt-3">
            
            <div className="absolute -top-3.5 inset-x-0 flex justify-center">
              <span className="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-zinc-950 text-white text-[11px] font-bold tracking-wide shadow-md">
                <Star className="w-3 h-3 fill-amber-400 text-amber-400" />
                <span>RECOMMENDED &bull; BEST VALUE</span>
              </span>
            </div>

            <div>
              <div className="flex items-center justify-between gap-2 mb-2 mt-1">
                <h3 className="font-display text-2xl font-bold text-zinc-950">
                  Professional
                </h3>
                <span className="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-[10px] font-bold text-emerald-800 uppercase tracking-wider">
                  2 Mo. Free
                </span>
              </div>
              <p className="text-xs text-zinc-500 leading-relaxed mb-5 min-h-[34px]">
                Autonomous operating backbone with advanced AI, official WhatsApp dispatch, and UPI QR.
              </p>

              {/* Price Block */}
              <div className="mb-5">
                <div className="flex items-baseline gap-2">
                  <span className="text-4xl font-display font-extrabold text-zinc-950">
                    {billingCycle === 'annual'
                      ? (currency === 'INR' ? '₹1,665' : '$15.80')
                      : (currency === 'INR' ? '₹1,999' : '$19')}
                  </span>
                  <span className="text-xs text-zinc-500 font-medium">/ month</span>
                  {billingCycle === 'annual' && (
                    <span className="text-xs text-zinc-400 line-through font-mono">
                      {currency === 'INR' ? '₹1,999' : '$19'}
                    </span>
                  )}
                </div>
                <div className="text-[11px] text-zinc-500 mt-1 font-mono">
                  {billingCycle === 'annual'
                    ? (currency === 'INR' ? 'Billed annually at ₹19,990/yr (Save ₹3,998)' : 'Billed annually at $190/yr (Save $38)')
                    : 'Billed monthly'}
                </div>
              </div>

              {/* Action CTA */}
              <a
                href="https://app.heycora.in/workspace/login?plan=pro"
                className="w-full inline-flex items-center justify-center gap-2 bg-zinc-950 hover:bg-zinc-800 text-white py-3.5 px-4 rounded-xl text-xs font-bold transition-all shadow-md hover:shadow-lg mb-6"
              >
                <span>Get started with Professional</span>
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
                    <span><strong>Advanced AI Reasoning Engine</strong></span>
                  </li>
                  <li className="flex items-center gap-2.5">
                    <Sparkles className="w-4 h-4 text-amber-500 shrink-0" />
                    <span><strong>{billingCycle === 'annual' ? '21,000' : '20,000'}</strong> AI Runs / month</span>
                  </li>
                  <li className="flex items-center gap-2.5 text-zinc-950 font-medium">
                    <WhatsAppIcon className="w-4 h-4 text-[#25D366] shrink-0" />
                    <span>Official WhatsApp automated dispatch</span>
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
              <div className="flex items-center justify-between gap-2 mb-2">
                <h3 className="font-display text-2xl font-bold text-zinc-950">
                  Scale
                </h3>
                {billingCycle === 'annual' && (
                  <span className="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-[10px] font-bold text-emerald-800 uppercase tracking-wider">
                    2 Mo. Free
                  </span>
                )}
              </div>
              <p className="text-xs text-zinc-500 leading-relaxed mb-5 min-h-[34px]">
                High-throughput infrastructure for agencies &amp; multi-member teams.
              </p>

              {/* Price Block */}
              <div className="mb-5">
                <div className="flex items-baseline gap-2">
                  <span className="text-4xl font-display font-extrabold text-zinc-950">
                    {billingCycle === 'annual'
                      ? (currency === 'INR' ? '₹2,499' : '$24.10')
                      : (currency === 'INR' ? '₹2,999' : '$29')}
                  </span>
                  <span className="text-xs text-zinc-500 font-medium">/ month</span>
                  {billingCycle === 'annual' && (
                    <span className="text-xs text-zinc-400 line-through font-mono">
                      {currency === 'INR' ? '₹2,999' : '$29'}
                    </span>
                  )}
                </div>
                <div className="text-[11px] text-zinc-500 mt-1 font-mono">
                  {billingCycle === 'annual'
                    ? (currency === 'INR' ? 'Billed annually at ₹29,990/yr (Save ₹5,998)' : 'Billed annually at $290/yr (Save $58)')
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
                    <span><strong>All Frontier AI Engines</strong></span>
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
                    <span>Unlimited* team seats</span>
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
            onClick={handleToggleComparison}
            className="inline-flex items-center gap-2.5 px-8 py-3.5 rounded-full bg-zinc-100 hover:bg-zinc-200 text-xs font-bold text-zinc-950 transition-all border border-zinc-200 shadow-2xs hover:shadow-xs active:scale-[0.98] cursor-pointer"
          >
            <span>{showComparison ? 'Hide feature comparison' : 'Compare all features in detail'}</span>
            {showComparison ? (
              <ChevronUp className="w-4 h-4 text-zinc-700 transition-transform duration-200" />
            ) : (
              <ChevronDown className="w-4 h-4 text-zinc-700 transition-transform duration-200" />
            )}
          </button>
        </div>

        {/* ══════════════════════════════════════════════════════════════════════
            ACCORDION COMPARISON MATRIX (PERFECTLY ALIGNED STICKY CARDS & IN-CARD CTAS)
        ══════════════════════════════════════════════════════════════════════ */}
        {showComparison && (
          <div className="mt-12 bg-white border border-zinc-200/90 rounded-[32px] p-4 sm:p-7 shadow-[0_8px_32px_rgba(0,0,0,0.03)] animate-in fade-in slide-in-from-top-3 duration-200">
            
            {/* Top Heading Block */}
            <div className="text-center max-w-[700px] mx-auto mb-8 pt-2">
              <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200/60 text-[11px] font-mono font-semibold uppercase tracking-wider text-emerald-800 mb-3">
                <Sparkles className="w-3.5 h-3.5 text-emerald-600" />
                <span>SIDE-BY-SIDE FEATURE MATRIX</span>
              </div>
              <h3 className="font-display text-2xl sm:text-4xl font-extrabold text-zinc-950 tracking-tight">
                Compare plans at a glance
              </h3>
              <p className="text-xs sm:text-sm text-zinc-500 mt-2 leading-relaxed">
                Everything included in each tier, from AI quota and custom domains to WhatsApp automations and GST tax compliance.
              </p>
            </div>

            {/* Scrollable Responsive Table with PERFECTLY ALIGNED STICKY HEADER CARDS */}
            <div className="overflow-x-auto relative">
              <table className="w-full text-left border-collapse text-xs min-w-[900px]">
                
                {/* ── Perfectly Aligned Sticky Header with In-Card CTAs ── */}
                <thead className="sticky top-16 z-20 bg-white/95 backdrop-blur-md transition-all shadow-[0_4px_12px_rgba(0,0,0,0.03)] border-b border-zinc-200/80">
                  <tr className="align-top">
                    
                    {/* Left title column */}
                    <th className="p-2.5 w-[24%] align-top bg-white">
                      <div className="h-[210px] p-3 flex flex-col justify-between">
                        <div>
                          <div className="h-5 mb-1" /> {/* Spacer aligning with badge row */}
                          <div className="font-bold text-sm sm:text-base text-zinc-950">Plans &amp; features</div>
                          <div className="text-xs text-zinc-500 font-normal mt-1 leading-snug">
                            All plans include core platform modules, regular updates, and 24/7 support.
                          </div>
                        </div>
                        <div className="text-[11px] font-mono text-zinc-400">
                          Toggle billing above or below ↕
                        </div>
                      </div>
                    </th>

                    {/* Free Forever Header Card */}
                    <th className="p-2.5 w-[15%] align-top bg-white">
                      <div className="h-[210px] bg-zinc-50/90 border border-zinc-200/80 rounded-2xl p-3.5 flex flex-col justify-between hover:border-zinc-300 transition-all">
                        <div>
                          <div className="h-5 mb-1 flex items-center" /> {/* Spacer */}
                          <div className="flex items-center justify-between gap-1">
                            <span className="font-bold text-xs sm:text-sm text-zinc-950">Free Forever</span>
                            <span className="w-5 h-5 rounded-full bg-emerald-100/70 text-emerald-700 flex items-center justify-center shrink-0">
                              <Leaf className="w-3 h-3" />
                            </span>
                          </div>
                          <div className="font-mono text-emerald-600 font-bold text-xs mt-1">₹0 / Free</div>
                          <div className="text-[10px] text-zinc-500 font-normal mt-1 h-7 line-clamp-2 leading-tight">
                            For individuals getting started
                          </div>
                        </div>

                        <a
                          href="https://app.heycora.in/workspace/login?plan=free"
                          className="w-full inline-flex items-center justify-center py-2 px-2 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white text-[11px] font-semibold transition-all shadow-2xs"
                        >
                          Start Free
                        </a>
                      </div>
                    </th>

                    {/* Starter Header Card */}
                    <th className="p-2.5 w-[15%] align-top bg-white">
                      <div className="h-[210px] bg-zinc-50/90 border border-zinc-200/80 rounded-2xl p-3.5 flex flex-col justify-between hover:border-zinc-300 transition-all">
                        <div>
                          <div className="h-5 mb-1 flex items-center" /> {/* Spacer */}
                          <div className="flex items-center justify-between gap-1">
                            <span className="font-bold text-xs sm:text-sm text-zinc-950">Starter</span>
                            <span className="w-5 h-5 rounded-full bg-emerald-100/70 text-emerald-700 flex items-center justify-center shrink-0">
                              <Rocket className="w-3 h-3" />
                            </span>
                          </div>
                          <div className="font-mono text-zinc-950 font-bold text-xs mt-1">
                            {billingCycle === 'annual' ? (currency === 'INR' ? '₹833/mo' : '$7.50/mo') : (currency === 'INR' ? '₹999/mo' : '$9/mo')}
                          </div>
                          <div className="text-[10px] text-zinc-500 font-normal mt-1 h-7 line-clamp-2 leading-tight">
                            For small teams &amp; early stage
                          </div>
                        </div>

                        <a
                          href="https://app.heycora.in/workspace/login?plan=starter"
                          className="w-full inline-flex items-center justify-center py-2 px-2 rounded-xl bg-zinc-200/80 hover:bg-zinc-300 text-zinc-950 text-[11px] font-semibold transition-all"
                        >
                          Get Starter
                        </a>
                      </div>
                    </th>

                    {/* Professional Header (Featured Highlighted Card with MOST POPULAR Badge) */}
                    <th className="p-2.5 w-[16%] align-top bg-white">
                      <div className="h-[210px] relative bg-white border-2 border-emerald-500 rounded-2xl p-3.5 flex flex-col justify-between shadow-[0_4px_20px_rgba(16,185,129,0.08)]">
                        <div>
                          <div className="h-5 mb-1 flex items-center justify-center">
                            <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-zinc-950 text-white text-[9px] font-mono font-bold tracking-wider uppercase shadow-xs">
                              <Star className="w-2.5 h-2.5 fill-amber-400 text-amber-400" />
                              <span>MOST POPULAR</span>
                            </span>
                          </div>
                          <div className="flex items-center justify-between gap-1">
                            <span className="font-bold text-xs sm:text-sm text-zinc-950">Professional</span>
                          </div>
                          <div className="font-mono text-zinc-950 font-extrabold text-xs sm:text-sm mt-1">
                            {billingCycle === 'annual' ? (currency === 'INR' ? '₹1,665/mo' : '$15.80/mo') : (currency === 'INR' ? '₹1,999/mo' : '$19/mo')}
                          </div>
                          <div className="text-[10px] text-zinc-500 font-normal mt-1 h-7 line-clamp-2 leading-tight">
                            For growing businesses
                          </div>
                        </div>

                        <a
                          href="https://app.heycora.in/workspace/login?plan=pro"
                          className="w-full inline-flex items-center justify-center py-2 px-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold transition-all shadow-xs"
                        >
                          Get Pro
                        </a>
                      </div>
                    </th>

                    {/* Scale Header Card */}
                    <th className="p-2.5 w-[15%] align-top bg-white">
                      <div className="h-[210px] bg-zinc-50/90 border border-zinc-200/80 rounded-2xl p-3.5 flex flex-col justify-between hover:border-zinc-300 transition-all">
                        <div>
                          <div className="h-5 mb-1 flex items-center" /> {/* Spacer */}
                          <div className="flex items-center justify-between gap-1">
                            <span className="font-bold text-xs sm:text-sm text-zinc-950">Scale</span>
                            <span className="w-5 h-5 rounded-full bg-emerald-100/70 text-emerald-700 flex items-center justify-center shrink-0">
                              <TrendingUp className="w-3 h-3" />
                            </span>
                          </div>
                          <div className="font-mono text-zinc-950 font-bold text-xs mt-1">
                            {billingCycle === 'annual' ? (currency === 'INR' ? '₹2,499/mo' : '$24/mo') : (currency === 'INR' ? '₹2,999/mo' : '$29/mo')}
                          </div>
                          <div className="text-[10px] text-zinc-500 font-normal mt-1 h-7 line-clamp-2 leading-tight">
                            For high-growth &amp; teams
                          </div>
                        </div>

                        <a
                          href="https://app.heycora.in/workspace/login?plan=scale"
                          className="w-full inline-flex items-center justify-center py-2 px-2 rounded-xl bg-zinc-200/80 hover:bg-zinc-300 text-zinc-950 text-[11px] font-semibold transition-all"
                        >
                          Get Scale
                        </a>
                      </div>
                    </th>

                    {/* India Plan Header (Warm Tinted Card with INDIA EDITION Badge) */}
                    <th className="p-2.5 w-[15%] align-top bg-white">
                      <div className="h-[210px] bg-gradient-to-b from-amber-50/70 to-amber-50/30 border border-amber-200/90 rounded-2xl p-3.5 flex flex-col justify-between">
                        <div>
                          <div className="h-5 mb-1 flex items-center justify-center">
                            <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-amber-100/80 text-[9px] font-mono font-bold text-amber-900 uppercase tracking-wide">
                              <span>🇮🇳 INDIA EDITION</span>
                            </span>
                          </div>
                          <div className="flex items-center justify-between gap-1">
                            <span className="font-bold text-xs sm:text-sm text-amber-950">India Plan</span>
                          </div>
                          <div className="font-mono text-amber-900 font-bold text-xs mt-1">
                            ₹499/mo <span className="font-normal text-amber-700 text-[9px]">(Annual)</span>
                          </div>
                          <div className="text-[10px] text-zinc-500 font-normal mt-1 h-7 line-clamp-2 leading-tight">
                            Entry solopreneur &amp; UPI
                          </div>
                        </div>

                        <a
                          href="https://app.heycora.in/workspace/login?plan=india_annual_499"
                          className="w-full inline-flex items-center justify-center py-2 px-2 rounded-xl bg-amber-950 hover:bg-amber-900 text-white text-[11px] font-bold transition-all shadow-2xs"
                        >
                          Claim India
                        </a>
                      </div>
                    </th>

                  </tr>
                </thead>

                <tbody className="text-zinc-700">
                  
                  {/* ─────────────────────────────────────────────────────────────
                      CATEGORY 1: AI & AUTOMATION
                  ───────────────────────────────────────────────────────────── */}
                  <tr>
                    <td colSpan={6} className="pt-6 pb-2">
                      <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-50/70 text-emerald-900 text-xs font-mono font-bold uppercase tracking-wider">
                        <Sparkles className="w-3.5 h-3.5 text-emerald-600" />
                        <span>AI &amp; AUTOMATION</span>
                      </div>
                    </td>
                  </tr>

                  <tr className="border-b border-zinc-100 hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 px-4 font-medium text-zinc-900">Monthly AI Runs</td>
                    <td className="py-3.5 px-4 text-center font-mono text-zinc-700">1,000 / mo</td>
                    <td className="py-3.5 px-4 text-center font-mono font-bold text-zinc-900 bg-zinc-50/40">{billingCycle === 'annual' ? '6,000' : '5,000'} / mo</td>
                    <td className="py-3.5 px-4 text-center font-mono font-bold text-emerald-700 bg-emerald-50/20">{billingCycle === 'annual' ? '21,000' : '20,000'} / mo</td>
                    <td className="py-3.5 px-4 text-center font-mono text-zinc-700">{billingCycle === 'annual' ? '61,000' : '60,000'} / mo</td>
                    <td className="py-3.5 px-4 text-center font-mono font-semibold text-amber-950 bg-amber-50/20">3,500 / mo</td>
                  </tr>

                  <tr className="border-b border-zinc-100 hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 px-4 font-medium text-zinc-900">AI Intelligence Engine Tier</td>
                    <td className="py-3.5 px-4 text-center text-zinc-600">Standard AI</td>
                    <td className="py-3.5 px-4 text-center font-medium text-zinc-900 bg-zinc-50/40">Standard AI</td>
                    <td className="py-3.5 px-4 text-center font-semibold text-emerald-700 bg-emerald-50/20">Advanced AI Reasoning</td>
                    <td className="py-3.5 px-4 text-center font-semibold text-zinc-950">All Frontier AI Engines</td>
                    <td className="py-3.5 px-4 text-center text-amber-950 bg-amber-50/20">Standard AI</td>
                  </tr>

                  <tr className="border-b border-zinc-100 hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 px-4 font-medium text-zinc-900">Autonomous Proposal &amp; Brief Generator</td>
                    <td className="py-3.5 px-4 text-center"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-zinc-50/40"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-emerald-50/20"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.5]" /></td>
                    <td className="py-3.5 px-4 text-center"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-amber-50/20"><Check className="w-4 h-4 mx-auto text-amber-800 stroke-[2.2]" /></td>
                  </tr>

                  <tr className="border-b border-zinc-100 hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 px-4 font-medium text-zinc-900">Autonomous AI Research Agent</td>
                    <td className="py-3.5 px-4 text-center text-zinc-300"><Minus className="w-3.5 h-3.5 mx-auto" /></td>
                    <td className="py-3.5 px-4 text-center text-zinc-300 bg-zinc-50/40"><Minus className="w-3.5 h-3.5 mx-auto" /></td>
                    <td className="py-3.5 px-4 text-center bg-emerald-50/20"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-amber-50/20 text-zinc-300"><Minus className="w-3.5 h-3.5 mx-auto" /></td>
                  </tr>

                  {/* ─────────────────────────────────────────────────────────────
                      CATEGORY 2: DOMAINS & WEB PRESENCE
                  ───────────────────────────────────────────────────────────── */}
                  <tr>
                    <td colSpan={6} className="pt-6 pb-2">
                      <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-50/70 text-emerald-900 text-xs font-mono font-bold uppercase tracking-wider">
                        <Globe className="w-3.5 h-3.5 text-emerald-600" />
                        <span>DOMAINS &amp; WEB PRESENCE</span>
                      </div>
                    </td>
                  </tr>

                  <tr className="border-b border-zinc-100 hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 px-4 font-medium text-zinc-900">Portfolio &amp; Booking Website Builder</td>
                    <td className="py-3.5 px-4 text-center"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-zinc-50/40"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-emerald-50/20"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.5]" /></td>
                    <td className="py-3.5 px-4 text-center"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-amber-50/20"><Check className="w-4 h-4 mx-auto text-amber-800 stroke-[2.2]" /></td>
                  </tr>

                  <tr className="border-b border-zinc-100 hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 px-4 font-medium text-zinc-900">Website Hosting URL</td>
                    <td className="py-3.5 px-4 text-center font-mono text-[11px] text-zinc-500">heycora.in/you</td>
                    <td className="py-3.5 px-4 text-center font-mono text-[11px] font-bold text-zinc-900 bg-zinc-50/40">Custom Domain (.com/.in)</td>
                    <td className="py-3.5 px-4 text-center font-mono text-[11px] font-bold text-emerald-700 bg-emerald-50/20">Custom Domain</td>
                    <td className="py-3.5 px-4 text-center font-mono text-[11px] text-zinc-800">Custom Domain</td>
                    <td className="py-3.5 px-4 text-center font-mono text-[11px] font-semibold text-amber-950 bg-amber-50/20">Custom .in Domain</td>
                  </tr>

                  <tr className="border-b border-zinc-100 hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 px-4 font-medium text-zinc-900">Free 1-Year Custom Domain (Annual)</td>
                    <td className="py-3.5 px-4 text-center text-zinc-300"><Minus className="w-3.5 h-3.5 mx-auto" /></td>
                    <td className="py-3.5 px-4 text-center bg-zinc-50/40 font-mono text-[11px] font-bold text-zinc-950">Free (.com / .in)</td>
                    <td className="py-3.5 px-4 text-center bg-emerald-50/20 font-mono text-[11px] font-bold text-emerald-800">Free (.com / .in)</td>
                    <td className="py-3.5 px-4 text-center font-mono text-[11px] font-bold text-zinc-950">Free (.com / .in)</td>
                    <td className="py-3.5 px-4 text-center font-mono text-[11px] font-semibold text-amber-950 bg-amber-50/20">Free .in Domain</td>
                  </tr>

                  <tr className="border-b border-zinc-100 hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 px-4 font-medium text-zinc-900">White-label &amp; Remove Cora Branding</td>
                    <td className="py-3.5 px-4 text-center text-zinc-300"><Minus className="w-3.5 h-3.5 mx-auto" /></td>
                    <td className="py-3.5 px-4 text-center bg-zinc-50/40"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-emerald-50/20"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.5]" /></td>
                    <td className="py-3.5 px-4 text-center"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-amber-50/20"><Check className="w-4 h-4 mx-auto text-amber-800 stroke-[2.2]" /></td>
                  </tr>

                  {/* ─────────────────────────────────────────────────────────────
                      CATEGORY 3: CLIENT CRM & TEAM
                  ───────────────────────────────────────────────────────────── */}
                  <tr>
                    <td colSpan={6} className="pt-6 pb-2">
                      <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-50/70 text-emerald-900 text-xs font-mono font-bold uppercase tracking-wider">
                        <Users className="w-3.5 h-3.5 text-emerald-600" />
                        <span>CLIENT CRM &amp; TEAM COLLABORATION</span>
                      </div>
                    </td>
                  </tr>

                  <tr className="border-b border-zinc-100 hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 px-4 font-medium text-zinc-900">Kanban Lead Funnel</td>
                    <td className="py-3.5 px-4 text-center"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-zinc-50/40"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-emerald-50/20"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.5]" /></td>
                    <td className="py-3.5 px-4 text-center"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-amber-50/20"><Check className="w-4 h-4 mx-auto text-amber-800 stroke-[2.2]" /></td>
                  </tr>

                  <tr className="border-b border-zinc-100 hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 px-4 font-medium text-zinc-900">Team Seats</td>
                    <td className="py-3.5 px-4 text-center font-mono text-zinc-700">1 seat</td>
                    <td className="py-3.5 px-4 text-center font-mono font-bold text-zinc-950 bg-zinc-50/40">2 seats</td>
                    <td className="py-3.5 px-4 text-center font-mono font-bold text-emerald-700 bg-emerald-50/20">5 seats</td>
                    <td className="py-3.5 px-4 text-center font-mono font-bold text-zinc-950">Unlimited*</td>
                    <td className="py-3.5 px-4 text-center font-mono text-zinc-700 bg-amber-50/20">1 seat</td>
                  </tr>

                  <tr className="border-b border-zinc-100 hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 px-4 font-medium text-zinc-900">Shoot Schedules &amp; Call-Sheets</td>
                    <td className="py-3.5 px-4 text-center"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-zinc-50/40"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-emerald-50/20"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.5]" /></td>
                    <td className="py-3.5 px-4 text-center"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-amber-50/20"><Check className="w-4 h-4 mx-auto text-amber-800 stroke-[2.2]" /></td>
                  </tr>

                  {/* ─────────────────────────────────────────────────────────────
                      CATEGORY 4: LEGAL E-SIGN & CONTRACTS
                  ───────────────────────────────────────────────────────────── */}
                  <tr>
                    <td colSpan={6} className="pt-6 pb-2">
                      <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-50/70 text-emerald-900 text-xs font-mono font-bold uppercase tracking-wider">
                        <FileCheck className="w-3.5 h-3.5 text-emerald-600" />
                        <span>LEGAL CONTRACTS &amp; CRYPTOGRAPHIC E-SIGN</span>
                      </div>
                    </td>
                  </tr>

                  <tr className="border-b border-zinc-100 hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 px-4 font-medium text-zinc-900">SHA-256 Tamper-Evident Vault</td>
                    <td className="py-3.5 px-4 text-center"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-zinc-50/40"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-emerald-50/20"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.5]" /></td>
                    <td className="py-3.5 px-4 text-center"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-amber-50/20"><Check className="w-4 h-4 mx-auto text-amber-800 stroke-[2.2]" /></td>
                  </tr>

                  <tr className="border-b border-zinc-100 hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 px-4 font-medium text-zinc-900">Legally Enforceable (Indian IT Act &amp; UETA)</td>
                    <td className="py-3.5 px-4 text-center"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-zinc-50/40"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-emerald-50/20"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.5]" /></td>
                    <td className="py-3.5 px-4 text-center"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-amber-50/20"><Check className="w-4 h-4 mx-auto text-amber-800 stroke-[2.2]" /></td>
                  </tr>

                  {/* ─────────────────────────────────────────────────────────────
                      CATEGORY 5: INVOICING & GST COMPLIANCE
                  ───────────────────────────────────────────────────────────── */}
                  <tr>
                    <td colSpan={6} className="pt-6 pb-2">
                      <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-50/70 text-emerald-900 text-xs font-mono font-bold uppercase tracking-wider">
                        <Receipt className="w-3.5 h-3.5 text-emerald-600" />
                        <span>INVOICING, PAYMENTS &amp; INDIAN GST</span>
                      </div>
                    </td>
                  </tr>

                  <tr className="border-b border-zinc-100 hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 px-4 font-medium text-zinc-900">18% GST Invoicing Engine</td>
                    <td className="py-3.5 px-4 text-center text-zinc-600">Basic Tax Math</td>
                    <td className="py-3.5 px-4 text-center font-medium text-zinc-900 bg-zinc-50/40">Auto CGST / SGST / IGST</td>
                    <td className="py-3.5 px-4 text-center font-semibold text-emerald-700 bg-emerald-50/20">Auto CGST / SGST / IGST</td>
                    <td className="py-3.5 px-4 text-center font-semibold text-zinc-900">Custom Multi-State Math</td>
                    <td className="py-3.5 px-4 text-center font-medium text-amber-950 bg-amber-50/20">Auto CGST / SGST / IGST</td>
                  </tr>

                  <tr className="border-b border-zinc-100 hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 px-4 font-medium text-zinc-900">Dynamic UPI QR Code (Zero Gateway Fees)</td>
                    <td className="py-3.5 px-4 text-center text-zinc-300"><Minus className="w-3.5 h-3.5 mx-auto" /></td>
                    <td className="py-3.5 px-4 text-center bg-zinc-50/40"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-emerald-50/20"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.5]" /></td>
                    <td className="py-3.5 px-4 text-center"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-amber-50/20"><Check className="w-4 h-4 mx-auto text-amber-800 stroke-[2.2]" /></td>
                  </tr>

                  {/* ─────────────────────────────────────────────────────────────
                      CATEGORY 6: DISPATCH & INTEGRATIONS
                  ───────────────────────────────────────────────────────────── */}
                  <tr>
                    <td colSpan={6} className="pt-6 pb-2">
                      <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-50/70 text-emerald-900 text-xs font-mono font-bold uppercase tracking-wider">
                        <Send className="w-3.5 h-3.5 text-emerald-600" />
                        <span>CLIENT DISPATCH &amp; AUTOMATIONS</span>
                      </div>
                    </td>
                  </tr>

                  <tr className="border-b border-zinc-100 hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 px-4 font-medium text-zinc-900">WhatsApp Business Automation</td>
                    <td className="py-3.5 px-4 text-center text-zinc-300"><Minus className="w-3.5 h-3.5 mx-auto" /></td>
                    <td className="py-3.5 px-4 text-center font-mono text-[11px] text-zinc-600 bg-zinc-50/40">Quick Dispatch</td>
                    <td className="py-3.5 px-4 text-center bg-emerald-50/20"><Check className="w-4 h-4 mx-auto text-[#25D366] stroke-[2.5]" /></td>
                    <td className="py-3.5 px-4 text-center"><Check className="w-4 h-4 mx-auto text-[#25D366] stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-amber-50/20 font-mono text-[11px] text-amber-950">Quick Dispatch</td>
                  </tr>

                  <tr className="border-b border-zinc-100 hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 px-4 font-medium text-zinc-900">Custom Email Dispatch (Your Domain)</td>
                    <td className="py-3.5 px-4 text-center text-zinc-300 font-mono text-[11px]">System mail</td>
                    <td className="py-3.5 px-4 text-center bg-zinc-50/40"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-emerald-50/20"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.5]" /></td>
                    <td className="py-3.5 px-4 text-center"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-amber-50/20 font-mono text-[11px] text-zinc-400">System mail</td>
                  </tr>

                  <tr className="border-b border-zinc-100 hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 px-4 font-medium text-zinc-900">Custom Webhooks &amp; REST APIs</td>
                    <td className="py-3.5 px-4 text-center text-zinc-300"><Minus className="w-3.5 h-3.5 mx-auto" /></td>
                    <td className="py-3.5 px-4 text-center text-zinc-300 bg-zinc-50/40"><Minus className="w-3.5 h-3.5 mx-auto" /></td>
                    <td className="py-3.5 px-4 text-center bg-emerald-50/20 text-zinc-300"><Minus className="w-3.5 h-3.5 mx-auto" /></td>
                    <td className="py-3.5 px-4 text-center"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-amber-50/20 text-zinc-300"><Minus className="w-3.5 h-3.5 mx-auto" /></td>
                  </tr>

                  {/* ─────────────────────────────────────────────────────────────
                      CATEGORY 7: SECURITY & SLA
                  ───────────────────────────────────────────────────────────── */}
                  <tr>
                    <td colSpan={6} className="pt-6 pb-2">
                      <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-50/70 text-emerald-900 text-xs font-mono font-bold uppercase tracking-wider">
                        <LifeBuoy className="w-3.5 h-3.5 text-emerald-600" />
                        <span>SECURITY, SUPPORT &amp; SLA</span>
                      </div>
                    </td>
                  </tr>

                  <tr className="border-b border-zinc-100 hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 px-4 font-medium text-zinc-900">Data Encryption &amp; DPDP 2023</td>
                    <td className="py-3.5 px-4 text-center"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-zinc-50/40"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-emerald-50/20"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.5]" /></td>
                    <td className="py-3.5 px-4 text-center"><Check className="w-4 h-4 mx-auto text-emerald-600 stroke-[2.2]" /></td>
                    <td className="py-3.5 px-4 text-center bg-amber-50/20"><Check className="w-4 h-4 mx-auto text-amber-800 stroke-[2.2]" /></td>
                  </tr>

                  <tr className="border-b border-zinc-100 hover:bg-zinc-50/50 transition-colors">
                    <td className="py-3.5 px-4 font-medium text-zinc-900">Support Channel</td>
                    <td className="py-3.5 px-4 text-center text-zinc-500">Community</td>
                    <td className="py-3.5 px-4 text-center font-medium text-zinc-800 bg-zinc-50/40">Email (24h)</td>
                    <td className="py-3.5 px-4 text-center font-semibold text-emerald-700 bg-emerald-50/20">Priority Support</td>
                    <td className="py-3.5 px-4 text-center font-semibold text-zinc-950">Dedicated SLA</td>
                    <td className="py-3.5 px-4 text-center text-zinc-700 bg-amber-50/20">Email + IST Desk</td>
                  </tr>

                </tbody>
              </table>
            </div>

            {/* ── Table Bottom Assurance Footer Bar ── */}
            <div className="mt-6 rounded-2xl bg-emerald-50/40 border border-emerald-100 p-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-zinc-700">
              <div className="flex items-center gap-2">
                <Sparkles className="w-4 h-4 text-emerald-600 shrink-0" />
                <span>All plans include core platform modules, regular updates, and 24/7 support.</span>
              </div>
              <div className="flex items-center gap-2 text-zinc-600 shrink-0 font-medium">
                <Shield className="w-4 h-4 text-zinc-700 shrink-0" />
                <span>Secure. Compliant. Built for modern businesses.</span>
              </div>
            </div>

          </div>
        )}

      </section>

      {/* ══════════════════════════════════════════════════════════════════════
          ROW 3: INDIA ONLY PLAN (BALANCED ENTRY-LEVEL SOLOPRENEUR SHOWCASE)
      ══════════════════════════════════════════════════════════════════════ */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-20">
        <div className="bg-gradient-to-br from-zinc-50/90 via-white to-amber-50/20 border-2 border-zinc-200/90 hover:border-zinc-300 rounded-[32px] p-6 sm:p-9 shadow-[0_8px_32px_rgba(0,0,0,0.04)] transition-all relative overflow-hidden">
          
          {/* Top Row: Identity, Subsidized Price & Primary CTA */}
          <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-zinc-200/80">
            
            <div className="space-y-2.5 max-w-[680px]">
              <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100/70 border border-amber-200 text-xs font-bold text-amber-900">
                <Building2 className="w-3.5 h-3.5 text-amber-700" />
                <span>INDIA ONLY PLAN &bull; EXCLUSIVE ANNUAL COMMITMENT</span>
              </div>

              <h2 className="font-display text-2xl sm:text-3xl font-extrabold text-zinc-950 tracking-tight">
                India Only Plan
              </h2>

              <p className="text-xs sm:text-sm text-zinc-600 leading-relaxed">
                A heavily subsidized entry-level operating system built exclusively for Indian solopreneurs, freelancers, and creative studios. Everything you need to operate locally &mdash; from GSTIN compliance and dynamic UPI QR to official WhatsApp notifications.
              </p>
            </div>

            {/* Price & Action */}
            <div className="flex flex-col sm:items-end justify-center gap-3 shrink-0">
              <div className="flex items-baseline gap-2">
                <span className="text-4xl font-display font-extrabold text-zinc-950">₹499</span>
                <span className="text-xs text-zinc-500 font-medium">/ month</span>
                <span className="text-xs text-emerald-700 font-semibold bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full font-mono">
                  Subsidized
                </span>
              </div>
              <div className="text-[11px] text-zinc-500 font-mono text-left sm:text-right">
                Billed annually at ₹5,988/year &bull; <em>Annual commitment only</em>
              </div>

              <a
                href="https://app.heycora.in/workspace/login?plan=india_annual_499"
                className="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 bg-zinc-950 hover:bg-zinc-800 text-white px-7 py-3.5 rounded-xl text-xs sm:text-sm font-bold transition-all shadow-sm hover:shadow-md"
              >
                <span>Claim India Only Plan</span>
                <ArrowRight className="w-4 h-4 text-zinc-400" />
              </a>
              <div className="text-[11px] text-zinc-400 font-mono flex items-center gap-1.5">
                <span>UDYAM / GSTIN Supported &bull; Instant Activation</span>
              </div>
            </div>

          </div>

          {/* Bottom Grid: 6 Indian Superpower Capabilities */}
          <div className="pt-6">
            <div className="text-[11px] font-mono font-bold uppercase tracking-widest text-zinc-400 mb-3.5">
              INDIA EDITION INCLUDED CAPABILITIES:
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
              
              {/* Card 1 */}
              <div className="flex items-start gap-3 p-3.5 rounded-xl bg-white border border-zinc-200/80 shadow-2xs hover:border-zinc-300 transition-all">
                <div className="w-8 h-8 rounded-lg bg-amber-50 border border-amber-200/60 flex items-center justify-center shrink-0 mt-0.5 text-amber-800">
                  <Globe className="w-4 h-4" />
                </div>
                <div className="min-w-0">
                  <div className="font-semibold text-zinc-950 text-xs">Free .in Custom Domain</div>
                  <div className="text-[11px] text-zinc-500 mt-0.5 leading-snug">
                    Complimentary 1-year <span className="font-mono text-zinc-800 font-bold">.in domain</span> with automated SSL
                  </div>
                </div>
              </div>

              {/* Card 2 */}
              <div className="flex items-start gap-3 p-3.5 rounded-xl bg-white border border-zinc-200/80 shadow-2xs hover:border-zinc-300 transition-all">
                <div className="w-8 h-8 rounded-lg bg-emerald-50 border border-emerald-200/60 flex items-center justify-center shrink-0 mt-0.5 text-emerald-800">
                  <QrCode className="w-4 h-4" />
                </div>
                <div className="min-w-0">
                  <div className="font-semibold text-zinc-950 text-xs">Dynamic UPI QR Code</div>
                  <div className="text-[11px] text-zinc-500 mt-0.5 leading-snug">
                    Zero gateway fees &mdash; direct bank settlement on all invoices
                  </div>
                </div>
              </div>

              {/* Card 3 */}
              <div className="flex items-start gap-3 p-3.5 rounded-xl bg-white border border-zinc-200/80 shadow-2xs hover:border-zinc-300 transition-all">
                <div className="w-8 h-8 rounded-lg bg-green-50 border border-green-200/60 flex items-center justify-center shrink-0 mt-0.5 text-[#25D366]">
                  <WhatsAppIcon className="w-4 h-4" />
                </div>
                <div className="min-w-0">
                  <div className="font-semibold text-zinc-950 text-xs flex items-center gap-1">
                    <span>WhatsApp Client Dispatch</span>
                  </div>
                  <div className="text-[11px] text-zinc-500 mt-0.5 leading-snug">
                    Official WhatsApp dispatch for proposals, contracts &amp; receipts
                  </div>
                </div>
              </div>

              {/* Card 4 */}
              <div className="flex items-start gap-3 p-3.5 rounded-xl bg-white border border-zinc-200/80 shadow-2xs hover:border-zinc-300 transition-all">
                <div className="w-8 h-8 rounded-lg bg-blue-50 border border-blue-200/60 flex items-center justify-center shrink-0 mt-0.5 text-blue-800">
                  <Receipt className="w-4 h-4" />
                </div>
                <div className="min-w-0">
                  <div className="font-semibold text-zinc-950 text-xs">18% GST Invoicing Engine</div>
                  <div className="text-[11px] text-zinc-500 mt-0.5 leading-snug">
                    Auto CGST, SGST, IGST tax breakdown &amp; GSTIN audit logs
                  </div>
                </div>
              </div>

              {/* Card 5 */}
              <div className="flex items-start gap-3 p-3.5 rounded-xl bg-white border border-zinc-200/80 shadow-2xs hover:border-zinc-300 transition-all">
                <div className="w-8 h-8 rounded-lg bg-purple-50 border border-purple-200/60 flex items-center justify-center shrink-0 mt-0.5 text-purple-800">
                  <Sparkles className="w-4 h-4" />
                </div>
                <div className="min-w-0">
                  <div className="font-semibold text-zinc-950 text-xs">3,500 AI Runs / month</div>
                  <div className="text-[11px] text-zinc-500 mt-0.5 leading-snug">
                    Standard AI reasoning for client proposals &amp; briefs
                  </div>
                </div>
              </div>

              {/* Card 6 */}
              <div className="flex items-start gap-3 p-3.5 rounded-xl bg-white border border-zinc-200/80 shadow-2xs hover:border-zinc-300 transition-all">
                <div className="w-8 h-8 rounded-lg bg-zinc-100 border border-zinc-200 flex items-center justify-center shrink-0 mt-0.5 text-zinc-900">
                  <ShieldCheck className="w-4 h-4" />
                </div>
                <div className="min-w-0">
                  <div className="font-semibold text-zinc-950 text-xs">1 Seat &amp; IST Desk</div>
                  <div className="text-[11px] text-zinc-500 mt-0.5 leading-snug">
                    Built for solopreneurs with standard email &amp; Indian helpdesk
                  </div>
                </div>
              </div>

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
                  className="w-full p-4 sm:p-5 text-left flex items-center justify-between gap-4 font-semibold text-xs sm:text-sm text-zinc-950 hover:bg-zinc-50/70 transition-colors cursor-pointer"
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

        {/* Disclaimer Note */}
        <div className="mt-8 text-center text-[11px] text-zinc-400 font-mono">
          * Fair usage policy applies to unlimited capabilities. All prices exclude applicable local taxes.
        </div>

        <div className="mt-6 text-center text-xs text-zinc-500 font-mono">
          <span>Need custom contract terms or have specific compliance questions? </span>
          <Link href="/contact" className="text-zinc-950 font-bold underline underline-offset-2 hover:text-zinc-700">
            Contact our Mumbai solutions desk &rarr;
          </Link>
        </div>
      </section>

      {/* ══════════════════════════════════════════════════════════════════════
          FLOATING STICKY BILLING CADENCE TOGGLE (ONLY WHEN TOP TOGGLE IS OFF-SCREEN)
      ══════════════════════════════════════════════════════════════════════ */}
      {showFloatingToggle && (
        <div className="fixed bottom-6 inset-x-0 mx-auto w-fit z-50 animate-in fade-in slide-in-from-bottom-4 duration-300">
          <div className="inline-flex items-center p-1.5 bg-white/95 backdrop-blur-xl rounded-full border border-zinc-200/90 shadow-[0_12px_40px_rgba(0,0,0,0.12)]">
            <button
              type="button"
              onClick={() => {
                setBillingCycle('monthly');
                trackEvent('pricing_cycle_change_floating', { cycle: 'monthly' });
              }}
              className={`px-5 py-2 rounded-full text-xs font-bold transition-all cursor-pointer ${
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
              className={`px-5 py-2 rounded-full text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer ${
                billingCycle === 'annual'
                  ? 'bg-zinc-950 text-white shadow-xs'
                  : 'text-zinc-600 hover:text-zinc-950'
              }`}
            >
              <span>Annual Billing</span>
              <span className="bg-emerald-500 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full shadow-2xs">
                2 Mo. Free
              </span>
            </button>
          </div>
        </div>
      )}

    </main>
  );
}
