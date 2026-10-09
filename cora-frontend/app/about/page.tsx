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
  TrendingUp,
  BarChart3,
  Camera,
  Code2,
  Settings,
  Clock
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
    name: 'Creative & Design',
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
    role: 'Client Operations & Agency Care',
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
    desc: 'Software should not require constant manual babysitting. We engineer systems that trigger client proposals, contracts, GST invoices, and scope approvals automatically.'
  },
  {
    title: 'Zero Bloat & Monochromatic Focus',
    desc: 'We uphold a strict Notion and Linear-inspired monochromatic design ethos. No distracting colorful clutter — only high-velocity tools engineered for agency operators.'
  },
  {
    title: 'Bank-Grade Data Sovereignty',
    desc: 'Your agency client data, contracts, and financial records belong solely to you. We maintain immutable SHA-256 cryptographic audit trails and SOC-2 security protocols.'
  },
  {
    title: 'Founder-First Accessibility',
    desc: 'Every ambitious agency founder deserves enterprise-grade operating power from day one. That is why our core platform remains accessible without punitive high paywalls.'
  }
];

export default function AboutPage() {
  return (
    <main className="w-full relative pt-24 sm:pt-28 lg:pt-32 pb-24 overflow-hidden bg-white selection:bg-zinc-950 selection:text-white">
      
      {/* ─────────────────────────────────────────────────────────────
          SECTION 1: HERO STAGE (Bento Team Mosaic Fitted Within 90vh)
      ───────────────────────────────────────────────────────────── */}
      <section className="relative w-full pt-4 sm:pt-8 lg:pt-12 pb-16 sm:pb-24 lg:pb-28 mb-16 sm:mb-24 lg:mb-32 min-h-[calc(90vh-90px)] flex flex-col justify-center">
        
        {/* Full-Bleed Ambient Background Canvas & Soft Elegant Wave Geometry */}
        <div className="absolute inset-0 pointer-events-none -z-10 overflow-hidden select-none">
          {/* Studio Clean Monochromatic Canvas Tint */}
          <div className="absolute inset-0 bg-gradient-to-b from-zinc-50/80 via-zinc-50/30 to-white" />
          
          {/* Subtle Organic Background Curving Waves */}
          <svg
            className="absolute inset-0 w-full h-full stroke-zinc-200/70 fill-none opacity-60 pointer-events-none"
            viewBox="0 0 1440 900"
            preserveAspectRatio="none"
          >
            <path
              d="M-100,750 C300,820 600,620 900,780 C1200,940 1400,680 1600,750"
              strokeWidth="1.5"
            />
            <path
              d="M-50,620 C350,700 700,500 1050,680 C1300,800 1500,580 1650,640"
              strokeWidth="1"
              strokeDasharray="4 6"
            />
            <path
              d="M1000,100 C1200,200 1350,150 1500,280 C1600,360 1700,300 1800,420"
              strokeWidth="1.5"
            />
          </svg>

          {/* Micro Dot Matrix Array (24px x 24px) for subtle texture */}
          <div 
            className="absolute inset-0 opacity-40"
            style={{
              backgroundImage: 'radial-gradient(circle, rgba(113, 113, 122, 0.2) 1.2px, transparent 1.2px)',
              backgroundSize: '24px 24px',
              maskImage: 'radial-gradient(ellipse 90% 75% at 50% 40%, black 30%, transparent 90%)',
              WebkitMaskImage: 'radial-gradient(ellipse 90% 75% at 50% 40%, black 30%, transparent 90%)',
            }}
          />

          {/* Smooth Bottom Fade to pure white transition */}
          <div className="absolute inset-x-0 bottom-0 h-16 sm:h-28 bg-gradient-to-t from-white via-white/80 to-transparent pointer-events-none" />
        </div>

        {/* Hero Content Container */}
        <div className="relative w-full max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8 flex-1 flex flex-col justify-center">
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 lg:gap-6 xl:gap-10 items-center">
            
            {/* ── Left Column: Story Copy, Single CTA & 3-Metric Stats Bar (5 Cols) ── */}
            <div className="lg:col-span-5 text-left flex flex-col justify-center">
              
              {/* Eyebrow */}
              <div className="flex items-center gap-2 mb-2 sm:mb-3">
                <span className="text-[10px] sm:text-[11px] font-mono font-bold tracking-[0.2em] text-zinc-500 uppercase">
                  ABOUT CORA
                </span>
                <span className="w-6 sm:w-8 h-[1px] bg-zinc-300" />
              </div>

              {/* Main Headline */}
              <h1 className="font-display text-2xl xs:text-3xl sm:text-4xl lg:text-[40px] xl:text-[46px] font-bold text-zinc-950 leading-[1.08] tracking-[-0.035em]">
                Built by people <br />
                <span className="bg-gradient-to-r from-zinc-950 via-zinc-700 to-zinc-400 bg-clip-text text-transparent">who understand</span> <br />
                how agencies run
              </h1>

              {/* Subheading / Description */}
              <p className="text-zinc-600 text-xs sm:text-[13.5px] lg:text-[14.5px] leading-relaxed font-normal mt-2.5 sm:mt-3.5 mb-4 sm:mb-5 max-w-[420px]">
                We&apos;re a team of operators, builders and creatives on a mission to create the first AI-native workspace for modern agencies.
              </p>

              {/* Single Action CTA Button (Takes user to Our Mission section) */}
              <div className="flex items-center gap-3 mb-4 sm:mb-6">
                <a
                  href="#mission"
                  className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 sm:px-6 py-2.5 sm:py-3 rounded-xl bg-zinc-950 text-white text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-xs group cursor-pointer"
                >
                  <span>Our mission</span>
                  <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:text-white group-hover:translate-x-0.5 transition-all" />
                </a>
              </div>

              {/* Bottom 3-Metric Stats Row */}
              <div className="grid grid-cols-3 gap-2 sm:gap-3.5 pt-3 sm:pt-4 border-t border-zinc-200 max-w-[440px]">
                <div>
                  <div className="font-display font-bold text-xs sm:text-sm lg:text-base text-zinc-950 tracking-tight">
                    1 mission
                  </div>
                  <p className="text-[9.5px] sm:text-[11px] text-zinc-500 font-normal leading-snug mt-0.5">
                    A brighter future for agency work
                  </p>
                </div>
                <div>
                  <div className="font-display font-bold text-xs sm:text-sm lg:text-base text-zinc-950 tracking-tight">
                    7+
                  </div>
                  <p className="text-[9.5px] sm:text-[11px] text-zinc-500 font-normal leading-snug mt-0.5">
                    Teams, one shared mindset
                  </p>
                </div>
                <div>
                  <div className="font-display font-bold text-xs sm:text-sm lg:text-base text-zinc-950 tracking-tight">
                    100%
                  </div>
                  <p className="text-[9.5px] sm:text-[11px] text-zinc-500 font-normal leading-snug mt-0.5">
                    Built for modern agencies
                  </p>
                </div>
              </div>

            </div>

            {/* ── Right Column: Authentic Randomized Organic Bento Collage (7 Cols) ── */}
            <div id="team-grid" className="lg:col-span-7 relative pt-3 sm:pt-4 lg:pt-5">
              
              <div className="grid grid-cols-3 gap-2 sm:gap-3 lg:gap-3.5 items-center">
                
                {/* ── Column 1 (Left - 3 stacked organically rotated & offset cards) ── */}
                <div className="flex flex-col gap-2 sm:gap-3 lg:gap-3.5">
                  
                  {/* Card 1: Marketing (Tilted counter-clockwise with top-left offset) */}
                  <div className="relative rounded-[18px] sm:rounded-[24px] overflow-hidden aspect-[4/3.7] bg-[#FDE8EA] shadow-[0_10px_24px_-4px_rgba(0,0,0,0.11),0_3px_8px_rgba(0,0,0,0.04)] border-[3px] sm:border-[4.5px] border-white group -rotate-3 -translate-y-1.5 -translate-x-0.5 hover:rotate-0 hover:translate-y-0 transition-all duration-300">
                    <Image
                      src="/images/about_team_marketing_strategy.jpg"
                      alt="Marketing"
                      fill
                      sizes="(max-width: 640px) 30vw, 240px"
                      className="object-cover object-center group-hover:scale-105 transition-transform duration-500 filter contrast-[1.02]"
                    />
                    <div className="absolute bottom-1.5 right-1.5 sm:bottom-2 sm:right-2 px-2 sm:px-2.5 py-0.5 rounded-full bg-white/95 backdrop-blur-md text-zinc-900 border border-zinc-200/80 shadow-xs flex items-center gap-1 text-[8.5px] sm:text-[10px] font-semibold whitespace-nowrap">
                      <TrendingUp className="w-2.5 h-2.5 sm:w-3 sm:h-3 text-zinc-800 stroke-[2.2]" />
                      <span>Marketing</span>
                    </div>
                  </div>

                  {/* Card 2: Finance (Tilted clockwise with rightward offset) */}
                  <div className="relative rounded-[18px] sm:rounded-[24px] overflow-hidden aspect-[4/3.7] bg-[#E0F0FA] shadow-[0_10px_24px_-4px_rgba(0,0,0,0.11),0_3px_8px_rgba(0,0,0,0.04)] border-[3px] sm:border-[4.5px] border-white group rotate-2 translate-x-1 hover:rotate-0 transition-all duration-300">
                    <Image
                      src="/images/about_team_finance_exec.jpg"
                      alt="Finance"
                      fill
                      sizes="(max-width: 640px) 30vw, 240px"
                      className="object-cover object-center group-hover:scale-105 transition-transform duration-500 filter contrast-[1.02]"
                    />
                    <div className="absolute bottom-1.5 left-1/2 -translate-x-1/2 sm:bottom-2 px-2 sm:px-2.5 py-0.5 rounded-full bg-white/95 backdrop-blur-md text-zinc-900 border border-zinc-200/80 shadow-xs flex items-center gap-1 text-[8.5px] sm:text-[10px] font-semibold whitespace-nowrap">
                      <BarChart3 className="w-2.5 h-2.5 sm:w-3 sm:h-3 text-zinc-800 stroke-[2.2]" />
                      <span>Finance</span>
                    </div>
                  </div>

                  {/* Card 3: Creative (Tilted counter-clockwise with downward offset) */}
                  <div className="relative rounded-[18px] sm:rounded-[24px] overflow-hidden aspect-[4/3.7] bg-[#EDE9FE] shadow-[0_10px_24px_-4px_rgba(0,0,0,0.11),0_3px_8px_rgba(0,0,0,0.04)] border-[3px] sm:border-[4.5px] border-white group -rotate-2 translate-y-1.5 -translate-x-0.5 hover:rotate-0 transition-all duration-300">
                    <Image
                      src="/images/about_team_creative_content.jpg"
                      alt="Creative"
                      fill
                      sizes="(max-width: 640px) 30vw, 240px"
                      className="object-cover object-center group-hover:scale-105 transition-transform duration-500 filter contrast-[1.02]"
                    />
                    <div className="absolute bottom-1.5 right-1.5 sm:bottom-2 sm:right-2 px-2 sm:px-2.5 py-0.5 rounded-full bg-white/95 backdrop-blur-md text-zinc-900 border border-zinc-200/80 shadow-xs flex items-center gap-1 text-[8.5px] sm:text-[10px] font-semibold whitespace-nowrap">
                      <Camera className="w-2.5 h-2.5 sm:w-3 sm:h-3 text-zinc-800 stroke-[2.2]" />
                      <span>Creative</span>
                    </div>
                  </div>

                </div>

                {/* ── Column 2 (Center - Dominant Founder Card + Sales) ── */}
                <div className="flex flex-col gap-2 sm:gap-3 lg:gap-3.5">
                  
                  {/* Card Center Top: Dravya - Founder (Hero Centerpiece with Sparks) */}
                  <div className="relative rounded-[22px] sm:rounded-[28px] overflow-hidden aspect-[3/3.8] sm:aspect-[3/3.9] bg-[#FDEDE7] shadow-[0_14px_32px_-4px_rgba(0,0,0,0.15),0_5px_12px_rgba(0,0,0,0.05)] border-[4px] sm:border-[5.5px] border-white group z-10 scale-[1.02] hover:scale-105 transition-all duration-300">
                    
                    {/* Hand-drawn celebratory doodle sparks at top-right */}
                    <div className="absolute top-1.5 right-1.5 sm:top-2.5 sm:right-2.5 pointer-events-none select-none z-20">
                      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" className="stroke-zinc-800 stroke-[2.2] stroke-linecap-round">
                        <line x1="6" y1="6" x2="2" y2="2" />
                        <line x1="12" y1="5" x2="12" y2="1" />
                        <line x1="18" y1="6" x2="22" y2="2" />
                      </svg>
                    </div>

                    <Image
                      src="/images/about_team_founder_center.jpg"
                      alt="Dravya — Founder"
                      fill
                      sizes="(max-width: 640px) 35vw, 320px"
                      className="object-cover object-top group-hover:scale-105 transition-transform duration-500 filter contrast-[1.03]"
                    />

                    {/* Gradient Overlay & Bottom Label */}
                    <div className="absolute inset-x-0 bottom-0 p-2 sm:p-3.5 bg-gradient-to-t from-black/85 via-black/40 to-transparent flex flex-col justify-end text-white">
                      <div className="font-display text-xs sm:text-sm lg:text-base font-bold tracking-tight leading-tight text-white">
                        Dravya
                      </div>
                      <div className="text-[8.5px] sm:text-[10.5px] text-zinc-300 font-medium font-sans mt-0.5">
                        Founder
                      </div>
                    </div>
                  </div>

                  {/* Card Center Bottom: Sales (Slightly tilted clockwise) */}
                  <div className="relative rounded-[18px] sm:rounded-[24px] overflow-hidden aspect-[4/3.7] bg-[#FDF2E9] shadow-[0_10px_24px_-4px_rgba(0,0,0,0.11),0_3px_8px_rgba(0,0,0,0.04)] border-[3px] sm:border-[4.5px] border-white group rotate-1 translate-y-1 hover:rotate-0 transition-all duration-300">
                    <Image
                      src="/images/about_team_sales_partnerships.jpg"
                      alt="Sales"
                      fill
                      sizes="(max-width: 640px) 30vw, 240px"
                      className="object-cover object-center group-hover:scale-105 transition-transform duration-500 filter contrast-[1.02]"
                    />
                    <div className="absolute bottom-1.5 left-1/2 -translate-x-1/2 sm:bottom-2 px-2 sm:px-2.5 py-0.5 rounded-full bg-[#EFF6FF] text-[#1D4ED8] border border-blue-200/60 shadow-xs flex items-center gap-1 text-[8.5px] sm:text-[10px] font-semibold whitespace-nowrap">
                      <Users className="w-2.5 h-2.5 sm:w-3 sm:h-3 text-[#1D4ED8] stroke-[2.2]" />
                      <span>Sales</span>
                    </div>
                  </div>

                </div>

                {/* ── Column 3 (Right - 3 stacked organically rotated cards + Hand-drawn note) ── */}
                <div className="flex flex-col gap-2 sm:gap-3 lg:gap-3.5 relative">
                  
                  {/* Card 1: Operations (Tilted clockwise with top-right offset) */}
                  <div className="relative rounded-[18px] sm:rounded-[24px] overflow-hidden aspect-[4/3.7] bg-[#EFE8F9] shadow-[0_10px_24px_-4px_rgba(0,0,0,0.11),0_3px_8px_rgba(0,0,0,0.04)] border-[3px] sm:border-[4.5px] border-white group rotate-3 -translate-y-1.5 translate-x-0.5 hover:rotate-0 hover:translate-y-0 transition-all duration-300">
                    <Image
                      src="/images/about_team_operations_success.jpg"
                      alt="Operations"
                      fill
                      sizes="(max-width: 640px) 30vw, 240px"
                      className="object-cover object-center group-hover:scale-105 transition-transform duration-500 filter contrast-[1.02]"
                    />
                    <div className="absolute bottom-1.5 left-1/2 -translate-x-1/2 sm:bottom-2 px-2 sm:px-2.5 py-0.5 rounded-full bg-[#EEF2FF] text-[#4338CA] border border-indigo-200/60 shadow-xs flex items-center gap-1 text-[8.5px] sm:text-[10px] font-semibold whitespace-nowrap">
                      <Settings className="w-2.5 h-2.5 sm:w-3 sm:h-3 text-[#4338CA] stroke-[2.2]" />
                      <span>Operations</span>
                    </div>
                  </div>

                  {/* Card 2: Engineering (Tilted counter-clockwise with rightward offset) */}
                  <div className="relative rounded-[18px] sm:rounded-[24px] overflow-hidden aspect-[4/3.7] bg-[#EAF7F0] shadow-[0_10px_24px_-4px_rgba(0,0,0,0.11),0_3px_8px_rgba(0,0,0,0.04)] border-[3px] sm:border-[4.5px] border-white group -rotate-2 translate-x-1 hover:rotate-0 transition-all duration-300">
                    <Image
                      src="/images/about_team_engineering_tech.jpg"
                      alt="Engineering"
                      fill
                      sizes="(max-width: 640px) 30vw, 240px"
                      className="object-cover object-center group-hover:scale-105 transition-transform duration-500 filter contrast-[1.02]"
                    />
                    <div className="absolute bottom-1.5 left-1/2 -translate-x-1/2 sm:bottom-2 px-2 sm:px-2.5 py-0.5 rounded-full bg-[#ECFDF5] text-[#047857] border border-emerald-200/60 shadow-xs flex items-center gap-1 text-[8.5px] sm:text-[10px] font-semibold whitespace-nowrap">
                      <Code2 className="w-2.5 h-2.5 sm:w-3 sm:h-3 text-[#047857] stroke-[2.2]" />
                      <span>Engineering</span>
                    </div>
                  </div>

                  {/* Card 3: Workflows (Tilted clockwise with downward offset) */}
                  <div className="relative rounded-[18px] sm:rounded-[24px] overflow-hidden aspect-[4/3.7] bg-[#FAF1E8] shadow-[0_10px_24px_-4px_rgba(0,0,0,0.11),0_3px_8px_rgba(0,0,0,0.04)] border-[3px] sm:border-[4.5px] border-white group rotate-2 translate-y-1.5 hover:rotate-0 transition-all duration-300">
                    <Image
                      src="/images/about_team_smooth_operations.jpg"
                      alt="Workflows"
                      fill
                      sizes="(max-width: 640px) 30vw, 240px"
                      className="object-cover object-center group-hover:scale-105 transition-transform duration-500 filter contrast-[1.02]"
                    />
                    <div className="absolute bottom-1.5 right-1.5 sm:bottom-2 sm:right-2 px-2 sm:px-2.5 py-0.5 rounded-full bg-[#FFF1F2] text-[#BE123C] border border-rose-200/60 shadow-xs flex items-center gap-1 text-[8.5px] sm:text-[10px] font-semibold whitespace-nowrap">
                      <Layers className="w-2.5 h-2.5 sm:w-3 sm:h-3 text-[#BE123C] stroke-[2.2]" />
                      <span>Workflows</span>
                    </div>
                  </div>

                </div>

              </div>

              {/* Hand-drawn scribble annotation: "A team that gets it." */}
              <div className="absolute -right-2 sm:-right-4 lg:-right-8 top-[24%] translate-y-[-50%] pointer-events-none select-none hidden md:flex flex-col items-center">
                <span className="font-scribble text-sm sm:text-base lg:text-[17px] text-zinc-600 whitespace-nowrap transform -rotate-6 leading-tight text-center">
                  A team<br />that gets<br />it.
                </span>
                <svg width="30" height="36" viewBox="0 0 36 42" fill="none" className="stroke-zinc-500 stroke-[1.8] stroke-linecap-round -rotate-12 mt-0.5">
                  <path d="M24 2C18 12 8 22 10 36M10 36L3 30M10 36L18 34" />
                </svg>
              </div>

            </div>

          </div>
        </div>

      </section>

      {/* ─────────────────────────────────────────────────────────────
          SECTION 2: OUR STORY / MISSION (From Scattered Tools to a Calmer Tomorrow)
      ───────────────────────────────────────────────────────────── */}
      {/* ─────────────────────────────────────────────────────────────
          SECTION 2: OUR STORY / MISSION (From Scattered Tools to a Calmer Tomorrow)
      ───────────────────────────────────────────────────────────── */}
      {/* ─────────────────────────────────────────────────────────────
          SECTION 2: OUR STORY / MISSION (Full-Width Edge-to-Edge Canvas)
      ───────────────────────────────────────────────────────────── */}
      <section id="story" className="relative w-full overflow-hidden mb-24 sm:mb-36 scroll-mt-24">
        {/* Anchor point for #mission links */}
        <div id="mission" className="absolute -top-24 left-0" />

        {/* ── Top Text & Call-To-Action Layer (Centered Over Canvas) ── */}
        <div className="relative z-10 w-full max-w-[880px] mx-auto px-4 sm:px-6 text-center pt-3 sm:pt-12">
          {/* Eyebrow */}
          <div className="inline-flex items-center gap-2 px-3.5 py-1 bg-white/95 backdrop-blur-md rounded-full text-[11px] font-mono font-bold tracking-[0.16em] uppercase text-zinc-700 border border-zinc-200/90 mb-3 sm:mb-5 shadow-2xs">
            <span>OUR STORY</span>
          </div>

          {/* Main Title */}
          <h2 className="font-display text-3xl xs:text-4xl sm:text-6xl lg:text-7xl font-bold tracking-tight text-zinc-950 mb-3 sm:mb-4 leading-[1.08] max-w-[820px] mx-auto">
            From scattered tools{' '}
            <span className="bg-gradient-to-r from-zinc-950 via-zinc-700 to-zinc-400 bg-clip-text text-transparent">
              to a calmer, bigger tomorrow.
            </span>
          </h2>

          {/* Concise Subtitle */}
          <p className="text-zinc-600 text-xs xs:text-sm sm:text-base lg:text-[17px] leading-relaxed max-w-[620px] mx-auto mb-5 sm:mb-8 font-normal">
            We&apos;re building Cora to give creative founders their time back — so they can run, grow, and enjoy their business on autopilot.
          </p>

          {/* CTA Button */}
          <div className="flex items-center justify-center mb-4 sm:mb-8">
            <a
              href="#manifesto"
              className="inline-flex items-center gap-2 bg-zinc-950 text-white hover:bg-zinc-800 border border-zinc-800 px-6 sm:px-7 py-3 sm:py-3.5 rounded-xl text-xs sm:text-sm font-semibold tracking-tight shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all group cursor-pointer"
            >
              <span>Read our story</span>
              <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:text-white group-hover:translate-x-0.5 transition-all" />
            </a>
          </div>
        </div>

        {/* ── Full-Bleed Edge-to-Edge Portal Artwork (Desktop & Mobile) ── */}
        {/* Desktop Version (Full Screen Width) */}
        <div className="hidden sm:block relative w-full aspect-[16/9] lg:aspect-[1920/1000] -mt-8 sm:-mt-12 lg:-mt-20 z-0">
          <Image
            src="/images/about_story_full_bg_desktop.jpg"
            alt="From scattered tools to Cora AI Workspace"
            fill
            priority
            sizes="100vw"
            className="object-cover object-top"
          />
          {/* Top seamless blend */}
          <div className="absolute inset-x-0 top-0 h-28 sm:h-40 bg-gradient-to-b from-white via-white/80 to-transparent pointer-events-none" />
          {/* Bottom seamless blend */}
          <div className="absolute inset-x-0 bottom-0 h-16 sm:h-24 bg-gradient-to-t from-white via-white/50 to-transparent pointer-events-none" />
        </div>

        {/* Mobile Version (Full Screen Width - Gap Eliminated) */}
        <div className="block sm:hidden relative w-full aspect-[576/760] -mt-8 xs:-mt-12 z-0">
          <Image
            src="/images/about_story_full_bg_mobile.jpg"
            alt="From scattered tools to Cora AI Workspace"
            fill
            priority
            sizes="100vw"
            className="object-cover object-top"
          />
          {/* Mobile Top Blend */}
          <div className="absolute inset-x-0 top-0 h-14 bg-gradient-to-b from-white via-white/70 to-transparent pointer-events-none" />
          {/* Mobile Bottom Blend */}
          <div className="absolute inset-x-0 bottom-0 h-12 bg-gradient-to-t from-white via-white/40 to-transparent pointer-events-none" />
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

