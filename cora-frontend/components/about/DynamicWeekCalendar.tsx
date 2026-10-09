'use client';

import React, { useState, useEffect } from 'react';
import { 
  Check, 
  Sun, 
  Search, 
  FileText, 
  FileCheck, 
  Users, 
  Calendar, 
  Percent, 
  Lock, 
  Zap 
} from 'lucide-react';

interface DayData {
  dayName: string;
  shortName: string;
  dayNumber: number;
  isToday: boolean;
  isSavedDay: boolean;
}

export function DynamicWeekCalendar() {
  const [days, setDays] = useState<DayData[]>([]);

  useEffect(() => {
    const now = new Date();
    const currentDayOfWeek = now.getDay();
    const distanceToMonday = currentDayOfWeek === 0 ? -6 : 1 - currentDayOfWeek;
    
    const monday = new Date(now);
    monday.setDate(now.getDate() + distanceToMonday);

    const weekDays: DayData[] = [];
    const dayNames = ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'];
    const fullDayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    for (let i = 0; i < 7; i++) {
      const d = new Date(monday);
      d.setDate(monday.getDate() + i);
      
      const isToday = 
        d.getDate() === now.getDate() &&
        d.getMonth() === now.getMonth() &&
        d.getFullYear() === now.getFullYear();

      weekDays.push({
        dayName: fullDayNames[i],
        shortName: dayNames[i],
        dayNumber: d.getDate(),
        isToday,
        isSavedDay: i === 3, // Thursday
      });
    }

    setDays(weekDays);
  }, []);

  // Fallback static days while hydrating
  const displayDays = days.length === 7 ? days : [
    { dayName: 'Monday', shortName: 'MON', dayNumber: 5, isToday: false, isSavedDay: false },
    { dayName: 'Tuesday', shortName: 'TUE', dayNumber: 6, isToday: false, isSavedDay: false },
    { dayName: 'Wednesday', shortName: 'WED', dayNumber: 7, isToday: false, isSavedDay: false },
    { dayName: 'Thursday', shortName: 'THU', dayNumber: 8, isToday: false, isSavedDay: true },
    { dayName: 'Friday', shortName: 'FRI', dayNumber: 9, isToday: false, isSavedDay: false },
    { dayName: 'Saturday', shortName: 'SAT', dayNumber: 10, isToday: false, isSavedDay: false },
    { dayName: 'Sunday', shortName: 'SUN', dayNumber: 11, isToday: false, isSavedDay: false },
  ];

  return (
    <div className="w-full text-center space-y-8 sm:space-y-12">
      
      {/* ── Eyebrow & Section Heading ── */}
      <div className="space-y-3 sm:space-y-4 max-w-[820px] mx-auto px-4">
        {/* Eyebrow Badge with Warm Status Dot */}
        <div className="inline-flex items-center gap-2 px-3.5 py-1 bg-white/95 backdrop-blur-md rounded-full text-[11px] font-mono font-bold tracking-[0.16em] uppercase text-zinc-700 border border-zinc-200/90 shadow-2xs">
          <span className="w-2 h-2 rounded-full bg-amber-400 shrink-0" />
          <span>OUR MOTTO</span>
        </div>

        {/* Main Title with Signature Gradient & Clean Vector Sparks */}
        <div>
          <h2 className="font-display text-3xl xs:text-4xl sm:text-5xl lg:text-[64px] font-bold tracking-[-0.03em] leading-[1.12] text-zinc-950 inline-block">
            Reclaim one full day,{' '}
            <span className="relative inline-block whitespace-nowrap">
              <span className="bg-gradient-to-r from-zinc-950 via-zinc-700 to-zinc-400 bg-clip-text text-transparent">
                every week.
              </span>
              {/* Clean vector celebration sparks */}
              <svg 
                width="22" 
                height="22" 
                viewBox="0 0 24 24" 
                fill="none" 
                className="absolute -top-2.5 -right-6 sm:-top-3.5 sm:-right-7 text-amber-500 stroke-amber-500 stroke-[2.2] stroke-linecap-round select-none pointer-events-none"
              >
                <path d="M12 2v4M4.93 4.93l2.83 2.83M19.07 4.93l-2.83 2.83" />
              </svg>
            </span>
          </h2>
        </div>

        {/* Subtitle */}
        <p className="text-zinc-600 text-xs xs:text-sm sm:text-base lg:text-[16.5px] leading-relaxed max-w-[660px] mx-auto font-normal">
          Marketing agencies, design shops, and dev consultancies reclaim 52 billable days every year — Cora executes your client onboarding, 18% GST billing, and retainer workflows completely on autopilot.
        </p>
      </div>

      {/* ── Unified Visual Canvas: 4 Satellite Cards + 7-Day Week + Connecting Splines ── */}
      <div className="relative w-full max-w-[840px] mx-auto px-2 sm:px-6 pt-4 sm:pt-8 pb-4 sm:pb-8">
        
        {/* SVG Connecting Curves (Smooth Bezier Flow into Thursday) */}
        <svg 
          className="absolute inset-0 w-full h-full pointer-events-none select-none z-10" 
          viewBox="0 0 840 320" 
          fill="none" 
          preserveAspectRatio="none"
        >
          {/* Top-Left to Thursday */}
          <path d="M 170 50 C 280 50, 350 90, 420 115" stroke="#C084FC" strokeWidth="1.5" strokeDasharray="4 4" strokeLinecap="round" opacity="0.65" />
          {/* Top-Right to Thursday */}
          <path d="M 670 50 C 560 50, 490 90, 420 115" stroke="#34D399" strokeWidth="1.5" strokeDasharray="4 4" strokeLinecap="round" opacity="0.65" />
          {/* Bottom-Left to Thursday */}
          <path d="M 170 270 C 280 270, 350 230, 420 205" stroke="#FB7185" strokeWidth="1.5" strokeDasharray="4 4" strokeLinecap="round" opacity="0.65" />
          {/* Bottom-Right to Thursday */}
          <path d="M 670 270 C 560 270, 490 230, 420 205" stroke="#60A5FA" strokeWidth="1.5" strokeDasharray="4 4" strokeLinecap="round" opacity="0.65" />
        </svg>

        {/* ── Top Row: 2 Floating Satellites ── */}
        <div className="flex items-center justify-between mb-3 sm:mb-5 px-1 sm:px-4 relative z-20">
          {/* Card 1: Client Discovery */}
          <div className="bg-white/95 backdrop-blur-md rounded-xl sm:rounded-2xl border border-zinc-200/90 p-2 sm:p-3 shadow-xs sm:shadow-sm flex items-center gap-2 sm:gap-2.5 text-left -rotate-1 hover:rotate-0 transition-transform">
            <div className="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-purple-50 border border-purple-200/60 flex items-center justify-center text-purple-600 shrink-0">
              <Search className="w-3.5 h-3.5 stroke-[2.2]" />
            </div>
            <div>
              <div className="text-[11px] sm:text-xs font-bold text-zinc-900 leading-tight">Client Discovery</div>
              <div className="text-[9.5px] sm:text-[10.5px] text-zinc-500 leading-tight mt-0.5">Intake & qualify leads</div>
            </div>
          </div>

          {/* Card 2: GST Billing */}
          <div className="bg-white/95 backdrop-blur-md rounded-xl sm:rounded-2xl border border-zinc-200/90 p-2 sm:p-3 shadow-xs sm:shadow-sm flex items-center gap-2 sm:gap-2.5 text-left rotate-1 hover:rotate-0 transition-transform">
            <div className="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-600 shrink-0">
              <FileText className="w-3.5 h-3.5 stroke-[2.2]" />
            </div>
            <div>
              <div className="text-[11px] sm:text-xs font-bold text-zinc-900 leading-tight">GST Billing</div>
              <div className="text-[9.5px] sm:text-[10.5px] text-zinc-500 leading-tight mt-0.5">18% automated</div>
            </div>
          </div>
        </div>

        {/* ── Center 7-Day Calendar Shelf ── */}
        <div className="relative z-20 max-w-[680px] mx-auto my-2 sm:my-3">
          
          {/* Cursive Annotation pointing to Thursday */}
          <div className="absolute -top-5 sm:-top-7 left-1/2 -translate-x-1/2 z-30 flex flex-col items-center pointer-events-none select-none">
            <span className="font-scribble text-[10.5px] sm:text-[12.5px] text-zinc-700 whitespace-nowrap transform -rotate-2 leading-none">
              One day back in your week
            </span>
            <svg width="14" height="12" viewBox="0 0 24 24" fill="none" className="stroke-amber-600 stroke-[2.2] stroke-linecap-round mt-0.5">
              <path d="M12 2 C12 8 12 12 12 18 M8 14 L12 18 L16 14" />
            </svg>
          </div>

          {/* Master 7-Day Card Shelf */}
          <div className="bg-white/95 backdrop-blur-md rounded-2xl sm:rounded-3xl p-1.5 sm:p-2.5 border border-zinc-200/90 shadow-[0_10px_30px_-5px_rgba(0,0,0,0.06)] grid grid-cols-7 gap-1 sm:gap-1.5 w-full">
            {displayDays.map((day, idx) => {
              const isSaved = day.isSavedDay;

              return (
                <div
                  key={idx}
                  className={`py-2 sm:py-3 px-0.5 sm:px-1 rounded-xl sm:rounded-2xl transition-all flex flex-col items-center justify-between text-center select-none ${
                    isSaved
                      ? 'bg-zinc-950 text-white shadow-[0_0_20px_rgba(251,191,36,0.35)] border border-amber-400/60 transform scale-[1.04] sm:scale-105 z-20'
                      : 'bg-zinc-50/70 text-zinc-800 border border-zinc-200/60'
                  } min-h-[82px] sm:min-h-[110px]`}
                >
                  {/* Short Day Name */}
                  <span className={`text-[8.5px] sm:text-[10.5px] font-mono font-bold uppercase tracking-tight ${
                    isSaved ? 'text-zinc-300' : 'text-zinc-400'
                  }`}>
                    {day.shortName}
                  </span>

                  {/* Day Number */}
                  <span className={`font-display text-xs sm:text-xl font-bold leading-none ${
                    isSaved ? 'text-white' : 'text-zinc-900'
                  }`}>
                    {day.dayNumber}
                  </span>

                  {/* Icon / Skeleton Content */}
                  {isSaved ? (
                    <div className="flex flex-col items-center gap-0.5 my-0.5">
                      <Sun className="w-3 h-3 sm:w-3.5 sm:h-3.5 text-amber-400" />
                      <span className="px-1 sm:px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-950 text-[6.5px] sm:text-[8.5px] font-bold font-mono tracking-tight leading-none shadow-2xs">
                        Reclaimed
                      </span>
                    </div>
                  ) : (
                    <div className="flex flex-col items-center gap-1 my-0.5">
                      <div className="w-3 sm:w-5 h-0.5 sm:h-1 bg-zinc-200 rounded-full" />
                      <div className="w-2 sm:w-3.5 h-0.5 sm:h-1 bg-zinc-200 rounded-full" />
                      <div className="w-3.5 h-3.5 sm:w-4 sm:h-4 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/70 flex items-center justify-center mt-0.5">
                        <Check className="w-2 h-2 sm:w-2.5 sm:h-2.5 stroke-[2.5]" />
                      </div>
                    </div>
                  )}

                </div>
              );
            })}
          </div>

        </div>

        {/* ── Bottom Row: 2 Floating Satellites ── */}
        <div className="flex items-center justify-between mt-3 sm:mt-5 px-1 sm:px-4 relative z-20">
          {/* Card 3: Contracts */}
          <div className="bg-white/95 backdrop-blur-md rounded-xl sm:rounded-2xl border border-zinc-200/90 p-2 sm:p-3 shadow-xs sm:shadow-sm flex items-center gap-2 sm:gap-2.5 text-left -rotate-1 hover:rotate-0 transition-transform">
            <div className="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-rose-50 border border-rose-200/60 flex items-center justify-center text-rose-600 shrink-0">
              <FileCheck className="w-3.5 h-3.5 stroke-[2.2]" />
            </div>
            <div>
              <div className="text-[11px] sm:text-xs font-bold text-zinc-900 leading-tight">Contracts</div>
              <div className="text-[9.5px] sm:text-[10.5px] text-zinc-500 leading-tight mt-0.5">Generate & e-sign</div>
            </div>
          </div>

          {/* Card 4: Follow-ups */}
          <div className="bg-white/95 backdrop-blur-md rounded-xl sm:rounded-2xl border border-zinc-200/90 p-2 sm:p-3 shadow-xs sm:shadow-sm flex items-center gap-2 sm:gap-2.5 text-left rotate-1 hover:rotate-0 transition-transform">
            <div className="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-blue-50 border border-blue-200/60 flex items-center justify-center text-blue-600 shrink-0">
              <Users className="w-3.5 h-3.5 stroke-[2.2]" />
            </div>
            <div>
              <div className="text-[11px] sm:text-xs font-bold text-zinc-900 leading-tight">Follow-ups</div>
              <div className="text-[9.5px] sm:text-[10.5px] text-zinc-500 leading-tight mt-0.5">Nudge & track</div>
            </div>
          </div>
        </div>

      </div>

      {/* ── Compact 4-Item Horizontal Icon Row (Clean on Mobile & Desktop) ── */}
      <div className="max-w-[840px] mx-auto px-2 sm:px-4 pt-4 sm:pt-6 border-t border-zinc-200/70">
        <div className="grid grid-cols-4 gap-2 sm:gap-4 text-center sm:text-left">
          
          {/* Stat 1: 52 Days Saved */}
          <div className="flex flex-col sm:flex-row items-center sm:items-center gap-1.5 sm:gap-3 p-1.5 rounded-xl">
            <div className="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-amber-50 border border-amber-200/60 flex items-center justify-center text-amber-700 shrink-0 shadow-2xs">
              <Calendar className="w-4 h-4 sm:w-4.5 sm:h-4.5 stroke-[1.8]" />
            </div>
            <div>
              <div className="text-[11px] sm:text-sm font-bold text-zinc-900 tracking-tight leading-tight">52 Days Saved</div>
              <div className="text-[9.5px] sm:text-xs text-zinc-500 hidden xs:block">Per year for real work</div>
            </div>
          </div>

          {/* Stat 2: 18% GST Autopilot */}
          <div className="flex flex-col sm:flex-row items-center sm:items-center gap-1.5 sm:gap-3 p-1.5 rounded-xl">
            <div className="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-indigo-50 border border-indigo-200/60 flex items-center justify-center text-indigo-700 shrink-0 shadow-2xs">
              <Percent className="w-4 h-4 sm:w-4.5 sm:h-4.5 stroke-[1.8]" />
            </div>
            <div>
              <div className="text-[11px] sm:text-sm font-bold text-zinc-900 tracking-tight leading-tight">18% GST Autopilot</div>
              <div className="text-[9.5px] sm:text-xs text-zinc-500 hidden xs:block">Accurate & effortless</div>
            </div>
          </div>

          {/* Stat 3: SHA-256 E-Sign */}
          <div className="flex flex-col sm:flex-row items-center sm:items-center gap-1.5 sm:gap-3 p-1.5 rounded-xl">
            <div className="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-700 shrink-0 shadow-2xs">
              <Lock className="w-4 h-4 sm:w-4.5 sm:h-4.5 stroke-[1.8]" />
            </div>
            <div>
              <div className="text-[11px] sm:text-sm font-bold text-zinc-900 tracking-tight leading-tight">E-Sign Vault</div>
              <div className="text-[9.5px] sm:text-xs text-zinc-500 hidden xs:block">SHA-256 secure</div>
            </div>
          </div>

          {/* Stat 4: Zero Admin Fatigue */}
          <div className="flex flex-col sm:flex-row items-center sm:items-center gap-1.5 sm:gap-3 p-1.5 rounded-xl">
            <div className="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-rose-50 border border-rose-200/60 flex items-center justify-center text-rose-700 shrink-0 shadow-2xs">
              <Zap className="w-4 h-4 sm:w-4.5 sm:h-4.5 stroke-[1.8]" />
            </div>
            <div>
              <div className="text-[11px] sm:text-sm font-bold text-zinc-900 tracking-tight leading-tight">Zero Fatigue</div>
              <div className="text-[9.5px] sm:text-xs text-zinc-500 hidden xs:block">Autopilot busywork</div>
            </div>
          </div>

        </div>
      </div>

    </div>
  );
}
