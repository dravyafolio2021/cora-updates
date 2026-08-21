'use client';

import React, { useState, useEffect, useRef } from 'react';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import { 
  Sparkles, 
  Lock, 
  FileText, 
  Zap, 
  ArrowRight, 
  Database,
  Receipt,
  MessageCircle,
  Calendar,
  ShieldCheck,
  CheckCircle2,
} from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

const STEPS_DATA = [
  {
    step: '01',
    label: 'Step 01',
    title: 'Client Inquiries & Instant Quotes',
    desc: 'Capture inquiries from WhatsApp or your website. Cora knows your service rates and packages, drafting an estimate and scope in seconds.',
    statTitle: '10 Seconds',
    statDesc: 'From inquiry to clear quote',
    visual: {
      leftNode: 'Inbound Lead',
      leftIcon: MessageCircle,
      centerNode: 'Cora Co-Founder',
      centerDesc: 'Native Business Memory',
      rightNode: 'Instant Quote',
      rightIcon: FileText,
    }
  },
  {
    step: '02',
    label: 'Step 02',
    title: '18% GST Invoicing & Instant UPI Links',
    desc: 'Generate compliant tax invoices with automatic CGST/SGST splits. Clients scan to pay immediately via PhonePe, GPay, or Paytm.',
    statTitle: '18% GST Auto',
    statDesc: 'Zero manual calculation errors',
    visual: {
      leftNode: 'Client Booking',
      leftIcon: Calendar,
      centerNode: '₹15,000 + 18% GST',
      centerDesc: 'Compliant Tax Split',
      rightNode: 'Direct UPI Payment',
      rightIcon: Receipt,
    }
  },
  {
    step: '03',
    label: 'Step 03',
    title: 'Automated Reminders & Client Follow-ups',
    desc: 'Keep your calendar full and your cash flow healthy. Cora sends automated WhatsApp reminders, follow-up messages, and repeat booking notes.',
    statTitle: '0% No-Shows',
    statDesc: 'Timely client reminders',
    visual: {
      leftNode: 'Confirmed Service',
      leftIcon: Zap,
      centerNode: 'Auto Reminders',
      centerDesc: 'Sent on WhatsApp',
      rightNode: 'Repeat Clients',
      rightIcon: Sparkles,
    }
  }
];

export function ProductFreedomRoiSection() {
  const [activeStep, setActiveStep] = useState(0);
  const sectionRef = useRef<HTMLElement>(null);
  const stepCardRef = useRef<HTMLDivElement>(null);
  const securityCardRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const timer = setInterval(() => {
      setActiveStep((prev) => (prev + 1) % STEPS_DATA.length);
    }, 6000);
    return () => clearInterval(timer);
  }, []);

  useEffect(() => {
    const ctx = gsap.context(() => {
      gsap.fromTo(
        '.prod-anim-item',
        { y: 30, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.7,
          stagger: 0.12,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: sectionRef.current,
            start: 'top 75%',
          },
        }
      );
    }, sectionRef);

    return () => ctx.revert();
  }, []);

  const handleStepClick = (index: number) => {
    setActiveStep(index);
    trackEvent('product_step_tab_clicked', { step: STEPS_DATA[index].step });

    if (stepCardRef.current) {
      gsap.fromTo(
        stepCardRef.current,
        { opacity: 0.7, y: 8 },
        { opacity: 1, y: 0, duration: 0.35, ease: 'power2.out' }
      );
    }
  };

  const currentStep = STEPS_DATA[activeStep];

  return (
    <section
      ref={sectionRef}
      className="py-20 sm:py-28 bg-white relative z-10 overflow-hidden border-b border-zinc-200/80"
    >
      <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        
        {/* ── PART 1: How Cora Works ── */}
        <div className="mb-20 sm:mb-28">
          
          <div className="text-center max-w-[760px] mx-auto mb-12 sm:mb-16 prod-anim-item">
            <span className="text-xs font-bold uppercase tracking-wider text-zinc-500 font-mono mb-2 inline-block">
              HOW IT WORKS
            </span>
            <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl font-bold text-zinc-950 leading-[1.12] tracking-[-0.03em] mb-4">
              From first inquiry to final payment in 3 steps
            </h2>
            <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed">
              Eliminate admin busywork and run your entire service business from a single chat.
            </p>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center prod-anim-item">
            
            {/* Step Selection Tabs */}
            <div className="lg:col-span-5 space-y-3.5">
              {STEPS_DATA.map((item, idx) => {
                const isActive = activeStep === idx;
                return (
                  <div
                    key={idx}
                    onClick={() => handleStepClick(idx)}
                    className={`cursor-pointer rounded-2xl p-5 sm:p-6 transition-all duration-300 border ${
                      isActive
                        ? 'bg-zinc-50 border-zinc-950 shadow-sm'
                        : 'bg-white border-zinc-200/70 hover:border-zinc-300 hover:bg-zinc-50/50'
                    }`}
                  >
                    <div className="flex items-center justify-between mb-2">
                      <span className={`text-xs font-bold font-mono uppercase tracking-wider ${isActive ? 'text-zinc-950' : 'text-zinc-400'}`}>
                        {item.label}
                      </span>
                      {isActive && (
                        <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
                      )}
                    </div>
                    
                    <h3 className={`font-display text-base sm:text-lg font-bold mb-1.5 transition-colors ${isActive ? 'text-zinc-950' : 'text-zinc-700'}`}>
                      {item.title}
                    </h3>
                    
                    <p className="text-xs sm:text-sm text-zinc-500 leading-relaxed line-clamp-2">
                      {item.desc}
                    </p>
                  </div>
                );
              })}
            </div>

            {/* Step Card Viewport */}
            <div
              ref={stepCardRef}
              className="lg:col-span-7 rounded-[32px] p-6 sm:p-10 bg-zinc-50 border border-zinc-200/90 shadow-2xs flex flex-col justify-between min-h-[380px] sm:min-h-[420px]"
            >
              <div className="relative w-full h-[180px] sm:h-[210px] bg-white rounded-2xl border border-zinc-200/80 p-6 flex items-center justify-between shadow-2xs overflow-hidden">
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

                {/* Center Node */}
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

              {/* Bottom Step Title */}
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

        {/* ── PART 2: Simplicity & Data Security ── */}
        <div className="pt-16 sm:pt-20 border-t border-zinc-200/80">
          
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-14 items-center">
            
            {/* Left Column: Security Illustration */}
            <div className="lg:col-span-6 flex justify-center prod-anim-item">
              <div
                ref={securityCardRef}
                className="w-full rounded-[32px] p-8 sm:p-12 bg-zinc-50 border border-zinc-200/90 shadow-2xs flex flex-col items-center justify-center min-h-[360px] sm:min-h-[400px]"
              >
                <div className="relative mb-7 flex flex-col items-center">
                  <div className="w-18 h-18 sm:w-20 sm:h-20 rounded-3xl bg-zinc-950 text-white shadow-md flex items-center justify-center border border-zinc-800">
                    <Lock className="w-8 h-8 sm:w-9 sm:h-9 text-emerald-400" />
                  </div>
                </div>

                <div className="grid grid-cols-3 gap-3.5 sm:gap-5 w-full max-w-[400px]">
                  <div className="bg-white rounded-2xl p-3.5 border border-zinc-200/90 shadow-2xs flex flex-col items-center text-center">
                    <span className="text-[9px] font-bold text-zinc-400 uppercase tracking-widest font-mono">PRIVACY</span>
                    <span className="text-xs sm:text-[13px] font-bold text-zinc-950 tracking-tight mt-0.5">Private</span>
                    <span className="text-[8.5px] font-semibold text-emerald-600 uppercase mt-0.5">Encrypted</span>
                  </div>

                  <div className="bg-white rounded-2xl p-3.5 border border-zinc-200/90 shadow-2xs flex flex-col items-center text-center">
                    <span className="text-[9px] font-bold text-zinc-400 uppercase tracking-widest font-mono">INDIA</span>
                    <span className="text-xs sm:text-[13px] font-bold text-zinc-950 tracking-tight mt-0.5">18% GST</span>
                    <span className="text-[8.5px] font-semibold text-zinc-700 uppercase mt-0.5">Compliant</span>
                  </div>

                  <div className="bg-white rounded-2xl p-3.5 border border-zinc-200/90 shadow-2xs flex flex-col items-center text-center">
                    <span className="text-[9px] font-bold text-zinc-400 uppercase tracking-widest font-mono">PAYMENTS</span>
                    <span className="text-xs sm:text-[13px] font-bold text-zinc-950 tracking-tight mt-0.5">UPI / QR</span>
                    <span className="text-[8.5px] font-semibold text-emerald-600 uppercase mt-0.5">Direct Bank</span>
                  </div>
                </div>
              </div>
            </div>

            {/* Right Column: Security Checklist */}
            <div className="lg:col-span-6 space-y-6 prod-anim-item">
              <div className="space-y-3">
                <span className="text-xs font-bold uppercase tracking-wider text-zinc-500 font-mono">
                  BUILT FOR INDIAN BUSINESSES
                </span>
                <h3 className="font-display text-2xl xs:text-3xl sm:text-[36px] font-bold text-zinc-950 leading-[1.15] tracking-tight">
                  Simple enough for day one. Secure enough for your accounts.
                </h3>
                <p className="text-zinc-600 text-sm sm:text-base font-normal leading-relaxed">
                  Your client records, pricing models, and financial numbers remain strictly private and encrypted.
                </p>
              </div>

              <div>
                <a
                  href="https://app.heycora.in/workspace/login?source=security_section"
                  onClick={() => trackEvent('cta_click', { section: 'security_section' })}
                  className="inline-flex items-center gap-2 bg-zinc-950 text-white px-5 sm:px-6 py-2.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-2xs"
                >
                  <span>Start free — no card needed</span>
                  <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
                </a>
              </div>

              <div className="space-y-2.5 pt-2">
                <div className="flex items-center gap-2.5 text-xs sm:text-sm font-medium text-zinc-700">
                  <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0" />
                  <span><strong>Zero Learning Curve:</strong> If you can use WhatsApp, you can use Cora</span>
                </div>
                <div className="flex items-center gap-2.5 text-xs sm:text-sm font-medium text-zinc-700">
                  <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0" />
                  <span><strong>Your Data Stays Yours:</strong> Never used to train public AI models</span>
                </div>
                <div className="flex items-center gap-2.5 text-xs sm:text-sm font-medium text-zinc-700">
                  <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0" />
                  <span><strong>Real-time Financial Clarity:</strong> Daily revenue &amp; pending balances at a glance</span>
                </div>
              </div>
            </div>

          </div>

        </div>

      </div>
    </section>
  );
}
