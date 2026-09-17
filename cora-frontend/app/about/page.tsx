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
  ChevronRight,
  TrendingUp
} from 'lucide-react';
import { DynamicWeekCalendar } from '@/components/about/DynamicWeekCalendar';
import { CoreValuesSection } from '@/components/about/CoreValuesSection';
import { ScrollDrivenTimeline } from '@/components/about/ScrollDrivenTimeline';

export const metadata: Metadata = {
  title: 'Our Story & Mission — Cora AI Co-Founder',
  description: 'Why we built Cora: replacing 7 fragmented subscriptions with 1 intelligent AI Co-Founder for professional service businesses.',
  alternates: {
    canonical: 'https://heycora.in/about',
  },
  openGraph: {
    title: 'Our Story & Mission — Cora AI Co-Founder',
    description: 'Why we built Cora: replacing 7 fragmented subscriptions with 1 intelligent AI Co-Founder for professional service businesses.',
    url: 'https://heycora.in/about',
    siteName: 'Cora',
  },
};

const TEAM_MEMBERS = [
  {
    name: 'Marketing & Growth',
    role: 'Campaign & Strategy Lead',
    image: '/images/about_team_marketing_strategy.jpg',
    bg: 'bg-[#FEE2E2]', // Soft Red / Pink
    border: 'border-red-100',
  },
  {
    name: 'Sales & Partnerships',
    role: 'Enterprise Partnerships',
    image: '/images/about_team_sales_partnerships.jpg',
    bg: 'bg-[#FFEDD5]', // Soft Orange / Peach
    border: 'border-orange-100',
  },
  {
    name: 'Finance & Capital',
    role: 'Financial Operations & GST',
    image: '/images/about_team_finance_exec.jpg',
    bg: 'bg-[#E0F2FE]', // Soft Sky Blue
    border: 'border-sky-100',
  },
  {
    name: 'Creative Studio',
    role: 'Brand & Visual Architecture',
    image: '/images/about_team_creative_content.jpg',
    bg: 'bg-[#EDE9FE]', // Soft Indigo
    border: 'border-indigo-100',
  },
  {
    name: 'Dravya Bansal',
    role: 'Founder & Product Architect',
    image: '/images/about_team_founder_center.jpg',
    bg: 'bg-[#FDEDE7]', // Warm Apricot
    border: 'border-amber-200',
  },
  {
    name: 'Customer Success',
    role: 'Operations & Studio Care',
    image: '/images/about_team_operations_success.jpg',
    bg: 'bg-[#F3E8FF]', // Soft Lavender
    border: 'border-zinc-200',
  },
  {
    name: 'Engineering & Tech',
    role: 'Core Systems Architect',
    image: '/images/about_team_engineering_tech.jpg',
    bg: 'bg-[#DCFCE7]', // Soft Mint Green
    border: 'border-emerald-100',
  },
  {
    name: 'Operations & Execution',
    role: 'Workflow Infrastructure',
    image: '/images/about_team_smooth_operations.jpg',
    bg: 'bg-[#E0E7FF]', // Soft Periwinkle
    border: 'border-blue-100',
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

export default function AboutPage() {
  return (
    <main className="w-full relative pt-24 sm:pt-32 pb-24 overflow-hidden bg-white selection:bg-zinc-950 selection:text-white">
      
      {/* ─────────────────────────────────────────────────────────────
          SECTION 1: HERO STAGE (Indian Team Mosaic Grid with Ambient Pattern)
      ───────────────────────────────────────────────────────────── */}
      <section className="relative w-full overflow-hidden text-center mb-24 sm:mb-32 pt-2 pb-10">
        
        {/* Full-Bleed Ambient Background Canvas & Architectural Geometric Patterns */}
        <div className="absolute inset-0 pointer-events-none -z-10 overflow-hidden select-none">
          {/* Base Studio Warm Tint */}
          <div className="absolute inset-0 bg-gradient-to-b from-[#FBF9F5] via-[#FCFAF7] via-60% to-white" />
          
          {/* 1. Precision Linear Architectural Grid (48px x 48px) */}
          <div 
            className="absolute inset-0"
            style={{
              backgroundImage: `
                linear-gradient(to right, rgba(24, 24, 27, 0.055) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(24, 24, 27, 0.055) 1px, transparent 1px)
              `,
              backgroundSize: '48px 48px',
              maskImage: 'radial-gradient(ellipse 90% 80% at 50% 32%, black 45%, transparent 95%)',
              WebkitMaskImage: 'radial-gradient(ellipse 90% 80% at 50% 32%, black 45%, transparent 95%)',
            }}
          />

          {/* 2. Micro Dot Matrix Array (24px x 24px) */}
          <div 
            className="absolute inset-0 opacity-80"
            style={{
              backgroundImage: 'radial-gradient(circle, rgba(113, 113, 122, 0.28) 1.2px, transparent 1.2px)',
              backgroundSize: '24px 24px',
              maskImage: 'radial-gradient(ellipse 85% 70% at 50% 35%, black 40%, transparent 90%)',
              WebkitMaskImage: 'radial-gradient(ellipse 85% 70% at 50% 35%, black 40%, transparent 90%)',
            }}
          />

          {/* 3. Subtle Concentric Orbital Radar & Crosshair Rings */}
          <div className="absolute top-[28%] left-1/2 -translate-x-1/2 -translate-y-1/2 w-[1100px] h-[1100px] pointer-events-none opacity-45">
            <svg viewBox="0 0 1100 1100" className="w-full h-full stroke-zinc-300/80 fill-none">
              <circle cx="550" cy="550" r="160" strokeWidth="1" strokeDasharray="3 5" />
              <circle cx="550" cy="550" r="280" strokeWidth="1" opacity="0.6" />
              <circle cx="550" cy="550" r="420" strokeWidth="1" strokeDasharray="5 7" opacity="0.5" />
              <circle cx="550" cy="550" r="530" strokeWidth="1" opacity="0.35" />
              {/* Fine Axis Lines */}
              <line x1="550" y1="30" x2="550" y2="1070" strokeWidth="1" strokeDasharray="4 8" opacity="0.35" />
              <line x1="30" y1="550" x2="1070" y2="550" strokeWidth="1" strokeDasharray="4 8" opacity="0.35" />
            </svg>
          </div>

          {/* 4. Ambient Warm Focal Glow behind Headline */}
          <div 
            className="absolute top-[20%] left-1/2 -translate-x-1/2 -translate-y-1/2 w-[900px] h-[480px] pointer-events-none"
            style={{
              background: 'radial-gradient(ellipse at center, rgba(251, 146, 60, 0.09) 0%, rgba(244, 244, 245, 0.3) 45%, transparent 75%)',
              filter: 'blur(60px)',
            }}
          />

          {/* 5. Minimal Engineering Coordinate Crosses */}
          <div className="max-w-[1360px] mx-auto h-full relative px-6 pointer-events-none hidden md:block">
            <div className="absolute top-10 left-6 font-mono text-[10px] text-zinc-400 select-none flex items-center gap-1.5 opacity-70">
              <span className="text-zinc-600 font-bold">+</span>
              <span>CORA // TEAM_ARCHITECTURE</span>
            </div>
            <div className="absolute top-10 right-6 font-mono text-[10px] text-zinc-400 select-none flex items-center gap-1.5 opacity-70">
              <span>EST. 2024 // AUTONOMOUS</span>
              <span className="text-zinc-600 font-bold">+</span>
            </div>
          </div>

          {/* 6. Silky Bottom Fade to pure white */}
          <div className="absolute inset-x-0 bottom-0 h-36 sm:h-52 bg-gradient-to-t from-white via-white/85 to-transparent pointer-events-none" />
        </div>

        {/* Inner Content Container */}
        <div className="relative w-full max-w-[1400px] mx-auto px-3 xs:px-4 sm:px-6">

        {/* Eyebrow (Strict Cora Design System Token: Inter font-sans semibold pill with active pulse) */}
        <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-white/90 backdrop-blur-md rounded-full text-xs font-semibold text-zinc-800 border border-zinc-200/80 mb-4 shadow-2xs">
          <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
          <span>Maximize Human Productivity</span>
        </div>

        {/* Main Headline (Strict Cora Design System Token: font-display tracking-tight with signature gradient) */}
        <h1 className="font-display text-4xl xs:text-5xl sm:text-6xl lg:text-[72px] font-bold text-zinc-950 leading-[1.08] tracking-[-0.03em] max-w-[960px] mx-auto mb-6 sm:mb-8">
          We are <span className="bg-gradient-to-r from-zinc-950 via-zinc-700 to-zinc-400 bg-clip-text text-transparent">CORA</span>.
        </h1>

        {/* Hero CTA Button (Strict Cora Design System Token: rounded-xl font-semibold text-sm) */}
        <div className="flex items-center justify-center mb-12 sm:mb-16">
          <a
            href="#manifesto"
            className="inline-flex items-center gap-2 bg-zinc-950 text-white hover:bg-zinc-800 px-6 sm:px-7 py-3 sm:py-3.5 rounded-xl text-xs sm:text-sm font-semibold tracking-tight shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all group cursor-pointer"
          >
            <Play className="w-3.5 h-3.5 fill-white text-white group-hover:scale-110 transition-transform" />
            <span>Learn more about us</span>
          </a>
        </div>

        {/* ── 5-Column Floating Team Mosaic Grid (Single Formation on Mobile & Desktop) ── */}
        <div className="relative w-full overflow-hidden max-w-[1240px] mx-auto pt-1 pb-4">
          
          <div className="relative z-10 grid grid-cols-5 gap-1.5 xs:gap-2.5 sm:gap-4 lg:gap-5 items-center justify-center">
            
            {/* Column 1: Far Left (Top: Marketing, Bottom: Sales) */}
            <div className="flex flex-col gap-1.5 xs:gap-2.5 sm:gap-4">
              {/* Card 1 Top: Marketing */}
              <div className="relative rounded-xl xs:rounded-2xl sm:rounded-[32px] bg-[#FDE8EA] aspect-[4/4.7] overflow-hidden shadow-2xs border border-red-100/80 group">
                <Image
                  src="/images/about_team_marketing_strategy.jpg"
                  alt="Marketing & Strategy Lead"
                  fill
                  sizes="(max-width: 640px) 20vw, 240px"
                  className="object-cover object-center group-hover:scale-105 transition-transform duration-500 filter contrast-[1.02]"
                />
                <div className="absolute inset-x-0 bottom-0 p-2 sm:p-2.5 bg-gradient-to-t from-black/80 via-black/35 to-transparent flex items-center justify-between text-white">
                  <span className="text-[10px] sm:text-[11px] font-semibold tracking-tight">Marketing</span>
                  <span className="text-[9px] sm:text-[10px] font-mono text-zinc-300">Strategy</span>
                </div>
              </div>

              {/* Card 1 Bottom: Sales */}
              <div className="relative rounded-xl xs:rounded-2xl sm:rounded-[32px] bg-[#FDF2E9] aspect-[4/4.7] overflow-hidden shadow-2xs border border-orange-100/80 group">
                <Image
                  src="/images/about_team_sales_partnerships.jpg"
                  alt="Sales & Partnerships"
                  fill
                  sizes="(max-width: 640px) 20vw, 240px"
                  className="object-cover object-center group-hover:scale-105 transition-transform duration-500 filter contrast-[1.02]"
                />
                <div className="absolute inset-x-0 bottom-0 p-2 sm:p-2.5 bg-gradient-to-t from-black/80 via-black/35 to-transparent flex items-center justify-between text-white">
                  <span className="text-[10px] sm:text-[11px] font-semibold tracking-tight">Sales</span>
                  <span className="text-[9px] sm:text-[10px] font-mono text-zinc-300">Growth</span>
                </div>
              </div>
            </div>

            {/* Column 2: Mid Left (Top: Finance, Bottom: Creative Content) */}
            <div className="flex flex-col gap-1.5 xs:gap-2.5 sm:gap-4">
              {/* Card 2 Top: Finance */}
              <div className="relative rounded-xl xs:rounded-2xl sm:rounded-[32px] bg-[#E0F0FA] aspect-[4/4.7] overflow-hidden shadow-2xs border border-sky-100/80 group">
                <Image
                  src="/images/about_team_finance_exec.jpg"
                  alt="Finance & Operations Lead"
                  fill
                  sizes="(max-width: 640px) 20vw, 240px"
                  className="object-cover object-center group-hover:scale-105 transition-transform duration-500 filter contrast-[1.02]"
                />
                <div className="absolute inset-x-0 bottom-0 p-2 sm:p-2.5 bg-gradient-to-t from-black/80 via-black/35 to-transparent flex items-center justify-between text-white">
                  <span className="text-[10px] sm:text-[11px] font-semibold tracking-tight">Finance</span>
                  <span className="text-[9px] sm:text-[10px] font-mono text-zinc-300">Ops</span>
                </div>
              </div>

              {/* Card 2 Bottom: Creative & Content */}
              <div className="relative rounded-xl xs:rounded-2xl sm:rounded-[32px] bg-[#EDE9FE] aspect-[4/4.7] overflow-hidden shadow-2xs border border-indigo-100/80 group">
                <Image
                  src="/images/about_team_creative_content.jpg"
                  alt="Creative & Content Studio"
                  fill
                  sizes="(max-width: 640px) 20vw, 240px"
                  className="object-cover object-center group-hover:scale-105 transition-transform duration-500 filter contrast-[1.02]"
                />
                <div className="absolute inset-x-0 bottom-0 p-2 sm:p-2.5 bg-gradient-to-t from-black/80 via-black/35 to-transparent flex items-center justify-between text-white">
                  <span className="text-[10px] sm:text-[11px] font-semibold tracking-tight">Creative</span>
                  <span className="text-[9px] sm:text-[10px] font-mono text-zinc-300">Studio</span>
                </div>
              </div>
            </div>

            {/* Column 3: Center Leader Photo (Founder) */}
            <div className="col-span-1 flex justify-center">
              <div className="relative w-full rounded-2xl xs:rounded-3xl sm:rounded-[36px] bg-[#FDEDE7] aspect-[4/5.1] overflow-hidden shadow-xs border border-amber-200/80 group">
                <Image
                  src="/images/about_team_founder_center.jpg"
                  alt="Dravya Bansal — Founder of Cora"
                  fill
                  sizes="(max-width: 640px) 25vw, 320px"
                  className="object-cover object-top group-hover:scale-105 transition-transform duration-500 filter contrast-[1.03]"
                />
                <div className="absolute inset-x-0 bottom-0 p-2.5 sm:p-3.5 bg-gradient-to-t from-black/80 via-black/35 to-transparent flex items-center justify-between text-white">
                  <span className="text-xs sm:text-sm font-bold tracking-tight">Dravya</span>
                  <span className="text-[10px] sm:text-[11px] font-mono text-amber-300">Founder</span>
                </div>
              </div>
            </div>

            {/* Columns 4 & 5: Right Side (Top: 2 Cards Side-by-Side, Bottom: 1 Wide Spanning Card) */}
            <div className="col-span-2 flex flex-col gap-1.5 xs:gap-2.5 sm:gap-4">
              
              {/* Top Row: Customer Success & Engineering */}
              <div className="grid grid-cols-2 gap-1.5 xs:gap-2.5 sm:gap-4">
                {/* Top Left: Customer Success */}
                <div className="relative rounded-xl xs:rounded-2xl sm:rounded-[32px] bg-[#EFE8F9] aspect-[4/4.7] overflow-hidden shadow-2xs border border-purple-100/80 group">
                  <Image
                    src="/images/about_team_operations_success.jpg"
                    alt="Customer Success & Operations"
                    fill
                    sizes="(max-width: 640px) 20vw, 240px"
                    className="object-cover object-center group-hover:scale-105 transition-transform duration-500 filter contrast-[1.02]"
                  />
                  <div className="absolute inset-x-0 bottom-0 p-2 sm:p-2.5 bg-gradient-to-t from-black/80 via-black/35 to-transparent flex items-center justify-between text-white">
                    <span className="text-[10px] sm:text-[11px] font-semibold tracking-tight">Success</span>
                    <span className="text-[9px] sm:text-[10px] font-mono text-zinc-300">Ops</span>
                  </div>
                </div>

                {/* Top Right: Engineering */}
                <div className="relative rounded-xl xs:rounded-2xl sm:rounded-[32px] bg-[#EAF7F0] aspect-[4/4.7] overflow-hidden shadow-2xs border border-emerald-100/80 group">
                  <Image
                    src="/images/about_team_engineering_tech.jpg"
                    alt="Engineering & Systems Architecture"
                    fill
                    sizes="(max-width: 640px) 20vw, 240px"
                    className="object-cover object-center group-hover:scale-105 transition-transform duration-500 filter contrast-[1.02]"
                  />
                  <div className="absolute inset-x-0 bottom-0 p-2 sm:p-2.5 bg-gradient-to-t from-black/80 via-black/35 to-transparent flex items-center justify-between text-white">
                    <span className="text-[10px] sm:text-[11px] font-semibold tracking-tight">Engineering</span>
                    <span className="text-[9px] sm:text-[10px] font-mono text-zinc-300">Systems</span>
                  </div>
                </div>
              </div>

              {/* Bottom Row: 1 Wide Spanning Card (Operations & Workflows) */}
              <div className="relative w-full rounded-xl xs:rounded-2xl sm:rounded-[32px] bg-[#FAF1E8] aspect-[8.3/4.7] overflow-hidden shadow-2xs border border-amber-100/80 group">
                <Image
                  src="/images/about_team_smooth_operations.jpg"
                  alt="Operations & Workflows"
                  fill
                  sizes="(max-width: 640px) 40vw, 480px"
                  className="object-cover object-center group-hover:scale-103 transition-transform duration-500 filter contrast-[1.02]"
                />
                <div className="absolute inset-x-0 bottom-0 p-2.5 sm:p-3 bg-gradient-to-t from-black/80 via-black/35 to-transparent flex items-center justify-between text-white">
                  <span className="text-[10px] sm:text-[11px] font-semibold tracking-tight">Operations &amp; Workflows</span>
                  <span className="text-[9px] sm:text-[10px] font-mono text-zinc-300">Execution</span>
                </div>
              </div>

            </div>

          </div>

        </div>

        </div>

      </section>

      {/* ─────────────────────────────────────────────────────────────
          SECTION 2: OUR MISSION (Active Voice & High-Conviction Copy)
      ───────────────────────────────────────────────────────────── */}
      <section className="relative w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-28 sm:mb-40 text-center">
        
        {/* Eyebrow (Monochromatic Token) */}
        <div className="inline-flex items-center gap-2 px-3.5 py-1 bg-white/90 backdrop-blur-md rounded-full text-[11px] font-mono font-bold tracking-[0.16em] uppercase text-zinc-700 border border-zinc-200/90 mb-4 shadow-2xs">
          <span>THE MISSION</span>
        </div>

        {/* Main Title (Signature Cora Heading Gradient) */}
        <div>
          <h2 className="font-display text-5xl xs:text-6xl sm:text-7xl lg:text-[80px] font-bold leading-[1.04] tracking-[-0.035em] bg-gradient-to-r from-zinc-950 via-zinc-700 to-zinc-400 bg-clip-text text-transparent inline-block mb-4 sm:mb-5">
            Our mission.
          </h2>
        </div>

        {/* Active Voice Subtitle */}
        <h3 className="font-display text-2xl xs:text-3xl sm:text-4xl font-bold text-zinc-950 tracking-tight mb-3 sm:mb-4 max-w-[700px] mx-auto">
          Give creative founders their time back.
        </h3>

        {/* Concise Single-Sentence Oneliner */}
        <p className="text-zinc-600 text-base sm:text-lg leading-relaxed max-w-[580px] mx-auto mb-8 font-normal">
          Cora replaces 7 fragmented subscriptions with 1 intelligent AI co-founder—automating discovery intake, 18% GST billing, and digital contracts on autopilot.
        </p>

        {/* CTA Button */}
        <div className="flex items-center justify-center mb-10 sm:mb-14">
          <a
            href="#manifesto"
            className="inline-flex items-center gap-2 bg-zinc-950 text-white hover:bg-zinc-800 border border-zinc-800 px-7 py-3.5 rounded-xl text-xs sm:text-sm font-semibold tracking-tight shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all group cursor-pointer"
          >
            <span>Read the founder manifesto</span>
            <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:text-white group-hover:translate-x-0.5 transition-all" />
          </a>
        </div>

        {/* ── Architectural Circular Portal (Background Removed & Seamlessly Blended) ── */}
        <div className="relative w-full max-w-[960px] mx-auto aspect-[16/9] mb-12 sm:mb-16 overflow-hidden">
          <Image
            src="/images/about_mission_portal_clean.png"
            alt="Cora Mission Horizon Portal"
            fill
            priority
            sizes="(max-width: 1240px) 100vw, 960px"
            className="object-contain object-center filter contrast-102"
          />
          {/* Subtle multi-directional gradients for seamless canvas integration */}
          <div className="absolute inset-x-0 top-0 h-14 bg-gradient-to-b from-white via-white/20 to-transparent pointer-events-none" />
          <div className="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-white via-white/60 to-transparent pointer-events-none" />
          <div className="absolute inset-y-0 left-0 w-12 bg-gradient-to-r from-white via-white/30 to-transparent pointer-events-none" />
          <div className="absolute inset-y-0 right-0 w-12 bg-gradient-to-l from-white via-white/30 to-transparent pointer-events-none" />
        </div>

        {/* ── Agency & Studio Sectors Powered by Cora ── */}
        <div className="pt-2 max-w-[960px] mx-auto">
          <span className="text-[10px] sm:text-[11px] font-mono font-bold uppercase tracking-widest text-zinc-400 block mb-4 sm:mb-5">
            ENGINEERED FOR MODERN SERVICE BUSINESSES
          </span>
          <div className="flex items-center justify-center flex-wrap gap-2.5 sm:gap-3 text-zinc-700">
            {[
              'Creative & Design Studios',
              'Performance Marketing Agencies',
              'Software & Dev Shops',
              'Commercial Production Houses',
              'Architectural 3D Studios',
              'Brand & Media Consultancies',
            ].map((sector, i) => (
              <span
                key={i}
                className="px-3.5 py-1.5 rounded-full bg-zinc-50 border border-zinc-200/80 text-xs sm:text-sm font-medium text-zinc-800 hover:bg-zinc-100 hover:border-zinc-300 transition-colors shadow-2xs"
              >
                {sector}
              </span>
            ))}
          </div>
        </div>

      </section>

      {/* ─────────────────────────────────────────────────────────────
          SECTION 3: OUR MOTTO (Dynamic Live Week Calendar & Telemetry)
      ───────────────────────────────────────────────────────────── */}
      <section className="w-full max-w-[1100px] mx-auto px-4 sm:px-6 mb-28 sm:mb-40">
        <DynamicWeekCalendar />
      </section>

      {/* ─────────────────────────────────────────────────────────────
          SECTION 4: OUR CORE VALUES (Edge-to-Edge Experience)
      ───────────────────────────────────────────────────────────── */}
      <section className="w-full relative overflow-hidden mb-28 sm:mb-40">
        <CoreValuesSection />
      </section>

      {/* ─────────────────────────────────────────────────────────────
          SECTION 5: OUR JOURNEY (Scroll-Driven Connected Timeline)
      ───────────────────────────────────────────────────────────── */}
      <section className="w-full max-w-[1100px] mx-auto px-4 sm:px-6 mb-28 sm:mb-40">
        <ScrollDrivenTimeline />
      </section>

      {/* ─────────────────────────────────────────────────────────────
          SECTION 6: FOUNDER MANIFESTO (Clean Canvas with Dravya Cutout)
      ───────────────────────────────────────────────────────────── */}
      <section className="w-full max-w-[1100px] mx-auto px-4 sm:px-6 mb-24 sm:mb-36">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 items-center">
          
          {/* Left Column: Dravya Bansal Cutout & Attribution */}
          <div className="lg:col-span-5 flex flex-col items-center lg:items-start text-center lg:text-left">
            <div className="relative w-[280px] xs:w-[320px] sm:w-[360px] aspect-[4/4.4] overflow-hidden">
              <Image
                src="/images/dravya_bansal_cutout_clean.png"
                alt="Dravya Bansal — Founder of Cora"
                fill
                priority
                sizes="(max-width: 768px) 320px, 380px"
                className="object-contain object-bottom filter contrast-[1.02]"
              />
              {/* Soft bottom fade to blend with white canvas */}
              <div className="absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-white via-white/80 to-transparent pointer-events-none" />
            </div>

            <div className="mt-2 space-y-0.5">
              <h3 className="font-display text-lg sm:text-xl font-bold text-zinc-950 tracking-tight">
                Dravya Bansal
              </h3>
              <p className="text-[11px] font-mono font-medium uppercase text-zinc-500 tracking-wider">
                CO-FOUNDER &amp; PRODUCT ARCHITECT
              </p>
            </div>
          </div>

          {/* Right Column: Founder Manifesto & Direct CTAs */}
          <div className="lg:col-span-7 space-y-5 text-center lg:text-left">
            
            {/* Eyebrow Pill */}
            <div>
              <div className="inline-flex items-center gap-2 px-3.5 py-1 bg-white rounded-full text-[11px] font-mono font-medium tracking-[0.16em] uppercase text-zinc-600 border border-zinc-200/90 shadow-2xs">
                <span>FOUNDER MANIFESTO</span>
              </div>
            </div>

            {/* Main Headline */}
            <div>
              <h2 className="font-display text-2xl xs:text-3xl sm:text-[32px] lg:text-[36px] font-bold leading-[1.25] tracking-tight text-zinc-950 max-w-[620px]">
                &ldquo;Win clients. Deliver great work. Get paid. <span className="bg-gradient-to-r from-zinc-950 via-zinc-700 to-zinc-400 bg-clip-text text-transparent inline-block">Cora keeps it all moving.&rdquo;</span>
              </h2>
            </div>

            {/* Description */}
            <p className="text-sm sm:text-base text-zinc-600 font-normal leading-relaxed max-w-[560px]">
              Run leads, onboarding, projects, approvals, reports and GST invoices from one connected workspace—built for marketing, design and development agencies.
            </p>

            {/* Action Buttons */}
            <div className="flex items-center justify-center lg:justify-start flex-wrap gap-3 pt-2">
              <Link
                href="/workspace/login"
                className="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-zinc-950 text-white text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-sm group"
              >
                <span>Start Your Agency Workspace</span>
                <ArrowRight className="w-4 h-4 group-hover:translate-x-0.5 transition-transform" />
              </Link>

              <a
                href="https://x.com/dravyafolio"
                target="_blank"
                rel="noopener noreferrer"
                className="inline-flex items-center gap-1.5 px-4 py-3 rounded-xl border border-zinc-200/90 bg-white text-zinc-700 text-xs sm:text-sm font-semibold hover:bg-zinc-50 hover:border-zinc-300 transition-all shadow-2xs"
              >
                <span>Follow on X</span>
                <ArrowUpRight className="w-3.5 h-3.5 text-zinc-400" />
              </a>

              <a
                href="https://linkedin.com/in/dravyafolio"
                target="_blank"
                rel="noopener noreferrer"
                className="inline-flex items-center gap-1.5 px-4 py-3 rounded-xl border border-zinc-200/90 bg-white text-zinc-700 text-xs sm:text-sm font-semibold hover:bg-zinc-50 hover:border-zinc-300 transition-all shadow-2xs"
              >
                <span>LinkedIn</span>
                <ArrowUpRight className="w-3.5 h-3.5 text-zinc-400" />
              </a>
            </div>

          </div>

        </div>
      </section>

    </main>
  );
}

