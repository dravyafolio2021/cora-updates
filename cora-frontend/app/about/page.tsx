import React from 'react';
import type { Metadata } from 'next';
import Image from 'next/image';
import { 
  Sparkles, 
  ArrowRight, 
  ArrowUpRight, 
  ShieldCheck, 
  Lock, 
  CheckCircle2, 
  Award, 
  Cpu, 
  Users, 
  Heart,
  FileText
} from 'lucide-react';

export const metadata: Metadata = {
  title: 'About Cora & Co-Founder Dravya Bansal — Cora Studio OS',
  description: 'Learn about Cora, our mission at Claraverse Inc., and Co-Founder Dravya Bansal. Building the autonomous operating system for ambitious creative agencies.',
  alternates: {
    canonical: 'https://heycora.in/about/',
  },
};

const VALUES = [
  {
    title: 'Autonomous Execution',
    desc: 'Software should not require constant manual input. We design systems that trigger proposals, contracts, invoices, and dispatch automatically.'
  },
  {
    title: 'Zero Bloat & Zero Distraction',
    desc: 'We follow a strict monochromatic design ethos. No colourful gimmicks, no unnecessary clicks — only high-velocity tools engineered for operators.'
  },
  {
    title: 'Bank-Grade Data Sovereignty',
    desc: 'Your studio IP, contracts, and financial records belong solely to you. We uphold immutable SHA-256 cryptographic audit trails and SOC-2 standards.'
  },
  {
    title: 'Founder-First Accessibility',
    desc: 'Every ambitious creator deserves enterprise-grade operating power from day one. That is why our core platform remains accessible without high paywalls.'
  }
];

export default function AboutPage() {
  return (
    <main className="w-full relative pt-32 sm:pt-40 pb-20 overflow-hidden bg-white">
      
      {/* ── Top Hero Section ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 text-center mb-20 sm:mb-28">
        <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-zinc-100 rounded-xl border border-zinc-200/90 text-xs font-semibold text-zinc-900 mb-4 shadow-2xs">
          <span className="w-2 h-2 rounded-full bg-emerald-500" />
          <span>Our Mission &amp; Origins</span>
        </div>

        <h1 className="font-display text-4xl xs:text-5xl sm:text-6xl font-bold text-zinc-950 leading-[1.1] tracking-[-0.035em] max-w-[840px] mx-auto mb-5">
          We build software for creators who refuse to settle for chaos
        </h1>

        <p className="text-zinc-600 text-base sm:text-xl font-normal leading-relaxed max-w-[660px] mx-auto mb-8">
          Cora was born out of real production sets, client invoice disputes, and weekend administrative burnout. Here is why we created an autonomous studio OS.
        </p>

        <div className="flex items-center justify-center flex-wrap gap-3.5">
          <a
            href="https://app.heycora.in/workspace/login?source=about_hero"
            className="inline-flex items-center gap-2 bg-zinc-950 text-white px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-sm border border-zinc-800 group"
          >
            <span>Get started for Free</span>
            <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
          </a>

          <a
            href="mailto:dravya.bansal@heycora.in?subject=Founder%20Inquiry%20from%20About%20Page"
            className="inline-flex items-center gap-2 bg-white text-zinc-950 border border-zinc-300 hover:border-zinc-400 px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-50 transition-all shadow-2xs"
          >
            <span>Chat with Founder</span>
          </a>
        </div>
      </section>

      {/* ── Co-Founder Spotlight Stage ── */}
      <section className="w-full max-w-[1240px] mx-auto px-4 sm:px-6 mb-28 sm:mb-36">
        <div className="w-full rounded-[36px] bg-[#0A0D10] text-white p-8 sm:p-14 md:p-16 border border-zinc-800 shadow-2xl relative overflow-hidden">
          
          {/* Subtle Ambient Radial Emerald Glow */}
          <div
            className="absolute top-0 right-1/4 w-[600px] h-[600px] pointer-events-none opacity-20"
            style={{
              background: 'radial-gradient(circle, rgba(16, 185, 129, 0.4) 0%, transparent 70%)',
            }}
          />

          <div className="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
            
            {/* Founder High-Res Portrait Column */}
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

            {/* Founder Manifesto & Socials Column */}
            <div className="lg:col-span-7 space-y-7">
              <div className="space-y-3">
                <div className="inline-flex items-center gap-2 px-3.5 py-1 bg-zinc-900 rounded-lg text-xs font-mono text-zinc-300 border border-zinc-800">
                  <span>FOUNDER MANIFESTO</span>
                </div>

                <h2 className="font-display text-2xl xs:text-3xl sm:text-[38px] font-bold text-white leading-[1.15] tracking-tight">
                  "Our mission is simple: eliminate administrative overhead so creative founders never have to touch a spreadsheet again."
                </h2>
              </div>

              <div className="space-y-4 text-zinc-300 text-sm sm:text-base font-normal leading-relaxed">
                <p>
                  Having worked directly with commercial production sets, photographers, and high-growth creative agencies, we saw brilliant founders spending over 20 hours every week writing repetitive scopes, chasing client contract signatures, and manually computing 18% GST tax splits.
                </p>
                <p>
                  At Claraverse Inc., we engineered **Cora** as a complete autonomous backbone. By connecting frontier AI reasoning models with legally binding cryptographic signature rails and instant UPI payment infrastructure, we give studio founders the freedom to focus entirely on their craft.
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

      {/* ── 4 Core Operating Principles ── */}
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

      {/* ── Enterprise Security & Compliance Summary ── */}
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

    </main>
  );
}
