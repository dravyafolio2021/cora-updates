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
        <div className="inline-flex items-center gap-2 px-3.5 py-1 text-[11px] xs:text-xs sm:text-[13px] font-mono font-bold uppercase tracking-widest text-[#7B2BF9] mb-3 sm:mb-5">
          <span>MAXIMIZE HUMAN PRODUCTIVITY</span>
        </div>

        {/* Main Headline (Reduced font weight, uppercase CORA) */}
        <h1 className="font-display text-4xl xs:text-5xl sm:text-6xl lg:text-[76px] font-bold text-zinc-950 leading-[1.08] tracking-[-0.03em] max-w-[960px] mx-auto mb-3.5 sm:mb-4">
          We are <span className="font-bold text-zinc-950">CORA</span>.
        </h1>

        {/* Subtitle */}
        <p className="text-zinc-600 text-sm xs:text-base sm:text-xl font-normal leading-relaxed max-w-[620px] mx-auto mb-7 sm:mb-9">
          The Convergence of software, AI, humans.
        </p>

        {/* Hero CTA Button */}
        <div className="flex items-center justify-center mb-10 sm:mb-16">
          <a
            href="#manifesto"
            className="inline-flex items-center gap-2.5 bg-[#18181B] text-white px-6 sm:px-7 py-3 sm:py-3.5 rounded-full text-xs sm:text-sm font-bold hover:bg-black transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5 group"
          >
            <Play className="w-3 h-3 fill-white text-white" />
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
          SECTION 2: CO-FOUNDER SPOTLIGHT & MANIFESTO
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
          SECTION 3: 4 CORE OPERATING PRINCIPLES
      ───────────────────────────────────────────────────────────── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-28 sm:mb-36">
        <div className="text-center max-w-[680px] mx-auto mb-16">
          <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-zinc-100 rounded-xl border border-zinc-200/90 text-xs font-semibold text-zinc-800 mb-3 shadow-2xs">
            <span>Operating Principles</span>
          </div>
          <h2 className="font-display text-3xl sm:text-4xl font-bold text-zinc-950 tracking-tight">
            How we design and build software
          </h2>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 gap-6 sm:gap-8">
          {VALUES.map((val, i) => (
            <div
              key={i}
              className="bg-zinc-50 border border-zinc-200/90 rounded-[28px] p-7 sm:p-9 space-y-3 shadow-2xs hover:bg-white hover:shadow-md transition-all duration-300"
            >
              <div className="font-mono text-xs font-bold text-zinc-400 uppercase">
                Principle 0{i + 1}
              </div>
              <h3 className="font-display text-xl font-bold text-zinc-950 tracking-tight">
                {val.title}
              </h3>
              <p className="text-zinc-600 text-sm leading-relaxed font-normal">
                {val.desc}
              </p>
            </div>
          ))}
        </div>
      </section>

      {/* ─────────────────────────────────────────────────────────────
          SECTION 4: ENTERPRISE SECURITY & COMPLIANCE SUMMARY
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
          SECTION 5: BOTTOM CTA BANNER
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
