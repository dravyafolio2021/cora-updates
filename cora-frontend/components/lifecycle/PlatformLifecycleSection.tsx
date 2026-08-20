'use client';

import React, { useEffect, useRef } from 'react';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import {
  FileText,
  Folder,
  CheckCircle2,
  Sparkles,
  ChevronDown,
  Hash,
  Globe,
  QrCode,
  ShieldCheck,
  Search,
  Users,
  Camera,
  TrendingUp,
  MessageCircle,
  Clock,
  ArrowRight,
} from 'lucide-react';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

export function PlatformLifecycleSection() {
  const sectionRef = useRef<HTMLElement>(null);

  useEffect(() => {
    const ctx = gsap.context(() => {
      // 1. Staggered entrance animation on scroll
      gsap.fromTo(
        '.cora-bento-card',
        { y: 35, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.8,
          stagger: 0.1,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: sectionRef.current,
            start: 'top 75%',
          },
        }
      );

      // 2. Subtle floating physics for tactile layered widgets
      gsap.to('.float-card-subtle', {
        y: -4,
        duration: 2.8,
        repeat: -1,
        yoyo: true,
        ease: 'sine.inOut',
        stagger: 0.2,
      });
    }, sectionRef);

    return () => ctx.revert();
  }, []);

  return (
    <section
      ref={sectionRef}
      className="py-20 sm:py-28 bg-[#FAFAFA] relative z-10 overflow-hidden border-b border-zinc-200/60"
    >
      <div className="w-full max-w-[1280px] mx-auto px-4 sm:px-6">
        
        {/* ── Section Header (Minimal, Confident) ── */}
        <div className="max-w-[780px] mx-auto text-center mb-14 sm:mb-18">
          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl font-semibold text-zinc-950 leading-[1.12] tracking-[-0.03em] mb-3.5">
            Structure that adapts to your business
          </h2>
          <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[600px] mx-auto">
            Choose the exact tools you need. Scale your studio without limits.
          </p>
        </div>

        {/* ── 7-Card Bento Grid with Solid Colors & Notion Micro-UI Widgets (No Border Outlines) ── */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-7 items-stretch justify-center">
          
          {/* ══════════════════════════════════════════════════════════════════
              CARD 1: WEBSITE CANVAS & SPACES (2-Col Wide — Solid Lavender #EDE9FE)
             ══════════════════════════════════════════════════════════════════ */}
          <div
            className="cora-bento-card lg:col-span-2 relative flex flex-col justify-between min-h-[420px] sm:min-h-[440px] overflow-hidden rounded-[32px] p-6 sm:p-8 bg-[#EDE9FE] shadow-[0px_10px_28px_rgba(139,92,246,0.06)] group hover:shadow-[0px_18px_40px_rgba(139,92,246,0.12)] transition-all duration-300"
          >
            {/* Header */}
            <div className="flex flex-col sm:flex-row sm:items-start justify-between gap-2 sm:gap-4 relative z-10">
              <div>
                <h3 className="font-display text-2xl sm:text-[26px] font-semibold text-zinc-950 tracking-tight mb-1">
                  Website Canvas &amp; Spaces
                </h3>
                <p className="text-zinc-700 text-xs sm:text-[13px] leading-snug max-w-[440px]">
                  Switch between client workspaces, visual Elementor freedom, and prompt sites with Lovable AI.
                </p>
              </div>
              <span className="text-[10.5px] font-bold uppercase px-3 py-1 bg-white text-purple-950 rounded-full shadow-2xs shrink-0 self-start">
                Shopify Speed 99
              </span>
            </div>

            {/* Micro-UI: Overlapping Notion Workspace Panels */}
            <div className="relative h-full w-full min-h-[220px] sm:min-h-[250px] my-auto py-2 flex items-center justify-center">
              
              {/* Back Workspace Panel (Personal Space) */}
              <div className="font-sans absolute top-2 left-2 sm:left-12 w-[220px] sm:w-[240px] bg-[#F4F4F6] rounded-2xl p-3.5 shadow-[0px_8px_20px_rgba(0,0,0,0.06)] space-y-2.5 z-10 transition-transform duration-300 group-hover:scale-[1.01]">
                <div className="flex items-center justify-between p-0.5">
                  <div className="flex items-center gap-2">
                    <div className="w-5 h-5 rounded-md bg-zinc-900 text-white flex items-center justify-center text-[10px] font-bold">
                      S
                    </div>
                    <span className="text-[12.5px] font-semibold text-zinc-900">Personal Space</span>
                  </div>
                  <ChevronDown className="w-3.5 h-3.5 text-zinc-400" />
                </div>

                <div className="space-y-1.5 text-[12px]">
                  <div className="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400 px-1">
                    Starred
                  </div>
                  <div className="flex items-center gap-2 px-2 py-1 bg-white rounded-lg shadow-2xs">
                    <FileText className="w-3.5 h-3.5 text-purple-600 shrink-0" />
                    <span className="text-zinc-800 text-[11px] font-medium truncate">Client Retainer 2026</span>
                  </div>
                  <div className="flex items-center gap-2 px-2 py-0.5 text-zinc-600">
                    <Globe className="w-3.5 h-3.5 text-zinc-400 shrink-0" />
                    <span className="text-[11px] font-mono truncate">yourbrand.in</span>
                  </div>
                </div>

                <div className="pt-1 border-t border-zinc-200/60 text-[10.5px] text-zinc-500 flex items-center gap-1.5 px-1">
                  <Folder className="w-3.5 h-3.5 text-purple-500" />
                  <span>Media Vault</span>
                </div>
              </div>

              {/* Front Workspace Panel (Lovable AI Site Generator) */}
              <div className="float-card-subtle font-sans absolute top-10 right-2 sm:right-12 w-[220px] sm:w-[245px] bg-white rounded-2xl p-3.5 shadow-[0px_14px_30px_rgba(0,0,0,0.10)] space-y-2.5 z-20 transition-transform duration-300 group-hover:translate-x-1">
                <div className="flex items-center justify-between p-0.5">
                  <div className="flex items-center gap-2">
                    <div className="w-5 h-5 rounded-md bg-purple-600 text-white flex items-center justify-center text-[10px] font-bold">
                      C
                    </div>
                    <span className="text-[12.5px] font-semibold text-zinc-900">Lovable AI</span>
                  </div>
                  <ChevronDown className="w-3.5 h-3.5 text-zinc-400" />
                </div>

                <div className="space-y-1.5 text-[12px]">
                  <div className="text-[9.5px] font-bold uppercase tracking-wider text-zinc-400 px-1">
                    Live Builder
                  </div>
                  <div className="flex items-center gap-1.5 px-2.5 py-1.5 bg-purple-50 rounded-lg text-purple-950 font-medium">
                    <Sparkles className="w-3.5 h-3.5 text-purple-600 shrink-0" />
                    <span className="text-[11px] truncate font-mono">"Luxury Agency Site..."</span>
                  </div>
                </div>

                <div className="pt-1 border-t border-zinc-100 flex items-center justify-between px-1 text-[10.5px] font-semibold">
                  <span className="text-zinc-500">Zero Code Lock-in</span>
                  <span className="text-emerald-700 font-mono">1-Click Live &rarr;</span>
                </div>
              </div>

            </div>

            {/* Footer Row */}
            <div className="flex items-center justify-between text-[11px] font-semibold text-zinc-600 pt-2 relative z-10">
              <span>WordPress Flexibility &bull; Fast Mobile CDN</span>
              <span className="text-purple-950 font-bold">Custom Domains &rarr;</span>
            </div>
          </div>


          {/* ══════════════════════════════════════════════════════════════════
              CARD 2: FOLDERS & LEGAL E-SIGN (1-Col — Solid Sky Blue #E0F2FE)
             ══════════════════════════════════════════════════════════════════ */}
          <div
            className="cora-bento-card relative flex flex-col justify-between min-h-[420px] sm:min-h-[440px] overflow-hidden rounded-[32px] p-6 sm:p-7 bg-[#E0F2FE] shadow-[0px_10px_28px_rgba(14,165,233,0.06)] group hover:shadow-[0px_18px_40px_rgba(14,165,233,0.12)] transition-all duration-300"
          >
            {/* Header */}
            <div className="flex flex-col gap-1 text-center relative z-10">
              <h3 className="font-display text-2xl font-semibold text-zinc-950 tracking-tight">
                Folders &amp; Tags
              </h3>
              <p className="text-zinc-700 text-xs sm:text-[13px] leading-snug">
                Classic structure for legal &amp; shoot hierarchies
              </p>
            </div>

            {/* Micro-UI: Tilted Overlapping Folder & Tag Cards */}
            <div className="relative h-full w-full min-h-[240px] flex items-center justify-center">
              <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[280px] h-[220px]">
                
                {/* Left Card: Folders (Rotated -7deg) */}
                <div className="font-sans absolute top-2 -left-1 w-[185px] bg-[#F4F4F6] -rotate-[7deg] rounded-2xl p-3.5 shadow-[0px_10px_25px_rgba(0,0,0,0.08)] space-y-2 z-10 transition-transform duration-300 group-hover:-rotate-[5deg]">
                  <div className="text-[11px] font-bold text-zinc-800 pb-1 border-b border-zinc-200/80">
                    Folders
                  </div>
                  <div className="space-y-1.5 text-[11px] text-zinc-700">
                    <div className="flex items-center gap-1.5">
                      <Folder className="w-3.5 h-3.5 text-sky-600 fill-sky-100" />
                      <span>Legal Contracts</span>
                    </div>
                    <div className="flex items-center gap-1.5">
                      <Folder className="w-3.5 h-3.5 text-amber-600 fill-amber-100" />
                      <span>18% GST Bills</span>
                    </div>
                    <div className="flex items-center gap-1.5">
                      <Folder className="w-3.5 h-3.5 text-emerald-600 fill-emerald-100" />
                      <span>Crew Call-Sheets</span>
                    </div>
                  </div>
                </div>

                {/* Right Card: Tags (Rotated +7deg) */}
                <div className="float-card-subtle font-sans absolute top-4 right-0 w-[165px] bg-white rotate-[7deg] rounded-2xl p-3.5 shadow-[0px_14px_30px_rgba(0,0,0,0.10)] space-y-2 z-20 transition-transform duration-300 group-hover:rotate-[5deg]">
                  <div className="text-[11px] font-bold text-zinc-800 pb-1 border-b border-zinc-100">
                    Tags
                  </div>
                  <div className="space-y-1.5 text-[10.5px]">
                    <div className="flex items-center gap-1 text-emerald-700 font-semibold bg-emerald-50 px-2 py-0.5 rounded-md">
                      <CheckCircle2 className="w-3 h-3 text-emerald-600" />
                      <span>#Signed</span>
                    </div>
                    <div className="flex items-center gap-1 text-sky-800 font-semibold bg-sky-50 px-2 py-0.5 rounded-md">
                      <Hash className="w-3 h-3 text-sky-600" />
                      <span>#Sec10A</span>
                    </div>
                    <div className="flex items-center gap-1 text-purple-800 font-semibold bg-purple-50 px-2 py-0.5 rounded-md">
                      <Hash className="w-3 h-3 text-purple-600" />
                      <span>#UPI-Paid</span>
                    </div>
                  </div>
                </div>

              </div>
            </div>

            <div className="text-center pt-2">
              <span className="text-[10.5px] font-semibold text-sky-950 bg-white/90 px-3.5 py-1 rounded-full shadow-2xs">
                SHA-256 Legally Enforceable
              </span>
            </div>
          </div>


          {/* ══════════════════════════════════════════════════════════════════
              CARD 3: COLLECTIONS & 18% GST (1-Col — Solid Mint Green #DCFCE7)
             ══════════════════════════════════════════════════════════════════ */}
          <div
            className="cora-bento-card relative flex flex-col justify-between min-h-[420px] sm:min-h-[440px] overflow-hidden rounded-[32px] p-6 sm:p-7 bg-[#DCFCE7] shadow-[0px_10px_28px_rgba(16,185,129,0.06)] group hover:shadow-[0px_18px_40px_rgba(16,185,129,0.12)] transition-all duration-300"
          >
            {/* Header */}
            <div className="flex flex-col gap-1 text-center relative z-10">
              <h3 className="font-display text-2xl font-semibold text-zinc-950 tracking-tight">
                Collections
              </h3>
              <p className="text-zinc-700 text-xs sm:text-[13px] leading-snug">
                Structured 18% GST tracking &amp; UPI math
              </p>
            </div>

            {/* Micro-UI: Notion Database Table Component */}
            <div className="relative h-full w-full min-h-[240px] flex items-center justify-center overflow-hidden">
              <div className="font-sans w-full max-w-[320px] bg-[#F4F4F6] rounded-2xl shadow-[0px_10px_25px_rgba(0,0,0,0.08)] p-2.5 space-y-2">
                
                {/* Table Header Row */}
                <div className="flex items-center gap-1.5 text-[10px] font-semibold text-zinc-500 px-1">
                  <div className="w-4 text-center">#</div>
                  <div className="flex-1">Aa Client &amp; Invoice</div>
                  <div className="w-16 text-right">Tax Split</div>
                </div>

                {/* Table Body */}
                <div className="bg-white rounded-xl shadow-2xs divide-y divide-zinc-100 text-[11px] overflow-hidden">
                  <div className="flex items-center px-2 py-1.5 hover:bg-zinc-50 transition-colors">
                    <span className="w-4 text-zinc-400 text-[10px]">1</span>
                    <div className="flex-1 flex items-center gap-1.5 font-medium text-zinc-900 truncate">
                      <FileText className="w-3 h-3 text-emerald-600 shrink-0" />
                      <span className="truncate">Vogue Lookbook</span>
                    </div>
                    <span className="px-1.5 py-0.5 rounded text-[9.5px] font-bold bg-amber-100 text-amber-800">
                      18% GST
                    </span>
                  </div>

                  <div className="flex items-center px-2 py-1.5 hover:bg-zinc-50 transition-colors">
                    <span className="w-4 text-zinc-400 text-[10px]">2</span>
                    <div className="flex-1 flex items-center gap-1.5 font-medium text-zinc-900 truncate">
                      <FileText className="w-3 h-3 text-blue-600 shrink-0" />
                      <span className="truncate">BMW Commercial</span>
                    </div>
                    <span className="px-1.5 py-0.5 rounded text-[9.5px] font-bold bg-emerald-100 text-emerald-800">
                      Paid UPI
                    </span>
                  </div>

                  <div className="flex items-center px-2 py-1.5 hover:bg-zinc-50 transition-colors">
                    <span className="w-4 text-zinc-400 text-[10px]">3</span>
                    <div className="flex-1 flex items-center gap-1.5 font-medium text-zinc-900 truncate">
                      <FileText className="w-3 h-3 text-purple-600 shrink-0" />
                      <span className="truncate">Oberoi Retainer</span>
                    </div>
                    <span className="px-1.5 py-0.5 rounded text-[9.5px] font-bold bg-purple-100 text-purple-800">
                      Auto Split
                    </span>
                  </div>
                </div>

                {/* Table Footer */}
                <div className="flex items-center justify-between text-[9.5px] text-zinc-400 px-1 pt-0.5">
                  <span>3 of 48 records</span>
                  <span className="text-emerald-700 font-semibold">+ Add Entry</span>
                </div>
              </div>
            </div>

            <div className="text-center pt-2">
              <span className="text-[10.5px] font-semibold text-emerald-950 bg-white/90 px-3.5 py-1 rounded-full shadow-2xs">
                Zero Manual Tax Calculations
              </span>
            </div>
          </div>


          {/* ══════════════════════════════════════════════════════════════════
              CARD 4: AI CONTENT & SEO COPILOT (1-Col — Solid Amber #FEF3C7)
             ══════════════════════════════════════════════════════════════════ */}
          <div
            className="cora-bento-card relative flex flex-col justify-between min-h-[420px] sm:min-h-[440px] overflow-hidden rounded-[32px] p-6 sm:p-7 bg-[#FEF3C7] shadow-[0px_10px_28px_rgba(245,158,11,0.06)] group hover:shadow-[0px_18px_40px_rgba(245,158,11,0.12)] transition-all duration-300"
          >
            {/* Header */}
            <div className="flex flex-col gap-1 text-center relative z-10">
              <h3 className="font-display text-2xl font-semibold text-zinc-950 tracking-tight">
                AI SEO &amp; Content
              </h3>
              <p className="text-zinc-700 text-xs sm:text-[13px] leading-snug">
                Multi-model AI indexed on Google in seconds
              </p>
            </div>

            {/* Micro-UI: Search Indexing & Copilot Card */}
            <div className="relative h-full w-full min-h-[240px] flex items-center justify-center">
              <div className="font-sans w-full max-w-[280px] bg-white rounded-2xl p-4 shadow-[0px_12px_28px_rgba(0,0,0,0.08)] space-y-3">
                <div className="flex items-center justify-between pb-2 border-b border-zinc-100">
                  <div className="flex items-center gap-1.5 text-amber-700 font-bold text-xs">
                    <Sparkles className="w-3.5 h-3.5" />
                    <span>Claude 3.5 &bull; GPT-4o</span>
                  </div>
                  <span className="text-[9px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">
                    IndexNow ✓
                  </span>
                </div>

                <div className="p-2.5 bg-amber-50/70 rounded-xl space-y-1">
                  <div className="text-[10px] text-zinc-500 font-mono">google.com/search</div>
                  <div className="text-[11.5px] font-bold text-zinc-900 truncate">
                    Best Photography Studio Mumbai
                  </div>
                  <div className="text-[10px] text-emerald-700 font-semibold flex items-center gap-1">
                    <CheckCircle2 className="w-3 h-3" />
                    <span>Ranked Page 1 &bull; 98 SEO Score</span>
                  </div>
                </div>

                <div className="flex items-center justify-between text-[10px] font-semibold text-zinc-500 pt-1">
                  <span>Geo-Targeted Keywords</span>
                  <span className="text-amber-900 font-bold">Auto-Publish &rarr;</span>
                </div>
              </div>
            </div>

            <div className="text-center pt-2">
              <span className="text-[10.5px] font-semibold text-amber-950 bg-white/90 px-3.5 py-1 rounded-full shadow-2xs">
                Zero SEO Agency Fees
              </span>
            </div>
          </div>


          {/* ══════════════════════════════════════════════════════════════════
              CARD 5: CREW & LOGISTICS ROSTER (1-Col — Solid Rose #FFE4E6)
             ══════════════════════════════════════════════════════════════════ */}
          <div
            className="cora-bento-card relative flex flex-col justify-between min-h-[420px] sm:min-h-[440px] overflow-hidden rounded-[32px] p-6 sm:p-7 bg-[#FFE4E6] shadow-[0px_10px_28px_rgba(244,63,94,0.06)] group hover:shadow-[0px_18px_40px_rgba(244,63,94,0.12)] transition-all duration-300"
          >
            {/* Header */}
            <div className="flex flex-col gap-1 text-center relative z-10">
              <h3 className="font-display text-2xl font-semibold text-zinc-950 tracking-tight">
                Crew &amp; Logistics
              </h3>
              <p className="text-zinc-700 text-xs sm:text-[13px] leading-snug">
                WhatsApp call-sheets &amp; gear check-outs
              </p>
            </div>

            {/* Micro-UI: Live Roster & WhatsApp Dispatch Card */}
            <div className="relative h-full w-full min-h-[240px] flex items-center justify-center">
              <div className="font-sans w-full max-w-[280px] bg-white rounded-2xl p-4 shadow-[0px_12px_28px_rgba(0,0,0,0.08)] space-y-2.5">
                
                {/* Crew Member Row */}
                <div className="flex items-center justify-between pb-2 border-b border-zinc-100 text-xs">
                  <div className="flex items-center gap-2">
                    <div className="w-6 h-6 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center text-[10px] font-bold">
                      R
                    </div>
                    <div>
                      <div className="font-bold text-zinc-900 text-[11px]">Rahul (Cinematographer)</div>
                      <div className="text-[9.5px] text-zinc-500">Call Time: 07:00 AM</div>
                    </div>
                  </div>
                  <span className="text-[9px] font-bold bg-emerald-50 text-emerald-800 px-1.5 py-0.5 rounded">
                    Confirmed
                  </span>
                </div>

                {/* Equipment Check-out Row */}
                <div className="p-2 bg-rose-50/60 rounded-xl flex items-center justify-between text-[10.5px]">
                  <div className="flex items-center gap-1.5 text-zinc-800 font-medium">
                    <Camera className="w-3.5 h-3.5 text-rose-600" />
                    <span>Sony FX3 Cinema Rig</span>
                  </div>
                  <span className="text-[9px] font-bold text-rose-800 bg-rose-100 px-1.5 py-0.5 rounded">
                    Checked Out
                  </span>
                </div>

                {/* WhatsApp Dispatch status */}
                <div className="flex items-center gap-1.5 text-[10px] text-emerald-800 bg-emerald-50/80 p-1.5 rounded-lg font-medium">
                  <MessageCircle className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                  <span className="truncate">WhatsApp Call-Sheet Dispatched</span>
                </div>
              </div>
            </div>

            <div className="text-center pt-2">
              <span className="text-[10.5px] font-semibold text-rose-950 bg-white/90 px-3.5 py-1 rounded-full shadow-2xs">
                0% No-Shows &bull; Zero Double-Bookings
              </span>
            </div>
          </div>


          {/* ══════════════════════════════════════════════════════════════════
              CARD 6: AI CASHFLOW & PROFITABILITY (2-Col Wide — Solid Indigo #EEF2FF)
             ══════════════════════════════════════════════════════════════════ */}
          <div
            className="cora-bento-card lg:col-span-2 relative flex flex-col justify-between min-h-[380px] sm:min-h-[400px] overflow-hidden rounded-[32px] p-6 sm:p-8 bg-[#EEF2FF] shadow-[0px_10px_28px_rgba(99,102,241,0.06)] group hover:shadow-[0px_18px_40px_rgba(99,102,241,0.12)] transition-all duration-300"
          >
            {/* Header */}
            <div className="flex flex-col sm:flex-row sm:items-start justify-between gap-2 sm:gap-4 relative z-10">
              <div>
                <h3 className="font-display text-2xl sm:text-[26px] font-semibold text-zinc-950 tracking-tight mb-1">
                  AI Cashflow &amp; Profitability
                </h3>
                <p className="text-zinc-700 text-xs sm:text-[13px] leading-snug max-w-[440px]">
                  30/60/90-day predictive runway forecasts, expense auto-categorization, and project profit margins.
                </p>
              </div>
              <span className="text-[10.5px] font-bold uppercase px-3 py-1 bg-white text-indigo-950 rounded-full shadow-2xs shrink-0 self-start">
                94% Financial Health
              </span>
            </div>

            {/* Micro-UI: Financial Runway Simulator & Cora's Take */}
            <div className="relative h-full w-full min-h-[200px] my-auto py-3 flex items-center justify-center">
              <div className="font-sans w-full max-w-[560px] bg-white rounded-2xl p-4 sm:p-5 shadow-[0px_12px_28px_rgba(0,0,0,0.08)] space-y-3">
                
                <div className="flex items-center justify-between pb-2 border-b border-zinc-100 text-xs font-bold text-zinc-900">
                  <span className="flex items-center gap-1.5 text-indigo-900">
                    <TrendingUp className="w-4 h-4 text-indigo-600" />
                    <span>Cora's Financial Take</span>
                  </span>
                  <span className="font-mono text-emerald-600 text-[13px]">₹1,45,000 Due Friday</span>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-3 gap-2.5 text-center">
                  <div className="bg-indigo-50/60 p-2.5 rounded-xl">
                    <div className="text-[10px] text-zinc-500">30-Day Runway</div>
                    <div className="text-sm font-bold text-zinc-900 font-mono">₹4.8L</div>
                  </div>
                  <div className="bg-indigo-50/60 p-2.5 rounded-xl">
                    <div className="text-[10px] text-zinc-500">60-Day Forecast</div>
                    <div className="text-sm font-bold text-zinc-900 font-mono">₹11.2L</div>
                  </div>
                  <div className="bg-indigo-50/60 p-2.5 rounded-xl">
                    <div className="text-[10px] text-zinc-500">Profit Margin</div>
                    <div className="text-sm font-bold text-emerald-700 font-mono">68% Avg</div>
                  </div>
                </div>

                <div className="flex items-center justify-between text-[10.5px] text-zinc-600 pt-0.5">
                  <span>Projected runway strong across all active retainers.</span>
                  <span className="text-indigo-950 font-bold">Simulate Profit &rarr;</span>
                </div>
              </div>
            </div>

            {/* Footer Row */}
            <div className="flex items-center justify-between text-[11px] font-semibold text-zinc-600 pt-2 relative z-10">
              <span>Automated Bank Reconciliation</span>
              <span className="text-indigo-950 font-bold">Instant UPI QR Settlements &rarr;</span>
            </div>
          </div>


          {/* ══════════════════════════════════════════════════════════════════
              CARD 7: TRANSPARENT PRICING & FREE FOREVER (1-Col — Solid Emerald #D1FAE5)
             ══════════════════════════════════════════════════════════════════ */}
          <div
            className="cora-bento-card relative flex flex-col justify-between min-h-[380px] sm:min-h-[400px] overflow-hidden rounded-[32px] p-6 sm:p-7 bg-[#D1FAE5] shadow-[0px_10px_28px_rgba(16,185,129,0.06)] group hover:shadow-[0px_18px_40px_rgba(16,185,129,0.12)] transition-all duration-300"
          >
            {/* Header */}
            <div className="flex flex-col gap-1 text-center relative z-10">
              <h3 className="font-display text-2xl font-semibold text-zinc-950 tracking-tight">
                Free Forever
              </h3>
              <p className="text-zinc-700 text-xs sm:text-[13px] leading-snug">
                1,000 free AI runs every month. No card required.
              </p>
            </div>

            {/* Micro-UI: Price Card */}
            <div className="relative h-full w-full min-h-[200px] my-auto flex items-center justify-center">
              <div className="font-sans w-full max-w-[280px] bg-white rounded-2xl p-4 shadow-[0px_12px_28px_rgba(0,0,0,0.08)] text-center space-y-2">
                <div className="text-4xl sm:text-5xl font-display font-bold text-zinc-950 tracking-tight">
                  ₹0 <span className="text-xs font-normal text-zinc-500">/ month</span>
                </div>
                <div className="text-[11px] font-semibold text-emerald-800 bg-emerald-50 py-1 px-2 rounded-lg">
                  India Plan ₹499/mo for Unlimited GST
                </div>
                <div className="text-[10px] text-zinc-500 pt-1">
                  1-Click Google Sign-in &bull; Instant Workspace
                </div>
              </div>
            </div>

            <div className="text-center pt-2">
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
