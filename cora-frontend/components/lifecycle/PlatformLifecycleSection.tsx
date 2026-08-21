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
  TrendingUp,
  MessageCircle,
  Clock,
  Calendar,
  Users,
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
      id="how-it-works"
      ref={sectionRef}
      className="py-20 sm:py-28 bg-[#FAFAFA] relative z-10 overflow-hidden border-b border-zinc-200/60"
    >
      <div className="w-full max-w-[1280px] mx-auto px-4 sm:px-6">
        
        {/* ── Section Header ── */}
        <div className="max-w-[780px] mx-auto text-center mb-14 sm:mb-18">
          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl font-bold text-zinc-950 leading-[1.12] tracking-[-0.03em] mb-3.5">
            One chat bar. Every business task.
          </h2>
          <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[600px] mx-auto">
            No switching between CRM tabs, accounting apps, and marketing tools.
          </p>
        </div>

        {/* ── 7-Card Bento Grid ── */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-7 items-stretch justify-center">
          
          {/* CARD 1: CLIENT INQUIRIES & LEAD CAPTURE (2-Col Wide) */}
          <div
            className="cora-bento-card lg:col-span-2 relative flex flex-col justify-between min-h-[420px] sm:min-h-[440px] overflow-hidden rounded-[32px] p-6 sm:p-8 bg-[#EDE9FE] shadow-[0px_10px_28px_rgba(139,92,246,0.06)] group hover:shadow-[0px_18px_40px_rgba(139,92,246,0.12)] transition-all duration-300"
          >
            <div className="flex flex-col sm:flex-row sm:items-start justify-between gap-2 sm:gap-4 relative z-10">
              <div>
                <h3 className="font-display text-2xl sm:text-[26px] font-bold text-zinc-950 tracking-tight mb-1">
                  Client Inquiries &amp; Leads
                </h3>
                <p className="text-zinc-700 text-xs sm:text-[13px] leading-snug max-w-[440px]">
                  Capture customer inquiries from WhatsApp, phone calls, and your website into one organized list.
                </p>
              </div>
              <span className="text-[10.5px] font-bold uppercase px-3 py-1 bg-white text-purple-950 rounded-full shadow-2xs shrink-0 self-start">
                Lead Manager
              </span>
            </div>

            {/* Micro-UI */}
            <div className="relative h-full w-full min-h-[220px] sm:min-h-[250px] my-auto py-2 flex items-center justify-center">
              <div className="font-sans absolute top-2 left-2 sm:left-12 w-[220px] sm:w-[240px] bg-[#F4F4F6] rounded-2xl p-3.5 shadow-[0px_8px_20px_rgba(0,0,0,0.06)] space-y-2.5 z-10 transition-transform duration-300 group-hover:scale-[1.01]">
                <div className="flex items-center justify-between p-0.5">
                  <div className="flex items-center gap-2">
                    <div className="w-5 h-5 rounded-md bg-zinc-900 text-white flex items-center justify-center text-[10px] font-bold">
                      S
                    </div>
                    <span className="text-[12.5px] font-semibold text-zinc-900">Active Inquiries</span>
                  </div>
                  <ChevronDown className="w-3.5 h-3.5 text-zinc-400" />
                </div>

                <div className="space-y-1.5 text-[12px]">
                  <div className="flex items-center gap-2 px-2 py-1 bg-white rounded-lg shadow-2xs">
                    <MessageCircle className="w-3.5 h-3.5 text-purple-600 shrink-0" />
                    <span className="text-zinc-800 text-[11px] font-medium truncate">Rahul (Hair Spa Package)</span>
                  </div>
                  <div className="flex items-center gap-2 px-2 py-0.5 text-zinc-600">
                    <Clock className="w-3.5 h-3.5 text-zinc-400 shrink-0" />
                    <span className="text-[11px] truncate">Follow up at 4:00 PM</span>
                  </div>
                </div>

                <div className="pt-1 border-t border-zinc-200/60 text-[10.5px] text-zinc-500 flex items-center gap-1.5 px-1">
                  <CheckCircle2 className="w-3.5 h-3.5 text-purple-500" />
                  <span>Auto-saved to client profile</span>
                </div>
              </div>

              <div className="float-card-subtle font-sans absolute top-10 right-2 sm:right-12 w-[220px] sm:w-[245px] bg-white rounded-2xl p-3.5 shadow-[0px_14px_30px_rgba(0,0,0,0.10)] space-y-2.5 z-20 transition-transform duration-300 group-hover:translate-x-1">
                <div className="flex items-center justify-between p-0.5">
                  <div className="flex items-center gap-2">
                    <div className="w-5 h-5 rounded-md bg-purple-600 text-white flex items-center justify-center text-[10px] font-bold">
                      C
                    </div>
                    <span className="text-[12.5px] font-semibold text-zinc-900">WhatsApp Sync</span>
                  </div>
                  <ChevronDown className="w-3.5 h-3.5 text-zinc-400" />
                </div>

                <div className="space-y-1.5 text-[12px]">
                  <div className="flex items-center gap-1.5 px-2.5 py-1.5 bg-purple-50 rounded-lg text-purple-950 font-medium">
                    <Sparkles className="w-3.5 h-3.5 text-purple-600 shrink-0" />
                    <span className="text-[11px] truncate">&ldquo;Quote sent via WhatsApp&rdquo;</span>
                  </div>
                </div>

                <div className="pt-1 border-t border-zinc-100 flex items-center justify-between px-1 text-[10.5px] font-semibold">
                  <span className="text-zinc-500">Ready to Book</span>
                  <span className="text-emerald-700 font-mono">Confirmed &rarr;</span>
                </div>
              </div>
            </div>

            <div className="flex items-center justify-between text-[11px] font-semibold text-zinc-600 pt-2 relative z-10">
              <span>WhatsApp &amp; Phone Inquiries</span>
              <span className="text-purple-950 font-bold">Never Lose a Lead &rarr;</span>
            </div>
          </div>

          {/* CARD 2: AGREEMENTS & SERVICE TERMS */}
          <div
            className="cora-bento-card relative flex flex-col justify-between min-h-[420px] sm:min-h-[440px] overflow-hidden rounded-[32px] p-6 sm:p-7 bg-[#E0F2FE] shadow-[0px_10px_28px_rgba(14,165,233,0.06)] group hover:shadow-[0px_18px_40px_rgba(14,165,233,0.12)] transition-all duration-300"
          >
            <div className="flex flex-col gap-1 text-center relative z-10">
              <h3 className="font-display text-2xl font-bold text-zinc-950 tracking-tight">
                Service Terms
              </h3>
              <p className="text-zinc-700 text-xs sm:text-[13px] leading-snug">
                Clear client terms ready to share
              </p>
            </div>

            <div className="relative h-full w-full min-h-[240px] flex items-center justify-center">
              <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[280px] h-[220px]">
                <div className="font-sans absolute top-2 -left-1 w-[185px] bg-[#F4F4F6] -rotate-[7deg] rounded-2xl p-3.5 shadow-[0px_10px_25px_rgba(0,0,0,0.08)] space-y-2 z-10 transition-transform duration-300 group-hover:-rotate-[5deg]">
                  <div className="text-[11px] font-bold text-zinc-800 pb-1 border-b border-zinc-200/80">
                    Terms
                  </div>
                  <div className="space-y-1.5 text-[11px] text-zinc-700">
                    <div className="flex items-center gap-1.5">
                      <FileText className="w-3.5 h-3.5 text-sky-600 fill-sky-100" />
                      <span>Service Scope</span>
                    </div>
                    <div className="flex items-center gap-1.5">
                      <FileText className="w-3.5 h-3.5 text-amber-600 fill-amber-100" />
                      <span>50% Advance</span>
                    </div>
                    <div className="flex items-center gap-1.5">
                      <FileText className="w-3.5 h-3.5 text-emerald-600 fill-emerald-100" />
                      <span>Cancellation Policy</span>
                    </div>
                  </div>
                </div>

                <div className="float-card-subtle font-sans absolute top-4 right-0 w-[165px] bg-white rotate-[7deg] rounded-2xl p-3.5 shadow-[0px_14px_30px_rgba(0,0,0,0.10)] space-y-2 z-20 transition-transform duration-300 group-hover:rotate-[5deg]">
                  <div className="text-[11px] font-bold text-zinc-800 pb-1 border-b border-zinc-100">
                    Status
                  </div>
                  <div className="space-y-1.5 text-[10.5px]">
                    <div className="flex items-center gap-1 text-emerald-700 font-semibold bg-emerald-50 px-2 py-0.5 rounded-md">
                      <CheckCircle2 className="w-3 h-3 text-emerald-600" />
                      <span>#Accepted</span>
                    </div>
                    <div className="flex items-center gap-1 text-sky-800 font-semibold bg-sky-50 px-2 py-0.5 rounded-md">
                      <Hash className="w-3 h-3 text-sky-600" />
                      <span>#Advance-Paid</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div className="text-center pt-2">
              <span className="text-[10.5px] font-semibold text-sky-950 bg-white/90 px-3.5 py-1 rounded-full shadow-2xs">
                Zero Scope Creep
              </span>
            </div>
          </div>

          {/* CARD 3: 18% GST INVOICING & BILLING */}
          <div
            className="cora-bento-card relative flex flex-col justify-between min-h-[420px] sm:min-h-[440px] overflow-hidden rounded-[32px] p-6 sm:p-7 bg-[#DCFCE7] shadow-[0px_10px_28px_rgba(16,185,129,0.06)] group hover:shadow-[0px_18px_40px_rgba(16,185,129,0.12)] transition-all duration-300"
          >
            <div className="flex flex-col gap-1 text-center relative z-10">
              <h3 className="font-display text-2xl font-bold text-zinc-950 tracking-tight">
                18% GST Billing
              </h3>
              <p className="text-zinc-700 text-xs sm:text-[13px] leading-snug">
                Automatic tax math &amp; UPI payment QR
              </p>
            </div>

            <div className="relative h-full w-full min-h-[240px] flex items-center justify-center overflow-hidden">
              <div className="font-sans w-full max-w-[320px] bg-[#F4F4F6] rounded-2xl shadow-[0px_10px_25px_rgba(0,0,0,0.08)] p-2.5 space-y-2">
                <div className="flex items-center gap-1.5 text-[10px] font-semibold text-zinc-500 px-1">
                  <div className="w-4 text-center">#</div>
                  <div className="flex-1">Client &amp; Service</div>
                  <div className="w-16 text-right">Tax Split</div>
                </div>

                <div className="bg-white rounded-xl shadow-2xs divide-y divide-zinc-100 text-[11px] overflow-hidden">
                  <div className="flex items-center px-2 py-1.5 hover:bg-zinc-50 transition-colors">
                    <span className="w-4 text-zinc-400 text-[10px]">1</span>
                    <div className="flex-1 flex items-center gap-1.5 font-medium text-zinc-900 truncate">
                      <FileText className="w-3 h-3 text-emerald-600 shrink-0" />
                      <span className="truncate">Monthly Membership</span>
                    </div>
                    <span className="px-1.5 py-0.5 rounded text-[9.5px] font-bold bg-amber-100 text-amber-800">
                      18% GST
                    </span>
                  </div>

                  <div className="flex items-center px-2 py-1.5 hover:bg-zinc-50 transition-colors">
                    <span className="w-4 text-zinc-400 text-[10px]">2</span>
                    <div className="flex-1 flex items-center gap-1.5 font-medium text-zinc-900 truncate">
                      <FileText className="w-3 h-3 text-blue-600 shrink-0" />
                      <span className="truncate">Private Consultation</span>
                    </div>
                    <span className="px-1.5 py-0.5 rounded text-[9.5px] font-bold bg-emerald-100 text-emerald-800">
                      Paid UPI
                    </span>
                  </div>

                  <div className="flex items-center px-2 py-1.5 hover:bg-zinc-50 transition-colors">
                    <span className="w-4 text-zinc-400 text-[10px]">3</span>
                    <div className="flex-1 flex items-center gap-1.5 font-medium text-zinc-900 truncate">
                      <FileText className="w-3 h-3 text-purple-600 shrink-0" />
                      <span className="truncate">Agency Retainer</span>
                    </div>
                    <span className="px-1.5 py-0.5 rounded text-[9.5px] font-bold bg-purple-100 text-purple-800">
                      Auto Split
                    </span>
                  </div>
                </div>

                <div className="flex items-center justify-between text-[9.5px] text-zinc-400 px-1 pt-0.5">
                  <span>3 records this week</span>
                  <span className="text-emerald-700 font-semibold">+ New Bill</span>
                </div>
              </div>
            </div>

            <div className="text-center pt-2">
              <span className="text-[10.5px] font-semibold text-emerald-950 bg-white/90 px-3.5 py-1 rounded-full shadow-2xs">
                Zero Manual Tax Calculations
              </span>
            </div>
          </div>

          {/* CARD 4: MARKETING & SOCIAL PROMOS */}
          <div
            className="cora-bento-card relative flex flex-col justify-between min-h-[420px] sm:min-h-[440px] overflow-hidden rounded-[32px] p-6 sm:p-7 bg-[#FEF3C7] shadow-[0px_10px_28px_rgba(245,158,11,0.06)] group hover:shadow-[0px_18px_40px_rgba(245,158,11,0.12)] transition-all duration-300"
          >
            <div className="flex flex-col gap-1 text-center relative z-10">
              <h3 className="font-display text-2xl font-bold text-zinc-950 tracking-tight">
                Promos &amp; Marketing
              </h3>
              <p className="text-zinc-700 text-xs sm:text-[13px] leading-snug">
                Draft localized posts &amp; client offers
              </p>
            </div>

            <div className="relative h-full w-full min-h-[240px] flex items-center justify-center">
              <div className="font-sans w-full max-w-[280px] bg-white rounded-2xl p-4 shadow-[0px_12px_28px_rgba(0,0,0,0.08)] space-y-3">
                <div className="flex items-center justify-between pb-2 border-b border-zinc-100">
                  <div className="flex items-center gap-1.5 text-amber-700 font-bold text-xs">
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
                  <span className="text-amber-900 font-bold">Copy Text &rarr;</span>
                </div>
              </div>
            </div>

            <div className="text-center pt-2">
              <span className="text-[10.5px] font-semibold text-amber-950 bg-white/90 px-3.5 py-1 rounded-full shadow-2xs">
                Zero Copywriter Fees
              </span>
            </div>
          </div>

          {/* CARD 5: APPOINTMENTS & SCHEDULES */}
          <div
            className="cora-bento-card relative flex flex-col justify-between min-h-[420px] sm:min-h-[440px] overflow-hidden rounded-[32px] p-6 sm:p-7 bg-[#FFE4E6] shadow-[0px_10px_28px_rgba(244,63,94,0.06)] group hover:shadow-[0px_18px_40px_rgba(244,63,94,0.12)] transition-all duration-300"
          >
            <div className="flex flex-col gap-1 text-center relative z-10">
              <h3 className="font-display text-2xl font-bold text-zinc-950 tracking-tight">
                Appointments
              </h3>
              <p className="text-zinc-700 text-xs sm:text-[13px] leading-snug">
                Client visits &amp; WhatsApp reminders
              </p>
            </div>

            <div className="relative h-full w-full min-h-[240px] flex items-center justify-center">
              <div className="font-sans w-full max-w-[280px] bg-white rounded-2xl p-4 shadow-[0px_12px_28px_rgba(0,0,0,0.08)] space-y-2.5">
                <div className="flex items-center justify-between pb-2 border-b border-zinc-100 text-xs">
                  <div className="flex items-center gap-2">
                    <div className="w-6 h-6 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center text-[10px] font-bold">
                      A
                    </div>
                    <div>
                      <div className="font-bold text-zinc-900 text-[11px]">Priya (Dental Checkup)</div>
                      <div className="text-[9.5px] text-zinc-500">Slot: Tomorrow 11:30 AM</div>
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
                  <MessageCircle className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                  <span className="truncate">WhatsApp Reminder Active</span>
                </div>
              </div>
            </div>

            <div className="text-center pt-2">
              <span className="text-[10.5px] font-semibold text-rose-950 bg-white/90 px-3.5 py-1 rounded-full shadow-2xs">
                Zero No-Shows &bull; Zero Double Bookings
              </span>
            </div>
          </div>

          {/* CARD 6: CASH FLOW & ACCOUNTS (2-Col Wide) */}
          <div
            className="cora-bento-card lg:col-span-2 relative flex flex-col justify-between min-h-[380px] sm:min-h-[400px] overflow-hidden rounded-[32px] p-6 sm:p-8 bg-[#EEF2FF] shadow-[0px_10px_28px_rgba(99,102,241,0.06)] group hover:shadow-[0px_18px_40px_rgba(99,102,241,0.12)] transition-all duration-300"
          >
            <div className="flex flex-col sm:flex-row sm:items-start justify-between gap-2 sm:gap-4 relative z-10">
              <div>
                <h3 className="font-display text-2xl sm:text-[26px] font-bold text-zinc-950 tracking-tight mb-1">
                  Cash Flow &amp; Accounts
                </h3>
                <p className="text-zinc-700 text-xs sm:text-[13px] leading-snug max-w-[440px]">
                  Know today&apos;s revenue, pending client balances, and GST liability at a single glance.
                </p>
              </div>
              <span className="text-[10.5px] font-bold uppercase px-3 py-1 bg-white text-indigo-950 rounded-full shadow-2xs shrink-0 self-start">
                Real-Time
              </span>
            </div>

            <div className="relative h-full w-full min-h-[200px] my-auto py-3 flex items-center justify-center">
              <div className="font-sans w-full max-w-[560px] bg-white rounded-2xl p-4 sm:p-5 shadow-[0px_12px_28px_rgba(0,0,0,0.08)] space-y-3">
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
                    <div className="text-sm font-bold text-amber-700 font-mono">₹35,000</div>
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

          {/* CARD 7: FREE FOREVER PLAN */}
          <div
            className="cora-bento-card relative flex flex-col justify-between min-h-[380px] sm:min-h-[400px] overflow-hidden rounded-[32px] p-6 sm:p-7 bg-[#D1FAE5] shadow-[0px_10px_28px_rgba(16,185,129,0.06)] group hover:shadow-[0px_18px_40px_rgba(16,185,129,0.12)] transition-all duration-300"
          >
            <div className="flex flex-col gap-1 text-center relative z-10">
              <h3 className="font-display text-2xl font-bold text-zinc-950 tracking-tight">
                Free Forever
              </h3>
              <p className="text-zinc-700 text-xs sm:text-[13px] leading-snug">
                Core chat &amp; 15 invoices every month.
              </p>
            </div>

            <div className="relative h-full w-full min-h-[200px] my-auto flex items-center justify-center">
              <div className="font-sans w-full max-w-[280px] bg-white rounded-2xl p-4 shadow-[0px_12px_28px_rgba(0,0,0,0.08)] text-center space-y-2">
                <div className="text-4xl sm:text-5xl font-display font-bold text-zinc-950 tracking-tight">
                  ₹0 <span className="text-xs font-normal text-zinc-500">/ month</span>
                </div>
                <div className="text-[11px] font-semibold text-emerald-800 bg-emerald-50 py-1 px-2 rounded-lg">
                  Standard Plan ₹299/mo for Unlimited
                </div>
                <div className="text-[10px] text-zinc-500 pt-1">
                  1-Click Google Sign-in &bull; No Credit Card
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
