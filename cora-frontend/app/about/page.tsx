import React from 'react';
import type { Metadata } from 'next';
import Image from 'next/image';
import Link from 'next/link';
import { 
  Play, 
  ArrowRight, 
  ArrowUpRight, 
  ShieldCheck, 
  Lock, 
  CheckCircle2, 
  Award, 
  Cpu, 
  Users, 
  Heart,
  FileText,
  Sparkles,
  Zap,
  Globe,
  Layers,
  ChevronRight
} from 'lucide-react';

export const metadata: Metadata = {
  title: 'About Cora — The Convergence of Software, AI & Humans',
  description: 'Meet the team behind Cora. Building the autonomous AI co-founder and operating system for modern studios, production houses, and creative agencies.',
  alternates: {
    canonical: 'https://heycora.in/about/',
  },
};

const TEAM_MEMBERS = [
  {
    name: 'Anika Roy',
    role: 'AI Research Lead',
    image: '/images/about_team_anika.jpg',
    bg: 'bg-[#FEE2E2]', // Soft Red / Pink
    border: 'border-red-100',
  },
  {
    name: 'Dr. Aarav Patel',
    role: 'Core Systems Architect',
    image: '/images/about_team_aarav.jpg',
    bg: 'bg-[#FEF3C7]', // Soft Amber
    border: 'border-amber-100',
  },
  {
    name: 'Ananya Sharma',
    role: 'Operations & Community',
    image: '/images/about_team_ananya.jpg',
    bg: 'bg-[#E0F2FE]', // Soft Sky Blue
    border: 'border-sky-100',
  },
  {
    name: 'Rohan Verma',
    role: 'Workflow Automation',
    image: '/images/about_team_rohan.jpg',
    bg: 'bg-[#FFEDD5]', // Soft Orange / Peach
    border: 'border-orange-100',
  },
  {
    name: 'Tanya Mehta',
    role: 'Product & Design',
    image: '/images/about_team_tanya.jpg',
    bg: 'bg-[#F3E8FF]', // Soft Lavender
    border: 'border-purple-100',
  },
  {
    name: 'Dev Singhania',
    role: 'Financial Infrastructure',
    image: '/images/about_team_dev.jpg',
    bg: 'bg-[#EDE9FE]', // Soft Indigo
    border: 'border-indigo-100',
  },
  {
    name: 'Pooja Nair',
    role: 'Legal Rails & Compliance',
    image: '/images/about_team_pooja.jpg',
    bg: 'bg-[#E0E7FF]', // Soft Periwinkle
    border: 'border-blue-100',
  },
  {
    name: 'Meera Iyer',
    role: 'Creative Direction',
    image: '/images/about_team_meera.jpg',
    bg: 'bg-[#DCFCE7]', // Soft Mint Green
    border: 'border-emerald-100',
  },
];

const VALUES = [
  {
    title: 'Autonomous Execution',
    desc: 'Software should not require constant manual babysitting. We engineer systems that trigger commercial proposals, contracts, invoices, and call-sheet reminders automatically.'
  },
  {
    title: 'Zero Bloat & Monochromatic Focus',
    desc: 'We uphold a strict Notion and Linear-inspired monochromatic design ethos. No distracting colorful clutter — only high-velocity tools engineered for operators.'
  },
  {
    title: 'Bank-Grade Data Sovereignty',
    desc: 'Your studio IP, contracts, and financial records belong solely to you. We maintain immutable SHA-256 cryptographic audit trails and SOC-2 security protocols.'
  },
  {
    title: 'Founder-First Accessibility',
    desc: 'Every ambitious creator deserves enterprise-grade operating power from day one. That is why our core platform remains accessible without punitive high paywalls.'
  }
];

const TRUSTED_STUDIOS = [
  'Lakmé Commercials',
  'Dharma 2.0',
  'Red Chillies VFX',
  'Spotify Studios',
  'Nike Creative India',
  'Vogue Studio Lab',
  'Amazon Prime Video'
];

export default function AboutPage() {
  return (
    <main className="w-full relative pt-28 sm:pt-36 pb-24 overflow-hidden bg-white selection:bg-zinc-950 selection:text-white">
      
      {/* ─────────────────────────────────────────────────────────────
          SECTION 1: HERO STAGE (ClickUp Style Indian Team Mosaic Grid)
      ───────────────────────────────────────────────────────────── */}
      <section className="w-full max-w-[1400px] mx-auto px-3 xs:px-4 sm:px-6 text-center mb-24 sm:mb-32">
        
        {/* Eyebrow */}
        <div className="inline-flex items-center gap-2 px-3.5 py-1 text-[11px] xs:text-xs sm:text-[13px] font-mono font-bold uppercase tracking-widest text-[#7B2BF9] mb-3 sm:mb-4">
          <span>MAXIMIZE HUMAN PRODUCTIVITY</span>
        </div>

        {/* Main Headline (Reduced font weight, uppercase CORA) */}
        <h1 className="font-display text-4xl xs:text-5xl sm:text-6xl lg:text-[76px] font-bold text-zinc-950 leading-[1.08] tracking-[-0.03em] max-w-[960px] mx-auto mb-6 sm:mb-8">
          We are <span className="font-bold text-zinc-950">CORA</span>.
        </h1>

        {/* Hero CTA Button (Strictly following Cora Design System Tokens: rounded-xl, monochromatic) */}
        <div className="flex items-center justify-center mb-12 sm:mb-16">
          <a
            href="#manifesto"
            className="inline-flex items-center gap-2 bg-zinc-950 text-white hover:bg-zinc-900 border border-zinc-800 px-6 sm:px-7 py-3 sm:py-3.5 rounded-xl text-xs sm:text-sm font-semibold tracking-tight shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all group"
          >
            <Play className="w-3.5 h-3.5 fill-white text-white group-hover:scale-110 transition-transform" />
            <span>Learn more about us</span>
          </a>
        </div>

        {/* ── 5-Column Floating Team Mosaic Grid (Single Formation on Mobile & Desktop) ── */}
        <div className="relative w-full overflow-hidden max-w-[1240px] mx-auto pt-1 pb-4">
          
          <div className="relative z-10 grid grid-cols-5 gap-1.5 xs:gap-2.5 sm:gap-4 lg:gap-5 items-center justify-center">
            
            {/* Column 1: Far Left (Top: Pink, Bottom: Cream) */}
            <div className="flex flex-col gap-1.5 xs:gap-2.5 sm:gap-4">
              {/* Card 1 Top */}
              <div className="relative rounded-xl xs:rounded-2xl sm:rounded-[32px] bg-[#FDE8EA] aspect-[4/4.7] overflow-hidden shadow-2xs">
                <div className="absolute inset-0 flex items-end justify-center">
                  <Image
                    src="/images/hero_mosaic_1_top.png"
                    alt="Cora Team Member"
                    fill
                    sizes="(max-width: 640px) 20vw, 240px"
                    className="object-contain object-bottom"
                  />
                </div>
              </div>

              {/* Card 1 Bottom */}
              <div className="relative rounded-xl xs:rounded-2xl sm:rounded-[32px] bg-[#FDF2E9] aspect-[4/4.7] overflow-hidden shadow-2xs">
                <div className="absolute inset-0 flex items-end justify-center">
                  <Image
                    src="/images/hero_mosaic_1_bottom.png"
                    alt="Cora Team Member"
                    fill
                    sizes="(max-width: 640px) 20vw, 240px"
                    className="object-contain object-bottom"
                  />
                </div>
              </div>
            </div>

            {/* Column 2: Mid Left (Top: Powder Blue, Bottom: Lilac) */}
            <div className="flex flex-col gap-1.5 xs:gap-2.5 sm:gap-4">
              {/* Card 2 Top */}
              <div className="relative rounded-xl xs:rounded-2xl sm:rounded-[32px] bg-[#E0F0FA] aspect-[4/4.7] overflow-hidden shadow-2xs">
                <div className="absolute inset-0 flex items-end justify-center">
                  <Image
                    src="/images/hero_mosaic_2_top.png"
                    alt="Cora Team Member"
                    fill
                    sizes="(max-width: 640px) 20vw, 240px"
                    className="object-contain object-bottom"
                  />
                </div>
              </div>

              {/* Card 2 Bottom */}
              <div className="relative rounded-xl xs:rounded-2xl sm:rounded-[32px] bg-[#EFE9FA] aspect-[4/4.7] overflow-hidden shadow-2xs">
                <div className="absolute inset-0 flex items-end justify-center">
                  <Image
                    src="/images/hero_mosaic_2_bottom.png"
                    alt="Cora Team Member"
                    fill
                    sizes="(max-width: 640px) 20vw, 240px"
                    className="object-contain object-bottom"
                  />
                </div>
              </div>
            </div>

            {/* Column 3: Center Hero Leader Card (Warm Apricot / Peach - Wider & Taller) */}
            <div className="col-span-1 flex justify-center">
              <div className="relative w-full rounded-2xl xs:rounded-3xl sm:rounded-[36px] bg-[#FDEDE7] aspect-[4/5.1] overflow-hidden shadow-xs">
                <div className="absolute inset-0 flex items-end justify-center">
                  <Image
                    src="/images/hero_mosaic_center.png"
                    alt="Cora AI Team Lead"
                    fill
                    sizes="(max-width: 640px) 25vw, 320px"
                    className="object-contain object-bottom"
                  />
                </div>
              </div>
            </div>

            {/* Column 4: Mid Right (Top: Soft Lavender, Bottom: Warm Beige) */}
            <div className="flex flex-col gap-1.5 xs:gap-2.5 sm:gap-4">
              {/* Card 4 Top */}
              <div className="relative rounded-xl xs:rounded-2xl sm:rounded-[32px] bg-[#EFE8F9] aspect-[4/4.7] overflow-hidden shadow-2xs">
                <div className="absolute inset-0 flex items-end justify-center">
                  <Image
                    src="/images/hero_mosaic_4_top.png"
                    alt="Cora Team Member"
                    fill
                    sizes="(max-width: 640px) 20vw, 240px"
                    className="object-contain object-bottom"
                  />
                </div>
              </div>

              {/* Card 4 Bottom */}
              <div className="relative rounded-xl xs:rounded-2xl sm:rounded-[32px] bg-[#FAF1E8] aspect-[4/4.7] overflow-hidden shadow-2xs">
                <div className="absolute inset-0 flex items-end justify-center">
                  <Image
                    src="/images/hero_mosaic_4_bottom.png"
                    alt="Cora Team Member"
                    fill
                    sizes="(max-width: 640px) 20vw, 240px"
                    className="object-contain object-bottom"
                  />
                </div>
              </div>
            </div>

            {/* Column 5: Far Right (Top: Soft Mint, Bottom: Sky Blue) */}
            <div className="flex flex-col gap-1.5 xs:gap-2.5 sm:gap-4">
              {/* Card 5 Top */}
              <div className="relative rounded-xl xs:rounded-2xl sm:rounded-[32px] bg-[#EAF7F0] aspect-[4/4.7] overflow-hidden shadow-2xs">
                <div className="absolute inset-0 flex items-end justify-center">
                  <Image
                    src="/images/hero_mosaic_5_top.png"
                    alt="Cora Team Member"
                    fill
                    sizes="(max-width: 640px) 20vw, 240px"
                    className="object-contain object-bottom"
                  />
                </div>
              </div>

              {/* Card 5 Bottom */}
              <div className="relative rounded-xl xs:rounded-2xl sm:rounded-[32px] bg-[#E3EFF8] aspect-[4/4.7] overflow-hidden shadow-2xs">
                <div className="absolute inset-0 flex items-end justify-center">
                  <Image
                    src="/images/hero_mosaic_5_bottom.png"
                    alt="Cora Team Member"
                    fill
                    sizes="(max-width: 640px) 20vw, 240px"
                    className="object-contain object-bottom"
                  />
                </div>
              </div>
            </div>

          </div>

        </div>

        {/* ── Brand Trust / Client Ticker at bottom (Matching Reference) ── */}
        <div className="pt-10 sm:pt-12 max-w-[1040px] mx-auto">
          <span className="text-[10px] sm:text-[11px] font-mono font-bold uppercase tracking-widest text-zinc-400 block mb-5 sm:mb-7">
            TRUSTED BY THE BEST
          </span>
          <div className="flex items-center justify-center flex-wrap gap-6 sm:gap-14 opacity-60 grayscale hover:grayscale-0 transition-all text-zinc-700">
            <span className="font-display font-extrabold text-base sm:text-xl tracking-tight">Google</span>
            <span className="font-display font-extrabold text-base sm:text-xl tracking-tight">airbnb</span>
            <span className="font-display font-black text-base sm:text-xl tracking-tighter uppercase italic">NIKE</span>
            <span className="font-display font-extrabold text-base sm:text-xl tracking-tight">Dropbox</span>
            <span className="font-display font-extrabold text-base sm:text-xl tracking-tight">PayPal</span>
            <span className="font-display font-black text-base sm:text-xl tracking-widest uppercase">NETFLIX</span>
          </div>
        </div>

      </section>

      {/* ─────────────────────────────────────────────────────────────
          SECTION 2: OUR MISSION (Mountain Summit Stage)
      ───────────────────────────────────────────────────────────── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-28 sm:mb-40">
        
        {/* Mountain Visual Stage with Gradient Title */}
        <div className="relative w-full rounded-[36px] sm:rounded-[48px] bg-gradient-to-b from-[#F8FAFC] via-[#F1F5F9] to-white border border-zinc-200/80 pt-12 sm:pt-20 px-4 sm:px-8 pb-4 overflow-hidden shadow-xs">
          
          {/* Gradient Mission Heading */}
          <div className="text-center relative z-20 mb-2 sm:mb-4">
            <h2 className="font-display text-4xl xs:text-5xl sm:text-7xl lg:text-[80px] font-extrabold tracking-[-0.035em]">
              <span className="bg-gradient-to-r from-[#2563EB] via-[#9333EA] to-[#DB2777] bg-clip-text text-transparent">
                Our mission.
              </span>
            </h2>
          </div>

          {/* Mountain Graphic with CORA Summit Flag */}
          <div className="relative w-full max-w-[980px] mx-auto aspect-[16/9] -mt-6 xs:-mt-10 sm:-mt-16 overflow-hidden">
            <Image
              src="/images/about_mission_mountain_cora.png"
              alt="Cora Mission Mountain Summit with Flag"
              fill
              priority
              className="object-contain object-top filter contrast-105"
            />
            {/* Subtle bottom fade to seamlessly blend with content */}
            <div className="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-white via-white/80 to-transparent pointer-events-none" />
          </div>

          {/* Mission Copy Container */}
          <div className="relative z-20 max-w-[860px] mx-auto pt-6 pb-8 sm:pb-12 text-left space-y-8">
            <h3 className="font-display text-2xl xs:text-3xl sm:text-4xl font-bold text-zinc-950 tracking-tight text-center sm:text-left">
              Maximize human productivity.
            </h3>

            <div className="space-y-5 text-zinc-600 text-base sm:text-lg leading-relaxed font-normal">
              <p>
                We started Cora because the way creative studios, production sets, and agencies work is broken. Teams juggle dozens of disconnected tools — briefs in one place, conversations in WhatsApp, contracts somewhere else, and GST spreadsheets in Excel — and critical context gets lost in between. We always knew there had to be a better way. So we built one platform to replace them all. While others scramble to bundle fragmented tools or bolt on AI as an afterthought, we&apos;ve been building toward autonomous studio convergence from the start.
              </p>
              <p>
                Today, we&apos;re at the most exciting inflection point in the history of software: the convergence of software, AI, and humans. For the first time, technology doesn&apos;t just organize your work — it understands it, anticipates it, and acts on it. Cora is where this convergence lives. Where studio founders, photographers, and creative directors work side by side with AI agents, in one place, with full context — and where every team can finally focus on the work that actually matters.
              </p>
              <p>
                Thousands of creative professionals across India already rely on Cora to power all of their commercial production work. We&apos;re laser-focused on removing the administrative busywork — the constant toggling, the rate-card negotiations, the delayed client signatures that drain productivity every single day. We envision a world where creative business feels effortlessly organized, contagiously creative, and endlessly efficient.
              </p>
              <p>
                Our mission is clear: maximize human productivity. Not by making people work harder, but by fundamentally reinventing how creative work gets done — through the convergence of everything.
              </p>
            </div>
          </div>

        </div>

      </section>

      {/* ─────────────────────────────────────────────────────────────
          SECTION 3: OUR MOTTO (Weekly Calendar Highlight Strip)
      ───────────────────────────────────────────────────────────── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-28 sm:mb-40">
        <div className="bg-zinc-50 border border-zinc-200/90 rounded-[36px] sm:rounded-[44px] p-8 sm:p-14 md:p-16 space-y-10 shadow-2xs">
          
          <div className="space-y-3">
            <div className="inline-flex items-center gap-2 text-xs font-mono font-bold uppercase tracking-widest text-[#7B2BF9]">
              <span>OUR MOTTO</span>
            </div>
            <h3 className="font-display text-3xl xs:text-4xl sm:text-5xl lg:text-[56px] font-bold text-zinc-950 tracking-tight">
              Save one day, Every week.
            </h3>
          </div>

          {/* Interactive 7-Day Calendar Strip */}
          <div className="w-full overflow-x-auto pb-2 scrollbar-none">
            <div className="min-w-[640px] grid grid-cols-7 gap-3 sm:gap-4">
              
              <div className="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-zinc-200/80 text-center flex flex-col items-center justify-center gap-1 shadow-2xs">
                <span className="font-display text-2xl sm:text-3xl font-bold text-zinc-800">10</span>
                <span className="text-xs font-mono text-zinc-500 font-semibold uppercase">Mon</span>
              </div>

              <div className="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-zinc-200/80 text-center flex flex-col items-center justify-center gap-1 shadow-2xs">
                <span className="font-display text-2xl sm:text-3xl font-bold text-zinc-800">11</span>
                <span className="text-xs font-mono text-zinc-500 font-semibold uppercase">Tue</span>
              </div>

              <div className="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-zinc-200/80 text-center flex flex-col items-center justify-center gap-1 shadow-2xs">
                <span className="font-display text-2xl sm:text-3xl font-bold text-zinc-800">12</span>
                <span className="text-xs font-mono text-zinc-500 font-semibold uppercase">Wed</span>
              </div>

              {/* Highlighted Day (Saved Day - Thursday) */}
              <div className="bg-zinc-950 text-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-zinc-800 text-center flex flex-col items-center justify-center gap-1 shadow-lg ring-4 ring-purple-500/20 scale-105 transition-transform">
                <span className="font-display text-2xl sm:text-3xl font-extrabold text-white">13</span>
                <span className="text-xs font-mono text-purple-400 font-bold uppercase">Thurs</span>
              </div>

              <div className="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-zinc-200/80 text-center flex flex-col items-center justify-center gap-1 shadow-2xs">
                <span className="font-display text-2xl sm:text-3xl font-bold text-zinc-800">14</span>
                <span className="text-xs font-mono text-zinc-500 font-semibold uppercase">Fri</span>
              </div>

              <div className="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-zinc-200/80 text-center flex flex-col items-center justify-center gap-1 shadow-2xs">
                <span className="font-display text-2xl sm:text-3xl font-bold text-zinc-800">15</span>
                <span className="text-xs font-mono text-zinc-500 font-semibold uppercase">Sat</span>
              </div>

              <div className="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-zinc-200/80 text-center flex flex-col items-center justify-center gap-1 shadow-2xs">
                <span className="font-display text-2xl sm:text-3xl font-bold text-zinc-800">16</span>
                <span className="text-xs font-mono text-zinc-500 font-semibold uppercase">Sun</span>
              </div>

            </div>
          </div>

        </div>
      </section>

      {/* ─────────────────────────────────────────────────────────────
          SECTION 4: OUR CORE VALUES (10-Card Bento Grid with Center Title)
      ───────────────────────────────────────────────────────────── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-28 sm:mb-40">
        
        <div className="text-center mb-12 sm:mb-16">
          <div className="inline-flex items-center gap-2 px-3.5 py-1 text-xs font-mono font-bold uppercase tracking-widest text-[#7B2BF9] mb-3">
            <span>DNA &amp; PRINCIPLES</span>
          </div>
          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl font-bold text-zinc-950 tracking-tight">
            Our core values
          </h2>
        </div>

        {/* 3-Column / Bento Values Matrix */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 items-stretch">
          
          {/* Value 1 */}
          <div className="bg-zinc-50 border border-zinc-200/90 rounded-[28px] p-7 sm:p-8 flex flex-col justify-between hover:bg-white hover:shadow-md transition-all">
            <h3 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 leading-tight mb-6">
              Normal<br />sucks
            </h3>
            <span className="text-xs font-mono font-bold text-zinc-400 uppercase tracking-wider">
              #SCRAPPY
            </span>
          </div>

          {/* Value 2 */}
          <div className="bg-zinc-50 border border-zinc-200/90 rounded-[28px] p-7 sm:p-8 flex flex-col justify-between hover:bg-white hover:shadow-md transition-all">
            <h3 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 leading-tight mb-6">
              Challenge the norms,<br />push boundaries
            </h3>
            <span className="text-xs font-mono font-bold text-zinc-400 uppercase tracking-wider">
              #GREATNESS
            </span>
          </div>

          {/* Value 3 */}
          <div className="bg-zinc-50 border border-zinc-200/90 rounded-[28px] p-7 sm:p-8 flex flex-col justify-between hover:bg-white hover:shadow-md transition-all">
            <h3 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 leading-tight mb-6">
              Embrace hard work,<br />do hard things
            </h3>
            <span className="text-xs font-mono font-bold text-zinc-400 uppercase tracking-wider">
              #RESILIENCY
            </span>
          </div>

          {/* Value 4 */}
          <div className="bg-zinc-50 border border-zinc-200/90 rounded-[28px] p-7 sm:p-8 flex flex-col justify-between hover:bg-white hover:shadow-md transition-all">
            <h3 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 leading-tight mb-6">
              Drive<br />urgency
            </h3>
            <span className="text-xs font-mono font-bold text-zinc-400 uppercase tracking-wider">
              #URGENCY
            </span>
          </div>

          {/* Value 5 */}
          <div className="bg-zinc-50 border border-zinc-200/90 rounded-[28px] p-7 sm:p-8 flex flex-col justify-between hover:bg-white hover:shadow-md transition-all">
            <h3 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 leading-tight mb-6">
              Grow 1%<br />every day
            </h3>
            <span className="text-xs font-mono font-bold text-zinc-400 uppercase tracking-wider">
              #GROWTHMINDSET
            </span>
          </div>

          {/* Value 6: Center High-Impact Dark Card */}
          <div className="bg-zinc-950 border border-zinc-800 rounded-[28px] p-7 sm:p-8 flex flex-col justify-between text-white shadow-xl relative overflow-hidden">
            <div className="absolute top-0 right-0 w-32 h-32 bg-purple-500/20 rounded-full blur-2xl pointer-events-none" />
            <h3 className="font-display text-xl sm:text-2xl font-bold text-white leading-tight mb-6">
              Be fun to<br />work with
            </h3>
            <span className="text-xs font-mono font-bold text-purple-400 uppercase tracking-wider">
              #FUN
            </span>
          </div>

          {/* Value 7 */}
          <div className="bg-zinc-50 border border-zinc-200/90 rounded-[28px] p-7 sm:p-8 flex flex-col justify-between hover:bg-white hover:shadow-md transition-all">
            <h3 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 leading-tight mb-6">
              Be in<br />the details
            </h3>
            <span className="text-xs font-mono font-bold text-zinc-400 uppercase tracking-wider">
              #DETAILSMATTER
            </span>
          </div>

          {/* Value 8 */}
          <div className="bg-zinc-50 border border-zinc-200/90 rounded-[28px] p-7 sm:p-8 flex flex-col justify-between hover:bg-white hover:shadow-md transition-all">
            <h3 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 leading-tight mb-6">
              Stay hungry,<br />stay nimble
            </h3>
            <span className="text-xs font-mono font-bold text-zinc-400 uppercase tracking-wider">
              #HARDWORK
            </span>
          </div>

          {/* Value 9 */}
          <div className="bg-zinc-50 border border-zinc-200/90 rounded-[28px] p-7 sm:p-8 flex flex-col justify-between hover:bg-white hover:shadow-md transition-all">
            <h3 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 leading-tight mb-6">
              Deliver the best<br />customer experience
            </h3>
            <span className="text-xs font-mono font-bold text-zinc-400 uppercase tracking-wider">
              #BESTCX
            </span>
          </div>

        </div>

      </section>

      {/* ─────────────────────────────────────────────────────────────
          SECTION 5: OUR JOURNEY (Interactive Timeline)
      ───────────────────────────────────────────────────────────── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-28 sm:mb-40">
        <div className="space-y-12">
          
          <div className="text-center max-w-[620px] mx-auto space-y-3">
            <div className="inline-flex items-center gap-2 text-xs font-mono font-bold uppercase tracking-widest text-[#7B2BF9]">
              <span>OUR EVOLUTION</span>
            </div>
            <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl font-bold text-zinc-950 tracking-tight">
              Our journey.
            </h2>
          </div>

          {/* Timeline Grid */}
          <div className="grid grid-cols-1 md:grid-cols-4 gap-6 relative">
            
            {/* Step 1 */}
            <div className="bg-white border border-zinc-200/90 rounded-[28px] p-6 sm:p-8 space-y-3 shadow-2xs">
              <div className="flex items-center justify-between">
                <span className="text-xs font-mono font-bold text-purple-600 uppercase">Q1 2024</span>
                <span className="w-2.5 h-2.5 rounded-full bg-purple-500" />
              </div>
              <h4 className="font-display text-lg font-bold text-zinc-950">Genesis &amp; Studio Sets</h4>
              <p className="text-xs sm:text-sm text-zinc-600 leading-relaxed font-normal">
                Founded to solve chaotic WhatsApp booking pipelines and manual proposal generation for Indian creators.
              </p>
            </div>

            {/* Step 2 */}
            <div className="bg-white border border-zinc-200/90 rounded-[28px] p-6 sm:p-8 space-y-3 shadow-2xs">
              <div className="flex items-center justify-between">
                <span className="text-xs font-mono font-bold text-purple-600 uppercase">Q3 2024</span>
                <span className="w-2.5 h-2.5 rounded-full bg-purple-500" />
              </div>
              <h4 className="font-display text-lg font-bold text-zinc-950">18% GST &amp; UPI Engine</h4>
              <p className="text-xs sm:text-sm text-zinc-600 leading-relaxed font-normal">
                Engineered India-first tax calculators, dynamic rate cards, and instant UPI soundbox confirmation webhooks.
              </p>
            </div>

            {/* Step 3 */}
            <div className="bg-white border border-zinc-200/90 rounded-[28px] p-6 sm:p-8 space-y-3 shadow-2xs">
              <div className="flex items-center justify-between">
                <span className="text-xs font-mono font-bold text-purple-600 uppercase">Q2 2025</span>
                <span className="w-2.5 h-2.5 rounded-full bg-purple-500" />
              </div>
              <h4 className="font-display text-lg font-bold text-zinc-950">SHA-256 E-Sign Vault</h4>
              <p className="text-xs sm:text-sm text-zinc-600 leading-relaxed font-normal">
                Released legally binding digital signature registries compliant with the Information Technology Act 2000.
              </p>
            </div>

            {/* Step 4: Active / Latest */}
            <div className="bg-zinc-950 text-white border border-zinc-800 rounded-[28px] p-6 sm:p-8 space-y-3 shadow-xl relative overflow-hidden">
              <div className="flex items-center justify-between">
                <span className="text-xs font-mono font-bold text-purple-400 uppercase">2026 &amp; Beyond</span>
                <span className="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping" />
              </div>
              <h4 className="font-display text-lg font-bold text-white">Super Agents &amp; Co-Founder</h4>
              <p className="text-xs sm:text-sm text-zinc-300 leading-relaxed font-normal">
                Full convergence of frontier autonomous reasoning models with end-to-end multi-tenant studio management.
              </p>
            </div>

          </div>

        </div>
      </section>

      {/* ─────────────────────────────────────────────────────────────
          SECTION 6: CO-FOUNDER SPOTLIGHT & MANIFESTO
      ───────────────────────────────────────────────────────────── */}
      <section id="manifesto" className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-28 sm:mb-36 scroll-mt-28">
        <div className="w-full rounded-[36px] bg-[#0A0D10] text-white p-8 sm:p-14 md:p-16 border border-zinc-800 shadow-2xl relative overflow-hidden">
          
          {/* Ambient Glow */}
          <div
            className="absolute top-0 right-1/4 w-[600px] h-[600px] pointer-events-none opacity-20"
            style={{
              background: 'radial-gradient(circle, rgba(99, 102, 241, 0.4) 0%, transparent 70%)',
            }}
          />

          <div className="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
            
            {/* Founder Portrait Column */}
            <div className="lg:col-span-5 flex justify-center">
              <div className="relative w-[280px] xs:w-[320px] sm:w-[360px] h-[400px] sm:h-[480px] rounded-[32px] overflow-hidden border border-zinc-800 shadow-2xl bg-zinc-900 group">
                <Image
                  src="/images/dravya_bansal_cutout_clean.png"
                  alt="Dravya Bansal — Co-Founder of Cora"
                  fill
                  sizes="(max-width: 768px) 320px, 400px"
                  className="object-cover object-top filter brightness-95 contrast-105 group-hover:scale-102 transition-transform duration-500"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-[#0A0D10] via-transparent to-transparent pointer-events-none" />

                {/* Verified Founder Badge */}
                <div className="absolute bottom-5 inset-x-5 z-10">
                  <div className="bg-zinc-950/90 backdrop-blur-md rounded-xl p-3 border border-zinc-700/80 flex items-center justify-between shadow-xs">
                    <div>
                      <div className="text-sm font-bold text-white">Dravya Bansal</div>
                      <div className="text-[11px] text-emerald-400 font-mono">Co-founder &amp; Product Architect</div>
                    </div>
                    <span className="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse" />
                  </div>
                </div>
              </div>
            </div>

            {/* Founder Manifesto Column */}
            <div className="lg:col-span-7 space-y-7">
              <div className="space-y-3">
                <div className="inline-flex items-center gap-2 px-3.5 py-1 bg-zinc-900 rounded-lg text-xs font-mono text-zinc-300 border border-zinc-800">
                  <span>FOUNDER MANIFESTO</span>
                </div>

                <h2 className="font-display text-2xl xs:text-3xl sm:text-[38px] font-bold text-white leading-[1.15] tracking-tight">
                  &ldquo;Our mission is simple: eliminate administrative overhead so creative founders never have to touch a spreadsheet again.&rdquo;
                </h2>
              </div>

              <div className="space-y-4 text-zinc-300 text-sm sm:text-base font-normal leading-relaxed">
                <p>
                  Having worked directly with commercial production sets, photographers, and high-growth creative agencies, we saw brilliant founders spending over 20 hours every week writing repetitive scopes, chasing client contract signatures, and manually computing 18% GST tax splits.
                </p>
                <p>
                  At Claraverse Inc., we engineered <strong className="text-white">Cora</strong> as a complete autonomous backbone. By connecting frontier AI reasoning models with legally binding cryptographic signature rails and instant UPI payment infrastructure, we give studio founders the freedom to focus entirely on their craft.
                </p>
              </div>

              {/* Verified Social Channels */}
              <div className="flex items-center flex-wrap gap-3 pt-2">
                <a
                  href="https://x.com/dravyafolio"
                  target="_blank"
                  rel="noopener noreferrer"
                  className="inline-flex items-center gap-1.5 bg-zinc-900 text-zinc-200 border border-zinc-700/80 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-xs"
                >
                  <span>Follow on X</span>
                  <ArrowUpRight className="w-3.5 h-3.5 text-zinc-400" />
                </a>

                <a
                  href="https://linkedin.com/in/dravyafolio"
                  target="_blank"
                  rel="noopener noreferrer"
                  className="inline-flex items-center gap-1.5 bg-zinc-900 text-zinc-200 border border-zinc-700/80 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-xs"
                >
                  <span>LinkedIn</span>
                  <ArrowUpRight className="w-3.5 h-3.5 text-zinc-400" />
                </a>

                <a
                  href="mailto:dravya.bansal@heycora.in"
                  className="inline-flex items-center gap-1.5 bg-zinc-900 text-zinc-200 border border-zinc-700/80 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-xs"
                >
                  <span>Direct Email</span>
                  <ArrowUpRight className="w-3.5 h-3.5 text-zinc-400" />
                </a>
              </div>
            </div>

          </div>

        </div>
      </section>

      {/* ─────────────────────────────────────────────────────────────
          SECTION 7: ENTERPRISE SECURITY & COMPLIANCE SUMMARY
      ───────────────────────────────────────────────────────────── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-24">
        <div className="bg-white border border-zinc-200/90 rounded-[36px] p-8 sm:p-12 shadow-[0px_20px_50px_rgba(0,0,0,0.04)] text-center space-y-8">
          <div className="max-w-[620px] mx-auto space-y-3">
            <h2 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight">
              Enterprise standards you can rely on
            </h2>
            <p className="text-zinc-600 text-sm sm:text-base font-normal leading-relaxed">
              Every document signed and payment processed through Cora meets international security benchmarks.
            </p>
          </div>

          <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 max-w-[840px] mx-auto">
            <div className="bg-zinc-50 rounded-2xl p-4 border border-zinc-200/80">
              <span className="text-[10px] font-mono text-zinc-400 uppercase block">SECURITY</span>
              <span className="text-base font-bold text-zinc-950">SOC 2 Type II</span>
              <span className="text-[10px] text-emerald-600 font-semibold block mt-0.5">COMPLIANT</span>
            </div>

            <div className="bg-zinc-50 rounded-2xl p-4 border border-zinc-200/80">
              <span className="text-[10px] font-mono text-zinc-400 uppercase block">GLOBAL</span>
              <span className="text-base font-bold text-zinc-950">ISO 27001</span>
              <span className="text-[10px] text-zinc-600 font-semibold block mt-0.5">CERTIFIED</span>
            </div>

            <div className="bg-zinc-50 rounded-2xl p-4 border border-zinc-200/80">
              <span className="text-[10px] font-mono text-zinc-400 uppercase block">LEGAL</span>
              <span className="text-base font-bold text-zinc-950">IT Act 2000</span>
              <span className="text-[10px] text-emerald-600 font-semibold block mt-0.5">E-SIGN HASH</span>
            </div>

            <div className="bg-zinc-50 rounded-2xl p-4 border border-zinc-200/80">
              <span className="text-[10px] font-mono text-zinc-400 uppercase block">TAXATION</span>
              <span className="text-base font-bold text-zinc-950">18% GST Math</span>
              <span className="text-[10px] text-zinc-600 font-semibold block mt-0.5">READY</span>
            </div>
          </div>
        </div>
      </section>

      {/* ─────────────────────────────────────────────────────────────
          SECTION 8: BOTTOM CTA BANNER
      ───────────────────────────────────────────────────────────── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        <div className="bg-zinc-950 text-white rounded-[36px] p-8 sm:p-14 text-center space-y-6 relative overflow-hidden shadow-xl">
          <div className="relative z-10 max-w-[580px] mx-auto space-y-4">
            <h2 className="font-display text-3xl sm:text-4xl font-bold tracking-tight">
              Ready to eliminate studio chaos?
            </h2>
            <p className="text-zinc-400 text-sm sm:text-base">
              Deploy your autonomous AI co-founder in under 2 minutes with zero upfront fees.
            </p>
            <div className="pt-2 flex items-center justify-center gap-3.5 flex-wrap">
              <Link
                href="/workspace/login"
                className="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl bg-white text-zinc-950 font-bold hover:bg-zinc-100 transition-all shadow-md hover:-translate-y-0.5"
              >
                <span>Launch Cora Workspace</span>
                <ArrowRight className="w-4 h-4" />
              </Link>
            </div>
          </div>
        </div>
      </section>

    </main>
  );
}
