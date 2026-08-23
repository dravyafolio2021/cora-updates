'use client';

import React, { useEffect, useRef } from 'react';
import Image from 'next/image';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import {
  Sparkles,
  ArrowRight,
  MessageSquare,
  ShieldCheck,
  Receipt,
  Globe,
  LayoutDashboard,
  CheckCircle2,
  Lock,
  Zap,
  Calendar,
  CreditCard,
} from 'lucide-react';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

export function FounderValueShowcase() {
  const sectionRef = useRef<HTMLElement>(null);

  useEffect(() => {
    const ctx = gsap.context(() => {
      gsap.fromTo(
        '.arch-card',
        { y: 35, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.7,
          stagger: 0.15,
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

  return (
    <section
      id="native-architecture"
      ref={sectionRef}
      className="py-20 sm:py-28 bg-[#FFFFFF] relative z-10 overflow-hidden border-b border-zinc-100"
    >
      <div className="w-full max-w-[1280px] mx-auto px-4 sm:px-6">
        
        {/* ── 1. Section Header ── */}
        <div className="max-w-[840px] mx-auto text-center mb-14 sm:mb-20">
          <div className="inline-flex items-center gap-1.5 px-3.5 py-1 bg-zinc-100 rounded-full text-zinc-800 text-xs font-semibold uppercase tracking-wider mb-4 border border-zinc-200/80 shadow-2xs">
            <Sparkles className="w-3.5 h-3.5 text-zinc-950" />
            <span>NATIVE DUAL-INTERFACE ARCHITECTURE</span>
          </div>

          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl lg:text-[48px] font-bold text-zinc-950 leading-[1.1] tracking-[-0.03em] mb-4">
            One AI Co-Founder. Connected from your <br className="hidden sm:inline" />
            Client&apos;s Screen to your Studio&apos;s Engine.
          </h2>
          <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[660px] mx-auto">
            Traditional tools force you to jump between website forms, WhatsApp chats, and accounting software. Cora bridges your customer-facing presence and your back-office execution in one shared brain.
          </p>
        </div>

        {/* ── 2. The Dual-Interface Architecture Showcase ── */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-10 mb-12">
          
          {/* ── Left Pillar: Customer-Facing Website & AI Concierge ── */}
          <div className="arch-card bg-[#FBFBFC] rounded-[36px] p-7 sm:p-10 border border-zinc-200/80 shadow-[0px_10px_30px_rgba(0,0,0,0.03)] flex flex-col justify-between relative overflow-hidden group hover:border-zinc-300 transition-all duration-300">
            
            <div>
              {/* Pillar Tag */}
              <div className="flex items-center justify-between mb-6">
                <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 border border-emerald-200/60 text-emerald-800 text-xs font-bold">
                  <Globe className="w-3.5 h-3.5 text-emerald-600" />
                  <span>Customer-Facing Interface</span>
                </div>
                <span className="text-[11px] font-semibold text-zinc-400">24/7 AI Concierge</span>
              </div>

              <h3 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight mb-2">
                Converts visitors into booked holds without human delay
              </h3>
              <p className="text-zinc-600 text-sm leading-relaxed mb-6">
                Your portfolio website &amp; WhatsApp link powered by an AI concierge that knows your approved rate cards, answers technical questions, and collects project briefs.
              </p>

              {/* Realistic Customer-Facing Interaction Card */}
              <div className="bg-white rounded-2xl p-5 border border-zinc-200/90 shadow-[0px_8px_24px_rgba(0,0,0,0.04)] space-y-3">
                <div className="flex items-center justify-between pb-2.5 border-b border-zinc-100">
                  <div className="flex items-center gap-2">
                    <div className="w-6 h-6 rounded-full bg-[#25D366] text-white flex items-center justify-center">
                      <MessageSquare className="w-3.5 h-3.5" />
                    </div>
                    <span className="text-xs font-bold text-zinc-900">WhatsApp &amp; Web Concierge</span>
                  </div>
                  <span className="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                    Instant Reply
                  </span>
                </div>

                <div className="space-y-2 text-xs">
                  <div className="p-2.5 bg-zinc-50 rounded-xl text-zinc-800">
                    <div className="text-[10px] text-zinc-400 font-medium mb-0.5">Client (Lakme Campaign)</div>
                    &ldquo;Can you shoot a 2-day commercial video in Mumbai next week? What are the charges with 18% GST?&rdquo;
                  </div>
                  <div className="p-2.5 bg-emerald-50/70 border border-emerald-100 rounded-xl text-emerald-950 space-y-1">
                    <div className="text-[10px] text-emerald-700 font-bold flex items-center gap-1">
                      <Sparkles className="w-3 h-3" />
                      <span>Cora AI Response (0.8s)</span>
                    </div>
                    <p className="text-[11.5px] leading-relaxed">
                      &ldquo;Yes! Oct 24-25 are available on hold. For a 2-day 4K commercial production, our rate is <strong>₹1,20,000 + 18% GST (₹21,600)</strong> = <strong>₹1,41,600</strong> total. Would you like me to lock the dates?&rdquo;
                    </p>
                  </div>
                </div>

                <div className="pt-1 flex items-center justify-between text-[11px] font-semibold text-zinc-500">
                  <span>Captures brief &amp; client contact</span>
                  <span className="text-emerald-700 font-bold">✓ Hold Registered</span>
                </div>
              </div>
            </div>

            {/* Bottom Capability Tags */}
            <div className="pt-6 mt-6 border-t border-zinc-200/60 flex items-center gap-2 flex-wrap text-xs text-zinc-700">
              <span className="px-2.5 py-1 rounded-lg bg-zinc-100 font-medium">WhatsApp Sync</span>
              <span className="px-2.5 py-1 rounded-lg bg-zinc-100 font-medium">Rate Card Intelligence</span>
              <span className="px-2.5 py-1 rounded-lg bg-zinc-100 font-medium">Auto-Intake Briefs</span>
            </div>

          </div>

          {/* ── Right Pillar: Admin-Facing Workspace & Operating Engine ── */}
          <div className="arch-card bg-[#FBFBFC] rounded-[36px] p-7 sm:p-10 border border-zinc-200/80 shadow-[0px_10px_30px_rgba(0,0,0,0.03)] flex flex-col justify-between relative overflow-hidden group hover:border-zinc-300 transition-all duration-300">
            
            <div>
              {/* Pillar Tag */}
              <div className="flex items-center justify-between mb-6">
                <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-50 border border-indigo-200/60 text-indigo-800 text-xs font-bold">
                  <LayoutDashboard className="w-3.5 h-3.5 text-indigo-600" />
                  <span>Admin-Facing Interface</span>
                </div>
                <span className="text-[11px] font-semibold text-zinc-400">Workspace OS</span>
              </div>

              <h3 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 tracking-tight mb-2">
                Executes legal contracts, 18% GST billing &amp; schedules
              </h3>
              <p className="text-zinc-600 text-sm leading-relaxed mb-6">
                The moment a client brief is captured, your admin workspace instantly prepares the commercial agreement, outputs a dynamic UPI payment QR, and updates your calendar.
              </p>

              {/* Realistic Admin Workspace Execution Card */}
              <div className="bg-white rounded-2xl p-5 border border-zinc-200/90 shadow-[0px_8px_24px_rgba(0,0,0,0.04)] space-y-3">
                <div className="flex items-center justify-between pb-2.5 border-b border-zinc-100">
                  <div className="flex items-center gap-2">
                    <div className="w-6 h-6 rounded-full bg-zinc-950 text-white flex items-center justify-center">
                      <Receipt className="w-3.5 h-3.5 text-zinc-200" />
                    </div>
                    <span className="text-xs font-bold text-zinc-900">Live Operating Ledger</span>
                  </div>
                  <span className="text-[10px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full">
                    Auto-Generated
                  </span>
                </div>

                <div className="grid grid-cols-2 gap-2.5 text-xs">
                  <div className="p-3 bg-zinc-50 rounded-xl space-y-1">
                    <div className="flex items-center gap-1 text-[10.5px] font-bold text-zinc-800">
                      <ShieldCheck className="w-3 h-3 text-sky-600" />
                      <span>Commercial NDA</span>
                    </div>
                    <div className="text-[10px] text-emerald-700 font-semibold">✓ Ready for E-Sign</div>
                  </div>

                  <div className="p-3 bg-zinc-50 rounded-xl space-y-1">
                    <div className="flex items-center gap-1 text-[10.5px] font-bold text-zinc-800">
                      <CreditCard className="w-3 h-3 text-emerald-600" />
                      <span>18% GST Bill</span>
                    </div>
                    <div className="text-[10px] text-zinc-900 font-mono font-bold">₹1,41,600 UPI QR</div>
                  </div>
                </div>

                <div className="p-2.5 bg-indigo-50/60 border border-indigo-100 rounded-xl flex items-center justify-between text-xs">
                  <div className="flex items-center gap-2">
                    <Calendar className="w-3.5 h-3.5 text-indigo-600" />
                    <span className="font-semibold text-indigo-950 text-[11px]">Hold Protected: Oct 24-25</span>
                  </div>
                  <span className="text-[10px] font-bold text-indigo-800 bg-white px-2 py-0.5 rounded shadow-2xs">
                    Locked
                  </span>
                </div>

                <div className="pt-1 flex items-center justify-between text-[11px] font-semibold text-zinc-500">
                  <span>Syncs to Tally Prime &amp; GSTR-1</span>
                  <span className="text-indigo-950 font-bold">1-Click Dispatch &rarr;</span>
                </div>
              </div>
            </div>

            {/* Bottom Capability Tags */}
            <div className="pt-6 mt-6 border-t border-zinc-200/60 flex items-center gap-2 flex-wrap text-xs text-zinc-700">
              <span className="px-2.5 py-1 rounded-lg bg-zinc-100 font-medium">SHA-256 E-Sign</span>
              <span className="px-2.5 py-1 rounded-lg bg-zinc-100 font-medium">Dynamic UPI Standee</span>
              <span className="px-2.5 py-1 rounded-lg bg-zinc-100 font-medium">GSTR-1 Tax Engine</span>
            </div>

          </div>

        </div>

        {/* ── 3. The Shared AI Context Banner ── */}
        <div className="bg-zinc-950 text-white rounded-[32px] p-6 sm:p-8 lg:p-10 flex flex-col md:flex-row items-center justify-between gap-6 shadow-xl border border-zinc-800">
          <div className="space-y-1 text-center md:text-left">
            <div className="inline-flex items-center gap-1.5 text-emerald-400 text-xs font-semibold uppercase tracking-wider mb-1">
              <Zap className="w-3.5 h-3.5" />
              <span>Zero Manual Data Entry</span>
            </div>
            <h4 className="font-display text-xl sm:text-2xl font-bold text-white tracking-tight">
              One shared brain connecting your leads, legal agreements, and bank settlement.
            </h4>
            <p className="text-zinc-400 text-xs sm:text-sm leading-relaxed max-w-[620px]">
              When a client confirms on your website or WhatsApp, your workspace handles the math, legal clauses, and booking slots automatically.
            </p>
          </div>

          <a
            href="https://app.heycora.in/workspace/login"
            className="inline-flex items-center gap-2 bg-white text-zinc-950 hover:bg-zinc-100 px-6 py-3 rounded-xl text-xs sm:text-sm font-bold shadow-sm transition-all hover:-translate-y-0.5 shrink-0"
          >
            <span>Start Free Workspace</span>
            <ArrowRight className="w-4 h-4" />
          </a>
        </div>

      </div>
    </section>
  );
}
