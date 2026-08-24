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
  ChevronDown,
  QrCode,
  Share2,
  Users,
} from 'lucide-react';
import { trackEvent } from '@/components/analytics/Analytics';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

// ── 0. Frequently Asked Questions Data (Reference Accurate) ──
const superAgentFaqs = [
  {
    question: 'What are Cora Super Agents?',
    answer:
      'Cora Super Agents are autonomous AI co-founders and specialized operational agents built specifically for commercial photography, film, and creative production studios. They understand commercial context, studio rate cards, 18% GST rules, and hold schedules to execute real workflows without manual supervision.',
  },
  {
    question: 'How are Super Agents different from AI chatbots?',
    answer:
      'Unlike passive chatbots that only generate text answers, Super Agents possess tool execution capabilities. They can lock calendar bay holds, calculate compliant 18% GST splits, generate SHA-256 sealed NDAs, dispatch call-sheets to 50+ crew on WhatsApp, and reconcile bank deposits directly in your Cora workspace.',
  },
  {
    question: 'What can Super Agents do?',
    answer:
      'Your AI team commands 5 major commercial pillars: Marketing (client outreach, campaigns, pitch decks), Sales (24/7 lead intake, instant WhatsApp quotations), Operations (studio bay calendar holds, crew call-sheets, gear checklists), Finance (18% GST invoicing, dynamic UPI QR standees, ledger reconciliation), and Legal (tamper-proof NDAs, model releases, usage licensing).',
  },
  {
    question: 'How do I create a Super Agent?',
    answer:
      'You can delegate tasks via natural language directly in your Cora workspace, chat naturally on WhatsApp, or tag @Cora in any client shoot brief. Your agent team instantly interprets the scope, delegates subtasks, and reports back upon completion.',
  },
  {
    question: 'Are Super Agents secure?',
    answer:
      'Yes. All commercial shoot contracts, client portfolios, and rate cards are encrypted with enterprise-grade AES-256. Every e-signature, NDA, and tax invoice receives a cryptographic SHA-256 seal with logged IP and timestamp audit trails, fully compliant under the Indian IT Act 2000.',
  },
  {
    question: 'How much do Super Agents cost?',
    answer:
      'Super Agents are included directly in all Cora Studio Pro and Enterprise plans with flexible token quotas. There are zero per-agent seat markups — one unified subscription unlocks your entire autonomous agent team across marketing, sales, operations, finance, and legal.',
  },
  {
    question: 'Can Super Agents connect to tools outside Cora?',
    answer:
      'Yes. Super Agents integrate natively with WhatsApp Business API, Razorpay, PhonePe UPI, Google Calendar, Apple Calendar, Tally Prime, Zoho Books, and cloud storage vaults like Google Drive and Dropbox.',
  },
  {
    question: 'Do Super Agents learn and improve over time?',
    answer:
      'Yes. Every commercial booking, custom rate negotiation, client preference, and shoot feedback signal grounds your agent team deeper into your studio\'s unique operating DNA, making future pricing and scheduling recommendations increasingly fast and accurate.',
  },
];

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

// ── 3. Broad Business Agent Showcase Data (Clean Vector SVG Icons, Zero Emojis) ──
const broadAgentSkills = [
  {
    id: 'marketing',
    title: 'Marketing & Content',
    role: 'Marketing Super Agent',
    icon: TrendingUp,
    badge: 'Growth Engine',
    shortDesc: 'Autonomous multi-channel campaigns, high-converting copy, and audience targeting.',
    agentAvatar: '/images/cora_agent_marketing.jpg',
    metrics: [
      { label: 'Campaign Reach', value: '+340%', change: 'MoM' },
      { label: 'Content Velocity', value: '14 Assets/hr', change: 'Automated' },
      { label: 'Active Channels', value: '4 Synced', change: 'Live' },
    ],
    speechPill: 'Marketing funnels synced & scheduled across 4 channels.',
  },
  {
    id: 'sales',
    title: 'Sales & Lead Intake',
    role: 'Sales Super Agent',
    icon: Zap,
    badge: '24/7 Lead Capture',
    shortDesc: 'Instant lead qualification, rate card quoting, and 24/7 client onboarding.',
    agentAvatar: '/images/cora_agent_sales.jpg',
    metrics: [
      { label: 'Response Time', value: '0.8s', change: 'Instant' },
      { label: 'Lead Conversion', value: '42.8%', change: '+18.4%' },
      { label: 'Active Inquiries', value: '28 In Queue', change: 'Live' },
    ],
    speechPill: 'Inbound brief qualified & formal quote delivered in 0.8s.',
  },
  {
    id: 'operations',
    title: 'Operations & Workflows',
    role: 'Operations Super Agent',
    icon: Layers,
    badge: 'Resource Allocation',
    shortDesc: 'Automated schedule dispatch, task delegation, and cross-team project execution.',
    agentAvatar: '/images/cora_agent_operations.jpg',
    metrics: [
      { label: 'Schedule Conflicts', value: '0 Clashes', change: 'Resolved' },
      { label: 'Task Dispatch', value: '100% On-Time', change: 'Automated' },
      { label: 'Team Sync Rate', value: '14/14 Crew', change: 'Confirmed' },
    ],
    speechPill: 'Studio calendar locked & 14 call-sheets dispatched.',
  },
  {
    id: 'finance',
    title: 'Finance & Invoicing',
    role: 'Finance Super Agent',
    icon: Receipt,
    badge: '18% GST Billing',
    shortDesc: 'Automated tax invoices, dynamic UPI QR standees, and ledger reconciliation.',
    agentAvatar: '/images/cora_agent_finance.jpg',
    metrics: [
      { label: 'Tax Accuracy', value: '100%', change: 'GSTR-1' },
      { label: 'Settlement Time', value: 'Instant UPI', change: 'Auto-Matched' },
      { label: 'Ledger Status', value: 'Synced', change: 'Tally Prime' },
    ],
    speechPill: '18% GST invoice verified & advance deposit matched.',
  },
  {
    id: 'legal',
    title: 'Legal & Compliance',
    role: 'Legal Super Agent',
    icon: ShieldCheck,
    badge: 'SHA-256 Vault',
    shortDesc: 'Cryptographically sealed NDAs, commercial usage licenses, and audit trails.',
    agentAvatar: '/images/cora_agent_legal.jpg',
    metrics: [
      { label: 'Seal Standard', value: 'SHA-256', change: 'IT Act 2000' },
      { label: 'Signature Audit', value: 'OTP Verified', change: 'Tamper-Proof' },
      { label: 'Vault Security', value: 'AES-256', change: 'Encrypted' },
    ],
    speechPill: 'Usage license cryptographically sealed with SHA-256 hash.',
  },
];

// ── Universal Deterministic Mathematical Task Counter ──
// Globally identical and synchronized across all visitors worldwide.
// Starts at exactly 1,000 tasks at launch timestamp (Aug 24, 2026 19:40 IST)
// and scales smoothly & organically to 1,000,000 tasks over 365 days.
const GLOBAL_LAUNCH_TIMESTAMP = 1787580600000; // August 24, 2026 19:40:00 IST (14:10:00 UTC)
const TOTAL_SPAN_MS = 365 * 24 * 60 * 60 * 1000; // 365 days in milliseconds

function computeGlobalUniversalTasks(): number {
  const now = typeof window !== 'undefined' ? Date.now() : GLOBAL_LAUNCH_TIMESTAMP;
  const elapsedMs = Math.max(0, now - GLOBAL_LAUNCH_TIMESTAMP);
  const progress = Math.min(1.0, elapsedMs / TOTAL_SPAN_MS);

  // S-Curve mathematical growth equation: 1,000 starting baseline scaling organically to 1,000,000 over 365 days
  const sCurve = 0.15 * progress + 0.85 * Math.pow(progress, 1.85);
  return Math.floor(1000 + (1000000 - 1000) * sCurve);
}

export default function AiAgentPage() {
  const [activeSkillId, setActiveSkillId] = useState<string>('marketing');
  const [liveTasksCount, setLiveTasksCount] = useState<number>(1000);
  const [openFaqIndex, setOpenFaqIndex] = useState<number | null>(null);

  const selectedSkill = broadAgentSkills.find((s) => s.id === activeSkillId) || broadAgentSkills[0];

  // Universal real-time synchronization:
  // Derived purely from UTC timestamp Date.now() — identical across every browser globally with zero reset on reload.
  useEffect(() => {
    if (typeof window !== 'undefined') {
      localStorage.removeItem('cora_task_offset');
    }
    setLiveTasksCount(computeGlobalUniversalTasks());

    const interval = setInterval(() => {
      setLiveTasksCount(computeGlobalUniversalTasks());
    }, 1000);

    return () => clearInterval(interval);
  }, []);

  const mainContainerRef = useRef<HTMLElement>(null);
  const heroActorRef = useRef<HTMLDivElement>(null);
  const capabilitiesSectionRef = useRef<HTMLElement>(null);
  const cardsTrackRef = useRef<HTMLDivElement>(null);

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

      // 3. Pinned Parallax Horizontal Scroll on Capabilities (AI Agent Team)
      if (cardsTrackRef.current && capabilitiesSectionRef.current) {
        const track = cardsTrackRef.current;
        const getScrollDistance = () => track.scrollWidth - window.innerWidth + 80;

        gsap.to(track, {
          x: () => -getScrollDistance(),
          ease: 'none',
          scrollTrigger: {
            trigger: capabilitiesSectionRef.current,
            start: 'top top',
            end: () => `+=${Math.max(window.innerHeight * 1.5, getScrollDistance() + 200)}`,
            pin: true,
            scrub: 0.8,
            invalidateOnRefresh: true,
            anticipatePin: 1,
          },
        });
      }

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

      // 6. Broad Agents & Workspace Live Demo (Comfortable scroll-pacing)
      const humanSkillsSection = document.getElementById('human-skills');
      if (humanSkillsSection) {
        ScrollTrigger.create({
          trigger: humanSkillsSection,
          start: 'top 20%',
          end: 'bottom 80%',
          scrub: 1.2,
          onUpdate: (self) => {
            const skillIndex = Math.min(
              broadAgentSkills.length - 1,
              Math.max(0, Math.floor(self.progress * broadAgentSkills.length))
            );
            setActiveSkillId(broadAgentSkills[skillIndex].id);
          },
        });
      }

      gsap.fromTo(
        '.gsap-skill-canvas',
        { scale: 0.96, opacity: 0 },
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
          SECTION 1: HERO PINNED STAGE (ClickUp Super Agents Style)
      ───────────────────────────────────────────────────────────── */}
      <section className="relative min-h-[92vh] flex flex-col justify-between pt-24 pb-12 sm:pt-28 sm:pb-16 overflow-hidden border-b border-zinc-100 bg-[#FFFFFF]">
        
        {/* Giant Watermark 3D Typography in Background (No TM) */}
        <div className="absolute inset-0 flex flex-col items-center justify-center pointer-events-none select-none z-0 overflow-hidden">
          <span className="font-display font-extrabold text-[120px] xs:text-[160px] sm:text-[230px] md:text-[290px] lg:text-[360px] text-zinc-950/[0.04] leading-[0.88] tracking-tighter">
            Super
          </span>
          <span className="font-display font-extrabold text-[95px] xs:text-[130px] sm:text-[190px] md:text-[240px] lg:text-[300px] text-zinc-950/[0.04] leading-[0.88] tracking-tighter">
            Agents
          </span>
        </div>

        {/* Soft Warm Radial Glow Behind Portrait */}
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[340px] sm:w-[560px] h-[340px] sm:h-[560px] bg-radial from-orange-200/40 via-amber-100/20 to-transparent rounded-full blur-3xl pointer-events-none z-0" />

        {/* Center Stage: Indian Female AI Co-Founder with Gradient Visor & Dissolving Edges */}
        <div className="relative z-10 w-full max-w-[1280px] mx-auto px-4 flex-1 flex flex-col items-center justify-center my-auto">
          <div className="relative w-[280px] h-[280px] xs:w-[320px] xs:h-[320px] sm:w-[420px] sm:h-[420px] lg:w-[480px] lg:h-[480px]">
            {/* Gradient Dissolve Mask so bottom/shoulders smoothly melt into canvas */}
            <div className="relative w-full h-full [mask-image:linear-gradient(to_bottom,black_60%,black_80%,transparent_100%)]">
              <Image
                src="/images/cora_hero_indian_agent.png"
                alt="Cora AI Super Agent with Futuristic Visor"
                fill
                priority
                className="object-contain drop-shadow-xl"
              />
              {/* Bottom Scrim Dissolve */}
              <div className="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-white via-white/30 to-transparent pointer-events-none" />
            </div>
          </div>
        </div>

        {/* Bottom Stage Overlay: Headline & CTAs (No TM) */}
        <div className="relative z-20 w-full max-w-[800px] mx-auto px-4 text-center space-y-5 gsap-hero-fade mt-2">
          <h1 className="font-display text-3xl xs:text-4xl sm:text-5xl lg:text-[54px] font-bold text-zinc-950 leading-[1.08] tracking-[-0.03em]">
            A new era of humans, <br />
            with <span className="text-zinc-900 font-extrabold">AI Super Agents</span>
          </h1>

          <div className="flex flex-col sm:flex-row items-center justify-center gap-3 pt-1">
            <Link
              href="/workspace/login"
              className="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-zinc-950 text-white hover:bg-zinc-800 text-sm sm:text-base font-semibold shadow-md hover:shadow-lg transition-all hover:-translate-y-0.5"
            >
              Try Super Agents
            </Link>
            <a
              href="#capabilities"
              className="w-full sm:w-auto px-7 py-3.5 rounded-xl bg-white border border-zinc-300 text-zinc-800 hover:text-zinc-950 hover:bg-zinc-50 text-sm sm:text-base font-semibold shadow-2xs backdrop-blur-sm transition-all"
            >
              Watch Intro
            </a>
          </div>
        </div>

      </section>

      {/* ─────────────────────────────────────────────────────────────
          SECTION 2: [CAPABILITIES] AI AGENTS PINNED HORIZONTAL PARALLAX
      ───────────────────────────────────────────────────────────── */}
      <section
        ref={capabilitiesSectionRef}
        id="capabilities"
        className="min-h-screen py-12 sm:py-16 bg-white relative z-10 border-b border-zinc-100 overflow-hidden flex flex-col justify-center"
      >
        <div className="w-full max-w-[1360px] mx-auto px-4 sm:px-6 shrink-0 mb-6 sm:mb-8">
          <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-4 pb-4 border-b border-zinc-200">
            <div>
              <span className="text-xs font-bold uppercase tracking-widest text-zinc-400 block mb-1">
                [ CAPABILITIES ]
              </span>
              <h2 className="font-display text-2xl sm:text-4xl lg:text-[44px] font-bold text-zinc-950 tracking-tight">
                Agents for everything
              </h2>
            </div>
            <p className="text-zinc-600 text-xs sm:text-sm max-w-[420px]">
              Scroll down to explore your autonomous agent team across marketing, sales, operations, finance &amp; legal.
            </p>
          </div>
        </div>

        {/* Pinned Horizontal Parallax Scroll Track */}
        <div className="w-full overflow-hidden px-4 sm:px-6">
          <div
            ref={cardsTrackRef}
            className="flex gap-4 sm:gap-6 w-max will-change-transform pb-2"
          >
            {aiAgentsList.map((card, idx) => (
              <div
                key={card.id}
                className="rounded-3xl overflow-hidden h-[480px] sm:h-[530px] lg:h-[570px] w-[290px] xs:w-[330px] sm:w-[370px] md:w-[400px] shrink-0 cursor-pointer relative group transition-all duration-300 flex flex-col justify-between p-6 border border-zinc-200/90 shadow-2xs hover:shadow-xl hover:scale-[1.01]"
              >
                {/* Full Background Image */}
                <Image
                  src={card.avatar}
                  alt={card.title}
                  fill
                  className="object-cover group-hover:scale-105 transition-transform duration-700 pointer-events-none"
                  priority={idx < 3}
                />

                {/* Gradient Dark Overlay for Legibility */}
                <div className="absolute inset-0 bg-gradient-to-t from-black/95 via-black/35 to-transparent pointer-events-none" />

                {/* Top: Index Indicator (No Active Badge) */}
                <div className="relative z-10 flex items-center justify-between">
                  <span className="text-[11px] font-mono font-bold text-white/90 bg-black/40 backdrop-blur-md px-3 py-1 rounded-full border border-white/15">
                    0{idx + 1}
                  </span>
                </div>

                {/* Bottom: Single-Line Title & Description */}
                <div className="relative z-10 space-y-1.5">
                  <h3 className="font-display text-xl sm:text-2xl font-bold text-white leading-tight">
                    {card.title}
                  </h3>
                  <p className="text-zinc-300 text-xs sm:text-sm leading-snug">
                    {card.role}
                  </p>
                  <div className="pt-3 flex items-center justify-between text-xs font-semibold text-white/95 border-t border-white/15">
                    <span>{card.action}</span>
                    <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                  </div>
                </div>
              </div>
            ))}
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
          SECTION 4: [WORKSPACE INTELLIGENCE] 5 BROAD AGENTS LIVE WORKSPACE DEMO
      ───────────────────────────────────────────────────────────── */}
      <section id="human-skills" className="py-16 sm:py-24 bg-white relative z-10 border-b border-zinc-100">
        <div className="w-full max-w-[1280px] mx-auto px-4 sm:px-6">
          
          <div className="bg-[#F8F9FA] rounded-[32px] sm:rounded-[40px] p-5 sm:p-8 lg:p-12 border border-zinc-200/80 shadow-xs">
            
            {/* Header */}
            <div className="max-w-[720px] mb-8 sm:mb-10 space-y-2">
              <span className="text-xs font-mono font-bold uppercase tracking-widest text-indigo-600 block">
                [ WORKSPACE INTELLIGENCE ]
              </span>
              <h2 className="font-display text-2xl sm:text-3xl lg:text-[34px] font-bold text-zinc-950 leading-tight">
                The only AI agents with cross-functional execution &ndash; from marketing to legal
              </h2>
              <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed">
                Explore how specialized AI agents handle broad commercial workflows autonomously.
              </p>
            </div>

            {/* Mobile-Friendly Horizontal Segment Bar (Visible on mobile & tablet) */}
            <div className="lg:hidden mb-6 -mx-1 flex gap-2 overflow-x-auto pb-2 scrollbar-none snap-x">
              {broadAgentSkills.map((skill) => {
                const isSelected = skill.id === activeSkillId;
                const IconComp = skill.icon;
                return (
                  <button
                    key={skill.id}
                    onClick={() => setActiveSkillId(skill.id)}
                    className={`shrink-0 snap-start px-3.5 py-2 rounded-xl flex items-center gap-2 text-xs font-semibold transition-all cursor-pointer ${
                      isSelected
                        ? 'bg-zinc-950 text-white shadow-sm border border-zinc-950'
                        : 'bg-white text-zinc-700 border border-zinc-200 hover:bg-zinc-100'
                    }`}
                  >
                    <IconComp className={`w-3.5 h-3.5 ${isSelected ? 'text-white' : 'text-zinc-500'}`} />
                    <span>{skill.title}</span>
                  </button>
                );
              })}
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">
              
              {/* Left Column: Desktop Sticky Tab Selectors */}
              <div className="hidden lg:block lg:col-span-5 space-y-2.5 lg:sticky lg:top-28">
                {broadAgentSkills.map((skill) => {
                  const isSelected = skill.id === activeSkillId;
                  const IconComp = skill.icon;
                  if (isSelected) {
                    return (
                      <div
                        key={skill.id}
                        onClick={() => setActiveSkillId(skill.id)}
                        className="p-4 rounded-xl bg-white border-2 border-zinc-950 shadow-md transition-all cursor-pointer space-y-1.5"
                      >
                        <div className="flex items-center justify-between">
                          <div className="flex items-center gap-2.5">
                            <div className="w-7 h-7 rounded-lg bg-zinc-950 text-white flex items-center justify-center">
                              <IconComp className="w-3.5 h-3.5" />
                            </div>
                            <span className="text-sm font-bold text-zinc-950">{skill.title}</span>
                          </div>
                          <span className="text-[10.5px] font-bold px-2 py-0.5 rounded-md bg-zinc-100 text-zinc-800 border border-zinc-200">
                            {skill.badge}
                          </span>
                        </div>
                        <p className="text-xs text-zinc-600 leading-relaxed pt-0.5">
                          {skill.shortDesc}
                        </p>
                      </div>
                    );
                  }
                  return (
                    <button
                      key={skill.id}
                      onClick={() => setActiveSkillId(skill.id)}
                      className="w-full px-4 py-3 rounded-xl bg-white/80 hover:bg-white border border-zinc-200/80 hover:border-zinc-300 shadow-2xs flex items-center gap-2.5 text-left transition-all cursor-pointer group"
                    >
                      <div className="w-7 h-7 rounded-lg bg-zinc-100 group-hover:bg-zinc-200 text-zinc-600 flex items-center justify-center transition-colors">
                        <IconComp className="w-3.5 h-3.5" />
                      </div>
                      <span className="text-xs sm:text-sm font-semibold text-zinc-700 group-hover:text-zinc-950 flex-1">
                        {skill.title}
                      </span>
                      <ChevronRight className="w-4 h-4 text-zinc-400 group-hover:text-zinc-700 transition-transform group-hover:translate-x-0.5" />
                    </button>
                  );
                })}
              </div>

              {/* Right Column: Live Visual SaaS Workspace Dashboard */}
              <div className="lg:col-span-7 gsap-skill-canvas">
                <div className="bg-white rounded-2xl sm:rounded-3xl border border-zinc-200/90 shadow-xl overflow-hidden">
                  
                  {/* Browser / Workspace App Bar */}
                  <div className="px-4 sm:px-5 py-3 bg-zinc-50 border-b border-zinc-200 flex items-center justify-between text-xs">
                    <div className="flex items-center gap-2">
                      <div className="flex gap-1.5">
                        <span className="w-2.5 h-2.5 rounded-full bg-zinc-300" />
                        <span className="w-2.5 h-2.5 rounded-full bg-zinc-300" />
                        <span className="w-2.5 h-2.5 rounded-full bg-zinc-300" />
                      </div>
                      <span className="font-mono text-zinc-400 text-[11px] ml-2 truncate">
                        cora.workspace / {activeSkillId} / live
                      </span>
                    </div>
                    <div className="flex items-center gap-1.5 shrink-0">
                      <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
                      <span className="font-semibold text-zinc-700 text-[11px]">Autonomous</span>
                    </div>
                  </div>

                  {/* Agent Header & Metrics Strip */}
                  <div className="p-4 sm:p-5 border-b border-zinc-100 bg-white">
                    <div className="flex items-center justify-between gap-3 mb-3.5">
                      <div className="flex items-center gap-3">
                        <div className="w-10 h-10 sm:w-11 sm:h-11 rounded-xl overflow-hidden shrink-0 border border-zinc-200 ring-2 ring-zinc-100 shadow-xs">
                          <Image
                            src={selectedSkill.agentAvatar}
                            alt={selectedSkill.role}
                            width={44}
                            height={44}
                            className="object-cover"
                          />
                        </div>
                        <div>
                          <h4 className="text-sm sm:text-base font-bold text-zinc-950">{selectedSkill.role}</h4>
                          <p className="text-xs text-zinc-500 font-medium">{selectedSkill.badge}</p>
                        </div>
                      </div>
                      <div className="text-right">
                        <span className="text-[10.5px] font-mono font-bold text-zinc-700 bg-zinc-100 px-2.5 py-1 rounded-md border border-zinc-200/80">
                          Latency: 0.8s
                        </span>
                      </div>
                    </div>

                    {/* 3 Metric Pills */}
                    <div className="grid grid-cols-3 gap-2">
                      {selectedSkill.metrics.map((m, idx) => (
                        <div key={idx} className="p-2.5 bg-zinc-50 rounded-xl border border-zinc-100 text-center">
                          <span className="text-[10px] text-zinc-500 block uppercase tracking-wider font-semibold truncate">
                            {m.label}
                          </span>
                          <span className="text-xs sm:text-sm font-bold text-zinc-950 font-mono block mt-0.5">
                            {m.value}
                          </span>
                          <span className="text-[9.5px] text-emerald-700 font-semibold block">
                            {m.change}
                          </span>
                        </div>
                      ))}
                    </div>
                  </div>

                  {/* Dynamic Visual Content Body */}
                  <div className="p-4 sm:p-5 space-y-3 bg-zinc-50/40 min-h-[320px] flex flex-col justify-between">
                    
                    {/* Demo 1: Marketing & Growth */}
                    {activeSkillId === 'marketing' && (
                      <div className="space-y-3">
                        <div className="p-3.5 rounded-xl bg-white border border-zinc-200/80 shadow-xs space-y-2">
                          <div className="flex items-center justify-between text-xs pb-1.5 border-b border-zinc-100">
                            <span className="font-bold text-zinc-900">Multi-Channel Distribution Matrix</span>
                            <span className="text-[10px] font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">4 Channels Synced</span>
                          </div>
                          <div className="grid grid-cols-2 gap-2 text-xs">
                            <div className="p-2 bg-zinc-50 rounded-lg border border-zinc-100 flex items-center justify-between">
                              <span className="text-zinc-700 font-medium">Instagram &amp; Reels</span>
                              <span className="text-[10px] font-mono font-semibold text-zinc-500">18:00 IST</span>
                            </div>
                            <div className="p-2 bg-zinc-50 rounded-lg border border-zinc-100 flex items-center justify-between">
                              <span className="text-zinc-700 font-medium">Client Newsletter</span>
                              <span className="text-[10px] font-mono font-semibold text-emerald-700">2.4k Opens</span>
                            </div>
                          </div>
                        </div>

                        <div className="p-3.5 rounded-xl bg-white border border-zinc-200/80 shadow-xs space-y-2">
                          <span className="text-xs font-bold text-zinc-900 block">Content Generation Queue</span>
                          <div className="flex flex-wrap gap-1.5">
                            <span className="text-[10.5px] font-medium bg-zinc-100 text-zinc-700 px-2.5 py-1 rounded-md border border-zinc-200/60">
                              Format: 4K Master Video
                            </span>
                            <span className="text-[10.5px] font-medium bg-zinc-100 text-zinc-700 px-2.5 py-1 rounded-md border border-zinc-200/60">
                              Tone: Commercial Luxury
                            </span>
                            <span className="text-[10.5px] font-medium bg-zinc-100 text-zinc-700 px-2.5 py-1 rounded-md border border-zinc-200/60">
                              Target: Brand Leads
                            </span>
                          </div>
                        </div>
                      </div>
                    )}

                    {/* Demo 2: Sales & Lead Intake */}
                    {activeSkillId === 'sales' && (
                      <div className="space-y-3">
                        <div className="p-3.5 rounded-xl bg-white border border-zinc-200/80 shadow-xs space-y-2">
                          <div className="flex items-center justify-between text-xs pb-1.5 border-b border-zinc-100">
                            <span className="font-bold text-zinc-900">Lead Pipeline &amp; Rate Quotation</span>
                            <span className="text-[10px] font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">0.8s Response</span>
                          </div>
                          <div className="p-2.5 bg-zinc-50 rounded-lg text-xs space-y-1 font-mono">
                            <div className="flex justify-between text-zinc-600">
                              <span>Commercial Shoot (2 Days):</span>
                              <span className="font-bold text-zinc-950">₹1,20,000</span>
                            </div>
                            <div className="flex justify-between text-zinc-600">
                              <span>18% GST (CGST + SGST):</span>
                              <span className="font-bold text-zinc-950">₹21,600</span>
                            </div>
                            <div className="flex justify-between pt-1 border-t border-zinc-200 text-zinc-950 font-bold">
                              <span>Total Quotation:</span>
                              <span className="text-emerald-700">₹1,41,600</span>
                            </div>
                          </div>
                        </div>

                        <div className="p-3 rounded-xl bg-white border border-zinc-200/80 shadow-xs flex items-center justify-between text-xs">
                          <span className="text-zinc-600 font-medium">WhatsApp Delivery Status:</span>
                          <span className="font-bold text-zinc-900 font-mono">Dispatched with Advance QR</span>
                        </div>
                      </div>
                    )}

                    {/* Demo 3: Operations & Workflows */}
                    {activeSkillId === 'operations' && (
                      <div className="space-y-3">
                        <div className="p-3.5 rounded-xl bg-white border border-zinc-200/80 shadow-xs space-y-2">
                          <div className="flex items-center justify-between text-xs pb-1.5 border-b border-zinc-100">
                            <span className="font-bold text-zinc-900">Production Sprint &amp; Schedule Grid</span>
                            <span className="text-[10px] font-mono font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded">Sprint #42 Active</span>
                          </div>
                          <div className="space-y-1.5 text-xs">
                            <div className="p-2 rounded-lg bg-zinc-50 border border-zinc-100 flex items-center justify-between">
                              <span className="font-medium text-zinc-800">Studio Bay 02 Setup</span>
                              <span className="text-[10px] font-mono font-bold text-emerald-700">Ready (06:45 IST)</span>
                            </div>
                            <div className="p-2 rounded-lg bg-zinc-50 border border-zinc-100 flex items-center justify-between">
                              <span className="font-medium text-zinc-800">Crew Call-Sheets Dispatched</span>
                              <span className="text-[10px] font-mono font-bold text-emerald-700">14/14 Confirmed</span>
                            </div>
                          </div>
                        </div>

                        <div className="p-3 rounded-xl bg-white border border-zinc-200/80 shadow-xs flex items-center justify-between text-xs">
                          <span className="text-zinc-600 font-medium">Clash Prevention Engine:</span>
                          <span className="font-bold text-emerald-700 font-mono">0 Conflicts Detected</span>
                        </div>
                      </div>
                    )}

                    {/* Demo 4: Finance & Invoicing */}
                    {activeSkillId === 'finance' && (
                      <div className="space-y-3">
                        <div className="p-3.5 rounded-xl bg-white border border-zinc-200/80 shadow-xs space-y-2">
                          <div className="flex items-center justify-between text-xs pb-1.5 border-b border-zinc-100">
                            <span className="font-bold text-zinc-900">GSTR-1 Tax Invoice #CORA-2026-104</span>
                            <span className="text-[10px] font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">GSTR-1 Valid</span>
                          </div>
                          <div className="grid grid-cols-3 gap-1.5 text-center text-xs">
                            <div className="p-2 bg-zinc-50 rounded-lg border border-zinc-100">
                              <span className="text-[10px] text-zinc-500 block">Base Value</span>
                              <span className="font-bold text-zinc-950 font-mono">₹2,50,000</span>
                            </div>
                            <div className="p-2 bg-zinc-50 rounded-lg border border-zinc-100">
                              <span className="text-[10px] text-zinc-500 block">18% GST</span>
                              <span className="font-bold text-zinc-950 font-mono">₹45,000</span>
                            </div>
                            <div className="p-2 bg-zinc-50 rounded-lg border border-zinc-100">
                              <span className="text-[10px] text-zinc-500 block">Total Payable</span>
                              <span className="font-bold text-emerald-700 font-mono">₹2,95,000</span>
                            </div>
                          </div>
                        </div>

                        <div className="p-3 rounded-xl bg-white border border-zinc-200/80 shadow-xs flex items-center justify-between text-xs">
                          <span className="text-zinc-600 font-medium">UPI Settlement Soundbox:</span>
                          <span className="font-bold text-zinc-900 font-mono">PhonePe / GPay Auto-Matched</span>
                        </div>
                      </div>
                    )}

                    {/* Demo 5: Legal & Compliance */}
                    {activeSkillId === 'legal' && (
                      <div className="space-y-3">
                        <div className="p-3.5 rounded-xl bg-white border border-zinc-200/80 shadow-xs space-y-2">
                          <div className="flex items-center justify-between text-xs pb-1.5 border-b border-zinc-100">
                            <span className="font-bold text-zinc-900">Commercial License &amp; NDA #LIC-892</span>
                            <span className="text-[10px] font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">IT Act Sealed</span>
                          </div>
                          <div className="p-2 bg-zinc-50 rounded-lg text-[11px] font-mono text-zinc-600 truncate border border-zinc-100">
                            SHA256: 9f82ab174e3c90df81e2893a7c6f01b7a2d4e6f8...
                          </div>
                        </div>

                        <div className="p-3 rounded-xl bg-white border border-zinc-200/80 shadow-xs flex items-center justify-between text-xs">
                          <span className="text-zinc-600 font-medium">Client Verification:</span>
                          <span className="font-bold text-zinc-900 font-mono">Mobile OTP Audit Trail Locked</span>
                        </div>
                      </div>
                    )}

                    {/* Bottom Status Action Bar */}
                    <div className="pt-2 flex items-center justify-between gap-3 border-t border-zinc-200/80">
                      <div className="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-zinc-950 text-white text-xs font-semibold shadow-xs">
                        <span>{selectedSkill.speechPill}</span>
                      </div>
                      <span className="text-[11px] font-mono text-zinc-500 hidden sm:inline">
                        100% Autonomous
                      </span>
                    </div>

                  </div>

                </div>
              </div>

            </div>
          </div>

        </div>
      </section>

      {/* ─────────────────────────────────────────────────────────────
          SECTION 5: REAL-TIME AUTOMATION TELEMETRY BANNER (ClickUp Style)
      ───────────────────────────────────────────────────────────── */}
      <section id="telemetry-banner" className="py-12 sm:py-16 bg-white relative z-10 border-b border-zinc-100">
        <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
          <div className="relative overflow-hidden bg-white border border-zinc-200/90 rounded-[32px] sm:rounded-[44px] p-8 sm:p-14 text-center shadow-xs">
            
            {/* Dot Matrix Pattern */}
            <div className="absolute inset-0 bg-[radial-gradient(#e4e4e7_1.2px,transparent_1.2px)] [background-size:20px_20px] pointer-events-none opacity-80" />

            {/* Left Indian Female AI Agent Team Member */}
            <div className="absolute -left-6 sm:-left-2 md:left-2 lg:left-6 bottom-0 top-6 sm:top-4 w-[200px] sm:w-[280px] lg:w-[340px] pointer-events-none opacity-90 hidden sm:block">
              <div className="relative w-full h-full [mask-image:linear-gradient(to_right,black_70%,transparent_100%)]">
                <Image
                  src="/images/cora_telemetry_female_agent.png"
                  alt="Cora Female Super Agent"
                  fill
                  className="object-contain object-bottom"
                />
              </div>
            </div>

            {/* Right Indian Male AI Agent Team Member */}
            <div className="absolute -right-6 sm:-right-2 md:right-2 lg:right-6 bottom-0 top-6 sm:top-4 w-[200px] sm:w-[280px] lg:w-[340px] pointer-events-none opacity-90 hidden sm:block">
              <div className="relative w-full h-full [mask-image:linear-gradient(to_left,black_70%,transparent_100%)]">
                <Image
                  src="/images/cora_telemetry_male_agent.png"
                  alt="Cora Male Super Agent"
                  fill
                  className="object-contain object-bottom"
                />
              </div>
            </div>

            {/* Center Live Telemetry Headline & Ticking Number */}
            <div className="relative z-10 max-w-[680px] mx-auto space-y-3 sm:space-y-4">
              <h3 className="font-display text-2xl sm:text-3xl lg:text-[38px] font-bold text-zinc-950 leading-tight">
                Work that used to take hours, <br />
                now happens in <span className="bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 bg-clip-text text-transparent font-extrabold">seconds</span>.
              </h3>

              <div className="font-display font-extrabold text-5xl sm:text-7xl lg:text-[88px] text-zinc-950 tracking-tight py-2 font-mono select-none">
                {liveTasksCount.toLocaleString('en-US')}
              </div>

              <p className="text-zinc-500 text-xs sm:text-sm font-medium">
                Tasks automated by Super Agents
              </p>

              <div className="pt-2">
                <Link
                  href="/workspace/login"
                  className="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl bg-zinc-950 text-white hover:bg-zinc-800 text-sm font-semibold shadow-md hover:shadow-lg transition-all hover:-translate-y-0.5"
                >
                  <span>Try Super Agents</span>
                  <ArrowRight className="w-4 h-4" />
                </Link>
              </div>
            </div>

          </div>
        </div>
      </section>

      {/* ─────────────────────────────────────────────────────────────
          SECTION 6: FREQUENTLY ASKED QUESTIONS (Accordion Layout)
      ───────────────────────────────────────────────────────────── */}
      <section id="faqs" className="py-20 sm:py-28 bg-white relative z-10">
        <div className="w-full max-w-[880px] mx-auto px-4 sm:px-6">
          
          <h2 className="font-display text-3xl sm:text-4xl lg:text-[46px] font-bold text-zinc-950 tracking-tight text-center mb-12 sm:mb-16">
            Frequently asked <span className="text-zinc-400">questions</span>
          </h2>

          <div className="divide-y divide-zinc-200 border-t border-b border-zinc-200">
            {superAgentFaqs.map((faq, fIdx) => {
              const isOpen = openFaqIndex === fIdx;
              return (
                <div key={fIdx} className="py-5 sm:py-6">
                  <button
                    onClick={() => setOpenFaqIndex(isOpen ? null : fIdx)}
                    className="w-full flex items-center justify-between gap-4 text-left group cursor-pointer focus:outline-none"
                    aria-expanded={isOpen}
                  >
                    <span className="font-display text-base sm:text-lg font-semibold text-zinc-900 group-hover:text-zinc-950 transition-colors">
                      {faq.question}
                    </span>
                    <ChevronDown
                      className={`w-5 h-5 shrink-0 text-zinc-400 group-hover:text-zinc-700 transition-transform duration-200 ${
                        isOpen ? 'rotate-180 text-zinc-950' : ''
                      }`}
                    />
                  </button>

                  {isOpen && (
                    <div className="pt-3.5 pr-8 text-zinc-600 text-sm sm:text-base leading-relaxed animate-in fade-in slide-in-from-top-1 duration-200">
                      <p>{faq.answer}</p>
                    </div>
                  )}
                </div>
              );
            })}
          </div>

        </div>
      </section>

      {/* ─────────────────────────────────────────────────────────────
          SECTION 7: [INFINITE SKILLS] 100+ CAPABILITIES TEAM SHOWCASE (AFTER FAQS)
      ───────────────────────────────────────────────────────────── */}
      <section id="skills-constellation" className="py-20 sm:py-28 bg-white relative z-10 border-t border-zinc-100">
        <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
          
          {/* Section Header */}
          <div className="max-w-[760px] mx-auto text-center mb-10 sm:mb-14 space-y-3">
            <span className="text-xs font-mono font-bold uppercase tracking-widest text-indigo-600 block">
              [ INFINITE SKILLS ]
            </span>
            <h2 className="font-display text-3xl sm:text-4xl lg:text-[46px] font-bold text-zinc-950 tracking-tight leading-[1.12]">
              One team. Hundreds of skills.
            </h2>
            <p className="text-zinc-600 text-sm sm:text-base max-w-[620px] mx-auto leading-relaxed">
              From client brief intake and 18% GST tax invoices to SHA-256 NDAs and studio hold scheduling &mdash; your AI agent team handles every complex workflow across your commercial studio.
            </p>
          </div>

          {/* Reference Showcase Visual Banner Card with object-cover */}
          <div className="relative w-full rounded-[32px] sm:rounded-[44px] overflow-hidden border border-zinc-200/90 shadow-xl bg-gradient-to-r from-[#4C1D95] via-[#7C3AED] via-45% to-[#EA580C] group">
            <div className="relative w-full h-[320px] xs:h-[380px] sm:h-[480px] md:h-[560px] lg:h-[680px]">
              <Image
                src="/images/cora_agents_skills_team.jpg"
                alt="Cora AI Super Agents Team with Infinite Skills"
                fill
                priority
                className="object-cover object-center group-hover:scale-[1.01] transition-transform duration-700"
              />
            </div>
          </div>

        </div>
      </section>

    </main>
  );
}
