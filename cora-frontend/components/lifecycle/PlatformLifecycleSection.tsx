'use client';

import React, { useEffect, useRef } from 'react';
import Image from 'next/image';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import {
  FileText,
  Folder,
  CheckCircle2,
  ChevronDown,
  Hash,
  Inbox,
  MessageSquare,
  ShieldCheck,
  CreditCard,
  Sparkles,
  Calendar,
  Clock,
  TrendingUp,
  ArrowRight,
  HelpCircle,
  Zap,
} from 'lucide-react';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

export function PlatformLifecycleSection() {
  const sectionRef = useRef<HTMLElement>(null);

  useEffect(() => {
    const ctx = gsap.context(() => {
      gsap.fromTo(
        '.cora-bento-card',
        { y: 30, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.65,
          stagger: 0.08,
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
      id="how-it-works"
      ref={sectionRef}
      className="py-20 sm:py-28 bg-[#FAFAFA] relative z-10 overflow-hidden border-b border-zinc-200/60"
    >
      <div className="w-full max-w-[1280px] mx-auto px-4 sm:px-6">
        
        {/* ── Section Header ── */}
        <div className="max-w-[780px] mx-auto text-center mb-10 sm:mb-14">
          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl font-bold text-zinc-950 leading-[1.12] tracking-[-0.03em] mb-3.5">
            One chat bar. Every business task.
          </h2>
          <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[600px] mx-auto">
            No switching between CRM tabs, accounting apps, and marketing tools.
          </p>
        </div>

        {/* ── Visual Illustration: The Tangled Disconnected Stack vs Unified Cora ── */}
        <div className="w-full max-w-[1040px] mx-auto mb-14 sm:mb-18 bg-white border border-zinc-200/80 rounded-3xl p-6 sm:p-8 shadow-[0px_10px_30px_rgba(0,0,0,0.03)] relative overflow-hidden">
          
          {/* Subtle Background Mesh SVG Ribbon */}
          <div className="absolute inset-0 pointer-events-none opacity-40">
            <svg className="w-full h-full" viewBox="0 0 1000 200" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                d="M 50 100 C 150 20, 250 180, 380 100 C 500 20, 600 180, 720 100 C 850 30, 920 120, 980 100"
                stroke="#E4E4E7"
                strokeWidth="18"
                strokeLinecap="round"
                fill="none"
              />
              <path
                d="M 60 110 C 160 170, 260 30, 390 110 C 510 170, 610 30, 730 110 C 840 160, 910 80, 970 110"
                stroke="#F4F4F5"
                strokeWidth="12"
                strokeLinecap="round"
                fill="none"
              />
            </svg>
          </div>

          <div className="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-6 lg:gap-8">
            
            {/* Left: Fragmented App Nodes & Chaos Bubbles */}
            <div className="flex-1 w-full flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6">
              
              {/* Node 1 */}
              <div className="flex flex-col items-center gap-2">
                <div className="flex items-center gap-2 bg-zinc-50 border border-zinc-200/80 rounded-2xl p-2.5 shadow-xs">
                  <div className="w-7 h-7 rounded-lg bg-[#25D366] text-white flex items-center justify-center font-bold text-xs shadow-xs">
                    <MessageSquare className="w-4 h-4 text-white" />
                  </div>
                  <div className="w-7 h-7 rounded-lg bg-[#107C41] text-white flex items-center justify-center font-bold text-xs shadow-xs">
                    <FileText className="w-4 h-4 text-white" />
                  </div>
                  <div className="w-7 h-7 rounded-lg bg-[#0066FF] text-white flex items-center justify-center font-bold text-xs shadow-xs">
                    <ShieldCheck className="w-4 h-4 text-white" />
                  </div>
                </div>
                <div className="px-3 py-1 bg-zinc-100/90 border border-zinc-200/60 rounded-full text-[11px] font-medium text-zinc-600 shadow-2xs">
                  &ldquo;Where is the advance invoice?&rdquo;
                </div>
              </div>

              {/* Connecting Icon */}
              <div className="hidden sm:flex items-center justify-center text-zinc-300">
                <ArrowRight className="w-5 h-5" />
              </div>

              {/* Node 2 */}
              <div className="flex flex-col items-center gap-2">
                <div className="flex items-center gap-2 bg-zinc-50 border border-zinc-200/80 rounded-2xl p-2.5 shadow-xs">
                  <div className="w-7 h-7 rounded-lg bg-[#5F259F] text-white flex items-center justify-center font-bold text-xs shadow-xs">
                    <CreditCard className="w-4 h-4 text-white" />
                  </div>
                  <div className="w-7 h-7 rounded-lg bg-[#4285F4] text-white flex items-center justify-center font-bold text-xs shadow-xs">
                    <Folder className="w-4 h-4 text-white" />
                  </div>
                  <div className="w-7 h-7 rounded-lg bg-[#EA4335] text-white flex items-center justify-center font-bold text-xs shadow-xs">
                    <Calendar className="w-4 h-4 text-white" />
                  </div>
                </div>
                <div className="px-3 py-1 bg-zinc-100/90 border border-zinc-200/60 rounded-full text-[11px] font-medium text-zinc-600 shadow-2xs">
                  &ldquo;Did they e-sign the model NDA?&rdquo;
                </div>
              </div>

            </div>

            {/* Right: Unified Single Cora Prompt Resolution */}
            <div className="w-full lg:w-[420px] bg-zinc-950 text-white rounded-2xl p-4 sm:p-5 shadow-[0px_16px_36px_rgba(0,0,0,0.2)] border border-zinc-800 space-y-2.5 shrink-0">
              <div className="flex items-center justify-between text-xs font-semibold text-zinc-400 pb-2 border-b border-zinc-800">
                <span className="flex items-center gap-1.5 text-emerald-400">
                  <Sparkles className="w-3.5 h-3.5" />
                  <span>One Unified Cora Chat Bar</span>
                </span>
                <span className="text-[10px] bg-zinc-800 px-2 py-0.5 rounded text-zinc-300 font-mono">0 Tabs Switched</span>
              </div>
              <div className="font-mono text-xs text-zinc-200 leading-relaxed bg-zinc-900/90 p-2.5 rounded-xl border border-zinc-800/80">
                &ldquo;Cora, confirm Priya&apos;s commercial shoot for Saturday, send the 18% GST bill on WhatsApp, and lock the call-time.&rdquo;
              </div>
              <div className="flex items-center justify-between text-[11px] text-zinc-400 pt-1">
                <span className="text-emerald-400 font-semibold flex items-center gap-1">
                  <CheckCircle2 className="w-3 h-3" />
                  <span>All 5 Apps Updated Automatically</span>
                </span>
              </div>
            </div>

          </div>

        </div>

        {/* ── 7-Card Foundational Bento Grid with Rich Visuals & Backdrops ── */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-7 items-stretch justify-center">
          
          {/* ── CARD 1: INQUIRIES & LEAD PIPELINE (2-Col Wide with Background Photo Layer) ── */}
          <div className="cora-bento-card lg:col-span-2 relative flex flex-col justify-between min-h-[440px] sm:min-h-[460px] overflow-hidden rounded-[32px] p-6 sm:p-8 bg-[#EEF2FF] border-0 shadow-[0px_4px_20px_rgba(99,102,241,0.04)] group hover:shadow-[0px_16px_36px_rgba(99,102,241,0.09)] transition-all duration-300">
            
            {/* Background Atmospheric Photo */}
            <div className="absolute right-0 top-0 w-1/2 h-full opacity-10 pointer-events-none mix-blend-multiply">
              <Image
                src="/images/usecase_commercial_studio.jpg"
                alt="Studio workflow"
                fill
                className="object-cover"
              />
            </div>

            <div className="flex flex-col sm:flex-row sm:items-start justify-between gap-2 sm:gap-4 relative z-10">
              <div>
                <h3 className="font-display text-2xl sm:text-[26px] font-bold text-zinc-950 tracking-tight mb-1">
                  Client Inquiries &amp; Leads
                </h3>
                <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed max-w-[460px]">
                  Capture customer inquiries from WhatsApp, phone calls, and web forms into one organized pipeline.
                </p>
              </div>
              <span className="text-[10.5px] font-bold uppercase px-3 py-1 bg-white text-indigo-950 rounded-full shadow-2xs shrink-0 self-start">
                Lead Manager
              </span>
            </div>

            {/* Anchored Layered UI Cards */}
            <div className="relative w-full h-[260px] sm:h-[280px] my-auto flex items-end justify-center z-10">
              
              {/* Back Card: Active Inquiries */}
              <div className="absolute bottom-6 left-2 sm:left-12 w-[220px] sm:w-[240px] bg-white rounded-2xl p-4 shadow-[0px_8px_24px_rgba(0,0,0,0.06)] border border-zinc-200/80 space-y-2.5 z-10 transition-transform duration-300 group-hover:-translate-y-1">
                <div className="flex items-center justify-between pb-2 border-b border-zinc-100">
                  <div className="flex items-center gap-2">
                    <div className="w-5 h-5 rounded-md bg-zinc-950 text-white flex items-center justify-center">
                      <Inbox className="w-3 h-3 text-zinc-200" />
                    </div>
                    <span className="text-xs font-bold text-zinc-900">Lead Registry</span>
                  </div>
                  <ChevronDown className="w-3.5 h-3.5 text-zinc-400" />
                </div>

                <div className="space-y-1.5 text-[11px] text-zinc-700">
                  <div className="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Active Leads</div>
                  <div className="flex items-center gap-1.5 text-zinc-800 font-medium truncate">
                    <FileText className="w-3 h-3 text-zinc-400 shrink-0" />
                    <span className="truncate">Lakme Fashion Shoot</span>
                  </div>
                  <div className="flex items-center gap-1.5 text-zinc-800 font-medium truncate">
                    <FileText className="w-3 h-3 text-zinc-400 shrink-0" />
                    <span className="truncate">BMW Commercial Video</span>
                  </div>
                </div>

                <div className="pt-1.5 border-t border-zinc-100 space-y-1 text-[10.5px] text-zinc-500">
                  <div className="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Deliverables</div>
                  <div className="flex items-center gap-1.5">
                    <Folder className="w-3 h-3 text-indigo-600 shrink-0" />
                    <span>Raw Footage &amp; Cuts</span>
                  </div>
                </div>
              </div>

              {/* Front Overlapping Card: WhatsApp Pipeline */}
              <div className="absolute bottom-0 right-2 sm:right-12 w-[220px] sm:w-[245px] bg-white rounded-2xl p-4 shadow-[0px_16px_36px_rgba(0,0,0,0.12)] border border-zinc-200/90 space-y-2.5 z-20 transition-transform duration-300 group-hover:translate-x-1 group-hover:-translate-y-1">
                <div className="flex items-center justify-between pb-2 border-b border-zinc-100">
                  <div className="flex items-center gap-2">
                    <div className="w-5 h-5 rounded-md bg-indigo-600 text-white flex items-center justify-center">
                      <MessageSquare className="w-3 h-3 text-white" />
                    </div>
                    <span className="text-xs font-bold text-zinc-900">WhatsApp Sync</span>
                  </div>
                  <ChevronDown className="w-3.5 h-3.5 text-zinc-400" />
                </div>

                <div className="space-y-1.5 text-[11px] text-zinc-700">
                  <div className="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Recent Client</div>
                  <div className="flex items-center gap-1.5 text-zinc-800 font-medium truncate">
                    <FileText className="w-3 h-3 text-indigo-500 shrink-0" />
                    <span className="truncate">Priya (Wedding Retainer)</span>
                  </div>
                </div>

                <div className="pt-1.5 border-t border-zinc-100 space-y-1 text-[10.5px]">
                  <div className="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Status</div>
                  <div className="flex items-center gap-1.5 flex-wrap">
                    <span className="px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-md font-semibold text-[10px]">
                      #Quote-Sent
                    </span>
                    <span className="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded-md font-semibold text-[10px]">
                      #Advance-Paid
                    </span>
                  </div>
                </div>
              </div>

            </div>

            <div className="flex items-center justify-between text-[11px] font-semibold text-zinc-600 pt-2 relative z-10">
              <span>WhatsApp &amp; Phone Inquiries</span>
              <span className="text-indigo-950 font-bold">Never Lose a Lead &rarr;</span>
            </div>

          </div>

          {/* ── CARD 2: CONTRACTS & E-SIGN VAULT (With E-Sign Seal Texture) ── */}
          <div className="cora-bento-card relative flex flex-col justify-between min-h-[440px] sm:min-h-[460px] overflow-hidden rounded-[32px] p-6 sm:p-7 bg-[#E0F2FE] border-0 shadow-[0px_4px_20px_rgba(14,165,233,0.04)] group hover:shadow-[0px_16px_36px_rgba(14,165,233,0.09)] transition-all duration-300">
            
            {/* Background Texture */}
            <div className="absolute right-0 bottom-0 w-3/4 h-1/2 opacity-15 pointer-events-none mix-blend-multiply">
              <Image
                src="/images/bento_esign_seal.jpg"
                alt="Legal vault"
                fill
                className="object-cover"
              />
            </div>

            <div className="text-center relative z-10">
              <h3 className="font-display text-2xl font-bold text-zinc-950 mb-1">
                Contracts &amp; E-Sign
              </h3>
              <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed">
                Standardize legal terms, NDAs &amp; advance clauses
              </p>
            </div>

            {/* Anchored Overlapping Cards */}
            <div className="relative w-full h-[260px] my-auto flex items-end justify-center z-10">
              
              {/* Left Card: Contract Templates */}
              <div className="absolute bottom-4 left-1 sm:left-2 w-[185px] bg-white rounded-2xl p-3.5 shadow-[0px_10px_28px_rgba(0,0,0,0.08)] border border-zinc-200/80 space-y-2 z-10 -rotate-[4deg] group-hover:-rotate-[2deg] transition-transform duration-300">
                <div className="flex items-center gap-1.5 text-xs font-bold text-zinc-900 pb-1.5 border-b border-zinc-100">
                  <ShieldCheck className="w-3.5 h-3.5 text-sky-600" />
                  <span>Legal Templates</span>
                </div>
                <div className="space-y-1.5 text-[10.5px] text-zinc-700 font-medium">
                  <div className="flex items-center gap-1.5 truncate">
                    <FileText className="w-3 h-3 text-sky-600 shrink-0" />
                    <span className="truncate">Commercial Agreement</span>
                  </div>
                  <div className="flex items-center gap-1.5 truncate">
                    <FileText className="w-3 h-3 text-sky-600 shrink-0" />
                    <span className="truncate">IT Act Model NDA</span>
                  </div>
                  <div className="flex items-center gap-1.5 truncate">
                    <FileText className="w-3 h-3 text-sky-600 shrink-0" />
                    <span className="truncate">Call-Sheet SOP</span>
                  </div>
                  <div className="flex items-center gap-1.5 truncate">
                    <FileText className="w-3 h-3 text-sky-600 shrink-0" />
                    <span className="truncate">50% Advance Clause</span>
                  </div>
                </div>
              </div>

              {/* Right Card: E-Sign Status Tags */}
              <div className="absolute bottom-6 right-1 sm:right-2 w-[155px] bg-white rounded-2xl p-3.5 shadow-[0px_16px_36px_rgba(0,0,0,0.12)] border border-zinc-200/90 space-y-2 z-20 rotate-[6deg] group-hover:rotate-[3deg] transition-transform duration-300">
                <div className="flex items-center gap-1.5 text-xs font-bold text-zinc-900 pb-1.5 border-b border-zinc-100">
                  <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600" />
                  <span>Status</span>
                </div>
                <div className="space-y-1.5 text-[10.5px]">
                  <div className="flex items-center gap-1.5 text-emerald-800 font-semibold bg-emerald-50 px-2 py-1 rounded-lg">
                    <Hash className="w-3 h-3 text-emerald-600" />
                    <span>e-signed</span>
                  </div>
                  <div className="flex items-center gap-1.5 text-sky-800 font-semibold bg-sky-50 px-2 py-1 rounded-lg">
                    <Hash className="w-3 h-3 text-sky-600" />
                    <span>advance-paid</span>
                  </div>
                </div>
              </div>

            </div>

            <div className="text-center pt-2 relative z-10">
              <span className="text-[10.5px] font-semibold text-sky-950 bg-white/90 px-3.5 py-1 rounded-full shadow-2xs">
                Zero Scope Creep
              </span>
            </div>

          </div>

          {/* ── CARD 3: 18% GST BILLING (With Real Indian Bill Texture) ── */}
          <div className="cora-bento-card relative flex flex-col justify-between min-h-[440px] sm:min-h-[460px] overflow-hidden rounded-[32px] p-6 sm:p-7 bg-[#DCFCE7] border-0 shadow-[0px_4px_20px_rgba(16,185,129,0.04)] group hover:shadow-[0px_16px_36px_rgba(16,185,129,0.09)] transition-all duration-300">
            
            {/* Background Texture */}
            <div className="absolute right-0 bottom-0 w-3/4 h-1/2 opacity-15 pointer-events-none mix-blend-multiply">
              <Image
                src="/images/bento_gst_upi.jpg"
                alt="GST Tax Math"
                fill
                className="object-cover"
              />
            </div>

            <div className="text-center relative z-10">
              <h3 className="font-display text-2xl font-bold text-zinc-950 mb-1">
                18% GST Billing
              </h3>
              <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed">
                Automatic tax math &amp; UPI payment QR
              </p>
            </div>

            {/* Anchored Structured Table */}
            <div className="relative w-full my-auto bg-white rounded-2xl shadow-[0px_12px_28px_rgba(0,0,0,0.08)] border border-zinc-200/90 overflow-hidden text-left z-10">
              <div className="flex items-center justify-between px-3.5 py-2.5 bg-zinc-50 border-b border-zinc-200/80 text-[10px] font-semibold text-zinc-500">
                <div className="flex items-center gap-1.5">
                  <span className="w-3 text-center">#</span>
                  <span>Client &amp; Project</span>
                </div>
                <span>Tax Split</span>
              </div>

              <div className="divide-y divide-zinc-100 text-[11px]">
                <div className="flex items-center justify-between px-3 py-2">
                  <div className="flex items-center gap-1.5 truncate">
                    <span className="w-3 text-zinc-400 text-[10px]">1</span>
                    <span className="font-medium text-zinc-900 truncate">Priya Wedding Shoot</span>
                  </div>
                  <span className="px-1.5 py-0.5 rounded text-[9.5px] font-bold bg-amber-100 text-amber-800">
                    18% GST
                  </span>
                </div>
                <div className="flex items-center justify-between px-3 py-2">
                  <div className="flex items-center gap-1.5 truncate">
                    <span className="w-3 text-zinc-400 text-[10px]">2</span>
                    <span className="font-medium text-zinc-900 truncate">Apex Realty Ad Video</span>
                  </div>
                  <span className="px-1.5 py-0.5 rounded text-[9.5px] font-bold bg-emerald-100 text-emerald-800">
                    Paid UPI
                  </span>
                </div>
                <div className="flex items-center justify-between px-3 py-2">
                  <div className="flex items-center gap-1.5 truncate">
                    <span className="w-3 text-zinc-400 text-[10px]">3</span>
                    <span className="font-medium text-zinc-900 truncate">Nitin Studio Retainer</span>
                  </div>
                  <span className="px-1.5 py-0.5 rounded text-[9.5px] font-bold bg-purple-100 text-purple-800">
                    Auto Split
                  </span>
                </div>
              </div>

              <div className="px-3 py-1.5 bg-zinc-50/60 border-t border-zinc-100 flex items-center justify-between text-[9.5px] text-zinc-400">
                <span>3 records this week</span>
                <span className="text-emerald-700 font-semibold">+ New Bill</span>
              </div>
            </div>

            <div className="text-center pt-2 relative z-10">
              <span className="text-[10.5px] font-semibold text-emerald-950 bg-white/90 px-3.5 py-1 rounded-full shadow-2xs">
                Zero Manual Calculations
              </span>
            </div>

          </div>

          {/* ── CARD 4: PROMOS & MARKETING ── */}
          <div className="cora-bento-card relative flex flex-col justify-between min-h-[440px] sm:min-h-[460px] overflow-hidden rounded-[32px] p-6 sm:p-7 bg-[#FEF3C7] border-0 shadow-[0px_4px_20px_rgba(245,158,11,0.04)] group hover:shadow-[0px_16px_36px_rgba(245,158,11,0.09)] transition-all duration-300">
            
            <div className="text-center relative z-10">
              <h3 className="font-display text-2xl font-bold text-zinc-950 mb-1">
                Promos &amp; Marketing
              </h3>
              <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed">
                Draft localized posts &amp; client offers
              </p>
            </div>

            <div className="relative w-full my-auto flex items-center justify-center z-10">
              <div className="w-full max-w-[280px] bg-white rounded-2xl p-4 shadow-[0px_12px_28px_rgba(0,0,0,0.08)] space-y-3">
                <div className="flex items-center justify-between pb-2 border-b border-zinc-100">
                  <div className="flex items-center gap-1.5 text-amber-800 font-bold text-xs">
                    <Sparkles className="w-3.5 h-3.5" />
                    <span>Promo Draft</span>
                  </div>
                  <span className="text-[9px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">
                    Ready
                  </span>
                </div>

                <div className="p-2.5 bg-amber-50/70 rounded-xl space-y-1">
                  <div className="text-[10px] text-zinc-500 font-mono">WhatsApp Offer</div>
                  <div className="text-[11.5px] font-bold text-zinc-900 truncate">
                    &ldquo;Weekend 20% Off Festive Pass&rdquo;
                  </div>
                  <div className="text-[10px] text-emerald-700 font-semibold flex items-center gap-1">
                    <CheckCircle2 className="w-3 h-3" />
                    <span>Formatted for regular clients</span>
                  </div>
                </div>

                <div className="flex items-center justify-between text-[10px] font-semibold text-zinc-500 pt-1">
                  <span>Customized to your rates</span>
                  <span className="text-amber-950 font-bold">Copy Text &rarr;</span>
                </div>
              </div>
            </div>

            <div className="text-center pt-2 relative z-10">
              <span className="text-[10.5px] font-semibold text-amber-950 bg-white/90 px-3.5 py-1 rounded-full shadow-2xs">
                Zero Copywriter Fees
              </span>
            </div>

          </div>

          {/* ── CARD 5: CALL-SHEETS & APPOINTMENTS ── */}
          <div className="cora-bento-card relative flex flex-col justify-between min-h-[440px] sm:min-h-[460px] overflow-hidden rounded-[32px] p-6 sm:p-7 bg-[#FFE4E6] border-0 shadow-[0px_4px_20px_rgba(244,63,94,0.04)] group hover:shadow-[0px_16px_36px_rgba(244,63,94,0.09)] transition-all duration-300">
            
            <div className="text-center relative z-10">
              <h3 className="font-display text-2xl font-bold text-zinc-950 mb-1">
                Appointments
              </h3>
              <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed">
                Client visits &amp; WhatsApp reminders
              </p>
            </div>

            <div className="relative w-full my-auto flex items-center justify-center z-10">
              <div className="w-full max-w-[280px] bg-white rounded-2xl p-4 shadow-[0px_12px_28px_rgba(0,0,0,0.08)] space-y-2.5">
                <div className="flex items-center justify-between pb-2 border-b border-zinc-100 text-xs">
                  <div className="flex items-center gap-2">
                    <div className="w-6 h-6 rounded-full bg-rose-100 text-rose-800 flex items-center justify-center text-[10px] font-bold">
                      PV
                    </div>
                    <div>
                      <div className="font-bold text-zinc-900 text-[11px]">Priya (Shoot Booking)</div>
                      <div className="text-[9.5px] text-zinc-500">Slot: Tomorrow 10:30 AM</div>
                    </div>
                  </div>
                  <span className="text-[9px] font-bold bg-emerald-50 text-emerald-800 px-1.5 py-0.5 rounded">
                    Confirmed
                  </span>
                </div>

                <div className="p-2 bg-rose-50/60 rounded-xl flex items-center justify-between text-[10.5px]">
                  <div className="flex items-center gap-1.5 text-zinc-800 font-medium">
                    <Calendar className="w-3.5 h-3.5 text-rose-600" />
                    <span>Auto Reminder</span>
                  </div>
                  <span className="text-[9px] font-bold text-rose-800 bg-rose-100 px-1.5 py-0.5 rounded">
                    Scheduled
                  </span>
                </div>

                <div className="flex items-center gap-1.5 text-[10px] text-emerald-800 bg-emerald-50/80 p-1.5 rounded-lg font-medium">
                  <MessageSquare className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                  <span className="truncate">WhatsApp Reminder Active</span>
                </div>
              </div>
            </div>

            <div className="text-center pt-2 relative z-10">
              <span className="text-[10.5px] font-semibold text-rose-950 bg-white/90 px-3.5 py-1 rounded-full shadow-2xs">
                Zero No-Shows &bull; Zero Double Bookings
              </span>
            </div>

          </div>

          {/* ── CARD 6: CASH FLOW & FINANCIAL LEDGER (2-Col Wide with Background Studio Lens) ── */}
          <div className="cora-bento-card lg:col-span-2 relative flex flex-col justify-between min-h-[400px] sm:min-h-[420px] overflow-hidden rounded-[32px] p-6 sm:p-8 bg-[#EEF2FF] border-0 shadow-[0px_4px_20px_rgba(99,102,241,0.04)] group hover:shadow-[0px_16px_36px_rgba(99,102,241,0.09)] transition-all duration-300">
            
            {/* Background Studio Photography */}
            <div className="absolute left-0 bottom-0 w-1/2 h-full opacity-10 pointer-events-none mix-blend-multiply">
              <Image
                src="/images/usecase_solo_creator.jpg"
                alt="Solo creator accounting"
                fill
                className="object-cover"
              />
            </div>

            <div className="flex flex-col sm:flex-row sm:items-start justify-between gap-2 sm:gap-4 relative z-10">
              <div>
                <h3 className="font-display text-2xl sm:text-[26px] font-bold text-zinc-950 tracking-tight mb-1">
                  Cash Flow &amp; Accounts
                </h3>
                <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed max-w-[460px]">
                  Know today&apos;s revenue, pending client balances, and GST liability at a single glance.
                </p>
              </div>
              <span className="text-[10.5px] font-bold uppercase px-3 py-1 bg-white text-indigo-950 rounded-full shadow-2xs shrink-0 self-start">
                Real-Time
              </span>
            </div>

            <div className="relative w-full my-auto py-3 flex items-center justify-center z-10">
              <div className="w-full max-w-[560px] bg-white rounded-2xl p-4 sm:p-5 shadow-[0px_12px_28px_rgba(0,0,0,0.08)] space-y-3">
                <div className="flex items-center justify-between pb-2 border-b border-zinc-100 text-xs font-bold text-zinc-900">
                  <span className="flex items-center gap-1.5 text-indigo-900">
                    <TrendingUp className="w-4 h-4 text-indigo-600" />
                    <span>Today&apos;s Financial Summary</span>
                  </span>
                  <span className="font-mono text-emerald-600 text-[13px]">₹45,000 Collected</span>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-3 gap-2.5 text-center">
                  <div className="bg-indigo-50/60 p-2.5 rounded-xl">
                    <div className="text-[10px] text-zinc-500">This Month</div>
                    <div className="text-sm font-bold text-zinc-900 font-mono">₹2,80,000</div>
                  </div>
                  <div className="bg-indigo-50/60 p-2.5 rounded-xl">
                    <div className="text-[10px] text-zinc-500">Pending Balances</div>
                    <div className="text-sm font-bold text-amber-800 font-mono">₹35,000</div>
                  </div>
                  <div className="bg-indigo-50/60 p-2.5 rounded-xl">
                    <div className="text-[10px] text-zinc-500">GST Output (18%)</div>
                    <div className="text-sm font-bold text-zinc-900 font-mono">₹42,700</div>
                  </div>
                </div>

                <div className="flex items-center justify-between text-[10.5px] text-zinc-600 pt-0.5">
                  <span>Tax-ready summaries ready to export for your CA.</span>
                  <span className="text-indigo-950 font-bold">Export Excel &rarr;</span>
                </div>
              </div>
            </div>

            <div className="flex items-center justify-between text-[11px] font-semibold text-zinc-600 pt-2 relative z-10">
              <span>Bank &amp; UPI Tracking</span>
              <span className="text-indigo-950 font-bold">Instant Receipts &rarr;</span>
            </div>

          </div>

          {/* ── CARD 7: FREE FOREVER TIER ── */}
          <div className="cora-bento-card relative flex flex-col justify-between min-h-[400px] sm:min-h-[420px] overflow-hidden rounded-[32px] p-6 sm:p-7 bg-[#D1FAE5] border-0 shadow-[0px_4px_20px_rgba(16,185,129,0.04)] group hover:shadow-[0px_16px_36px_rgba(16,185,129,0.09)] transition-all duration-300">
            
            <div className="text-center relative z-10">
              <h3 className="font-display text-2xl font-bold text-zinc-950 mb-1">
                Free Forever
              </h3>
              <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed">
                Core chat &amp; 15 invoices every month
              </p>
            </div>

            <div className="relative w-full my-auto flex items-center justify-center z-10">
              <div className="w-full max-w-[280px] bg-white rounded-2xl p-5 shadow-[0px_12px_28px_rgba(0,0,0,0.08)] text-center space-y-2.5">
                <div className="text-4xl sm:text-5xl font-display font-bold text-zinc-950 tracking-tight">
                  ₹0 <span className="text-xs font-normal text-zinc-500">/ month</span>
                </div>
                <div className="text-[11px] font-semibold text-emerald-800 bg-emerald-50 py-1.5 px-2.5 rounded-lg">
                  Standard Plan ₹299/mo for Unlimited
                </div>
                <div className="text-[10px] text-zinc-500 pt-1">
                  1-Click Google Sign-in &bull; No Credit Card
                </div>
              </div>
            </div>

            <div className="text-center pt-2 relative z-10">
              <span className="text-[10.5px] font-semibold text-emerald-950 bg-white/90 px-3.5 py-1 rounded-full shadow-2xs">
                Start Free in 30 Seconds
              </span>
            </div>

          </div>

        </div>

      </div>
    </section>
  );
}
