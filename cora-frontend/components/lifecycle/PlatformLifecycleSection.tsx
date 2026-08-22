'use client';

import React, { useEffect, useRef } from 'react';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import {
  FileText,
  Folder,
  CheckCircle2,
  ChevronDown,
  Star,
  Hash,
  Sparkles,
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
          stagger: 0.1,
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
        <div className="max-w-[780px] mx-auto text-center mb-14 sm:mb-18">
          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl font-bold text-zinc-950 leading-[1.12] tracking-[-0.03em] mb-3.5">
            One chat bar. Every business task.
          </h2>
          <p className="text-zinc-600 text-base sm:text-lg font-normal leading-relaxed max-w-[600px] mx-auto">
            No switching between CRM tabs, accounting apps, and marketing tools.
          </p>
        </div>

        {/* ── 3 Elegant Editorial Cards (Calm, Anchored, Meaningful) ── */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 items-stretch justify-center">
          
          {/* ── CARD 1: SPACES (Workspaces & Client Pipelines) ── */}
          <div className="cora-bento-card relative flex flex-col justify-between h-[480px] sm:h-[520px] overflow-hidden rounded-[32px] p-6 sm:p-8 bg-[#EEF2FF] shadow-[0px_4px_20px_rgba(99,102,241,0.04)] group hover:shadow-[0px_16px_36px_rgba(99,102,241,0.09)] transition-all duration-300">
            
            {/* Header */}
            <div className="text-center relative z-10">
              <h3 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 mb-1.5">
                Spaces
              </h3>
              <p className="text-zinc-600 text-xs sm:text-sm max-w-[240px] mx-auto leading-relaxed">
                Switch between client shoots, retainers, and agency accounts
              </p>
            </div>

            {/* Anchored Layered UI Cards */}
            <div className="relative w-full h-[280px] sm:h-[300px] mt-auto flex items-end justify-center">
              
              {/* Back Card: Studio Space */}
              <div className="absolute bottom-6 left-2 sm:left-4 w-[210px] sm:w-[230px] bg-white rounded-2xl p-4 shadow-[0px_8px_24px_rgba(0,0,0,0.06)] border border-zinc-200/80 space-y-2.5 z-10 transition-transform duration-300 group-hover:-translate-y-1">
                <div className="flex items-center justify-between pb-2 border-b border-zinc-100">
                  <div className="flex items-center gap-2">
                    <div className="w-5 h-5 rounded-md bg-zinc-900 text-white flex items-center justify-center text-[10px] font-bold">
                      📸
                    </div>
                    <span className="text-xs font-bold text-zinc-900">Studio Space</span>
                  </div>
                  <ChevronDown className="w-3.5 h-3.5 text-zinc-400" />
                </div>

                <div className="space-y-1.5 text-[11px] text-zinc-700">
                  <div className="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Starred</div>
                  <div className="flex items-center gap-1.5 text-zinc-800 font-medium truncate">
                    <FileText className="w-3 h-3 text-zinc-400" />
                    <span>Lakme Fashion Week</span>
                  </div>
                  <div className="flex items-center gap-1.5 text-zinc-800 font-medium truncate">
                    <FileText className="w-3 h-3 text-zinc-400" />
                    <span>BMW Commercial Shoot</span>
                  </div>
                </div>

                <div className="pt-1.5 border-t border-zinc-100 space-y-1 text-[10.5px] text-zinc-500">
                  <div className="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Folders</div>
                  <div className="flex items-center gap-1.5">
                    <Folder className="w-3 h-3 text-indigo-500" />
                    <span>Client Deliverables</span>
                  </div>
                </div>
              </div>

              {/* Front Overlapping Card: Agency Space */}
              <div className="absolute bottom-0 right-2 sm:right-4 w-[210px] sm:w-[230px] bg-white rounded-2xl p-4 shadow-[0px_16px_36px_rgba(0,0,0,0.12)] border border-zinc-200/90 space-y-2.5 z-20 transition-transform duration-300 group-hover:translate-x-1 group-hover:-translate-y-1">
                <div className="flex items-center justify-between pb-2 border-b border-zinc-100">
                  <div className="flex items-center gap-2">
                    <div className="w-5 h-5 rounded-md bg-indigo-600 text-white flex items-center justify-center text-[10px] font-bold">
                      🏢
                    </div>
                    <span className="text-xs font-bold text-zinc-900">Agency Workspace</span>
                  </div>
                  <ChevronDown className="w-3.5 h-3.5 text-zinc-400" />
                </div>

                <div className="space-y-1.5 text-[11px] text-zinc-700">
                  <div className="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Starred</div>
                  <div className="flex items-center gap-1.5 text-zinc-800 font-medium truncate">
                    <FileText className="w-3 h-3 text-indigo-500" />
                    <span>Zomato Brand Retainer</span>
                  </div>
                </div>

                <div className="pt-1.5 border-t border-zinc-100 space-y-1 text-[10.5px]">
                  <div className="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Tags</div>
                  <div className="flex items-center gap-1.5 flex-wrap">
                    <span className="px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-md font-semibold text-[10px]">
                      #In-Progress
                    </span>
                    <span className="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded-md font-semibold text-[10px]">
                      #Advance-Paid
                    </span>
                  </div>
                </div>
              </div>

            </div>

          </div>

          {/* ── CARD 2: FOLDERS & TAGS (SOPs, Contracts & Workflows) ── */}
          <div className="cora-bento-card relative flex flex-col justify-between h-[480px] sm:h-[520px] overflow-hidden rounded-[32px] p-6 sm:p-8 bg-[#E0F2FE] shadow-[0px_4px_20px_rgba(14,165,233,0.04)] group hover:shadow-[0px_16px_36px_rgba(14,165,233,0.09)] transition-all duration-300">
            
            {/* Header */}
            <div className="text-center relative z-10">
              <h3 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 mb-1.5">
                Folders &amp; Tags
              </h3>
              <p className="text-zinc-600 text-xs sm:text-sm max-w-[240px] mx-auto leading-relaxed">
                Standard structure for legal contracts, rates, and clear hierarchies
              </p>
            </div>

            {/* Anchored Folders & Tags Overlapping Cards */}
            <div className="relative w-full h-[280px] sm:h-[300px] mt-auto flex items-end justify-center">
              
              {/* Left Card: Folders / SOPs */}
              <div className="absolute bottom-4 left-2 sm:left-4 w-[190px] sm:w-[210px] bg-white rounded-2xl p-4 shadow-[0px_10px_28px_rgba(0,0,0,0.08)] border border-zinc-200/80 space-y-2 z-10 -rotate-[4deg] group-hover:-rotate-[2deg] transition-transform duration-300">
                <div className="text-xs font-bold text-zinc-900 pb-1.5 border-b border-zinc-100">
                  Folders &amp; SOPs
                </div>
                <div className="space-y-2 text-[11px] text-zinc-700 font-medium">
                  <div className="flex items-center gap-2">
                    <Folder className="w-3.5 h-3.5 text-sky-600" />
                    <span>Contracts &amp; NDAs</span>
                  </div>
                  <div className="flex items-center gap-2">
                    <Folder className="w-3.5 h-3.5 text-sky-600" />
                    <span>Studio Rate Cards</span>
                  </div>
                  <div className="flex items-center gap-2">
                    <Folder className="w-3.5 h-3.5 text-sky-600" />
                    <span>Call-Sheet SOPs</span>
                  </div>
                  <div className="flex items-center gap-2">
                    <Folder className="w-3.5 h-3.5 text-sky-600" />
                    <span>E-Sign Vault</span>
                  </div>
                </div>
              </div>

              {/* Right Card: Tags */}
              <div className="absolute bottom-8 right-2 sm:right-4 w-[160px] sm:w-[180px] bg-white rounded-2xl p-4 shadow-[0px_16px_36px_rgba(0,0,0,0.12)] border border-zinc-200/90 space-y-2.5 z-20 rotate-[6deg] group-hover:rotate-[3deg] transition-transform duration-300">
                <div className="text-xs font-bold text-zinc-900 pb-1.5 border-b border-zinc-100">
                  Status Tags
                </div>
                <div className="space-y-1.5 text-[11px]">
                  <div className="flex items-center gap-1.5 text-zinc-700 font-semibold bg-zinc-50 px-2 py-1 rounded-lg">
                    <Hash className="w-3 h-3 text-zinc-400" />
                    <span>e-signed</span>
                  </div>
                  <div className="flex items-center gap-1.5 text-emerald-700 font-semibold bg-emerald-50 px-2 py-1 rounded-lg">
                    <Hash className="w-3 h-3 text-emerald-500" />
                    <span>advance-paid</span>
                  </div>
                  <div className="flex items-center gap-1.5 text-sky-700 font-semibold bg-sky-50 px-2 py-1 rounded-lg">
                    <Hash className="w-3 h-3 text-sky-500" />
                    <span>delivered</span>
                  </div>
                </div>
              </div>

            </div>

          </div>

          {/* ── CARD 3: COLLECTIONS (Notion-Style Data Table) ── */}
          <div className="cora-bento-card relative flex flex-col justify-between h-[480px] sm:h-[520px] overflow-hidden rounded-[32px] p-6 sm:p-8 bg-[#DCFCE7] shadow-[0px_4px_20px_rgba(16,185,129,0.04)] group hover:shadow-[0px_16px_36px_rgba(16,185,129,0.09)] transition-all duration-300">
            
            {/* Header */}
            <div className="text-center relative z-10">
              <h3 className="font-display text-2xl sm:text-3xl font-bold text-zinc-950 mb-1.5">
                Collections
              </h3>
              <p className="text-zinc-600 text-xs sm:text-sm max-w-[240px] mx-auto leading-relaxed">
                For structured tracking, GST records, and rich client data
              </p>
            </div>

            {/* Anchored Clean Data Table */}
            <div className="relative w-full mt-auto bg-white rounded-t-2xl shadow-[0px_16px_36px_rgba(0,0,0,0.1)] border-t border-x border-zinc-200/90 overflow-hidden text-left transition-transform duration-300 group-hover:-translate-y-1">
              
              {/* Table Header Row */}
              <div className="flex items-center justify-between px-3.5 py-2.5 bg-zinc-50 border-b border-zinc-200/80 text-[10.5px] font-semibold text-zinc-500">
                <div className="flex items-center gap-2">
                  <span className="w-3 text-center">#</span>
                  <span>Client &amp; Booking</span>
                </div>
                <span>18% GST Total</span>
              </div>

              {/* Table Rows */}
              <div className="divide-y divide-zinc-100 text-[11px]">
                
                <div className="flex items-center justify-between px-3.5 py-2 hover:bg-zinc-50/80 transition-colors">
                  <div className="flex items-center gap-2 truncate">
                    <span className="w-3 text-zinc-400 text-[10px]">1</span>
                    <FileText className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                    <span className="font-medium text-zinc-900 truncate">Priya (Wedding Shoot)</span>
                  </div>
                  <span className="font-mono font-bold text-zinc-900 text-[10.5px]">₹75,000</span>
                </div>

                <div className="flex items-center justify-between px-3.5 py-2 hover:bg-zinc-50/80 transition-colors">
                  <div className="flex items-center gap-2 truncate">
                    <span className="w-3 text-zinc-400 text-[10px]">2</span>
                    <FileText className="w-3.5 h-3.5 text-sky-600 shrink-0" />
                    <span className="font-medium text-zinc-900 truncate">Apex Realty (Ad Video)</span>
                  </div>
                  <span className="font-mono font-bold text-zinc-900 text-[10.5px]">₹1,20,000</span>
                </div>

                <div className="flex items-center justify-between px-3.5 py-2 hover:bg-zinc-50/80 transition-colors">
                  <div className="flex items-center gap-2 truncate">
                    <span className="w-3 text-zinc-400 text-[10px]">3</span>
                    <FileText className="w-3.5 h-3.5 text-indigo-600 shrink-0" />
                    <span className="font-medium text-zinc-900 truncate">Nitin Studio (Retainer)</span>
                  </div>
                  <span className="font-mono font-bold text-emerald-700 text-[10.5px]">Paid UPI</span>
                </div>

                <div className="flex items-center justify-between px-3.5 py-2 hover:bg-zinc-50/80 transition-colors">
                  <div className="flex items-center gap-2 truncate">
                    <span className="w-3 text-zinc-400 text-[10px]">4</span>
                    <FileText className="w-3.5 h-3.5 text-amber-600 shrink-0" />
                    <span className="font-medium text-zinc-900 truncate">Aura Clinic (Social Menu)</span>
                  </div>
                  <span className="font-mono font-bold text-zinc-900 text-[10.5px]">₹32,000</span>
                </div>

              </div>

              {/* Table Footer */}
              <div className="px-3.5 py-1.5 bg-zinc-50/60 border-t border-zinc-100 flex items-center justify-between text-[10px] text-zinc-400">
                <span>4 active bookings</span>
                <span className="text-emerald-700 font-semibold">Synced in Real-Time</span>
              </div>

            </div>

          </div>

        </div>

      </div>
    </section>
  );
}
