'use client';

import React, { useState, useEffect } from 'react';
import { Clock, CheckCircle2, Sparkles, ArrowRight, Zap } from 'lucide-react';

interface DayData {
  dayName: string;
  shortName: string;
  dayNumber: number;
  fullDate: Date;
  isToday: boolean;
  isSavedDay: boolean; // By default Thursday
}

export function DynamicWeekCalendar() {
  const [days, setDays] = useState<DayData[]>([]);
  const [selectedDayIndex, setSelectedDayIndex] = useState<number>(3); // Default to Thursday (index 3)
  const [weekRangeStr, setWeekRangeStr] = useState<string>('');

  useEffect(() => {
    const now = new Date();
    const currentDayOfWeek = now.getDay(); // 0 is Sunday, 1 is Monday...
    // Calculate distance to Monday
    // If Sunday (0), distance is -6, else 1 - currentDayOfWeek
    const distanceToMonday = currentDayOfWeek === 0 ? -6 : 1 - currentDayOfWeek;
    
    const monday = new Date(now);
    monday.setDate(now.getDate() + distanceToMonday);

    const weekDays: DayData[] = [];
    const dayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
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
        fullDate: d,
        isToday,
        isSavedDay: i === 3, // Thursday is the designated saved day
      });
    }

    setDays(weekDays);

    // Format week range e.g. "AUG 24 – AUG 30, 2026"
    const startMonth = monday.toLocaleString('en-US', { month: 'short' }).toUpperCase();
    const sunday = weekDays[6].fullDate;
    const endMonth = sunday.toLocaleString('en-US', { month: 'short' }).toUpperCase();
    const year = sunday.getFullYear();

    if (startMonth === endMonth) {
      setWeekRangeStr(`${startMonth} ${monday.getDate()} – ${sunday.getDate()}, ${year}`);
    } else {
      setWeekRangeStr(`${startMonth} ${monday.getDate()} – ${endMonth} ${sunday.getDate()}, ${year}`);
    }
  }, []);

  const activeDay = days[selectedDayIndex] || days[3] || {
    dayName: 'Thursday',
    shortName: 'Thu',
    dayNumber: 27,
    isSavedDay: true,
  };

  return (
    <div className="space-y-8">
      
      {/* Calendar Header with Live Week Badge */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-zinc-200/80 pb-6">
        <div className="space-y-1.5">
          <div className="inline-flex items-center gap-2 text-xs font-mono font-bold uppercase tracking-widest text-zinc-500">
            <span>OUR MOTTO</span>
          </div>
          <h3 className="font-display text-3xl xs:text-4xl sm:text-5xl lg:text-[56px] font-bold text-zinc-950 tracking-tight leading-[1.1]">
            Save one day, Every week.
          </h3>
        </div>

        {/* Dynamic Current Week Metric Tag */}
        <div className="inline-flex items-center gap-2.5 bg-white border border-zinc-200 px-4 py-2 rounded-xl text-xs font-mono text-zinc-700 shadow-2xs self-start sm:self-auto">
          <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
          <span className="font-bold">{weekRangeStr || 'CURRENT WEEK'}</span>
        </div>
      </div>

      {/* 7-Day Interactive Grid */}
      <div className="w-full overflow-x-auto pb-2 scrollbar-none">
        <div className="min-w-[640px] grid grid-cols-7 gap-2.5 sm:gap-3.5">
          {days.length > 0 ? (
            days.map((day, idx) => {
              const isSelected = selectedDayIndex === idx;
              const isSaved = day.isSavedDay;

              return (
                <button
                  key={idx}
                  type="button"
                  onClick={() => setSelectedDayIndex(idx)}
                  className={`relative rounded-2xl sm:rounded-3xl p-4 sm:p-5 flex flex-col items-center justify-between gap-2 text-center transition-all duration-200 outline-none ${
                    isSaved
                      ? 'bg-zinc-950 text-white border-2 border-zinc-800 ring-4 ring-zinc-900/10 shadow-lg scale-105 z-10'
                      : isSelected
                      ? 'bg-zinc-900 text-white border border-zinc-700 shadow-md scale-102 z-10'
                      : 'bg-white text-zinc-800 border border-zinc-200/80 hover:border-zinc-400 hover:shadow-xs hover:-translate-y-0.5'
                  }`}
                >
                  {/* Today Pill */}
                  {day.isToday && (
                    <span className="absolute -top-2.5 px-2 py-0.5 bg-zinc-900 text-white border border-zinc-700 text-[9px] font-mono font-bold uppercase tracking-wider rounded-full shadow-xs">
                      Today
                    </span>
                  )}

                  {/* Saved Tag on Saved Day */}
                  {isSaved && !day.isToday && (
                    <span className="absolute -top-2.5 px-2 py-0.5 bg-zinc-100 text-zinc-950 border border-zinc-300 text-[9px] font-mono font-bold uppercase tracking-wider rounded-full shadow-xs">
                      Saved
                    </span>
                  )}

                  <span className={`text-xs font-mono font-semibold uppercase tracking-wider ${
                    isSaved ? 'text-zinc-300' : isSelected ? 'text-zinc-300' : 'text-zinc-500'
                  }`}>
                    {day.shortName}
                  </span>

                  <span className={`font-display text-2xl sm:text-3xl font-extrabold ${
                    isSaved ? 'text-white' : isSelected ? 'text-white' : 'text-zinc-900'
                  }`}>
                    {day.dayNumber}
                  </span>

                  <span className={`text-[10px] font-mono font-medium ${
                    isSaved ? 'text-emerald-400 font-bold' : isSelected ? 'text-zinc-300' : 'text-zinc-400'
                  }`}>
                    {isSaved ? '0 hrs admin' : '8.5h focus'}
                  </span>
                </button>
              );
            })
          ) : (
            // Fallback skeleton while hydrating
            Array.from({ length: 7 }).map((_, i) => (
              <div
                key={i}
                className="bg-white rounded-2xl p-4 border border-zinc-200/80 text-center h-28 animate-pulse"
              />
            ))
          )}
        </div>
      </div>

      {/* Dynamic Telemetry / Day-Saved Breakdown Card */}
      <div className="bg-white rounded-2xl sm:rounded-3xl border border-zinc-200/80 p-6 sm:p-8 shadow-xs">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
          
          {/* Left Column: Day Context & Impact Headline */}
          <div className="lg:col-span-5 space-y-2">
            <div className="inline-flex items-center gap-2 text-xs font-mono font-bold text-zinc-500 uppercase tracking-wider">
              <Clock className="w-3.5 h-3.5 text-zinc-700" />
              <span>{activeDay.dayName} Breakdown</span>
            </div>
            <h4 className="font-display text-xl sm:text-2xl font-bold text-zinc-950">
              {activeDay.isSavedDay
                ? 'Your Completely Automated Day'
                : `Active Studio Operations on ${activeDay.dayName}`}
            </h4>
            <p className="text-xs sm:text-sm text-zinc-600 leading-relaxed font-normal">
              {activeDay.isSavedDay
                ? 'Cora executes client inquiries, rate calculations, GST billing, and contract signatures completely autonomously.'
                : 'All routine communications and rate negotiations are handled automatically in the background by your AI co-founder.'}
            </p>
          </div>

          {/* Right Column: 4 Metric Badges */}
          <div className="lg:col-span-7 grid grid-cols-2 sm:grid-cols-4 gap-3">
            
            <div className="bg-zinc-50 border border-zinc-200/70 rounded-2xl p-4 text-center space-y-1">
              <span className="text-[11px] font-mono text-zinc-500 uppercase font-semibold">Time Saved</span>
              <p className="font-display text-lg sm:text-xl font-bold text-zinc-950">8.5 hrs</p>
              <span className="text-[10px] font-mono text-emerald-600 font-bold block">100% Free</span>
            </div>

            <div className="bg-zinc-50 border border-zinc-200/70 rounded-2xl p-4 text-center space-y-1">
              <span className="text-[11px] font-mono text-zinc-500 uppercase font-semibold">18% GST</span>
              <p className="font-display text-lg sm:text-xl font-bold text-zinc-950">Autopilot</p>
              <span className="text-[10px] font-mono text-zinc-500 font-medium block">Instant UPI</span>
            </div>

            <div className="bg-zinc-50 border border-zinc-200/70 rounded-2xl p-4 text-center space-y-1">
              <span className="text-[11px] font-mono text-zinc-500 uppercase font-semibold">E-Sign Vault</span>
              <p className="font-display text-lg sm:text-xl font-bold text-zinc-950">SHA-256</p>
              <span className="text-[10px] font-mono text-zinc-500 font-medium block">Compliant</span>
            </div>

            <div className="bg-zinc-50 border border-zinc-200/70 rounded-2xl p-4 text-center space-y-1">
              <span className="text-[11px] font-mono text-zinc-500 uppercase font-semibold">Annual ROI</span>
              <p className="font-display text-lg sm:text-xl font-bold text-zinc-950">52 Days</p>
              <span className="text-[10px] font-mono text-zinc-700 font-semibold block">Per Year</span>
            </div>

          </div>

        </div>
      </div>

    </div>
  );
}
