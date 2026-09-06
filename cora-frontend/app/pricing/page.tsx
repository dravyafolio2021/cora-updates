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
  Send,
  LifeBuoy,
  Leaf,
  Rocket,
  TrendingUp,
  Shield,
  Calculator,
  Layers,
  Clock,
  AlertCircle,
  RotateCcw,
  Flame
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
  const [appliedCoupon, setAppliedCoupon] = useState<string | null>(null);
  const [flashSecondsLeft, setFlashSecondsLeft] = useState(600);

  const topToggleRef = useRef<HTMLDivElement>(null);
  const cardsContainerRef = useRef<HTMLDivElement>(null);

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

  // Dynamic Coupon & URL Parameter Detection + Confetti Blast
  useEffect(() => {
    if (typeof window !== 'undefined') {
      const params = new URLSearchParams(window.location.search);
      const coupon = params.get('coupon');
      const plan = params.get('plan');
      if (coupon?.toUpperCase() === 'INDIA40' || plan === 'india_only') {
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
      }
    }
  }, []);

  // Auto-center the middle Professional card on mobile screens
  useEffect(() => {
    if (typeof window !== 'undefined' && window.innerWidth < 768 && cardsContainerRef.current) {
      const timer = setTimeout(() => {
        const container = cardsContainerRef.current;
        if (container && container.children[1]) {
          const proCard = container.children[1] as HTMLElement;
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
    <main className="w-full relative pb-16 sm:pb-24 overflow-hidden bg-white text-zinc-900">
      
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
        <div className="relative z-10 w-full max-w-[1240px] mx-auto px-4 sm:px-6 flex flex-col items-center text-center">
          
          {/* Announcement Pill */}
          <div className="inline-flex items-center gap-2 px-3 py-1 sm:px-3.5 sm:py-1.5 bg-white/95 backdrop-blur-md rounded-full shadow-[0px_2px_8px_rgba(0,0,0,0.05)] border border-white/80 text-[11px] sm:text-xs font-medium text-zinc-800 mb-3 sm:mb-4">
            <span className="flex h-2 w-2 relative">
              <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-600 opacity-75"></span>
              <span className="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
            </span>
            <span>Simple, transparent plans &bull; No hidden fees</span>
          </div>

          {/* Heading */}
          <h1 className="font-display text-3xl sm:text-5xl md:text-6xl font-bold text-zinc-950 leading-[1.15] tracking-[-0.035em] max-w-[760px] mx-auto mb-2 sm:mb-3">
            Plans that fit your business
          </h1>

          {/* Body */}
          <p className="text-zinc-700 text-sm sm:text-lg font-normal leading-relaxed max-w-[540px] mx-auto">
            Start free forever with 1,000 monthly AI runs. Upgrade anytime as you grow.
          </p>

        </div>
      </section>

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
                onClick={() => {
                  const el = document.getElementById('india-only-plan');
                  if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }}
                className="px-4 py-2 rounded-xl bg-white text-zinc-950 font-extrabold text-xs hover:bg-amber-50 transition-all shadow-md cursor-pointer shrink-0"
              >
                Claim Deal ↓
              </button>
            </div>
          </div>
        </div>
      )}

      {/* ══════════════════════════════════════════════════════════════════════
          ROW 1: FREE FOREVER USP SHOWCASE (MOBILE OPTIMIZED COMPACT 2-COL GRID)
      ══════════════════════════════════════════════════════════════════════ */}
      <section className="w-full max-w-[1240px] mx-auto px-3 sm:px-6 mb-8 sm:mb-14 -mt-3 sm:-mt-10 relative z-10">
        <div className="bg-white border border-zinc-200/90 rounded-2xl sm:rounded-[24px] p-3 sm:p-5 lg:p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)] hover:shadow-[0_8px_28px_rgba(0,0,0,0.05)] transition-all">
          
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 sm:gap-6 pb-2.5 sm:pb-4 border-b border-zinc-100">
            <div className="space-y-1 sm:space-y-1.5 max-w-[700px]">
              <div className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50/80 border border-emerald-200/60 text-[10px] sm:text-xs font-semibold text-emerald-800">
                <span className="flex h-1.5 w-1.5 rounded-full bg-emerald-500" />
                <span>100% Free Forever</span>
                <span className="text-emerald-300">&bull;</span>
                <span className="font-normal text-emerald-700">No credit card</span>
              </div>

              <h2 className="font-display text-base sm:text-2xl lg:text-3xl font-bold text-zinc-950 tracking-tight">
                Free Forever Plan
              </h2>

              <p className="text-[11px] sm:text-sm text-zinc-500 leading-normal sm:leading-relaxed">
                Everything you need to launch and operate. Website builder, client pipeline, proposals, and contracts with zero upfront cost.
              </p>
            </div>

            <div className="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-2 shrink-0 pt-1 sm:pt-0">
              <a
                href="https://app.heycora.in/workspace/login?plan=free"
                className="inline-flex items-center justify-center gap-1.5 bg-zinc-950 hover:bg-zinc-800 text-white px-3.5 py-2 sm:px-5 sm:py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all shadow-sm hover:shadow-md cursor-pointer"
              >
                <span>Get Started Free</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
              </a>
              <div className="flex items-center gap-1.5 text-[10px] font-mono text-zinc-400">
                <span>⚡ 30-Sec Setup</span>
                <span>&bull;</span>
                <span>🔒 Full Ownership</span>
              </div>
            </div>
          </div>

          {/* Single-row desktop (6 cols) and ultra-compact mobile layout */}
          <div className="pt-2.5 sm:pt-4">
            <div className="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-1.5 sm:gap-2.5">
              
              <div className="flex items-center gap-1.5 sm:gap-2 p-1.5 sm:p-2.5 rounded-lg sm:rounded-xl bg-zinc-50/70 border border-zinc-100 hover:border-zinc-200 hover:bg-zinc-50 transition-all min-w-0">
                <div className="w-5 h-5 sm:w-6 sm:h-6 rounded-md sm:rounded-lg bg-white border border-zinc-200/80 flex items-center justify-center shrink-0 text-zinc-800 shadow-2xs">
                  <Layout className="w-2.5 h-2.5 sm:w-3 sm:h-3" />
                </div>
                <div className="min-w-0 flex-1">
                  <div className="font-semibold text-zinc-900 text-[10px] sm:text-[11px] truncate">Website Builder</div>
                  <div className="text-[9px] sm:text-[10px] text-zinc-500 truncate">
                    heycora.in/you
                  </div>
                </div>
              </div>

              <div className="flex items-center gap-1.5 sm:gap-2 p-1.5 sm:p-2.5 rounded-lg sm:rounded-xl bg-zinc-50/70 border border-zinc-100 hover:border-zinc-200 hover:bg-zinc-50 transition-all min-w-0">
                <div className="w-5 h-5 sm:w-6 sm:h-6 rounded-md sm:rounded-lg bg-white border border-zinc-200/80 flex items-center justify-center shrink-0 text-zinc-800 shadow-2xs">
                  <ShieldCheck className="w-2.5 h-2.5 sm:w-3 sm:h-3" />
                </div>
                <div className="min-w-0 flex-1">
                  <div className="font-semibold text-zinc-900 text-[10px] sm:text-[11px] truncate">Kanban CRM</div>
                  <div className="text-[9px] sm:text-[10px] text-zinc-500 truncate">
                    Leads &amp; pipeline
                  </div>
                </div>
              </div>

              <div className="flex items-center gap-1.5 sm:gap-2 p-1.5 sm:p-2.5 rounded-lg sm:rounded-xl bg-zinc-50/70 border border-zinc-100 hover:border-zinc-200 hover:bg-zinc-50 transition-all min-w-0">
                <div className="w-5 h-5 sm:w-6 sm:h-6 rounded-md sm:rounded-lg bg-white border border-zinc-200/80 flex items-center justify-center shrink-0 text-amber-600 shadow-2xs">
                  <Sparkles className="w-2.5 h-2.5 sm:w-3 sm:h-3" />
                </div>
                <div className="min-w-0 flex-1">
                  <div className="font-semibold text-zinc-900 text-[10px] sm:text-[11px] truncate">1,000 AI Credits</div>
                  <div className="text-[9px] sm:text-[10px] text-zinc-500 truncate">
                    Proposals &amp; briefs
                  </div>
                </div>
              </div>

              <div className="flex items-center gap-1.5 sm:gap-2 p-1.5 sm:p-2.5 rounded-lg sm:rounded-xl bg-zinc-50/70 border border-zinc-100 hover:border-zinc-200 hover:bg-zinc-50 transition-all min-w-0">
                <div className="w-5 h-5 sm:w-6 sm:h-6 rounded-md sm:rounded-lg bg-white border border-zinc-200/80 flex items-center justify-center shrink-0 text-zinc-800 shadow-2xs">
                  <FileCheck className="w-2.5 h-2.5 sm:w-3 sm:h-3" />
                </div>
                <div className="min-w-0 flex-1">
                  <div className="font-semibold text-zinc-900 text-[10px] sm:text-[11px] truncate">SHA-256 E-Sign</div>
                  <div className="text-[9px] sm:text-[10px] text-zinc-500 truncate">
                    Unlimited contracts
                  </div>
                </div>
              </div>

              <div className="flex items-center gap-1.5 sm:gap-2 p-1.5 sm:p-2.5 rounded-lg sm:rounded-xl bg-zinc-50/70 border border-zinc-100 hover:border-zinc-200 hover:bg-zinc-50 transition-all min-w-0">
                <div className="w-5 h-5 sm:w-6 sm:h-6 rounded-md sm:rounded-lg bg-white border border-zinc-200/80 flex items-center justify-center shrink-0 text-zinc-800 shadow-2xs">
                  <Receipt className="w-2.5 h-2.5 sm:w-3 sm:h-3" />
                </div>
                <div className="min-w-0 flex-1">
                  <div className="font-semibold text-zinc-900 text-[10px] sm:text-[11px] truncate">18% GST Invoicing</div>
                  <div className="text-[9px] sm:text-[10px] text-zinc-500 truncate">
                    Tax math &amp; PDFs
                  </div>
                </div>
              </div>

              <div className="flex items-center gap-1.5 sm:gap-2 p-1.5 sm:p-2.5 rounded-lg sm:rounded-xl bg-zinc-50/70 border border-zinc-100 hover:border-zinc-200 hover:bg-zinc-50 transition-all min-w-0">
                <div className="w-5 h-5 sm:w-6 sm:h-6 rounded-md sm:rounded-lg bg-white border border-zinc-200/80 flex items-center justify-center shrink-0 text-zinc-500 shadow-2xs">
                  <Globe className="w-2.5 h-2.5 sm:w-3 sm:h-3" />
                </div>
                <div className="min-w-0 flex-1">
                  <div className="font-semibold text-zinc-900 text-[10px] sm:text-[11px] truncate">Cora Subdomain</div>
                  <div className="text-[9px] sm:text-[10px] text-zinc-500 truncate">
                    Custom domain ready
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
      <section className="w-full max-w-[960px] mx-auto px-4 sm:px-6 mb-12 sm:mb-16">
        
        {/* Section Header & Primary Cadence Switcher */}
        <div className="mb-6 sm:mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4 sm:gap-6 pb-4 border-b border-zinc-100 text-center md:text-left">
          <div>
            <div className="text-[10px] sm:text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-400">
              GROWTH PLANS
            </div>
            <h2 className="font-display text-xl sm:text-3xl font-bold text-zinc-950 mt-1">
              High-Throughput Operating Plans
            </h2>
            <p className="text-xs sm:text-sm text-zinc-500 mt-1 max-w-xl mx-auto md:mx-0">
              Connect your custom domain, automate client communications, and supercharge operations with advanced AI.
            </p>
          </div>

          {/* Primary Cadence Switcher with 2 Months Free badge */}
          <div ref={topToggleRef} className="flex flex-col items-center md:items-end gap-2 shrink-0 mx-auto md:mx-0">
            <div className="w-fit inline-flex items-center p-1 bg-zinc-100/90 rounded-full border border-zinc-200/80 shadow-2xs">
              <button
                type="button"
                onClick={() => {
                  setBillingCycle('monthly');
                  trackEvent('pricing_cycle_change', { cycle: 'monthly' });
                }}
                className={`px-4 sm:px-5 py-2 rounded-full text-xs font-semibold transition-all cursor-pointer ${
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
                className={`px-4 sm:px-5 py-2 rounded-full text-xs font-semibold transition-all flex items-center gap-1.5 cursor-pointer ${
                  billingCycle === 'annual'
                    ? 'bg-zinc-950 text-white shadow-xs'
                    : 'text-zinc-600 hover:text-zinc-950'
                }`}
              >
                <span>Annual Billing</span>
                <span className="bg-emerald-500 text-white text-[9px] sm:text-[10px] font-bold px-2 py-0.5 rounded-full shadow-2xs">
                  2 Mo. Free
                </span>
              </button>
            </div>

            <div className="text-[11px] sm:text-xs text-zinc-500 text-center md:text-right min-h-[20px] flex items-center justify-center md:justify-end">
              {billingCycle === 'annual' ? (
                <div className="inline-flex items-center gap-1.5 text-emerald-800 font-medium animate-in fade-in duration-150">
                  <Gift className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                  <span>Includes 2 months free + 1-year custom domain</span>
                </div>
              ) : (
                <div className="text-zinc-400 font-normal animate-in fade-in duration-150">
                  Save 2 months + get a free domain with annual billing
                </div>
              )}
            </div>
          </div>
        </div>

        {/* 3-Tier SaaS Cards: Swipeable Snap Row on Mobile, Snug 3-Col Grid on Desktop */}
        <div 
          ref={cardsContainerRef}
          className="flex md:grid md:grid-cols-3 gap-3 md:gap-3 lg:gap-3.5 overflow-x-auto md:overflow-visible snap-x snap-mandatory scroll-pl-4 scroll-pr-4 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden px-4 sm:px-0 -mx-4 sm:mx-0 py-6 sm:py-8 items-stretch"
        >
          
          {/* CARD 1: STARTER */}
          <div className="w-[82vw] max-w-[305px] md:w-auto shrink-0 snap-center bg-white border border-zinc-200/90 rounded-2xl sm:rounded-[22px] overflow-hidden flex flex-col justify-between shadow-[0_2px_10px_rgba(0,0,0,0.03)] hover:shadow-lg hover:border-zinc-300 transition-all">
            <div>
              {/* Top Header Banner in Vibrant Emerald Green */}
              <div className="bg-[#0b7a4d] h-[155px] p-5 text-white flex flex-col justify-between relative">
                <div className="flex items-center justify-between gap-2 h-7">
                  <div className="w-9 h-9 rounded-xl bg-[#a7f3d0] text-[#065f46] shadow-xs flex items-center justify-center">
                    <Rocket className="w-4 h-4" />
                  </div>
                </div>
                <div>
                  <h3 className="font-display text-xl font-bold text-white tracking-tight">
                    Starter
                  </h3>
                  <p className="text-xs text-emerald-50/90 mt-1 leading-relaxed line-clamp-2 h-[34px]">
                    For independent operators establishing their brand with custom domains.
                  </p>
                </div>
              </div>

              {/* Card Body */}
              <div className="p-5 sm:p-5.5">
                {/* Price Block */}
                <div className="h-[68px] mb-4 flex flex-col justify-center">
                  <div className="flex items-baseline gap-1.5">
                    <span className="text-3xl sm:text-[34px] font-display font-bold text-zinc-950 tracking-tight leading-none">
                      {billingCycle === 'annual'
                        ? (currency === 'INR' ? '₹833' : '$7.50')
                        : (currency === 'INR' ? '₹999' : '$9')}
                    </span>
                    <span className="text-xs text-zinc-500 font-medium">/ mo</span>
                    {billingCycle === 'annual' && (
                      <span className="text-xs text-zinc-400 line-through ml-1 font-mono">
                        {currency === 'INR' ? '₹999' : '$9'}
                      </span>
                    )}
                  </div>
                  <div className="text-[11px] text-zinc-500 mt-1.5 flex items-center gap-1.5 h-4">
                    {billingCycle === 'annual' ? (
                      <>
                        <span className="text-emerald-700 font-medium">
                          {currency === 'INR' ? '₹9,990 / yr' : '$90 / yr'}
                        </span>
                        <span className="text-zinc-300">&bull;</span>
                        <span className="text-zinc-500">
                          {currency === 'INR' ? 'Save ₹1,998' : 'Save $18'}
                        </span>
                      </>
                    ) : (
                      <span className="text-zinc-400">Billed monthly &bull; Cancel anytime</span>
                    )}
                  </div>
                </div>

                {/* Action CTA */}
                <a
                  href="https://app.heycora.in/workspace/login?plan=starter"
                  className="h-11 w-full inline-flex items-center justify-center gap-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 px-4 rounded-xl text-xs font-bold transition-all mb-5 cursor-pointer"
                >
                  <span>Get started</span>
                  <ArrowRight className="w-3.5 h-3.5 text-zinc-500" />
                </a>

                {/* Features List */}
                <div className="space-y-2.5 pt-2 border-t border-zinc-100">
                  <div className="text-xs font-semibold text-zinc-900">
                    Includes:
                  </div>
                  <ul className="space-y-2 text-xs text-zinc-700">
                    <li className="flex items-center gap-2.5">
                      <Sparkles className="w-3.5 h-3.5 text-amber-500 shrink-0" />
                      <span><strong>{billingCycle === 'annual' ? '6,000' : '5,000'}</strong> AI Runs / mo</span>
                    </li>
                    <li className="flex items-center gap-2.5">
                      <Check className="w-3.5 h-3.5 text-zinc-900 stroke-[2] shrink-0" />
                      <span>Connect custom domain (.com / .in)</span>
                    </li>
                    <li className="flex items-center gap-2.5">
                      <Check className="w-3.5 h-3.5 text-zinc-900 stroke-[2] shrink-0" />
                      <span>Custom business email sending</span>
                    </li>
                    <li className="flex items-center gap-2.5">
                      <Check className="w-3.5 h-3.5 text-zinc-900 stroke-[2] shrink-0" />
                      <span>Up to 2 team seats</span>
                    </li>
                    <li className="flex items-center gap-2.5">
                      <Check className="w-3.5 h-3.5 text-zinc-900 stroke-[2] shrink-0" />
                      <span>100% white-label client view</span>
                    </li>
                    {billingCycle === 'annual' && (
                      <li className="flex items-center gap-2.5 text-emerald-800 font-medium">
                        <Gift className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                        <span>Free 1-Yr Custom Domain</span>
                      </li>
                    )}
                  </ul>
                </div>
              </div>
            </div>
          </div>

          {/* CARD 2: PROFESSIONAL (RECOMMENDED) */}
          <div className="w-[82vw] max-w-[305px] md:w-auto shrink-0 snap-center bg-white border border-zinc-200/90 rounded-2xl sm:rounded-[22px] overflow-hidden flex flex-col justify-between shadow-[0_4px_16px_rgba(0,0,0,0.06)] hover:shadow-xl transition-all relative">
            <div>
              {/* Top Header Banner in Vibrant Magenta / Fuchsia */}
              <div className="bg-[#be006b] h-[155px] p-5 text-white flex flex-col justify-between relative">
                <div className="flex items-center justify-between gap-2 h-7">
                  <div className="w-9 h-9 rounded-xl bg-[#fbcfe8] text-[#9d174d] shadow-xs flex items-center justify-center">
                    <Sparkles className="w-4 h-4" />
                  </div>
                  <span className="inline-flex items-center px-2.5 py-0.5 rounded-full bg-white text-zinc-950 text-[10px] font-bold tracking-wide shadow-xs">
                    Recommended
                  </span>
                </div>
                <div>
                  <h3 className="font-display text-xl font-bold text-white tracking-tight">
                    Professional
                  </h3>
                  <p className="text-xs text-pink-50/90 mt-1 leading-relaxed line-clamp-2 h-[34px]">
                    Autonomous backbone with advanced AI, official WhatsApp dispatch &amp; UPI QR.
                  </p>
                </div>
              </div>

              {/* Card Body */}
              <div className="p-5 sm:p-5.5">
                {/* Price Block */}
                <div className="h-[68px] mb-4 flex flex-col justify-center">
                  <div className="flex items-baseline gap-1.5">
                    <span className="text-3xl sm:text-[34px] font-display font-bold text-zinc-950 tracking-tight leading-none">
                      {billingCycle === 'annual'
                        ? (currency === 'INR' ? '₹1,665' : '$15.80')
                        : (currency === 'INR' ? '₹1,999' : '$19')}
                    </span>
                    <span className="text-xs text-zinc-500 font-medium">/ mo</span>
                    {billingCycle === 'annual' && (
                      <span className="text-xs text-zinc-400 line-through ml-1 font-mono">
                        {currency === 'INR' ? '₹1,999' : '$19'}
                      </span>
                    )}
                  </div>
                  <div className="text-[11px] text-zinc-500 mt-1.5 flex items-center gap-1.5 h-4">
                    {billingCycle === 'annual' ? (
                      <>
                        <span className="text-emerald-700 font-medium">
                          {currency === 'INR' ? '₹19,990 / yr' : '$190 / yr'}
                        </span>
                        <span className="text-zinc-300">&bull;</span>
                        <span className="text-zinc-500">
                          {currency === 'INR' ? 'Save ₹3,998' : 'Save $38'}
                        </span>
                      </>
                    ) : (
                      <span className="text-zinc-400">Billed monthly &bull; Cancel anytime</span>
                    )}
                  </div>
                </div>

                {/* Action CTA */}
                <a
                  href="https://app.heycora.in/workspace/login?plan=pro"
                  className="h-11 w-full inline-flex items-center justify-center gap-2 bg-zinc-950 hover:bg-zinc-800 text-white px-4 rounded-xl text-xs font-bold transition-all shadow-sm hover:shadow-md mb-5 cursor-pointer"
                >
                  <span>Get started with Professional</span>
                  <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                </a>

                {/* Features List */}
                <div className="space-y-2.5 pt-2 border-t border-zinc-100">
                  <div className="text-xs font-semibold text-zinc-900">
                    Everything in Starter, plus:
                  </div>
                  <ul className="space-y-2 text-xs text-zinc-700">
                    <li className="flex items-center gap-2.5">
                      <Sparkles className="w-3.5 h-3.5 text-blue-600 shrink-0" />
                      <span><strong>Advanced AI Reasoning Engine</strong></span>
                    </li>
                    <li className="flex items-center gap-2.5">
                      <Sparkles className="w-3.5 h-3.5 text-amber-500 shrink-0" />
                      <span><strong>{billingCycle === 'annual' ? '21,000' : '20,000'}</strong> AI Runs / mo</span>
                    </li>
                    <li className="flex items-center gap-2.5 text-zinc-950 font-medium">
                      <WhatsAppIcon className="w-3.5 h-3.5 text-[#25D366] shrink-0" />
                      <span>Official WhatsApp automated dispatch</span>
                    </li>
                    <li className="flex items-center gap-2.5">
                      <Check className="w-3.5 h-3.5 text-zinc-900 stroke-[2] shrink-0" />
                      <span>Dynamic UPI QR code on invoices</span>
                    </li>
                    <li className="flex items-center gap-2.5">
                      <Check className="w-3.5 h-3.5 text-zinc-900 stroke-[2] shrink-0" />
                      <span>Automated GST tax splits (CGST/SGST/IGST)</span>
                    </li>
                    <li className="flex items-center gap-2.5">
                      <Check className="w-3.5 h-3.5 text-zinc-900 stroke-[2] shrink-0" />
                      <span>Up to 5 team seats &amp; roles</span>
                    </li>
                    {billingCycle === 'annual' && (
                      <li className="flex items-center gap-2.5 text-emerald-800 font-medium">
                        <Gift className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                        <span>Free 1-Yr Custom Domain</span>
                      </li>
                    )}
                  </ul>
                </div>
              </div>
            </div>
          </div>

          {/* CARD 3: SCALE */}
          <div className="w-[82vw] max-w-[305px] md:w-auto shrink-0 snap-center bg-white border border-zinc-200/90 rounded-2xl sm:rounded-[22px] overflow-hidden flex flex-col justify-between shadow-[0_2px_10px_rgba(0,0,0,0.03)] hover:shadow-lg hover:border-zinc-300 transition-all">
            <div>
              {/* Top Header Banner in Vibrant Royal Violet / Indigo */}
              <div className="bg-[#5438dc] h-[155px] p-5 text-white flex flex-col justify-between relative">
                <div className="flex items-center justify-between gap-2 h-7">
                  <div className="w-9 h-9 rounded-xl bg-[#ddd6fe] text-[#4c1d95] shadow-xs flex items-center justify-center">
                    <TrendingUp className="w-4 h-4" />
                  </div>
                </div>
                <div>
                  <h3 className="font-display text-xl font-bold text-white tracking-tight">
                    Scale
                  </h3>
                  <p className="text-xs text-indigo-50/90 mt-1 leading-relaxed line-clamp-2 h-[34px]">
                    High-throughput infrastructure for agencies &amp; multi-member teams.
                  </p>
                </div>
              </div>

              {/* Card Body */}
              <div className="p-5 sm:p-5.5">
                {/* Price Block */}
                <div className="h-[68px] mb-4 flex flex-col justify-center">
                  <div className="flex items-baseline gap-1.5">
                    <span className="text-3xl sm:text-[34px] font-display font-bold text-zinc-950 tracking-tight leading-none">
                      {billingCycle === 'annual'
                        ? (currency === 'INR' ? '₹2,499' : '$24.10')
                        : (currency === 'INR' ? '₹2,999' : '$29')}
                    </span>
                    <span className="text-xs text-zinc-500 font-medium">/ mo</span>
                    {billingCycle === 'annual' && (
                      <span className="text-xs text-zinc-400 line-through ml-1 font-mono">
                        {currency === 'INR' ? '₹2,999' : '$29'}
                      </span>
                    )}
                  </div>
                  <div className="text-[11px] text-zinc-500 mt-1.5 flex items-center gap-1.5 h-4">
                    {billingCycle === 'annual' ? (
                      <>
                        <span className="text-emerald-700 font-medium">
                          {currency === 'INR' ? '₹29,990 / yr' : '$290 / yr'}
                        </span>
                        <span className="text-zinc-300">&bull;</span>
                        <span className="text-zinc-500">
                          {currency === 'INR' ? 'Save ₹5,998' : 'Save $58'}
                        </span>
                      </>
                    ) : (
                      <span className="text-zinc-400">Billed monthly &bull; Cancel anytime</span>
                    )}
                  </div>
                </div>

                {/* Action CTA */}
                <a
                  href="https://app.heycora.in/workspace/login?plan=scale"
                  className="h-11 w-full inline-flex items-center justify-center gap-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 px-4 rounded-xl text-xs font-bold transition-all mb-5 cursor-pointer"
                >
                  <span>Get started</span>
                  <ArrowRight className="w-3.5 h-3.5 text-zinc-500" />
                </a>

                {/* Features List */}
                <div className="space-y-2.5 pt-2 border-t border-zinc-100">
                  <div className="text-xs font-semibold text-zinc-900">
                    Everything in Professional, plus:
                  </div>
                  <ul className="space-y-2 text-xs text-zinc-700">
                    <li className="flex items-center gap-2.5">
                      <Sparkles className="w-3.5 h-3.5 text-purple-600 shrink-0" />
                      <span><strong>All Frontier AI Engines</strong></span>
                    </li>
                    <li className="flex items-center gap-2.5">
                      <Sparkles className="w-3.5 h-3.5 text-amber-500 shrink-0" />
                      <span><strong>{billingCycle === 'annual' ? '61,000' : '60,000'}</strong> AI Runs / mo</span>
                    </li>
                    <li className="flex items-center gap-2.5">
                      <Check className="w-3.5 h-3.5 text-zinc-900 stroke-[2] shrink-0" />
                      <span>Autonomous AI Research Agent</span>
                    </li>
                    <li className="flex items-center gap-2.5">
                      <Check className="w-3.5 h-3.5 text-zinc-900 stroke-[2] shrink-0" />
                      <span>Unlimited* team seats</span>
                    </li>
                    <li className="flex items-center gap-2.5">
                      <Check className="w-3.5 h-3.5 text-zinc-900 stroke-[2] shrink-0" />
                      <span>Custom Webhooks &amp; API access</span>
                    </li>
                    <li className="flex items-center gap-2.5">
                      <Check className="w-3.5 h-3.5 text-zinc-900 stroke-[2] shrink-0" />
                      <span>Dedicated account manager &amp; SLA</span>
                    </li>
                    {billingCycle === 'annual' && (
                      <li className="flex items-center gap-2.5 text-emerald-800 font-medium">
                        <Gift className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                        <span>Free 1-Yr Custom Domain</span>
                      </li>
                    )}
                  </ul>
                </div>
              </div>
            </div>
          </div>

        </div>

        {/* Mobile Swipe Hint */}
        <div className="flex md:hidden items-center justify-center gap-2 mt-4 text-[11px] text-zinc-400">
          <span className="inline-block w-1.5 h-1.5 rounded-full bg-zinc-300"></span>
          <span className="inline-block w-3.5 h-1.5 rounded-full bg-[#be006b]"></span>
          <span className="inline-block w-1.5 h-1.5 rounded-full bg-zinc-300"></span>
          <span className="ml-1 text-zinc-500 font-medium">Swipe to explore other plans &rarr;</span>
        </div>

        {/* ── Compare All Features CTA Button ── */}
        <div className="mt-8 sm:mt-10 text-center">
          <button
            type="button"
            onClick={handleToggleComparison}
            className="inline-flex items-center gap-2 px-6 sm:px-8 py-3 sm:py-3.5 rounded-full bg-zinc-100 hover:bg-zinc-200 text-xs font-bold text-zinc-950 transition-all border border-zinc-200 shadow-2xs hover:shadow-xs active:scale-[0.98] cursor-pointer"
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
          <div className="mt-8 sm:mt-12 bg-white border border-zinc-200/90 rounded-2xl sm:rounded-[32px] p-3.5 sm:p-7 shadow-[0_8px_32px_rgba(0,0,0,0.03)] animate-in fade-in slide-in-from-top-3 duration-200">
            
            {/* Top Heading Block */}
            <div className="text-center max-w-[700px] mx-auto mb-6 sm:mb-8 pt-1 sm:pt-2">
              <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200/60 text-[10px] sm:text-[11px] font-mono font-semibold uppercase tracking-wider text-emerald-800 mb-2 sm:mb-3">
                <Sparkles className="w-3.5 h-3.5 text-emerald-600" />
                <span>SIDE-BY-SIDE FEATURE MATRIX</span>
              </div>
              <h3 className="font-display text-xl sm:text-4xl font-extrabold text-zinc-950 tracking-tight">
                Compare plans at a glance
              </h3>
              <p className="text-xs sm:text-sm text-zinc-500 mt-1.5 sm:mt-2 leading-relaxed">
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
                    <td className="py-3.5 px-4 font-medium text-zinc-900">
                      <span>Free 1-Year Custom Domain</span>
                      <span className="text-[10px] text-zinc-400 font-normal block">Annual plans only</span>
                    </td>
                    <td className="py-3.5 px-4 text-center text-zinc-300">
                      <Minus className="w-3.5 h-3.5 mx-auto" />
                    </td>
                    <td className="py-3.5 px-4 text-center bg-zinc-50/40">
                      {billingCycle === 'annual' ? (
                        <span className="font-mono text-[11px] font-bold text-zinc-950">Free (.com / .in)</span>
                      ) : (
                        <Minus className="w-3.5 h-3.5 mx-auto text-zinc-300" />
                      )}
                    </td>
                    <td className="py-3.5 px-4 text-center bg-emerald-50/20">
                      {billingCycle === 'annual' ? (
                        <span className="font-mono text-[11px] font-bold text-emerald-700">Free (.com / .in)</span>
                      ) : (
                        <Minus className="w-3.5 h-3.5 mx-auto text-zinc-300" />
                      )}
                    </td>
                    <td className="py-3.5 px-4 text-center">
                      {billingCycle === 'annual' ? (
                        <span className="font-mono text-[11px] font-bold text-zinc-950">Free (.com / .in)</span>
                      ) : (
                        <Minus className="w-3.5 h-3.5 mx-auto text-zinc-300" />
                      )}
                    </td>
                    <td className="py-3.5 px-4 text-center bg-amber-50/20">
                      <span className="font-mono text-[11px] font-semibold text-amber-950">Free .in Domain</span>
                    </td>
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
            <div className="mt-4 sm:mt-6 rounded-xl sm:rounded-2xl bg-emerald-50/40 border border-emerald-100 p-3 sm:p-4 flex flex-col sm:flex-row items-center justify-between gap-2.5 sm:gap-3 text-[11px] sm:text-xs text-zinc-700">
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
          ROW 3: INDIA ONLY PLAN (CLEAN, COMPACT MOBILE & PROFESSIONAL SHOWCASE)
      ══════════════════════════════════════════════════════════════════════ */}
      <section id="india-only-plan" className="w-full max-w-[1240px] mx-auto px-3.5 sm:px-6 mb-14 sm:mb-20">
        <div className={`border-2 rounded-2xl sm:rounded-[32px] p-4 sm:p-9 shadow-[0_8px_32px_rgba(0,0,0,0.04)] transition-all relative overflow-hidden ${
          appliedCoupon === 'INDIA40'
            ? 'bg-gradient-to-br from-amber-50/80 via-white to-orange-50/30 border-amber-400 ring-4 ring-amber-400/20'
            : 'bg-gradient-to-br from-zinc-50/90 via-white to-amber-50/20 border-zinc-200/90 hover:border-zinc-300'
        }`}>
          
          {/* Top Row: Identity, Subsidized Price & Primary CTA */}
          <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-4 sm:gap-6 pb-4 sm:pb-6 border-b border-zinc-200/80">
            
            <div className="space-y-2 sm:space-y-2.5 max-w-[660px]">
              <div className="flex flex-wrap items-center gap-2">
                <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full bg-amber-100/80 border border-amber-200/80 text-[10px] sm:text-xs font-bold text-amber-900">
                  <Building2 className="w-3.5 h-3.5 text-amber-700" />
                  <span>INDIA ONLY &bull; ANNUAL COMMITMENT ONLY</span>
                </div>
                {appliedCoupon === 'INDIA40' && (
                  <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-100 border border-emerald-300 text-emerald-900 text-[10px] sm:text-xs font-mono font-bold animate-pulse">
                    <Sparkles className="w-3 h-3 text-emerald-700" />
                    <span>40% FLASH DISCOUNT UNLOCKED</span>
                  </span>
                )}
              </div>

              {appliedCoupon === 'INDIA40' && (
                <div className="p-3 rounded-2xl bg-amber-400/20 border border-amber-400/50 flex flex-wrap items-center justify-between gap-2 text-xs">
                  <div className="flex items-center gap-1.5 font-bold text-amber-950">
                    <Flame className="w-4 h-4 text-amber-600 fill-amber-600 animate-bounce" />
                    <span>FLASH TIER ACTIVE: ONLY 4 DISCOUNTED SPOTS LEFT TODAY</span>
                  </div>
                  <div className="flex items-center gap-1.5 font-mono text-xs font-bold text-amber-950 bg-white/90 px-2.5 py-1 rounded-xl border border-amber-300/80 shadow-2xs">
                    <Clock className="w-3.5 h-3.5 text-amber-700" />
                    <span>Offer Ends in: {formatFlashTimer(flashSecondsLeft)}</span>
                  </div>
                </div>
              )}

              <h2 className="font-display text-xl sm:text-3xl font-extrabold text-zinc-950 tracking-tight">
                India Only Plan
              </h2>

              <p className="text-xs sm:text-sm text-zinc-600 leading-relaxed">
                A heavily subsidized entry-level operating system built exclusively for Indian solopreneurs, freelancers, and creative studios. Everything you need to operate locally &mdash; from GSTIN compliance and dynamic UPI QR to official WhatsApp notifications.
              </p>
            </div>

            {/* Price & Action */}
            <div className="flex flex-col sm:items-end justify-center gap-2 sm:gap-3 shrink-0 pt-1 sm:pt-0">
              <div className="flex items-center gap-2 sm:gap-2.5">
                <div className="flex items-baseline gap-1 sm:gap-1.5">
                  <span className="text-3xl sm:text-4xl font-display font-extrabold text-zinc-950">
                    {appliedCoupon === 'INDIA40' ? '₹299' : '₹499'}
                  </span>
                  <span className="text-xs text-zinc-500 font-medium">/ month</span>
                  {appliedCoupon === 'INDIA40' && (
                    <span className="text-base text-zinc-400 line-through ml-1 font-mono font-bold">
                      ₹499
                    </span>
                  )}
                </div>
                <span className="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 text-[10px] sm:text-[11px] font-bold tracking-wide">
                  Annual Plan Only
                </span>
              </div>

              <div className="text-[11px] sm:text-xs text-zinc-500 font-mono text-left sm:text-right">
                {appliedCoupon === 'INDIA40' ? (
                  <>
                    Billed as <span className="text-emerald-700 font-bold">₹3,588/year</span> &bull; <span className="text-emerald-600 font-semibold">Save ₹2,400 with 40% Off</span>
                  </>
                ) : (
                  <>
                    Billed as <span className="text-zinc-950 font-semibold">₹5,988/year</span> &bull; 1-Year Commitment
                  </>
                )}
              </div>

              <a
                href={
                  appliedCoupon === 'INDIA40'
                    ? 'https://app.heycora.in/workspace/login?plan=india_annual_299&coupon=INDIA40'
                    : 'https://app.heycora.in/workspace/login?plan=india_annual_499'
                }
                className="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-zinc-950 hover:bg-zinc-800 text-white px-5 py-2.5 sm:px-7 sm:py-3.5 rounded-xl text-xs sm:text-sm font-bold transition-all shadow-md hover:shadow-lg cursor-pointer hover:scale-[1.02] active:scale-[0.98]"
              >
                <span>{appliedCoupon === 'INDIA40' ? 'Claim 40% Discount (₹299/mo) →' : 'Claim India Only Plan'}</span>
                <ArrowRight className="w-4 h-4 text-zinc-400" />
              </a>
            </div>

          </div>

          {/* Bottom Grid: Compact 2-col on Mobile / 3-col on Desktop */}
          <div className="pt-4 sm:pt-6">
            <div className="text-[10px] sm:text-[11px] font-mono font-bold uppercase tracking-widest text-zinc-400 mb-2.5 sm:mb-3.5">
              INDIA EDITION INCLUDED CAPABILITIES:
            </div>

            <div className="grid grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-3.5">
              
              {/* Card 1 */}
              <div className="flex items-start gap-2 sm:gap-3 p-2.5 sm:p-3.5 rounded-lg sm:rounded-xl bg-white border border-zinc-200/80 shadow-2xs hover:border-zinc-300 transition-all">
                <div className="w-6 h-6 sm:w-8 sm:h-8 rounded-md sm:rounded-lg bg-amber-50 border border-amber-200/60 flex items-center justify-center shrink-0 mt-0.5 text-amber-800">
                  <Globe className="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                </div>
                <div className="min-w-0">
                  <div className="font-semibold text-zinc-950 text-[11px] sm:text-xs">Free .in Domain</div>
                  <div className="text-[10px] sm:text-[11px] text-zinc-500 mt-0.5 leading-snug line-clamp-2">
                    Complimentary 1-year <span className="font-mono text-zinc-800 font-bold">.in</span> with SSL
                  </div>
                </div>
              </div>

              {/* Card 2 */}
              <div className="flex items-start gap-2 sm:gap-3 p-2.5 sm:p-3.5 rounded-lg sm:rounded-xl bg-white border border-zinc-200/80 shadow-2xs hover:border-zinc-300 transition-all">
                <div className="w-6 h-6 sm:w-8 sm:h-8 rounded-md sm:rounded-lg bg-emerald-50 border border-emerald-200/60 flex items-center justify-center shrink-0 mt-0.5 text-emerald-800">
                  <QrCode className="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                </div>
                <div className="min-w-0">
                  <div className="font-semibold text-zinc-950 text-[11px] sm:text-xs">Dynamic UPI QR</div>
                  <div className="text-[10px] sm:text-[11px] text-zinc-500 mt-0.5 leading-snug line-clamp-2">
                    Zero gateway fee direct settlement
                  </div>
                </div>
              </div>

              {/* Card 3 */}
              <div className="flex items-start gap-2 sm:gap-3 p-2.5 sm:p-3.5 rounded-lg sm:rounded-xl bg-white border border-zinc-200/80 shadow-2xs hover:border-zinc-300 transition-all">
                <div className="w-6 h-6 sm:w-8 sm:h-8 rounded-md sm:rounded-lg bg-green-50 border border-green-200/60 flex items-center justify-center shrink-0 mt-0.5 text-[#25D366]">
                  <WhatsAppIcon className="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                </div>
                <div className="min-w-0">
                  <div className="font-semibold text-zinc-950 text-[11px] sm:text-xs">WhatsApp Dispatch</div>
                  <div className="text-[10px] sm:text-[11px] text-zinc-500 mt-0.5 leading-snug line-clamp-2">
                    Proposals, contracts &amp; receipts
                  </div>
                </div>
              </div>

              {/* Card 4 */}
              <div className="flex items-start gap-2 sm:gap-3 p-2.5 sm:p-3.5 rounded-lg sm:rounded-xl bg-white border border-zinc-200/80 shadow-2xs hover:border-zinc-300 transition-all">
                <div className="w-6 h-6 sm:w-8 sm:h-8 rounded-md sm:rounded-lg bg-blue-50 border border-blue-200/60 flex items-center justify-center shrink-0 mt-0.5 text-blue-800">
                  <Receipt className="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                </div>
                <div className="min-w-0">
                  <div className="font-semibold text-zinc-950 text-[11px] sm:text-xs">18% GST Invoicing</div>
                  <div className="text-[10px] sm:text-[11px] text-zinc-500 mt-0.5 leading-snug line-clamp-2">
                    Auto CGST, SGST, IGST tax breakdown
                  </div>
                </div>
              </div>

              {/* Card 5 */}
              <div className="flex items-start gap-2 sm:gap-3 p-2.5 sm:p-3.5 rounded-lg sm:rounded-xl bg-white border border-zinc-200/80 shadow-2xs hover:border-zinc-300 transition-all">
                <div className="w-6 h-6 sm:w-8 sm:h-8 rounded-md sm:rounded-lg bg-purple-50 border border-purple-200/60 flex items-center justify-center shrink-0 mt-0.5 text-purple-800">
                  <Sparkles className="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                </div>
                <div className="min-w-0">
                  <div className="font-semibold text-zinc-950 text-[11px] sm:text-xs">3,500 AI Runs/mo</div>
                  <div className="text-[10px] sm:text-[11px] text-zinc-500 mt-0.5 leading-snug line-clamp-2">
                    Standard AI reasoning for briefs
                  </div>
                </div>
              </div>

              {/* Card 6 */}
              <div className="flex items-start gap-2 sm:gap-3 p-2.5 sm:p-3.5 rounded-lg sm:rounded-xl bg-white border border-zinc-200/80 shadow-2xs hover:border-zinc-300 transition-all">
                <div className="w-6 h-6 sm:w-8 sm:h-8 rounded-md sm:rounded-lg bg-zinc-100 border border-zinc-200 flex items-center justify-center shrink-0 mt-0.5 text-zinc-900">
                  <ShieldCheck className="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                </div>
                <div className="min-w-0">
                  <div className="font-semibold text-zinc-950 text-[11px] sm:text-xs">1 Seat &amp; IST Desk</div>
                  <div className="text-[10px] sm:text-[11px] text-zinc-500 mt-0.5 leading-snug line-clamp-2">
                    Solopreneur email + Indian desk
                  </div>
                </div>
              </div>

            </div>
          </div>

        </div>
      </section>

      {/* ── Frequently Asked Questions ── */}
      <section className="w-full max-w-[860px] mx-auto px-3.5 sm:px-6">
        <div className="text-center mb-8 sm:mb-10">
          <h2 className="font-display text-xl sm:text-3xl font-bold text-zinc-950 mb-1.5 sm:mb-2">
            Frequently asked questions
          </h2>
          <p className="text-xs sm:text-sm text-zinc-500 font-normal">
            Everything you need to know about plans, billing, and autonomous quotas.
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
          * Fair usage policy applies to unlimited capabilities. All prices exclude applicable local taxes.
        </div>

        <div className="mt-4 sm:mt-6 text-center text-[11px] sm:text-xs text-zinc-500 font-mono">
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
        <div className="fixed bottom-4 sm:bottom-6 inset-x-0 mx-auto w-fit z-50 animate-in fade-in slide-in-from-bottom-4 duration-300">
          <div className="inline-flex items-center p-1 sm:p-1.5 bg-white/95 backdrop-blur-xl rounded-full border border-zinc-200/90 shadow-[0_12px_40px_rgba(0,0,0,0.12)]">
            <button
              type="button"
              onClick={() => {
                setBillingCycle('monthly');
                trackEvent('pricing_cycle_change_floating', { cycle: 'monthly' });
              }}
              className={`px-4 sm:px-5 py-1.5 sm:py-2 rounded-full text-[11px] sm:text-xs font-bold transition-all cursor-pointer ${
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
              className={`px-4 sm:px-5 py-1.5 sm:py-2 rounded-full text-[11px] sm:text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer ${
                billingCycle === 'annual'
                  ? 'bg-zinc-950 text-white shadow-xs'
                  : 'text-zinc-600 hover:text-zinc-950'
              }`}
            >
              <span>Annual Billing</span>
              <span className="bg-emerald-500 text-white text-[9px] sm:text-[10px] font-extrabold px-1.5 sm:px-2 py-0.5 rounded-full shadow-2xs">
                2 Mo. Free
              </span>
            </button>
          </div>
        </div>
      )}

    </main>
  );
}
