'use client';

import React, { useState, useEffect, useRef } from 'react';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import { 
  Sparkles, 
  ShieldCheck, 
  Lock, 
  FileText, 
  Zap, 
  CheckCircle2, 
  ArrowRight, 
  HardDrive, 
  Database,
  Cpu,
  Receipt,
  Shield,
  Key
} from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

const STEPS_DATA = [
  {
    step: '01',
    label: 'Step 01',
    title: 'Instant Inbound & AI Proposal Generation',
    desc: 'Capture inquiries from WhatsApp or your custom website form. Cora routes the request, calculates production costs, and drafts a branded scope of work in under 60 seconds.',
    statTitle: '60 Seconds',
    statDesc: 'From inquiry to client proposal',
    visual: {
      leftNode: 'Inbound Inquiry',
      leftIcon: Zap,
      centerNode: 'Multi-Model AI Brain',
      centerDesc: 'Gemini 2.0 & Claude 3.5 Auto-Routing',
      rightNode: 'Live Scope & Rates',
      rightIcon: Database,
    }
  },
  {
    step: '02',
    label: 'Step 02',
    title: 'Tamper-Evident E-Sign & 18% GST Invoicing',
    desc: 'Clients sign legally binding agreements on mobile without downloading any app. Cora automatically generates compliant B2B tax invoices with CGST/SGST/IGST splits.',
    statTitle: '100% Legal',
    statDesc: 'Indian IT Act 2000 & GST compliant',
    visual: {
      leftNode: 'Cryptographic E-Sign',
      leftIcon: FileText,
      centerNode: '₹85,000 + 18% GST',
      centerDesc: 'Instant Razorpay / UPI QR Settlement',
      rightNode: 'Direct Bank Settlement',
      rightIcon: Receipt,
    }
  },
  {
    step: '03',
    label: 'Step 03',
    title: 'Automated 4K Asset Delivery & Client Retention',
    desc: 'Once payment is confirmed, Cora unlocks client deliverables, sends automated WhatsApp call-sheets to crew, and triggers automated 5-star Google review collection.',
    statTitle: '20+ Hrs / Wk',
    statDesc: 'Reclaimed founder & admin time',
    visual: {
      leftNode: 'Cloud Media Vault',
      leftIcon: HardDrive,
      centerNode: 'Instant Client Access',
      centerDesc: 'Automated WhatsApp & Email Dispatch',
      rightNode: '5-Star SEO Review',
      rightIcon: Sparkles,
    }
  }
];

export function ProductFreedomRoiSection() {
  const [activeStep, setActiveStep] = useState(0);
  const sectionRef = useRef<HTMLElement>(null);
  const stepCardRef = useRef<HTMLDivElement>(null);
  const securityCardRef = useRef<HTMLDivElement>(null);

  // Auto step timer
  useEffect(() => {
    const timer = setInterval(() => {
      setActiveStep((prev) => (prev + 1) % STEPS_DATA.length);
    }, 6000);
    return () => clearInterval(timer);
  }, []);

  useEffect(() => {
    const ctx = gsap.context(() => {
      // Entrance reveal for all items in the section
      gsap.fromTo(
        '.prod-anim-item',
        { y: 30, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.8,
          stagger: 0.1,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: sectionRef.current,
            start: 'top 78%',
          },
        }
      );
    }, sectionRef);

    return () => ctx.revert();
  }, []);

  const currentStep = STEPS_DATA[activeStep];

  return (
    <section
      ref={sectionRef}
      id="how-it-works"
      className="py-20 sm:py-28 relative z-10 bg-white border-b border-zinc-200/70 overflow-hidden"
    >
      <div className="w-full max-w-[1200px] mx-auto px-4 sm:px-6">
        
        {/* ── Section Main Header ── */}
        <div className="prod-anim-item text-center max-w-[780px] mx-auto mb-16 sm:mb-20">
          <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-zinc-100 rounded-full border border-zinc-200/90 text-xs font-semibold text-zinc-900 mb-4 shadow-2xs">
            <span className="w-2 h-2 rounded-full bg-emerald-500" />
            <span>Autonomous Execution &amp; ROI</span>
          </div>
          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl font-bold text-zinc-950 leading-[1.12] tracking-[-0.03em] mb-3.5">
            How Cora works for your studio
          </h2>
          <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[620px] mx-auto">
            From initial client inquiry to legally binding e-signatures, 18% GST settlement, and encrypted vault delivery in minutes.
          </p>
        </div>

        {/* ── PART 1: 3-Step Interactive Workflow (USP & Freedom) ── */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-14 items-center mb-20 sm:mb-28">
          
          {/* Left Column: Metrics & Value Proposition */}
          <div className="lg:col-span-5 space-y-7 prod-anim-item">
            <div className="space-y-3.5">
              <span className="text-xs font-bold uppercase tracking-wider text-zinc-500 font-mono">
                Workflow Automation
              </span>
              <h3 className="font-display text-2xl xs:text-3xl sm:text-[36px] font-bold text-zinc-950 leading-[1.15] tracking-tight">
                Scale your agency without the headcount
              </h3>
              <p className="text-zinc-600 text-sm sm:text-base font-normal leading-relaxed">
                Connect your client channels, let Cora automate agreements, 18% GST invoices, and call-sheet dispatch, and reclaim your weekends.
              </p>
            </div>

            {/* ROI Badges Grid */}
            <div className="grid grid-cols-2 gap-6 pt-5 border-t border-zinc-200/80">
              <div>
                <h4 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight">
                  20+ Hours
                </h4>
                <p className="text-zinc-600 text-xs sm:text-[13px] font-normal mt-1 leading-snug">
                  Reclaimed founder &amp; admin time every single week
                </p>
              </div>

              <div>
                <h4 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight">
                  2 Minutes
                </h4>
                <p className="text-zinc-600 text-xs sm:text-[13px] font-normal mt-1 leading-snug">
                  Setup to connect your client portal and begin instantly
                </p>
              </div>
            </div>
          </div>

          {/* Right Column: Monochromatic Interactive Stepper Card */}
          <div className="lg:col-span-7 flex flex-col items-center prod-anim-item">
            
            {/* Stepper Tabs */}
            <div className="flex items-center gap-2 mb-3 self-end pr-2">
              {STEPS_DATA.map((s, idx) => (
                <button
                  key={s.step}
                  onClick={() => {
                    setActiveStep(idx);
                    trackEvent('step_tab_click', { step: s.step });
                  }}
                  className={`px-4 py-1.5 rounded-xl text-xs font-semibold transition-all duration-200 ${
                    activeStep === idx
                      ? 'bg-zinc-950 text-white shadow-xs'
                      : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200/80'
                  }`}
                >
                  {s.label}
                </button>
              ))}
            </div>

            {/* Step Card Viewport */}
            <div
              ref={stepCardRef}
              className="w-full rounded-[32px] p-6 sm:p-10 bg-zinc-50 border border-zinc-200/90 shadow-[0px_12px_36px_rgba(0,0,0,0.03)] flex flex-col justify-between min-h-[380px] sm:min-h-[420px]"
            >
              {/* Visual Flow Representation */}
              <div className="relative w-full h-[180px] sm:h-[210px] bg-white rounded-2xl border border-zinc-200/80 p-6 flex items-center justify-between shadow-2xs overflow-hidden">
                
                {/* Micro Grid Background */}
                <div 
                  className="absolute inset-0 opacity-[0.05] pointer-events-none"
                  style={{
                    backgroundImage: 'radial-gradient(circle at 1px 1px, #18181B 1px, transparent 0)',
                    backgroundSize: '16px 16px',
                  }}
                />

                {/* Left Node */}
                <div className="relative z-10 flex flex-col items-center gap-2">
                  <div className="w-12 h-12 rounded-xl bg-zinc-50 border border-zinc-200/90 shadow-2xs flex items-center justify-center text-zinc-900">
                    {React.createElement(currentStep.visual.leftIcon, { className: 'w-5 h-5 text-zinc-800' })}
                  </div>
                  <span className="text-[11px] font-semibold text-zinc-700 max-w-[90px] text-center leading-tight">
                    {currentStep.visual.leftNode}
                  </span>
                </div>

                {/* Connecting Dotted Line */}
                <div className="flex-1 px-4 relative flex items-center justify-center">
                  <div className="w-full border-t-2 border-dashed border-zinc-300 relative">
                    <div className="absolute top-1/2 -translate-y-1/2 left-1/2 -translate-x-1/2 w-2 h-2 rounded-full bg-emerald-500 animate-ping" />
                  </div>
                </div>

                {/* Center Node (AI Brain / Main Processor) */}
                <div className="relative z-10 flex flex-col items-center gap-2">
                  <div className="w-16 h-16 rounded-2xl bg-zinc-950 text-white shadow-sm flex items-center justify-center border border-zinc-800">
                    <Sparkles className="w-7 h-7 text-emerald-400" />
                  </div>
                  <span className="text-xs font-bold text-zinc-950 max-w-[120px] text-center leading-tight">
                    {currentStep.visual.centerNode}
                  </span>
                  <span className="text-[9.5px] text-zinc-500 font-medium text-center">
                    {currentStep.visual.centerDesc}
                  </span>
                </div>

                {/* Connecting Dotted Line */}
                <div className="flex-1 px-4 relative flex items-center justify-center">
                  <div className="w-full border-t-2 border-dashed border-zinc-300" />
                </div>

                {/* Right Node */}
                <div className="relative z-10 flex flex-col items-center gap-2">
                  <div className="w-12 h-12 rounded-xl bg-zinc-50 border border-zinc-200/90 shadow-2xs flex items-center justify-center text-zinc-900">
                    {React.createElement(currentStep.visual.rightIcon, { className: 'w-5 h-5 text-emerald-600' })}
                  </div>
                  <span className="text-[11px] font-semibold text-zinc-700 max-w-[90px] text-center leading-tight">
                    {currentStep.visual.rightNode}
                  </span>
                </div>

              </div>

              {/* Bottom Step Title & Description */}
              <div className="mt-6 text-center space-y-1.5">
                <h4 className="font-display text-lg sm:text-xl font-bold text-zinc-950 tracking-tight">
                  {currentStep.title}
                </h4>
                <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed max-w-[500px] mx-auto font-normal">
                  {currentStep.desc}
                </p>
              </div>

            </div>

          </div>

        </div>


        {/* ── PART 2: Monochromatic Security & Compliance Sub-Grid ── */}
        <div className="pt-16 sm:pt-20 border-t border-zinc-200/80">
          
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-14 items-center">
            
            {/* Left Column: Monochromatic Security Visual Architecture Card */}
            <div className="lg:col-span-6 flex justify-center prod-anim-item">
              <div
                ref={securityCardRef}
                className="w-full rounded-[32px] p-8 sm:p-12 bg-zinc-50 border border-zinc-200/90 shadow-[0px_12px_36px_rgba(0,0,0,0.03)] flex flex-col items-center justify-center min-h-[360px] sm:min-h-[400px]"
              >
                {/* Monochromatic Vault Icon & Node */}
                <div className="relative mb-7 flex flex-col items-center">
                  <div className="w-18 h-18 sm:w-20 sm:h-20 rounded-3xl bg-zinc-950 text-white shadow-md flex items-center justify-center border border-zinc-800">
                    <Lock className="w-8 h-8 sm:w-9 sm:h-9 text-emerald-400" />
                  </div>
                  <div className="w-px h-6 bg-zinc-300 my-1" />
                  <div className="w-7 h-7 rounded-full bg-white border border-zinc-300 flex items-center justify-center shadow-2xs text-zinc-800">
                    <Cpu className="w-3.5 h-3.5 text-zinc-900" />
                  </div>
                </div>

                {/* 3 Security Compliance Seals in Monochromatic Theme */}
                <div className="grid grid-cols-3 gap-3.5 sm:gap-5 w-full max-w-[400px]">
                  <div className="bg-white rounded-2xl p-3.5 border border-zinc-200/90 shadow-2xs flex flex-col items-center text-center">
                    <span className="text-[9px] font-bold text-zinc-400 uppercase tracking-widest font-mono">AICPA</span>
                    <span className="text-xs sm:text-[13px] font-black text-zinc-950 tracking-tight mt-0.5">SOC 2</span>
                    <span className="text-[8.5px] font-semibold text-emerald-600 uppercase mt-0.5">TYPE II</span>
                  </div>

                  <div className="bg-white rounded-2xl p-3.5 border border-zinc-200/90 shadow-2xs flex flex-col items-center text-center">
                    <span className="text-[9px] font-bold text-zinc-400 uppercase tracking-widest font-mono">GLOBAL</span>
                    <span className="text-xs sm:text-[13px] font-black text-zinc-950 tracking-tight mt-0.5">ISO</span>
                    <span className="text-[8.5px] font-semibold text-zinc-700 uppercase mt-0.5">27001</span>
                  </div>

                  <div className="bg-white rounded-2xl p-3.5 border border-zinc-200/90 shadow-2xs flex flex-col items-center text-center">
                    <span className="text-[9px] font-bold text-zinc-400 uppercase tracking-widest font-mono">LEGAL</span>
                    <span className="text-xs sm:text-[13px] font-black text-zinc-950 tracking-tight mt-0.5">SHA-256</span>
                    <span className="text-[8.5px] font-semibold text-emerald-600 uppercase mt-0.5">IT ACT 2000</span>
                  </div>
                </div>
              </div>
            </div>

            {/* Right Column: Security Checklist & Action */}
            <div className="lg:col-span-6 space-y-6 prod-anim-item">
              <div className="space-y-3">
                <span className="text-xs font-bold uppercase tracking-wider text-zinc-500 font-mono">
                  Data Sovereignty &amp; Trust
                </span>
                <h3 className="font-display text-2xl xs:text-3xl sm:text-[36px] font-bold text-zinc-950 leading-[1.15] tracking-tight">
                  Your studio IP &amp; financials protected at every level
                </h3>
                <p className="text-zinc-600 text-sm sm:text-base font-normal leading-relaxed">
                  Enterprise-grade data encryption, immutable audit trails, and strict role permissions designed to keep your client agreements and financial records completely secure.
                </p>
              </div>

              {/* Get Started Button */}
              <div>
                <a
                  href="https://app.heycora.in/workspace/login?source=security_section"
                  onClick={() => trackEvent('cta_click', { section: 'security_section' })}
                  className="inline-flex items-center gap-2 bg-zinc-950 text-white px-5 sm:px-6 py-2.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-sm border border-zinc-800 group"
                >
                  <span>Get started now</span>
                  <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
                </a>
              </div>

              {/* Bullet Points with Clean Chevron */}
              <div className="space-y-2.5 pt-2">
                <div className="flex items-center gap-2.5 text-xs sm:text-sm font-medium text-zinc-700">
                  <span className="text-emerald-600 font-bold">›</span>
                  <span>End-to-end encrypted document &amp; invoice vault</span>
                </div>
                <div className="flex items-center gap-2.5 text-xs sm:text-sm font-medium text-zinc-700">
                  <span className="text-emerald-600 font-bold">›</span>
                  <span>Tamper-evident cryptographic signature hashes (SHA-256)</span>
                </div>
                <div className="flex items-center gap-2.5 text-xs sm:text-sm font-medium text-zinc-700">
                  <span className="text-emerald-600 font-bold">›</span>
                  <span>Multi-tier role permissions for core team, crew &amp; clients</span>
                </div>
                <div className="flex items-center gap-2.5 text-xs sm:text-sm font-medium text-zinc-700">
                  <span className="text-emerald-600 font-bold">›</span>
                  <span>100% Indian IT Act 2000 &amp; GST compliance standards</span>
                </div>
              </div>
            </div>

          </div>

        </div>

      </div>
    </section>
  );
}
