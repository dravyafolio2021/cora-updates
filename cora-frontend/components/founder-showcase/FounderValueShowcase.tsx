'use client';

import React, { useEffect, useRef } from 'react';
import Image from 'next/image';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import { ArrowRight, Check, Sparkles, MessageSquare, QrCode, FileText, CheckCircle2, Zap, Send } from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

export function FounderValueShowcase() {
  const sectionRef = useRef<HTMLElement>(null);
  const commandCardRef = useRef<HTMLDivElement>(null);
  const meadowBgRef = useRef<HTMLDivElement>(null);
  const invoiceCardRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const ctx = gsap.context(() => {
      // 1. Staggered Scroll Entrance for Cards
      gsap.fromTo(
        '.cap-card',
        { y: 40, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.8,
          stagger: 0.14,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: sectionRef.current,
            start: 'top 78%',
          },
        }
      );

      // 2. Parallax Scrolling on Card 2 Meadow Landscape Background
      if (meadowBgRef.current && sectionRef.current) {
        gsap.to(meadowBgRef.current, {
          yPercent: -16,
          ease: 'none',
          scrollTrigger: {
            trigger: sectionRef.current,
            start: 'top bottom',
            end: 'bottom top',
            scrub: 1.4,
          },
        });
      }

      // 3. Subtle Floating Motion on Card 1 Command snippet
      if (commandCardRef.current) {
        gsap.to(commandCardRef.current, {
          y: -6,
          duration: 3.2,
          repeat: -1,
          yoyo: true,
          ease: 'sine.inOut',
        });
      }

      // 4. Subtle Floating Motion on Card 3 Invoice snippet
      if (invoiceCardRef.current) {
        gsap.to(invoiceCardRef.current, {
          y: -6,
          duration: 2.8,
          repeat: -1,
          yoyo: true,
          ease: 'sine.inOut',
        });
      }
    }, sectionRef);

    return () => ctx.revert();
  }, []);

  const handleCtaClick = () => {
    trackEvent('founder_capabilities_cta_clicked');
  };

  return (
    <section
      id="founder-showcase"
      ref={sectionRef}
      className="py-16 sm:py-24 bg-white relative z-10 overflow-hidden border-b border-zinc-100"
    >
      <div className="w-full max-w-[1240px] mx-auto px-4 sm:px-6">
        
        {/* ── 1. Section Header with Crystal-Clear Messaging ── */}
        <div className="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-12 sm:mb-16">
          <div className="max-w-[680px]">
            {/* Top Pill */}
            <div className="inline-flex items-center gap-1.5 px-3.5 py-1 bg-zinc-100 rounded-full text-zinc-800 text-xs font-semibold uppercase tracking-wider mb-4 border border-zinc-200/80">
              <Sparkles className="w-3.5 h-3.5 text-zinc-950" />
              <span>THE AI OPERATING SYSTEM</span>
            </div>

            {/* Display Headline */}
            <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl lg:text-[44px] font-bold text-zinc-950 leading-[1.14] lg:leading-[1.12] tracking-[-0.03em]">
              Everything your service business needs to run on autopilot.
            </h2>
          </div>

          {/* Right Subtitle & Action Button */}
          <div className="max-w-[440px] flex flex-col items-start lg:items-end text-left lg:text-right gap-4">
            <p className="text-zinc-600 text-sm sm:text-base leading-relaxed font-normal">
              Replace 10 disconnected tools. From the first inquiry on WhatsApp to legal e-signing and 18% GST payments — Cora handles the busywork in one conversation.
            </p>
            <a
              href="https://app.heycora.in/workspace/login"
              onClick={handleCtaClick}
              className="inline-flex items-center gap-2 bg-zinc-950 text-white hover:bg-zinc-800 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold shadow-2xs transition-all hover:-translate-y-0.5"
            >
              <span>Start free workspace</span>
              <ArrowRight className="w-3.5 h-3.5" />
            </a>
          </div>
        </div>

        {/* ── 2. Three Highly Descriptive, Relevant Value Cards ── */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-7">
          
          {/* ── Card 1: Conversational Chat Ops (Replaced Distorted Shield with Real Chat Snippet) ── */}
          <div className="cap-card bg-zinc-50 rounded-[32px] p-6 sm:p-7 border border-zinc-200/80 flex flex-col justify-between h-[450px] sm:h-[490px] relative overflow-hidden group hover:shadow-[0px_20px_45px_rgba(0,0,0,0.06)] transition-all duration-300">
            <div>
              <h3 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 text-center mb-1.5">
                Run operations in plain words
              </h3>
              <p className="text-xs text-zinc-600 text-center max-w-[270px] mx-auto leading-relaxed">
                Type what you need done like talking to a co-founder. Cora handles the math, creates contracts, and dispatches links.
              </p>
            </div>

            {/* Center Realistic AI Command Card Graphic (100% Relevant to Chat Ops) */}
            <div ref={commandCardRef} className="relative my-auto w-full max-w-[290px] mx-auto space-y-2.5 will-change-transform">
              
              {/* User Voice / Chat Prompt Bubble */}
              <div className="bg-white rounded-2xl p-3 border border-zinc-200/90 shadow-[0_4px_16px_rgba(0,0,0,0.04)] text-left">
                <div className="flex items-center gap-2 mb-1">
                  <div className="w-5 h-5 rounded-full bg-zinc-900 text-white flex items-center justify-center text-[10px] font-bold">
                    You
                  </div>
                  <span className="text-[10px] font-semibold text-zinc-500">Inquiry Prompt</span>
                </div>
                <p className="text-[11.5px] font-medium text-zinc-900 leading-snug">
                  &ldquo;Make a ₹15,000 invoice for Rahul with 18% GST&rdquo;
                </p>
              </div>

              {/* Instant Structured AI Action Output */}
              <div className="bg-zinc-950 text-white rounded-2xl p-3.5 shadow-[0_8px_24px_rgba(0,0,0,0.12)] space-y-2 text-left">
                <div className="flex items-center justify-between">
                  <div className="flex items-center gap-1.5">
                    <Sparkles className="w-3.5 h-3.5 text-emerald-400" />
                    <span className="text-[11px] font-bold text-white">Bill Generated in 2s</span>
                  </div>
                  <span className="px-1.5 py-0.5 rounded text-[9px] font-mono font-bold bg-emerald-500/20 text-emerald-300">
                    Ready
                  </span>
                </div>

                <div className="bg-zinc-900/90 rounded-xl p-2 text-[10.5px] text-zinc-300 space-y-1">
                  <div className="flex justify-between">
                    <span>Base: ₹15,000</span>
                    <span className="text-emerald-400 font-medium">+18% GST (₹2,700)</span>
                  </div>
                  <div className="flex justify-between text-white font-bold border-t border-zinc-800 pt-1">
                    <span>Total: ₹17,700</span>
                    <span className="text-emerald-400">UPI QR Active</span>
                  </div>
                </div>
              </div>

            </div>

            {/* Bottom Horizontal Tag Pills */}
            <div className="flex items-center justify-center gap-2 flex-wrap">
              <span className="text-[11px] font-semibold bg-white border border-zinc-200/90 text-zinc-800 px-3.5 py-1.5 rounded-full shadow-2xs">
                Zero Complex Menus
              </span>
              <span className="text-[11px] font-semibold bg-white border border-zinc-200/90 text-zinc-800 px-3.5 py-1.5 rounded-full shadow-2xs">
                English &amp; Hinglish
              </span>
            </div>
          </div>

          {/* ── Card 2: Native Business Memory & CRM ── */}
          <div className="cap-card rounded-[32px] p-6 sm:p-7 border border-zinc-200/80 flex flex-col justify-between h-[450px] sm:h-[490px] relative overflow-hidden group hover:shadow-[0px_20px_45px_rgba(0,0,0,0.08)] transition-all duration-300">
            {/* Background Meadow Landscape */}
            <div
              ref={meadowBgRef}
              className="absolute inset-0 -top-16 -bottom-16 pointer-events-none select-none will-change-transform"
            >
              <Image
                src="/images/cora_hero_landscape.jpg"
                alt="Cora Background"
                fill
                className="object-cover object-[center_35%]"
              />
            </div>
            
            <div className="absolute inset-0 bg-gradient-to-t from-white/50 via-white/10 to-white/40 pointer-events-none" />

            <div className="relative z-10">
              <h3 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 text-center mb-1.5">
                Remembers your clients &amp; rates
              </h3>
              <p className="text-xs text-zinc-700 text-center max-w-[270px] mx-auto leading-relaxed">
                Knows your pricing tiers, past shoot dates, and terms. No copy-pasting context every morning.
              </p>
            </div>

            {/* Center Floating Glass Client Memory Card */}
            <div className="relative z-10 my-auto bg-white/95 backdrop-blur-md rounded-2xl p-4 border border-white shadow-[0px_14px_35px_rgba(0,0,0,0.12)] text-left">
              <div className="flex items-center justify-between mb-2.5 pb-2 border-b border-zinc-100">
                <div className="flex items-center gap-2">
                  <div className="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-[10px] font-bold">
                    RV
                  </div>
                  <div>
                    <h4 className="text-xs font-bold text-zinc-950">Rahul Verma</h4>
                    <p className="text-[10px] text-zinc-500">Commercial Studio Retainer</p>
                  </div>
                </div>
                <span className="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200/80 rounded-full text-[9.5px] font-bold">
                  ● Rate Saved
                </span>
              </div>

              <div className="space-y-1.5 text-[11px] text-zinc-700 font-medium">
                <div className="flex items-center justify-between">
                  <span className="text-zinc-500">Agreed Package:</span>
                  <span className="font-bold text-zinc-900">₹45,000 / Shoot</span>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-zinc-500">GST Registration:</span>
                  <span className="font-mono text-zinc-800 text-[10px]">27AAAAA0000A1Z5</span>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-zinc-500">Next Booking:</span>
                  <span className="font-semibold text-emerald-700">Hold on 28th Aug</span>
                </div>
              </div>
            </div>

            {/* Bottom Status Row */}
            <div className="relative z-10 flex items-center justify-center">
              <span className="text-[11px] font-semibold bg-white/95 backdrop-blur-md border border-white text-zinc-900 px-4 py-1.5 rounded-full shadow-2xs">
                Zero Re-Explaining Context
              </span>
            </div>
          </div>

          {/* ── Card 3: WhatsApp + 18% GST + Instant UPI (Indian Commerce Engine) ── */}
          <div className="cap-card bg-zinc-950 rounded-[32px] p-6 sm:p-7 border border-zinc-800 flex flex-col justify-between h-[450px] sm:h-[490px] relative overflow-hidden group hover:shadow-[0px_20px_45px_rgba(0,0,0,0.25)] transition-all duration-300 text-white">
            <div>
              <h3 className="font-display text-xl sm:text-2xl font-bold text-white text-center mb-1.5">
                Built for Indian commerce
              </h3>
              <p className="text-xs text-zinc-400 text-center max-w-[270px] mx-auto leading-relaxed">
                Generate 18% GST bills, collect with PhonePe &amp; GPay UPI QR codes, and dispatch directly on WhatsApp.
              </p>
            </div>

            {/* Center Realistic Tax Invoice & UPI Card Graphic */}
            <div ref={invoiceCardRef} className="relative my-auto w-full max-w-[290px] mx-auto bg-zinc-900/90 rounded-2xl p-4 border border-zinc-800 shadow-[0px_14px_35px_rgba(0,0,0,0.4)] text-left will-change-transform">
              
              <div className="flex items-center justify-between pb-2 border-b border-zinc-800">
                <span className="text-[10px] font-mono font-bold text-zinc-400 tracking-wider">
                  TAX INVOICE #CORA-2026
                </span>
                <span className="px-1.5 py-0.5 rounded text-[9px] font-bold bg-[#F5D491]/20 text-[#F5D491]">
                  18% GST
                </span>
              </div>

              <div className="py-2.5 space-y-1 text-[11px] text-zinc-300">
                <div className="flex justify-between">
                  <span>Professional Services</span>
                  <span className="font-semibold text-white">₹50,000</span>
                </div>
                <div className="flex justify-between text-zinc-400 text-[10px]">
                  <span>CGST (9%) + SGST (9%)</span>
                  <span>+₹9,000</span>
                </div>
                <div className="flex justify-between items-center pt-1 border-t border-zinc-800/80 font-bold text-white">
                  <span>Total Amount</span>
                  <span className="text-emerald-400 text-xs">₹59,000</span>
                </div>
              </div>

              {/* Instant Settlement Badge */}
              <div className="pt-2 border-t border-zinc-800 flex items-center justify-between text-[10px]">
                <span className="flex items-center gap-1 text-zinc-300">
                  <QrCode className="w-3.5 h-3.5 text-emerald-400" />
                  <span>PhonePe / GPay QR</span>
                </span>
                <span className="text-emerald-400 font-semibold font-mono">0% Gateway Fee</span>
              </div>

            </div>

            {/* Bottom Tag */}
            <div className="flex flex-col items-center text-center">
              <div className="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-zinc-900 border border-zinc-800 rounded-full text-[11px] text-zinc-300 font-mono">
                <QrCode className="w-3.5 h-3.5 text-[#F5D491]" />
                <span>WhatsApp • UPI • 18% GST</span>
              </div>
            </div>
          </div>

        </div>

      </div>
    </section>
  );
}
