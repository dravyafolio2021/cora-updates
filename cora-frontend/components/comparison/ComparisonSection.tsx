'use client';

import React, { useState, useEffect, useRef } from 'react';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import { X, Check } from 'lucide-react';
import { trackEvent } from '../analytics/Analytics';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

export function ComparisonSection() {
  const [activeTab, setActiveTab] = useState<'before' | 'after'>('before');
  const sectionRef = useRef<HTMLElement>(null);
  const containerRef = useRef<HTMLDivElement>(null);
  const dialRef = useRef<HTMLDivElement>(null);
  const knobRef = useRef<HTMLDivElement>(null);
  const beforeCardRef = useRef<HTMLDivElement>(null);
  const afterCardRef = useRef<HTMLDivElement>(null);

  // GSAP ScrollTrigger Parallax and Entrance Animations
  useEffect(() => {
    const ctx = gsap.context(() => {
      // 1. Entrance animation for the section header
      gsap.fromTo(
        '.comp-header',
        { y: 30, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.8,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: sectionRef.current,
            start: 'top 80%',
          },
        }
      );

      gsap.fromTo(
        containerRef.current,
        { y: 40, opacity: 0, scale: 0.98 },
        {
          y: 0,
          opacity: 1,
          scale: 1,
          duration: 0.9,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: sectionRef.current,
            start: 'top 75%',
          },
        }
      );

      // 2. Parallax floating effect on the rotary dial on scroll
      if (dialRef.current && sectionRef.current) {
        gsap.to(dialRef.current, {
          y: -14,
          ease: 'none',
          scrollTrigger: {
            trigger: sectionRef.current,
            start: 'top bottom',
            end: 'bottom top',
            scrub: 1.2,
          },
        });
      }

      // 3. ScrollTrigger to auto-flip to 'after' when scrolled past mid-section (if user hasn't toggled yet)
      ScrollTrigger.create({
        trigger: sectionRef.current,
        start: 'top 45%',
        onEnter: () => {
          setActiveTab('after');
        },
      });
    }, sectionRef);

    return () => ctx.revert();
  }, []);

  // GSAP animation when activeTab changes
  useEffect(() => {
    if (!knobRef.current) return;

    if (activeTab === 'before') {
      // Rotate dial to the left (-30deg)
      gsap.to(knobRef.current, {
        rotation: -30,
        duration: 0.6,
        ease: 'back.out(1.8)',
      });

      // Animate cards cross-fade
      if (beforeCardRef.current && afterCardRef.current) {
        gsap.to(afterCardRef.current, {
          opacity: 0,
          scale: 0.98,
          pointerEvents: 'none',
          duration: 0.35,
          ease: 'power2.inOut',
        });
        gsap.to(beforeCardRef.current, {
          opacity: 1,
          scale: 1,
          pointerEvents: 'auto',
          duration: 0.45,
          delay: 0.1,
          ease: 'power2.out',
        });
      }
    } else {
      // Rotate dial to the right (+30deg)
      gsap.to(knobRef.current, {
        rotation: 30,
        duration: 0.6,
        ease: 'back.out(1.8)',
      });

      // Animate cards cross-fade
      if (beforeCardRef.current && afterCardRef.current) {
        gsap.to(beforeCardRef.current, {
          opacity: 0,
          scale: 0.98,
          pointerEvents: 'none',
          duration: 0.35,
          ease: 'power2.inOut',
        });
        gsap.to(afterCardRef.current, {
          opacity: 1,
          scale: 1,
          pointerEvents: 'auto',
          duration: 0.45,
          delay: 0.1,
          ease: 'power2.out',
        });
      }
    }
  }, [activeTab]);

  const toggleTab = (tab: 'before' | 'after') => {
    setActiveTab(tab);
    trackEvent('comparison_toggle', { tab });
  };

  return (
    <section
      ref={sectionRef}
      id="comparison"
      className="py-20 sm:py-28 relative z-10 bg-[#FAFAFA] overflow-hidden border-b border-zinc-200/60"
    >
      <div className="w-full max-w-[1140px] mx-auto px-4 sm:px-6">
        
        {/* ── Section Header ── */}
        <div className="comp-header text-center max-w-[780px] mx-auto mb-16 sm:mb-20">
          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl font-semibold text-zinc-950 leading-[1.12] tracking-[-0.03em] mb-3.5">
            Smarter decisions start with clear data
          </h2>
          <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[580px] mx-auto">
            Compare how top studios run on autonomous Cora workflows versus fragmented spreadsheets.
          </p>
        </div>

        {/* ── Main Comparison Container with Rotary Dial Seam ── */}
        <div ref={containerRef} className="relative w-full max-w-[960px] mx-auto">
          
          {/* Top Control Tabs Bar with Central Rotary Dial */}
          <div className="relative flex items-center justify-between z-30 mb-[-1px]">
            
            {/* Left Tab: Before Cora */}
            <button
              type="button"
              onClick={() => toggleTab('before')}
              className={`flex-1 flex items-center justify-center py-4 px-6 sm:px-8 border-b border-r rounded-br-[24px] transition-all duration-300 cursor-pointer ${
                activeTab === 'before'
                  ? 'border-[#DDE5ED] bg-white text-zinc-900 font-semibold shadow-xs'
                  : 'border-transparent text-zinc-400 hover:text-zinc-600 font-medium'
              }`}
            >
              <span className="font-display text-sm sm:text-base tracking-tight">
                Before Cora
              </span>
            </button>

            {/* Central Rotary Switch Knob */}
            <div
              ref={dialRef}
              onClick={() => toggleTab(activeTab === 'before' ? 'after' : 'before')}
              className="relative z-40 -mt-7 sm:-mt-9 mx-2 sm:mx-4 shrink-0 cursor-pointer group select-none"
              title="Click to toggle Before / After"
            >
              {/* Outer Housing / Bezel */}
              <div className="w-[100px] sm:w-[124px] h-[115px] sm:h-[142px] relative flex items-center justify-center filter drop-shadow-[0px_8px_16px_rgba(0,0,0,0.18)] transition-transform duration-300 group-hover:scale-105">
                
                {/* Dial Base Frame Background */}
                <div className="absolute inset-0 bg-gradient-to-b from-[#2B3036] via-[#1A1D20] to-[#0D0F11] rounded-[48px] border border-white/20 shadow-inner flex flex-col items-center pt-2">
                  
                  {/* Top Pointer Indicator Notch */}
                  <div
                    className={`w-3.5 h-3.5 rotate-45 rounded-xs transition-colors duration-300 ${
                      activeTab === 'after'
                        ? 'bg-emerald-400 shadow-[0_0_10px_#10B981]'
                        : 'bg-zinc-400'
                    }`}
                  />
                </div>

                {/* Inner Rotating Disc */}
                <div
                  ref={knobRef}
                  className="relative z-10 w-[74px] sm:w-[94px] h-[74px] sm:h-[94px] mt-4 rounded-full flex items-center justify-center transition-shadow duration-500"
                  style={{
                    boxShadow:
                      activeTab === 'after'
                        ? '0px 0px 30px rgba(16, 185, 129, 0.65), 0px 4px 14px rgba(0, 0, 0, 0.4)'
                        : '0px 6px 16px rgba(0, 0, 0, 0.35)',
                  }}
                >
                  {/* Before State Face (Brushed Silver Dial) */}
                  <div
                    className={`absolute inset-0 rounded-full bg-gradient-to-tr from-[#D1D5DB] via-[#F3F4F6] to-[#9CA3AF] border-2 border-white/80 flex items-center justify-center transition-opacity duration-300 ${
                      activeTab === 'before' ? 'opacity-100' : 'opacity-0'
                    }`}
                  >
                    {/* Metal Cap Center */}
                    <div className="w-9 sm:w-11 h-9 sm:h-11 rounded-full bg-gradient-to-b from-[#E5E7EB] via-[#9CA3AF] to-[#6B7280] shadow-inner border border-white/40 flex items-center justify-center">
                      <div className="w-3.5 h-3.5 rounded-full bg-zinc-700/60 shadow-xs" />
                    </div>
                  </div>

                  {/* After State Face (Glowing Emerald / Cora Green Emblem Face) */}
                  <div
                    className={`absolute inset-0 rounded-full bg-gradient-to-tr from-[#059669] via-[#10B981] to-[#34D399] border-2 border-emerald-200/90 flex items-center justify-center transition-opacity duration-300 ${
                      activeTab === 'after' ? 'opacity-100' : 'opacity-0'
                    }`}
                  >
                    {/* Glowing Cora Icon Center */}
                    <div className="w-9 sm:w-11 h-9 sm:h-11 rounded-full bg-zinc-950/90 shadow-[0_0_16px_rgba(255,255,255,0.4)] border border-emerald-300/40 flex items-center justify-center">
                      <div className="text-white font-bold font-mono text-xs tracking-tighter">
                        &lt; &gt;
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>

            {/* Right Tab: After Cora */}
            <button
              type="button"
              onClick={() => toggleTab('after')}
              className={`flex-1 flex items-center justify-center py-4 px-6 sm:px-8 border-b border-l rounded-bl-[24px] transition-all duration-300 cursor-pointer ${
                activeTab === 'after'
                  ? 'border-[#DDE5ED] bg-white text-zinc-900 font-semibold shadow-xs'
                  : 'border-transparent text-zinc-400 hover:text-zinc-600 font-medium'
              }`}
            >
              <span className="font-display text-sm sm:text-base tracking-tight">
                After Cora
              </span>
            </button>

          </div>


          {/* ── Cards Viewport ── */}
          <div className="relative min-h-[460px] sm:min-h-[420px] w-full">
            
            {/* ══════════════════════════════════════════════════════════════════
                CARD 1: BEFORE CORA (Light Slate Viewport)
               ══════════════════════════════════════════════════════════════════ */}
            <div
              ref={beforeCardRef}
              className={`w-full rounded-[30px] p-2.5 sm:p-3 transition-all duration-300 ${
                activeTab === 'before'
                  ? 'relative z-10 opacity-100 pointer-events-auto'
                  : 'absolute inset-0 z-0 opacity-0 pointer-events-none'
              }`}
            >
              <div className="w-full bg-[#EDF1F4] rounded-[24px] p-6 sm:p-10 border border-zinc-200/80 shadow-[0px_10px_30px_rgba(0,0,0,0.04)]">
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                  
                  {/* Left: Pain Points List */}
                  <div className="lg:col-span-7 space-y-6">
                    <h3 className="font-display text-2xl sm:text-[28px] font-semibold text-zinc-900 tracking-tight leading-snug">
                      Challenges of managing operations today
                    </h3>

                    <div className="space-y-3.5">
                      <div className="flex items-start gap-3 text-xs sm:text-sm text-zinc-700 font-normal leading-relaxed">
                        <div className="w-5 h-5 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0 mt-0.5 border border-red-200">
                          <X className="w-3.5 h-3.5 stroke-[2.4]" />
                        </div>
                        <span>Financial data is spread across multiple platforms and is hard to understand</span>
                      </div>

                      <div className="flex items-start gap-3 text-xs sm:text-sm text-zinc-700 font-normal leading-relaxed">
                        <div className="w-5 h-5 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0 mt-0.5 border border-red-200">
                          <X className="w-3.5 h-3.5 stroke-[2.4]" />
                        </div>
                        <span>Lack of clear direction for shoot scheduling, hold dates, or crew allocation</span>
                      </div>

                      <div className="flex items-start gap-3 text-xs sm:text-sm text-zinc-700 font-normal leading-relaxed">
                        <div className="w-5 h-5 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0 mt-0.5 border border-red-200">
                          <X className="w-3.5 h-3.5 stroke-[2.4]" />
                        </div>
                        <span>Tracking invoices, 18% GST splits, and client retainers manually takes endless time</span>
                      </div>

                      <div className="flex items-start gap-3 text-xs sm:text-sm text-zinc-700 font-normal leading-relaxed">
                        <div className="w-5 h-5 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0 mt-0.5 border border-red-200">
                          <X className="w-3.5 h-3.5 stroke-[2.4]" />
                        </div>
                        <span>Business decisions based on incomplete spreadsheets and missed WhatsApp messages</span>
                      </div>
                    </div>
                  </div>

                  {/* Right: Stat Cards (Light Red Accent) */}
                  <div className="lg:col-span-5 flex flex-col gap-4">
                    
                    {/* Stat 1 */}
                    <div className="bg-red-500/[0.05] border border-red-500/20 rounded-[20px] p-5 sm:p-6 transition-transform duration-300 hover:scale-[1.02]">
                      <div className="font-display text-4xl sm:text-5xl font-bold text-zinc-900 tracking-tight mb-1">
                        68%
                      </div>
                      <p className="text-zinc-600 text-xs sm:text-sm font-medium">
                        Financial data confusion &amp; administrative overload
                      </p>
                    </div>

                    {/* Stat 2 */}
                    <div className="bg-red-500/[0.05] border border-red-500/20 rounded-[20px] p-5 sm:p-6 transition-transform duration-300 hover:scale-[1.02]">
                      <div className="font-display text-4xl sm:text-5xl font-bold text-zinc-900 tracking-tight mb-1">
                        55%
                      </div>
                      <p className="text-zinc-600 text-xs sm:text-sm font-medium">
                        Poor data understanding &amp; delayed client collections
                      </p>
                    </div>

                  </div>

                </div>
              </div>
            </div>


            {/* ══════════════════════════════════════════════════════════════════
                CARD 2: AFTER CORA (Dark Obsidian Viewport)
               ══════════════════════════════════════════════════════════════════ */}
            <div
              ref={afterCardRef}
              className={`w-full rounded-[30px] p-2.5 sm:p-3 transition-all duration-300 ${
                activeTab === 'after'
                  ? 'relative z-10 opacity-100 pointer-events-auto'
                  : 'absolute inset-0 z-0 opacity-0 pointer-events-none'
              }`}
            >
              <div className="relative overflow-hidden w-full bg-[#0B0F12] rounded-[24px] p-6 sm:p-10 border border-zinc-800 shadow-[0px_16px_40px_rgba(0,0,0,0.35)]">
                
                {/* Subtle Ambient Meadow / Dark Atmosphere Background Glow */}
                <div
                  className="absolute inset-0 pointer-events-none opacity-25"
                  style={{
                    background:
                      'radial-gradient(ellipse at 80% 90%, rgba(16, 185, 129, 0.25) 0%, transparent 60%), radial-gradient(ellipse at 20% 10%, rgba(52, 211, 153, 0.15) 0%, transparent 50%)',
                  }}
                />

                <div className="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                  
                  {/* Left: Solution Capabilities List */}
                  <div className="lg:col-span-7 space-y-6">
                    <h3 className="font-display text-2xl sm:text-[28px] font-semibold text-white tracking-tight leading-snug">
                      Smarter way to manage your business
                    </h3>

                    <div className="space-y-3.5">
                      <div className="flex items-start gap-3 text-xs sm:text-sm text-zinc-300 font-normal leading-relaxed">
                        <div className="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 mt-0.5 border border-emerald-500/30">
                          <Check className="w-3.5 h-3.5 stroke-[2.4]" />
                        </div>
                        <span>Get clear AI recommendations and multi-model routing based on real-time data</span>
                      </div>

                      <div className="flex items-start gap-3 text-xs sm:text-sm text-zinc-300 font-normal leading-relaxed">
                        <div className="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 mt-0.5 border border-emerald-500/30">
                          <Check className="w-3.5 h-3.5 stroke-[2.4]" />
                        </div>
                        <span>Understand risks and margins before making client commitments or crew bookings</span>
                      </div>

                      <div className="flex items-start gap-3 text-xs sm:text-sm text-zinc-300 font-normal leading-relaxed">
                        <div className="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 mt-0.5 border border-emerald-500/30">
                          <Check className="w-3.5 h-3.5 stroke-[2.4]" />
                        </div>
                        <span>Monitor your full portfolio, 18% GST invoices, and UPI payouts with zero manual effort</span>
                      </div>

                      <div className="flex items-start gap-3 text-xs sm:text-sm text-zinc-300 font-normal leading-relaxed">
                        <div className="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 mt-0.5 border border-emerald-500/30">
                          <Check className="w-3.5 h-3.5 stroke-[2.4]" />
                        </div>
                        <span>Make consistent, informed business choices backed by automated Google SEO indexation</span>
                      </div>
                    </div>
                  </div>

                  {/* Right: Stat Cards (Dark Emerald Glow) */}
                  <div className="lg:col-span-5 flex flex-col gap-4">
                    
                    {/* Stat 1 */}
                    <div className="bg-zinc-900/90 border border-emerald-500/30 rounded-[20px] p-5 sm:p-6 shadow-[0px_10px_24px_rgba(16,185,129,0.12)] transition-transform duration-300 hover:scale-[1.02]">
                      <div className="font-display text-4xl sm:text-5xl font-bold text-white tracking-tight mb-1">
                        3X Faster
                      </div>
                      <p className="text-zinc-400 text-xs sm:text-sm font-medium">
                        Smart decisions &amp; project turnaround
                      </p>
                    </div>

                    {/* Stat 2 */}
                    <div className="bg-zinc-900/90 border border-emerald-500/30 rounded-[20px] p-5 sm:p-6 shadow-[0px_10px_24px_rgba(16,185,129,0.12)] transition-transform duration-300 hover:scale-[1.02]">
                      <div className="font-display text-4xl sm:text-5xl font-bold text-white tracking-tight mb-1">
                        24/7
                      </div>
                      <p className="text-zinc-400 text-xs sm:text-sm font-medium">
                        Real-time tracking &amp; autonomous dispatch
                      </p>
                    </div>

                  </div>

                </div>
              </div>
            </div>

          </div>

        </div>

      </div>
    </section>
  );
}

