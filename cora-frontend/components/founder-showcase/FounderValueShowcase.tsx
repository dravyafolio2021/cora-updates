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

  useEffect(() => {
    const ctx = gsap.context(() => {
      // Staggered Entrance
      gsap.fromTo(
        '.cap-card',
        { y: 30, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.65,
          stagger: 0.12,
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

          {/* Right Action Button */}
          <div className="flex items-center lg:items-end justify-start lg:justify-end shrink-0">
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

        {/* ── 2. Three Clean, Grounded Visual Cards ── */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-7">
          
          {/* ── Card 1: Natural Language Operations ── */}
          <div className="cap-card rounded-[32px] p-6 sm:p-7 border border-zinc-200/80 flex flex-col justify-between h-[470px] sm:h-[510px] relative overflow-hidden group hover:shadow-[0px_20px_45px_rgba(0,0,0,0.08)] transition-all duration-300 bg-zinc-50">
            
            {/* Background Atmosphere */}
            <div className="absolute inset-0 pointer-events-none select-none opacity-40 group-hover:opacity-50 transition-opacity">
              <Image
                src="/images/usecase_solo_creator.jpg"
                alt="Creative founder workspace"
                fill
                className="object-cover object-center"
              />
            </div>
            <div className="absolute inset-0 bg-gradient-to-t from-white via-white/85 to-white/70 pointer-events-none" />

            {/* Clean Header */}
            <div className="relative z-10 text-center">
              <h3 className="font-display text-2xl font-bold text-zinc-950">
                Talk naturally to your business
              </h3>
            </div>

            {/* Solid Grounded Dialogue Card */}
            <div className="relative z-10 w-full max-w-[300px] mx-auto space-y-2.5 text-left my-auto">
              
              {/* User Prompt Note */}
              <div className="bg-white/95 backdrop-blur-md rounded-2xl p-4 border border-zinc-200/90 shadow-[0_6px_20px_rgba(0,0,0,0.05)]">
                <span className="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block mb-1">
                  Plain English Request
                </span>
                <p className="text-[12.5px] font-medium text-zinc-900 leading-snug">
                  &ldquo;Draft a ₹25,000 agreement for Priya&rsquo;s 2-day shoot and bill 18% GST&rdquo;
                </p>
              </div>

              {/* Action Result */}
              <div className="bg-zinc-900 text-white rounded-2xl p-4 shadow-[0_10px_28px_rgba(0,0,0,0.14)] space-y-2.5 border border-zinc-800">
                <div className="flex items-center justify-between text-[11.5px]">
                  <span className="font-bold text-zinc-100 flex items-center gap-1.5">
                    <Check className="w-4 h-4 text-emerald-400" />
                    Agreement &amp; Tax Bill Ready
                  </span>
                  <span className="text-[9.5px] font-mono text-zinc-400">2.1s</span>
                </div>
                <div className="text-[11.5px] text-zinc-300 pt-2 border-t border-zinc-800 flex justify-between items-center">
                  <span>Total: ₹29,500</span>
                  <span className="text-white font-medium text-[10.5px] underline underline-offset-2">
                    Send on WhatsApp →
                  </span>
                </div>
              </div>

            </div>

            {/* Bottom Status Tag */}
            <div className="relative z-10 flex items-center justify-center">
              <span className="text-[11px] font-semibold bg-white/90 backdrop-blur-sm border border-zinc-200 text-zinc-800 px-4 py-1.5 rounded-full shadow-2xs">
                Zero Complex Menus
              </span>
            </div>
          </div>

          {/* ── Card 2: Native Business Memory & CRM ── */}
          <div className="cap-card rounded-[32px] p-6 sm:p-7 border border-zinc-200/80 flex flex-col justify-between h-[470px] sm:h-[510px] relative overflow-hidden group hover:shadow-[0px_20px_45px_rgba(0,0,0,0.08)] transition-all duration-300">
            
            {/* Background Landscape */}
            <div className="absolute inset-0 pointer-events-none select-none">
              <Image
                src="/images/cora_hero_landscape.jpg"
                alt="Natural landscape"
                fill
                className="object-cover object-[center_35%]"
              />
            </div>
            <div className="absolute inset-0 bg-gradient-to-t from-white/60 via-white/20 to-white/50 pointer-events-none" />

            {/* Clean Header */}
            <div className="relative z-10 text-center">
              <h3 className="font-display text-2xl font-bold text-zinc-950">
                Remembers every client &amp; rate
              </h3>
            </div>

            {/* Solid Grounded Client Dossier Card */}
            <div className="relative z-10 w-full max-w-[300px] mx-auto bg-white/95 backdrop-blur-md rounded-2xl p-4 border border-white shadow-[0px_14px_35px_rgba(0,0,0,0.12)] text-left my-auto">
              <div className="flex items-center justify-between mb-3 pb-2.5 border-b border-zinc-100">
                <div className="flex items-center gap-2.5">
                  <div className="w-8 h-8 rounded-full bg-zinc-950 text-white flex items-center justify-center text-xs font-bold">
                    RV
                  </div>
                  <div>
                    <h4 className="text-xs font-bold text-zinc-950">Rahul Verma</h4>
                    <p className="text-[10px] text-zinc-500">Commercial Studio Retainer</p>
                  </div>
                </div>
                <span className="px-2 py-0.5 bg-zinc-100 text-zinc-800 border border-zinc-200 rounded-full text-[9.5px] font-bold">
                  Client Profile
                </span>
              </div>

              <div className="space-y-2 text-[11px] text-zinc-700 font-medium">
                <div className="flex items-center justify-between">
                  <span className="text-zinc-500">Locked Rate:</span>
                  <span className="font-bold text-zinc-900">₹45,000 / shoot</span>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-zinc-500">State &amp; GSTIN:</span>
                  <span className="font-mono text-zinc-800 text-[10.5px]">Maharashtra (27)</span>
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

          {/* ── Card 3: Real Indian Business Payment QR Standee ── */}
          <div className="cap-card rounded-[32px] p-6 sm:p-7 border border-zinc-200/80 flex flex-col justify-between h-[470px] sm:h-[510px] relative overflow-hidden group hover:shadow-[0px_20px_45px_rgba(0,0,0,0.08)] transition-all duration-300 bg-zinc-950 text-white">
            
            {/* Background Atmosphere */}
            <div className="absolute inset-0 pointer-events-none select-none opacity-30 group-hover:opacity-40 transition-opacity">
              <Image
                src="/images/bento_gst_upi.jpg"
                alt="Commerce documentation"
                fill
                className="object-cover object-center"
              />
            </div>
            <div className="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/90 to-zinc-950/80 pointer-events-none" />

            {/* Clean Header (No redundant description) */}
            <div className="relative z-10 text-center">
              <h3 className="font-display text-2xl font-bold text-white">
                Engineered for Indian commerce
              </h3>
            </div>

            {/* Real Authentic High-Density Indian Payment QR Card */}
            <div className="relative z-10 w-full max-w-[280px] mx-auto bg-white text-zinc-950 rounded-2xl p-3.5 border border-zinc-200/80 shadow-[0px_16px_40px_rgba(0,0,0,0.35)] text-center my-auto">
              
              {/* Standee Merchant Header */}
              <div className="pb-2 border-b border-zinc-100 mb-2">
                <div className="flex items-center justify-center gap-1.5">
                  <span className="font-display text-xs font-bold text-zinc-950 uppercase tracking-wide">
                    DRAVYA CREATIVE STUDIO
                  </span>
                  <span className="w-3.5 h-3.5 rounded-full bg-blue-600 text-white flex items-center justify-center text-[8px] font-bold">✓</span>
                </div>
                {/* UPI Provider Badges */}
                <div className="flex items-center justify-center gap-1.5 mt-1.5 text-[9px] font-semibold text-zinc-700">
                  <span className="px-1.5 py-0.5 bg-zinc-100 rounded text-zinc-800">GPay</span>
                  <span className="px-1.5 py-0.5 bg-zinc-100 rounded text-zinc-800">PhonePe</span>
                  <span className="px-1.5 py-0.5 bg-zinc-100 rounded text-zinc-800">Paytm</span>
                  <span className="px-1.5 py-0.5 bg-zinc-100 rounded text-zinc-800">BHIM</span>
                </div>
              </div>

              {/* Realistic High-Density QR Code Matrix */}
              <div className="relative w-36 h-36 mx-auto bg-white p-2 border border-zinc-200 rounded-xl shadow-inner flex items-center justify-center">
                <svg className="w-full h-full" viewBox="0 0 105 105" fill="none" shapeRendering="crispEdges">
                  {/* Position Detection Patterns (Top-Left, Top-Right, Bottom-Left) */}
                  {/* Top-Left */}
                  <rect x="0" y="0" width="35" height="35" fill="#18181B" />
                  <rect x="5" y="5" width="25" height="25" fill="#FFFFFF" />
                  <rect x="10" y="10" width="15" height="15" fill="#18181B" />

                  {/* Top-Right */}
                  <rect x="70" y="0" width="35" height="35" fill="#18181B" />
                  <rect x="75" y="5" width="25" height="25" fill="#FFFFFF" />
                  <rect x="80" y="10" width="15" height="15" fill="#18181B" />

                  {/* Bottom-Left */}
                  <rect x="0" y="70" width="35" height="35" fill="#18181B" />
                  <rect x="5" y="75" width="25" height="25" fill="#FFFFFF" />
                  <rect x="10" y="80" width="15" height="15" fill="#18181B" />

                  {/* Timing Patterns */}
                  <rect x="35" y="30" width="5" height="5" fill="#18181B" />
                  <rect x="45" y="30" width="5" height="5" fill="#18181B" />
                  <rect x="55" y="30" width="5" height="5" fill="#18181B" />
                  <rect x="65" y="30" width="5" height="5" fill="#18181B" />

                  <rect x="30" y="35" width="5" height="5" fill="#18181B" />
                  <rect x="30" y="45" width="5" height="5" fill="#18181B" />
                  <rect x="30" y="55" width="5" height="5" fill="#18181B" />
                  <rect x="30" y="65" width="5" height="5" fill="#18181B" />

                  {/* Alignment Pattern (Bottom-Right) */}
                  <rect x="65" y="65" width="25" height="25" fill="#18181B" />
                  <rect x="70" y="70" width="15" height="15" fill="#FFFFFF" />
                  <rect x="75" y="75" width="5" height="5" fill="#18181B" />

                  {/* Realistic Dense Data Payload Blocks */}
                  <rect x="40" y="0" width="5" height="5" fill="#18181B" />
                  <rect x="50" y="0" width="5" height="5" fill="#18181B" />
                  <rect x="60" y="0" width="5" height="5" fill="#18181B" />

                  <rect x="40" y="10" width="10" height="5" fill="#18181B" />
                  <rect x="55" y="10" width="10" height="5" fill="#18181B" />

                  <rect x="40" y="20" width="5" height="5" fill="#18181B" />
                  <rect x="50" y="20" width="15" height="5" fill="#18181B" />

                  <rect x="0" y="40" width="5" height="10" fill="#18181B" />
                  <rect x="10" y="40" width="5" height="5" fill="#18181B" />
                  <rect x="20" y="40" width="5" height="15" fill="#18181B" />

                  <rect x="0" y="55" width="10" height="5" fill="#18181B" />
                  <rect x="15" y="55" width="5" height="5" fill="#18181B" />

                  <rect x="40" y="40" width="5" height="5" fill="#18181B" />
                  <rect x="50" y="40" width="10" height="5" fill="#18181B" />
                  <rect x="65" y="40" width="10" height="5" fill="#18181B" />
                  <rect x="80" y="40" width="10" height="5" fill="#18181B" />
                  <rect x="95" y="40" width="10" height="5" fill="#18181B" />

                  <rect x="35" y="50" width="10" height="5" fill="#18181B" />
                  <rect x="50" y="50" width="5" height="10" fill="#18181B" />
                  <rect x="60" y="50" width="15" height="5" fill="#18181B" />
                  <rect x="80" y="50" width="5" height="5" fill="#18181B" />
                  <rect x="90" y="50" width="15" height="5" fill="#18181B" />

                  <rect x="35" y="60" width="5" height="5" fill="#18181B" />
                  <rect x="45" y="60" width="10" height="5" fill="#18181B" />
                  <rect x="60" y="60" width="5" height="5" fill="#18181B" />
                  <rect x="95" y="60" width="10" height="5" fill="#18181B" />

                  <rect x="40" y="70" width="10" height="5" fill="#18181B" />
                  <rect x="55" y="70" width="5" height="15" fill="#18181B" />
                  <rect x="95" y="70" width="5" height="10" fill="#18181B" />

                  <rect x="40" y="80" width="5" height="10" fill="#18181B" />
                  <rect x="50" y="80" width="5" height="5" fill="#18181B" />
                  <rect x="95" y="85" width="10" height="5" fill="#18181B" />

                  <rect x="40" y="95" width="15" height="5" fill="#18181B" />
                  <rect x="60" y="95" width="10" height="5" fill="#18181B" />
                  <rect x="75" y="95" width="15" height="5" fill="#18181B" />
                  <rect x="95" y="95" width="10" height="5" fill="#18181B" />

                  <rect x="70" y="90" width="5" height="5" fill="#18181B" />
                  <rect x="85" y="90" width="15" height="5" fill="#18181B" />
                </svg>
                {/* Center UPI Badge */}
                <div className="absolute inset-0 m-auto w-7 h-7 rounded-lg bg-zinc-950 text-white flex items-center justify-center text-[11px] font-bold shadow-md border-2 border-white">
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
