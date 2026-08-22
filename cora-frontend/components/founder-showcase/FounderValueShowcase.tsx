'use client';

import React, { useEffect, useRef } from 'react';
import Image from 'next/image';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import { ArrowRight, Sparkles, Check, QrCode } from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

export function FounderValueShowcase() {
  const sectionRef = useRef<HTMLElement>(null);
  const card1Ref = useRef<HTMLDivElement>(null);
  const card2Ref = useRef<HTMLDivElement>(null);
  const card3Ref = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const ctx = gsap.context(() => {
      // 1. Staggered Entrance
      gsap.fromTo(
        '.cap-card',
        { y: 35, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.7,
          stagger: 0.12,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: sectionRef.current,
            start: 'top 78%',
          },
        }
      );

      // 2. Gentle Natural Floating Effects
      if (card1Ref.current) {
        gsap.to(card1Ref.current, {
          y: -5,
          duration: 3.5,
          repeat: -1,
          yoyo: true,
          ease: 'sine.inOut',
        });
      }

      if (card3Ref.current) {
        gsap.to(card3Ref.current, {
          y: -5,
          duration: 3.2,
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
        
        {/* ── 1. Section Header ── */}
        <div className="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-12 sm:mb-16">
          <div className="max-w-[680px]">
            {/* Top Pill */}
            <div className="inline-flex items-center gap-1.5 px-3.5 py-1 bg-zinc-100 rounded-full text-zinc-800 text-xs font-semibold uppercase tracking-wider mb-4 border border-zinc-200/80">
              <Sparkles className="w-3.5 h-3.5 text-zinc-950" />
              <span>THE ALL-IN-ONE OS</span>
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

        {/* ── 2. Three Editorial Visual Cards (Real Photography, Tactile Cards, Zero Neon) ── */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-7">
          
          {/* ── Card 1: Natural Language Chat Operations ── */}
          <div className="cap-card rounded-[32px] p-6 sm:p-7 border border-zinc-200/80 flex flex-col justify-between h-[460px] sm:h-[500px] relative overflow-hidden group hover:shadow-[0px_20px_45px_rgba(0,0,0,0.08)] transition-all duration-300 bg-zinc-50">
            
            {/* Background Photographic Atmosphere */}
            <div className="absolute inset-0 pointer-events-none select-none opacity-40 group-hover:opacity-50 transition-opacity">
              <Image
                src="/images/usecase_solo_creator.jpg"
                alt="Creative founder workspace"
                fill
                className="object-cover object-center"
              />
            </div>
            <div className="absolute inset-0 bg-gradient-to-t from-white via-white/85 to-white/70 pointer-events-none" />

            {/* Header Text */}
            <div className="relative z-10 text-center">
              <h3 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 mb-1.5">
                Talk naturally to your business
              </h3>
              <p className="text-xs text-zinc-600 max-w-[270px] mx-auto leading-relaxed font-normal">
                Type what you need done in plain English or Hinglish. Cora calculates taxes, drafts contracts, and prepares links.
              </p>
            </div>

            {/* Center Tactile Dialogue Card */}
            <div ref={card1Ref} className="relative z-10 my-auto w-full max-w-[300px] mx-auto space-y-2 text-left will-change-transform">
              
              {/* User Prompt Note */}
              <div className="bg-white/95 backdrop-blur-md rounded-2xl p-3.5 border border-zinc-200/90 shadow-[0_6px_20px_rgba(0,0,0,0.05)]">
                <span className="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block mb-1">
                  Your Plain English Request
                </span>
                <p className="text-[12px] font-medium text-zinc-900 leading-snug">
                  &ldquo;Draft a ₹25,000 agreement for Priya&rsquo;s 2-day shoot and bill 18% GST&rdquo;
                </p>
              </div>

              {/* Instant Action Result */}
              <div className="bg-zinc-900 text-white rounded-2xl p-3.5 shadow-[0_10px_28px_rgba(0,0,0,0.14)] space-y-2 border border-zinc-800">
                <div className="flex items-center justify-between text-[11px]">
                  <span className="font-bold text-zinc-100 flex items-center gap-1.5">
                    <Check className="w-3.5 h-3.5 text-emerald-400" />
                    Agreement &amp; Invoice Prepared
                  </span>
                  <span className="text-[9.5px] font-mono text-zinc-400">2.1s</span>
                </div>
                <div className="text-[11px] text-zinc-300 pt-1.5 border-t border-zinc-800 flex justify-between items-center">
                  <span>Total: ₹29,500</span>
                  <span className="text-white font-medium text-[10px] underline underline-offset-2">
                    Ready to send on WhatsApp →
                  </span>
                </div>
              </div>

            </div>

            {/* Bottom Status Tags */}
            <div className="relative z-10 flex items-center justify-center gap-2 flex-wrap">
              <span className="text-[11px] font-semibold bg-white/90 backdrop-blur-sm border border-zinc-200 text-zinc-800 px-3.5 py-1.5 rounded-full shadow-2xs">
                Zero Menus or Spreadsheets
              </span>
            </div>
          </div>

          {/* ── Card 2: Native Business Memory & CRM ── */}
          <div className="cap-card rounded-[32px] p-6 sm:p-7 border border-zinc-200/80 flex flex-col justify-between h-[460px] sm:h-[500px] relative overflow-hidden group hover:shadow-[0px_20px_45px_rgba(0,0,0,0.08)] transition-all duration-300">
            
            {/* Background Meadow Landscape with Natural Light */}
            <div className="absolute inset-0 pointer-events-none select-none">
              <Image
                src="/images/cora_hero_landscape.jpg"
                alt="Natural landscape"
                fill
                className="object-cover object-[center_35%]"
              />
            </div>
            <div className="absolute inset-0 bg-gradient-to-t from-white/60 via-white/20 to-white/50 pointer-events-none" />

            {/* Header Text */}
            <div className="relative z-10 text-center">
              <h3 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 mb-1.5">
                Remembers every client &amp; rate
              </h3>
              <p className="text-xs text-zinc-700 max-w-[270px] mx-auto leading-relaxed font-normal">
                Knows your pricing tiers, past shoot dates, and terms. You never have to re-explain context or search old chats.
              </p>
            </div>

            {/* Center Floating Glass Client Dossier Card */}
            <div className="relative z-10 my-auto bg-white/95 backdrop-blur-md rounded-2xl p-4 border border-white shadow-[0px_14px_35px_rgba(0,0,0,0.12)] text-left">
              <div className="flex items-center justify-between mb-3 pb-2.5 border-b border-zinc-100">
                <div className="flex items-center gap-2">
                  <div className="w-7 h-7 rounded-full bg-zinc-950 text-white flex items-center justify-center text-[11px] font-bold">
                    RV
                  </div>
                  <div>
                    <h4 className="text-xs font-bold text-zinc-950">Rahul Verma</h4>
                    <p className="text-[10.5px] text-zinc-500">Commercial Studio Retainer</p>
                  </div>
                </div>
                <span className="px-2 py-0.5 bg-zinc-100 text-zinc-800 border border-zinc-200 rounded-full text-[9.5px] font-bold">
                  Client Profile
                </span>
              </div>

              <div className="space-y-1.5 text-[11px] text-zinc-700 font-medium">
                <div className="flex items-center justify-between">
                  <span className="text-zinc-500">Locked Rate:</span>
                  <span className="font-bold text-zinc-900">₹45,000 / shoot</span>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-zinc-500">State &amp; GSTIN:</span>
                  <span className="font-mono text-zinc-800 text-[10px]">Maharashtra (27)</span>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-zinc-500">Upcoming Booking:</span>
                  <span className="font-semibold text-zinc-900">Saturday 9:00 AM</span>
                </div>
              </div>
            </div>

            {/* Bottom Status Row */}
            <div className="relative z-10 flex items-center justify-center">
              <span className="text-[11px] font-semibold bg-white/95 backdrop-blur-md border border-white text-zinc-900 px-4 py-1.5 rounded-full shadow-2xs">
                Zero Re-Explaining
              </span>
            </div>
          </div>

          {/* ── Card 3: Indian Commerce & GST Invoicing ── */}
          <div className="cap-card rounded-[32px] p-6 sm:p-7 border border-zinc-200/80 flex flex-col justify-between h-[460px] sm:h-[500px] relative overflow-hidden group hover:shadow-[0px_20px_45px_rgba(0,0,0,0.08)] transition-all duration-300 bg-zinc-950 text-white">
            
            {/* Background Studio Production Atmosphere */}
            <div className="absolute inset-0 pointer-events-none select-none opacity-30 group-hover:opacity-40 transition-opacity">
              <Image
                src="/images/bento_gst_upi.jpg"
                alt="Commerce documentation"
                fill
                className="object-cover object-center"
              />
            </div>
            <div className="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/90 to-zinc-950/80 pointer-events-none" />

            {/* Header Text */}
            <div className="relative z-10 text-center">
              <h3 className="font-display text-xl sm:text-2xl font-bold text-white mb-1.5">
                Engineered for Indian commerce
              </h3>
              <p className="text-xs text-zinc-400 max-w-[270px] mx-auto leading-relaxed font-normal">
                Direct UPI payments with zero gateway deductions, automated 18% GST splits, and 1-tap WhatsApp delivery.
              </p>
            </div>

            {/* Center Realistic Indian Business Payment QR Card */}
            <div ref={card3Ref} className="relative z-10 my-auto w-full max-w-[280px] mx-auto bg-white text-zinc-950 rounded-2xl p-3.5 border border-zinc-200/80 shadow-[0px_16px_40px_rgba(0,0,0,0.35)] text-center will-change-transform">
              
              {/* Standee Merchant Header */}
              <div className="pb-2 border-b border-zinc-100 mb-2">
                <div className="flex items-center justify-center gap-1.5">
                  <span className="font-display text-xs font-bold text-zinc-950 uppercase tracking-wide">
                    DRAVYA CREATIVE STUDIO
                  </span>
                  <span className="w-3.5 h-3.5 rounded-full bg-blue-600 text-white flex items-center justify-center text-[8px] font-bold">✓</span>
                </div>
                <p className="text-[9.5px] text-zinc-500 font-medium mt-0.5">
                  Accepted via any UPI App
                </p>
                {/* UPI Provider Badges */}
                <div className="flex items-center justify-center gap-1.5 mt-1.5 text-[9px] font-semibold text-zinc-700">
                  <span className="px-1.5 py-0.5 bg-zinc-100 rounded text-zinc-800">GPay</span>
                  <span className="px-1.5 py-0.5 bg-zinc-100 rounded text-zinc-800">PhonePe</span>
                  <span className="px-1.5 py-0.5 bg-zinc-100 rounded text-zinc-800">Paytm</span>
                  <span className="px-1.5 py-0.5 bg-zinc-100 rounded text-zinc-800">BHIM</span>
                </div>
              </div>

              {/* Real Vector QR Code Matrix with Center UPI Emblem */}
              <div className="relative w-36 h-36 mx-auto bg-white p-2 border border-zinc-200 rounded-xl shadow-inner flex items-center justify-center">
                <svg className="w-full h-full" viewBox="0 0 100 100" fill="none">
                  {/* Outer corner squares */}
                  <rect x="5" y="5" width="26" height="26" rx="4" fill="#18181B" />
                  <rect x="9" y="9" width="18" height="18" rx="2" fill="#FFFFFF" />
                  <rect x="13" y="13" width="10" height="10" rx="1" fill="#18181B" />

                  <rect x="69" y="5" width="26" height="26" rx="4" fill="#18181B" />
                  <rect x="73" y="9" width="18" height="18" rx="2" fill="#FFFFFF" />
                  <rect x="77" y="13" width="10" height="10" rx="1" fill="#18181B" />

                  <rect x="5" y="69" width="26" height="26" rx="4" fill="#18181B" />
                  <rect x="9" y="73" width="18" height="18" rx="2" fill="#FFFFFF" />
                  <rect x="13" y="77" width="10" height="10" rx="1" fill="#18181B" />

                  {/* QR Data Pattern Blocks */}
                  <rect x="36" y="8" width="6" height="6" rx="1" fill="#18181B" />
                  <rect x="46" y="8" width="8" height="6" rx="1" fill="#18181B" />
                  <rect x="58" y="8" width="6" height="6" rx="1" fill="#18181B" />

                  <rect x="36" y="18" width="12" height="6" rx="1" fill="#18181B" />
                  <rect x="52" y="18" width="10" height="6" rx="1" fill="#18181B" />

                  <rect x="8" y="36" width="6" height="6" rx="1" fill="#18181B" />
                  <rect x="18" y="36" width="8" height="6" rx="1" fill="#18181B" />
                  <rect x="30" y="36" width="6" height="8" rx="1" fill="#18181B" />
                  <rect x="40" y="36" width="20" height="6" rx="1" fill="#18181B" />
                  <rect x="64" y="36" width="12" height="6" rx="1" fill="#18181B" />
                  <rect x="80" y="36" width="12" height="6" rx="1" fill="#18181B" />

                  <rect x="8" y="46" width="12" height="6" rx="1" fill="#18181B" />
                  <rect x="24" y="46" width="8" height="8" rx="1" fill="#18181B" />
                  <rect x="68" y="46" width="10" height="6" rx="1" fill="#18181B" />
                  <rect x="82" y="46" width="10" height="8" rx="1" fill="#18181B" />

                  <rect x="36" y="58" width="8" height="12" rx="1" fill="#18181B" />
                  <rect x="48" y="58" width="14" height="6" rx="1" fill="#18181B" />
                  <rect x="66" y="58" width="8" height="6" rx="1" fill="#18181B" />
                  <rect x="78" y="58" width="14" height="6" rx="1" fill="#18181B" />

                  <rect x="36" y="74" width="12" height="6" rx="1" fill="#18181B" />
                  <rect x="52" y="74" width="8" height="14" rx="1" fill="#18181B" />
                  <rect x="64" y="74" width="14" height="6" rx="1" fill="#18181B" />
                  <rect x="82" y="74" width="10" height="14" rx="1" fill="#18181B" />

                  <rect x="36" y="84" width="10" height="8" rx="1" fill="#18181B" />
                  <rect x="64" y="84" width="12" height="8" rx="1" fill="#18181B" />
                </svg>
                {/* Center UPI Badge */}
                <div className="absolute inset-0 m-auto w-7 h-7 rounded-lg bg-zinc-950 text-white flex items-center justify-center text-[10px] font-bold shadow-md border-2 border-white">
                  ₹
                </div>
              </div>

              {/* Amount and UPI VPA ID */}
              <div className="mt-2.5 pt-2 border-t border-zinc-100">
                <div className="flex items-center justify-between text-xs">
                  <span className="font-semibold text-zinc-600 text-[10.5px]">Amount (with 18% GST):</span>
                  <span className="font-bold text-zinc-950 text-sm">₹59,000</span>
                </div>
                <p className="text-[10px] font-mono text-zinc-500 mt-0.5">
                  UPI ID: <span className="text-zinc-800 font-semibold">studio@okhdfcbank</span>
                </p>
              </div>

            </div>

            {/* Bottom Status Tag */}
            <div className="relative z-10 flex flex-col items-center text-center">
              <div className="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-zinc-900/90 border border-zinc-800 rounded-full text-[11px] text-zinc-300 font-mono">
                <QrCode className="w-3.5 h-3.5 text-zinc-400" />
                <span>WhatsApp • UPI • 18% GST</span>
              </div>
            </div>

          </div>

        </div>

      </div>
    </section>
  );
}
