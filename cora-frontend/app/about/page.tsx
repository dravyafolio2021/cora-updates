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
    border: 'border-zinc-200',
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
          SECTION 1: HERO STAGE (Indian Team Mosaic Grid)
      ───────────────────────────────────────────────────────────── */}
      <section className="w-full max-w-[1400px] mx-auto px-3 xs:px-4 sm:px-6 text-center mb-24 sm:mb-32">
        
        {/* Eyebrow (Cora Design System Token: Monochromatic / Neutral) */}
        <div className="inline-flex items-center gap-2 px-3 py-1 bg-zinc-100 rounded-md text-[11px] xs:text-xs font-mono font-semibold uppercase tracking-widest text-zinc-700 border border-zinc-200/80 mb-3 sm:mb-4">
          <span>MAXIMIZE HUMAN PRODUCTIVITY</span>
        </div>

        {/* Main Headline (Strict Cora Monochromatic Tokens) */}
        <h1 className="font-display text-4xl xs:text-5xl sm:text-6xl lg:text-[76px] font-bold text-zinc-950 leading-[1.08] tracking-[-0.03em] max-w-[960px] mx-auto mb-6 sm:mb-8">
          We are <span className="font-bold text-zinc-950">CORA</span>.
        </h1>

        {/* Hero CTA Button (Strictly following Cora Design System Tokens: rounded-xl, monochromatic) */}
        <div className="flex items-center justify-center mb-12 sm:mb-16">
          <a
            href="#manifesto"
            className="inline-flex items-center gap-2 bg-zinc-950 text-white hover:bg-zinc-850 border border-zinc-800 px-6 sm:px-7 py-3 sm:py-3.5 rounded-xl text-xs sm:text-sm font-semibold tracking-tight shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all group"
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

      </section>

      {/* ─────────────────────────────────────────────────────────────
          SECTION 2: OUR MISSION (Strict Cora Monochromatic Tokens)
      ───────────────────────────────────────────────────────────── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-28 sm:mb-40 text-center">
        
        {/* Eyebrow (Monochromatic Token) */}
        <div className="inline-flex items-center gap-2 px-3 py-1 bg-zinc-100 rounded-md text-[11px] xs:text-xs font-mono font-semibold uppercase tracking-widest text-zinc-700 border border-zinc-200/80 mb-3 sm:mb-4">
          <span>MAXIMIZE HUMAN PRODUCTIVITY</span>
        </div>

        {/* Main Title (Pure Monochromatic Black/Zinc) */}
        <h2 className="font-display text-5xl xs:text-6xl sm:text-7xl lg:text-[84px] font-bold text-zinc-950 leading-[1.05] tracking-[-0.035em] mb-4 sm:mb-5">
          Our mission.
        </h2>

        {/* Subtitle */}
        <h3 className="font-display text-2xl xs:text-3xl sm:text-4xl font-bold text-zinc-950 tracking-tight mb-4">
          Maximize human productivity.
        </h3>

        {/* Mission Brief Description */}
        <p className="text-zinc-600 text-base sm:text-lg leading-relaxed max-w-[660px] mx-auto mb-8 font-normal">
          We started Cora because the way the world works is broken. Teams juggle dozens of disconnected tools—tasks in one place, conversations in another, documents somewhere else—and critical context gets lost in between.
        </p>

        {/* CTA Button */}
        <div className="flex items-center justify-center mb-10 sm:mb-14">
          <a
            href="#manifesto"
            className="inline-flex items-center gap-2 bg-zinc-950 text-white hover:bg-zinc-850 border border-zinc-800 px-6 sm:px-7 py-3 sm:py-3.5 rounded-xl text-xs sm:text-sm font-semibold tracking-tight shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all group"
          >
            <span>Learn more about us</span>
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

        {/* ── Brand Trust / Client Ticker at bottom ── */}
        <div className="pt-2 max-w-[1040px] mx-auto">
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
          SECTION 3: OUR MOTTO (Dynamic Live Week Calendar & Telemetry)
      ───────────────────────────────────────────────────────────── */}
      <section className="w-full max-w-[1100px] mx-auto px-4 sm:px-6 mb-28 sm:mb-40">
        <DynamicWeekCalendar />
      </section>

      {/* ─────────────────────────────────────────────────────────────
          SECTION 4: OUR CORE VALUES (Responsive Mobile Strip & Desktop Grid)
      ───────────────────────────────────────────────────────────── */}
      <section className="w-full max-w-[1100px] mx-auto px-4 sm:px-6 mb-28 sm:mb-40">
        <CoreValuesSection />
      </section>

      {/* ─────────────────────────────────────────────────────────────
          SECTION 5: OUR JOURNEY (Scroll-Driven Connected Timeline)
      ───────────────────────────────────────────────────────────── */}
      <section className="w-full max-w-[1100px] mx-auto px-4 sm:px-6 mb-28 sm:mb-40">
        <ScrollDrivenTimeline />
      </section>

      {/* ─────────────────────────────────────────────────────────────
          SECTION 6: CO-FOUNDER SPOTLIGHT (Light Mode & Natural Canvas Blend)
      ───────────────────────────────────────────────────────────── */}
      <section id="manifesto" className="w-full max-w-[1100px] mx-auto px-4 sm:px-6 mb-24 sm:mb-32 scroll-mt-28">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
          
          {/* Founder Portrait with Gradient Alpha Cloud Blending */}
          <div className="lg:col-span-5 flex flex-col items-center lg:items-start">
            <div className="relative w-[280px] xs:w-[320px] sm:w-[360px] h-[380px] sm:h-[440px]">
              {/* Soft Atmospheric Glow Behind Portrait */}
              <div 
                className="absolute inset-0 rounded-full blur-3xl opacity-40 bg-zinc-200 pointer-events-none -z-10"
              />
              
              {/* Cutout Image with Bottom Dissolve Gradient */}
              <div className="relative w-full h-full">
                <Image
                  src="/images/dravya_bansal_cutout_clean.png"
                  alt="Dravya Bansal — Co-Founder of Cora"
                  fill
                  priority
                  sizes="(max-width: 768px) 320px, 360px"
                  className="object-contain object-bottom filter contrast-[1.02]"
                />
                {/* Natural White Mist / Cloud Gradient Over Bottom */}
                <div className="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-white via-white/80 to-transparent pointer-events-none" />
                <div className="absolute inset-y-0 left-0 w-8 bg-gradient-to-r from-white/60 to-transparent pointer-events-none" />
                <div className="absolute inset-y-0 right-0 w-8 bg-gradient-to-l from-white/60 to-transparent pointer-events-none" />
              </div>
            </div>

            {/* Clean Attribution */}
            <div className="mt-2 text-center lg:text-left space-y-0.5">
              <h3 className="font-display font-bold text-zinc-950 text-base sm:text-lg">
                Dravya Bansal
              </h3>
              <p className="text-xs font-mono text-zinc-500 uppercase tracking-wider">
                Co-Founder &amp; Product Architect
              </p>
            </div>
          </div>

          {/* Founder Manifesto Text Column */}
          <div className="lg:col-span-7 space-y-6 text-center lg:text-left">
            <span className="text-[11px] sm:text-xs font-mono font-semibold uppercase tracking-widest text-zinc-500 block">
              FOUNDER MANIFESTO
            </span>

            {/* Single Powerful Heading */}
            <h2 className="font-display text-2xl xs:text-3xl sm:text-[32px] lg:text-[36px] font-bold text-zinc-950 leading-[1.2] tracking-tight">
              &ldquo;Built for creators, not spreadsheets.&rdquo;
            </h2>

            {/* Oneliner Description */}
            <p className="text-sm sm:text-base text-zinc-600 font-normal leading-relaxed max-w-[520px]">
              Cora unifies client discovery, automated GST billing, and digital agreements into one calm, intelligent operating system.
            </p>

            {/* The CTA & Minimal Channels */}
            <div className="flex items-center justify-center lg:justify-start flex-wrap gap-3.5 pt-2">
              <Link
                href="/workspace/login"
                className="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-zinc-950 text-white text-xs sm:text-sm font-semibold hover:bg-zinc-850 transition-all shadow-sm"
              >
                <span>Launch Cora Workspace</span>
                <ArrowRight className="w-4 h-4" />
              </Link>

              <a
                href="https://x.com/dravyafolio"
                target="_blank"
                rel="noopener noreferrer"
                className="inline-flex items-center gap-1.5 px-4 py-3 rounded-xl border border-zinc-200/90 text-zinc-700 text-xs sm:text-sm font-semibold hover:bg-zinc-100 transition-all"
              >
                <span>Follow on X</span>
                <ArrowUpRight className="w-3.5 h-3.5 text-zinc-400" />
              </a>

              <a
                href="https://linkedin.com/in/dravyafolio"
                target="_blank"
                rel="noopener noreferrer"
                className="inline-flex items-center gap-1.5 px-4 py-3 rounded-xl border border-zinc-200/90 text-zinc-700 text-xs sm:text-sm font-semibold hover:bg-zinc-100 transition-all"
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

