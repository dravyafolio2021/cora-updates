'use client';

import React, { useEffect, useRef } from 'react';
import Image from 'next/image';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import {
  Sparkles,
  MessageSquare,
  ShieldCheck,
  Receipt,
  CheckCircle2,
  TrendingUp,
  Clock,
  ArrowRight,
  Zap,
} from 'lucide-react';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

export function ValueTransformationSection() {
  const sectionRef = useRef<HTMLElement>(null);

  useEffect(() => {
    const ctx = gsap.context(() => {
      gsap.fromTo(
        '.value-step',
        { y: 30, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.65,
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
      id="value-transformation"
      ref={sectionRef}
      className="py-20 sm:py-28 bg-[#FFFFFF] relative z-10 overflow-hidden border-b border-zinc-100"
    >
      <div className="w-full max-w-[1280px] mx-auto px-4 sm:px-6">
        
        {/* ── 1. Section Header ── */}
        <div className="max-w-[840px] mx-auto text-center mb-16 sm:mb-20">
          <div className="inline-flex items-center gap-1.5 px-3.5 py-1 bg-zinc-100 rounded-full text-zinc-800 text-xs font-semibold uppercase tracking-wider mb-4 border border-zinc-200/80 shadow-2xs">
            <Sparkles className="w-3.5 h-3.5 text-zinc-950" />
            <span>HOW CORA CREATES VALUE</span>
          </div>

          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl lg:text-[48px] font-bold text-zinc-950 leading-[1.1] tracking-[-0.03em] mb-4">
            From chaotic WhatsApp threads <br className="hidden sm:inline" />
            to automated client revenue
          </h2>
          <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[620px] mx-auto">
            How your AI co-founder turns scattered inquiries into signed agreements, 18% GST bills, and settled UPI payments.
          </p>
        </div>

        {/* ── 2. Continuous Visual Transformation Flow (3 Connected Stages) ── */}
        <div className="relative mb-14">
          
          {/* Continuous Connecting Flow Line (Desktop) */}
          <div className="hidden lg:block absolute top-[90px] left-[15%] right-[15%] h-[2px] bg-gradient-to-r from-emerald-200 via-sky-200 to-indigo-200 -z-0" />

          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-8 relative z-10">
            
            {/* ── STAGE 1: INTAKE & QUOTING ── */}
            <div className="value-step bg-[#FBFBFC] rounded-[32px] p-6 sm:p-7 border border-zinc-200/80 shadow-[0px_8px_24px_rgba(0,0,0,0.03)] flex flex-col justify-between hover:border-zinc-300 hover:shadow-md transition-all duration-300">
              <div className="space-y-4">
                
                {/* Visual Knot Card 1 */}
                <div className="h-[170px] bg-white rounded-2xl p-4 border border-zinc-200/90 shadow-2xs flex flex-col justify-between relative overflow-hidden">
                  <div className="flex items-center justify-between pb-2 border-b border-zinc-100">
                    <div className="flex items-center gap-1.5">
                      <div className="w-5 h-5 rounded-md bg-[#25D366] text-white flex items-center justify-center">
                        <MessageSquare className="w-3 h-3 text-white" />
                      </div>
                      <span className="text-xs font-bold text-zinc-900">WhatsApp Intake</span>
                    </div>
                    <span className="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                      Instant Reply
                    </span>
                  </div>

                  <div className="space-y-1.5">
                    <div className="p-2 bg-emerald-50/70 border border-emerald-100 rounded-xl text-[11px] text-emerald-950 font-medium">
                      &ldquo;Quote: 2-Day 4K Ad Shoot = ₹1,20,000 + 18% GST&rdquo;
                    </div>
                    <div className="flex items-center gap-1.5">
                      <span className="px-2 py-0.5 rounded bg-zinc-100 text-zinc-700 text-[9.5px] font-semibold">
                        #Hold-Oct-24
                      </span>
                      <span className="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[9.5px] font-semibold">
                        #Rate-Approved
                      </span>
                    </div>
                  </div>

                  <div className="flex items-center justify-between text-[10px] text-zinc-400">
                    <span>Response time: 0.8s</span>
                    <span className="text-emerald-700 font-bold">✓ Lead Captured</span>
                  </div>
                </div>

                {/* Stage Headline & Narrative */}
                <div className="space-y-1.5 pt-2">
                  <div className="text-xs font-bold uppercase tracking-wider text-emerald-700 flex items-center gap-1">
                    <span>Stage 01</span> &bull; <span>Lead Capture</span>
                  </div>
                  <h3 className="font-display text-2xl font-bold text-zinc-950 tracking-tight">
                    Zero Lost Inquiries
                  </h3>
                  <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed">
                    <strong className="text-zinc-950 font-bold">100% 24/7 response rate</strong>. Inbound WhatsApp DMs &amp; website visitors receive approved pricing packages and lock hold dates instantly while you shoot on set.
                  </p>
                </div>
              </div>

              <div className="pt-4 mt-4 border-t border-zinc-200/60 flex items-center justify-between text-xs text-zinc-500">
                <span>WhatsApp Brief Sync</span>
                <span className="text-zinc-950 font-semibold">Automated</span>
              </div>
            </div>

            {/* ── STAGE 2: CONTRACTS & GST BILLING ── */}
            <div className="value-step bg-[#FBFBFC] rounded-[32px] p-6 sm:p-7 border border-zinc-200/80 shadow-[0px_8px_24px_rgba(0,0,0,0.03)] flex flex-col justify-between hover:border-zinc-300 hover:shadow-md transition-all duration-300">
              <div className="space-y-4">
                
                {/* Visual Knot Card 2 */}
                <div className="h-[170px] bg-white rounded-2xl p-4 border border-zinc-200/90 shadow-2xs flex flex-col justify-between relative overflow-hidden">
                  <div className="flex items-center justify-between pb-2 border-b border-zinc-100">
                    <div className="flex items-center gap-1.5">
                      <div className="w-5 h-5 rounded-md bg-zinc-950 text-white flex items-center justify-center">
                        <Receipt className="w-3 h-3 text-zinc-200" />
                      </div>
                      <span className="text-xs font-bold text-zinc-900">18% GST &amp; E-Sign</span>
                    </div>
                    <span className="text-[10px] font-bold text-sky-700 bg-sky-50 px-2 py-0.5 rounded-full">
                      Legally Bound
                    </span>
                  </div>

                  <div className="grid grid-cols-2 gap-2">
                    <div className="p-2 bg-sky-50/70 border border-sky-100 rounded-xl space-y-0.5">
                      <div className="text-[9px] text-sky-900 font-bold">Commercial NDA</div>
                      <div className="text-[10px] text-emerald-700 font-semibold flex items-center gap-1">
                        <ShieldCheck className="w-2.5 h-2.5" />
                        <span>E-Signed</span>
                      </div>
                    </div>
                    <div className="p-2 bg-emerald-50/70 border border-emerald-100 rounded-xl space-y-0.5">
                      <div className="text-[9px] text-emerald-900 font-bold">50% Advance</div>
                      <div className="text-[10.5px] font-mono font-bold text-zinc-950">₹70,800 UPI</div>
                    </div>
                  </div>

                  <div className="flex items-center justify-between text-[10px] text-zinc-400">
                    <span>SHA-256 Verified</span>
                    <span className="text-sky-700 font-bold">✓ Advance Paid</span>
                  </div>
                </div>

                {/* Stage Headline & Narrative */}
                <div className="space-y-1.5 pt-2">
                  <div className="text-xs font-bold uppercase tracking-wider text-sky-700 flex items-center gap-1">
                    <span>Stage 02</span> &bull; <span>Compliance &amp; Advance</span>
                  </div>
                  <h3 className="font-display text-2xl font-bold text-zinc-950 tracking-tight">
                    Zero Payment Delays
                  </h3>
                  <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed">
                    <strong className="text-zinc-950 font-bold">50% advance collected upfront</strong>. IT Act compliant commercial agreements &amp; 18% CGST/SGST split QR standees generated automatically before cameras roll.
                  </p>
                </div>
              </div>

              <div className="pt-4 mt-4 border-t border-zinc-200/60 flex items-center justify-between text-xs text-zinc-500">
                <span>Dynamic UPI Standee</span>
                <span className="text-zinc-950 font-semibold">1-Click QR</span>
              </div>
            </div>

            {/* ── STAGE 3: EXECUTION & FINANCIAL LEDGER ── */}
            <div className="value-step bg-[#FBFBFC] rounded-[32px] p-6 sm:p-7 border border-zinc-200/80 shadow-[0px_8px_24px_rgba(0,0,0,0.03)] flex flex-col justify-between hover:border-zinc-300 hover:shadow-md transition-all duration-300">
              <div className="space-y-4">
                
                {/* Visual Knot Card 3 */}
                <div className="h-[170px] bg-white rounded-2xl p-4 border border-zinc-200/90 shadow-2xs flex flex-col justify-between relative overflow-hidden">
                  <div className="flex items-center justify-between pb-2 border-b border-zinc-100">
                    <div className="flex items-center gap-1.5">
                      <div className="w-5 h-5 rounded-md bg-indigo-600 text-white flex items-center justify-center">
                        <TrendingUp className="w-3 h-3 text-white" />
                      </div>
                      <span className="text-xs font-bold text-zinc-900">CA-Ready Ledger</span>
                    </div>
                    <span className="text-[10px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full">
                      Reconciled
                    </span>
                  </div>

                  <div className="space-y-1.5">
                    <div className="p-2 bg-indigo-50/60 rounded-xl flex items-center justify-between text-[10.5px]">
                      <span className="text-zinc-600">Month Revenue:</span>
                      <span className="font-mono font-bold text-zinc-950">₹3,45,000</span>
                    </div>
                    <div className="p-2 bg-zinc-50 rounded-xl flex items-center justify-between text-[10.5px]">
                      <span className="text-zinc-600">18% GST Output:</span>
                      <span className="font-mono font-bold text-indigo-900">₹52,600 (GSTR-1)</span>
                    </div>
                  </div>

                  <div className="flex items-center justify-between text-[10px] text-zinc-400">
                    <span>Export to Tally / Excel</span>
                    <span className="text-indigo-700 font-bold">✓ Tax Ready</span>
                  </div>
                </div>

                {/* Stage Headline & Narrative */}
                <div className="space-y-1.5 pt-2">
                  <div className="text-xs font-bold uppercase tracking-wider text-indigo-700 flex items-center gap-1">
                    <span>Stage 03</span> &bull; <span>Ledger &amp; Tax Sync</span>
                  </div>
                  <h3 className="font-display text-2xl font-bold text-zinc-950 tracking-tight">
                    Zero Admin Friction
                  </h3>
                  <p className="text-zinc-600 text-xs sm:text-sm leading-relaxed">
                    <strong className="text-zinc-950 font-bold">3.5 hours saved every day</strong>. Call-sheets dispatched to crew, final balances tracked, and tax-ready summaries exported directly for your CA.
                  </p>
                </div>
              </div>

              <div className="pt-4 mt-4 border-t border-zinc-200/60 flex items-center justify-between text-xs text-zinc-500">
                <span>GSTR-1 &amp; Tally Export</span>
                <span className="text-zinc-950 font-semibold">1-Tap Sync</span>
              </div>
            </div>

          </div>

        </div>

        {/* ── 3. Bottom Proof Banner ── */}
        <div className="bg-zinc-950 text-white rounded-[28px] p-6 sm:p-8 flex flex-col md:flex-row items-center justify-between gap-6 shadow-xl border border-zinc-800">
          <div className="space-y-1 text-center md:text-left">
            <h4 className="font-display text-xl sm:text-2xl font-bold text-white tracking-tight">
              Ready to automate your creative studio operations?
            </h4>
            <p className="text-zinc-400 text-xs sm:text-sm leading-relaxed max-w-[580px]">
              Set up your rate card, connect your WhatsApp, and start closing inquiries with 18% GST compliance in under 2 minutes.
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
