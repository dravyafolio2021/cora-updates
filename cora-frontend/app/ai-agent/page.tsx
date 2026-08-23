'use client';

import React, { useState, useEffect, useRef } from 'react';
import Image from 'next/image';
import Link from 'next/link';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import {
  Sparkles,
  ArrowRight,
  Send,
  MessageSquare,
  Receipt,
  ShieldCheck,
  Calendar,
  TrendingUp,
  Cpu,
  CheckCircle2,
  Lock,
  Zap,
  Layers,
  Search,
  Radio,
  FileText,
  Activity,
  Check,
  ChevronRight,
  ChevronLeft,
  QrCode,
  Share2,
  Users,
} from 'lucide-react';
import { trackEvent } from '@/components/analytics/Analytics';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

// ── 1. Expandable Tags Data for Hero ──
interface TagItem {
  id: string;
  label: string;
  desc: string;
}

const humanPowers: TagItem[] = [
  { id: 'assign', label: 'Assign', desc: 'Delegate client inquiries and shoot bookings to your AI co-founders just like a human team. They understand context and execute immediately.' },
  { id: 'message', label: 'Message', desc: 'Chat naturally in WhatsApp or your studio workspace. Request rate cards, hold date checks, or call-sheet updates in real-time.' },
  { id: 'mention', label: 'Mention', desc: 'Tag @Cora anywhere in client briefs or contracts. Your co-founder jumps in, checks hold dates, and drafts commercial scope.' },
  { id: '50-skills', label: '50+ Studio Skills', desc: 'Auto-calculate 18% GST, generate SHA-256 signed NDAs, dispatch call-sheets, and reconcile UPI advances in 1 click.' },
];

const superpowers: TagItem[] = [
  { id: '24-7', label: '24/7 Autonomous', desc: 'Your AI co-founders work around the clock, qualifying client inquiries, locking holds, and collecting advance deposits while you shoot.' },
  { id: 'ambient', label: 'Ambient Context', desc: 'Monitors studio schedules, pending contracts, and gear waivers in the background, anticipating hold clashes before they happen.' },
  { id: 'infinite-memory', label: 'Infinite Memory', desc: 'Grounded in your studio’s rate cards, past portfolios, GSTIN, and client history with instant contextual recall.' },
  { id: 'self-learning', label: 'Self-Learning', desc: 'Every booking, client brief, and feedback signal refines your co-founder’s pricing accuracy and execution speed.' },
];

// ── 2. Carousel Co-Founders Data ──
const cofoundersList = [
  {
    id: 'pm',
    title: 'Intake & PM Co-Founder',
    role: 'Standardizes WhatsApp briefs & locks hold dates',
    color: '#FDEDE7',
    tagColor: 'text-amber-700 bg-amber-100',
    avatar: '/images/agent_card_pm.jpg',
    action: 'Standardize Brief',
    stat: '0.8s Response',
  },
  {
    id: 'sales',
    title: 'Sales & WhatsApp Concierge',
    role: '24/7 client booking, rate card quotes & holds',
    color: '#EDF6FD',
    tagColor: 'text-sky-700 bg-sky-100',
    avatar: '/images/agent_card_sales.jpg',
    action: 'Instant Quote',
    stat: '100% Lead Capture',
  },
  {
    id: 'gst',
    title: '18% GST & Billing Co-Founder',
    role: 'Instant CGST/SGST split & dynamic UPI QR',
    color: '#EDFDED',
    tagColor: 'text-emerald-700 bg-emerald-100',
    avatar: '/images/agent_card_gst.jpg',
    action: 'Generate Invoice',
    stat: 'GSTR-1 Ready',
  },
  {
    id: 'legal',
    title: 'Legal & E-Sign Co-Founder',
    role: 'Commercial NDAs & SHA-256 digital seals',
    color: '#EFEDFD',
    tagColor: 'text-purple-700 bg-purple-100',
    avatar: '/images/agent_card_legal.jpg',
    action: 'Draft Agreement',
    stat: 'IT Act Valid',
  },
  {
    id: 'ledger',
    title: 'Finance & Ledger Co-Founder',
    role: 'Reconciles UPI advances & exports to Tally/CA',
    color: '#EAE8FE',
    tagColor: 'text-indigo-700 bg-indigo-100',
    avatar: '/images/agent_card_finance.jpg',
    action: 'Reconcile Cash Flow',
    stat: '1-Tap CA Sync',
  },
];

// ── 3. Human Skills Showcase Data ──
const humanSkills = [
  {
    id: 'brief-intake',
    title: 'WhatsApp Brief Intake',
    shortDesc: 'Auto-parses raw voice notes & WhatsApp chats into structured commercial shoot scopes.',
    badge: '1-Tap Kickoff',
    previewTitle: 'Lakme Commercial Shoot #CS-942',
    previewSubtitle: 'Parsed from WhatsApp Voice Note (0.8s)',
    details: [
      { label: 'Client', value: 'Lakme Fashion Brand Team' },
      { label: 'Hold Dates', value: 'October 24 - 25 (Confirmed)' },
      { label: 'Deliverables', value: '4K ProRes Master + 40 Retouched Stills' },
      { label: 'Commercial Budget', value: '₹1,20,000 + 18% GST (₹1,41,600)' },
    ],
  },
  {
    id: 'gst-calc',
    title: 'Instant 18% GST Calculation',
    shortDesc: 'Generates compliant GSTIN tax invoices with dynamic UPI soundbox QR standees.',
    badge: 'GSTR-1 Valid',
    previewTitle: 'Tax Invoice #CORA-2026-104',
    previewSubtitle: 'Auto-Verified with GST Portal',
    details: [
      { label: 'Billed To', value: 'Oberoi Realty Ltd (GSTIN: 27AABCO1234F1Z5)' },
      { label: 'Taxable Value', value: '₹2,50,000' },
      { label: 'CGST 9% + SGST 9%', value: '₹22,500 + ₹22,500' },
      { label: 'Total Payable', value: '₹2,95,000 (Dynamic UPI QR Active)' },
    ],
  },
  {
    id: 'legal-sign',
    title: 'SHA-256 E-Signature NDAs',
    shortDesc: 'Protects commercial usage rights and model releases with tamper-proof digital seals.',
    badge: 'IT Act 2000 Compliant',
    previewTitle: 'Commercial Rights & Usage License',
    previewSubtitle: 'SHA-256 Hash: 9f82ab174e3c90df...',
    details: [
      { label: 'Usage Scope', value: 'Digital, Social & Print (India Region, 1 Year)' },
      { label: 'Advance Deposit', value: '50% Non-Refundable Upfront' },
      { label: 'E-Sign Audit', value: 'Client Signed via Mobile Link (IP & Timestamp Logged)' },
      { label: 'Vault Registry', value: 'Stored in Encrypted Cora Vault' },
    ],
  },
  {
    id: 'call-sheets',
    title: 'Crew Call-Time Broadcast',
    shortDesc: 'Dispatches personalized call-sheets to 50+ crew with live WhatsApp read receipts.',
    badge: '100% Delivery',
    previewTitle: 'Mehboob Studio Bay 02 Call-Sheet',
    previewSubtitle: 'Dispatched to 14 Crew Members',
    details: [
      { label: 'Call Time', value: '07:30 AM IST (Stage Setup: 06:45 AM)' },
      { label: 'Location Pin', value: 'Mehboob Studio, Bandra West, Mumbai' },
      { label: 'Gear Checklist', value: 'Sony FX6 A/B, Aputure 600d x3, C-Stands x8' },
      { label: 'Read Status', value: '14/14 Crew Acknowledged' },
    ],
  },
  {
    id: 'upi-reconcile',
    title: 'UPI Advance Reconciliation',
    shortDesc: 'Matches PhonePe, Razorpay & UPI deposits automatically to open bookings.',
    badge: 'Instant Sync',
    previewTitle: 'Operating Cash Flow & Ledger',
    previewSubtitle: 'Auto-Matched to Shoot #CS-942',
    details: [
      { label: 'Advance Received', value: '₹70,800 via Instant UPI' },
      { label: 'Pending at Delivery', value: '₹70,800 (Auto-Reminder Set)' },
      { label: 'Tally Prime Export', value: 'Voucher #CORA-REC-89 Generated' },
      { label: 'CA Filing Status', value: 'Ready for Monthly GSTR-1 Filing' },
    ],
  },
];

export default function AiAgentPage() {
  const [activeHumanTag, setActiveHumanTag] = useState<string | null>('assign');
  const [activeSuperTag, setActiveSuperTag] = useState<string | null>('24-7');
  const [activeCarouselIndex, setActiveCarouselIndex] = useState<number>(0);
  const [activeSkillId, setActiveSkillId] = useState<string>('brief-intake');

  const selectedSkill = humanSkills.find((s) => s.id === activeSkillId) || humanSkills[0];

  const mainContainerRef = useRef<HTMLElement>(null);
  const heroActorRef = useRef<HTMLDivElement>(null);

  const scrollCarousel = (direction: 'left' | 'right') => {
    if (direction === 'left') {
      setActiveCarouselIndex((prev) => (prev > 0 ? prev - 1 : cofoundersList.length - 1));
    } else {
      setActiveCarouselIndex((prev) => (prev < cofoundersList.length - 1 ? prev + 1 : 0));
    }
  };

  // ── GSAP ScrollTrigger Animations ──
  useEffect(() => {
    const ctx = gsap.context(() => {
      // 1. Hero Headline & Subtitle Fade Up
      gsap.fromTo(
        '.gsap-hero-fade',
        { y: 35, opacity: 0 },
        { y: 0, opacity: 1, duration: 0.8, stagger: 0.12, ease: 'power3.out' }
      );

      // 2. Hero Powers Slide-In
      gsap.fromTo(
        '.gsap-hero-left',
        { x: -30, opacity: 0 },
        { x: 0, opacity: 1, duration: 0.75, delay: 0.25, ease: 'power3.out' }
      );
      gsap.fromTo(
        '.gsap-hero-right',
        { x: 30, opacity: 0 },
        { x: 0, opacity: 1, duration: 0.75, delay: 0.25, ease: 'power3.out' }
      );

      // 3. Hero Center Actor Floating Bob
      if (heroActorRef.current) {
        gsap.to(heroActorRef.current, {
          y: -8,
          duration: 3,
          repeat: -1,
          yoyo: true,
          ease: 'sine.inOut',
        });
      }

      // 4. Capabilities Cards Staggered ScrollTrigger
      gsap.fromTo(
        '.gsap-capability-card',
        { y: 40, opacity: 0, scale: 0.96 },
        {
          y: 0,
          opacity: 1,
          scale: 1,
          duration: 0.65,
          stagger: 0.08,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: '#capabilities',
            start: 'top 80%',
          },
        }
      );

      // 5. Orchestration Pipeline Step Reveal
      gsap.fromTo(
        '.gsap-orchestration-box',
        { y: 40, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.75,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: '#orchestration',
            start: 'top 80%',
          },
        }
      );

      // 6. Human Skills Showcase
      gsap.fromTo(
        '.gsap-skill-pill',
        { x: -20, opacity: 0 },
        {
          x: 0,
          opacity: 1,
          duration: 0.55,
          stagger: 0.08,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: '#human-skills',
            start: 'top 80%',
          },
        }
      );

      gsap.fromTo(
        '.gsap-skill-canvas',
        { scale: 0.95, opacity: 0 },
        {
          scale: 1,
          opacity: 1,
          duration: 0.7,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: '#human-skills',
            start: 'top 80%',
          },
        }
      );

      // 7. Technology & Telemetry Cards Stagger
      gsap.fromTo(
        '.gsap-tech-card',
        { y: 35, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.65,
          stagger: 0.12,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: '#technology',
            start: 'top 82%',
          },
        }
      );
    }, mainContainerRef);

    return () => ctx.revert();
  }, []);

  return (
    <main
      ref={mainContainerRef}
      className="min-h-screen bg-[#FFFFFF] text-zinc-950 font-sans selection:bg-zinc-950 selection:text-white"
    >
      
      {/* ─────────────────────────────────────────────────────────────
          SECTION 1: HERO PINNED STAGE (ClickUp Super Agents™ Style)
      ───────────────────────────────────────────────────────────── */}
      <section className="relative pt-24 pb-14 sm:pt-32 sm:pb-20 overflow-hidden border-b border-zinc-100 bg-[#FAFAFB]">
        
        {/* Background Atmospheric Glow */}
        <div className="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] sm:w-[900px] h-[400px] bg-gradient-to-b from-zinc-200/50 via-zinc-100/30 to-transparent rounded-full blur-3xl pointer-events-none" />

        <div className="w-full max-w-[1280px] mx-auto px-4 sm:px-6 relative z-10">
          
          {/* Top Eyebrow */}
          <div className="text-center mb-3 gsap-hero-fade">
            <div className="inline-flex items-center gap-2 px-3.5 py-1 bg-zinc-950 text-white rounded-full text-xs font-semibold uppercase tracking-widest shadow-sm">
              <Sparkles className="w-3.5 h-3.5 text-amber-400" />
              <span>CORA AI CO-FOUNDERS&trade;</span>
            </div>
          </div>

          {/* Main Hero Headline */}
          <div className="text-center max-w-[900px] mx-auto mb-6 sm:mb-10">
            <h1 className="gsap-hero-fade font-display text-4xl xs:text-5xl sm:text-6xl lg:text-[68px] font-bold text-zinc-950 leading-[1.05] tracking-[-0.04em] mb-3.5">
              A new era of service businesses, with <br className="hidden sm:inline" />
              <span className="text-zinc-400 font-medium">AI Co-Founders&trade;</span>
            </h1>
            <p className="gsap-hero-fade text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[660px] mx-auto">
              Maximize studio productivity with specialized autonomous teammates. @mention, assign bookings, and automate 18% GST billing &mdash; grounded in your studio&apos;s live brain.
            </p>

            {/* CTAs */}
            <div className="gsap-hero-fade flex flex-col sm:flex-row items-center justify-center gap-3.5 mt-5 sm:mt-7">
              <Link
                href="/workspace/login"
                className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-full bg-zinc-950 text-white hover:bg-zinc-800 text-sm font-bold shadow-md hover:shadow-lg transition-all hover:-translate-y-0.5"
              >
                <span>Try AI Co-Founders Free</span>
                <ArrowRight className="w-4 h-4" />
              </Link>
              <a
                href="#capabilities"
                className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-full bg-white border border-zinc-200 text-zinc-800 hover:text-zinc-950 hover:bg-zinc-50 text-sm font-semibold shadow-2xs transition-all"
              >
                <span>Explore Capabilities</span>
              </a>
            </div>
          </div>

          {/* Central 3D Character Visual + Two-Column Expandable Powers */}
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-center max-w-[1180px] mx-auto pt-2">
            
            {/* Left Column: HUMAN POWERS */}
            <div className="lg:col-span-4 space-y-3.5 gsap-hero-left">
              <div className="flex items-center gap-2 border-b border-zinc-200 pb-2">
                <div className="w-2 h-2 rounded-full bg-zinc-950" />
                <span className="text-xs font-bold uppercase tracking-widest text-zinc-950">
                  HUMAN POWERS
                </span>
              </div>

              <div className="space-y-2">
                {humanPowers.map((item) => {
                  const isExpanded = activeHumanTag === item.id;
                  return (
                    <div
                      key={item.id}
                      onClick={() => setActiveHumanTag(isExpanded ? null : item.id)}
                      className={`p-3 rounded-2xl border transition-all duration-200 cursor-pointer ${
                        isExpanded
                          ? 'bg-white border-zinc-950 shadow-sm ring-1 ring-zinc-950/10'
                          : 'bg-white/70 hover:bg-white border-zinc-200/80 hover:border-zinc-300 shadow-2xs'
                      }`}
                    >
                      <div className="flex items-center justify-between">
                        <span className="text-xs sm:text-sm font-bold text-zinc-900">{item.label}</span>
                        <span className="text-xs font-bold text-zinc-400">{isExpanded ? '−' : '+'}</span>
                      </div>
                      {isExpanded && (
                        <p className="text-xs text-zinc-600 leading-relaxed mt-2 pt-2 border-t border-zinc-100">
                          {item.desc}
                        </p>
                      )}
                    </div>
                  );
                })}
              </div>
            </div>

            {/* Center: 3D Co-Founder Actor Card with Floating Bob */}
            <div ref={heroActorRef} className="lg:col-span-4 flex flex-col items-center justify-center text-center">
              <div className="relative w-[260px] h-[340px] sm:w-[300px] sm:h-[390px] rounded-3xl overflow-hidden shadow-2xl border border-zinc-200/90 bg-white group">
                <Image
                  src="/images/agent_card_pm.jpg"
                  alt="Cora AI Co-Founder"
                  fill
                  className="object-cover group-hover:scale-105 transition-transform duration-500"
                  priority
                />
                <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent flex flex-col justify-end p-5 text-left text-white">
                  <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-500/90 text-[10.5px] font-bold text-white mb-1.5 w-fit">
                    <span className="w-1.5 h-1.5 rounded-full bg-white animate-pulse" />
                    <span>ONLINE &bull; 0.8s LATENCY</span>
                  </span>
                  <h3 className="font-display text-base sm:text-lg font-bold">Intake &amp; Project Co-Founder</h3>
                  <p className="text-zinc-300 text-xs mt-0.5">Handling briefs, rate cards &amp; hold dates</p>
                </div>
              </div>
            </div>

            {/* Right Column: SUPERPOWERS */}
            <div className="lg:col-span-4 space-y-3.5 gsap-hero-right">
              <div className="flex items-center gap-2 border-b border-zinc-200 pb-2">
                <div className="w-2 h-2 rounded-full bg-emerald-600" />
                <span className="text-xs font-bold uppercase tracking-widest text-zinc-950">
                  SUPERPOWERS
                </span>
              </div>

              <div className="space-y-2">
                {superpowers.map((item) => {
                  const isExpanded = activeSuperTag === item.id;
                  return (
                    <div
                      key={item.id}
                      onClick={() => setActiveSuperTag(isExpanded ? null : item.id)}
                      className={`p-3 rounded-2xl border transition-all duration-200 cursor-pointer ${
                        isExpanded
                          ? 'bg-white border-zinc-950 shadow-sm ring-1 ring-zinc-950/10'
                          : 'bg-white/70 hover:bg-white border-zinc-200/80 hover:border-zinc-300 shadow-2xs'
                      }`}
                    >
                      <div className="flex items-center justify-between">
                        <span className="text-xs sm:text-sm font-bold text-zinc-900">{item.label}</span>
                        <span className="text-xs font-bold text-zinc-400">{isExpanded ? '−' : '+'}</span>
                      </div>
                      {isExpanded && (
                        <p className="text-xs text-zinc-600 leading-relaxed mt-2 pt-2 border-t border-zinc-100">
                          {item.desc}
                        </p>
                      )}
                    </div>
                  );
                })}
              </div>
            </div>

          </div>

          {/* Mobile Streaming Task Pills */}
          <div className="mt-8 overflow-hidden select-none [mask-image:linear-gradient(to_right,transparent,black_10%,black_90%,transparent)]">
            <div className="flex min-w-full shrink-0 items-center justify-around gap-3 animate-marquee py-1">
              {[
                'WhatsApp Brief Intake',
                '18% GST Invoicing',
                'SHA-256 Model NDAs',
                'Dynamic UPI QR Standees',
                '50+ Crew Call-Sheets',
                'Hold Date Clashes',
                'CA GSTR-1 Sync',
                'Client Review Portals',
                'Scope Creep Protection',
              ].concat([
                'WhatsApp Brief Intake',
                '18% GST Invoicing',
                'SHA-256 Model NDAs',
                'Dynamic UPI QR Standees',
                '50+ Crew Call-Sheets',
              ]).map((pill, idx) => (
                <span
                  key={idx}
                  className="px-3.5 py-1.5 rounded-full bg-white border border-zinc-200 text-zinc-800 text-xs font-semibold shadow-2xs whitespace-nowrap"
                >
                  &bull; {pill}
                </span>
              ))}
            </div>
          </div>

        </div>
      </section>

      {/* ─────────────────────────────────────────────────────────────
          SECTION 2: [CAPABILITIES] CO-FOUNDER CAROUSEL DECK
      ───────────────────────────────────────────────────────────── */}
      <section id="capabilities" className="py-14 sm:py-20 bg-white relative z-10 border-b border-zinc-100">
        <div className="w-full max-w-[1280px] mx-auto px-4 sm:px-6">
          
          {/* Eyebrow Header */}
          <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-4 pb-5 border-b border-zinc-200 mb-8 sm:mb-10">
            <div>
              <span className="text-xs font-bold uppercase tracking-widest text-zinc-400 block mb-1">
                [ CAPABILITIES ]
              </span>
              <h2 className="font-display text-2xl sm:text-4xl lg:text-[44px] font-bold text-zinc-950 tracking-tight">
                Co-Founders for everything
              </h2>
            </div>
            <div className="flex items-center gap-3">
              <p className="text-zinc-600 text-xs sm:text-sm max-w-[360px] hidden md:block">
                Autonomous co-founders tailored for commercial studios, creative agencies &amp; solo founders.
              </p>
              <div className="flex items-center gap-2 shrink-0">
                <button
                  onClick={() => scrollCarousel('left')}
                  className="w-9 h-9 rounded-full border border-zinc-200 flex items-center justify-center hover:bg-zinc-100 text-zinc-800 transition-colors cursor-pointer"
                  aria-label="Previous co-founder"
                >
                  <ChevronLeft className="w-4 h-4" />
                </button>
                <button
                  onClick={() => scrollCarousel('right')}
                  className="w-9 h-9 rounded-full border border-zinc-200 flex items-center justify-center hover:bg-zinc-100 text-zinc-800 transition-colors cursor-pointer"
                  aria-label="Next co-founder"
                >
                  <ChevronRight className="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>

          {/* Cards Carousel Grid */}
          <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
            {cofoundersList.map((card, idx) => {
              const isCurrent = idx === activeCarouselIndex;
              return (
                <div
                  key={card.id}
                  onClick={() => setActiveCarouselIndex(idx)}
                  className={`gsap-capability-card rounded-3xl p-5 border transition-all duration-300 flex flex-col justify-between h-[410px] cursor-pointer relative overflow-hidden group ${
                    isCurrent
                      ? 'ring-2 ring-zinc-950 border-transparent shadow-xl'
                      : 'hover:shadow-md border-zinc-200/80 bg-zinc-50/50'
                  }`}
                  style={{ backgroundColor: isCurrent ? card.color : undefined }}
                >
                  <div>
                    <div className="flex items-center justify-between mb-2.5">
                      <span className={`text-[10px] font-bold px-2 py-0.5 rounded-full ${card.tagColor}`}>
                        {card.stat}
                      </span>
                      <span className="text-xs font-mono font-bold text-zinc-400">0{idx + 1}</span>
                    </div>
                    <h3 className="font-display text-base sm:text-lg font-bold text-zinc-950 leading-snug mb-1">
                      {card.title}
                    </h3>
                    <p className="text-zinc-600 text-xs leading-relaxed line-clamp-2">
                      {card.role}
                    </p>
                  </div>

                  {/* Character Avatar with Parallax Frame */}
                  <div className="relative w-full h-[170px] rounded-2xl overflow-hidden my-2 border border-zinc-200/60 bg-white shadow-2xs">
                    <Image
                      src={card.avatar}
                      alt={card.title}
                      fill
                      className="object-cover group-hover:scale-105 transition-transform duration-500"
                    />
                  </div>

                  <div className="pt-2 border-t border-zinc-200/60 flex items-center justify-between text-xs font-semibold text-zinc-800">
                    <span>{card.action}</span>
                    <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                  </div>
                </div>
              );
            })}
          </div>

        </div>
      </section>

      {/* ─────────────────────────────────────────────────────────────
          SECTION 3: MULTI-AGENT ORCHESTRATION ("One Prompt Spins Up a Team")
      ───────────────────────────────────────────────────────────── */}
      <section id="orchestration" className="py-14 sm:py-20 bg-[#FAFAFB] relative z-10 border-b border-zinc-100">
        <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
            
            {/* Left: Interactive Multi-Agent Pipeline Visualization */}
            <div className="lg:col-span-7 space-y-3 gsap-orchestration-box">
              <div className="bg-white rounded-3xl p-6 sm:p-8 border border-zinc-200/90 shadow-sm space-y-4">
                
                {/* Step 1: Input Prompt */}
                <div className="p-4 rounded-2xl bg-zinc-50 border border-zinc-200/70 space-y-1.5">
                  <div className="flex items-center gap-2">
                    <div className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
                    <span className="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">
                      ONE CLIENT INQUIRY RECEIVED
                    </span>
                  </div>
                  <p className="text-xs sm:text-sm font-semibold text-zinc-950 font-mono">
                    &ldquo;Hey Cora, Nike wants 2-day shoot at Studio 4 with 3 models, ₹4.5L budget. Lock it in.&rdquo;
                  </p>
                </div>

                {/* Step 2: 4 Co-Founders Mobilize in Parallel */}
                <div className="grid grid-cols-2 gap-2.5">
                  <div className="p-3 bg-amber-50/70 border border-amber-200/80 rounded-xl space-y-1">
                    <div className="text-[10px] font-bold text-amber-800 uppercase">Intake Co-Founder</div>
                    <div className="text-xs font-semibold text-zinc-900">Hold Oct 24-25 Locked in Studio 4</div>
                  </div>
                  <div className="p-3 bg-emerald-50/70 border border-emerald-200/80 rounded-xl space-y-1">
                    <div className="text-[10px] font-bold text-emerald-800 uppercase">18% GST Co-Founder</div>
                    <div className="text-xs font-semibold text-zinc-900">₹4.5L + ₹81k GST (Dynamic UPI QR)</div>
                  </div>
                  <div className="p-3 bg-purple-50/70 border border-purple-200/80 rounded-xl space-y-1">
                    <div className="text-[10px] font-bold text-purple-800 uppercase">Legal Co-Founder</div>
                    <div className="text-xs font-semibold text-zinc-900">Commercial Usage NDA &bull; SHA-256 Hash</div>
                  </div>
                  <div className="p-3 bg-indigo-50/70 border border-indigo-200/80 rounded-xl space-y-1">
                    <div className="text-[10px] font-bold text-indigo-800 uppercase">Call-Sheet Co-Founder</div>
                    <div className="text-xs font-semibold text-zinc-900">12 Crew Call-Sheets Ready to Dispatch</div>
                  </div>
                </div>

                <div className="flex items-center justify-between text-xs text-zinc-500 pt-1">
                  <span>Execution Time: <strong className="text-zinc-950 font-bold">1.2 seconds</strong></span>
                  <span className="text-emerald-700 font-bold">✓ 4 Co-Founders Synchronized</span>
                </div>
              </div>
            </div>

            {/* Right: Narrative */}
            <div className="lg:col-span-5 space-y-4">
              <span className="text-xs font-bold uppercase tracking-widest text-zinc-400 block">
                [ ORCHESTRATION IN MINUTES ]
              </span>
              <h2 className="font-display text-2xl sm:text-4xl font-bold text-zinc-950 leading-tight">
                One prompt spins up an entire autonomous team
              </h2>
              <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed">
                Your goals, booking holds, and billing frustration &mdash; automatically delegated to specialized co-founders who work together without human bottlenecks.
              </p>
              <div className="pt-2">
                <Link
                  href="/workspace/login"
                  className="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-zinc-950 text-white hover:bg-zinc-800 text-xs sm:text-sm font-semibold shadow-sm transition-all"
                >
                  <span>Explore Multi-Agent Workspace</span>
                  <ArrowRight className="w-4 h-4" />
                </Link>
              </div>
            </div>

          </div>
        </div>
      </section>

      {/* ─────────────────────────────────────────────────────────────
          SECTION 4: [HUMAN SKILLS] INTERACTIVE PILL SHOWCASE
      ───────────────────────────────────────────────────────────── */}
      <section id="human-skills" className="py-14 sm:py-20 bg-white relative z-10 border-b border-zinc-100">
        <div className="w-full max-w-[1280px] mx-auto px-4 sm:px-6">
          
          {/* Section Header */}
          <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-4 pb-5 border-b border-zinc-200 mb-8 sm:mb-10">
            <div>
              <span className="text-xs font-bold uppercase tracking-widest text-zinc-400 block mb-1">
                [ HUMAN SKILLS ]
              </span>
              <h2 className="font-display text-2xl sm:text-4xl lg:text-[44px] font-bold text-zinc-950 tracking-tight">
                Do more than humanly possible
              </h2>
            </div>
            <p className="text-zinc-600 text-xs sm:text-sm max-w-[420px]">
              &ldquo;The holy grail of what service enterprises are chasing &mdash; this is a game changer for creative business productivity.&rdquo;
            </p>
          </div>

          {/* Interactive 2-Column Showcase */}
          <div className="bg-[#F8F9FA] rounded-[32px] p-5 sm:p-8 lg:p-10 border border-zinc-200/80 shadow-xs">
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
              
              {/* Left Column: Clickable Skill Pills */}
              <div className="lg:col-span-5 space-y-2.5">
                <h3 className="font-display text-lg sm:text-xl font-bold text-zinc-950 mb-2">
                  The only agents that work like humans &mdash; with infinite skills
                </h3>

                {humanSkills.map((skill) => {
                  const isSelected = skill.id === activeSkillId;
                  return (
                    <div
                      key={skill.id}
                      onClick={() => setActiveSkillId(skill.id)}
                      className={`gsap-skill-pill p-3.5 rounded-2xl border transition-all duration-200 cursor-pointer ${
                        isSelected
                          ? 'bg-white border-zinc-950 shadow-md ring-1 ring-zinc-950/10'
                          : 'bg-white/70 hover:bg-white border-zinc-200/70 hover:border-zinc-300 shadow-2xs'
                      }`}
                    >
                      <div className="flex items-center justify-between">
                        <span className="text-xs sm:text-sm font-bold text-zinc-900">{skill.title}</span>
                        <span className={`text-[10px] font-bold px-2 py-0.5 rounded ${
                          isSelected ? 'bg-zinc-950 text-white' : 'bg-zinc-100 text-zinc-600'
                        }`}>
                          {skill.badge}
                        </span>
                      </div>
                      {isSelected && (
                        <p className="text-xs text-zinc-600 leading-relaxed mt-2 pt-2 border-t border-zinc-100">
                          {skill.shortDesc}
                        </p>
                      )}
                    </div>
                  );
                })}
              </div>

              {/* Right Column: Live Artifact Canvas Preview */}
              <div className="lg:col-span-7 gsap-skill-canvas">
                <div className="bg-white rounded-2xl p-5 sm:p-7 border border-zinc-200 shadow-lg space-y-4">
                  <div className="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <div>
                      <div className="text-xs sm:text-sm font-bold text-zinc-900">{selectedSkill.previewTitle}</div>
                      <div className="text-[11px] text-zinc-500 font-mono">{selectedSkill.previewSubtitle}</div>
                    </div>
                    <span className="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-100">
                      ● Active Artifact
                    </span>
                  </div>

                  <div className="space-y-2.5">
                    {selectedSkill.details.map((row, rIdx) => (
                      <div key={rIdx} className="p-2.5 bg-zinc-50 rounded-xl border border-zinc-100 flex items-center justify-between text-xs">
                        <span className="text-zinc-500 font-medium">{row.label}</span>
                        <span className="font-semibold text-zinc-950 font-mono text-right">{row.value}</span>
                      </div>
                    ))}
                  </div>

                  <div className="pt-1 flex items-center justify-between text-xs text-zinc-500">
                    <span>Tamper-Proof Audit Hash: <code className="text-zinc-700 font-mono">SHA256:8f2a...</code></span>
                    <span className="text-zinc-950 font-bold">1-Click PDF &rarr;</span>
                  </div>
                </div>
              </div>

            </div>
          </div>

        </div>
      </section>

      {/* ─────────────────────────────────────────────────────────────
          SECTION 5: [TECHNOLOGY] TELEMETRY & LIVE RADAR
      ───────────────────────────────────────────────────────────── */}
      <section id="technology" className="py-14 sm:py-20 bg-white relative z-10 border-b border-zinc-100">
        <div className="w-full max-w-[1280px] mx-auto px-4 sm:px-6">
          
          <div className="max-w-[760px] mx-auto text-center mb-10">
            <span className="text-xs font-bold uppercase tracking-widest text-zinc-400 block mb-1">
              [ PROPRIETARY AGENTIC TECHNOLOGY ]
            </span>
            <h2 className="font-display text-2xl sm:text-4xl font-bold text-zinc-950 tracking-tight">
              Enterprise security &amp; live ambient awareness
            </h2>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-5 sm:gap-6">
            
            {/* Card 1: Studio Automation Telemetry */}
            <div className="gsap-tech-card p-5 sm:p-6 rounded-3xl bg-zinc-50 border border-zinc-200/80 space-y-3.5 shadow-2xs">
              <div className="flex items-center justify-between">
                <span className="text-xs font-bold text-zinc-700 uppercase tracking-wider">
                  STUDIO AUTOMATION
                </span>
                <span className="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">
                  Top 5%
                </span>
              </div>
              <div className="font-display text-3xl sm:text-4xl font-bold text-zinc-950">
                84.6%
              </div>
              <p className="text-xs text-zinc-600 leading-relaxed">
                Repetitive client briefing, GST invoicing, and call-sheet coordination executed with zero manual effort.
              </p>
              <div className="p-3 bg-white rounded-xl border border-zinc-200 space-y-2 text-xs">
                <div className="flex justify-between font-semibold">
                  <span className="text-zinc-600">Hours Saved / Week:</span>
                  <span className="text-zinc-950 font-bold">18.5 hrs</span>
                </div>
                <div className="w-full h-1.5 bg-zinc-100 rounded-full overflow-hidden">
                  <div className="w-4/5 h-full bg-emerald-500 rounded-full" />
                </div>
              </div>
            </div>

            {/* Card 2: Live Ambient Radar */}
            <div className="gsap-tech-card p-5 sm:p-6 rounded-3xl bg-zinc-50 border border-zinc-200/80 space-y-3.5 shadow-2xs">
              <div className="flex items-center justify-between">
                <span className="text-xs font-bold text-zinc-700 uppercase tracking-wider">
                  AMBIENT RADAR
                </span>
                <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
              </div>
              <div className="font-display text-3xl sm:text-4xl font-bold text-zinc-950">
                24/7 Sweep
              </div>
              <p className="text-xs text-zinc-600 leading-relaxed">
                Continuously scans for pending commercial usage renewals, unconfirmed hold dates, and unpaid UPI advances.
              </p>
              <div className="p-3 bg-white rounded-xl border border-zinc-200 space-y-1.5 text-xs">
                <div className="flex items-center justify-between font-medium">
                  <span className="text-zinc-700">&bull; Lakme Shoot Oct 24 Hold</span>
                  <span className="text-emerald-700 font-bold">Safe</span>
                </div>
                <div className="flex items-center justify-between font-medium">
                  <span className="text-zinc-700">&bull; Oberoi Advance Deposit</span>
                  <span className="text-amber-700 font-bold">Pending</span>
                </div>
              </div>
            </div>

            {/* Card 3: Encrypted SHA-256 Vault */}
            <div className="gsap-tech-card p-5 sm:p-6 rounded-3xl bg-zinc-50 border border-zinc-200/80 space-y-3.5 shadow-2xs">
              <div className="flex items-center justify-between">
                <span className="text-xs font-bold text-zinc-700 uppercase tracking-wider">
                  SECURITY &amp; AUDIT
                </span>
                <Lock className="w-4 h-4 text-zinc-500" />
              </div>
              <div className="font-display text-3xl sm:text-4xl font-bold text-zinc-950">
                SHA-256
              </div>
              <p className="text-xs text-zinc-600 leading-relaxed">
                Every commercial agreement, model NDA, and tax invoice is cryptographically sealed and verifiable under the IT Act 2000.
              </p>
              <div className="p-3 bg-white rounded-xl border border-zinc-200 font-mono text-[10.5px] text-zinc-600 truncate">
                Hash: 7f8a9b1c2d3e4f5a6b7c8d9e...
              </div>
            </div>

          </div>

        </div>
      </section>

    </main>
  );
}
