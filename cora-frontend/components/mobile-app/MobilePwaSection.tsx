'use client';

import React, { useEffect, useRef } from 'react';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import { 
  Check, 
  ArrowRight, 
  Smartphone, 
  Bell, 
  RotateCw, 
  Zap, 
  FileText, 
  Receipt, 
  Send, 
  Sparkles,
  Users,
  ShieldCheck,
  Camera
} from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

export function MobilePwaSection() {
  const sectionRef = useRef<HTMLElement>(null);
  const phoneRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const ctx = gsap.context(() => {
      // Reveal animation
      gsap.fromTo(
        '.pwa-anim-item',
        { y: 30, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.8,
          stagger: 0.12,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: sectionRef.current,
            start: 'top 78%',
          },
        }
      );

      // Subtle float on the phone mockup
      if (phoneRef.current) {
        gsap.to(phoneRef.current, {
          y: '-=8',
          duration: 3.5,
          repeat: -1,
          yoyo: true,
          ease: 'sine.inOut',
        });
      }
    }, sectionRef);

    return () => ctx.revert();
  }, []);

  return (
    <section
      ref={sectionRef}
      id="mobile-app"
      className="py-20 sm:py-28 relative z-10 bg-white border-b border-zinc-200/70 overflow-hidden"
    >
      <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        
        {/* ── Main Showcase Container Card ── */}
        <div className="pwa-anim-item relative w-full rounded-[36px] overflow-hidden bg-gradient-to-r from-white via-[#F6F9FD] to-[#E9F3FC] border border-zinc-200/90 shadow-[0px_20px_50px_rgba(0,0,0,0.06)] p-8 sm:p-12 md:p-16">
          
          {/* Subtle Ambient Radial Glow */}
          <div
            className="absolute top-0 right-0 w-[500px] h-[500px] pointer-events-none opacity-70"
            style={{
              background: 'radial-gradient(circle at 75% 25%, rgba(186, 224, 255, 0.45) 0%, transparent 65%)',
            }}
          />

          <div className="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-14 items-center">
            
            {/* ── Left Column: Value Proposition & Bullets ── */}
            <div className="lg:col-span-7 space-y-7">
              
              <div className="space-y-4">
                <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-white rounded-xl border border-zinc-200/90 text-xs font-semibold text-zinc-800 shadow-2xs">
                  <Smartphone className="w-3.5 h-3.5 text-zinc-900" />
                  <span>PWA Mobile App</span>
                </div>

                <h2 className="font-display text-3xl xs:text-4xl sm:text-[46px] font-bold text-zinc-950 leading-[1.12] tracking-[-0.03em]">
                  Everything you need to grow your business; even on the go!
                </h2>

                <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[580px]">
                  We make it easy to run your studio top to bottom and on the go, with real-time autonomous execution right from your phone.
                </p>
              </div>

              {/* 3 Checkmark Bullet Items */}
              <div className="space-y-4 pt-2">
                <div className="flex items-start gap-3">
                  <div className="w-5 h-5 rounded-full bg-zinc-950 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
                    <Check className="w-3 h-3 stroke-[3]" />
                  </div>
                  <div className="text-sm sm:text-[15px] text-zinc-800 leading-snug">
                    <strong className="text-zinc-950 font-bold">Mobile App:</strong> Run your business from anywhere with zero app store install friction.
                  </div>
                </div>

                <div className="flex items-start gap-3">
                  <div className="w-5 h-5 rounded-full bg-zinc-950 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
                    <Check className="w-3 h-3 stroke-[3]" />
                  </div>
                  <div className="text-sm sm:text-[15px] text-zinc-800 leading-snug">
                    <strong className="text-zinc-950 font-bold">Autonomous Dispatch:</strong> Send WhatsApp call-sheets to crew and capture client sign-offs in real time.
                  </div>
                </div>

                <div className="flex items-start gap-3">
                  <div className="w-5 h-5 rounded-full bg-zinc-950 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
                    <Check className="w-3 h-3 stroke-[3]" />
                  </div>
                  <div className="text-sm sm:text-[15px] text-zinc-800 leading-snug">
                    <strong className="text-zinc-950 font-bold">On-Set Invoicing:</strong> Issue legally binding e-signs and collect instant 18% GST UPI payments right from set.
                  </div>
                </div>
              </div>

              {/* Primary CTA */}
              <div className="pt-3">
                <a
                  href="https://app.heycora.in/workspace/login?source=mobile_pwa_section"
                  onClick={() => trackEvent('cta_click', { section: 'mobile_pwa_section' })}
                  className="inline-flex items-center gap-2 bg-zinc-950 text-white px-6 py-3.5 rounded-xl text-xs sm:text-sm font-semibold hover:bg-zinc-800 transition-all shadow-sm border border-zinc-800 group"
                >
                  <span>Get started for Free</span>
                  <ArrowRight className="w-3.5 h-3.5 text-zinc-400 group-hover:translate-x-0.5 transition-transform" />
                </a>
              </div>

            </div>

            {/* ── Right Column: High-Fidelity iPhone 16 PWA Mockup ── */}
            <div className="lg:col-span-5 flex justify-center lg:justify-end">
              <div
                ref={phoneRef}
                className="w-[290px] xs:w-[310px] sm:w-[330px] rounded-[44px] bg-[#0E1114] p-3 shadow-[0px_25px_60px_rgba(0,0,0,0.22)] border-[4px] border-[#2C3138] relative select-none"
              >
                {/* Dynamic Island Notch */}
                <div className="absolute top-5 left-1/2 -translate-x-1/2 w-24 h-5 bg-black rounded-full z-30 flex items-center justify-between px-2">
                  <div className="w-2.5 h-2.5 rounded-full bg-zinc-900" />
                  <div className="w-2 h-2 rounded-full bg-[#183446]" />
                </div>

                {/* Inner Screen Viewport */}
                <div className="w-full bg-[#F8FAFC] rounded-[36px] overflow-hidden pt-8 pb-5 px-4 text-zinc-950 font-sans border border-zinc-200/80">
                  
                  {/* Top Mobile Status Header */}
                  <div className="flex items-center justify-between mb-4 pt-1">
                    <div className="flex items-center gap-2">
                      <div className="w-7 h-7 rounded-lg bg-zinc-950 text-white flex items-center justify-center">
                        <Camera className="w-3.5 h-3.5" />
                      </div>
                      <div className="leading-tight">
                        <div className="text-xs font-bold text-zinc-950">Studio Aperture</div>
                        <div className="text-[9.5px] text-zinc-500 font-medium">Commercial Bay 01</div>
                      </div>
                    </div>

                    <div className="flex items-center gap-2 text-zinc-600">
                      <Bell className="w-4 h-4" />
                      <RotateCw className="w-3.5 h-3.5 text-zinc-400" />
                    </div>
                  </div>

                  {/* Section: Today at a Glance */}
                  <div className="mb-3.5">
                    <span className="text-[11px] font-bold text-zinc-800 tracking-tight">Today at a Glance</span>
                    
                    <div className="grid grid-cols-2 gap-2 mt-2">
                      {/* Metric 1: Tasks Due */}
                      <div className="bg-white rounded-xl p-2.5 border border-zinc-200/90 shadow-2xs">
                        <div className="flex items-center justify-between text-[10px] text-zinc-500 font-semibold">
                          <span>Active Shoots</span>
                          <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" />
                        </div>
                        <div className="text-lg font-bold text-zinc-950 font-display mt-0.5">12</div>
                      </div>

                      {/* Metric 2: Pipeline Value */}
                      <div className="bg-white rounded-xl p-2.5 border border-zinc-200/90 shadow-2xs">
                        <div className="flex items-center justify-between text-[10px] text-zinc-500 font-semibold">
                          <span>Pipeline Value</span>
                          <span className="text-emerald-600 font-bold">₹</span>
                        </div>
                        <div className="text-lg font-bold text-zinc-950 font-display mt-0.5">₹24.5L</div>
                      </div>

                      {/* Metric 3: Client Approvals */}
                      <div className="bg-white rounded-xl p-2.5 border border-zinc-200/90 shadow-2xs">
                        <div className="flex items-center justify-between text-[10px] text-zinc-500 font-semibold">
                          <span>E-Signs Pending</span>
                          <FileText className="w-3 h-3 text-zinc-400" />
                        </div>
                        <div className="text-lg font-bold text-zinc-950 font-display mt-0.5">4</div>
                      </div>

                      {/* Metric 4: Payouts */}
                      <div className="bg-white rounded-xl p-2.5 border border-zinc-200/90 shadow-2xs">
                        <div className="flex items-center justify-between text-[10px] text-zinc-500 font-semibold">
                          <span>GST Invoices</span>
                          <Receipt className="w-3 h-3 text-emerald-600" />
                        </div>
                        <div className="text-lg font-bold text-zinc-950 font-display mt-0.5">₹4.8L</div>
                      </div>
                    </div>
                  </div>

                  {/* Quick Action Pinned Apps */}
                  <div className="bg-white rounded-2xl p-3 border border-zinc-200/90 shadow-2xs">
                    <span className="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block mb-2 font-mono">
                      Quick Operations
                    </span>

                    <div className="grid grid-cols-3 gap-2 text-center">
                      <div className="flex flex-col items-center gap-1 p-1.5 rounded-lg bg-zinc-50 hover:bg-zinc-100 transition-colors">
                        <div className="w-7 h-7 rounded-full bg-zinc-950 text-white flex items-center justify-center">
                          <Send className="w-3.5 h-3.5" />
                        </div>
                        <span className="text-[9px] font-semibold text-zinc-800">Dispatch</span>
                      </div>

                      <div className="flex flex-col items-center gap-1 p-1.5 rounded-lg bg-zinc-50 hover:bg-zinc-100 transition-colors">
                        <div className="w-7 h-7 rounded-full bg-zinc-950 text-white flex items-center justify-center">
                          <FileText className="w-3.5 h-3.5 text-emerald-400" />
                        </div>
                        <span className="text-[9px] font-semibold text-zinc-800">E-Sign</span>
                      </div>

                      <div className="flex flex-col items-center gap-1 p-1.5 rounded-lg bg-zinc-50 hover:bg-zinc-100 transition-colors">
                        <div className="w-7 h-7 rounded-full bg-zinc-950 text-white flex items-center justify-center">
                          <Sparkles className="w-3.5 h-3.5 text-amber-400" />
                        </div>
                        <span className="text-[9px] font-semibold text-zinc-800">AI Scope</span>
                      </div>
                    </div>
                  </div>

                </div>

                {/* Home Indicator Bar */}
                <div className="w-28 h-1 bg-zinc-600 rounded-full mx-auto mt-3" />
              </div>
            </div>

          </div>

        </div>

      </div>
    </section>
  );
}
