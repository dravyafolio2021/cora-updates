'use client';

import React, { useEffect, useRef } from 'react';
import Image from 'next/image';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import { ArrowRight, Check, Sparkles, MessageSquare, QrCode } from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

export function FounderValueShowcase() {
  const sectionRef = useRef<HTMLElement>(null);
  const shieldRef = useRef<HTMLDivElement>(null);
  const meadowBgRef = useRef<HTMLDivElement>(null);
  const paidRef = useRef<HTMLDivElement>(null);

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

      // 3. Subtle Floating Sine-Wave Motion on 3D Shield (Card 1)
      if (shieldRef.current) {
        gsap.to(shieldRef.current, {
          y: -8,
          rotation: -1.2,
          duration: 3.2,
          repeat: -1,
          yoyo: true,
          ease: 'sine.inOut',
        });
      }

      // 4. Subtle 3D Floating Pulse on Metallic PAID (Card 3)
      if (paidRef.current) {
        gsap.to(paidRef.current, {
          y: -6,
          scale: 1.025,
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
        
        {/* ── 1. Section Header ── */}
        <div className="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-12 sm:mb-16">
          <div className="max-w-[640px]">
            {/* Top Pill */}
            <div className="inline-flex items-center gap-1.5 px-3.5 py-1 bg-zinc-100 rounded-full text-zinc-800 text-xs font-semibold uppercase tracking-wider mb-4 border border-zinc-200/80">
              <Sparkles className="w-3.5 h-3.5 text-zinc-950" />
              <span>THE AI CO-FOUNDER</span>
            </div>

            {/* Display Headline */}
            <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl lg:text-[46px] font-bold text-zinc-950 leading-[1.14] lg:leading-[1.12] tracking-[-0.03em]">
              Stop juggling 10 disconnected apps.
            </h2>
          </div>

          {/* Right Subtitle & Action Button */}
          <div className="max-w-[420px] flex flex-col items-start lg:items-end text-left lg:text-right gap-4">
            <p className="text-zinc-600 text-sm sm:text-base leading-relaxed font-normal">
              Cora knows your business context natively. You never have to re-explain who your clients are or what you sell.
            </p>
            <a
              href="https://app.heycora.in/workspace/login"
              onClick={handleCtaClick}
              className="inline-flex items-center gap-2 bg-zinc-950 text-white hover:bg-zinc-800 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold shadow-2xs transition-all hover:-translate-y-0.5"
            >
              <span>Get started for free</span>
              <ArrowRight className="w-3.5 h-3.5" />
            </a>
          </div>
        </div>

        {/* ── 2. Three Focused Value Cards ── */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-7">
          
          {/* ── Card 1: Natural Language Actions ── */}
          <div className="cap-card bg-zinc-50 rounded-[32px] p-6 sm:p-7 border border-zinc-200/80 flex flex-col justify-between h-[440px] sm:h-[480px] relative overflow-hidden group hover:shadow-[0px_20px_45px_rgba(0,0,0,0.06)] transition-all duration-300">
            <div>
              <h3 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 text-center mb-1.5">
                Just ask in plain words
              </h3>
              <p className="text-xs text-zinc-600 text-center max-w-[250px] mx-auto leading-relaxed">
                Type &ldquo;Make a ₹15,000 invoice for Rahul with 18% GST&rdquo; — Cora handles the math and sends it instantly.
              </p>
            </div>

            {/* Center 3D Blue Shield Graphic */}
            <div className="relative my-auto flex items-center justify-center">
              <div
                ref={shieldRef}
                className="w-32 h-36 relative flex items-center justify-center drop-shadow-[0px_18px_30px_rgba(37,99,235,0.28)] will-change-transform"
              >
                <svg className="w-full h-full" viewBox="0 0 100 120" fill="none">
                  <path
                    d="M50 5L15 20V55C15 85 50 115 50 115C50 115 85 85 85 55V20L50 5Z"
                    fill="url(#blue_shield_grad)"
                    stroke="rgba(255,255,255,0.7)"
                    strokeWidth="2"
                  />
                  <defs>
                    <linearGradient id="blue_shield_grad" x1="50" y1="5" x2="50" y2="115" gradientUnits="userSpaceOnUse">
                      <stop stopColor="#60A5FA" />
                      <stop offset="0.45" stopColor="#2563EB" />
                      <stop offset="1" stopColor="#1D4ED8" />
                    </linearGradient>
                  </defs>
                </svg>
                <div className="absolute inset-0 flex items-center justify-center text-white">
                  <div className="w-12 h-12 rounded-full bg-white/20 backdrop-blur-md border border-white/50 flex items-center justify-center shadow-inner">
                    <Check className="w-6 h-6 stroke-[3] text-white" />
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
                Plain English &amp; Hinglish
              </span>
            </div>
          </div>

          {/* ── Card 2: Native Business Memory ── */}
          <div className="cap-card rounded-[32px] p-6 sm:p-7 border border-zinc-200/80 flex flex-col justify-between h-[440px] sm:h-[480px] relative overflow-hidden group hover:shadow-[0px_20px_45px_rgba(0,0,0,0.08)] transition-all duration-300">
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
            
            <div className="absolute inset-0 bg-gradient-to-t from-white/40 via-transparent to-white/30 pointer-events-none" />

            <div className="relative z-10">
              <h3 className="font-display text-xl sm:text-2xl font-bold text-zinc-950 text-center mb-1.5">
                Remembers your entire business
              </h3>
              <p className="text-xs text-zinc-700 text-center max-w-[250px] mx-auto leading-relaxed">
                Knows your client rates, past payments, and service packages. No copy-pasting context every morning.
              </p>
            </div>

            {/* Center Floating Glass Card */}
            <div className="relative z-10 my-auto bg-white/90 backdrop-blur-md rounded-2xl p-4 border border-white/95 shadow-[0px_14px_35px_rgba(0,0,0,0.12)]">
              <div className="flex items-center justify-between mb-3">
                <div className="flex items-center gap-1.5">
                  <span className="px-2 py-0.5 bg-emerald-500 text-white rounded-full text-[10px] font-bold">
                    Active
                  </span>
                  <span className="text-xs font-bold text-zinc-900">Client Memory</span>
                </div>
                <span className="text-[10px] text-zinc-500 font-mono">Synced</span>
              </div>

              <div className="h-16 w-full relative">
                <svg className="w-full h-full overflow-visible" viewBox="0 0 200 60" preserveAspectRatio="none">
                  <path
                    d="M0,45 C30,40 50,50 80,30 C110,10 130,5 150,25 C170,45 185,15 200,10"
                    fill="none"
                    stroke="#2563EB"
                    strokeWidth="3.2"
                    strokeLinecap="round"
                  />
                  <circle cx="130" cy="10" r="5" fill="#18181B" stroke="#ffffff" strokeWidth="2.5" />
                </svg>
              </div>

              <div className="flex items-center justify-between pt-2 border-t border-zinc-200/60 text-[10px] text-zinc-600 font-medium">
                <span>Rate History: Saved</span>
                <span>Active Leads: 12</span>
              </div>
            </div>

            {/* Bottom Status Row */}
            <div className="relative z-10 flex items-center justify-center">
              <span className="text-[11px] font-semibold bg-white/95 backdrop-blur-md border border-white text-zinc-900 px-4 py-1.5 rounded-full shadow-2xs">
                Zero Re-Explaining
              </span>
            </div>
          </div>

          {/* ── Card 3: WhatsApp & Payment Ready ── */}
          <div className="cap-card bg-zinc-950 rounded-[32px] p-6 sm:p-7 border border-zinc-800 flex flex-col justify-between h-[440px] sm:h-[480px] relative overflow-hidden group hover:shadow-[0px_20px_45px_rgba(0,0,0,0.25)] transition-all duration-300 text-white">
            <div>
              <h3 className="font-display text-xl sm:text-2xl font-bold text-white text-center mb-1.5">
                Built for Indian commerce
              </h3>
              <p className="text-xs text-zinc-400 text-center max-w-[250px] mx-auto leading-relaxed">
                Generate instant UPI payment links, share receipts directly on WhatsApp, and track cash flow in real-time.
              </p>
            </div>

            {/* Center Big Bold 3D Metallic Typography */}
            <div
              ref={paidRef}
              className="relative my-auto flex flex-col items-center justify-center select-none will-change-transform"
            >
              <div className="font-display text-5xl sm:text-6xl font-bold tracking-tight bg-gradient-to-b from-[#F5D491] via-[#D8A756] to-[#8C6221] bg-clip-text text-transparent drop-shadow-[0px_6px_20px_rgba(216,167,86,0.38)]">
                PAID
              </div>
              <div className="font-display text-5xl sm:text-6xl font-bold tracking-tight bg-gradient-to-t from-transparent via-[#D8A756]/20 to-transparent bg-clip-text text-transparent transform scale-y-[-0.6] opacity-40 blur-[1px] -mt-3 pointer-events-none">
                PAID
              </div>
            </div>

            {/* Bottom Tag */}
            <div className="flex flex-col items-center text-center gap-2">
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
