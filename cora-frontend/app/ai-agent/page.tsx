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

// ── 2. Carousel AI Agents Data (Broad Functional Roles & Indian Portraits) ──
const aiAgentsList = [
  {
    id: 'marketing',
    title: 'Marketing Agent',
    role: 'Campaigns, brand reach & content',
    avatar: '/images/cora_agent_marketing.jpg',
    action: 'Deploy Campaigns',
  },
  {
    id: 'sales',
    title: 'Sales Agent',
    role: '24/7 lead capture & client booking',
    avatar: '/images/cora_agent_sales.jpg',
    action: 'Capture Leads',
  },
  {
    id: 'operations',
    title: 'Operations Agent',
    role: 'Studio schedules, holds & crew',
    avatar: '/images/cora_agent_operations.jpg',
    action: 'Manage Studio',
  },
  {
    id: 'finance',
    title: 'Finance Agent',
    role: '18% GST billing & cash flow',
    avatar: '/images/cora_agent_finance.jpg',
    action: 'Reconcile Ledger',
  },
  {
    id: 'legal',
    title: 'Legal Agent',
    role: 'NDAs, usage rights & contracts',
    avatar: '/images/cora_agent_legal.jpg',
    action: 'Draft Agreements',
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
      setActiveCarouselIndex((prev) => (prev > 0 ? prev - 1 : aiAgentsList.length - 1));
    } else {
      setActiveCarouselIndex((prev) => (prev < aiAgentsList.length - 1 ? prev + 1 : 0));
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
      <section className="relative min-h-[92vh] flex flex-col justify-between pt-24 pb-12 sm:pt-28 sm:pb-16 overflow-hidden border-b border-zinc-100 bg-[#FFFFFF]">
        
        {/* Giant Watermark 3D Typography in Background */}
        <div className="absolute inset-0 flex flex-col items-center justify-center pointer-events-none select-none z-0 overflow-hidden">
          <span className="font-display font-extrabold text-[120px] xs:text-[160px] sm:text-[230px] md:text-[290px] lg:text-[360px] text-zinc-950/[0.04] leading-[0.88] tracking-tighter">
            Super
          </span>
          <span className="font-display font-extrabold text-[95px] xs:text-[130px] sm:text-[190px] md:text-[240px] lg:text-[300px] text-zinc-950/[0.04] leading-[0.88] tracking-tighter">
            Agents<span className="text-[0.3em] align-top">™</span>
          </span>
        </div>

        {/* Soft Warm Radial Glow Behind Portrait */}
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[340px] sm:w-[560px] h-[340px] sm:h-[560px] bg-radial from-orange-200/40 via-amber-100/20 to-transparent rounded-full blur-3xl pointer-events-none z-0" />

        {/* Center Stage: Indian Boy Hero Portrait with Gradient Visor */}
        <div className="relative z-10 w-full max-w-[1280px] mx-auto px-4 flex-1 flex flex-col items-center justify-center my-auto">
          <div ref={heroActorRef} className="relative w-[300px] h-[300px] sm:w-[440px] sm:h-[440px] lg:w-[520px] lg:h-[520px]">
            <div className="relative w-full h-full [mask-image:linear-gradient(to_bottom,black_75%,transparent_100%)]">
              <Image
                src="/images/cora_hero_indian_agent.jpg"
                alt="Cora AI Super Agent with Futuristic Visor"
                fill
                priority
                className="object-contain drop-shadow-2xl"
              />
            </div>
          </div>
        </div>

        {/* Bottom Stage Overlay: Headline & CTAs */}
        <div className="relative z-20 w-full max-w-[800px] mx-auto px-4 text-center space-y-5 gsap-hero-fade mt-2">
          <h1 className="font-display text-3xl xs:text-4xl sm:text-5xl lg:text-[54px] font-bold text-zinc-950 leading-[1.08] tracking-[-0.03em]">
            A new era of humans, <br />
            with <span className="text-zinc-900 font-extrabold">AI Super Agents&trade;</span>
          </h1>

          <div className="flex flex-col sm:flex-row items-center justify-center gap-3 pt-1">
            <Link
              href="/workspace/login"
              className="w-full sm:w-auto px-8 py-3.5 rounded-full bg-zinc-950 text-white hover:bg-zinc-800 text-sm sm:text-base font-bold shadow-md hover:shadow-lg transition-all hover:-translate-y-0.5"
            >
              Try Super Agents
            </Link>
            <a
              href="#capabilities"
              className="w-full sm:w-auto px-7 py-3.5 rounded-full bg-white/95 border border-zinc-200 text-zinc-800 hover:text-zinc-950 hover:bg-zinc-50 text-sm sm:text-base font-semibold shadow-2xs backdrop-blur-sm transition-all"
            >
              Watch Intro
            </a>
          </div>
        </div>

      </section>

      {/* ─────────────────────────────────────────────────────────────
          SECTION 2: [CAPABILITIES] AI AGENTS CAROUSEL DECK
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
                Agents for everything
              </h2>
            </div>
            <div className="flex items-center gap-3">
              <p className="text-zinc-600 text-xs sm:text-sm max-w-[380px] hidden md:block">
                Your AI Co-Founder commands specialized autonomous agents across marketing, sales, operations, finance &amp; legal.
              </p>
              <div className="flex items-center gap-2 shrink-0">
                <button
                  onClick={() => scrollCarousel('left')}
                  className="w-9 h-9 rounded-full border border-zinc-200 flex items-center justify-center hover:bg-zinc-100 text-zinc-800 transition-colors cursor-pointer"
                  aria-label="Previous agent"
                >
                  <ChevronLeft className="w-4 h-4" />
                </button>
                <button
                  onClick={() => scrollCarousel('right')}
                  className="w-9 h-9 rounded-full border border-zinc-200 flex items-center justify-center hover:bg-zinc-100 text-zinc-800 transition-colors cursor-pointer"
                  aria-label="Next agent"
                >
                  <ChevronRight className="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>

          {/* Cards Carousel Grid: Image Background with Single-Line Text */}
          <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            {aiAgentsList.map((card, idx) => {
              const isCurrent = idx === activeCarouselIndex;
              return (
                <div
                  key={card.id}
                  onClick={() => setActiveCarouselIndex(idx)}
                  className={`gsap-capability-card rounded-3xl overflow-hidden h-[410px] sm:h-[430px] cursor-pointer relative group transition-all duration-300 flex flex-col justify-between p-5 ${
                    isCurrent
                      ? 'ring-2 ring-zinc-950 shadow-2xl scale-[1.02]'
                      : 'border border-zinc-200/90 shadow-2xs hover:shadow-md hover:scale-[1.01]'
                  }`}
                >
                  {/* Full Background Image */}
                  <Image
                    src={card.avatar}
                    alt={card.title}
                    fill
                    className="object-cover group-hover:scale-105 transition-transform duration-700 pointer-events-none"
                    priority={idx < 2}
                  />

                  {/* Gradient Dark Overlay for Legibility */}
                  <div className="absolute inset-0 bg-gradient-to-t from-black/95 via-black/35 to-transparent pointer-events-none" />

                  {/* Top: Index Indicator */}
                  <div className="relative z-10 flex items-center justify-between">
                    <span className="text-[11px] font-mono font-bold text-white/90 bg-black/40 backdrop-blur-md px-2.5 py-1 rounded-full border border-white/15">
                      0{idx + 1}
                    </span>
                    {isCurrent && (
                      <span className="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-500 text-white flex items-center gap-1">
                        <span className="w-1.5 h-1.5 rounded-full bg-white animate-pulse" />
                        Active
                      </span>
                    )}
                  </div>

                  {/* Bottom: Single-Line Title & Description */}
                  <div className="relative z-10 space-y-1">
                    <h3 className="font-display text-lg font-bold text-white leading-tight truncate">
                      {card.title}
                    </h3>
                    <p className="text-zinc-300 text-xs leading-snug line-clamp-1">
                      {card.role}
                    </p>
                    <div className="pt-2 flex items-center justify-between text-xs font-semibold text-white/95 border-t border-white/15">
                      <span>{card.action}</span>
                      <ArrowRight className="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
                    </div>
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
      <section id="orchestration" className="py-16 sm:py-24 bg-white relative z-10 border-b border-zinc-100">
        <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-center">
            
            {/* Left: Interactive Multi-Agent Tree Branching Flowchart */}
            <div className="lg:col-span-7 gsap-orchestration-box">
              <div className="relative w-full max-w-[540px] mx-auto bg-[#FAFAFB] sm:bg-white rounded-3xl p-6 sm:p-10 border border-zinc-200/80 shadow-xs flex flex-col items-center justify-center min-h-[480px]">
                
                {/* SVG Connecting Branch Lines */}
                <svg
                  className="absolute inset-0 w-full h-full pointer-events-none stroke-zinc-300"
                  viewBox="0 0 500 460"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  {/* Top Vertical Line into Co-Founder */}
                  <path d="M 250,0 L 250,26" strokeWidth="1.5" />

                  {/* Branching from Top Co-Founder to 3 Agents */}
                  <path d="M 250,68 C 250,115 100,115 100,165" strokeWidth="1.5" />
                  <path d="M 250,68 L 250,140" strokeWidth="1.5" />
                  <path d="M 250,68 C 250,115 400,115 400,165" strokeWidth="1.5" />

                  {/* Vertical Lines from Agents into Action Pills */}
                  <path d="M 100,205 L 100,236" strokeWidth="1.5" />
                  <path d="M 250,180 L 250,210" strokeWidth="1.5" />
                  <path d="M 400,205 L 400,236" strokeWidth="1.5" />

                  {/* Convergence Lines from Action Pills to Bottom Stem */}
                  <path d="M 100,274 C 100,340 250,340 250,405" strokeWidth="1.5" />
                  <path d="M 250,248 L 250,405" strokeWidth="1.5" />
                  <path d="M 400,274 C 400,340 250,340 250,405" strokeWidth="1.5" />

                  {/* Bottom Vertical Stem */}
                  <path d="M 250,405 L 250,460" strokeWidth="1.5" />
                </svg>

                {/* Node 0: Top Central AI Co-Founder Avatar */}
                <div className="relative z-10 flex flex-col items-center mb-16 sm:mb-20">
                  <div className="w-12 h-12 sm:w-14 sm:h-14 rounded-full overflow-hidden border-2 border-white shadow-md bg-white ring-4 ring-amber-100/80">
                    <Image
                      src="/images/cora_hero_indian_agent.jpg"
                      alt="AI Co-Founder"
                      width={56}
                      height={56}
                      className="object-cover"
                    />
                  </div>
                </div>

                {/* Nodes 1, 2, 3: 3 Parallel Specialized Agents & Pills */}
                <div className="relative z-10 w-full grid grid-cols-3 gap-2 sm:gap-4 items-start text-center mb-16 sm:mb-20">
                  
                  {/* Left Branch: Marketing / Copywriting */}
                  <div className="flex flex-col items-center space-y-3">
                    <div className="w-10 h-10 sm:w-12 sm:h-12 rounded-full overflow-hidden border-2 border-white shadow-sm bg-white ring-4 ring-pink-100/90">
                      <Image
                        src="/images/cora_agent_marketing.jpg"
                        alt="Marketing Agent"
                        width={48}
                        height={48}
                        className="object-cover"
                      />
                    </div>
                    <div className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-zinc-200/90 shadow-2xs text-[11px] sm:text-xs font-semibold text-zinc-800 whitespace-nowrap">
                      <span className="w-2 h-2 rounded-full bg-sky-500" />
                      <span>Copywriting</span>
                    </div>
                  </div>

                  {/* Center Branch: Operations / Email Design */}
                  <div className="flex flex-col items-center space-y-3 -mt-6 sm:-mt-8">
                    <div className="w-10 h-10 sm:w-12 sm:h-12 rounded-full overflow-hidden border-2 border-white shadow-sm bg-white ring-4 ring-purple-100/90">
                      <Image
                        src="/images/cora_agent_sales.jpg"
                        alt="Sales Agent"
                        width={48}
                        height={48}
                        className="object-cover"
                      />
                    </div>
                    <div className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-zinc-200/90 shadow-2xs text-[11px] sm:text-xs font-semibold text-zinc-800 whitespace-nowrap">
                      <span className="w-2 h-2 rounded-full bg-amber-500" />
                      <span>Email Design</span>
                    </div>
                  </div>

                  {/* Right Branch: Legal & Campaign Lifecycle */}
                  <div className="flex flex-col items-center space-y-3">
                    <div className="w-10 h-10 sm:w-12 sm:h-12 rounded-full overflow-hidden border-2 border-white shadow-sm bg-white ring-4 ring-cyan-100/90">
                      <Image
                        src="/images/cora_agent_legal.jpg"
                        alt="Legal Agent"
                        width={48}
                        height={48}
                        className="object-cover"
                      />
                    </div>
                    <div className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-zinc-200/90 shadow-2xs text-[11px] sm:text-xs font-semibold text-zinc-800 whitespace-nowrap">
                      <span className="w-2 h-2 rounded-full bg-purple-500" />
                      <span>Campaign lifecycle</span>
                    </div>
                  </div>

                </div>

              </div>
            </div>

            {/* Right: Narrative */}
            <div className="lg:col-span-5 space-y-5">
              <span className="text-xs font-mono font-bold uppercase tracking-widest text-indigo-600 block">
                AGENTS IN MINUTES
              </span>
              <h2 className="font-display text-3xl sm:text-4xl lg:text-[44px] font-bold text-zinc-950 leading-[1.1] tracking-tight">
                One prompt spins up an entire team
              </h2>
              <p className="text-zinc-600 text-sm sm:text-base leading-relaxed">
                Your goals, workflows, and frustrations &mdash; automatically delegated to a team of agents.
              </p>
              <div className="pt-2">
                <Link
                  href="/workspace/login"
                  className="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl bg-zinc-950 text-white hover:bg-zinc-800 text-sm font-bold shadow-sm transition-all hover:-translate-y-0.5"
                >
                  <span>Explore all agents</span>
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
