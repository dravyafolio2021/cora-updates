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
        <div className="max-w-[780px] mx-auto text-center mb-12 sm:mb-16">
          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl font-bold text-zinc-950 leading-[1.12] tracking-[-0.03em] mb-3.5">
            One chat bar. Every business task.
          </h2>
          <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[600px] mx-auto">
            No switching between CRM tabs, accounting apps, and marketing tools.
          </p>
        </div>

        {/* ── 7-Card Bento Grid with Nature Sky Backgrounds & Crisp Floating White UI ── */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 items-stretch justify-center">
          
          {/* ── CARD 1: INQUIRIES & LEAD PIPELINE (2-Col Wide, Mountain Lavender Background) ── */}
          <div className="cora-bento-card lg:col-span-2 relative flex flex-col justify-between min-h-[460px] sm:min-h-[500px] overflow-hidden rounded-[32px] p-6 sm:p-8 shadow-[0px_10px_30px_rgba(0,0,0,0.05)] border border-zinc-200/60 group hover:shadow-[0px_20px_40px_rgba(0,0,0,0.09)] transition-all duration-300">
            
            {/* Tranquil Lavender Mountain Background Image */}
            <Image
              src="/images/card_bg_mountain_lavender.jpg"
              alt="Mountain Lavender Sky Background"
              fill
              className="absolute inset-0 object-cover -z-10"
              sizes="(max-width: 1024px) 100vw, 66vw"
              priority
            />

            {/* Header */}
            <div className="text-center relative z-10 max-w-[500px] mx-auto">
              <h3 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 mb-1.5 tracking-tight">
                Client Inquiries &amp; CRM
              </h3>
              <p className="text-zinc-700 text-xs sm:text-sm leading-relaxed">
                Capture customer briefs from WhatsApp and phone calls into an automated pipeline
              </p>
            </div>

            {/* Anchored Layered UI Cards Emerging from Bottom */}
            <div className="relative w-full h-[270px] sm:h-[300px] mt-auto flex items-end justify-center gap-3 sm:gap-4 z-10">
              
              {/* Left Card: Active Leads */}
              <div className="w-[190px] sm:w-[220px] bg-white rounded-2xl p-4 shadow-[0px_16px_36px_rgba(0,0,0,0.1)] border border-zinc-200/80 space-y-2.5 transition-transform duration-300 group-hover:-translate-y-1">
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
                  <div className="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Active Briefs</div>
                  <div className="flex items-center gap-1.5 text-zinc-800 font-medium truncate">
                    <FileText className="w-3 h-3 text-zinc-400 shrink-0" />
                    <span className="truncate">Lakme Fashion Week</span>
                  </div>
                  <div className="flex items-center gap-1.5 text-zinc-800 font-medium truncate">
                    <FileText className="w-3 h-3 text-zinc-400 shrink-0" />
                    <span className="truncate">BMW Commercial Ad</span>
                  </div>
                </div>

                <div className="pt-1.5 border-t border-zinc-100 flex items-center gap-1.5 text-[10.5px] text-zinc-500">
                  <Folder className="w-3 h-3 text-indigo-600 shrink-0" />
                  <span>Deliverables Ready</span>
                </div>
              </div>

              {/* Right Card: WhatsApp Sync */}
              <div className="w-[200px] sm:w-[230px] bg-white rounded-2xl p-4 shadow-[0px_20px_40px_rgba(0,0,0,0.14)] border border-zinc-200/90 space-y-2.5 transition-transform duration-300 group-hover:translate-x-1 group-hover:-translate-y-1">
                <div className="flex items-center justify-between pb-2 border-b border-zinc-100">
                  <div className="flex items-center gap-2">
                    <div className="w-5 h-5 rounded-md bg-[#25D366] text-white flex items-center justify-center">
                      <MessageSquare className="w-3 h-3 text-white" />
                    </div>
                    <span className="text-xs font-bold text-zinc-900">WhatsApp Intake</span>
                  </div>
                  <span className="text-[9px] font-bold bg-emerald-50 text-emerald-700 px-1.5 py-0.5 rounded">
                    Active
                  </span>
                </div>

                <div className="space-y-1 text-[10.5px]">
                  <div className="font-semibold text-zinc-900 truncate">Priya (Wedding Retainer)</div>
                  <div className="text-[9.5px] text-zinc-500 truncate">&ldquo;Quote approved for 2 days.&rdquo;</div>
                </div>

                <div className="pt-1.5 border-t border-zinc-100 flex items-center gap-1.5 flex-wrap">
                  <span className="px-2 py-0.5 bg-emerald-50 text-emerald-800 rounded font-semibold text-[9.5px]">
                    #Quote-Sent
                  </span>
                  <span className="px-2 py-0.5 bg-indigo-50 text-indigo-800 rounded font-semibold text-[9.5px]">
                    #Advance-Paid
                  </span>
                </div>
              </div>

            </div>

          </div>

          {/* ── CARD 2: CONTRACTS & E-SIGN (Wildflower Meadow Background) ── */}
          <div className="cora-bento-card relative flex flex-col justify-between min-h-[460px] sm:min-h-[500px] overflow-hidden rounded-[32px] p-6 sm:p-7 shadow-[0px_10px_30px_rgba(0,0,0,0.05)] border border-zinc-200/60 group hover:shadow-[0px_20px_40px_rgba(0,0,0,0.09)] transition-all duration-300">
            
            {/* Wildflower Meadow Sky Background Image */}
            <Image
              src="/images/card_bg_meadow_sky.jpg"
              alt="Wildflower Meadow Sky Background"
              fill
              className="absolute inset-0 object-cover -z-10"
              sizes="(max-width: 1024px) 100vw, 33vw"
            />

            {/* Header */}
            <div className="text-center relative z-10">
              <h3 className="font-display text-2xl font-bold text-zinc-950 mb-1.5">
                Contracts &amp; E-Sign
              </h3>
              <p className="text-zinc-700 text-xs sm:text-sm leading-relaxed max-w-[240px] mx-auto">
                Standardize legal agreements, model NDAs &amp; advance clauses
              </p>
            </div>

            {/* Anchored Overlapping Cards */}
            <div className="relative w-full h-[270px] mt-auto flex items-end justify-center z-10">
              
              {/* Left Card: Legal Templates */}
              <div className="absolute bottom-4 left-0 sm:left-1 w-[180px] bg-white rounded-2xl p-3.5 shadow-[0px_12px_28px_rgba(0,0,0,0.1)] border border-zinc-200/80 space-y-2 z-10 -rotate-[4deg] group-hover:-rotate-[2deg] transition-transform duration-300">
                <div className="flex items-center gap-1.5 text-xs font-bold text-zinc-900 pb-1.5 border-b border-zinc-100">
                  <ShieldCheck className="w-3.5 h-3.5 text-sky-600" />
                  <span>Legal Vault</span>
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
                    <span className="truncate">50% Advance Policy</span>
                  </div>
                </div>
              </div>

              {/* Right Card: E-Sign Status */}
              <div className="absolute bottom-8 right-0 sm:right-1 w-[150px] bg-white rounded-2xl p-3.5 shadow-[0px_16px_36px_rgba(0,0,0,0.14)] border border-zinc-200/90 space-y-2 z-20 rotate-[6deg] group-hover:rotate-[3deg] transition-transform duration-300">
                <div className="flex items-center gap-1.5 text-xs font-bold text-zinc-900 pb-1.5 border-b border-zinc-100">
                  <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600" />
                  <span>E-Sign Status</span>
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

          </div>

          {/* ── CARD 3: 18% GST BILLING (Spring Green Hillside Background) ── */}
          <div className="cora-bento-card relative flex flex-col justify-between min-h-[460px] sm:min-h-[500px] overflow-hidden rounded-[32px] p-6 sm:p-7 shadow-[0px_10px_30px_rgba(0,0,0,0.05)] border border-zinc-200/60 group hover:shadow-[0px_20px_40px_rgba(0,0,0,0.09)] transition-all duration-300">
            
            {/* Spring Green Hillside Background Image */}
            <Image
              src="/images/card_bg_spring_green.jpg"
              alt="Spring Green Hillside Background"
              fill
              className="absolute inset-0 object-cover -z-10"
              sizes="(max-width: 1024px) 100vw, 33vw"
            />

            {/* Header */}
            <div className="text-center relative z-10">
              <h3 className="font-display text-2xl font-bold text-zinc-950 mb-1.5">
                18% GST Billing
              </h3>
              <p className="text-zinc-700 text-xs sm:text-sm leading-relaxed max-w-[240px] mx-auto">
                Automatic tax math, CGST/SGST split &amp; UPI payment QR
              </p>
            </div>

            {/* Anchored Structured Table */}
            <div className="relative w-full mt-auto bg-white rounded-2xl shadow-[0px_16px_36px_rgba(0,0,0,0.12)] border border-zinc-200/90 overflow-hidden text-left z-10 transition-transform duration-300 group-hover:-translate-y-1">
              <div className="flex items-center justify-between px-3.5 py-2.5 bg-zinc-50 border-b border-zinc-200/80 text-[10px] font-semibold text-zinc-500">
                <div className="flex items-center gap-1.5">
                  <span className="w-3 text-center">#</span>
                  <span>Client &amp; Project</span>
                </div>
                <span>18% GST Total</span>
              </div>

              <div className="divide-y divide-zinc-100 text-[11px]">
                <div className="flex items-center justify-between px-3 py-2">
                  <div className="flex items-center gap-1.5 truncate">
                    <span className="w-3 text-zinc-400 text-[10px]">1</span>
                    <span className="font-medium text-zinc-900 truncate">Priya Wedding Shoot</span>
                  </div>
                  <span className="font-mono font-bold text-zinc-900 text-[10.5px]">₹75,000</span>
                </div>
                <div className="flex items-center justify-between px-3 py-2">
                  <div className="flex items-center gap-1.5 truncate">
                    <span className="w-3 text-zinc-400 text-[10px]">2</span>
                    <span className="font-medium text-zinc-900 truncate">Apex Realty Ad Video</span>
                  </div>
                  <span className="font-mono font-bold text-zinc-900 text-[10.5px]">₹1,20,000</span>
                </div>
                <div className="flex items-center justify-between px-3 py-2">
                  <div className="flex items-center gap-1.5 truncate">
                    <span className="w-3 text-zinc-400 text-[10px]">3</span>
                    <span className="font-medium text-zinc-900 truncate">Nitin Studio Retainer</span>
                  </div>
                  <span className="font-mono font-bold text-emerald-700 text-[10.5px]">Paid UPI</span>
                </div>
              </div>

              <div className="px-3.5 py-1.5 bg-zinc-50/70 border-t border-zinc-100 flex items-center justify-between text-[10px] text-zinc-500">
                <span>3 active invoices</span>
                <span className="text-emerald-700 font-semibold">+ New Bill</span>
              </div>
            </div>

          </div>

          {/* ── CARD 4: PROMOS & MARKETING (Wildflower Meadow Background) ── */}
          <div className="cora-bento-card relative flex flex-col justify-between min-h-[440px] sm:min-h-[460px] overflow-hidden rounded-[32px] p-6 sm:p-7 shadow-[0px_10px_30px_rgba(0,0,0,0.05)] border border-zinc-200/60 group hover:shadow-[0px_20px_40px_rgba(0,0,0,0.09)] transition-all duration-300">
            
            <Image
              src="/images/card_bg_meadow_sky.jpg"
              alt="Meadow Sky Background"
              fill
              className="absolute inset-0 object-cover -z-10"
              sizes="(max-width: 1024px) 100vw, 33vw"
            />

            <div className="text-center relative z-10">
              <h3 className="font-display text-2xl font-bold text-zinc-950 mb-1">
                Promos &amp; Marketing
              </h3>
              <p className="text-zinc-700 text-xs sm:text-sm leading-relaxed max-w-[240px] mx-auto">
                Draft localized posts &amp; WhatsApp client offers
              </p>
            </div>

            <div className="relative w-full mt-auto flex items-center justify-center z-10">
              <div className="w-full bg-white rounded-2xl p-4 shadow-[0px_16px_36px_rgba(0,0,0,0.12)] border border-zinc-200/80 space-y-2.5 transition-transform duration-300 group-hover:-translate-y-1">
                <div className="flex items-center justify-between pb-1.5 border-b border-zinc-100">
                  <div className="flex items-center gap-1.5 text-amber-900 font-bold text-xs">
                    <Sparkles className="w-3.5 h-3.5 text-amber-600" />
                    <span>Promo Draft</span>
                  </div>
                  <span className="text-[9px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">
                    Ready
                  </span>
                </div>

                <div className="p-2.5 bg-amber-50/70 rounded-xl space-y-1">
                  <div className="text-[9.5px] text-zinc-500 font-mono">WhatsApp Offer</div>
                  <div className="text-[11.5px] font-bold text-zinc-900 truncate">
                    &ldquo;Weekend 20% Off Festive Shoot Pass&rdquo;
                  </div>
                </div>

                <div className="flex items-center justify-between text-[10px] font-semibold text-zinc-500 pt-0.5">
                  <span>Customized to your rates</span>
                  <span className="text-amber-950 font-bold">Copy Text &rarr;</span>
                </div>
              </div>
            </div>

          </div>

          {/* ── CARD 5: APPOINTMENTS & CALENDAR (Mountain Lavender Background) ── */}
          <div className="cora-bento-card relative flex flex-col justify-between min-h-[440px] sm:min-h-[460px] overflow-hidden rounded-[32px] p-6 sm:p-7 shadow-[0px_10px_30px_rgba(0,0,0,0.05)] border border-zinc-200/60 group hover:shadow-[0px_20px_40px_rgba(0,0,0,0.09)] transition-all duration-300">
            
            <Image
              src="/images/card_bg_mountain_lavender.jpg"
              alt="Mountain Background"
              fill
              className="absolute inset-0 object-cover -z-10"
              sizes="(max-width: 1024px) 100vw, 33vw"
            />

            <div className="text-center relative z-10">
              <h3 className="font-display text-2xl font-bold text-zinc-950 mb-1">
                Appointments
              </h3>
              <p className="text-zinc-700 text-xs sm:text-sm leading-relaxed max-w-[240px] mx-auto">
                Shoot bookings &amp; automated WhatsApp reminders
              </p>
            </div>

            <div className="relative w-full mt-auto flex items-center justify-center z-10">
              <div className="w-full bg-white rounded-2xl p-4 shadow-[0px_16px_36px_rgba(0,0,0,0.12)] border border-zinc-200/80 space-y-2.5 transition-transform duration-300 group-hover:-translate-y-1">
                <div className="flex items-center justify-between pb-1.5 border-b border-zinc-100 text-xs">
                  <div>
                    <div className="font-bold text-zinc-900 text-[11px]">Priya (Shoot Booking)</div>
                    <div className="text-[9.5px] text-zinc-500">Slot: Tomorrow 10:30 AM</div>
                  </div>
                  <span className="text-[9px] font-bold bg-emerald-50 text-emerald-800 px-1.5 py-0.5 rounded">
                    Confirmed
                  </span>
                </div>

                <div className="flex items-center justify-between text-[10.5px] bg-rose-50/60 p-2 rounded-xl">
                  <span className="text-zinc-800 font-medium flex items-center gap-1.5">
                    <Calendar className="w-3.5 h-3.5 text-rose-600" />
                    <span>Auto Reminder</span>
                  </span>
                  <span className="text-[9px] font-bold text-rose-800 bg-rose-100 px-1.5 py-0.5 rounded">
                    Scheduled
                  </span>
                </div>
              </div>
            </div>

          </div>

          {/* ── CARD 6: CASH FLOW & ACCOUNTS (2-Col Wide, Mountain Lavender Background) ── */}
          <div className="cora-bento-card lg:col-span-2 relative flex flex-col justify-between min-h-[420px] sm:min-h-[440px] overflow-hidden rounded-[32px] p-6 sm:p-8 shadow-[0px_10px_30px_rgba(0,0,0,0.05)] border border-zinc-200/60 group hover:shadow-[0px_20px_40px_rgba(0,0,0,0.09)] transition-all duration-300">
            
            <Image
              src="/images/card_bg_mountain_lavender.jpg"
              alt="Mountain Sky Background"
              fill
              className="absolute inset-0 object-cover -z-10"
              sizes="(max-width: 1024px) 100vw, 66vw"
            />

            <div className="flex flex-col sm:flex-row sm:items-start justify-between gap-2 sm:gap-4 relative z-10">
              <div>
                <h3 className="font-display text-2xl sm:text-[26px] font-bold text-zinc-950 tracking-tight mb-1">
                  Cash Flow &amp; Accounts
                </h3>
                <p className="text-zinc-700 text-xs sm:text-sm leading-relaxed max-w-[460px]">
                  Know today&apos;s revenue, pending client balances, and GST liability at a single glance.
                </p>
              </div>
              <span className="text-[10.5px] font-bold uppercase px-3 py-1 bg-white text-indigo-950 rounded-full shadow-2xs shrink-0 self-start">
                Real-Time
              </span>
            </div>

            <div className="relative w-full my-auto py-2 flex items-center justify-center z-10">
              <div className="w-full max-w-[560px] bg-white rounded-2xl p-4 sm:p-5 shadow-[0px_16px_36px_rgba(0,0,0,0.12)] border border-zinc-200/80 space-y-3">
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

            <div className="flex items-center justify-between text-[11px] font-semibold text-zinc-700 pt-2 relative z-10">
              <span>Bank &amp; UPI Tracking</span>
              <span className="text-indigo-950 font-bold">Instant Receipts &rarr;</span>
            </div>

          </div>

          {/* ── CARD 7: FREE FOREVER TIER (Spring Green Hillside Background) ── */}
          <div className="cora-bento-card relative flex flex-col justify-between min-h-[420px] sm:min-h-[440px] overflow-hidden rounded-[32px] p-6 sm:p-7 shadow-[0px_10px_30px_rgba(0,0,0,0.05)] border border-zinc-200/60 group hover:shadow-[0px_20px_40px_rgba(0,0,0,0.09)] transition-all duration-300">
            
            <Image
              src="/images/card_bg_spring_green.jpg"
              alt="Green Hillside Background"
              fill
              className="absolute inset-0 object-cover -z-10"
              sizes="(max-width: 1024px) 100vw, 33vw"
            />

            <div className="text-center relative z-10">
              <h3 className="font-display text-2xl font-bold text-zinc-950 mb-1">
                Free Forever
              </h3>
              <p className="text-zinc-700 text-xs sm:text-sm leading-relaxed">
                Core chat &amp; 15 invoices every month
              </p>
            </div>

            <div className="relative w-full my-auto flex items-center justify-center z-10">
              <div className="w-full max-w-[280px] bg-white rounded-2xl p-5 shadow-[0px_16px_36px_rgba(0,0,0,0.12)] border border-zinc-200/80 text-center space-y-2.5 transition-transform duration-300 group-hover:-translate-y-1">
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
              <span className="text-[10.5px] font-semibold text-emerald-950 bg-white/95 px-3.5 py-1 rounded-full shadow-2xs border border-emerald-100">
                Start Free in 30 Seconds
              </span>
            </div>

          </div>

        </div>

      </div>
    </section>
  );
}
